<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empresa;
use App\Services\ReportExportService;

class ReportesController extends Controller
{
    protected $reportExportService;
    
    public function __construct(ReportExportService $reportExportService)
    {
        $this->reportExportService = $reportExportService;
    }
    
    /**
     * Mostrar configuración de reportes
     */
    public function index()
    {
        try {
            Log::info('ReportesController@index: Iniciando método');
            
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            Log::info('ReportesController@index: Datos de sesión', [
                'empresa_data_presente' => $empresaData ? 'sí' : 'no',
                'usuario_data_presente' => $userData ? 'sí' : 'no',
                'empresa_id' => $empresaData['id'] ?? 'no_id',
                'usuario_email' => $userData['email'] ?? 'no_email'
            ]);
            
            if (!$empresaData) {
                Log::warning('ReportesController@index: No hay empresa seleccionada');
                return redirect()->route('dashboard')->with('error', 'No hay empresa seleccionada');
            }
            
            // Crear configuración por defecto si no hay conexión a MongoDB
            $configuracion = [
                'encabezado_personalizado' => false,
                'pie_pagina_personalizado' => false,
                'logo_en_reportes' => true,
                'formatos_disponibles' => ['pdf', 'excel', 'csv'],
                'plantillas_personalizadas' => [],
                'reportes_automaticos' => false,
                'programacion_reportes' => [],
                'destinatarios_reportes' => [],
                'incluir_graficos' => true,
                // Datos adicionales para el nuevo diseño
                'formato_predeterminado' => 'pdf',
                'calidad_imagenes' => 'media',
                'max_size_mb' => 50,
                'distribucion_automatica' => false,
                'emails_destinatarios' => '',
                'asunto_email' => 'Reporte Automático - {fecha}',
                'mensaje_email' => 'Se adjunta el reporte solicitado.',
                'backup_nube' => false,
                'proveedor_nube' => '',
                'carpeta_nube' => '/GIR365/Reportes',
                'retencion_dias' => 90,
                'total_reportes' => 15,
                'reportes_mes' => 8,
                'plantillas_activas' => 4,
                'reportes_programados' => 2,
                'espacio_usado' => 35,
                'plantillas' => [
                    [
                        'id' => 'plantilla_1',
                        'nombre' => 'Reporte Psicosocial Básico',
                        'tipo' => 'psicosocial',
                        'formato' => 'pdf',
                        'activa' => true
                    ],
                    [
                        'id' => 'plantilla_2', 
                        'nombre' => 'Reporte de Hallazgos',
                        'tipo' => 'hallazgos',
                        'formato' => 'excel',
                        'activa' => true
                    ]
                ],
                'reportes_programados' => [
                    [
                        'id' => 'prog_1',
                        'nombre' => 'Reporte Mensual',
                        'frecuencia' => 'mensual',
                        'proxima_ejecucion' => '2025-09-01 09:00',
                        'activo' => true
                    ]
                ]
            ];

            // Intentar obtener empresa desde MongoDB (opcional)
            try {
                $empresa = Empresa::where('id', $empresaData['id'])->first();
                if ($empresa && isset($empresa->configuracion_reportes)) {
                    $configuracion = array_merge($configuracion, $empresa->configuracion_reportes);
                    Log::info('ReportesController@index: Configuración cargada desde MongoDB');
                } else {
                    Log::info('ReportesController@index: Usando configuración por defecto (MongoDB no disponible o sin config)');
                }
            } catch (\Exception $mongoException) {
                Log::warning('ReportesController@index: Error de MongoDB, usando configuración por defecto', [
                    'error' => $mongoException->getMessage()
                ]);
            }

            // Crear objeto empresa simulado si no existe
            $empresa = $empresa ?? (object)[
                'id' => $empresaData['id'],
                'razon_social' => $empresaData['razon_social'] ?? 'Empresa Demo',
                'configuracion_reportes' => $configuracion
            ];
            
            Log::info('ReportesController@index: Configuración preparada exitosamente');
            
            return view('modules.configuracion.reportes.index', compact('empresa', 'configuracion', 'userData'));
            
        } catch (\Exception $e) {
            Log::error('ReportesController@index: Error general', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al cargar la configuración de reportes: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualizar configuración de reportes
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'encabezado_personalizado' => 'boolean',
                'pie_pagina_personalizado' => 'boolean',
                'logo_en_reportes' => 'boolean',
                'formatos_disponibles' => 'required|array',
                'formatos_disponibles.*' => 'in:pdf,excel,csv,word',
                'texto_encabezado' => 'nullable|string|max:500',
                'texto_pie_pagina' => 'nullable|string|max:500',
                'incluir_graficos' => 'boolean',
                'reportes_automaticos' => 'boolean',
                'programacion_reportes' => 'nullable|array',
                'destinatarios_reportes' => 'nullable|array'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_reportes)) {
                $empresa->configuracion_reportes = [];
            }
            
            // Actualizar configuración
            $empresa->configuracion_reportes = [
                'encabezado_personalizado' => $request->boolean('encabezado_personalizado'),
                'pie_pagina_personalizado' => $request->boolean('pie_pagina_personalizado'),
                'logo_en_reportes' => $request->boolean('logo_en_reportes'),
                'formatos_disponibles' => $request->formatos_disponibles,
                'texto_encabezado' => $request->texto_encabezado,
                'texto_pie_pagina' => $request->texto_pie_pagina,
                'incluir_graficos' => $request->boolean('incluir_graficos'),
                'reportes_automaticos' => $request->boolean('reportes_automaticos'),
                'programacion_reportes' => $request->programacion_reportes ?? [],
                'destinatarios_reportes' => $request->destinatarios_reportes ?? [],
                'plantillas_personalizadas' => $empresa->configuracion_reportes['plantillas_personalizadas'] ?? []
            ];
            
            $empresa->save();
            
            return back()->with('success', 'Configuración de reportes actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de reportes: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de reportes');
        }
    }
    
    /**
     * Guardar plantilla personalizada
     */
    public function guardarPlantilla(Request $request)
    {
        try {
            $request->validate([
                'nombre_plantilla' => 'required|string|max:100',
                'descripcion_plantilla' => 'nullable|string|max:255',
                'tipo_reporte' => 'required|string|in:hallazgos,psicosocial,general',
                'contenido_plantilla' => 'required|string',
                'plantilla_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_reportes)) {
                $empresa->configuracion_reportes = [
                    'plantillas_personalizadas' => []
                ];
            }
            
            // Inicializar plantillas si no existen
            if (!isset($empresa->configuracion_reportes['plantillas_personalizadas'])) {
                $empresa->configuracion_reportes['plantillas_personalizadas'] = [];
            }
            
            // Editar o crear nueva plantilla
            if ($request->plantilla_id) {
                // Editar plantilla existente
                foreach ($empresa->configuracion_reportes['plantillas_personalizadas'] as $key => $plantilla) {
                    if ($plantilla['id'] == $request->plantilla_id) {
                        $empresa->configuracion_reportes['plantillas_personalizadas'][$key]['nombre'] = $request->nombre_plantilla;
                        $empresa->configuracion_reportes['plantillas_personalizadas'][$key]['descripcion'] = $request->descripcion_plantilla;
                        $empresa->configuracion_reportes['plantillas_personalizadas'][$key]['tipo'] = $request->tipo_reporte;
                        $empresa->configuracion_reportes['plantillas_personalizadas'][$key]['contenido'] = $request->contenido_plantilla;
                        $empresa->configuracion_reportes['plantillas_personalizadas'][$key]['updated_at'] = now();
                    }
                }
            } else {
                // Crear nueva plantilla
                $newPlantilla = [
                    'id' => uniqid('plantilla_'),
                    'nombre' => $request->nombre_plantilla,
                    'descripcion' => $request->descripcion_plantilla,
                    'tipo' => $request->tipo_reporte,
                    'contenido' => $request->contenido_plantilla,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $empresa->configuracion_reportes['plantillas_personalizadas'][] = $newPlantilla;
            }
            
            $empresa->save();
            
            return back()->with('success', 'Plantilla guardada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al guardar plantilla: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar la plantilla');
        }
    }
    
    /**
     * Eliminar plantilla
     */
    public function eliminarPlantilla(Request $request)
    {
        try {
            $request->validate([
                'plantilla_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_reportes['plantillas_personalizadas'])) {
                return back()->with('error', 'Empresa o plantillas no encontradas');
            }
            
            // Filtrar plantillas para remover la seleccionada
            $empresa->configuracion_reportes['plantillas_personalizadas'] = array_filter(
                $empresa->configuracion_reportes['plantillas_personalizadas'], 
                function($plantilla) use ($request) {
                    return $plantilla['id'] != $request->plantilla_id;
                }
            );
            
            // Reindexar el array
            $empresa->configuracion_reportes['plantillas_personalizadas'] = array_values(
                $empresa->configuracion_reportes['plantillas_personalizadas']
            );
            
            $empresa->save();
            
            return back()->with('success', 'Plantilla eliminada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar plantilla: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la plantilla');
        }
    }
    
    /**
     * Previsualizar plantilla
     */
    public function previsualizarPlantilla(Request $request)
    {
        try {
            $request->validate([
                'plantilla_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_reportes['plantillas_personalizadas'])) {
                return response()->json(['error' => 'Empresa o plantillas no encontradas'], 404);
            }
            
            // Buscar la plantilla
            $plantilla = null;
            foreach ($empresa->configuracion_reportes['plantillas_personalizadas'] as $p) {
                if ($p['id'] == $request->plantilla_id) {
                    $plantilla = $p;
                    break;
                }
            }
            
            if (!$plantilla) {
                return response()->json(['error' => 'Plantilla no encontrada'], 404);
            }            // Como no tenemos acceso directo al ReportExportService, mostramos una vista previa básica
            $preview = '<div class="preview-header">Vista previa de plantilla: ' . htmlspecialchars($plantilla['nombre']) . '</div>';
            $preview .= '<div class="preview-content">' . nl2br(htmlspecialchars($plantilla['contenido'])) . '</div>';
            
            return response()->json(['preview' => $preview]);
            
        } catch (\Exception $e) {
            Log::error('Error al previsualizar plantilla: ' . $e->getMessage());
            return response()->json(['error' => 'Error al previsualizar la plantilla'], 500);
        }
    }
}
