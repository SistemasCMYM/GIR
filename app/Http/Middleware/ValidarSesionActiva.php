<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Sesion;
use Illuminate\Support\Facades\Log;

class ValidarSesionActiva
{
    /**
     * Validar que la sesión esté activa según el schema de Node.js
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Obtener datos de sesión
            $userData = session('user_data');
            $sessionId = session('session_id');
            
            if (!$userData || !$sessionId) {
                return $this->redirectToLogin('Sesión no encontrada');
            }
            
            // Verificar que la sesión existe en MongoDB
            $sesion = Sesion::where('sesion_id', $sessionId)
                           ->where('cuenta_id', $userData['cuenta_id'])
                           ->first();
                           
            if (!$sesion) {
                return $this->redirectToLogin('Sesión inválida');
            }
            
            // Verificar que la sesión esté activa
            if ($sesion->estado !== 'activa') {
                return $this->redirectToLogin('Sesión inactiva');
            }
            
            // Verificar que no haya expirado
            if ($sesion->fecha_expiracion && now() > $sesion->fecha_expiracion) {
                // Marcar sesión como expirada
                $sesion->update([
                    'estado' => 'expirada',
                    'fecha_fin' => now()
                ]);
                
                return $this->redirectToLogin('Sesión expirada');
            }
            
            // Actualizar última actividad
            $sesion->update([
                'ultima_actividad' => now(),
                'ip_actual' => $request->ip(),
                'user_agent_actual' => $request->userAgent()
            ]);
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Error en middleware ValidarSesionActiva: ' . $e->getMessage());
            return $this->redirectToLogin('Error validando sesión');
        }
    }
    
    /**
     * Redirigir al login limpiando la sesión
     */
    private function redirectToLogin(string $mensaje)
    {
        session()->forget(['authenticated', 'user_data', 'session_id', 'cuenta_id', 'mongo_session_token']);
        session()->regenerateToken();
        return redirect()->route('login')->withErrors(['error' => $mensaje]);
    }
}
