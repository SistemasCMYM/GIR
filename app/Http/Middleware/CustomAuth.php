<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAuth
{    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado usando nuestro sistema personalizado
        $isAuthenticated = \App\Http\Controllers\AuthController::isAuthenticated();
        
        if (!$isAuthenticated) {
            return redirect()->route('auth.nit.form')->withErrors([
                'general' => 'Debe iniciar sesión para acceder a esta página.'
            ]);
        }

        \Log::info('CustomAuth middleware - User authenticated, continuing to next middleware');
        return $next($request);
    }
}
