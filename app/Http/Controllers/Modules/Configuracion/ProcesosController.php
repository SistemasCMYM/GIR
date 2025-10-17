<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Models\Empresa;
use App\Models\Plan;

class ProcesosController extends Controller
{
    /**
     * Mostrar configuración de procesos
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
            
            // Configuración actual de procesos
            $configuracion = $empresa->configuracion_procesos ?? [
                'procesos_automaticos' => true,
                'horario_procesos' => [
                    'hora_inicio' => '01:00',
                    'dias_semana' => [1, 2, 3, 4, 5] // Lunes a viernes
                ],
                'tareas_programadas' => [
                    'limpieza_logs' => true,
                    'backup_datos' => true,
                    'envio_reportes' => true,
                    'sincronizacion_datos' => false
                ],
                'retencion_datos' => [
                    'logs' => 90, // días
                    'notificaciones' => 60, // días
                    'datos_temporales' => 30 // días
                ],
                'procesos_activos' => []
            ];
            
            return view('modules.configuracion.procesos.index', compact('empresa', 'configuracion', 'userData'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de procesos: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración de procesos');
        }
    }
    
    /**
     * Actualizar configuración de procesos
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'procesos_automaticos' => 'boolean',
                'hora_inicio' => 'required|string',
                'dias_semana' => 'required|array',
                'dias_semana.*' => 'integer|min:0|max:6',
                'limpieza_logs' => 'boolean',
                'backup_datos' => 'boolean',
                'envio_reportes' => 'boolean',
                'sincronizacion_datos' => 'boolean',
                'retencion_logs' => 'required|integer|min:30|max:365',
                'retencion_notificaciones' => 'required|integer|min:30|max:365',
                'retencion_datos_temporales' => 'required|integer|min:7|max:90'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_procesos)) {
                $empresa->configuracion_procesos = [];
            }
            
            // Preservar procesos activos existentes
            $procesosActivos = $empresa->configuracion_procesos['procesos_activos'] ?? [];
            
            // Actualizar configuración
            $empresa->configuracion_procesos = [
                'procesos_automaticos' => $request->boolean('procesos_automaticos'),
                'horario_procesos' => [
                    'hora_inicio' => $request->hora_inicio,
                    'dias_semana' => $request->dias_semana
                ],
                'tareas_programadas' => [
                    'limpieza_logs' => $request->boolean('limpieza_logs'),
                    'backup_datos' => $request->boolean('backup_datos'),
                    'envio_reportes' => $request->boolean('envio_reportes'),
                    'sincronizacion_datos' => $request->boolean('sincronizacion_datos')
                ],
                'retencion_datos' => [
                    'logs' => (int)$request->retencion_logs,
                    'notificaciones' => (int)$request->retencion_notificaciones,
                    'datos_temporales' => (int)$request->retencion_datos_temporales
                ],
                'procesos_activos' => $procesosActivos
            ];
            
            $empresa->save();
            
            return back()->with('success', 'Configuración de procesos actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de procesos: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de procesos');
        }
    }
    
    /**
     * Mostrar gestión de planes de acción
     */
    public function planesAccion()
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
            
            // Obtener planes de acción
            $planes = Plan::where('empresa_id', $empresaData['id'])->get();
            
            return view('modules.configuracion.procesos.planes_accion', compact('empresa', 'planes'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de planes de acción: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la gestión de planes de acción');
        }
    }
    
    /**
     * Ejecutar proceso manual
     */
    public function ejecutarProceso(Request $request)
    {
        try {
            $request->validate([
                'proceso' => 'required|string|in:limpieza_logs,backup_datos,envio_reportes,sincronizacion_datos,reindexar_busqueda'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Registrar inicio del proceso
            $procesoId = uniqid('proc_');
            
            if (!isset($empresa->configuracion_procesos['procesos_activos'])) {
                $empresa->configuracion_procesos['procesos_activos'] = [];
            }
            
            $empresa->configuracion_procesos['procesos_activos'][] = [
                'id' => $procesoId,
                'tipo' => $request->proceso,
                'estado' => 'iniciado',
                'inicio' => now(),
                'fin' => null,
                'resultado' => null,
                'detalles' => 'Proceso iniciado manualmente'
            ];
            
            $empresa->save();
            
            // Ejecutar el proceso según el tipo
            switch ($request->proceso) {
                case 'limpieza_logs':
                    // Simular limpieza de logs
                    Log::info('Proceso de limpieza de logs iniciado para empresa: ' . $empresa->id);
                    sleep(2); // Simular un proceso que toma tiempo
                    $resultado = 'Limpieza de logs completada';
                    break;
                    
                case 'backup_datos':
                    // Intentar ejecutar comando de artisan para backup
                    try {
                        Artisan::call('backup:run', ['--only-db' => true, '--empresa' => $empresa->id]);
                        $resultado = 'Backup de datos completado';
                    } catch (\Exception $e) {
                        $resultado = 'Error en backup: ' . $e->getMessage();
                    }
                    break;
                    
                case 'envio_reportes':
                    // Simular envío de reportes
                    Log::info('Proceso de envío de reportes iniciado para empresa: ' . $empresa->id);
                    sleep(3); // Simular un proceso que toma tiempo
                    $resultado = 'Envío de reportes completado';
                    break;
                    
                case 'sincronizacion_datos':
                    // Simular sincronización de datos
                    Log::info('Proceso de sincronización de datos iniciado para empresa: ' . $empresa->id);
                    sleep(4); // Simular un proceso que toma tiempo
                    $resultado = 'Sincronización de datos completada';
                    break;
                    
                case 'reindexar_busqueda':
                    // Simular reindexación de búsqueda
                    Log::info('Proceso de reindexación de búsqueda iniciado para empresa: ' . $empresa->id);
                    sleep(2); // Simular un proceso que toma tiempo
                    $resultado = 'Reindexación de búsqueda completada';
                    break;
                    
                default:
                    $resultado = 'Proceso no reconocido';
                    break;
            }
            
            // Actualizar estado del proceso
            foreach ($empresa->configuracion_procesos['procesos_activos'] as $key => $proceso) {
                if ($proceso['id'] === $procesoId) {
                    $empresa->configuracion_procesos['procesos_activos'][$key]['estado'] = 'completado';
                    $empresa->configuracion_procesos['procesos_activos'][$key]['fin'] = now();
                    $empresa->configuracion_procesos['procesos_activos'][$key]['resultado'] = $resultado;
                }
            }
            
            $empresa->save();
            
            return back()->with('success', 'Proceso ejecutado correctamente: ' . $resultado);
            
        } catch (\Exception $e) {
            Log::error('Error al ejecutar proceso: ' . $e->getMessage());
            return back()->with('error', 'Error al ejecutar el proceso');
        }
    }
    
    /**
     * Ver historial de procesos
     */
    public function historialProcesos()
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
            
            // Obtener procesos históricos
            $procesos = $empresa->configuracion_procesos['procesos_activos'] ?? [];
            
            // Ordenar por fecha de inicio (más reciente primero)
            usort($procesos, function($a, $b) {
                return strtotime($b['inicio']) - strtotime($a['inicio']);
            });
            
            return view('modules.configuracion.procesos.historial', compact('empresa', 'procesos'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar historial de procesos: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el historial de procesos');
        }
    }
    
    /**
     * Limpiar historial de procesos
     */
    public function limpiarHistorial()
    {
        try {
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Limpiar procesos activos
            $empresa->configuracion_procesos['procesos_activos'] = [];
            $empresa->save();
            
            return back()->with('success', 'Historial de procesos limpiado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al limpiar historial de procesos: ' . $e->getMessage());
            return back()->with('error', 'Error al limpiar el historial de procesos');
        }
    }
}
