<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\MongoPermisoService;

class ModulePermissionMiddleware
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
    public function handle(Request $request, Closure $next, string $modulo, string $accion = null): Response
    {
        // Verificar rutas públicas que no requieren permisos
        $publicRoutes = ['login', 'login.nit', 'login.credentials', 'login.nit.verify', 'login.credentials.verify', 'welcome'];
        
        if ($request->routeIs($publicRoutes)) {
            return $next($request);
        }
        
        // Si es la ruta dashboard, permitir el acceso sin verificar permisos adicionales
        if ($request->routeIs('dashboard') || $request->path() === 'dashboard') {
            Log::info('Acceso al dashboard permitido para: ' . ($request->session()->get('user_data')['email'] ?? 'N/A'));
            return $next($request);
        }
        
        // Verificar autenticación básica
        if (!Session::get('authenticated')) {
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Debe iniciar sesión para continuar.'
            ]);
        }

        // Obtener datos de usuario y empresa
        $userData = Session::get('user_data');
        $empresaData = Session::get('empresa_data');
        
        if (!$userData || !$empresaData) {
            Log::warning('Datos de usuario o empresa no encontrados en sesión');
            Session::flush();
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Sesión inválida. Por favor, inicie sesión nuevamente.'
            ]);
        }
        
        $userId = $userData['id'];
        $empresaId = $empresaData['id'];
        
        // Verificar si es Super Admin (tiene acceso a todo)
        $roles_super_admin = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin'];
        $isSuperAdmin = isset($userData['rol']) && in_array($userData['rol'], $roles_super_admin);
        
        if ($isSuperAdmin) {
            Log::info('Super Admin: acceso permitido al módulo ' . $modulo);
            return $next($request);
        }
        
        // Verificar permiso específico para el módulo y acción
        $tienePermiso = $this->permisoService->tienePermiso($userId, $empresaId, $modulo, $accion);
        
        if (!$tienePermiso) {
            Log::warning('Acceso denegado: Usuario ' . ($userData['email'] ?? 'N/A') . ' no tiene permiso para ' . $modulo . ($accion ? '/' . $accion : ''));
            
            // Determinar hacia dónde redirigir según el contexto
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No tiene permiso para realizar esta acción'
                ], 403);
            }
            
            return redirect()->route('dashboard')->withErrors([
                'general' => 'No tiene permiso para acceder a este módulo o realizar esta acción.'
            ]);
        }
        
        Log::info('Acceso permitido: Usuario ' . ($userData['email'] ?? 'N/A') . ' -> ' . $modulo . ($accion ? '/' . $accion : ''));
        
        return $next($request);
    }
}
