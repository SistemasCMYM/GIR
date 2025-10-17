<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting específico para rutas de autenticación
        $key = 'auth-attempts:' . $request->ip();
        
        // Permitir máximo 5 intentos por minuto
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Demasiados intentos de autenticación. Intente nuevamente en ' . $seconds . ' segundos.',
                'retry_after' => $seconds
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 60 segundos de ventana
        
        $response = $next($request);
        
        // Si la autenticación fue exitosa, limpiar el contador
        if ($response->getStatusCode() === 200) {
            RateLimiter::clear($key);
        }
        
        return $response;
    }
}
