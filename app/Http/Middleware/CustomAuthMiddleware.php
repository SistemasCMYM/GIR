<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CustomAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CustomAuthMiddleware: Procesando ruta: ' . $request->path());
        
        // Verificar rutas públicas que no requieren autenticación
        $publicRoutes = ['login', 'login.nit', 'login.credentials', 'login.nit.verify', 'login.credentials.verify', 'welcome'];
        
        if ($request->routeIs($publicRoutes)) {
            Log::info('CustomAuthMiddleware: Accediendo a ruta pública: ' . $request->route()->getName());
            return $next($request);
        }
        
        // Verificar si el usuario está autenticado via session
        if (!$request->session()->has('authenticated') || !$request->session()->get('authenticated')) {
            // Registrar intento de acceso no autenticado
            Log::warning('CustomAuthMiddleware: Intento de acceso no autenticado a: ' . $request->path());
            
            // Si la sesión no existe, redirigir al login por NIT
            return redirect()->route('login.nit')->with('error', 'Por favor inicie sesión para continuar.');
        }

        // Verificar que tenga datos de usuario
        if (!$request->session()->has('user_data') || !$request->session()->has('empresa_data')) {
            Log::warning('CustomAuthMiddleware: Datos de sesión incompletos para usuario que intenta acceder a: ' . $request->path());
            Log::info('CustomAuthMiddleware: user_data existe: ' . ($request->session()->has('user_data') ? 'sí' : 'no'));
            Log::info('CustomAuthMiddleware: empresa_data existe: ' . ($request->session()->has('empresa_data') ? 'sí' : 'no'));
            // Evitar vaciar toda la sesión para no cerrar la sesión del usuario inesperadamente.
            // En su lugar, eliminar solo las claves críticas faltantes y forzar regeneración del token CSRF.
            $request->session()->forget(['user_data', 'empresa_data', 'cuenta_id', 'empresa_id', 'mongo_session_token']);
            $request->session()->regenerateToken();
            return redirect()->route('login.nit')->with('error', 'Datos de sesión incompletos. Por favor inicie sesión nuevamente.');
        }

        // Verificar que la empresa sigue activa
        $empresa = $request->session()->get('empresa_data');
        if (!$empresa || !($empresa['estado'] ?? true)) {
            Log::warning('CustomAuthMiddleware: Intento de acceso con empresa inactiva a: ' . $request->path());
            // Remover sólo las claves relacionadas con la empresa y forzar logout parcial sin vaciar otros datos que puedan ser útiles.
            $request->session()->forget(['empresa_data', 'empresa_id', 'mongo_session_token']);
            $request->session()->regenerateToken();
            return redirect()->route('login.nit')->with('error', 'Su empresa no está activa en el sistema.');
        }

        // Todo en orden, continuar
        Log::info('CustomAuthMiddleware: Acceso autenticado exitoso a: ' . $request->path() . ' por usuario: ' . ($request->session()->get('user_data')['email'] ?? 'N/A'));
        return $next($request);
    }
}
