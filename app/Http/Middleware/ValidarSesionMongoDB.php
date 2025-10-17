<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Sesion;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Log;

class ValidarSesionMongoDB
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('ValidarSesionMongoDB: inicio', [
            'route' => $request->route()?->getName(),
            'path' => $request->path(),
            'session_keys' => array_keys($request->session()->all()),
            'user_data' => $request->session()->get('user_data')
        ]);
        try {
            // Verificar si ya existe una sesión Laravel válida
            if (session('authenticated') && session('cuenta_id')) {
                $cuentaId = session('cuenta_id');
                
                // Verificar que la cuenta existe y está activa
                $cuenta = Cuenta::find($cuentaId);
                if (!$cuenta || !$this->isAccountActive($cuenta)) {
                    Log::warning('ValidarSesionMongoDB: cuenta no válida o inactiva, limpiando sesión', ['cuenta_id' => $cuentaId]);
                    $this->limpiarSesion();
                    return $this->redirectToLogin();
                }
                
                // Verificar sesión MongoDB
                $sesionMongo = Sesion::findActiveByAccount($cuentaId);
                if (!$sesionMongo || !$sesionMongo->isActive()) {
                    // Crear nueva sesión MongoDB si no existe
                    $this->crearSesionMongoDB($cuenta, $request);
                } else {
                    // Actualizar actividad de la sesión existente
                    $sesionMongo->updateActivity();
                }
                
                return $next($request);
            }
            
            // Si no hay sesión Laravel, verificar token MongoDB
            $token = $request->header('Authorization') ?? 
                    $request->input('token') ?? 
                    $request->session()->get('mongo_session_token');
            
            if ($token) {
                $sesion = Sesion::findActiveByToken($token);
                
                if ($sesion && $sesion->isActive()) {
                    $cuenta = $sesion->cuenta;
                    
                    if ($cuenta && $cuenta->estado === 'activa') {
                        // Restaurar sesión Laravel desde MongoDB
                        $this->restaurarSesionLaravel($cuenta, $sesion);
                        $sesion->updateActivity();
                        
                        return $next($request);
                    }
                }
            }
            
            // No hay sesión válida, limpiar y redirigir
            $this->limpiarSesion();
            return $this->redirectToLogin();
            
        } catch (\Exception $e) {
            Log::error('Error en ValidarSesionMongoDB: ' . $e->getMessage());
            $this->limpiarSesion();
            return $this->redirectToLogin();
        }
    }
    
    /**
     * Crear sesión MongoDB para cuenta autenticada
     */
    private function crearSesionMongoDB($cuenta, $request)
    {
        $sesion = Sesion::createForAccount(
            $cuenta->id,
            session('empresa_id'),
            $request->ip(),
            $request->userAgent()
        );
        
        // Guardar token en sesión Laravel para futuras referencias
        session(['mongo_session_token' => $sesion->token_sesion]);
        
        Log::info('Sesión MongoDB creada', [
            'cuenta_id' => $cuenta->id,
            'sesion_id' => $sesion->id
        ]);
    }
    
    /**
     * Restaurar sesión Laravel desde MongoDB
     */
    private function restaurarSesionLaravel($cuenta, $sesion)
    {
        session([
            'authenticated' => true,
            'cuenta_id' => $cuenta->id,
            'user_data' => [
                'id' => $cuenta->id,
                'nick' => $cuenta->nick,
                'email' => $cuenta->email,
                'rol' => $cuenta->rol,
                'tipo' => $cuenta->tipo,
                'estado' => $cuenta->estado
            ],
            'mongo_session_token' => $sesion->token_sesion
        ]);
        
        // Si la cuenta tiene empresas, establecer la primera como activa
        if (!empty($cuenta->empresas)) {
            session(['empresa_id' => $cuenta->empresas[0]]);
        }
        
        Log::info('Sesión Laravel restaurada desde MongoDB', [
            'cuenta_id' => $cuenta->id,
            'sesion_id' => $sesion->id
        ]);
    }
    
    /**
     * Limpiar todas las sesiones
     */
    private function limpiarSesion()
    {
        $cuentaId = session('cuenta_id');
        
        if ($cuentaId) {
            // Cerrar sesiones MongoDB
            Sesion::closeAllForAccount($cuentaId);
        }
        
        // Limpiar sólo las claves sensibles de sesión en lugar de vaciar todo el arreglo.
        session()->forget([
            'authenticated', 'cuenta_id', 'user_data', 'empresa_id', 'empresa_data',
            'mongo_session_token', 'session_token', 'session_id', 'user_id', 'usuario_id'
        ]);
        // Regenerar token CSRF por seguridad
        session()->regenerateToken();
    }
    
    /**
     * Redirigir al login según el contexto
     */
    private function redirectToLogin()
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Sesión no válida',
                'redirect' => route('auth.nit.form')
            ], 401);
        }
        
        return redirect()->route('auth.nit.form')
                        ->with('message', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
    }
    
    /**
     * Verificar si una cuenta está activa
     */
    private function isAccountActive($cuenta)
    {
        if (!$cuenta || !isset($cuenta->estado)) {
            return false;
        }
        
        $estado = strtolower($cuenta->estado);
        
        // Estados considerados activos
        $estadosActivos = ['activa', 'activo', 'active', '1', 1, true];
        
        // Estados considerados inactivos
        $estadosInactivos = ['inactiva', 'inactivo', 'inactive', 'suspendida', 'suspendido', 'bloqueada', 'bloqueado', '0', 0, false];
        
        // Si está explícitamente en estados inactivos
        if (in_array($estado, $estadosInactivos) || in_array($cuenta->estado, $estadosInactivos)) {
            return false;
        }
        
        // Si está en estados activos o es un estado no reconocido pero no inactivo
        return in_array($estado, $estadosActivos) || in_array($cuenta->estado, $estadosActivos) || !in_array($estado, $estadosInactivos);
    }
}
