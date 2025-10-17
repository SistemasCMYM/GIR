<?php

namespace App\Http\Controllers\Psicosocial;

use Illuminate\Http\Request;
use App\Models\EvaluacionPsicosocial;
use App\Traits\SuperAdminAccess;
use App\Traits\RoleBasedAccess;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Diagnostico;
use App\Models\Hoja;
use App\Models\Datos;
use App\Models\Actividad;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Services\BateriaPsicosocialService;
use App\Services\PsicosocialResumenService;
use App\Http\Controllers\Controller;

class PsicosocialController extends Controller
{
    use SuperAdminAccess, RoleBasedAccess {
        SuperAdminAccess::isSuperAdmin insteadof RoleBasedAccess;
        RoleBasedAccess::isSuperAdmin as isSuperAdminRoleBased;
        SuperAdminAccess::hasModuleAccess insteadof RoleBasedAccess;
        RoleBasedAccess::hasModuleAccess as hasModuleAccessRoleBased;
    }

    protected $bateriaPsicosocialService;
    protected $psicosocialResumenService;

    public function __construct(BateriaPsicosocialService $bateriaPsicosocialService, PsicosocialResumenService $psicosocialResumenService)
    {
        $this->bateriaPsicosocialService = $bateriaPsicosocialService;
        $this->psicosocialResumenService = $psicosocialResumenService;
    }

    /**
     * Display a listing of evaluaciones psicosociales for the current company.
     */
    public function index()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Verificar si es Super Admin o tiene rol con acceso especial
            $isSuperAdmin = esSuperAdmin($userData);
            $userRole = $userData['rol'] ?? 'usuario';

            // SuperAdmin tiene acceso completo sin restricciones
            if ($isSuperAdmin) {
                Log::info('PsicosocialController: Acceso SuperAdmin detectado', [
                    'usuario' => $userData['usuario'] ?? 'N/A',
                    'rol' => $userRole
                ]);
            } else {
                // Roles con acceso al módulo psicosocial
                $rolesAccesoPsico = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin', 'SuperAdmin', 'administrador_empresa', 'admin_empresa', 'psicologo', 'supervisor'];
                $hasModuleAccess = in_array($userRole, $rolesAccesoPsico);

                if (!$hasModuleAccess) {
                    return redirect()->route('error.permission-denied');
                }
            }

            // Si no hay empresa, SuperAdmin puede ver todas las empresas
            if (!$empresaData || !isset($empresaData['id'])) {
                if ($isSuperAdmin) {
                    // SuperAdmin puede acceder sin empresa específica
                    $empresaId = null;
                } else {
                    return view('modules.psicosocial.error', [
                        'mensaje' => 'No se ha seleccionado una empresa válida',
                        'error' => 'Empresa no encontrada'
                    ]);
                }
            } else {
                $empresaId = $empresaData['id'];
            }

            // Obtener diagnósticos
            $query = Diagnostico::orderBy('created_at', 'desc');

            // SuperAdmin puede ver todos los datos, otros usuarios solo de su empresa
            if (!$isSuperAdmin && $empresaId) {
                $query->where('empresa_id', $empresaId);
            }

            Log::info('PsicosocialController: Aplicando filtro', [
                'empresa_id' => $empresaId,
                'user_role' => $userRole,
                'is_super_admin' => $isSuperAdmin,
                'filtro_empresa' => !$isSuperAdmin && $empresaId ? 'SI' : 'NO'
            ]);

            $diagnosticos = $query->get();
            $diagnosticoIds = $diagnosticos->pluck('id')->toArray();

            // Obtener todas las hojas asociadas a los diagnósticos desde MongoDB
            $hojas = Hoja::whereIn('diagnostico_id', $diagnosticoIds)->get();

            // Helper para leer estados soportando nombres antiguos y nuevos
            $getEstado = function ($hoja, $clave) {
                // Mapa de equivalencias
                $map = [
                    'datos' => 'estado_datos_generales',
                    'intralaboral' => 'estado_intralaboral',
                    'extralaboral' => 'estado_extralaboral',
                    'estres' => 'estado_estres',
                ];
                $valor = null;
                if (isset($hoja->{$clave})) {
                    $valor = $hoja->{$clave};
                } elseif (isset($map[$clave]) && isset($hoja->{$map[$clave]})) {
                    $valor = $hoja->{$map[$clave]};
                }
                // Normalizar valores verdaderos a string esperado
                if ($valor === true || $valor === 1) return 'completado';
                if ($valor === false || $valor === 0) return 'pendiente';
                return is_string($valor) ? $valor : 'pendiente';
            };

            $totalDiagnosticos = $diagnosticos->count();

            // Estadísticas globales basadas en el estado real de las hojas
            $totalHojasAsignadas = $hojas->count();

            // Calcular completadas: todas las evaluaciones (datos, intralaboral, extralaboral, estres) deben estar en 'completado'
            $totalCompletadas = $hojas->filter(function ($hoja) use ($getEstado) {
                return $getEstado($hoja, 'datos') === 'completado' &&
                    $getEstado($hoja, 'intralaboral') === 'completado' &&
                    $getEstado($hoja, 'extralaboral') === 'completado' &&
                    $getEstado($hoja, 'estres') === 'completado';
            })->count();

            // Calcular en proceso: al menos una evaluación en 'en_progreso'
            $totalEnProceso = $hojas->filter(function ($hoja) use ($getEstado) {
                return $getEstado($hoja, 'datos') === 'en_progreso' ||
                    $getEstado($hoja, 'intralaboral') === 'en_progreso' ||
                    $getEstado($hoja, 'extralaboral') === 'en_progreso' ||
                    $getEstado($hoja, 'estres') === 'en_progreso';
            })->count();

            // Calcular pendientes: todas las evaluaciones en 'pendiente'
            $totalPendientes = $hojas->filter(function ($hoja) use ($getEstado) {
                return $getEstado($hoja, 'datos') === 'pendiente' &&
                    $getEstado($hoja, 'intralaboral') === 'pendiente' &&
                    $getEstado($hoja, 'extralaboral') === 'pendiente' &&
                    $getEstado($hoja, 'estres') === 'pendiente';
            })->count();

            // Empleados evaluados únicos
            $empleadosEvaluados = $hojas->pluck('empleado_id')->filter()->unique()->count();

            // Distribución de niveles de riesgo basada en hojas completadas
            $nivelesRiesgo = [
                'Sin Riesgo' => 0,
                'Riesgo Bajo' => 0,
                'Riesgo Medio' => 0,
                'Riesgo Alto' => 0,
                'Riesgo Muy Alto' => 0
            ];

            foreach ($hojas as $hoja) {
                // Solo procesar hojas completamente terminadas
                if (
                    $hoja->datos === 'completado' &&
                    $hoja->intralaboral === 'completado' &&
                    $hoja->extralaboral === 'completado' &&
                    $hoja->estres === 'completado'
                ) {

                    // Obtener puntajes de las evaluaciones de forma segura
                    $puntajeIntralaboral = 0;
                    $puntajeExtralaboral = 0;
                    $puntajeEstres = 0;

                    try {
                        // Manejo seguro de puntaje_intralaboral
                        if (isset($hoja->puntaje_intralaboral)) {
                            $puntaje = $hoja->puntaje_intralaboral;
                            if (is_string($puntaje) || $puntaje instanceof \Traversable) {
                                $decoded = safe_json_decode($puntaje, true);
                                if (!empty($decoded)) {
                                    $puntaje = $decoded;
                                }
                            }
                            if (is_array($puntaje) && isset($puntaje['total'])) {
                                $puntajeIntralaboral = (float) $puntaje['total'];
                            }
                        }

                        // Manejo seguro de puntaje_extralaboral
                        if (isset($hoja->puntaje_extralaboral)) {
                            $puntaje = $hoja->puntaje_extralaboral;
                            if (is_string($puntaje) || $puntaje instanceof \Traversable) {
                                $decoded = safe_json_decode($puntaje, true);
                                if (!empty($decoded)) {
                                    $puntaje = $decoded;
                                }
                            }
                            if (is_array($puntaje) && isset($puntaje['total'])) {
                                $puntajeExtralaboral = (float) $puntaje['total'];
                            }
                        }

                        // Manejo seguro de puntaje_estres
                        if (isset($hoja->puntaje_estres)) {
                            $puntaje = $hoja->puntaje_estres;
                            if (is_string($puntaje) || $puntaje instanceof \Traversable) {
                                $decoded = safe_json_decode($puntaje, true);
                                if (!empty($decoded)) {
                                    $puntaje = $decoded;
                                }
                            }
                            if (is_array($puntaje) && isset($puntaje['total'])) {
                                $puntajeEstres = (float) $puntaje['total'];
                            }
                        }
                    } catch (\Exception $e) {
                        // En caso de error, usar valores por defecto
                        Log::warning("Error procesando puntajes para hoja {$hoja->id}: " . $e->getMessage());
                        $puntajeIntralaboral = 0;
                        $puntajeExtralaboral = 0;
                        $puntajeEstres = 0;
                    }

                    // Calcular puntaje ponderado total
                    $puntajePonderadoIntralaboral = $puntajeIntralaboral * 0.5;
                    $puntajePonderadoExtralaboral = $puntajeExtralaboral * 0.3;
                    $puntajePonderadoEstres = $puntajeEstres * 0.2;

                    $puntajeTotal = $puntajePonderadoIntralaboral + $puntajePonderadoExtralaboral + $puntajePonderadoEstres;

                    // Determinar nivel de riesgo según el tipo de forma utilizada
                    $forma = strtolower($hoja->intralaboral_tipo ?? 'a');
                    $nivelRiesgo = $this->bateriaPsicosocialService->interpretarNivelRiesgo($puntajeTotal, $forma);

                    // Mapear el nivel de riesgo al formato correcto
                    $nivelRiesgoMapeado = '';
                    switch ($nivelRiesgo) {
                        case 'sin_riesgo':
                            $nivelRiesgoMapeado = 'Sin Riesgo';
                            break;
                        case 'bajo':
                            $nivelRiesgoMapeado = 'Riesgo Bajo';
                            break;
                        case 'medio':
                            $nivelRiesgoMapeado = 'Riesgo Medio';
                            break;
                        case 'alto':
                            $nivelRiesgoMapeado = 'Riesgo Alto';
                            break;
                        case 'muy_alto':
                            $nivelRiesgoMapeado = 'Riesgo Muy Alto';
                            break;
                        default:
                            $nivelRiesgoMapeado = 'Sin Riesgo';
                    }

                    if (isset($nivelesRiesgo[$nivelRiesgoMapeado])) {
                        $nivelesRiesgo[$nivelRiesgoMapeado]++;
                    }
                }
            }

            // Calcular porcentaje de avance global
            $totalHojas = $hojas->count();
            $porcentajeCompletado = $totalHojas > 0 ? round(($totalCompletadas / $totalHojas) * 100, 1) : 0;

            // Preparar estadísticas base (se ajustarán tras formatear tarjetas)
            $estadisticas = [
                'total_diagnosticos' => $totalDiagnosticos,
                'diagnosticos' => $totalDiagnosticos,
                'evaluaciones_completadas' => $totalCompletadas,
                'evaluaciones_en_proceso' => $totalEnProceso,
                'evaluaciones_pendientes' => $totalPendientes,
                'empleados_evaluados' => $empleadosEvaluados,
                'total_evaluaciones' => $totalHojasAsignadas,
                'porcentaje_completado' => $porcentajeCompletado,
                'niveles_riesgo' => $nivelesRiesgo
            ];

            // Preparar diagnósticos con estadísticas para las tarjetas
            $diagnosticosFormateados = collect();

            foreach ($diagnosticos as $diag) {
                $hojasDiag = $hojas->where('diagnostico_id', $diag->id);

                // Nueva lógica: clasificar cada hoja según su estado real
                $completadasDiag = 0;
                $enProcesoDiag = 0;
                $pendientesDiag = 0;

                foreach ($hojasDiag as $hoja) {
                    // Si todas las secciones están en 'completado'
                    if (
                        $getEstado($hoja, 'datos') === 'completado' &&
                        $getEstado($hoja, 'intralaboral') === 'completado' &&
                        $getEstado($hoja, 'extralaboral') === 'completado' &&
                        $getEstado($hoja, 'estres') === 'completado'
                    ) {
                        $completadasDiag++;
                    }
                    // Si todas las secciones están en 'pendiente'
                    elseif (
                        $getEstado($hoja, 'datos') === 'pendiente' &&
                        $getEstado($hoja, 'intralaboral') === 'pendiente' &&
                        $getEstado($hoja, 'extralaboral') === 'pendiente' &&
                        $getEstado($hoja, 'estres') === 'pendiente'
                    ) {
                        $pendientesDiag++;
                    }
                    // Si alguna sección está en 'en_progreso' (y no todas en pendiente ni todas en completado)
                    elseif (
                        $getEstado($hoja, 'datos') === 'en_progreso' ||
                        $getEstado($hoja, 'intralaboral') === 'en_progreso' ||
                        $getEstado($hoja, 'extralaboral') === 'en_progreso' ||
                        $getEstado($hoja, 'estres') === 'en_progreso'
                    ) {
                        $enProcesoDiag++;
                    }
                }

                // Crear objeto con propiedades accesibles
                $porcentajeCompletadoDiag = $hojasDiag->count() > 0 ? round(($completadasDiag / $hojasDiag->count()) * 100, 1) : 0;
                
                $diagFormateado = (object) [
                    'id' => $diag->id,
                    'descripcion' => $diag->descripcion ?? $diag->clave ?? ('Diagnóstico #' . substr($diag->id, -6)),
                    'empresa_id' => $diag->empresa_id,
                    'profesional_id' => $diag->profesional_id,
                    'created_at' => $diag->created_at,
                    'filtro' => $diag->filtro ?? false,
                    'estado' => $diag->estado ?? 'pendiente',
                    'empleados_asignados' => $hojasDiag->count(),
                    'evaluaciones_completadas' => $completadasDiag,
                    'evaluaciones_pendientes' => $pendientesDiag,
                    'evaluaciones_en_proceso' => $enProcesoDiag,
                    'progreso' => $porcentajeCompletadoDiag,
                    'porcentaje_completado' => $porcentajeCompletadoDiag, // Propiedad que espera la vista
                    'total_empleados' => $hojasDiag->count(), // Propiedad que espera la vista 
                    'completados' => $completadasDiag, // Propiedad que espera la vista
                    'profesional_info' => $diag->profesional_info ?? null,
                    'distribucion_riesgo' => $diag->distribucion_riesgo ?? [],
                    'total' => $hojasDiag->count(),
                    'en_proceso' => $enProcesoDiag,
                    'pendientes' => $pendientesDiag
                ];

                $diagnosticosFormateados->push($diagFormateado);
            }

            // Preparar estadísticas de tarjetas
            $estadisticasTarjetas = [
                'total' => $diagnosticosFormateados->sum('total_empleados'),
                'completados' => $diagnosticosFormateados->sum('evaluaciones_completadas'),
                'en_proceso' => $diagnosticosFormateados->sum('en_proceso'),
                'pendientes' => $diagnosticosFormateados->sum('pendientes')
            ];

            // Recalcular valores del header basados en las tarjetas mostradas para máxima coherencia
            $estadisticas['total_diagnosticos'] = $diagnosticosFormateados->count();
            $estadisticas['diagnosticos'] = $estadisticas['total_diagnosticos'];
            $estadisticas['total_evaluaciones'] = $estadisticasTarjetas['total'];
            $estadisticas['evaluaciones_completadas'] = $estadisticasTarjetas['completados'];
            $estadisticas['evaluaciones_en_proceso'] = $estadisticasTarjetas['en_proceso'];
            $estadisticas['evaluaciones_pendientes'] = $estadisticasTarjetas['pendientes'];
            $estadisticas['porcentaje_completado'] = $estadisticas['total_evaluaciones'] > 0
                ? round(($estadisticas['evaluaciones_completadas'] / $estadisticas['total_evaluaciones']) * 100, 1)
                : 0;

            // Log de verificación (sin datos sensibles)
            Log::info('PsicosocialController@index header stats', [
                'total_diagnosticos' => $estadisticas['total_diagnosticos'],
                'evaluaciones_en_proceso' => $estadisticas['evaluaciones_en_proceso'],
                'evaluaciones_completadas' => $estadisticas['evaluaciones_completadas'],
                'evaluaciones_pendientes' => $estadisticas['evaluaciones_pendientes'],
                'empleados_evaluados' => $estadisticas['empleados_evaluados'],
                'porcentaje' => $estadisticas['porcentaje_completado']
            ]);

            // Preparar distribución de riesgo formateada para la vista
            $distribucionRiesgo = [];
            $totalEvaluacionesConRiesgo = array_sum($nivelesRiesgo);
            
            if ($totalEvaluacionesConRiesgo > 0) {
                $riskColors = [
                    'Sin Riesgo' => '#28a745',
                    'Riesgo Bajo' => '#17a2b8',
                    'Riesgo Medio' => '#ffc107',
                    'Riesgo Alto' => '#fd7e14',
                    'Riesgo Muy Alto' => '#dc3545'
                ];

                $riskCssClasses = [
                    'Sin Riesgo' => 'risk-sin-riesgo',
                    'Riesgo Bajo' => 'risk-bajo',
                    'Riesgo Medio' => 'risk-medio',
                    'Riesgo Alto' => 'risk-alto',
                    'Riesgo Muy Alto' => 'risk-muy-alto'
                ];

                foreach ($nivelesRiesgo as $nivel => $cantidad) {
                    $porcentaje = round(($cantidad / $totalEvaluacionesConRiesgo) * 100, 1);
                    $distribucionRiesgo[] = [
                        'label' => $nivel,
                        'value' => $cantidad,
                        'percentage' => $porcentaje,
                        'color' => $riskColors[$nivel] ?? '#6c757d',
                        'css_class' => $riskCssClasses[$nivel] ?? 'risk-default'
                    ];
                }
            }
            
            return view('modules.psicosocial.index', [
                'estadisticas' => $estadisticas,
                'estadisticasTarjetas' => $estadisticasTarjetas,
                'distribucionRiesgo' => $distribucionRiesgo,
                'diagnosticos' => $diagnosticosFormateados,
                'empresaData' => $empresaData,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al cargar dashboard psicosocial: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return view('modules.psicosocial.index', [
                'estadisticas' => [
                    'total_diagnosticos' => 0,
                    'diagnosticos' => 0,
                    'evaluaciones_completadas' => 0,
                    'evaluaciones_en_proceso' => 0,
                    'evaluaciones_pendientes' => 0,
                    'empleados_evaluados' => 0,
                    'total_evaluaciones' => 0,
                    'porcentaje_completado' => 0,
                    'niveles_riesgo' => ['Sin Riesgo' => 0, 'Riesgo Bajo' => 0, 'Riesgo Medio' => 0, 'Riesgo Alto' => 0, 'Riesgo Muy Alto' => 0]
                ],
                'estadisticasTarjetas' => [
                    'total' => 0,
                    'completados' => 0,
                    'en_proceso' => 0,
                    'pendientes' => 0
                ],
                'distribucionRiesgo' => [],
                'diagnosticos' => collect(),
                'empresaData' => null,
                'error' => $e->getMessage()
            ])->with('error', 'Error al cargar el dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar resumen básico de un diagnóstico - VERSIÓN LIMPIA
     */
    public function resumen($id)
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            $isSuperAdmin = $this->isSuperAdmin();

            // Obtener el diagnóstico
            $diagnostico = \App\Models\Diagnostico::find($id);

            if (!$diagnostico) {
                return redirect()->route('psicosocial.index')
                    ->with('error', 'Diagnóstico no encontrado');
            }

            // SEGURIDAD: SuperAdmin puede ver todos los diagnósticos, otros usuarios solo de su empresa
            if (!$isSuperAdmin && $diagnostico->empresa_id !== $empresaData['id']) {
                Log::warning('Acceso bloqueado - Diagnóstico de otra empresa', [
                    'user_id' => $userData['id'] ?? null,
                    'user_empresa' => $empresaData['id'],
                    'diagnostico_empresa' => $diagnostico->empresa_id,
                    'diagnostico_id' => $id
                ]);
                return redirect()->route('psicosocial.index')
                    ->with('error', 'Sin permisos para acceder a este diagnóstico');
            }

            // Obtener TODAS las hojas del diagnóstico (no solo completadas)
            $todasHojas = \App\Models\Hoja::where('diagnostico_id', $id)->get();

            // Filtrar hojas con al menos un cuestionario completado para estadísticas
            $hojasCompletadas = $todasHojas->filter(function($hoja) {
                return $hoja->datos === 'completado' &&
                       $hoja->intralaboral === 'completado' &&
                       $hoja->extralaboral === 'completado' &&
                       $hoja->estres === 'completado';
            });

            // Calcular estadísticas reales
            $total = $todasHojas->count();
            $completadas = $hojasCompletadas->count();
            $pendientes = $total - $completadas;
            $progreso = $total > 0 ? round(($completadas / $total) * 100, 2) : 0;

            $estadisticas = [
                'total' => $total,
                'completadas' => $completadas,
                'pendientes' => $pendientes,
                'progreso' => $progreso
            ];

            // Si no hay ninguna hoja asignada, mostrar mensaje
            if ($todasHojas->isEmpty()) {
                return view('modules.psicosocial.resumen_general', [
                    'estadisticas' => ['total' => 0, 'completadas' => 0, 'pendientes' => 0, 'progreso' => 0],
                    'resumen' => [],
                    'opciones' => [],
                    'mensaje' => 'No hay evaluaciones asignadas para mostrar en el resumen.',
                    'diagnostico' => $diagnostico,
                    'empresaData' => $empresaData
                ]);
            }

            // Obtener estadísticas optimizadas (usa el método ya optimizado)
            $resumen = $this->obtenerResumenCompleto($id);

            // Obtener opciones de filtros desde las hojas
            $opciones = $this->obtenerOpcionesFiltros($todasHojas);

            // Log temporal para debug
            \Log::info('Resumen datos:', [
                'total_hojas' => $todasHojas->count(),
                'completadas' => $hojasCompletadas->count(),
                'resumen_keys' => array_keys($resumen),
                'resumen_completo_keys' => isset($resumen['completo']) ? array_keys($resumen['completo']) : [],
                'distribucion_riesgo' => $resumen['completo']['distribucion_riesgo'] ?? 'no existe'
            ]);

            return view('modules.psicosocial.resumen_general', compact(
                'estadisticas',
                'resumen',
                'opciones',
                'diagnostico'
            ) + ['empresaData' => $empresaData]);

        } catch (\Exception $e) {
            Log::error('Error en resumen psicosocial: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')
                ->with('error', 'Error al cargar el resumen: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new diagnostico psicosocial.
     */
    public function create()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            $isSuperAdmin = $this->isSuperAdmin();

            // Obtener profesionales de la empresa
            $profesionales = collect();
            try {
                if ($empresaData && isset($empresaData['id'])) {
                    // Obtener profesionales usando MongoDB
                    $profesionales = \App\Models\Usuario::where('empresa_id', $empresaData['id'])
                        ->where('tipo_cuenta', 'profesional')
                        ->where('estado', 'activo')
                        ->get();
                }
            } catch (\Exception $e) {
                Log::warning('Error al cargar profesionales: ' . $e->getMessage());
                $profesionales = collect();
            }

            // Obtener filtros de empleados (por tipo de contrato)
            $filtrosEmpleados = [];
            try {
                if ($empresaData && isset($empresaData['id'])) {
                    // Obtener tipos de contrato únicos de los empleados
                    $empleados = \App\Models\Empresas\Empleado::where('empresa_id', $empresaData['id'])
                        ->whereNotNull('contrato_key')
                        ->get();
                    
                    $contratosUnicos = $empleados->pluck('contrato_key')->unique()->values();
                    
                    foreach ($contratosUnicos as $contratoKey) {
                        if (!empty($contratoKey)) {
                            // Obtener el primer empleado con este contrato para obtener el label
                            $empleadoEjemplo = $empleados->where('contrato_key', $contratoKey)->first();
                            $contratoLabel = $empleadoEjemplo->contrato_label ?? $contratoKey;
                            
                            $filtrosEmpleados[] = [
                                'tipo' => 'contrato',
                                'valor' => $contratoKey,
                                'descripcion' => $contratoLabel . ' (' . $empleados->where('contrato_key', $contratoKey)->count() . ' empleados)'
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error al cargar filtros de empleados: ' . $e->getMessage());
                $filtrosEmpleados = [];
            }

            return view('modules.psicosocial.create', compact(
                'userData',
                'empresaData',
                'isSuperAdmin',
                'profesionales',
                'filtrosEmpleados'
            ));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de diagnóstico psicosocial: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario. Por favor, intente nuevamente.');
        }
    }

    /**
     * Store a newly created diagnostico psicosocial in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit');
            }

            return redirect()->route('psicosocial.index')
                ->with('success', 'Diagnóstico creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear diagnóstico psicosocial: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el diagnóstico. Por favor, intente nuevamente.');
        }
    }

    /**
     * Display the specified diagnostico psicosocial.
     */
    public function show($id)
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            $isSuperAdmin = $this->isSuperAdmin();

            $diagnostico = \App\Models\Diagnostico::find($id);

            if (!$diagnostico) {
                return redirect()->route('psicosocial.index')
                    ->with('error', 'Diagnóstico no encontrado.');
            }

            // SEGURIDAD: SuperAdmin puede ver todos los diagnósticos, otros usuarios solo de su empresa
            if (!$isSuperAdmin && $diagnostico->empresa_id !== $empresaData['id']) {
                Log::warning('Acceso bloqueado - Diagnóstico de otra empresa', [
                    'user_id' => $userData['id'] ?? null,
                    'user_empresa' => $empresaData['id'],
                    'diagnostico_empresa' => $diagnostico->empresa_id,
                    'diagnostico_id' => $id
                ]);
                return redirect()->route('psicosocial.index')
                    ->with('error', 'Sin permisos para acceder a este diagnóstico');
            }

            // Obtener estadísticas optimizadas para 1M+ hojas usando MongoDB agregaciones
            $estadisticasHojas = $this->obtenerEstadisticasHojasOptimizado($diagnostico->id);
            
            // Variables básicas para la vista
            $totalHojas = $estadisticasHojas['total'] ?? 0;
            $totalCompletadas = $estadisticasHojas['completadas'] ?? 0;
            $totalPendientes = $estadisticasHojas['pendientes'] ?? 0;
            $porcentajeCompletado = $totalHojas > 0 ? round(($totalCompletadas / $totalHojas) * 100, 1) : 0;

            // Estados por cuestionario
            $estadosIntralaboral = $estadisticasHojas['estados_intralaboral'] ?? [
                'completado' => 0,
                'en_progreso' => 0,
                'pendiente' => 0
            ];

            $estadosExtralaboral = $estadisticasHojas['estados_extralaboral'] ?? [
                'completado' => 0,
                'en_progreso' => 0,
                'pendiente' => 0
            ];

            $estadosEstres = $estadisticasHojas['estados_estres'] ?? [
                'completado' => 0,
                'en_progreso' => 0,
                'pendiente' => 0
            ];

            $estadosDatos = $estadisticasHojas['estados_datos'] ?? [
                'completado' => 0,
                'en_progreso' => 0,
                'pendiente' => 0
            ];

            // Distribución por forma (A/B)
            $distribucionForma = $estadisticasHojas['distribucion_forma'] ?? [
                'forma_a' => 0,
                'forma_b' => 0
            ];

            // Estadísticas para las tarjetas superiores
            $estadisticas = [
                'total' => $totalHojas,
                'completados' => $totalCompletadas,
                'pendientes' => $totalPendientes,
                'porcentaje_general' => $porcentajeCompletado,
                'porcentaje_completado' => $porcentajeCompletado
            ];

            // Obtener hojas paginadas optimizadamente para UI (máximo 100 por página para rendimiento)
            $hojasPaginadas = $this->obtenerHojasPaginadas($diagnostico->id, request('page', 1));
            
            // Para compatibilidad con vistas que usan $hojas
            $hojas = $hojasPaginadas;

            // Obtener información adicional requerida por la vista
            $empleados = $this->obtenerEmpleadosOptimizado($diagnostico->id);
            
            // Información del profesional y empresa
            $profesionalNombre = 'No especificado';
            $empresaNombre = 'No especificado';
            
            // Calcular niveles de riesgo desde TODAS las hojas usando agregación MongoDB
            $nivelesRiesgo = [
                'sin_riesgo' => 0,
                'bajo' => 0,
                'medio' => 0,
                'alto' => 0,
                'muy_alto' => 0
            ];

            // Contar evaluaciones por nivel de riesgo usando agregación para 30K+ registros
            try {
                $pipelineRiesgo = [
                    [
                        '$match' => [
                            'diagnostico_id' => $diagnostico->id,
                            'nivel_riesgo' => ['$exists' => true, '$ne' => null]
                        ]
                    ],
                    [
                        '$group' => [
                            '_id' => '$nivel_riesgo',
                            'count' => ['$sum' => 1]
                        ]
                    ]
                ];

                $resultadosRiesgo = \App\Models\Hoja::raw(function($collection) use ($pipelineRiesgo) {
                    return $collection->aggregate($pipelineRiesgo)->toArray();
                });

                foreach ($resultadosRiesgo as $resultado) {
                    $nivel = $resultado['_id'] ?? null;
                    $cantidad = $resultado['count'] ?? 0;
                    
                    if ($nivel && isset($nivelesRiesgo[$nivel])) {
                        $nivelesRiesgo[$nivel] = $cantidad;
                    }
                }

                Log::info('Niveles de riesgo calculados desde agregación MongoDB', [
                    'diagnostico_id' => $diagnostico->id,
                    'niveles' => $nivelesRiesgo,
                    'total_con_nivel' => array_sum($nivelesRiesgo)
                ]);
            } catch (\Exception $e) {
                Log::warning('Error al calcular niveles de riesgo: ' . $e->getMessage());
            }
            
            Log::info('Diagnóstico IDs para búsqueda', [
                'empresa_id_raw' => $diagnostico->empresa_id,
                'empresa_id_type' => gettype($diagnostico->empresa_id),
                'profesional_id_raw' => $diagnostico->profesional_id,
                'profesional_id_type' => gettype($diagnostico->profesional_id)
            ]);
            
            // Obtener nombre de la empresa desde la base de datos 'empresas'
            if ($diagnostico->empresa_id) {
                try {
                    $empresaIdBusqueda = $diagnostico->empresa_id;
                    
                    Log::info('Buscando empresa con ID', ['id' => $empresaIdBusqueda]);
                    
                    // Buscar por el campo 'id' (no por _id)
                    $empresaDoc = DB::connection('mongodb_empresas')
                        ->collection('empresas')
                        ->where('id', $empresaIdBusqueda)
                        ->first();
                    
                    Log::info('Resultado búsqueda empresa', [
                        'encontrado' => $empresaDoc ? 'SI' : 'NO',
                        'nombre' => $empresaDoc['nombre'] ?? 'N/A',
                        'campos_disponibles' => $empresaDoc ? array_keys((array)$empresaDoc) : []
                    ]);
                    
                    if ($empresaDoc && isset($empresaDoc['nombre'])) {
                        $empresaNombre = $empresaDoc['nombre'];
                    } else {
                        $empresaNombre = 'Empresa no encontrada';
                    }
                } catch (\Exception $e) {
                    Log::warning('Error al obtener empresa para diagnóstico', [
                        'empresa_id' => $diagnostico->empresa_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $empresaNombre = 'Error al cargar empresa';
                }
            }
            
            // Obtener nombre y apellido del profesional desde la base de datos 'cmym' -> colección 'perfiles'
            if ($diagnostico->profesional_id) {
                try {
                    $profesionalIdBusqueda = $diagnostico->profesional_id;
                    
                    Log::info('Buscando profesional con ID', ['id' => $profesionalIdBusqueda]);
                    
                    // Buscar por el campo 'id' (no por _id)
                    $perfilDoc = DB::connection('mongodb')
                        ->collection('perfiles')
                        ->where('id', $profesionalIdBusqueda)
                        ->first();
                    
                    Log::info('Resultado búsqueda profesional', [
                        'encontrado' => $perfilDoc ? 'SI' : 'NO',
                        'nombre' => isset($perfilDoc['nombre']) ? $perfilDoc['nombre'] : 'N/A',
                        'apellido' => isset($perfilDoc['apellido']) ? $perfilDoc['apellido'] : 'N/A',
                        'campos_disponibles' => $perfilDoc ? array_keys((array)$perfilDoc) : []
                    ]);
                    
                    if ($perfilDoc) {
                        // Validar que el perfil esté asociado a la misma empresa
                        $perfilEmpresaId = $perfilDoc['empresa_id'] ?? null;
                        
                        // Convertir ambos IDs a string para comparación
                        $diagnosticoEmpresaId = (string) $diagnostico->empresa_id;
                        $perfilEmpresaIdStr = is_object($perfilEmpresaId) ? (string) $perfilEmpresaId : (string) $perfilEmpresaId;
                        
                        Log::info('Validación empresa del profesional', [
                            'diagnostico_empresa_id' => $diagnosticoEmpresaId,
                            'perfil_empresa_id' => $perfilEmpresaIdStr,
                            'coinciden' => $perfilEmpresaIdStr === $diagnosticoEmpresaId ? 'SI' : 'NO'
                        ]);
                        
                        // Permitir si coincide o si no tiene empresa_id en el perfil
                        if (!$perfilEmpresaId || $perfilEmpresaIdStr === $diagnosticoEmpresaId) {
                            $nombre = $perfilDoc['nombre'] ?? '';
                            $apellido = $perfilDoc['apellido'] ?? '';
                            
                            if (!empty($nombre) || !empty($apellido)) {
                                $profesionalNombre = trim($nombre . ' ' . $apellido);
                            } else {
                                $profesionalNombre = 'Profesional sin nombre';
                            }
                        } else {
                            Log::warning('Perfil no asociado a la empresa del diagnóstico', [
                                'perfil_id' => $diagnostico->profesional_id,
                                'perfil_empresa' => $perfilEmpresaIdStr,
                                'diagnostico_empresa' => $diagnosticoEmpresaId
                            ]);
                            $profesionalNombre = 'Profesional no autorizado';
                        }
                    } else {
                        $profesionalNombre = 'Profesional no encontrado';
                    }
                } catch (\Exception $e) {
                    Log::warning('Error al obtener perfil profesional para diagnóstico', [
                        'profesional_id' => $diagnostico->profesional_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $profesionalNombre = 'Error al cargar profesional';
                }
            }

            return view('modules.psicosocial.diagnostico.show', compact(
                'diagnostico',
                'userData',
                'empresaData',
                'isSuperAdmin',
                'totalHojas',
                'totalCompletadas',
                'totalPendientes',
                'porcentajeCompletado',
                'estadosIntralaboral',
                'estadosExtralaboral',
                'estadosEstres',
                'estadosDatos',
                'distribucionForma',
                'estadisticas',
                'hojasPaginadas',
                'hojas',
                'empleados',
                'profesionalNombre',
                'empresaNombre',
                'nivelesRiesgo'
            ));
        } catch (\Exception $e) {
            Log::error('Error al mostrar diagnóstico psicosocial: ' . $e->getMessage());
            return redirect()->route('psicosocial.index')
                ->with('error', 'Error al cargar el diagnóstico.');
        }
    }

    /**
     * Obtener hojas paginadas optimizadamente para UI
     * Limitado a 100 por página para mantener rendimiento en frontend
     */
    private function obtenerHojasPaginadas($diagnosticoId, $page = 1, $perPage = 100)
    {
        try {
            $skip = ($page - 1) * $perPage;
            
            // Usar agregación MongoDB para obtener TODAS las hojas del diagnóstico
            $pipeline = [
                [
                    '$match' => [
                        'diagnostico_id' => $diagnosticoId
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 1,
                        'id' => 1,
                        'empleado_id' => 1,
                        'nombre' => 1,
                        'numero_documento' => 1,
                        'email' => 1,
                        'cargo' => 1,
                        'contrato_key' => 1,
                        'estado' => 1,
                        'estado_datos_generales' => 1,
                        'estado_intralaboral' => 1,
                        'estado_extralaboral' => 1,
                        'estado_estres' => 1,
                        'datos' => 1,
                        'intralaboral' => 1,
                        'extralaboral' => 1,
                        'estres' => 1,
                        'completado' => 1,
                        'forma_intralaboral' => 1,
                        'nivel_riesgo' => 1,
                        'puntaje_total' => 1,
                        'created_at' => 1,
                        'updated_at' => 1
                    ]
                ],
                [
                    '$sort' => [
                        'nombre' => 1
                    ]
                ],
                [
                    '$skip' => $skip
                ],
                [
                    '$limit' => $perPage
                ]
            ];

            $hojas = \App\Models\Hoja::raw(function($collection) use ($pipeline) {
                return $collection->aggregate($pipeline)->toArray();
            });

            // Convertir a Collection para compatibilidad con Laravel
            $hojasCollection = collect($hojas)->map(function($hoja) {
                return (object) $hoja;
            });

            // Crear un objeto paginated manually para simplificar
            $paginatedHojas = new \Illuminate\Pagination\LengthAwarePaginator(
                $hojasCollection,
                $this->contarTotalHojas($diagnosticoId),
                $perPage,
                $page,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );

            return $paginatedHojas;

        } catch (\Exception $e) {
            Log::error('Error al obtener hojas paginadas: ' . $e->getMessage());
            
            // Retornar colección vacía en caso de error
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                $perPage,
                $page,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
        }
    }

    /**
     * Contar total de hojas optimizadamente
     */
    private function contarTotalHojas($diagnosticoId)
    {
        try {
            // Contar TODAS las hojas del diagnóstico (completadas, en progreso y pendientes)
            $count = \App\Models\Hoja::where('diagnostico_id', $diagnosticoId)->count();
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Error al contar hojas: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener empleados optimizadamente para la tabla de empleados
     */
    private function obtenerEmpleadosOptimizado($diagnosticoId, $limit = 1000)
    {
        try {
            // Usar agregación MongoDB para obtener TODOS los empleados (no solo completados)
            $pipeline = [
                [
                    '$match' => [
                        'diagnostico_id' => $diagnosticoId
                    ]
                ],
                [
                    '$project' => [
                        'id' => 1,
                        'empleado_id' => 1,
                        'nombre' => 1,
                        'numero_documento' => '$numero_documento',
                        'dni' => '$numero_documento',
                        'email' => 1,
                        'cargo' => 1,
                        'nivel_riesgo' => 1,
                        'puntaje_total' => [
                            '$ifNull' => ['$puntaje_total', 0]
                        ],
                        'puntaje' => [
                            '$ifNull' => ['$puntaje_total', 0]
                        ],
                        'completado' => 1,
                        'estado_datos_generales' => 1,
                        'estado_intralaboral' => 1,
                        'estado_extralaboral' => 1,
                        'estado_estres' => 1,
                        'datos' => '$estado_datos_generales',
                        'intralaboral' => '$estado_intralaboral',
                        'extralaboral' => '$estado_extralaboral',
                        'estres' => '$estado_estres'
                    ]
                ],
                [
                    '$addFields' => [
                        'porcentaje' => [
                            '$multiply' => [
                                [
                                    '$divide' => [
                                        [
                                            '$add' => [
                                                ['$cond' => [['$eq' => ['$datos', 'completado']], 1, 0]],
                                                ['$cond' => [['$eq' => ['$intralaboral', 'completado']], 1, 0]],
                                                ['$cond' => [['$eq' => ['$extralaboral', 'completado']], 1, 0]],
                                                ['$cond' => [['$eq' => ['$estres', 'completado']], 1, 0]]
                                            ]
                                        ],
                                        4
                                    ]
                                ],
                                100
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => [
                        'nombre' => 1
                    ]
                ],
                [
                    '$limit' => $limit
                ]
            ];

            $empleados = \App\Models\Hoja::raw(function($collection) use ($pipeline) {
                return $collection->aggregate($pipeline)->toArray();
            });

            // Convertir a Collection para facilitar el manejo
            return collect($empleados)->map(function($empleado) {
                return (object) $empleado;
            });

        } catch (\Exception $e) {
            Log::error('Error al obtener empleados: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtener estadísticas optimizadas de hojas usando MongoDB agregaciones
     * Diseñado para manejar hasta 1M+ hojas eficientemente
     */
    private function obtenerEstadisticasHojasOptimizado($diagnosticoId)
    {
        try {
            // Usar agregación MongoDB para procesar 1M+ registros eficientemente (TODAS las hojas)
            $pipeline = [
                [
                    '$match' => [
                        'diagnostico_id' => $diagnosticoId
                    ]
                ],
                [
                    '$group' => [
                        '_id' => null,
                        'total' => ['$sum' => 1],
                        'completadas' => [
                            '$sum' => [
                                '$cond' => [
                                    [
                                        '$and' => [
                                            ['$eq' => ['$datos', 'completado']],
                                            ['$eq' => ['$intralaboral', 'completado']],
                                            ['$eq' => ['$extralaboral', 'completado']],
                                            ['$eq' => ['$estres', 'completado']]
                                        ]
                                    ],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'pendientes' => [
                            '$sum' => [
                                '$cond' => [
                                    [
                                        '$and' => [
                                            [
                                                '$or' => [
                                                    ['$eq' => ['$datos', 'pendiente']],
                                                    ['$eq' => ['$datos', null]]
                                                ]
                                            ],
                                            [
                                                '$or' => [
                                                    ['$eq' => ['$intralaboral', 'pendiente']],
                                                    ['$eq' => ['$intralaboral', null]]
                                                ]
                                            ],
                                            [
                                                '$or' => [
                                                    ['$eq' => ['$extralaboral', 'pendiente']],
                                                    ['$eq' => ['$extralaboral', null]]
                                                ]
                                            ],
                                            [
                                                '$or' => [
                                                    ['$eq' => ['$estres', 'pendiente']],
                                                    ['$eq' => ['$estres', null]]
                                                ]
                                            ]
                                        ]
                                    ],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        // Conteos por cuestionario y estado
                        'intralaboral_completado' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$intralaboral', 'completado']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'intralaboral_progreso' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$intralaboral', 'en_progreso']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'intralaboral_pendiente' => [
                            '$sum' => [
                                '$cond' => [
                                    [
                                        '$or' => [
                                            ['$eq' => ['$intralaboral', 'pendiente']],
                                            ['$eq' => ['$intralaboral', null]]
                                        ]
                                    ],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'extralaboral_completado' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$extralaboral', 'completado']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'extralaboral_progreso' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$extralaboral', 'en_progreso']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'extralaboral_pendiente' => [
                            '$sum' => [
                                '$cond' => [
                                    [
                                        '$or' => [
                                            ['$eq' => ['$extralaboral', 'pendiente']],
                                            ['$eq' => ['$extralaboral', null]]
                                        ]
                                    ],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'estres_completado' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$estres', 'completado']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'estres_progreso' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$estres', 'en_progreso']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'estres_pendiente' => [
                            '$sum' => [
                                '$cond' => [
                                    [
                                        '$or' => [
                                            ['$eq' => ['$estres', 'pendiente']],
                                            ['$eq' => ['$estres', null]]
                                        ]
                                    ],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'datos_completado' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$datos', 'completado']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'datos_progreso' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$datos', 'en_progreso']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'datos_pendiente' => [
                            '$sum' => [
                                '$cond' => [
                                    [
                                        '$or' => [
                                            ['$eq' => ['$datos', 'pendiente']],
                                            ['$eq' => ['$datos', null]]
                                        ]
                                    ],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'forma_a' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$forma_intralaboral', 'A']],
                                    1,
                                    0
                                ]
                            ]
                        ],
                        'forma_b' => [
                            '$sum' => [
                                '$cond' => [
                                    ['$eq' => ['$forma_intralaboral', 'B']],
                                    1,
                                    0
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $resultado = \App\Models\Hoja::raw(function($collection) use ($pipeline) {
                return $collection->aggregate($pipeline)->toArray();
            });

            if (empty($resultado)) {
                return [
                    'total' => 0,
                    'completadas' => 0,
                    'pendientes' => 0,
                    'en_progreso' => 0,
                    'estados_intralaboral' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                    'estados_extralaboral' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                    'estados_estres' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                    'estados_datos' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                    'distribucion_forma' => ['forma_a' => 0, 'forma_b' => 0]
                ];
            }

            $datos = $resultado[0];

            return [
                'total' => $datos['total'] ?? 0,
                'completadas' => $datos['completadas'] ?? 0,
                'pendientes' => $datos['pendientes'] ?? 0,
                'en_progreso' => $datos['en_progreso'] ?? 0,
                'estados_intralaboral' => [
                    'completado' => $datos['intralaboral_completado'] ?? 0,
                    'en_progreso' => $datos['intralaboral_progreso'] ?? 0,
                    'pendiente' => $datos['intralaboral_pendiente'] ?? 0
                ],
                'estados_extralaboral' => [
                    'completado' => $datos['extralaboral_completado'] ?? 0,
                    'en_progreso' => $datos['extralaboral_progreso'] ?? 0,
                    'pendiente' => $datos['extralaboral_pendiente'] ?? 0
                ],
                'estados_estres' => [
                    'completado' => $datos['estres_completado'] ?? 0,
                    'en_progreso' => $datos['estres_progreso'] ?? 0,
                    'pendiente' => $datos['estres_pendiente'] ?? 0
                ],
                'estados_datos' => [
                    'completado' => $datos['datos_completado'] ?? 0,
                    'en_progreso' => $datos['datos_progreso'] ?? 0,
                    'pendiente' => $datos['datos_pendiente'] ?? 0
                ],
                'distribucion_forma' => [
                    'forma_a' => $datos['forma_a'] ?? 0,
                    'forma_b' => $datos['forma_b'] ?? 0
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de hojas: ' . $e->getMessage());
            
            // Retornar valores por defecto en caso de error
            return [
                'total' => 0,
                'completadas' => 0,
                'pendientes' => 0,
                'en_progreso' => 0,
                'estados_intralaboral' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                'estados_extralaboral' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                'estados_estres' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                'estados_datos' => ['completado' => 0, 'en_progreso' => 0, 'pendiente' => 0],
                'distribucion_forma' => ['forma_a' => 0, 'forma_b' => 0]
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('psicosocial.index')
            ->with('success', 'Diagnóstico actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return redirect()->route('psicosocial.index')
            ->with('success', 'Diagnóstico eliminado exitosamente');
    }

    /**
     * Resumen general
     */
    public function resumenGeneral()
    {
        return $this->resumen(1);
    }

    /**
     * Resumen completo
     */
    public function resumenNueveSecciones($id = null)
    {
        return $this->resumen($id ?? 1);
    }

    /**
     * Métodos adicionales básicos
     */
    public function resumenIndividual($hojaId)
    {
        return redirect()->route('psicosocial.index');
    }

    public function obtenerDetalleEmpleado($id, $hojaId)
    {
        return response()->json(['message' => 'En desarrollo']);
    }

    public function evaluacion($diagnosticoId, $hojaId)
    {
        return redirect()->route('psicosocial.index');
    }

    public function enviarLinkEmpleado($id)
    {
        return response()->json(['message' => 'Link enviado']);
    }

    public function createApplicationCard()
    {
        return redirect()->route('psicosocial.index');
    }

    public function storeApplicationCard()
    {
        return redirect()->route('psicosocial.index');
    }

    public function showApplicationCard($id)
    {
        return redirect()->route('psicosocial.index');
    }

    public function showDiagnostico($id)
    {
        return $this->show($id);
    }

    public function intervencion($id)
    {
        return redirect()->route('psicosocial.index');
    }

    public function resultados($id)
    {
        return $this->resumen($id);
    }

    public function imprimir($id)
    {
        return redirect()->route('psicosocial.index');
    }

    public function exportar($id)
    {
        return redirect()->route('psicosocial.index');
    }

    public function exportarPdf($id)
    {
        return redirect()->route('psicosocial.index');
    }

    public function exportarExcel($id)
    {
        return redirect()->route('psicosocial.index');
    }

    public function exportToPDF()
    {
        return redirect()->route('psicosocial.index');
    }

    public function exportExcel()
    {
        return redirect()->route('psicosocial.index');
    }

    public function summaryReport()
    {
        return redirect()->route('psicosocial.index');
    }

    public function detailedReport($evaluationId)
    {
        return redirect()->route('psicosocial.index');
    }

    public function estadisticas()
    {
        return redirect()->route('psicosocial.index');
    }

    public function getGlobalStatisticsData()
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function evaluacionEmpleado($hojaId)
    {
        return redirect()->route('psicosocial.index');
    }

    /**
     * Obtener resumen completo del diagnóstico con todas las estadísticas
     */
    private function obtenerResumenCompleto($diagnosticoId)
    {
        try {
            // Obtener TODAS las hojas del diagnóstico (no solo completadas)
            // Esto permite ver el resumen incluso si las evaluaciones están en progreso
            $hojas = \App\Models\Hoja::where('diagnostico_id', $diagnosticoId)->get();

            if ($hojas->isEmpty()) {
                \Log::info('obtenerResumenCompleto: No hay hojas para diagnostico ' . $diagnosticoId);
                return ['completo' => []];
            }

            \Log::info('obtenerResumenCompleto: Procesando ' . $hojas->count() . ' hojas');

            // Estructura del resumen
            $resumen = [
                'completo' => [
                    'distribucion_riesgo' => $this->calcularDistribucionRiesgo($hojas),
                    'total_psicosocial' => $this->calcularTotalPsicosocial($hojas),
                    'intralaboral_general' => $this->calcularIntralaboralGeneral($hojas),
                    'intralaboral_a' => $this->calcularIntralaboralA($hojas),
                    'intralaboral_b' => $this->calcularIntralaboralB($hojas),
                    'extralaboral' => $this->calcularExtralaboral($hojas),
                    'estres' => $this->calcularEstres($hojas),
                    'datos_sociodemograficos' => $this->calcularDatosSociodemograficos($hojas)
                ]
            ];

            return $resumen;
        } catch (\Exception $e) {
            \Log::error('Error en obtenerResumenCompleto: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return ['completo' => []];
        }
    }

    /**
     * Obtener opciones de filtros desde las hojas
     */
    private function obtenerOpcionesFiltros($hojas)
    {
        $opciones = [
            'areas' => [],
            'sedes' => [],
            'ciudades' => [],
            'tipos_contrato' => [],
            'procesos' => [],
            'formas' => []
        ];

        foreach ($hojas as $hoja) {
            // Áreas
            if (!empty($hoja->area) && !in_array($hoja->area, $opciones['areas'])) {
                $opciones['areas'][] = $hoja->area;
            }

            // Sedes
            if (!empty($hoja->sede) && !in_array($hoja->sede, $opciones['sedes'])) {
                $opciones['sedes'][] = $hoja->sede;
            }

            // Ciudades
            if (!empty($hoja->ciudad_residencia) && !in_array($hoja->ciudad_residencia, $opciones['ciudades'])) {
                $opciones['ciudades'][] = $hoja->ciudad_residencia;
            }

            // Tipos de contrato
            if (!empty($hoja->tipo_contrato) && !in_array($hoja->tipo_contrato, $opciones['tipos_contrato'])) {
                $opciones['tipos_contrato'][] = $hoja->tipo_contrato;
            }

            // Procesos
            if (!empty($hoja->proceso) && !in_array($hoja->proceso, $opciones['procesos'])) {
                $opciones['procesos'][] = $hoja->proceso;
            }

            // Formas
            if (!empty($hoja->forma_aplicada) && !in_array($hoja->forma_aplicada, $opciones['formas'])) {
                $opciones['formas'][] = $hoja->forma_aplicada;
            }
        }

        // Ordenar los arrays
        sort($opciones['areas']);
        sort($opciones['sedes']);
        sort($opciones['ciudades']);
        sort($opciones['tipos_contrato']);
        sort($opciones['procesos']);
        sort($opciones['formas']);

        return $opciones;
    }

    // Métodos auxiliares para cálculos específicos
    private function calcularDistribucionRiesgo($hojas)
    {
        $niveles = [
            'sin_riesgo' => 0,
            'bajo' => 0,
            'medio' => 0,
            'alto' => 0,
            'muy_alto' => 0,
            'sin_calcular' => 0 // Para hojas sin evaluaciones completadas
        ];

        $total = $hojas->count();

        foreach ($hojas as $hoja) {
            // Usar el método getNivelRiesgo() para calcular el nivel dinámicamente
            $nivel = $hoja->getNivelRiesgo();
            
            // Mapear nombres de nivel para consistencia
            $nivelMapeado = str_replace('riesgo_', '', $nivel);
            
            if (isset($niveles[$nivelMapeado])) {
                $niveles[$nivelMapeado]++;
            } elseif ($nivel === 'sin_calcular') {
                $niveles['sin_calcular']++;
            }
        }

        // Calcular porcentajes solo para niveles de riesgo (excluir sin_calcular)
        $totalCalculado = $total - $niveles['sin_calcular'];
        $nivelesResultado = [];
        
        foreach ($niveles as $key => $cantidad) {
            if ($key !== 'sin_calcular') {
                $nivelesResultado[$key] = [
                    'cantidad' => $cantidad,
                    'porcentaje' => $totalCalculado > 0 ? round(($cantidad / $totalCalculado) * 100, 2) : 0
                ];
            }
        }

        return [
            'niveles' => $nivelesResultado,
            'sin_calcular' => $niveles['sin_calcular'],
            'total_evaluado' => $totalCalculado
        ];
    }

    private function calcularTotalPsicosocial($hojas)
    {
        // Implementación básica - ajustar según necesidades
        return [
            'por_instrumento' => [],
            'poblacion' => $hojas->count()
        ];
    }

    private function calcularIntralaboralGeneral($hojas)
    {
        // Implementación básica - ajustar según necesidades
        return [
            'poblacion' => $hojas->count(),
            'dominios' => [],
            'dimensiones' => []
        ];
    }

    private function calcularIntralaboralA($hojas)
    {
        $hojasA = $hojas->where('forma_aplicada', 'A');
        return [
            'poblacion' => $hojasA->count(),
            'dominios' => [],
            'dimensiones' => []
        ];
    }

    private function calcularIntralaboralB($hojas)
    {
        $hojasB = $hojas->where('forma_aplicada', 'B');
        return [
            'poblacion' => $hojasB->count(),
            'dominios' => [],
            'dimensiones' => []
        ];
    }

    private function calcularExtralaboral($hojas)
    {
        return [
            'poblacion' => $hojas->count(),
            'dimensiones' => []
        ];
    }

    private function calcularEstres($hojas)
    {
        return [
            'poblacion' => $hojas->count(),
            'dimensiones' => []
        ];
    }

    private function calcularDatosSociodemograficos($hojas)
    {
        $datos = [
            'genero' => [],
            'edad' => [],
            'estado_civil' => [],
            'tipo_vivienda' => [],
            'estrato_social' => [],
            'tipo_cargo' => []
        ];

        // Cargar las relaciones de datosPersonales para optimizar consultas
        $hojas->load('datosPersonales');

        // Implementación básica - contar por categorías
        foreach ($hojas as $hoja) {
            $datosPersonales = $hoja->datosPersonales;
            
            if (!$datosPersonales) {
                continue; // Saltar hojas sin datos personales
            }

            // Género
            $genero = $datosPersonales->sexo ?? $datosPersonales->genero ?? 'No especificado';
            if (!isset($datos['genero'][$genero])) {
                $datos['genero'][$genero] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $datos['genero'][$genero]['cantidad']++;

            // Edad o rango etario
            $edad = $datosPersonales->rango_edad ?? $datosPersonales->edad ?? 'No especificado';
            if (!isset($datos['edad'][$edad])) {
                $datos['edad'][$edad] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $datos['edad'][$edad]['cantidad']++;

            // Estado civil
            $estadoCivil = $datosPersonales->estado_civil ?? 'No especificado';
            if (!isset($datos['estado_civil'][$estadoCivil])) {
                $datos['estado_civil'][$estadoCivil] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $datos['estado_civil'][$estadoCivil]['cantidad']++;

            // Tipo de vivienda
            $tipoVivienda = $datosPersonales->tipo_vivienda ?? 'No especificado';
            if (!isset($datos['tipo_vivienda'][$tipoVivienda])) {
                $datos['tipo_vivienda'][$tipoVivienda] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $datos['tipo_vivienda'][$tipoVivienda]['cantidad']++;

            // Estrato social
            $estratoSocial = $datosPersonales->estrato ?? $datosPersonales->estrato_social ?? 'No especificado';
            if (!isset($datos['estrato_social'][$estratoSocial])) {
                $datos['estrato_social'][$estratoSocial] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $datos['estrato_social'][$estratoSocial]['cantidad']++;

            // Tipo de cargo
            $tipoCargo = $datosPersonales->tipo_cargo ?? 'No especificado';
            if (!isset($datos['tipo_cargo'][$tipoCargo])) {
                $datos['tipo_cargo'][$tipoCargo] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $datos['tipo_cargo'][$tipoCargo]['cantidad']++;
        }

        // Calcular porcentajes
        $total = $hojas->count();
        foreach ($datos as $categoria => &$valores) {
            foreach ($valores as $key => &$valor) {
                $valor['porcentaje'] = $total > 0 ? round(($valor['cantidad'] / $total) * 100, 2) : 0;
            }
        }

        return $datos;
    }
}
