<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Empresa;
use App\Models\Cuenta;
use App\Models\User;
use App\Services\SecurityLogger;

class SeguridadController extends Controller
{    protected $securityLogger;
    
    public function __construct()
    {
        // Inicializar servicio de log si existe o usar implementación básica
        if (class_exists('\App\Services\SecurityLogger')) {
            $this->securityLogger = app('\App\Services\SecurityLogger');
        } else {
            // Implementación básica si no existe el servicio
            $this->securityLogger = new class {
                public function getRecentLogs($empresaId, $limit = 20) {
                    return collect([]); // Devolver colección vacía
                }
                
                public function log($empresaId, $tipo, $mensaje, $usuarioId = null) {
                    // Registrar en el log normal de Laravel
                    Log::info("Log de seguridad: [$empresaId] [$tipo] $mensaje " . ($usuarioId ? "(Usuario: $usuarioId)" : ""));
                }
                
                public function getLogs($empresaId, $fechaInicio, $fechaFin, $tipo, $usuario, $limit) {
                    return collect([]); // Devolver colección vacía
                }
            };
        }
    }
    
    /**
     * Mostrar configuración de seguridad
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
            
            // Configuración actual de seguridad
            $configuracion = $empresa->configuracion_seguridad ?? [
                'politica_contrasenas' => [
                    'longitud_minima' => 8,
                    'requiere_mayusculas' => true,
                    'requiere_numeros' => true,
                    'requiere_caracteres_especiales' => true,
                    'caducidad_dias' => 90,
                    'historial_contrasenas' => 5,
                    'intentos_fallidos' => 5
                ],
                'sesiones' => [
                    'tiempo_inactividad' => 30, // minutos
                    'sesiones_simultaneas' => true,
                    'bloqueo_ip_intentos' => 10
                ],
                'autenticacion_doble_factor' => false,
                'registro_actividad' => true,
                'nivel_log' => 'medio' // bajo, medio, alto
            ];
            
            // Obtener logs de seguridad recientes
            $logs = $this->securityLogger->getRecentLogs($empresa->id, 20);
            
            return view('modules.configuracion.seguridad.index', compact('empresa', 'configuracion', 'logs', 'userData'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de seguridad: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración de seguridad');
        }
    }
    
    /**
     * Actualizar configuración de seguridad
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'longitud_minima' => 'required|integer|min:6|max:20',
                'requiere_mayusculas' => 'boolean',
                'requiere_numeros' => 'boolean',
                'requiere_caracteres_especiales' => 'boolean',
                'caducidad_dias' => 'required|integer|min:0|max:365',
                'historial_contrasenas' => 'required|integer|min:0|max:20',
                'intentos_fallidos' => 'required|integer|min:0|max:20',
                'tiempo_inactividad' => 'required|integer|min:5|max:240',
                'sesiones_simultaneas' => 'boolean',
                'bloqueo_ip_intentos' => 'required|integer|min:0|max:100',
                'autenticacion_doble_factor' => 'boolean',
                'registro_actividad' => 'boolean',
                'nivel_log' => 'required|in:bajo,medio,alto'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_seguridad)) {
                $empresa->configuracion_seguridad = [];
            }
            
            // Actualizar configuración
            $empresa->configuracion_seguridad = [
                'politica_contrasenas' => [
                    'longitud_minima' => (int)$request->longitud_minima,
                    'requiere_mayusculas' => $request->boolean('requiere_mayusculas'),
                    'requiere_numeros' => $request->boolean('requiere_numeros'),
                    'requiere_caracteres_especiales' => $request->boolean('requiere_caracteres_especiales'),
                    'caducidad_dias' => (int)$request->caducidad_dias,
                    'historial_contrasenas' => (int)$request->historial_contrasenas,
                    'intentos_fallidos' => (int)$request->intentos_fallidos
                ],
                'sesiones' => [
                    'tiempo_inactividad' => (int)$request->tiempo_inactividad,
                    'sesiones_simultaneas' => $request->boolean('sesiones_simultaneas'),
                    'bloqueo_ip_intentos' => (int)$request->bloqueo_ip_intentos
                ],
                'autenticacion_doble_factor' => $request->boolean('autenticacion_doble_factor'),
                'registro_actividad' => $request->boolean('registro_actividad'),
                'nivel_log' => $request->nivel_log
            ];
            
            $empresa->save();
            
            // Registrar cambio en log de seguridad
            $this->securityLogger->log($empresa->id, 'configuracion', 'Actualización de políticas de seguridad', $userData['id'] ?? null);
            
            return back()->with('success', 'Configuración de seguridad actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de seguridad: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de seguridad');
        }
    }
    
    /**
     * Ver logs de seguridad
     */
    public function logs(Request $request)
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData) {
                return redirect()->route('dashboard')->with('error', 'No hay empresa seleccionada');
            }
            
            // Filtros
            $fechaInicio = $request->fecha_inicio ?? now()->subDays(30)->format('Y-m-d');
            $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');
            $tipo = $request->tipo ?? 'todos';
            $usuario = $request->usuario ?? null;
            $limit = $request->limit ?? 100;
            
            // Obtener logs
            $logs = $this->securityLogger->getLogs(
                $empresaData['id'],
                $fechaInicio,
                $fechaFin,
                $tipo,
                $usuario,
                $limit
            );
            
            // Obtener lista de usuarios para el filtro
            $usuarios = Cuenta::where('empresa_id', $empresaData['id'])
                ->select('id', 'nombre', 'email')
                ->get();
            
            return view('modules.configuracion.seguridad.logs', compact('logs', 'usuarios', 'fechaInicio', 'fechaFin', 'tipo', 'usuario', 'limit'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar logs de seguridad: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los logs de seguridad');
        }
    }
    
    /**
     * Forzar cambio de contraseña para usuarios
     */
    public function forzarCambioContrasena(Request $request)
    {
        try {
            $request->validate([
                'usuarios' => 'required|array',
                'usuarios.*' => 'exists:cuentas,id'
            ]);
            
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData) {
                return back()->with('error', 'No hay empresa seleccionada');
            }
            
            foreach ($request->usuarios as $usuarioId) {
                $cuenta = Cuenta::find($usuarioId);
                
                if ($cuenta && $cuenta->empresa_id === $empresaData['id']) {
                    $cuenta->cambio_contrasena_requerido = true;
                    $cuenta->save();
                    
                    // Registrar en log
                    $this->securityLogger->log(
                        $empresaData['id'],
                        'seguridad',
                        'Forzar cambio de contraseña para usuario: ' . $cuenta->email,
                        $userData['id'] ?? null
                    );
                }
            }
            
            return back()->with('success', 'Se ha forzado el cambio de contraseña para los usuarios seleccionados');
            
        } catch (\Exception $e) {
            Log::error('Error al forzar cambio de contraseña: ' . $e->getMessage());
            return back()->with('error', 'Error al forzar el cambio de contraseña');
        }
    }
    
    /**
     * Desbloquear usuarios
     */
    public function desbloquearUsuarios(Request $request)
    {
        try {
            $request->validate([
                'usuarios' => 'required|array',
                'usuarios.*' => 'exists:cuentas,id'
            ]);
            
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData) {
                return back()->with('error', 'No hay empresa seleccionada');
            }
            
            foreach ($request->usuarios as $usuarioId) {
                $cuenta = Cuenta::find($usuarioId);
                
                if ($cuenta && $cuenta->empresa_id === $empresaData['id']) {
                    $cuenta->bloqueado = false;
                    $cuenta->intentos_fallidos = 0;
                    $cuenta->save();
                    
                    // Registrar en log
                    $this->securityLogger->log(
                        $empresaData['id'],
                        'seguridad',
                        'Desbloqueo de usuario: ' . $cuenta->email,
                        $userData['id'] ?? null
                    );
                }
            }
            
            return back()->with('success', 'Usuarios desbloqueados correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al desbloquear usuarios: ' . $e->getMessage());
            return back()->with('error', 'Error al desbloquear usuarios');
        }
    }
}
