<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MongoEmpresaService;
use App\Services\MongoCuentaService;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showNitForm()
    {
        return view('auth.nit');
    }

    public function validateNit(Request $request)
    {
        $request->validate(['nit' => 'required']);
        $mongo = new MongoEmpresaService();
        $empresa = $mongo->findByNit($request->nit);
        if (!$empresa) {
            return back()->withErrors(['nit' => 'NIT no encontrado'])->withInput();
        }
        session(['nit' => $request->nit, 'empresa_id' => $empresa['id']]);
        return redirect()->route('login');
    }

    public function showLoginForm()
    {
        if (!session('nit')) {
            return redirect()->route('login.nit');
        }
        // Obtener datos de la empresa para mostrar en el login
        $empresa = null;
        if (session('nit')) {
            $mongo = new \App\Services\MongoEmpresaService();
            $empresa = $mongo->findByNit(session('nit'));
        }
        return view('auth.login', [
            'nit' => session('nit'),
            'empresa' => $empresa
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $empresaId = session('empresa_id');
        $mongo = new MongoCuentaService();
        $user = $mongo->findByEmailAndEmpresa($request->email, $empresaId);
        // Validar que el hash sea Bcrypt (acepta cualquier cost factor)
        $isBcrypt = false;
        if ($user && isset($user['contrasena'])) {
            // Bcrypt hash debe empezar con $2a$, $2b$ o $2y$ y tener cualquier cost factor
            if (preg_match('/^\$(2[aby])\$\d{2}\$/', $user['contrasena'])) {
                $isBcrypt = true;
            }
        }
        if (!$user || !$isBcrypt || !Hash::check($request->password, $user['contrasena'])) {
            return back()->withErrors(['email' => 'Credenciales incorrectas'])->onlyInput('email');
        }        // Aquí puedes guardar datos de usuario en sesión si lo deseas
        session(['user_email' => $user['email'], 'user_name' => $user['nick'], 'user_id' => $user['id']]);
        return redirect()->route('dashboard');
    }
}
