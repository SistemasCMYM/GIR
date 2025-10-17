<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Datos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\SuperAdminAccess;
use App\Traits\RoleBasedAccess;

class GestionInstrumentosController extends Controller
{
    use SuperAdminAccess, RoleBasedAccess {
        SuperAdminAccess::isSuperAdmin insteadof RoleBasedAccess;
        RoleBasedAccess::isSuperAdmin as isSuperAdminRoleBased;
        SuperAdminAccess::hasModuleAccess insteadof RoleBasedAccess;
        RoleBasedAccess::hasModuleAccess as hasModuleAccessRoleBased;
    }

    /**
     * Display the main gestión instrumentos dashboard
     */
    public function index()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            if (!$this->hasModuleAccess($userData, 'psicosocial')) {
                return view('errors.403', [
                    'message' => 'No tiene permisos para acceder al módulo de Gestión de Instrumentos Psicosociales.'
                ]);
            }

            return view('admin.gestion-instrumentos.index', [
                'userData' => $userData,
                'empresaData' => $empresaData
            ]);

        } catch (\Exception $e) {
            Log::error('Error en gestión instrumentos index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el módulo de gestión de instrumentos.');
        }
    }

    /**
     * Display cuestionarios index
     */
    public function cuestionariosIndex()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Verificar estadísticas de preguntas
            $estadisticasPreguntas = [
                'intralaboral_a' => Pregunta::where('tipo', 'intralaboral_a')->count(),
                'intralaboral_b' => Pregunta::where('tipo', 'intralaboral_b')->count(),
                'extralaboral' => Pregunta::where('tipo', 'extralaboral')->count(),
                'estres' => Pregunta::where('tipo', 'estres')->count(),
                'total' => Pregunta::count()
            ];

            return view('admin.gestion-instrumentos.cuestionarios.index', [
                'userData' => $userData,
                'empresaData' => $empresaData,
                'estadisticasPreguntas' => $estadisticasPreguntas
            ]);

        } catch (\Exception $e) {
            Log::error('Error en cuestionarios index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los cuestionarios.');
        }
    }

    /**
     * Display Intralaboral Forma A según estructura del manual
     */
    public function intralaboralFormaA()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Cargar preguntas organizadas por dominios y dimensiones según manual
            $preguntasFormaA = Pregunta::where('tipo', 'intralaboral_a')
                                    ->orderBy('consecutivo')
                                    ->get();

            // Organizar por estructura del manual página 93+
            $dominiosFormaA = $this->organizarPreguntasPorDominios($preguntasFormaA, 'forma_a');

            return view('admin.gestion-instrumentos.cuestionarios.intralaboral-forma-a', [
                'userData' => $userData,
                'empresaData' => $empresaData,
                'preguntas' => $preguntasFormaA,
                'dominios' => $dominiosFormaA,
                'totalPreguntas' => 123
            ]);

        } catch (\Exception $e) {
            Log::error('Error en intralaboral forma A: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario Intralaboral Forma A.');
        }
    }

    /**
     * Display Intralaboral Forma B según estructura del manual
     */
    public function intralaboralFormaB()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Cargar preguntas organizadas por dominios y dimensiones según manual
            $preguntasFormaB = Pregunta::where('tipo', 'intralaboral_b')
                                    ->orderBy('consecutivo')
                                    ->get();

            // Organizar por estructura del manual página 93+
            $dominiosFormaB = $this->organizarPreguntasPorDominios($preguntasFormaB, 'forma_b');

            return view('admin.gestion-instrumentos.cuestionarios.intralaboral-forma-b', [
                'userData' => $userData,
                'empresaData' => $empresaData,
                'preguntas' => $preguntasFormaB,
                'dominios' => $dominiosFormaB,
                'totalPreguntas' => 97
            ]);

        } catch (\Exception $e) {
            Log::error('Error en intralaboral forma B: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario Intralaboral Forma B.');
        }
    }

    /**
     * Display Extralaboral cuestionario
     */
    public function extralaboral()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            $preguntasExtralaboral = Pregunta::where('tipo', 'extralaboral')
                                           ->orderBy('consecutivo')
                                           ->get();

            return view('admin.gestion-instrumentos.cuestionarios.extralaboral', [
                'userData' => $userData,
                'empresaData' => $empresaData,
                'preguntas' => $preguntasExtralaboral,
                'totalPreguntas' => 31
            ]);

        } catch (\Exception $e) {
            Log::error('Error en extralaboral: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario Extralaboral.');
        }
    }

    /**
     * Display Estrés cuestionario
     */
    public function estres()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            $preguntasEstres = Pregunta::where('tipo', 'estres')
                                     ->orderBy('consecutivo')
                                     ->get();

            return view('admin.gestion-instrumentos.cuestionarios.estres', [
                'userData' => $userData,
                'empresaData' => $empresaData,
                'preguntas' => $preguntasEstres,
                'totalPreguntas' => 31
            ]);

        } catch (\Exception $e) {
            Log::error('Error en estrés: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario de Estrés.');
        }
    }

    /**
     * Display Datos Generales form
     */
    public function datosGenerales()
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit')->with('error', 'Sesión no iniciada');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            return view('admin.gestion-instrumentos.cuestionarios.datos-generales', [
                'userData' => $userData,
                'empresaData' => $empresaData
            ]);

        } catch (\Exception $e) {
            Log::error('Error en datos generales: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario de Datos Generales.');
        }
    }

    /**
     * Organizar preguntas por dominios según manual oficial (página 93+)
     */
    private function organizarPreguntasPorDominios($preguntas, $forma)
    {
        // Estructura según manual bateria-instrumento-evaluacion-factores-riesgo-psicosocial.pdf
        if ($forma === 'forma_a') {
            return [
                'liderazgo_relaciones_sociales' => [
                    'nombre' => 'Liderazgo y relaciones sociales en el trabajo',
                    'items' => '61 ítems (1-61)',
                    'dimensiones' => [
                        'caracteristicas_liderazgo' => [
                            'nombre' => 'Características del liderazgo',
                            'factores' => ['relaciones_sociales', 'retroalimentacion_rendimiento', 'relacion_jefe']
                        ],
                        'relaciones_sociales_trabajo' => [
                            'nombre' => 'Relaciones sociales en el trabajo',
                            'factores' => ['relaciones_sociales', 'apoyo_companeros']
                        ]
                    ],
                    'preguntas' => $preguntas->whereBetween('consecutivo', [1, 61])
                ],
                'control_trabajo' => [
                    'nombre' => 'Control sobre el trabajo',
                    'items' => '44 ítems (62-105)',
                    'dimensiones' => [
                        'claridad_rol' => ['nombre' => 'Claridad de rol'],
                        'capacitacion' => ['nombre' => 'Capacitación'],
                        'participacion_cambio' => ['nombre' => 'Participación y manejo del cambio'],
                        'oportunidades_desarrollo' => ['nombre' => 'Oportunidades para el uso y desarrollo de habilidades y conocimientos'],
                        'control_autonomia' => ['nombre' => 'Control y autonomía sobre el trabajo']
                    ],
                    'preguntas' => $preguntas->whereBetween('consecutivo', [62, 105])
                ],
                'demandas_trabajo' => [
                    'nombre' => 'Demandas del trabajo',
                    'items' => '18 ítems (106-123)',
                    'dimensiones' => [
                        'demandas_ambientales' => ['nombre' => 'Demandas ambientales y de esfuerzo físico'],
                        'demandas_emocionales' => ['nombre' => 'Demandas emocionales'],
                        'demandas_cuantitativas' => ['nombre' => 'Demandas cuantitativas'],
                        'influencia_entorno_extralaboral' => ['nombre' => 'Influencia del trabajo sobre el entorno extralaboral'],
                        'exigencias_responsabilidad' => ['nombre' => 'Exigencias de responsabilidad del cargo'],
                        'demandas_carga_mental' => ['nombre' => 'Demandas de carga mental'],
                        'consistencia_rol' => ['nombre' => 'Consistencia del rol'],
                        'demandas_jornada' => ['nombre' => 'Demandas de la jornada de trabajo']
                    ],
                    'preguntas' => $preguntas->whereBetween('consecutivo', [106, 123])
                ]
            ];
        } else { // forma_b
            return [
                'liderazgo_relaciones_sociales' => [
                    'nombre' => 'Liderazgo y relaciones sociales en el trabajo',
                    'items' => '47 ítems (1-47)',
                    'preguntas' => $preguntas->whereBetween('consecutivo', [1, 47])
                ],
                'control_trabajo' => [
                    'nombre' => 'Control sobre el trabajo',
                    'items' => '31 ítems (48-78)',
                    'preguntas' => $preguntas->whereBetween('consecutivo', [48, 78])
                ],
                'demandas_trabajo' => [
                    'nombre' => 'Demandas del trabajo',
                    'items' => '19 ítems (79-97)',
                    'preguntas' => $preguntas->whereBetween('consecutivo', [79, 97])
                ]
            ];
        }
    }

    /**
     * Guardar respuestas de cuestionario
     */
    public function guardarRespuestas(Request $request)
    {
        try {
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return response()->json(['error' => 'Sesión no iniciada'], 401);
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Validar datos del request
            $respuestas = $request->except(['_token', 'cuestionario_tipo']);
            $tipoCuestionario = $request->input('cuestionario_tipo');

            if (empty($respuestas)) {
                return response()->json(['error' => 'No se recibieron respuestas'], 400);
            }

            // Guardar en colección respuestas
            foreach ($respuestas as $preguntaId => $valor) {
                if (strpos($preguntaId, 'pregunta_') === 0) {
                    $preguntaIdClean = str_replace('pregunta_', '', $preguntaId);
                    
                    Respuesta::create([
                        'pregunta_id' => $preguntaIdClean,
                        'tipo_cuestionario' => $tipoCuestionario,
                        'valor_respuesta' => (int)$valor,
                        'usuario_id' => $userData['_id'] ?? null,
                        'empresa_id' => $empresaData['_id'] ?? null,
                        'fecha_respuesta' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Respuestas guardadas exitosamente',
                'total_respuestas' => count($respuestas)
            ]);

        } catch (\Exception $e) {
            Log::error('Error guardando respuestas: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}
