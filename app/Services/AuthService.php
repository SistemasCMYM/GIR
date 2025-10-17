<?php

namespace App\Services;

use App\Models\MongoDB\Cuenta;
use App\Models\MongoDB\Empresa;
use App\Models\MongoDB\Perfil;
use App\Models\MongoDB\Permiso;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Verificar empresa por NIT
     */
    public function verificarEmpresa($nit)
    {
        try {
            $empresa = Empresa::where('nit', $nit)
                            ->where('estado', true)
                            ->first();
            
            return $empresa;
        } catch (\Exception $e) {
            Log::error('Error al verificar empresa: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar credenciales de cuenta
     */
    public function verificarCredenciales($email, $password, $empresaId)
    {
        try {
            $cuenta = Cuenta::where('email', $email)
                          ->where('estado', 'activa')
                          ->first();

            if (!$cuenta) {
                return ['success' => false, 'message' => 'Cuenta no encontrada'];
            }

            // Verificar contraseña
            if (!Hash::check($password, $cuenta->contrasena)) {
                return ['success' => false, 'message' => 'Contraseña incorrecta'];
            }

            // Verificar que la cuenta tenga acceso a la empresa
            if (!in_array($empresaId, $cuenta->empresas ?? [])) {
                return ['success' => false, 'message' => 'Sin acceso a esta empresa'];
            }

            // Obtener perfil y permisos
            $perfil = Perfil::where('cuenta_id', $cuenta->id)->first();
            $permisos = Permiso::where('cuenta_id', $cuenta->id)->get();

            return [
                'success' => true,
                'cuenta' => $cuenta,
                'perfil' => $perfil,
                'permisos' => $permisos
            ];

        } catch (\Exception $e) {
            Log::error('Error al verificar credenciales: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema'];
        }
    }

    /**
     * Iniciar sesión
     */
    public function iniciarSesion($empresa, $cuenta, $perfil, $permisos)
    {
        try {
            // Guardar datos en sesión
            Session::put('empresa_data', [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
                'razon_social' => $empresa->razon_social,
                'nit' => $empresa->nit,
                'colorPrimario' => $empresa->colorPrimario,
                'colorSecundario' => $empresa->colorSecundario,
                'logo' => $empresa->logo
            ]);

            Session::put('user_data', [
                'id' => $cuenta->id,
                'email' => $cuenta->email,
                'rol' => $cuenta->rol,
                'tipo' => $cuenta->tipo,
                'nombre' => $perfil->nombre ?? '',
                'apellido' => $perfil->apellido ?? '',
                'perfil' => $perfil ? $perfil->toArray() : null
            ]);

            Session::put('permisos_data', $permisos->toArray());

            return true;

        } catch (\Exception $e) {
            Log::error('Error al iniciar sesión: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar permisos de acceso a módulo
     */
    public function verificarPermisoModulo($modulo, $accion = 'read')
    {
        try {
            $userData = Session::get('user_data');
            if (!$userData) return false;

            // Super administrador tiene acceso completo
            if ($userData['rol'] === 'SuperAdmin') {
                return true;
            }

            // Dashboard siempre accesible para usuarios autenticados
            if ($modulo === 'dashboard') {
                return true;
            }

            $permisos = Session::get('permisos_data', []);
            
            foreach ($permisos as $permiso) {
                if ($permiso['modulo'] === $modulo) {
                    $acciones = $permiso['acciones'] ?? [];
                    return in_array($accion, $acciones) || in_array('all', $acciones);
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error al verificar permisos: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cerrar sesión
     */
    public function cerrarSesion()
    {
        try {
            Session::flush();
            return true;
        } catch (\Exception $e) {
            Log::error('Error al cerrar sesión: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function estaAutenticado()
    {
        return Session::has('user_data') && Session::has('empresa_data');
    }

    /**
     * Obtener datos del usuario actual
     */
    public function getUsuarioActual()
    {
        return Session::get('user_data');
    }

    /**
     * Obtener datos de la empresa actual
     */
    public function getEmpresaActual()
    {
        return Session::get('empresa_data');
    }
}
