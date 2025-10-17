<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para validar acceso de Super Administrador
 * Garantiza que solo usuarios con rol 'super_admin' puedan acceder a rutas protegidas
 */
class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar autenticación usando el sistema de sesiones personalizado
        if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
            Log::warning('Intento de acceso no autenticado a ruta de Super Admin', [
                'ip' => $request->ip(),
                'route' => $request->route()?->getName(),
                'user_agent' => $request->userAgent()
            ]);
            
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Debe iniciar sesión para acceder a esta sección.'
            ]);
        }

        // Obtener datos del usuario autenticado
        $userData = \App\Http\Controllers\AuthController::user();
        
        if (!$userData) {
            Log::warning('Usuario sin datos válidos intentando acceder a ruta de Super Admin', [
                'session_exists' => session()->has('user_data'),
                'route' => $request->route()?->getName()
            ]);
            
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Datos de sesión inválidos. Inicie sesión nuevamente.'
            ]);
        }

        // Verificar si es Super Administrador
        if (!$this->isSuperAdmin($userData)) {
            Log::warning('Intento de acceso no autorizado a ruta de Super Admin', [
                'user_email' => $userData->email ?? 'N/A',
                'user_tipo' => $userData->tipo ?? 'N/A',
                'user_rol' => $userData->rol ?? 'N/A',
                'route' => $request->route()?->getName(),
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('dashboard')->withErrors([
                'general' => 'No tiene permisos para acceder a esta sección. Solo Super Administradores.'
            ]);
        }

        // Log de acceso exitoso para auditoría
        Log::info('Acceso autorizado de Super Admin', [
            'user_email' => $userData->email ?? 'N/A',
            'route' => $request->route()?->getName(),
            'timestamp' => now()->toISOString()
        ]);

        return $next($request);
    }    /**
     * Verificar si el usuario es Super Administrador
     * Múltiples validaciones para máxima compatibilidad
     */
    private function isSuperAdmin($userData): bool
    {
        // Verificaciones por tipo de usuario
        $validTypes = ['super_admin', 'superadmin', 'root'];
        
        // 1. Verificar por campo 'tipo'
        if (isset($userData->tipo) && in_array(strtolower($userData->tipo), $validTypes)) {
            return true;
        }
        
        // 2. Verificar por campo 'rol'
        if (isset($userData->rol) && in_array(strtolower($userData->rol), $validTypes)) {
            return true;
        }

        // 3. Verificación por email predefinido (configurado en .env)
        $superAdminConfig = config('app.super_admins', []);
        $superAdminEmails = $superAdminConfig['emails'] ?? [];
        
        if (isset($userData->email) && in_array(strtolower($userData->email), array_map('strtolower', $superAdminEmails))) {
            return true;
        }

        // 4. Verificar por campo booleano si existe
        if (isset($userData->is_super_admin) && $userData->is_super_admin === true) {
            return true;
        }

        // 5. Admin sin empresa asignada (caso especial)
        if (empty($userData->empresa_id) && isset($userData->rol) && strtolower($userData->rol) === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Obtener información segura del usuario para logs
     */
    private function getUserInfo($userData): array
    {
        return [
            'id' => $userData->_id ?? $userData->id ?? 'N/A',
            'email' => $userData->email ?? 'N/A',
            'tipo' => $userData->tipo ?? 'N/A',
            'rol' => $userData->rol ?? 'N/A',
            'empresa_id' => $userData->empresa_id ?? null,
            'timestamp' => now()->toISOString()
        ];
    }
}
