<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empresa;
use App\Models\Notificacion;
use App\Services\MongoNotificacionService;

class NotificacionesController extends Controller
{
    protected $notificacionService;
    
    public function __construct()
    {
        // Verificar si existe el servicio o crear una implementación básica
        if (class_exists('\App\Services\MongoNotificacionService')) {
            $this->notificacionService = app('\App\Services\MongoNotificacionService');
        } else {
            // Implementación básica
            $this->notificacionService = new class {
                public function getNotificaciones($empresaId, $limit = 10) {
                    return collect([]);
                }
                
                public function crearNotificacion($data) {
                    Log::info("Creando notificación: " . json_encode($data));
                    return true;
                }
            };
        }
    }
    
    /**
     * Mostrar configuración de notificaciones
     */
    public function index()
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData) {
                Log::warning('NotificacionesController: No hay empresa_data en sesión');
                // En lugar de redirigir, crear datos por defecto
                $empresaData = ['id' => null, 'nombre' => 'Sin empresa'];
                $empresa = (object)['id' => null, 'nombre' => 'Sin empresa'];
            } else {
                // Obtener empresa actual desde MongoDB con manejo de errores
                try {
                    $empresa = Empresa::where('id', $empresaData['id'])->first();
                    
                    if (!$empresa) {
                        Log::warning('Empresa no encontrada en BD: ' . $empresaData['id']);
                        $empresa = (object)[
                            'id' => $empresaData['id'],
                            'nombre' => $empresaData['nombre'] ?? 'Empresa'
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Error conectando a MongoDB para notificaciones: ' . $e->getMessage());
                    $empresa = (object)[
                        'id' => $empresaData['id'] ?? null,
                        'nombre' => $empresaData['nombre'] ?? 'Empresa'
                    ];
                }
            }
            
            // Configuración actual de notificaciones (con valores por defecto seguros)
            $configuracion = isset($empresa->configuracion_notificaciones) 
                ? $empresa->configuracion_notificaciones 
                : [
                'tipos_habilitados' => ['evaluaciones', 'sistema'],
                'frecuencias' => ['evaluaciones' => 7, 'vencimientos' => 3, 'recordatorios' => 1],
                'canales_habilitados' => ['email', 'dashboard'],
                'notificaciones_email' => true,
                'notificaciones_sistema' => true,
                'notificaciones_hallazgos' => true,
                'notificaciones_psicosocial' => true,
                'frecuencia_resumen' => 'diario',
                'destinatarios_adicionales' => [],
                'plantillas_correo' => [],
                'notificaciones_usuarios_nuevos' => true,
                'notificaciones_tareas' => true
            ];
            
            // Extraer datos para la vista
            $tiposHabilitados = $configuracion['tipos_habilitados'] ?? ['evaluaciones', 'sistema'];
            $frecuencias = $configuracion['frecuencias'] ?? ['evaluaciones' => 7, 'vencimientos' => 3, 'recordatorios' => 1];
            $canalesHabilitados = $configuracion['canales_habilitados'] ?? ['email', 'dashboard'];
            
            // Obtener notificaciones recientes usando acceso directo al modelo
            $notificacionesRecientes = [];
            try {
                if (class_exists('App\Models\Notificacion')) {
                    $notificacionesRecientes = \App\Models\Notificacion::orderBy('fecha_creacion', 'desc')
                        ->limit(5)
                        ->get()
                        ->toArray();
                }
            } catch (\Exception $e) {
                \Log::warning('Error al obtener notificaciones recientes: ' . $e->getMessage());
                $notificacionesRecientes = [];
            }
            
            Log::info('NotificacionesController: Vista cargada exitosamente');
            
            return view('modules.configuracion.notificaciones.index', compact(
                'empresa', 
                'configuracion', 
                'userData',
                'tiposHabilitados',
                'frecuencias', 
                'canalesHabilitados',
                'notificacionesRecientes'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de notificaciones: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback: intentar cargar vista con datos mínimos
            try {
                return view('modules.configuracion.notificaciones.index', [
                    'empresa' => (object)['id' => null, 'nombre' => 'Sin empresa'],
                    'configuracion' => [
                        'tipos_habilitados' => ['evaluaciones', 'sistema'],
                        'frecuencias' => ['evaluaciones' => 7, 'vencimientos' => 3, 'recordatorios' => 1],
                        'canales_habilitados' => ['email', 'dashboard'],
                        'notificaciones_email' => true,
                        'notificaciones_sistema' => true,
                        'frecuencia_resumen' => 'diario'
                    ],
                    'userData' => session('user_data'),
                    'tiposHabilitados' => ['evaluaciones', 'sistema'],
                    'frecuencias' => ['evaluaciones' => 7, 'vencimientos' => 3, 'recordatorios' => 1],
                    'canalesHabilitados' => ['email', 'dashboard'],
                    'notificacionesRecientes' => [],
                    'database_available' => false,
                    'error_message' => 'No se pudo conectar a la base de datos. Mostrando valores por defecto.'
                ]);
            } catch (\Exception $fallbackException) {
                Log::error('Error crítico en fallback de notificaciones: ' . $fallbackException->getMessage());
                return back()->with('error', 'Error crítico al cargar la configuración de notificaciones');
            }
        }
    }
    
    /**
     * Actualizar configuración de notificaciones
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'notificaciones_email' => 'boolean',
                'notificaciones_sistema' => 'boolean',
                'notificaciones_hallazgos' => 'boolean',
                'notificaciones_psicosocial' => 'boolean',
                'frecuencia_resumen' => 'required|in:diario,semanal,nunca',
                'destinatarios_adicionales' => 'nullable|array',
                'destinatarios_adicionales.*' => 'email',
                'notificaciones_usuarios_nuevos' => 'boolean',
                'notificaciones_tareas' => 'boolean'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_notificaciones)) {
                $empresa->configuracion_notificaciones = [];
            }
            
            // Preservar plantillas de correo existentes
            $plantillasExistentes = $empresa->configuracion_notificaciones['plantillas_correo'] ?? [];
            
            // Actualizar configuración
            $empresa->configuracion_notificaciones = [
                'notificaciones_email' => $request->boolean('notificaciones_email'),
                'notificaciones_sistema' => $request->boolean('notificaciones_sistema'),
                'notificaciones_hallazgos' => $request->boolean('notificaciones_hallazgos'),
                'notificaciones_psicosocial' => $request->boolean('notificaciones_psicosocial'),
                'frecuencia_resumen' => $request->frecuencia_resumen,
                'destinatarios_adicionales' => $request->destinatarios_adicionales ?? [],
                'plantillas_correo' => $plantillasExistentes,
                'notificaciones_usuarios_nuevos' => $request->boolean('notificaciones_usuarios_nuevos'),
                'notificaciones_tareas' => $request->boolean('notificaciones_tareas')
            ];
            
            $empresa->save();
            
            return back()->with('success', 'Configuración de notificaciones actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de notificaciones: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de notificaciones');
        }
    }
    
    /**
     * Actualizar tipos de notificaciones habilitados
     */
    public function updateTipos(Request $request)
    {
        try {
            $request->validate([
                'tipos' => 'nullable|array',
                'tipos.*' => 'string|in:evaluaciones,vencimientos,sistema,recordatorios'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_notificaciones)) {
                $empresa->configuracion_notificaciones = [];
            }
            
            // Actualizar tipos habilitados
            $empresa->configuracion_notificaciones['tipos_habilitados'] = $request->tipos ?? [];
            
            $empresa->save();
            
            return back()->with('success', 'Tipos de notificaciones actualizados correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar tipos de notificaciones: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar los tipos de notificaciones');
        }
    }
    
    /**
     * Actualizar frecuencias de notificaciones
     */
    public function updateFrecuencias(Request $request)
    {
        try {
            $request->validate([
                'freq_evaluaciones' => 'required|integer|in:1,3,7,15,30',
                'freq_vencimientos' => 'required|integer|in:1,3,7',
                'freq_recordatorios' => 'required|integer|in:1,3,7'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_notificaciones)) {
                $empresa->configuracion_notificaciones = [];
            }
            
            // Actualizar frecuencias
            $empresa->configuracion_notificaciones['frecuencias'] = [
                'evaluaciones' => (int)$request->freq_evaluaciones,
                'vencimientos' => (int)$request->freq_vencimientos,
                'recordatorios' => (int)$request->freq_recordatorios
            ];
            
            $empresa->save();
            
            return back()->with('success', 'Frecuencias de notificaciones actualizadas correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar frecuencias de notificaciones: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar las frecuencias de notificaciones');
        }
    }
    
    /**
     * Actualizar canales de notificaciones
     */
    public function updateCanales(Request $request)
    {
        try {
            $request->validate([
                'canales' => 'nullable|array',
                'canales.*' => 'string|in:email,push,sms,dashboard'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_notificaciones)) {
                $empresa->configuracion_notificaciones = [];
            }
            
            // Actualizar canales habilitados
            $empresa->configuracion_notificaciones['canales_habilitados'] = $request->canales ?? [];
            
            $empresa->save();
            
            return back()->with('success', 'Canales de notificaciones actualizados correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar canales de notificaciones: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar los canales de notificaciones');
        }
    }
    
    /**
     * Guardar plantilla de correo
     */
    public function guardarPlantilla(Request $request)
    {
        try {
            $request->validate([
                'nombre_plantilla' => 'required|string|max:100',
                'asunto_plantilla' => 'required|string|max:200',
                'tipo_plantilla' => 'required|string|in:bienvenida,tarea,hallazgo,psicosocial,general',
                'contenido_html' => 'required|string',
                'plantilla_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_notificaciones)) {
                $empresa->configuracion_notificaciones = [
                    'plantillas_correo' => []
                ];
            }
            
            // Inicializar plantillas si no existen
            if (!isset($empresa->configuracion_notificaciones['plantillas_correo'])) {
                $empresa->configuracion_notificaciones['plantillas_correo'] = [];
            }
            
            // Editar o crear nueva plantilla
            if ($request->plantilla_id) {
                // Editar plantilla existente
                foreach ($empresa->configuracion_notificaciones['plantillas_correo'] as $key => $plantilla) {
                    if ($plantilla['id'] == $request->plantilla_id) {
                        $empresa->configuracion_notificaciones['plantillas_correo'][$key]['nombre'] = $request->nombre_plantilla;
                        $empresa->configuracion_notificaciones['plantillas_correo'][$key]['asunto'] = $request->asunto_plantilla;
                        $empresa->configuracion_notificaciones['plantillas_correo'][$key]['tipo'] = $request->tipo_plantilla;
                        $empresa->configuracion_notificaciones['plantillas_correo'][$key]['contenido_html'] = $request->contenido_html;
                        $empresa->configuracion_notificaciones['plantillas_correo'][$key]['updated_at'] = now();
                    }
                }
            } else {
                // Crear nueva plantilla
                $newPlantilla = [
                    'id' => uniqid('email_tpl_'),
                    'nombre' => $request->nombre_plantilla,
                    'asunto' => $request->asunto_plantilla,
                    'tipo' => $request->tipo_plantilla,
                    'contenido_html' => $request->contenido_html,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $empresa->configuracion_notificaciones['plantillas_correo'][] = $newPlantilla;
            }
            
            $empresa->save();
            
            return back()->with('success', 'Plantilla de correo guardada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al guardar plantilla de correo: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar la plantilla de correo');
        }
    }
    
    /**
     * Eliminar plantilla de correo
     */
    public function eliminarPlantilla(Request $request)
    {
        try {
            $request->validate([
                'plantilla_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_notificaciones['plantillas_correo'])) {
                return back()->with('error', 'Empresa o plantillas no encontradas');
            }
            
            // Filtrar plantillas para remover la seleccionada
            $empresa->configuracion_notificaciones['plantillas_correo'] = array_filter(
                $empresa->configuracion_notificaciones['plantillas_correo'], 
                function($plantilla) use ($request) {
                    return $plantilla['id'] != $request->plantilla_id;
                }
            );
            
            // Reindexar el array
            $empresa->configuracion_notificaciones['plantillas_correo'] = array_values(
                $empresa->configuracion_notificaciones['plantillas_correo']
            );
            
            $empresa->save();
            
            return back()->with('success', 'Plantilla de correo eliminada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar plantilla de correo: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la plantilla de correo');
        }
    }
    
    /**
     * Enviar notificación de prueba
     */
    public function enviarPrueba(Request $request)
    {
        try {
            $request->validate([
                'destinatario' => 'required|email',
                'plantilla_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_notificaciones['plantillas_correo'])) {
                return back()->with('error', 'Empresa o plantillas no encontradas');
            }
            
            // Buscar plantilla
            $plantilla = null;
            foreach ($empresa->configuracion_notificaciones['plantillas_correo'] as $p) {
                if ($p['id'] == $request->plantilla_id) {
                    $plantilla = $p;
                    break;
                }
            }
            
            if (!$plantilla) {
                return back()->with('error', 'Plantilla no encontrada');
            }
            
            // Datos para la notificación basados en la plantilla seleccionada
            $notificacionData = [
                'empresa_id' => $empresa->id,
                'tipo' => $plantilla['tipo'] ?? 'general',
                'asunto' => $plantilla['asunto'],
                'mensaje' => $plantilla['contenido_html'],
                'destinatario' => $request->destinatario,
                'enviado_por' => session('user_data')['id'] ?? null,
                'estado' => 'pendiente',
                'prioridad' => $request->prioridad ?? 'normal',
                'fecha_creacion' => now(),
                'fecha_envio' => null
            ];
            
            // Crear notificación
            $this->notificacionService->crearNotificacion($notificacionData);
            
            return back()->with('success', 'Notificación de prueba enviada correctamente a ' . $request->destinatario);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de prueba: ' . $e->getMessage());
            return back()->with('error', 'Error al enviar la notificación de prueba');
        }
    }
}
