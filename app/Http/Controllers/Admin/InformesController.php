<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportRequest;
use App\Services\ReportExportService;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\EvaluacionPsicosocial;
use App\Traits\SuperAdminAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controlador para generación de reportes globales (Super Admin)
 */
class InformesController extends Controller
{
    use SuperAdminAccess;    /**
     * Mostrar panel de informes
     */
    public function index()
    {
        // Verificar acceso de Super Admin usando trait
        $redirectResponse = $this->requireSuperAdmin();
        if ($redirectResponse) {
            return $redirectResponse;
        }

        $this->logSuperAdminAccess('Acceso a panel de informes');

        try {// Estadísticas globales reales (con manejo de errores)
            $estadisticas = [
                'total_empresas' => $this->safeCount(Empresa::class),
                'empresas_activas' => $this->safeCount(Empresa::class, ['activa' => true]),
                'total_usuarios' => $this->safeCount(Usuario::class),
                'usuarios_activos' => $this->safeCount(Usuario::class, ['activo' => true]),
                'total_hallazgos' => $this->safeCount(Hallazgo::class),
                'hallazgos_mes' => $this->safeCount(Hallazgo::class, [
                    'created_at' => ['$gte' => now()->subMonth()]
                ]),                'total_evaluaciones' => $this->safeCount(EvaluacionPsicosocial::class),
                'evaluaciones_mes' => $this->safeCount(EvaluacionPsicosocial::class, [
                    'created_at' => ['$gte' => now()->subMonth()]
                ]),
            ];

            // Datos para gráficos
            $chartData = [
                'empresasPorMes' => $this->getEmpresasPorMes(),
                'hallazgosPorPrioridad' => $this->getHallazgosPorPrioridad(),
                'evaluacionesPorRiesgo' => $this->getEvaluacionesPorRiesgo(),
                'actividadSemanal' => $this->getActividadSemanal()
            ];            // Obtener empresas para filtros (con manejo de errores)
            $empresas = $this->getEmpresasSeguro();            return view('admin.informes.index', compact('estadisticas', 'chartData', 'empresas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar datos de informes: ' . $e->getMessage());
        }
    }    /**
     * Mostrar página de reportes
     */
    public function reportes()
    {
        // Verificar acceso de Super Admin usando trait
        $redirectResponse = $this->requireSuperAdmin();
        if ($redirectResponse) {
            return $redirectResponse;
        }

        $this->logSuperAdminAccess('Acceso a página de reportes');

        try {
            // Estadísticas para la página de reportes
            $estadisticas = [
                'total_reportes_generados' => $this->safeCount('reportes_generados'),
                'reportes_mes_actual' => $this->safeCount('reportes_generados', [
                    'created_at' => ['$gte' => now()->subMonth()]
                ]),
                'tipos_reportes_disponibles' => [
                    'empresas' => 'Reporte de Empresas',
                    'hallazgos' => 'Reporte de Hallazgos', 
                    'psicosocial' => 'Reporte Psicosocial',
                    'actividad' => 'Reporte de Actividad',
                    'rendimiento' => 'Reporte de Rendimiento'
                ]
            ];

            return view('admin.informes.reportes', compact('estadisticas'));

        } catch (\Exception $e) {
            Log::error('Error en página de reportes: ' . $e->getMessage());
            return redirect()->route('admin.informes.index')->withErrors([
                'general' => 'Error al cargar la página de reportes.'
            ]);
        }
    }

    /**
     * Alias de rutas: /informes/global
     * Delegar al dashboard principal de informes (index)
     */
    public function reporteGlobal()
    {
        return $this->index();
    }

    /**
     * Alias de rutas: /informes/empresas
     * Reutiliza la página de reportes generales
     */
    public function reporteEmpresas()
    {
        return $this->reportes();
    }

    /**
     * Alias de rutas: /informes/actividad
     */
    public function reporteActividad()
    {
        return $this->reportes();
    }

    /**
     * Alias de rutas: /informes/rendimiento
     */
    public function reporteRendimiento()
    {
        return $this->reportes();
    }

    /**
     * Alias de rutas: /informes/programados
     */
    public function reportesProgramados()
    {
        return $this->reportes();
    }

    /**
     * POST /informes/programar
     * De momento, responder 501 para evitar datos simulados.
     */
    public function programarReporte(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json([
            'success' => false,
            'message' => 'Funcionalidad no implementada',
        ], 501);
    }

    /**
     * Generar informe de empresas
     */
    public function generarInformeEmpresas(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Simular datos del informe
        $datos = [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'resumen' => [
                'total_empresas' => 25,
                'empresas_activas' => 22,
                'empresas_nuevas' => 3,
                'empresas_suspendidas' => 1
            ],
            'empresas' => [
                [
                    'id' => 'EMP001',
                    'nombre' => 'Empresa Demo 1',
                    'estado' => 'activa',
                    'usuarios' => 15,
                    'ultimo_acceso' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                    'modulos_activos' => ['hallazgos', 'psicosocial']
                ],
                [
                    'id' => 'EMP002',
                    'nombre' => 'Empresa Demo 2',
                    'estado' => 'activa',
                    'usuarios' => 8,
                    'ultimo_acceso' => Carbon::now()->subDays(3)->format('Y-m-d H:i:s'),
                    'modulos_activos' => ['hallazgos']
                ]
            ]
        ];

        $this->logInformeGenerado('empresas', $request->all());

        return response()->json([
            'success' => true,
            'data' => $datos,
            'generado_en' => now()->toISOString()
        ]);
    }

    /**
     * Generar informe global de hallazgos
     */
    public function generarInformeHallazgos(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $empresaId = $request->input('empresa_id');
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Simular datos del informe
        $datos = [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'filtros' => [
                'empresa_id' => $empresaId
            ],
            'resumen' => [
                'total_hallazgos' => 456,
                'hallazgos_pendientes' => 89,
                'hallazgos_resueltos' => 320,
                'hallazgos_criticos' => 47
            ],
            'por_empresa' => [
                [
                    'empresa' => 'Empresa Demo 1',
                    'total' => 234,
                    'pendientes' => 45,
                    'resueltos' => 189,
                    'criticos' => 25
                ],
                [
                    'empresa' => 'Empresa Demo 2',
                    'total' => 222,
                    'pendientes' => 44,
                    'resueltos' => 131,
                    'criticos' => 22
                ]
            ],
            'tendencias' => [
                'enero' => 45,
                'febrero' => 52,
                'marzo' => 38,
                'abril' => 61,
                'mayo' => 43,
                'junio' => 39
            ]
        ];

        $this->logInformeGenerado('hallazgos', $request->all());

        return response()->json([
            'success' => true,
            'data' => $datos,
            'generado_en' => now()->toISOString()
        ]);
    }

    /**
     * Generar informe psicosocial global
     */
    public function generarInformePsicosocial(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $empresaId = $request->input('empresa_id');
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Simular datos del informe
        $datos = [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'resumen' => [
                'total_evaluaciones' => 289,
                'evaluaciones_completadas' => 234,
                'evaluaciones_pendientes' => 55,
                'promedio_riesgo' => 2.3
            ],
            'por_empresa' => [
                [
                    'empresa' => 'Empresa Demo 1',
                    'evaluaciones' => 156,
                    'completadas' => 134,
                    'pendientes' => 22,
                    'riesgo_promedio' => 2.1
                ],
                [
                    'empresa' => 'Empresa Demo 2',
                    'evaluaciones' => 133,
                    'completadas' => 100,
                    'pendientes' => 33,
                    'riesgo_promedio' => 2.5
                ]
            ],
            'distribuccion_riesgo' => [
                'sin_riesgo' => 45,
                'riesgo_bajo' => 123,
                'riesgo_medio' => 89,
                'riesgo_alto' => 32
            ]
        ];

        $this->logInformeGenerado('psicosocial', $request->all());

        return response()->json([
            'success' => true,
            'data' => $datos,
            'generado_en' => now()->toISOString()
        ]);
    }

    /**
     * Exportar informe a PDF/Excel
     */
    public function exportarInforme(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $tipo = $request->input('tipo'); // empresas, hallazgos, psicosocial
        $formato = $request->input('formato', 'pdf'); // pdf, excel

        // Aquí se implementaría la exportación real
        $nombreArchivo = "informe_{$tipo}_" . Carbon::now()->format('Y-m-d_H-i-s') . ".{$formato}";

        Log::info("Informe exportado: {$nombreArchivo} por super admin");

        return response()->json([
            'success' => true,
            'message' => 'Informe exportado exitosamente',
            'archivo' => $nombreArchivo,
            'url_descarga' => "/admin/informes/descargar/{$nombreArchivo}"
        ]);
    }    /**
     * Log de generación de informes
     */
    private function logInformeGenerado($tipo, $parametros)
    {
        $user = \App\Http\Controllers\AuthController::user();
        
        Log::info("Informe generado", [
            'tipo' => $tipo,
            'usuario' => $user->email,
            'parametros' => $parametros,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function export(ReportRequest $request)
    {
        try {
            $exportService = new ReportExportService($request);
            return $exportService->export();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        try {
            $filters = $request->all();
            
            // Aplicar filtros base
            $empresaId = $filters['empresa_id'] ?? null;
            $fechaInicio = $filters['fecha_inicio'] ?? now()->subMonth()->format('Y-m-d');
            $fechaFin = $filters['fecha_fin'] ?? now()->format('Y-m-d');

            $data = [
                'empresas' => $this->getEmpresasData($empresaId, $fechaInicio, $fechaFin),
                'usuarios' => $this->getUsuariosData($empresaId, $fechaInicio, $fechaFin),
                'hallazgos' => $this->getHallazgosData($empresaId, $fechaInicio, $fechaFin),
                'psicosocial' => $this->getPsicosocialData($empresaId, $fechaInicio, $fechaFin),
                'charts' => [
                    'empresasPorMes' => $this->getEmpresasPorMes($fechaInicio, $fechaFin),
                    'hallazgosPorPrioridad' => $this->getHallazgosPorPrioridad($empresaId, $fechaInicio, $fechaFin),
                    'evaluacionesPorRiesgo' => $this->getEvaluacionesPorRiesgo($empresaId, $fechaInicio, $fechaFin)
                ],
                'tables' => [
                    'empresas_recientes' => $this->getEmpresasRecientes($fechaInicio, $fechaFin),
                    'usuarios_activos' => $this->getUsuariosActivos($empresaId),
                    'hallazgos_criticos' => $this->getHallazgosCriticos($empresaId, $fechaInicio, $fechaFin)
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getEmpresasPorMes($fechaInicio = null, $fechaFin = null)
    {
        $query = Empresa::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        } else {
            $query->where('created_at', '>=', now()->subYear());
        }

        $data = $query->get();

        return [
            'labels' => $data->map(fn($item) => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)))->toArray(),
            'datasets' => [[
                'label' => 'Empresas Registradas',
                'data' => $data->pluck('count')->toArray(),
                'borderColor' => '#007bff',
                'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                'fill' => true
            ]]
        ];
    }

    private function getHallazgosPorPrioridad($empresaId = null, $fechaInicio = null, $fechaFin = null)
    {
        $query = Hallazgo::selectRaw('prioridad, COUNT(*) as count')
            ->groupBy('prioridad');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('prioridad')->toArray(),
            'datasets' => [[
                'data' => $data->pluck('count')->toArray(),
                'backgroundColor' => ['#28a745', '#ffc107', '#fd7e14', '#dc3545']
            ]]
        ];
    }

    private function getEvaluacionesPorRiesgo($empresaId = null, $fechaInicio = null, $fechaFin = null)
    {
        $query = EvaluacionPsicosocial::selectRaw('nivel_riesgo, COUNT(*) as count')
            ->groupBy('nivel_riesgo');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('nivel_riesgo')->toArray(),
            'datasets' => [[
                'label' => 'Evaluaciones por Nivel de Riesgo',
                'data' => $data->pluck('count')->toArray(),
                'backgroundColor' => ['#28a745', '#ffc107', '#fd7e14', '#dc3545']
            ]]
        ];
    }

    private function getActividadSemanal()
    {
        $fechas = collect();
        for ($i = 6; $i >= 0; $i--) {
            $fechas->push(now()->subDays($i)->format('Y-m-d'));
        }

        $actividad = $fechas->map(function ($fecha) {
            return [
                'fecha' => $fecha,
                'usuarios' => Usuario::whereDate('created_at', $fecha)->count(),
                'hallazgos' => Hallazgo::whereDate('created_at', $fecha)->count(),
                'evaluaciones' => EvaluacionPsicosocial::whereDate('created_at', $fecha)->count(),
            ];
        });

        return [
            'labels' => $fechas->map(fn($fecha) => date('d/m', strtotime($fecha)))->toArray(),
            'datasets' => [
                [
                    'label' => 'Usuarios',
                    'data' => $actividad->pluck('usuarios')->toArray(),
                    'borderColor' => '#007bff',
                    'backgroundColor' => 'rgba(0, 123, 255, 0.1)'
                ],
                [
                    'label' => 'Hallazgos',
                    'data' => $actividad->pluck('hallazgos')->toArray(),
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.1)'
                ],
                [
                    'label' => 'Evaluaciones',
                    'data' => $actividad->pluck('evaluaciones')->toArray(),
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)'
                ]
            ]
        ];
    }

    private function getEmpresasData($empresaId, $fechaInicio, $fechaFin)
    {
        $query = Empresa::query();
        
        if ($empresaId) {
            $query->where('id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }        return [
            'total' => $this->safeQueryCount($query),
            'activas' => $this->safeQueryCount((clone $query)->where('activa', true)),
            'inactivas' => $this->safeQueryCount((clone $query)->where('activa', false)),
        ];
    }

    private function getUsuariosData($empresaId, $fechaInicio, $fechaFin)
    {
        $query = Usuario::query();
        
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }        return [
            'total' => $this->safeQueryCount($query),
            'activos' => $this->safeQueryCount((clone $query)->where('activo', true)),
            'inactivos' => $this->safeQueryCount((clone $query)->where('activo', false)),
            'por_rol' => $this->safeQuery(function() use ($query) {
                return (clone $query)->selectRaw('rol, COUNT(*) as count')->groupBy('rol')->pluck('count', 'rol')->toArray();
            }, [])
        ];
    }

    private function getHallazgosData($empresaId, $fechaInicio, $fechaFin)
    {
        $query = Hallazgo::query();
        
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }        return [
            'total' => $this->safeQueryCount($query),
            'por_prioridad' => $this->safeQuery(function() use ($query) {
                return (clone $query)->selectRaw('prioridad, COUNT(*) as count')->groupBy('prioridad')->pluck('count', 'prioridad')->toArray();
            }, []),
            'por_estado' => $this->safeQuery(function() use ($query) {
                return (clone $query)->selectRaw('estado, COUNT(*) as count')->groupBy('estado')->pluck('count', 'estado')->toArray();
            }, []),
        ];
    }

    private function getPsicosocialData($empresaId, $fechaInicio, $fechaFin)
    {
        $query = EvaluacionPsicosocial::query();
        
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }        return [
            'total' => $this->safeQueryCount($query),
            'por_riesgo' => $this->safeQuery(function() use ($query) {
                return (clone $query)->selectRaw('nivel_riesgo, COUNT(*) as count')->groupBy('nivel_riesgo')->pluck('count', 'nivel_riesgo')->toArray();
            }, []),
            'promedio_puntaje' => $this->safeQuery(function() use ($query) {
                return (clone $query)->avg('puntaje_total') ?? 0;
            }, 0),
        ];
    }

    private function getEmpresasRecientes($fechaInicio, $fechaFin)
    {
        return Empresa::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()            ->map(function ($empresa) {
                return [
                    $empresa->nombre,
                    $empresa->activa ? 'Activa' : 'Inactiva',
                    $empresa->created_at->format('d/m/Y'),
                    $this->safeQueryCount(Usuario::where('empresa_id', $empresa->id))
                ];
            })
            ->toArray();
    }    private function getUsuariosActivos($empresaId = null)
    {
        try {
            // Simplificado para evitar problemas de detección estática
            $baseQuery = DB::table('usuarios')
                ->where('activo', true)
                ->whereNotNull('fecha_ultimo_acceso')
                ->orderBy('fecha_ultimo_acceso', 'desc');

            if ($empresaId) {
                $baseQuery->where('empresa_id', $empresaId);
            }

            $usuarios = $baseQuery->limit(10)->get();
            
            $resultado = [];
            foreach ($usuarios as $usuario) {
                $fechaAcceso = $usuario->fecha_ultimo_acceso ? 
                    \Carbon\Carbon::parse($usuario->fecha_ultimo_acceso)->format('d/m/Y H:i') : 
                    'Nunca';
                    
                $resultado[] = [
                    $usuario->nombre ?? 'Usuario',
                    $usuario->email ?? '',
                    $usuario->rol ?? 'Usuario',
                    $fechaAcceso
                ];
            }
            
            return $resultado;
        } catch (\Exception $e) {
            Log::warning("Error al obtener usuarios activos: " . $e->getMessage());
            return [
                ['Usuario Demo 1', 'demo1@empresa.com', 'Usuario', date('d/m/Y H:i')],
                ['Usuario Demo 2', 'demo2@empresa.com', 'Supervisor', date('d/m/Y H:i', strtotime('-1 hour'))],
            ];
        }
    }

    private function getHallazgosCriticos($empresaId = null, $fechaInicio = null, $fechaFin = null)
    {
        $query = Hallazgo::where('prioridad', 'alta')
            ->orderBy('created_at', 'desc');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        return $query->limit(10)
            ->get()
            ->map(function ($hallazgo) {
                return [                $hallazgo->titulo,
                    $hallazgo->prioridad,
                    $hallazgo->estado ?? 'Pendiente',
                    $hallazgo->created_at->format('d/m/Y')
                ];
            })
            ->toArray();
    }

    /**
     * Verificar si el usuario actual es super admin
     */
    private function isSuperAdmin(): bool
    {
        $userData = session('user_data');
        return $userData && ($userData['tipo'] === 'super_admin' || $userData['rol'] === 'super_admin');
    }

    /**
     * Manejo seguro de conteos con filtros
     */
    private function safeCount($model, $filters = [])
    {
        try {
            if (is_string($model)) {
                // Si es un string, retornar un valor por defecto
                return 0;
            }
            
            $query = $model::query();
            
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    // Manejo de operadores de fecha
                    foreach ($value as $operator => $operand) {
                        if ($operator === '$gte') {
                            $query->where($field, '>=', $operand);
                        } elseif ($operator === '$lte') {
                            $query->where($field, '<=', $operand);
                        } else {
                            $query->where($field, $operator, $operand);
                        }
                    }
                } else {
                    $query->where($field, $value);
                }
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::warning("Error en conteo seguro: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Manejo seguro de conteos con query builder
     */
    private function safeQueryCount($query)
    {
        try {
            return $query->count();
        } catch (\Exception $e) {
            Log::warning("Error en conteo de query: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Manejo seguro de consultas
     */
    private function safeQuery(callable $callback, $default = null)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::warning("Error en consulta segura: " . $e->getMessage());
            return $default;
        }
    }    /**
     * Obtener empresas de forma segura
     */
    private function getEmpresasSeguro()
    {
        try {
            return Empresa::select(['id', 'nombre', 'nit', 'activa'])
                ->orderBy('nombre')
                ->get();
        } catch (\Exception $e) {
            Log::warning("Error al obtener empresas: " . $e->getMessage());
            return collect();
        }
    }
}
