<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Mostrar la página principal con información de la plataforma
     */
    public function showLanding()
    {
        return view('welcome');
    }

    /**
     * Mostrar formulario de verificación de NIT
     */
    public function showNitForm()
    {
        return view('auth.nit-form');
    }

    /**
     * Verificar el NIT de la empresa
     */
    public function verifyNit(Request $request)
    {
        $request->validate([
            'nit' => 'required|string'
        ]);

        $nit = $request->input('nit');
        
        // Buscar empresa por NIT
        $empresa = Empresa::findByNit($nit);
        
        if (!$empresa || !$empresa->isActive()) {
            return back()->withErrors([
                'nit' => 'NIT no encontrado o empresa inactiva.'
            ])->withInput();
        }

        // Guardar información de la empresa en sesión
        Session::put('auth_step_1', [
            'empresa_id' => $empresa->id,
            'empresa_nombre' => $empresa->nombre,
            'empresa_nit' => $empresa->nit
        ]);

        return redirect()->route('login.credentials');
    }

    /**
     * Mostrar formulario de credenciales
     */
    public function showCredentialsForm()
    {
        // Verificar que se haya completado el paso 1
        if (!Session::has('auth_step_1')) {
            return redirect()->route('login.nit');
        }

        $empresaData = Session::get('auth_step_1');
        
        return view('auth.credentials-form', [
            'empresa' => $empresaData
        ]);
    }

    /**
     * Verificar credenciales del usuario
     */
    public function verifyCredentials(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Verificar que se haya completado el paso 1
        if (!Session::has('auth_step_1')) {
            return redirect()->route('login.nit');
        }

        $empresaData = Session::get('auth_step_1');
        $email = $request->input('email');
        $password = $request->input('password');

        // Buscar cuenta del usuario
        $cuenta = Cuenta::findByEmailAndEmpresa($email, $empresaData['empresa_id']);

        if (!$cuenta || !$cuenta->isActive()) {
            return back()->withErrors([
                'email' => 'Usuario no encontrado o inactivo para esta empresa.'
            ])->withInput();
        }

        // Verificar contraseña
        if (!$cuenta->checkPassword($password)) {
            return back()->withErrors([
                'password' => 'Contraseña incorrecta.'
            ])->withInput();
        }

        // Autenticar usuario
        $this->loginUser($cuenta, $empresaData);

        return redirect()->route('dashboard');
    }

    /**
     * Iniciar sesión del usuario
     */
    private function loginUser($cuenta, $empresaData)
    {
        Session::put('auth_user', [
            'cuenta_id' => $cuenta->id,
            'email' => $cuenta->email,
            'nick' => $cuenta->nick,
            'rol' => $cuenta->rol,
            'empresa_id' => $empresaData['empresa_id'],
            'empresa_nombre' => $empresaData['empresa_nombre'],
            'empresa_nit' => $empresaData['empresa_nit']
        ]);

        // Limpiar datos temporales de autenticación
        Session::forget('auth_step_1');
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function check()
    {
        return Session::has('auth_user');
    }

    /**
     * Obtener datos del usuario autenticado
     */
    public function user()
    {
        return Session::get('auth_user');
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('login.nit');
    }
}
