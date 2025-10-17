<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Perfil;
use App\Models\Rol;
use Illuminate\Support\Facades\Log;

class ValidarPermisosPerfil
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $modulo
     * @param  string  $permiso
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $modulo = null, $permiso = 'read')
    {
        try {
            // Verificar si hay sesión activa
            if (!session()->has('user_data')) {
                return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder.');
            }

            $userData = session('user_data');
            $cuentaId = $userData['id'] ?? null;

            if (!$cuentaId) {
                return redirect()->route('login')->with('error', 'Sesión inválida.');
            }

            // Buscar el perfil del usuario
            $perfil = Perfil::where('cuenta_id', $cuentaId)->first();

            if (!$perfil) {
                Log::warning("Usuario sin perfil asignado: $cuentaId");
                return redirect()->route('dashboard')->with('error', 'No tiene permisos asignados. Contacte al administrador.');
            }

            // Verificar si es SuperAdmin (acceso total)
            if ($this->esSuperAdmin($perfil)) {
                return $next($request);
            }

            // Si se especifica un módulo, verificar acceso
            if ($modulo && !$this->tieneAccesoModulo($perfil, $modulo)) {
                return redirect()->route('dashboard')->with('error', "No tiene acceso al módulo: $modulo");
            }

            // Verificar permisos específicos
            if (!$this->tienePermiso($perfil, $permiso)) {
                return redirect()->route('dashboard')->with('error', "No tiene permisos para: $permiso");
            }

            // Agregar información del perfil a la request para uso posterior
            $request->merge([
                'user_perfil' => $perfil,
                'user_modulos' => $this->getModulosAccesibles($perfil),
                'user_permisos' => $this->getPermisosUsuario($perfil)
            ]);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Error en ValidarPermisosPerfil: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error validando permisos.');
        }
    }

    /**
     * Verificar si el usuario es SuperAdmin
     */
    private function esSuperAdmin($perfil)
    {
        // Buscar si tiene rol SuperAdmin
        $rolSuperAdmin = Rol::where('nombre', 'SuperAdmin')
            ->where('tipo', 'interna')
            ->first();

        if (!$rolSuperAdmin) {
            return false;
        }

        // Verificar si el perfil está asociado a este rol
        return $perfil->rol_codigo === 'SuperAdmin' || 
               $perfil->permisos === 'all' ||
               (is_array($perfil->permisos) && in_array('all', $perfil->permisos));
    }

    /**
     * Verificar acceso a módulo
     */
    private function tieneAccesoModulo($perfil, $modulo)
    {
        $modulos = $perfil->modulos;
        
        // Si es string, convertir a array
        if (is_string($modulos)) {
            $modulos = [$modulos];
        }
        
        if (!is_array($modulos)) {
            return false;
        }

        // Verificar acceso específico o acceso a administración (acceso a todo)
        return in_array($modulo, $modulos) || 
               in_array('administracion', $modulos) ||
               in_array('dashboard', $modulos); // dashboard siempre tiene acceso básico
    }

    /**
     * Verificar permiso específico
     */
    private function tienePermiso($perfil, $permiso)
    {
        $permisos = $perfil->permisos;
        
        // Si es string, convertir a array
        if (is_string($permisos)) {
            $permisos = [$permisos];
        }
        
        if (!is_array($permisos)) {
            return false;
        }

        // Verificar permiso específico o permiso total
        return in_array('all', $permisos) || in_array($permiso, $permisos);
    }

    /**
     * Obtener módulos accesibles para el usuario
     */
    private function getModulosAccesibles($perfil)
    {
        $modulos = $perfil->modulos;
        
        if (is_string($modulos)) {
            return [$modulos];
        }
        
        return is_array($modulos) ? $modulos : [];
    }

    /**
     * Obtener permisos del usuario
     */
    private function getPermisosUsuario($perfil)
    {
        $permisos = $perfil->permisos;
        
        if (is_string($permisos)) {
            return [$permisos];
        }
        
        return is_array($permisos) ? $permisos : [];
    }
}
