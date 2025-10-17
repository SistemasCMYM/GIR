<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Sesion;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SessionPersistence
{    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('SessionPersistence: inicio', [
            'route' => $request->route()?->getName(),
            'path' => $request->path(),
            'session_keys' => array_keys($request->session()->all()),
            'user_data' => $request->session()->get('user_data')
        ]);
        // Verificar rutas públicas que no requieren sesión
        $publicRoutes = ['login', 'login.nit', 'login.credentials', 'login.nit.verify', 'login.credentials.verify', 'welcome'];
        
        if ($request->routeIs($publicRoutes)) {
            return $next($request);
        }
        
        // Verificar si la sesión tiene datos de usuario
        if (!session('user_id') && !session('usuario_id')) {
            // Evitar vaciar toda la sesión para no cerrar sesión inesperadamente.
            session()->forget(['user_id', 'usuario_id', 'user_name', 'user_email', 'company_name', 'company_nit']);
            session()->regenerateToken();
            return redirect()->route('login.nit')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
        }

        $userId = session('user_id') ?: session('usuario_id');
        $sessionToken = session('session_token');

        if (!$sessionToken) {
            // Limpiar sólo claves de sesión relacionadas con token
            session()->forget(['session_token', 'mongo_session_token', 'session_id']);
            session()->regenerateToken();
            return redirect()->route('login.nit')->with('error', 'Token de sesión inválido.');
        }
        $activeSession = Sesion::query()->where('token_sesion', $sessionToken)
                               ->where('usuario_id', $userId)
                               ->where('activa', true)
                               ->first();

        if (!$activeSession) {
            // Limpiar sólo claves sensibles
            session()->forget(['session_token', 'mongo_session_token', 'session_id', 'user_id', 'usuario_id']);
            session()->regenerateToken();
            return redirect('/login')->with('error', 'Sesión expirada o inválida.');
        }
        $lastActivity = Carbon::parse($activeSession->ultima_actividad ?? $activeSession->inicio_sesion);
        $now = Carbon::now();
        
        if ($lastActivity->diffInHours($now) > 24) {
            // Marcar sesión como expirada
            $activeSession->update([
                'activa' => false,
                'fin_sesion' => $now
            ]);
            
            session()->forget(['session_token', 'mongo_session_token', 'session_id', 'user_id', 'usuario_id']);
            session()->regenerateToken();
            return redirect()->route('login.nit')->with('error', 'Sesión expirada por inactividad.');
        }

        // Actualizar última actividad cada 5 minutos para no sobrecargar la BD
        if ($lastActivity->diffInMinutes($now) >= 5) {
            $activeSession->update(['ultima_actividad' => $now]);
        }

        // Renovar datos de sesión si es necesario
        $this->refreshSessionData($userId);

        return $next($request);
    }

    /**
     * Refresh session data if incomplete
     */
    private function refreshSessionData($userId)
    {
        // Si faltan datos importantes de sesión, recargarlos
        if (!session('user_name') || !session('company_name')) {
            $usuario = Usuario::with(['empresa', 'perfil'])->find($userId);
            
            if ($usuario) {
                $empresa = $usuario->empresa;
                
                session([
                    'user_name' => $usuario->nombre ?? $usuario->nombres . ' ' . ($usuario->apellidos ?? ''),
                    'user_email' => $usuario->email,
                    'company_name' => $empresa ? $empresa->nombre : 'No asignada',
                    'company_nit' => $empresa ? $empresa->nit : 'No disponible',
                    'user_profile' => $usuario->perfil ? $usuario->perfil->nombre : 'Usuario estándar'
                ]);
            }
        }
    }
}
