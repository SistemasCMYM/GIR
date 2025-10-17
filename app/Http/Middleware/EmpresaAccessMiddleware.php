<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\MongoPermisoService;

class EmpresaAccessMiddleware
{
    protected $permisoService;
    
    public function __construct(MongoPermisoService $permisoService)
    {
        $this->permisoService = $permisoService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar rutas públicas que no requieren acceso a empresa
        $publicRoutes = ['login', 'login.nit', 'login.credentials', 'login.nit.verify', 'login.credentials.verify', 'welcome'];
        
        if ($request->routeIs($publicRoutes)) {
            Log::info('Accediendo a ruta pública (EmpresaAccess): ' . $request->route()->getName());
            return $next($request);
        }
        
        // Verificar rutas de configuración que tienen tratamiento especial
        $currentPath = $request->path();
        if (strpos($currentPath, 'configuracion') !== false || $request->routeIs('configuracion.*')) {
            Log::info('Ruta de configuración detectada: ' . $currentPath);
            
            // Para configuración, solo verificar autenticación básica
            if (!Session::get('authenticated')) {
                Log::info('Usuario no autenticado intentando acceder a configuración: ' . $currentPath);
                return redirect()->route('login.nit')->withErrors([
                    'general' => 'Debe iniciar sesión para continuar.'
                ]);
            }
            
            // Permitir acceso a configuración si está autenticado
            Log::info('Permitiendo acceso a configuración para usuario autenticado');
            return $next($request);
        }
        
        // Verificar autenticación básica
        if (!Session::get('authenticated')) {
            Log::info('Usuario no autenticado intentando acceder a: ' . $request->path());
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Debe iniciar sesión para continuar.'
            ]);
        }

        // Obtener datos de empresa y usuario de la sesión
        $empresaData = Session::get('empresa_data');
        $userData = Session::get('user_data');

        if (!$empresaData || !$userData) {
            Log::warning('Datos de empresa o usuario no encontrados en sesión', [
                'empresaData_present' => $empresaData ? 'yes' : 'no',
                'userData_present' => $userData ? 'yes' : 'no',
                'path' => $request->path(),
                'session_id' => Session::getId()
            ]);
            
            // Limpiar sesión incompleta y redirigir al login
            Session::flush();
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Sesión incompleta. Por favor, inicie sesión nuevamente.'
            ]);
        }

        // Verificar rol del usuario
        $userRole = $userData['rol'] ?? 'usuario';
        $empresaId = $empresaData['id'];
        $userId = $userData['id'];

        // Definir array de roles de super administrador
        $roles_super_admin = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin', 'SuperAdmin'];
        $isSuperAdmin = in_array($userRole, $roles_super_admin);

        // Verificar acceso a rutas específicas
        $currentPath = $request->path();
        
        // Super Administrador tiene acceso a todo
        if ($isSuperAdmin) {
            Log::info('Acceso de Super Administrador: ' . $userData['email'] . ' a ruta: ' . $currentPath . ' (módulo detectado: ' . $this->determinarModuloPorRuta($currentPath) . ')');
            
            // Agregar información de acceso global al request
            $request->merge([
                'current_empresa_id' => $empresaId,
                'current_empresa' => $empresaData,
                'current_user_role' => $userRole,
                'isSuperAdmin' => true,
                'canAccessAllEmpresas' => true
            ]);
            
            return $next($request);
        }

        // Para rutas de administración general, solo Super Admin
        if ($request->is('admin/*')) {
            Log::warning('Usuario sin permisos de Super Admin intentando acceder a admin: ' . $userData['email']);
            return redirect()->route('login.nit')->withErrors([
                'general' => 'No tiene permisos para acceder a esta sección de administración.'
            ]);
        }

        // Verificar acceso específico a la empresa
        if (!$this->hasEmpresaAccess($userData, $empresaId)) {
            Log::warning('Usuario sin acceso a empresa intentando acceder: ' . $userData['email'] . ' -> Empresa ID: ' . $empresaId);
            // Para problemas de acceso a empresa, limpiar sesión y redirigir al login
            Session::flush();
            return redirect()->route('login.nit')->withErrors([
                'general' => 'No tiene permisos para acceder a esta empresa.'
            ]);
        }

        // Si llegamos aquí, el usuario tiene acceso a la empresa
        // Verificar permisos específicos para el módulo según la ruta actual
        $modulo = $this->determinarModuloPorRuta($currentPath);
        
        Log::info('EmpresaAccessMiddleware: Verificando módulo por ruta', [
            'ruta' => $currentPath,
            'modulo_detectado' => $modulo,
            'user_email' => $userData['email'],
            'user_role' => $userRole,
        ]);
        
        // Excepción especial: el dashboard siempre está disponible para usuarios autenticados
        if ($modulo === 'dashboard') {
            Log::info('Dashboard: Acceso permitido sin validación adicional para usuario: ' . $userData['email']);
            // Agregar información de empresa al request para uso en controladores
            $request->merge([
                'current_empresa_id' => $empresaId,
                'current_empresa' => $empresaData,
                'current_user_role' => $userRole,
                'isSuperAdmin' => false,
                'canAccessAllEmpresas' => false
            ]);
            
            Log::info('Dashboard: Middleware completado exitosamente');
            return $next($request);
        }
        
        // Validar acceso a módulo usando el modelo Cuenta (perfil y permisos) para otros módulos
        $cuentaModel = \App\Models\Cuenta::where('id', $userId)->first();
        
        Log::info('EmpresaAccessMiddleware: Validando acceso a módulo', [
            'modulo' => $modulo,
            'ruta' => $currentPath,
            'usuario' => $userData['email'],
            'cuenta_encontrada' => $cuentaModel ? 'sí' : 'no'
        ]);
        
        // Para rutas de configuración, permitir acceso a todos los usuarios autenticados
        if ($modulo === 'configuracion' || strpos($currentPath, 'configuracion') !== false) {
            Log::info('EmpresaAccessMiddleware: Permitiendo acceso a configuración', [
                'usuario' => $userData['email'],
                'ruta' => $currentPath,
                'rol' => $userRole
            ]);
            
            // Permitir acceso a configuración para todos los usuarios autenticados con empresa válida
            $request->merge([
                'current_empresa_id' => $empresaId,
                'current_empresa' => $empresaData,
                'current_user_role' => $userRole,
                'isSuperAdmin' => in_array($userRole, $roles_super_admin),
                'canAccessAllEmpresas' => in_array($userRole, $roles_super_admin)
            ]);
            
            return $next($request);
        }
        
        if ($modulo && $cuentaModel && !$cuentaModel->puedeAccederModulo($empresaId, $modulo)) {
            Log::warning('EmpresaAccessMiddleware: Usuario sin permiso (perfil/permisos) para módulo: ' . $modulo . ' -> Usuario: ' . $userData['email']);
            return redirect()->route('error.permission-denied')->withErrors([
                'general' => 'No tiene permisos para acceder a este módulo.'
            ]);
        }

        // Agregar información de empresa al request para uso en controladores
        $request->merge([
            'current_empresa_id' => $empresaId,
            'current_empresa' => $empresaData,
            'current_user_role' => $userRole,
            'isSuperAdmin' => false,
            'canAccessAllEmpresas' => false
        ]);

        Log::info('Acceso autorizado: ' . $userData['email'] . ' -> Empresa: ' . $empresaData['nombre'] . ' (Rol: ' . $userRole . ') -> Módulo: ' . ($modulo ?? 'general'));

        return $next($request);
    }

    /**
     * Verificar si el usuario tiene acceso a la empresa específica
     */
    private function hasEmpresaAccess($userData, $empresaId): bool
    {
        // Definir roles de administrador con acceso global
        $roles_super_admin = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin', 'SuperAdmin'];
        
        // Super Admin siempre tiene acceso
        if (isset($userData['rol']) && in_array($userData['rol'], $roles_super_admin)) {
            Log::info('Super Admin con acceso global: ' . ($userData['email'] ?? 'N/A'));
            return true;
        }

        // Verificar si el usuario tiene la empresa en su lista de empresas
        $userEmpresas = $userData['empresas'] ?? [];
        
        if (is_array($userEmpresas)) {
            // Convertir a strings para asegurar la comparación correcta
            $empresaIdStr = (string)$empresaId;
            $empresasStr = array_map(function($id) { return (string)$id; }, $userEmpresas);
            
            if (in_array($empresaIdStr, $empresasStr)) {
                Log::info('Usuario con acceso a empresa por lista de empresas: ' . ($userData['email'] ?? 'N/A'));
                return true;
            }
        }

        // Si no hay lista de empresas específica, verificar por campo empresa_id
        $userEmpresaId = $userData['empresa_id'] ?? null;
        if ($userEmpresaId && (string)$userEmpresaId === (string)$empresaId) {
            Log::info('Usuario con acceso a empresa por empresa_id: ' . ($userData['email'] ?? 'N/A'));
            return true;
        }
        
        // Verificar si el usuario tiene un perfil o rol que permita acceso a esta empresa
        $userRole = $userData['rol'] ?? 'usuario';
        $rolesConAcceso = ['administrador_empresa', 'admin_empresa', 'psicologo', 'tecnico', 'supervisor'];
        
        if (in_array($userRole, $rolesConAcceso)) {
            // Para estos roles, verificar si tienen permisos específicos para esta empresa
            $userId = $userData['id'] ?? null;
            if ($userId) {
                $tienePermiso = $this->permisoService->tienePermiso($userId, $empresaId, 'dashboard');
                if ($tienePermiso) {
                    Log::info('Usuario con acceso por rol y permisos: ' . ($userData['email'] ?? 'N/A') . ' (Rol: ' . $userRole . ')');
                    return true;
                }
            }
        }

        Log::warning('Usuario sin acceso a empresa: ' . ($userData['email'] ?? 'N/A') . ' -> Empresa: ' . $empresaId);
        return false;
    }
    
    /**
     * Determinar el módulo según la ruta actual
     */
    private function determinarModuloPorRuta($ruta): ?string
    {
        // Mapeo de rutas a módulos
        $rutasModulos = [
            'psicosocial' => 'psicosocial',
            'hallazgo' => 'hallazgos',
            'plan' => 'planes',
            'empresa' => 'empresas',
            'usuario' => 'usuarios',
            'empleado' => 'empleados',
            'perfil' => 'perfiles',
            'dashboard' => 'dashboard',
            'reporte' => 'reportes',
            'capacitacion' => 'capacitaciones',
            'configuracion' => 'configuracion' // Cambiado a 'configuracion' para coincidir con el control de permisos
        ];
        
        // Agregar tratamiento especial para la ruta de configuración
        if (strpos($ruta, 'configuracion') === 0 || $ruta === 'configuracion') {
            Log::info('Ruta de configuración detectada: ' . $ruta);
            return 'configuracion';
        }
        
        foreach ($rutasModulos as $patronRuta => $modulo) {
            if (strpos($ruta, $patronRuta) === 0 || $ruta === $patronRuta) {
                return $modulo;
            }
        }
        
        return null; // No se identificó un módulo específico
    }
}
