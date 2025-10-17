<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * DESARROLLO ÚNICAMENTE - Establecer sesión SuperAdmin 
     * NO USAR EN PRODUCCIÓN - Eliminar este método en producción
     */
    public function setSuperAdminSession()
    {
        // ADVERTENCIA: Este método es solo para desarrollo
        // Debe ser eliminado en producción por seguridad
        if (app()->environment('production')) {
            abort(404);
        }
        
        /*
        $superAdminData = [
            '_id' => '65abc123def456789012',
            'nombre' => 'SuperAdmin',
            'apellido' => 'CMYM',
            'email' => 'superadmin@cmym.com.co',
            'super_admin' => true,
            'is_super_admin' => true,
            'rol' => 'SuperAdmin',
            'perfil' => [
                'nombre' => 'SuperAdmin',
                'permisos' => ['*']
            ],
            'estado' => 'activo',
            'tipo_usuario' => 'super_admin'
        ];

        $empresaData = [
            '_id' => '65abc123def456789013',
            'nombre' => 'CMYM Consultores',
            'nit' => '900123456-7',
            'tipo' => 'consultora'
        ];

        // Establecer ambas variables para compatibilidad
        session([
            'usuario_data' => $superAdminData,  // Para dashboard
            'user_data' => $superAdminData,     // Para middleware
            'empresa_data' => $empresaData
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesión SuperAdmin establecida',
            'data' => [
                'usuario_data' => session('usuario_data'),
                'user_data' => session('user_data'),
                'empresa_data' => session('empresa_data')
            ]
        ]);
        */
        
        return response()->json([
            'success' => false,
            'message' => 'Método deshabilitado - solo para desarrollo'
        ]);
    }

    /**
     * Limpiar sesión
     */
    public function clearSession()
    {
        \Illuminate\Support\Facades\Log::info('Admin\SessionController::clearSession invoked - clearing sensitive session keys');
        session()->forget(['authenticated', 'user_data', 'empresa_data', 'cuenta_id', 'mongo_session_token', 'session_token']);
        session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Sesión limpiada (claves sensibles)'
        ]);
    }

    /**
     * Verificar estado de sesión
     */
    public function checkSession()
    {
        return response()->json([
            'usuario_data' => session('usuario_data'),
            'user_data' => session('user_data'), 
            'empresa_data' => session('empresa_data'),
            'all_session' => session()->all()
        ]);
    }
}
