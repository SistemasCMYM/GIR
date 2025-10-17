<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Mostrar página de error de permisos
     */
    public function permissionDenied()
    {
        return view('errors.permission-denied');
    }
    
    /**
     * Mostrar página de error general
     */
    public function general()
    {
        return view('errors.general');
    }
      /**
     * Mostrar página de error 404
     */
    public function notFound()
    {
        return view('errors.404');
    }
    
    /**
     * Mostrar página de login expirado
     */
    public function loginExpired()
    {
    // Registrar y limpiar sólo claves sensibles
    \Illuminate\Support\Facades\Log::warning('ErrorController::loginExpired invocado - limpiando claves de sesión sensibles');
    session()->forget(['authenticated', 'user_data', 'empresa_data', 'cuenta_id', 'mongo_session_token']);
    session()->regenerateToken();

    return redirect()->route('login.nit')->with('error', 'Su sesión ha expirado o es inválida. Por favor, inicie sesión nuevamente.');
    }
}
