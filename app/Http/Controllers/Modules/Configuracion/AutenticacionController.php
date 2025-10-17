<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Empresa;
use App\Models\Cuenta;
use App\Models\User;

class AutenticacionController extends Controller
{
    /**
     * Mostrar configuración de autenticación
     */
    public function index()
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData) {
                return redirect()->route('dashboard')->with('error', 'No hay empresa seleccionada');
            }
            
            // Obtener empresa actual desde MongoDB
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('dashboard')->with('error', 'Empresa no encontrada');
            }
            
            // Configuración actual de autenticación
            $configuracion = $empresa->configuracion_autenticacion ?? [
                'permitir_registro' => false,
                'requerir_verificacion_email' => true,
                'autenticacion_doble_factor' => false,
                'politica_contrasenas' => [
                    'longitud_minima' => 8,
                    'requiere_mayusculas' => true,
                    'requiere_numeros' => true,
                    'requiere_caracteres_especiales' => true,
                    'caducidad_dias' => 90,
                    'historial_contrasenas' => 5,
                    'intentos_fallidos' => 5
                ],
                'bloqueo_cuentas' => true,
                'duracion_sesion' => 120, // minutos
                'sso_habilitado' => false
            ];
            
            return view('modules.configuracion.autenticacion.index', compact('empresa', 'configuracion', 'userData'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de autenticación: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración de autenticación');
        }
    }
    
    /**
     * Actualizar configuración de autenticación
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'permitir_registro' => 'boolean',
                'requerir_verificacion_email' => 'boolean',
                'autenticacion_doble_factor' => 'boolean',
                'longitud_minima' => 'required|integer|min:6|max:20',
                'requiere_mayusculas' => 'boolean',
                'requiere_numeros' => 'boolean',
                'requiere_caracteres_especiales' => 'boolean',
                'caducidad_dias' => 'required|integer|min:0|max:365',
                'historial_contrasenas' => 'required|integer|min:0|max:20',
                'intentos_fallidos' => 'required|integer|min:0|max:20',
                'bloqueo_cuentas' => 'boolean',
                'duracion_sesion' => 'required|integer|min:5|max:1440',
                'sso_habilitado' => 'boolean'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_autenticacion)) {
                $empresa->configuracion_autenticacion = [];
            }
            
            // Actualizar configuración
            $empresa->configuracion_autenticacion = [
                'permitir_registro' => $request->boolean('permitir_registro'),
                'requerir_verificacion_email' => $request->boolean('requerir_verificacion_email'),
                'autenticacion_doble_factor' => $request->boolean('autenticacion_doble_factor'),
                'politica_contrasenas' => [
                    'longitud_minima' => (int)$request->longitud_minima,
                    'requiere_mayusculas' => $request->boolean('requiere_mayusculas'),
                    'requiere_numeros' => $request->boolean('requiere_numeros'),
                    'requiere_caracteres_especiales' => $request->boolean('requiere_caracteres_especiales'),
                    'caducidad_dias' => (int)$request->caducidad_dias,
                    'historial_contrasenas' => (int)$request->historial_contrasenas,
                    'intentos_fallidos' => (int)$request->intentos_fallidos
                ],
                'bloqueo_cuentas' => $request->boolean('bloqueo_cuentas'),
                'duracion_sesion' => (int)$request->duracion_sesion,
                'sso_habilitado' => $request->boolean('sso_habilitado')
            ];
            
            $empresa->save();
            
            return back()->with('success', 'Configuración de autenticación actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de autenticación: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de autenticación');
        }
    }
    
    /**
     * Mostrar administración de usuarios
     */
    public function usuarios()
    {
        try {
            $empresaData = session('empresa_data');
            
            if (!$empresaData) {
                return redirect()->route('dashboard')->with('error', 'No hay empresa seleccionada');
            }
            
            // Obtener empresa actual
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('dashboard')->with('error', 'Empresa no encontrada');
            }
            
            // Obtener usuarios de la empresa
            $usuarios = Cuenta::where('empresa_id', $empresaData['id'])->get();
            
            return view('modules.configuracion.autenticacion.usuarios', compact('empresa', 'usuarios'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar administración de usuarios: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la administración de usuarios');
        }
    }
    
    /**
     * Bloquear/Desbloquear usuario
     */
    public function toggleBloqueo(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            
            // Obtener usuario
            $usuario = Cuenta::find($request->usuario_id);
            
            if (!$usuario || $usuario->empresa_id !== $empresaData['id']) {
                return back()->with('error', 'Usuario no encontrado o no pertenece a esta empresa');
            }
            
            // Cambiar estado de bloqueo
            $usuario->bloqueado = !$usuario->bloqueado;
            
            // Si se está desbloqueando, reiniciar intentos fallidos
            if (!$usuario->bloqueado) {
                $usuario->intentos_fallidos = 0;
            }
            
            $usuario->save();
            
            $mensaje = $usuario->bloqueado 
                ? 'Usuario bloqueado correctamente' 
                : 'Usuario desbloqueado correctamente';
            
            return back()->with('success', $mensaje);
            
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de bloqueo: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar el estado de bloqueo del usuario');
        }
    }
    
    /**
     * Restablecer contraseña de usuario
     */
    public function restablecerContrasena(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|string',
                'nueva_contrasena' => 'required|string|min:8'
            ]);
            
            $empresaData = session('empresa_data');
            
            // Obtener usuario
            $usuario = Cuenta::find($request->usuario_id);
            
            if (!$usuario || $usuario->empresa_id !== $empresaData['id']) {
                return back()->with('error', 'Usuario no encontrado o no pertenece a esta empresa');
            }
            
            // Actualizar contraseña
            $usuario->password = Hash::make($request->nueva_contrasena);
            $usuario->cambio_contrasena_requerido = true; // Forzar cambio en próximo inicio de sesión
            $usuario->save();
            
            // Si también existe en la tabla de usuarios de Laravel, actualizar ahí también
            $userLaravel = User::where('email', $usuario->email)->first();
            if ($userLaravel) {
                $userLaravel->password = Hash::make($request->nueva_contrasena);
                $userLaravel->save();
            }
            
            return back()->with('success', 'Contraseña restablecida correctamente. El usuario deberá cambiarla en su próximo inicio de sesión.');
            
        } catch (\Exception $e) {
            Log::error('Error al restablecer contraseña: ' . $e->getMessage());
            return back()->with('error', 'Error al restablecer la contraseña del usuario');
        }
    }
    
    /**
     * Habilitar/Deshabilitar 2FA para usuario
     */
    public function toggle2FA(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            
            // Obtener usuario
            $usuario = Cuenta::find($request->usuario_id);
            
            if (!$usuario || $usuario->empresa_id !== $empresaData['id']) {
                return back()->with('error', 'Usuario no encontrado o no pertenece a esta empresa');
            }
            
            // Cambiar estado de 2FA
            $doble_factor = $usuario->doble_factor ?? false;
            $usuario->doble_factor = !$doble_factor;
            
            // Si se está habilitando, generar secreto nuevo
            if ($usuario->doble_factor) {
                // Simulación de generación de secreto - en producción usar una librería adecuada
                $usuario->doble_factor_secreto = bin2hex(random_bytes(10));
            } else {
                $usuario->doble_factor_secreto = null;
            }
            
            $usuario->save();
            
            $mensaje = $usuario->doble_factor 
                ? 'Autenticación de doble factor habilitada para el usuario' 
                : 'Autenticación de doble factor deshabilitada para el usuario';
            
            return back()->with('success', $mensaje);
            
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de 2FA: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar el estado de autenticación de doble factor');
        }
    }
}
