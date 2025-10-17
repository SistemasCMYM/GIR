<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ConfiguracionAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log de debugging
        Log::info('ConfiguracionAccessMiddleware: Iniciando validación', [
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip()
        ]);

        // Verificar autenticación básica: aceptar bandera 'authenticated' o presencia de datos de usuario
        $userData = Session::get('user_data') ?: Session::get('usuario_data');
        $isAuthenticated = Session::get('authenticated') || !empty($userData);

        if (!$isAuthenticated) {
            Log::warning('ConfiguracionAccessMiddleware: Usuario no autenticado', [ 'path' => $request->path() ]);
            return redirect()->route('login.nit')->withErrors(['general' => 'Debe iniciar sesión para continuar.']);
        }

        if (!$userData) {
            Log::warning('ConfiguracionAccessMiddleware: Datos de usuario no encontrados');
            return redirect()->route('dashboard')->withErrors(['general' => 'Sesión de usuario inválida.']);
        }

        // Log de datos de usuario para debugging
        $email = $userData['email'] ?? $userData['correo'] ?? 'no_email';
        $rol = strtolower($userData['rol'] ?? $userData['role'] ?? $userData['tipo'] ?? '');
        $isSuper = in_array($rol, ['super_admin','superadmin','super administrator','superadministrador','root'], true)
            || (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);

        Log::info('ConfiguracionAccessMiddleware: Datos de usuario', [
            'email' => $email,
            'rol' => $rol,
            'path' => $request->path()
        ]);

        if (!$isSuper) {
            Log::warning('ConfiguracionAccessMiddleware: Acceso denegado. Requiere SuperAdmin', [ 'email' => $email, 'rol' => $rol ]);
            return redirect()->route('dashboard')->with('warning', 'Acceso restringido. Solo SuperAdmin puede acceder a Configuración.');
        }

        Log::info('ConfiguracionAccessMiddleware: Acceso concedido a Configuración', [ 'email' => $email ]);
        return $next($request);
    }
}
