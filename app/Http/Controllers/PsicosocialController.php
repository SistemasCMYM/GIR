<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnostico;
use App\Models\Hoja;
use App\Models\Empresas\Empleado;
use App\Models\Empresas\Empresa;
use App\Models\User;
use App\Services\BateriaPsicosocialService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PsicosocialController extends Controller
{
    protected $bateriaPsicosocialService;

    public function __construct(BateriaPsicosocialService $bateriaPsicosocialService)
    {
        $this->bateriaPsicosocialService = $bateriaPsicosocialService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('user_data') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            // Obtener empresa_id desde los datos de sesión
            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            if (!$empresa_id) {
                return view('modules.psicosocial.index')->with([
                    'estadisticas' => ['diagnosticos' => 0, 'total_evaluaciones' => 0, 'evaluaciones_completadas' => 0, 'evaluaciones_pendientes' => 0, 'empleados_evaluados' => 0],
                    'distribucionRiesgo' => [],
                    'diagnosticos' => collect(),
                    'estadisticasTarjetas' => ['total' => 0, 'completados' => 0, 'en_proceso' => 0, 'pendientes' => 0],
                    'empresa_nombre' => 'Sin empresa asignada',
                    'error' => 'No tiene una empresa asignada. Contacte al administrador.'
                ]);
            }
            
            // Obtener información de la empresa actual
            $empresa = Empresa::find($empresa_id);

            $empresa_nombre = $empresa ? ($empresa->nombre ?? $empresa->razon_social) : ($empresa_data['nombre'] ?? 'Empresa actual');
            
            // Obtener estadísticas generales base desde el servicio
            $estadisticas = $this->bateriaPsicosocialService->obtenerEstadisticasGenerales($empresa_id);
            
            // Obtener distribución de riesgo
            $distribucionRiesgoRaw = $this->bateriaPsicosocialService->obtenerDistribucionRiesgo($empresa_id);
            
            // Transformar distribución de riesgo para la vista
            $distribucionRiesgo = $this->transformarDistribucionRiesgo($distribucionRiesgoRaw);
            
            // Obtener tarjetas de aplicación (diagnósticos)
            $diagnosticos = Diagnostico::on('mongodb_psicosocial')
                ->where('empresa_id', $empresa_id)
                ->orderBy('_fechaCreado', 'desc')
                ->get();

            // Acumuladores globales
            $globalTotal = 0;
            $globalCompletadas = 0;
            $globalPendientes = 0;
            $globalEnProceso = 0;

            // Calcular estadísticas y cargar profesional para cada diagnóstico (alineado con la vista)
            foreach ($diagnosticos as $diagnostico) {
                $estadisticasDiagnostico = $this->bateriaPsicosocialService->obtenerEstadisticasDiagnostico($diagnostico->_id);
                $total = (int)($estadisticasDiagnostico['total'] ?? 0);
                $completadas = (int)($estadisticasDiagnostico['completadas'] ?? 0);
                $pendientes = (int)($estadisticasDiagnostico['pendientes'] ?? 0);
                $enProceso = max($total - $completadas - $pendientes, 0);

                // Campos esperados por la vista
                $diagnostico->total_empleados = $total;
                $diagnostico->completados = $completadas;
                $diagnostico->pendientes = $pendientes;
                $diagnostico->en_proceso = $enProceso;
                $diagnostico->porcentaje_completado = $total > 0 ? round(($completadas / $total) * 100, 1) : 0;

                // Compatibilidad con banderas de filtro en tarjetas (si existen en el documento)
                $diagnostico->tiene_filtro = (bool)($diagnostico->filtro ?? $diagnostico->filtro_key ?? false);
                $diagnostico->empleados_pendientes_asignar = (int)($diagnostico->empleados_pendientes_asignar ?? 0);

                // Mantener campos previamente agregados
                $diagnostico->empleados_asignados = $total;
                $diagnostico->evaluaciones_completadas = $completadas;
                $diagnostico->evaluaciones_pendientes = $pendientes;
                $diagnostico->progreso = $diagnostico->porcentaje_completado;
                $diagnostico->distribucion_riesgo = $estadisticasDiagnostico['niveles_riesgo'];

                // Acumular globales
                $globalTotal += $total;
                $globalCompletadas += $completadas;
                $globalPendientes += $pendientes;
                $globalEnProceso += $enProceso;

                // Cargar información del profesional asignado
                $profesional = null;
                if (!empty($diagnostico->profesional_id)) {
                    $profesional = User::where('id', $diagnostico->profesional_id)
                        ->where('perfil_id', 'psicologo')
                        ->first();
                }
                if ($profesional) {
                    $diagnostico->profesional_info = [
                        'nombre' => trim(($profesional->primerNombre ?? '') . ' ' . ($profesional->primerApellido ?? '')),
                        'email' => $profesional->email ?? '',
                        'id' => $profesional->id
                    ];
                } else {
                    $diagnostico->profesional_info = null;
                }
            }

            // Obtener estadísticas de tarjetas (si el servicio las provee)
            $estadisticasTarjetas = $this->bateriaPsicosocialService->obtenerEstadisticasTarjetas($empresa_id);

            // Alinear claves usadas en la vista para métricas superiores
            // Importante: los valores calculados aquí deben prevalecer sobre los del servicio
            // por eso se hace merge primero con el servicio y luego con nuestros overrides.
            $estadisticas = array_merge($estadisticas ?? [], [
                // alias para compatibilidad con vistas antiguas
                'diagnosticos' => $diagnosticos->count(),
                'total_diagnosticos' => $diagnosticos->count(),
                'evaluaciones_completadas' => $globalCompletadas,
                'evaluaciones_en_proceso' => $globalEnProceso,
                'evaluaciones_pendientes' => $globalPendientes,
                // Total de empleados asignados a las tarjetas
                'empleados_evaluados' => $globalTotal,
                'porcentaje_completado' => $globalTotal > 0 ? round(($globalCompletadas / $globalTotal) * 100, 1) : 0,
            ]);

            // Mapear distribución de riesgo a etiquetas esperadas por la vista
            // Construimos un arreglo niveles_riesgo con llaves "Sin Riesgo", "Riesgo Bajo", ...
            $nivelesMap = [
                'Sin Riesgo' => 0,
                'Riesgo Bajo' => 0,
                'Riesgo Medio' => 0,
                'Riesgo Alto' => 0,
                'Riesgo Muy Alto' => 0,
            ];
            foreach ($distribucionRiesgo as $item) {
                if (isset($nivelesMap[$item['label']])) {
                    $nivelesMap[$item['label']] = (int)($item['value'] ?? 0);
                }
            }
            $estadisticas['niveles_riesgo'] = $nivelesMap;

            Log::info('PsicosocialController@index - Datos para vista', [
                'empresa_id' => $empresa_id,
                'empresa_nombre' => $empresa_nombre,
                'estadisticas' => $estadisticas,
                'distribucionRiesgo' => $distribucionRiesgo,
                'estadisticasTarjetas' => $estadisticasTarjetas,
                'total_diagnosticos' => $diagnosticos->count()
            ]);

            return view('modules.psicosocial.index', compact(
                'estadisticas',
                'distribucionRiesgo',
                'diagnosticos',
                'estadisticasTarjetas',
                'empresa_nombre'
            ));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@index: ' . $e->getMessage());
            return view('modules.psicosocial.index')->with([
                'estadisticas' => ['diagnosticos' => 0, 'total_evaluaciones' => 0, 'evaluaciones_completadas' => 0, 'evaluaciones_pendientes' => 0, 'empleados_evaluados' => 0],
                'distribucionRiesgo' => [],
                'diagnosticos' => collect(),
                'estadisticasTarjetas' => ['total' => 0, 'completados' => 0, 'en_proceso' => 0, 'pendientes' => 0],
                'empresa_nombre' => 'Empresa actual'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $empresa_id = session('empresa_data.id');

            // Obtener profesionales (psicólogos) habilitados para la empresa
            $profesionales = User::whereHas('cuenta', function($query) use ($empresa_id) {
                $query->where('empresa_id', $empresa_id);
            })->where('perfil_id', 'psicologo')->get();

            // Obtener contratos ya asignados a otros diagnósticos
            $contratosAsignados = Diagnostico::on('mongodb_psicosocial')
                ->where('empresa_id', $empresa_id)
                ->whereNotNull('contrato_key')
                ->pluck('contrato_key')
                ->toArray();

            // Obtener empleados por tipo de contrato que no están asignados a otros diagnósticos
            $empleadosPorContrato = Empleado::where('empresa_id', $empresa_id)
                ->whereNotNull('contrato_key')
                ->whereNotIn('contrato_key', $contratosAsignados)
                ->get()
                ->groupBy('contrato_key')
                ->map(function($empleados, $contrato_key) {
                    $primerEmpleado = $empleados->first();
                    return [
                        'key' => $contrato_key,
                        'label' => $primerEmpleado->contrato_label ?? $contrato_key,
                        'count' => $empleados->count(),
                        'empleados' => $empleados
                    ];
                });

            return view('modules.psicosocial.create', compact('profesionales', 'empleadosPorContrato'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@create: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:70',
            'evaluador_id' => 'required|exists:users,id',
            'fecha_evaluacion' => 'required|date',
            'observaciones' => 'nullable|string|max:255',
            'filtro_empleados' => 'nullable|string',
        ]);

        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            // Obtener información del contrato si se especificó
            $contrato_label = null;
            if ($request->filtro_empleados) {
                $empleadoContrato = Empleado::where('empresa_id', $empresa_id)
                    ->where('contrato_key', $request->filtro_empleados)
                    ->first();
                $contrato_label = $empleadoContrato ? $empleadoContrato->contrato_label : $request->filtro_empleados;
            }

            // Crear el diagnóstico
            $diagnostico = new Diagnostico();
            $diagnostico->setConnection('mongodb_psicosocial');
            $diagnostico->empresa_id = $empresa_id;
            $diagnostico->descripcion = $request->descripcion;
            $diagnostico->profesional_id = $request->evaluador_id; // Usar profesional_id como en el esquema original
            $diagnostico->objetivo = $request->descripcion; // También en objetivo
            $diagnostico->observaciones = $request->observaciones;
            $diagnostico->contrato_key = $request->filtro_empleados;
            $diagnostico->contrato_label = $contrato_label;
            $diagnostico->filtro_key = $request->filtro_empleados; // También guardar en filtro_key
            $diagnostico->filtro = !empty($request->filtro_empleados); // Marcar filtro como true si hay filtro_key
            $diagnostico->clave = 'PSI-' . date('Y-m-d') . '-' . uniqid(); // Generar clave única
            $diagnostico->grupo = date('Y'); // Año actual como grupo
            $diagnostico->estado = 'creado';
            $diagnostico->_fechaCreado = now();
            $diagnostico->save();

            // TODO: Implement employee assignment logic if needed
            // Employee assignment would be handled separately or through another service method

            return redirect()->route('psicosocial.show', $diagnostico->_id)
                ->with('success', 'Tarjeta de aplicación psicosocial creada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el diagnóstico: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            $diagnostico = Diagnostico::on('mongodb_psicosocial')->find($id);
            
            if (!$diagnostico) {
                Log::error('Diagnóstico no encontrado', ['id' => $id]);
                return redirect()->route('psicosocial.index')->with('error', 'Diagnóstico no encontrado.');
            }
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            Log::info('PsicosocialController@show validation', [
                'diagnostico_id' => $id,
                'diagnostico_empresa_id' => $diagnostico->empresa_id,
                'session_empresa_id' => $empresa_id
            ]);
            
            if ($empresa_id && $diagnostico->empresa_id && $diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para ver este diagnóstico.');
            }

            // Obtener estadísticas del diagnóstico usando el servicio
            $estadisticas = $this->bateriaPsicosocialService->obtenerEstadisticasDiagnostico($id);

            // Obtener hojas (empleados) del diagnóstico con información del empleado
            $hojas = Hoja::on('mongodb_psicosocial')->where('diagnostico_id', $id)->get();
            
            Log::info('Total hojas encontradas para diagnóstico', [
                'diagnostico_id' => $id,
                'total_hojas' => $hojas->count()
            ]);
            
            // Enriquecer hojas con información de empleados
            $hojasConEmpleados = [];
            foreach ($hojas as $hoja) {
                $empleado = null;
                if ($hoja->empleado_id) {
                    // Buscar empleado en la base de datos principal
                    $empleado = Empleado::where('_id', $hoja->empleado_id)->first();
                }
                
                // Acceder a los estados usando el método toArray() que es más confiable
                $hojaArray = $hoja->toArray();
                $estadoDatos = $hojaArray['datos'] ?? 'pendiente';
                $estadoIntralaboral = $hojaArray['intralaboral'] ?? 'pendiente';
                $estadoExtralaboral = $hojaArray['extralaboral'] ?? 'pendiente';
                $estadoEstres = $hojaArray['estres'] ?? 'pendiente';
                
                // Log detallado para cada hoja
                Log::info('Estados de hoja individual', [
                    'hoja_id' => $hoja->_id,
                    'empleado_id' => $hoja->empleado_id,
                    'empleado_nombre' => $empleado ? $empleado->primerNombre . ' ' . $empleado->primerApellido : $hoja->nombre,
                    'datos' => $estadoDatos,
                    'intralaboral' => $estadoIntralaboral,
                    'extralaboral' => $estadoExtralaboral,
                    'estres' => $estadoEstres,
                    'diagnostico_id' => $hoja->diagnostico_id
                ]);
                
                $estados = [$estadoDatos, $estadoIntralaboral, $estadoExtralaboral, $estadoEstres];
                $completados = count(array_filter($estados, function($estado) { return $estado === 'completado'; }));
                $progreso = round(($completados / 4) * 100, 1);
                
                // Calcular nivel de riesgo y puntaje solo si todos están completados
                $nivelRiesgo = 'Sin evaluar';
                $puntajeTotal = 0;
                $badgeClass = 'secondary';
                
                if ($completados === 4) {
                    // Calcular puntaje total transformado
                    $puntajeIntralaboral = $hoja->puntaje_intralaboral['total'] ?? 0;
                    $puntajeExtralaboral = $hoja->puntaje_extralaboral['total'] ?? 0;
                    $puntajeEstres = $hoja->puntaje_estres['total'] ?? 0;
                    
                    // Usar factor de transformación simplificado
                    $puntajeTotal = $puntajeIntralaboral + $puntajeExtralaboral + $puntajeEstres;
                    
                    Log::info('Calculando puntajes para hoja completada', [
                        'hoja_id' => $hoja->_id,
                        'puntaje_intralaboral' => $puntajeIntralaboral,
                        'puntaje_extralaboral' => $puntajeExtralaboral,
                        'puntaje_estres' => $puntajeEstres,
                        'puntaje_total' => $puntajeTotal
                    ]);
                    
                    // Determinar nivel de riesgo
                    if ($puntajeTotal <= 20.6) {
                        $nivelRiesgo = 'Sin Riesgo';
                        $badgeClass = 'success';
                    } elseif ($puntajeTotal <= 26) {
                        $nivelRiesgo = 'Bajo';
                        $badgeClass = 'info';
                    } elseif ($puntajeTotal <= 31.5) {
                        $nivelRiesgo = 'Medio';
                        $badgeClass = 'warning';
                    } elseif ($puntajeTotal <= 38.7) {
                        $nivelRiesgo = 'Alto';
                        $badgeClass = 'danger';
                    } else {
                        $nivelRiesgo = 'Muy Alto';
                        $badgeClass = 'dark';
                    }
                }
                
                $hojasConEmpleados[] = [
                    'hoja' => $hoja,
                    'empleado' => $empleado,
                    'progreso' => $progreso,
                    'nivel_riesgo' => $nivelRiesgo,
                    'puntaje_total' => $puntajeTotal,
                    'badge_class' => $badgeClass,
                    'estado_datos' => $estadoDatos,
                    'estado_intralaboral' => $estadoIntralaboral,
                    'estado_extralaboral' => $estadoExtralaboral,
                    'estado_estres' => $estadoEstres
                ];
            }

            return view('modules.psicosocial.show', compact('diagnostico', 'estadisticas', 'hojas', 'hojasConEmpleados'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('psicosocial.index')->with('error', 'Diagnóstico no encontrado.');
        }
    }

    /**
     * Summary report for all evaluations or specific diagnosis.
     */
    public function summaryReport(Request $request)
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            $diagnostico_id = $request->get('diagnostico_id');

            // Obtener resumen completo usando el servicio
            $resumen = $this->bateriaPsicosocialService->obtenerEstadisticasGenerales($empresa_id);

            return view('modules.psicosocial.summary-report', compact('resumen'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@summaryReport: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al generar el reporte resumen.');
        }
    }

    /**
     * Get risk distribution data for AJAX calls.
     */
    public function getRiskDistributionData()
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            // Intentar obtener datos reales de MongoDB
            try {
                $distribucionRiesgoRaw = $this->bateriaPsicosocialService->obtenerDistribucionRiesgo($empresa_id);
                $distribucionRiesgo = $this->transformarDistribucionRiesgo($distribucionRiesgoRaw);
            } catch (\Exception $mongoException) {
                // Si MongoDB no está disponible, devolver datos de ejemplo
                Log::warning('MongoDB no disponible, usando datos de ejemplo: ' . $mongoException->getMessage());
                $distribucionRiesgo = $this->getDistribucionRiesgoDemoData();
            }
            
            return response()->json($distribucionRiesgo);
        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@getRiskDistributionData: ' . $e->getMessage());
            // Devolver datos de ejemplo en caso de error
            return response()->json($this->getDistribucionRiesgoDemoData());
        }
    }

    /**
     * Obtener datos de ejemplo para distribución de riesgo
     */
    private function getDistribucionRiesgoDemoData()
    {
        return [
            [
                'label' => 'Sin Riesgo',
                'value' => 0,
                'percentage' => 0,
                'color' => '#008235'
            ],
            [
                'label' => 'Riesgo Bajo',
                'value' => 0,
                'percentage' => 0,
                'color' => '#00D364'
            ],
            [
                'label' => 'Riesgo Medio',
                'value' => 0,
                'percentage' => 0,
                'color' => '#FFD600'
            ],
            [
                'label' => 'Riesgo Alto',
                'value' => 0,
                'percentage' => 0,
                'color' => '#DD0505'
            ],
            [
                'label' => 'Riesgo Muy Alto',
                'value' => 0,
                'percentage' => 0,
                'color' => '#A30203'
            ]
        ];
    }

    /**
     * Export to PDF.
     */
    public function exportPDF(Request $request)
    {
        try {
            $empresa_id = Auth::user()->cuenta->empresa_id;
            $diagnostico_id = $request->get('diagnostico_id');

            return $this->bateriaPsicosocialService->exportarPDF($empresa_id, $diagnostico_id, $request->all());

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@exportPDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar PDF.');
        }
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        try {
            $empresa_id = Auth::user()->cuenta->empresa_id;
            $diagnostico_id = $request->get('diagnostico_id');

            return $this->bateriaPsicosocialService->exportarExcel($empresa_id, $diagnostico_id, $request->all());

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@exportExcel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar Excel.');
        }
    }

    /**
     * Statistics page.
     */
    public function estadisticas()
    {
        try {
            $empresa_id = Auth::user()->cuenta->empresa_id;
            
            // Obtener estadísticas completas usando el servicio
            $estadisticas = $this->bateriaPsicosocialService->obtenerEstadisticasGenerales($empresa_id);

            return view('modules.psicosocial.estadisticas', compact('estadisticas'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@estadisticas: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al cargar estadísticas.');
        }
    }

    /**
     * Show detailed summary for a specific diagnosis.
     */
    public function resumen(string $id)
    {
    try {
            if (!session('authenticated') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            $diagnostico = Diagnostico::on('mongodb_psicosocial')->find($id);
            if (!$diagnostico) {
                return back()->with('error', 'Diagnóstico no encontrado');
            }
            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            if ($empresa_id && $diagnostico->empresa_id && $diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para ver este diagnóstico.');
            }

            $filtros = request()->only(['area', 'sede', 'ciudad', 'tipo_contrato', 'proceso', 'forma']);
            $filtros = array_filter($filtros, function($value) { return !empty($value) && $value !== ''; });

            $hojas = Hoja::on('mongodb_psicosocial')->where('diagnostico_id', $id)->get();
            $hojasCompletadas = $hojas->filter(function($hoja) {
                $hojaArray = $hoja->toArray();
                return ($hojaArray['datos'] === 'completado' || $hojaArray['datos'] === true)
                    && ($hojaArray['intralaboral'] === 'completado' || $hojaArray['intralaboral'] === true)
                    && ($hojaArray['extralaboral'] === 'completado' || $hojaArray['extralaboral'] === true)
                    && ($hojaArray['estres'] === 'completado' || $hojaArray['estres'] === true);
            });

            // Variables por defecto para evitar errores en la vista
            $resumen = [
                'completo' => [
                    'total' => 0,
                    'completadas' => 0,
                    'pendientes' => 0,
                    'en_proceso' => 0,
                    'distribucion_riesgo' => ['niveles' => []],
                    'total_psicosocial' => ['por_instrumento' => []],
                    'intralaboral_general' => [
                        'poblacion' => 0,
                        'dominios' => [],
                        'dimensiones' => []
                    ],
                    'intralaboral_a' => [
                        'poblacion' => 0,
                        'dominios' => [],
                        'dimensiones' => []
                    ],
                    'intralaboral_b' => [
                        'poblacion' => 0,
                        'dominios' => [],
                        'dimensiones' => []
                    ],
                    'extralaboral' => [
                        'poblacion' => 0,
                        'dimensiones' => []
                    ],
                    'estres' => [
                        'poblacion' => 0,
                        'dimensiones' => []
                    ],
                    'datos_sociodemograficos' => []
                ]
            ];
            $estadisticas = [
                'total' => $hojas->count(),
                'completadas' => $hojasCompletadas->count(),
                'pendientes' => $hojas->count() - $hojasCompletadas->count(),
                'en_proceso' => 0
            ];
            $dimensionesA = [];
            $dimensionesB = [];
            $dimensionesExtra = [];
            $dimensionesEstres = [];
            $categoriasSociodemograficas = [];
            
            // Filtros disponibles para la vista
            $opciones = [
                'areas' => [],
                'sedes' => [],
                'ciudades' => [],
                'tipos_contrato' => [],
                'procesos' => []
            ];
            
            // Datos de la empresa para la vista
            $empresaData = ['nombre' => $empresa_data['name'] ?? 'Empresa sin nombre'];

            // Si hay hojas completadas, obtener datos reales
            if ($hojasCompletadas->count() > 0) {
                $resumen = $this->bateriaPsicosocialService->obtenerEstadisticasResumen($id, $filtros);
                $estadisticas = [
                    'total' => $resumen['completo']['total'] ?? $hojas->count(),
                    'completadas' => $resumen['completo']['completadas'] ?? $hojasCompletadas->count(),
                    'pendientes' => $resumen['completo']['pendientes'] ?? ($hojas->count() - $hojasCompletadas->count()),
                    'en_proceso' => $resumen['completo']['en_proceso'] ?? 0
                ];
                $dimensionesA = $resumen['completo']['intralaboral_a']['dimensiones'] ?? [];
                $dimensionesB = $resumen['completo']['intralaboral_b']['dimensiones'] ?? [];
                $dimensionesExtra = $resumen['completo']['extralaboral']['dimensiones'] ?? [];
                $dimensionesEstres = $resumen['completo']['estres']['dimensiones'] ?? [];
                $categoriasSociodemograficas = $resumen['completo']['datos_sociodemograficos'] ?? [];
            }

            return view('psicosocial.resumen', compact(
                'diagnostico',
                'resumen',
                'estadisticas',
                'dimensionesA',
                'dimensionesB',
                'dimensionesExtra',
                'dimensionesEstres',
                'categoriasSociodemograficas',
                'hojasCompletadas',
                'hojas',
                'filtros',
                'opciones',
                'empresaData'
            ));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@resumen: ' . $e->getMessage(), [
                'diagnostico_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('psicosocial.index')
                ->with('error', 'Error al cargar el resumen del diagnóstico: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed 9-section summary for a specific diagnosis using the new complete service.
     */
    public function resumenNueveSecciones(Request $request, string $id = null)
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            if (!$empresa_id) {
                return redirect()->route('psicosocial.index')->with('error', 'No tiene una empresa asignada.');
            }

            $diagnostico = null;
            if ($id) {
                $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
                // Verificar que el diagnóstico pertenece a la empresa del usuario actual
                if ($diagnostico->empresa_id !== $empresa_id) {
                    abort(403, 'No tiene permisos para ver este diagnóstico.');
                }
            }

            // Obtener filtros del request
            $filtros = $request->only([
                'genero', 'edad_min', 'edad_max', 'cargo', 'area', 'estado_civil', 
                'nivel_estudio', 'estrato', 'tipo_contrato', 'sede', 'ciudad'
            ]);

            // Limpiar filtros vacíos
            $filtros = array_filter($filtros, function($value) {
                return !empty($value) && $value !== '' && $value !== null;
            });

            Log::info('PsicosocialController@resumenNueveSecciones', [
                'empresa_id' => $empresa_id,
                'diagnostico_id' => $id,
                'filtros' => $filtros
            ]);

            // Obtener resumen completo de 9 secciones usando el servicio consolidado
            $resumenCompleto = $this->bateriaPsicosocialService->obtenerEstadisticasGenerales($empresa_id);

            // Preparar datos para la vista
            $data = [
                'diagnostico' => $diagnostico,
                'resumen' => $resumenCompleto,
                'filtros_aplicados' => $filtros,
                'empresa_nombre' => $empresa_data['name'] ?? 'Sin nombre'
            ];

            Log::info('PsicosocialController@resumenNueveSecciones data prepared', [
                'total_hojas' => $resumenCompleto['total_hojas'] ?? 0,
                'total_hojas_a' => $resumenCompleto['total_hojas_a'] ?? 0,
                'total_hojas_b' => $resumenCompleto['total_hojas_b'] ?? 0,
                'secciones_disponibles' => array_keys($resumenCompleto)
            ]);

            return view('modules.psicosocial.resumen-completo', $data);

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@resumenNueveSecciones: ' . $e->getMessage(), [
                'diagnostico_id' => $id ?? 'null',
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('psicosocial.index')
                ->with('error', 'Error al cargar el resumen de diagnóstico: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para editar este diagnóstico.');
            }

            // Obtener profesionales (psicólogos) habilitados para la empresa
            $profesionales = User::whereHas('cuenta', function($query) use ($empresa_id) {
                $query->where('empresa_id', $empresa_id);
            })->where('perfil_id', 'psicologo')->get();

            return view('modules.psicosocial.edit', compact('diagnostico', 'profesionales'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@edit: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Diagnóstico no encontrado.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:70',
            'evaluador_id' => 'required|exists:users,id',
            'fecha_evaluacion' => 'required|date',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para editar este diagnóstico.');
            }

            $diagnostico->update([
                'descripcion' => $request->descripcion,
                'evaluador_id' => $request->evaluador_id,
                'fecha_evaluacion' => $request->fecha_evaluacion,
                'observaciones' => $request->observaciones,
            ]);

            return redirect()->route('psicosocial.show', $id)
                ->with('success', 'Diagnóstico actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el diagnóstico: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para eliminar este diagnóstico.');
            }

            // Eliminar hojas asociadas
            Hoja::where('diagnostico_id', $id)->delete();
            
            // Eliminar diagnóstico
            $diagnostico->delete();

            return redirect()->route('psicosocial.index')
                ->with('success', 'Diagnóstico eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@destroy: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')
                ->with('error', 'Error al eliminar el diagnóstico.');
        }
    }

    /**
     * Show intervention plan for a specific diagnosis.
     */
    public function intervencion(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para ver este diagnóstico.');
            }

            // Obtener estadísticas para el plan de intervención
            $estadisticas = $this->bateriaPsicosocialService->obtenerEstadisticasDiagnostico($id);
            $resumen = $this->bateriaPsicosocialService->obtenerEstadisticasResumen($id, []);

            return view('modules.psicosocial.intervencion', compact('diagnostico', 'estadisticas', 'resumen'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@intervencion: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al cargar el plan de intervención.');
        }
    }

    /**
     * Get employee detail for a specific evaluation.
     */
    public function obtenerDetalleEmpleado(string $id, string $hojaId)
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $diagnostico = Diagnostico::find($id);
            $hoja = Hoja::on('mongodb_psicosocial')->find($hojaId);
            
            if (!$diagnostico || !$hoja) {
                return response()->json(['error' => 'Diagnóstico o hoja no encontrados'], 404);
            }
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            Log::info('obtenerDetalleEmpleado validation', [
                'diagnostico_empresa_id' => $diagnostico->empresa_id,
                'session_empresa_id' => $empresa_id,
                'hoja_diagnostico_id' => $hoja->diagnostico_id,
                'requested_diagnostico_id' => $id
            ]);
            
            // Validaciones básicas
            if ($hoja->diagnostico_id !== $id) {
                return response()->json(['error' => 'La evaluación no pertenece a este diagnóstico.'], 403);
            }
            
            if ($empresa_id && $diagnostico->empresa_id && $diagnostico->empresa_id !== $empresa_id) {
                return response()->json(['error' => 'No tiene permisos para ver esta evaluación.'], 403);
            }

            // Calcular nivel de riesgo individual
            $nivelRiesgo = $this->calcularNivelTotalHoja($hoja);
            
            return response()->json([
                'hoja' => $hoja,
                'nivel_riesgo' => $nivelRiesgo,
                'diagnostico' => $diagnostico
            ]);

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@obtenerDetalleEmpleado: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al obtener detalle del empleado.'], 500);
        }
    }

    /**
     * Show evaluation form for a specific employee.
     */
    public function evaluacion(string $diagnosticoId, string $hojaId)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($diagnosticoId);
            $hoja = Hoja::on('mongodb_psicosocial')->findOrFail($hojaId);
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id || $hoja->diagnostico_id !== $diagnosticoId) {
                abort(403, 'No tiene permisos para ver esta evaluación.');
            }

            return view('modules.psicosocial.evaluacion', compact('diagnostico', 'hoja'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@evaluacion: ' . $e->getMessage());
            return redirect()->route('psicosocial.show', $diagnosticoId)->with('error', 'Error al cargar la evaluación.');
        }
    }

    /**
     * Export specific diagnosis to Excel.
     */
    public function exportarExcel(string $id)
    {
        try {
            $empresa_id = Auth::user()->cuenta->empresa_id;
            return $this->bateriaPsicosocialService->exportarExcel($empresa_id, $id);

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@exportarExcel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar Excel.');
        }
    }

    /**
     * Export specific diagnosis to PDF.
     */
    public function exportarPDF(string $id, string $tipo = 'completo')
    {
        try {
            $empresa_id = Auth::user()->cuenta->empresa_id;
            return $this->bateriaPsicosocialService->exportarPDF($empresa_id, $id, ['tipo' => $tipo]);

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@exportarPDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar PDF.');
        }
    }

    /**
     * Print specific diagnosis report.
     */
    public function imprimir(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para imprimir este diagnóstico.');
            }
            // Obtener resumen completo para impresión
            $resumen = $this->bateriaPsicosocialService->obtenerEstadisticasResumen($id, []);
            // Obtener hojas del diagnóstico
            $hojas = Hoja::on('mongodb_psicosocial')->where('diagnostico_id', $id)->get();
            return view('modules.psicosocial.imprimir', compact('diagnostico', 'resumen', 'hojas'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@imprimir: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al preparar la impresión.');
        }
    }

    /**
     * Show results for a specific diagnosis.
     */
    public function resultados(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para ver este diagnóstico.');
            }
            $estadisticas = $this->bateriaPsicosocialService->obtenerEstadisticasDiagnostico($id);
            $resumen = $this->bateriaPsicosocialService->obtenerEstadisticasResumen($id, []);
            $hojas = Hoja::on('mongodb_psicosocial')->where('diagnostico_id', $id)->get();

            // SIEMPRE definir $hojasConResultados aunque no haya hojas
            $hojasConResultadosArr = collect();
            if ($hojas && $hojas->count() > 0) {
                foreach ($hojas as $hoja) {
                    $hojaArray = $hoja->toArray();
                    $completados = ($hojaArray['datos'] === 'completado' || $hojaArray['datos'] === true)
                        && ($hojaArray['intralaboral'] === 'completado' || $hojaArray['intralaboral'] === true)
                        && ($hojaArray['extralaboral'] === 'completado' || $hojaArray['extralaboral'] === true)
                        && ($hojaArray['estres'] === 'completado' || $hojaArray['estres'] === true);
                    if ($completados) {
                        $nivelRiesgo = $this->calcularNivelTotalHoja($hoja);
                        $puntajeTotal = ($hoja->puntaje_intralaboral['total'] ?? 0)
                            + ($hoja->puntaje_extralaboral['total'] ?? 0)
                            + ($hoja->puntaje_estres['total'] ?? 0);
                        $hojasConResultadosArr->push([
                            'hoja' => $hoja,
                            'nivel_riesgo' => $nivelRiesgo,
                            'puntaje_total' => $puntajeTotal
                        ]);
                    }
                }
                $hojasConResultados = $hojasConResultadosArr->count();
            } else {
                $hojasConResultados = 0;
            }

            return view('modules.psicosocial.resultados', compact('diagnostico', 'estadisticas', 'resumen', 'hojas', 'hojasConResultados', 'hojasConResultadosArr'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@resultados: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al cargar los resultados.');
        }
    }

    /**
     * Export specific diagnosis (generic method).
     */
    public function exportar(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para exportar este diagnóstico.');
            }
            // Por defecto, exportar a PDF
            return $this->exportarPDF($id);

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@exportar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar el diagnóstico.');
        }
    }

    /**
     * Show specific diagnosis details.
     */
    public function showDiagnostico(string $id)
    {
        // Redirigir al método show principal
        return $this->show($id);
    }

    /**
     * Show results for specific diagnosis.
     */
    public function diagnosticoResultados(string $id)
    {
        try {
            $diagnostico = Diagnostico::on('mongodb_psicosocial')->findOrFail($id);
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para ver este diagnóstico.');
            }
            $estadisticas = $this->bateriaPsicosocialService->obtenerEstadisticasDiagnostico($id);
            $resumen = $this->bateriaPsicosocialService->obtenerEstadisticasResumen($id, []);
            $hojas = Hoja::on('mongodb_psicosocial')->where('diagnostico_id', $id)->get();

            // SIEMPRE definir $hojasConResultados aunque no haya hojas
            $hojasConResultadosArr = collect();
            if ($hojas && $hojas->count() > 0) {
                foreach ($hojas as $hoja) {
                    $hojaArray = $hoja->toArray();
                    $completados = ($hojaArray['datos'] === 'completado' || $hojaArray['datos'] === true)
                        && ($hojaArray['intralaboral'] === 'completado' || $hojaArray['intralaboral'] === true)
                        && ($hojaArray['extralaboral'] === 'completado' || $hojaArray['extralaboral'] === true)
                        && ($hojaArray['estres'] === 'completado' || $hojaArray['estres'] === true);
                    if ($completados) {
                        $nivelRiesgo = $this->calcularNivelTotalHoja($hoja);
                        $puntajeTotal = ($hoja->puntaje_intralaboral['total'] ?? 0)
                            + ($hoja->puntaje_extralaboral['total'] ?? 0)
                            + ($hoja->puntaje_estres['total'] ?? 0);
                        $hojasConResultadosArr->push([
                            'hoja' => $hoja,
                            'nivel_riesgo' => $nivelRiesgo,
                            'puntaje_total' => $puntajeTotal
                        ]);
                    }
                }
                $hojasConResultados = $hojasConResultadosArr->count();
            } else {
                $hojasConResultados = 0;
            }

            return view('modules.psicosocial.diagnostico.resultados', compact('diagnostico', 'estadisticas', 'resumen', 'hojas', 'hojasConResultados', 'hojasConResultadosArr', 'hojas'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@diagnosticoResultados: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al cargar los resultados.');
        }
    }

    /**
     * Send evaluation link to employee.
     */
    public function enviarLinkEmpleado(Request $request, string $id)
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return redirect()->route('login.nit')->with('error', 'Debe autenticarse para acceder al módulo psicosocial');
            }

            $diagnostico = Diagnostico::findOrFail($id);
            
            // Verificar que el diagnóstico pertenece a la empresa del usuario actual
            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            if ($diagnostico->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para acceder a este diagnóstico.');
            }

            $hojaId = $request->input('hoja_id');
            $hoja = Hoja::findOrFail($hojaId);

            Log::info('Envío de link de evaluación', [
                'diagnostico_id' => $id,
                'hoja_id' => $hojaId,
                'empleado' => $hoja->nombre
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link de evaluación enviado exitosamente a ' . $hoja->nombre
            ]);

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@enviarLinkEmpleado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el link de evaluación.'
            ], 500);
        }
    }

    /**
     * Create application card view.
     */
    public function createApplicationCard()
    {
        // Redirigir al create principal
        return $this->create();
    }

    /**
     * Store application card (legacy compatibility).
     */
    public function storeApplicationCard(Request $request)
    {
        // Redirigir al store principal
        return $this->store($request);
    }

    /**
     * Show application card (legacy compatibility).
     */
    public function showApplicationCard(string $id)
    {
        // Redirigir al show principal
        return $this->show($id);
    }

    /**
     * Detailed report for specific evaluation.
     */
    public function detailedReport(string $evaluationId)
    {
        try {
            $hoja = Hoja::findOrFail($evaluationId);
            
            // Verificar que la hoja pertenece a la empresa del usuario actual
            $empresa_id = Auth::user()->cuenta->empresa_id;
            if ($hoja->empresa_id !== $empresa_id) {
                abort(403, 'No tiene permisos para ver esta evaluación.');
            }

            // Calcular nivel de riesgo individual
            $nivelRiesgo = $this->calcularNivelTotalHoja($hoja);
            
            return view('modules.psicosocial.detailed-report', compact('hoja', 'nivelRiesgo'));

        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@detailedReport: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')->with('error', 'Error al cargar el reporte detallado.');
        }
    }

    /**
     * Get global statistics data for AJAX calls.
     */
    public function getGlobalStatisticsData()
    {
        try {
            // Verificar autenticación y empresa desde la sesión
            if (!session('authenticated') || !session('empresa_data')) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            
            return response()->json($this->bateriaPsicosocialService->obtenerEstadisticasGenerales($empresa_id));
        } catch (\Exception $e) {
            Log::error('Error en PsicosocialController@getGlobalStatisticsData: ' . $e->getMessage());
            $empresa_data = session('empresa_data');
            $empresa_id = $empresa_data['id'] ?? null;
            return response()->json($this->bateriaPsicosocialService->obtenerEstadisticasGenerales($empresa_id));
        }
    }

    /**
     * Transformar distribución de riesgo para la vista
     */
    private function transformarDistribucionRiesgo($distribucionRaw)
    {
        $coloresRiesgo = [
            'sin_riesgo' => '#008235',
            'bajo' => '#00D364',
            'medio' => '#FFD600',
            'alto' => '#DA3D3D',
            'muy_alto' => '#D10000'
        ];
        
        $etiquetasRiesgo = [
            'sin_riesgo' => 'Sin Riesgo',
            'bajo' => 'Riesgo Bajo',
            'medio' => 'Riesgo Medio',
            'alto' => 'Riesgo Alto',
            'muy_alto' => 'Riesgo Muy Alto'
        ];
        
        $total = $distribucionRaw['total'] ?? 0;
        $distribucionTransformada = [];
        
        foreach (['sin_riesgo', 'bajo', 'medio', 'alto', 'muy_alto'] as $nivel) {
            $valor = $distribucionRaw[$nivel] ?? 0;
            $porcentaje = $total > 0 ? round(($valor / $total) * 100, 1) : 0;
            
            $distribucionTransformada[] = [
                'label' => $etiquetasRiesgo[$nivel],
                'value' => $valor,
                'percentage' => $porcentaje,
                'color' => $coloresRiesgo[$nivel],
                'nivel' => $nivel,
                'css_class' => 'risk-' . str_replace('_', '-', $nivel)
            ];
        }
        
        return $distribucionTransformada;
    }

    /**
     * Obtener filtros disponibles basados en las hojas
     */
    private function obtenerFiltrosDisponibles($hojas)
    {
        $filtros = [
            'areas' => [],
            'sedes' => [],
            'ciudades' => [],
            'tipos_contrato' => [],
            'procesos' => [],
            'formas' => ['A', 'B']
        ];

        foreach ($hojas as $hoja) {
            // Obtener datos generales si existen
            $datos = \App\Models\Datos::on('mongodb_psicosocial')
                ->where('hoja_id', $hoja->_id)
                ->first();
            
            if ($datos) {
                $datosArray = $datos->toArray();
                
                // Extraer filtros únicos
                if (!empty($datosArray['departamento_cargo'])) {
                    $filtros['areas'][] = $datosArray['departamento_cargo'];
                }
                if (!empty($datosArray['lugar_trabajo'])) {
                    $filtros['sedes'][] = $datosArray['lugar_trabajo'];
                }
                if (!empty($datosArray['lugar_residencia'])) {
                    $filtros['ciudades'][] = $datosArray['lugar_residencia'];
                }
                if (!empty($datosArray['tipo_contrato'])) {
                    $filtros['tipos_contrato'][] = $datosArray['tipo_contrato'];
                }
                if (!empty($datosArray['nombre_cargo'])) {
                    $filtros['procesos'][] = $datosArray['nombre_cargo'];
                }
            }
        }

        // Eliminar duplicados y ordenar
        foreach ($filtros as $key => $valores) {
            if (is_array($valores)) {
                $filtros[$key] = array_unique($valores);
                sort($filtros[$key]);
            }
        }

        return $filtros;
    }

    /**
     * Calcular nivel total de riesgo para una hoja
     */
    private function calcularNivelTotalHoja($hoja)
    {
        // Obtener puntajes totales
        $puntajeIntralaboral = $hoja->puntaje_intralaboral['total'] ?? 0;
        $puntajeExtralaboral = $hoja->puntaje_extralaboral['total'] ?? 0;
        $puntajeEstres = $hoja->puntaje_estres['total'] ?? 0;

        // Calcular puntaje total
        $puntajeTotal = $puntajeIntralaboral + $puntajeExtralaboral + $puntajeEstres;

        // Determinar nivel de riesgo
        if ($puntajeTotal <= 20.6) {
            return 'sin_riesgo';
        } elseif ($puntajeTotal <= 26) {
            return 'bajo';
        } elseif ($puntajeTotal <= 31.5) {
            return 'medio';
        } elseif ($puntajeTotal <= 38.7) {
            return 'alto';
        } else {
            return 'muy_alto';
        }
    }
}
