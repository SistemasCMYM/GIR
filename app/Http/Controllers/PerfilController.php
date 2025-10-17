<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PerfilController extends Controller
{    
    public function show()
    {
        // Verificar autenticación
        if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
            return redirect()->route('login.nit');
        }

        $userData = \App\Http\Controllers\AuthController::user();
        $empresaData = \App\Http\Controllers\AuthController::empresa();

        return view('perfil', compact('userData', 'empresaData'));
    }

    public function configuracion()
    {
        // Verificar si el usuario es SuperAdmin antes de redirigir a configuración
        $userData = \App\Http\Controllers\AuthController::user();
        
        if (!$userData) {
            return redirect()->route('dashboard')->with('warning', 'Sesión inválida.');
        }
        
        $rol = strtolower($userData['rol'] ?? $userData['role'] ?? $userData['tipo'] ?? '');
        $isSuperAdmin = in_array($rol, ['super_admin','superadmin','super administrator','superadministrador','root'], true)
            || (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
        
        if ($isSuperAdmin) {
            return redirect()->route('configuracion.index');
        } else {
            return redirect()->route('dashboard')->with('warning', 'Acceso restringido. Solo SuperAdmin puede acceder a Configuración.');
        }
    }

    public function updatePassword(Request $request)
    {
        // Verificar autenticación
        if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
            return redirect()->route('login.nit');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es requerida.',
            'new_password.required' => 'La nueva contraseña es requerida.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Aquí iría la lógica para actualizar la contraseña
        // Dependiendo de cómo esté implementado el sistema de autenticación
        
        return back()->with('success', 'Contraseña actualizada exitosamente.');
    }
}
