<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;

class EmpresaContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Evitar bucles de redirección - no aplicar middleware a rutas de auth
        $routeName = $request->route() ? $request->route()->getName() : null;
        $excludedRoutes = [
            'auth.nit.form',
            'auth.nit.verify',
            'auth.credentials.form',
            'auth.credentials.verify',
            'login.nit',
            'login.credentials',
            'login.nit.verify',
            'login.credentials.verify',
            'login',
            'logout',
            'auth.logout',
            'landing',
            'home',
            'welcome'
        ];

        if (in_array($routeName, $excludedRoutes) || 
            str_starts_with($request->path(), 'auth/') ||
            str_starts_with($request->path(), 'login/') ||
            $request->path() === '/' ||
            $request->path() === 'home' ||
            $request->path() === 'welcome') {
            Log::info('EmpresaContextMiddleware: Ruta excluida del middleware', [
                'route' => $routeName,
                'path' => $request->path()
            ]);
            return $next($request);
        }

        // Verificar autenticación básica
        if (!AuthController::isAuthenticated()) {
            Log::warning('EmpresaContextMiddleware: Usuario no autenticado', [
                'route' => $routeName,
                'url' => $request->url()
            ]);
            return redirect()->route('auth.nit.form')->withErrors([
                'general' => 'Debe iniciar sesión para acceder a esta página.'
            ]);
        }

        // Obtener datos del usuario primero para verificar SuperAdmin
        $userData = session('user_data');
        if (!$userData) {
            Log::warning('EmpresaContextMiddleware: Usuario sin datos válidos', [
                'route' => $routeName
            ]);
            return redirect()->route('auth.credentials.form')->withErrors([
                'general' => 'Datos de usuario incompletos. Complete la autenticación.'
            ]);
        }

        // 🔹 BYPASS PARA SUPERADMIN: Permitir acceso incluso sin empresa_data
        $isSuperAdmin = $this->isSuperAdmin($userData);
        
        if ($isSuperAdmin) {
            Log::info('EmpresaContextMiddleware: SuperAdmin detectado - BYPASS activado', [
                'user_email' => $userData['email'] ?? 'unknown',
                'route' => $routeName
            ]);
            
            // Permitir acceso sin validaciones de empresa
            return $next($request);
        }

        // Verificar que existe empresa_data en sesión (solo para usuarios NO SuperAdmin)
        $empresaData = session('empresa_data');
        if (!$empresaData || !isset($empresaData['id'])) {
            Log::warning('EmpresaContextMiddleware: No hay empresa_data en sesión', [
                'user_session' => $userData['email'] ?? 'unknown',
                'empresa_data' => $empresaData,
                'route' => $routeName
            ]);
            
            // NO eliminar sesión, solo redirigir para que se re-autentique
            return redirect()->route('auth.credentials.form')->withErrors([
                'general' => 'Sesión incompleta. Complete la autenticación.'
            ]);
        }

        // Verificar que el usuario tiene empresas_acceso definidas
        if (!isset($userData['empresas_acceso'])) {
            Log::warning('EmpresaContextMiddleware: Usuario sin empresas_acceso definidas', [
                'route' => $routeName
            ]);
            return redirect()->route('auth.credentials.form')->withErrors([
                'general' => 'Datos de usuario incompletos. Complete la autenticación.'
            ]);
        }

        $empresaId = $empresaData['id'];
        $empresasAcceso = $userData['empresas_acceso'];
        
        // Verificar acceso (considerando diferentes formatos de ID)
        $tieneAcceso = in_array($empresaId, $empresasAcceso) || 
                      in_array($empresaData['_id'] ?? $empresaId, $empresasAcceso) ||
                      in_array((string)$empresaId, $empresasAcceso);

        if (!$tieneAcceso) {
            Log::warning('EmpresaContextMiddleware: Usuario sin acceso a empresa actual', [
                'user_email' => $userData['email'] ?? 'unknown',
                'empresa_id' => $empresaId,
                'empresas_acceso' => $empresasAcceso,
                'route' => $routeName
            ]);
            
            // NO eliminar sesión, solo mostrar error
            return redirect()->route('auth.nit.form')->withErrors([
                'general' => 'No tiene permisos para acceder a esta empresa.'
            ]);
        }

        // Establecer empresa_id en el request para uso global
        $request->attributes->set('empresa_id', $empresaId);
        
        // Log de acceso exitoso
        Log::info('EmpresaContextMiddleware: Acceso autorizado', [
            'user_email' => $userData['email'] ?? 'unknown',
            'empresa_id' => $empresaId,
            'empresa_name' => $empresaData['razon_social'] ?? 'Sin nombre',
            'is_super_admin' => $userData['isSuperAdmin'] ?? false
        ]);

        return $next($request);
    }

    /**
     * Verificar si el usuario es Super Administrador
     * Compatible con múltiples formatos de datos de sesión
     */
    private function isSuperAdmin($userData): bool
    {
        // Convertir a array si es objeto
        $data = is_object($userData) ? (array)$userData : $userData;

        // Lista de roles/tipos válidos para SuperAdmin
        $validRoles = ['super_admin', 'superadmin', 'root', 'super administrator', 'superadministrador'];
        
        // 1. Verificar por campo 'rol'
        if (isset($data['rol']) && in_array(strtolower($data['rol']), $validRoles, true)) {
            return true;
        }
        
        // 2. Verificar por campo 'tipo'
        if (isset($data['tipo']) && in_array(strtolower($data['tipo']), $validRoles, true)) {
            return true;
        }

        // 3. Verificar por flag booleano isSuperAdmin
        if (isset($data['isSuperAdmin']) && $data['isSuperAdmin'] === true) {
            return true;
        }

        // 4. Verificar por flag booleano is_super_admin
        if (isset($data['is_super_admin']) && $data['is_super_admin'] === true) {
            return true;
        }

        // 5. Verificación por email predefinido (configurado en .env)
        $superAdminConfig = config('app.super_admins', []);
        $superAdminEmails = $superAdminConfig['emails'] ?? [];
        
        if (isset($data['email']) && in_array(strtolower($data['email']), array_map('strtolower', $superAdminEmails))) {
            return true;
        }

        return false;
    }
}
