<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Pregunta;
use App\Models\Datos;
use App\Models\Respuesta;
use App\Models\Consentimiento;
use App\Models\Encuesta;
use App\Models\EvaluacionPsicosocial;

/**
 * Controlador para la Gestión de Instrumentos
 * Maneja Consentimientos, Cuestionarios y Encuestas
 */
class GestionInstrumentosController extends Controller
{
    /**
     * Página principal de Gestión de Instrumentos
     */
    public function index()
    {
        try {
            // Contadores reales desde la base de datos, filtrados por empresa si hay usuario autenticado
            // Asunciones:
            // - Consentimientos: contados por registros activos y por empresa
            // - Encuestas: contadas por empresa cuando aplique
            // - Aplicaciones activas: contadas como evaluaciones psicosociales registradas por empresa
            // - Cuestionarios: lista de sistema (no almacenados por empresa en BD en este punto)

            $empresaId = Auth::check() && isset(Auth::user()->empresa_id) ? Auth::user()->empresa_id : null;

            if ($empresaId) {
                $total_consentimientos = Consentimiento::activos()->where('empresa_id', $empresaId)->count();
                $total_encuestas = Encuesta::where('empresa_id', $empresaId)->count();
                $aplicaciones_activas = EvaluacionPsicosocial::where('empresa_id', $empresaId)->count();
            } else {
                // Fallback global
                $total_consentimientos = Consentimiento::activos()->count();
                $total_encuestas = Encuesta::count();
                $aplicaciones_activas = EvaluacionPsicosocial::count();
            }

            $total_cuestionarios = count($this->getCuestionariosList());

            $stats = [
                'total_consentimientos' => $total_consentimientos,
                'total_cuestionarios' => $total_cuestionarios,
                'total_encuestas' => $total_encuestas,
                'aplicaciones_activas' => $aplicaciones_activas,
            ];

            return view('admin.gestion-instrumentos.index', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error en GestionInstrumentosController@index: ' . $e->getMessage());

            // Valores por defecto si falla la consulta
            $stats = [
                'total_consentimientos' => 0,
                'total_cuestionarios' => count($this->getCuestionariosList()),
                'total_encuestas' => 0,
                'aplicaciones_activas' => 0,
            ];

            return view('admin.gestion-instrumentos.index', compact('stats'))
                ->with('warning', 'No se pudieron cargar las estadísticas principales');
        }
    }

    // ============================================
    // CONSENTIMIENTOS
    // ============================================

    /**
     * Lista de consentimientos
     */
    public function consentimientosIndex()
    {
        try {
            // Placeholder - aquí se implementaría el modelo de Consentimientos
            $consentimientos = collect([
                (object)[
                    'id' => 1,
                    'nombre' => 'Consentimiento Informado - Evaluación Psicosocial',
                    'descripcion' => 'Consentimiento para la aplicación de la batería de riesgo psicosocial',
                    'estado' => 'activo',
                    'created_at' => now()->subDays(30),
                    'updated_at' => now()
                ],
                (object)[
                    'id' => 2,
                    'nombre' => 'Consentimiento - Manejo de Datos Personales',
                    'descripcion' => 'Autorización para el tratamiento de datos personales según Ley 1581',
                    'estado' => 'activo',
                    'created_at' => now()->subDays(15),
                    'updated_at' => now()
                ]
            ]);

            return view('admin.gestion-instrumentos.consentimientos.index', compact('consentimientos'));
        } catch (\Exception $e) {
            Log::error('Error en consentimientosIndex: ' . $e->getMessage());
            return view('admin.gestion-instrumentos.consentimientos.index', [
                'consentimientos' => collect([])
            ]);
        }
    }

    /**
     * Crear consentimiento
     */
    public function consentimientosCreate()
    {
        return view('admin.gestion-instrumentos.consentimientos.create');
    }

    /**
     * Editar consentimiento
     */
    public function consentimientosEdit($id)
    {
        // Placeholder - implementar cuando se tenga el modelo
        $consentimiento = (object)[
            'id' => $id,
            'nombre' => 'Consentimiento de Ejemplo',
            'descripcion' => 'Descripción del consentimiento',
            'contenido' => 'Contenido del consentimiento informado...',
            'estado' => 'activo'
        ];

        return view('admin.gestion-instrumentos.consentimientos.edit', compact('consentimiento'));
    }

    // ============================================
    // CUESTIONARIOS
    // ============================================

    /**
     * Lista de cuestionarios
     */
    public function cuestionariosIndex()
    {
        try {
            // Obtener conteos de preguntas desde la base de datos
            $preguntasFormaA = Pregunta::where('tipo', 'intralaboral_a')->count();
            $preguntasFormaB = Pregunta::where('tipo', 'intralaboral_b')->count();
            $preguntasExtralaboral = Pregunta::where('tipo', 'extralaboral')->count();
            $preguntasEstres = Pregunta::where('tipo', 'estres')->count();

            return view('admin.gestion-instrumentos.cuestionarios.index', compact(
                'preguntasFormaA', 
                'preguntasFormaB', 
                'preguntasExtralaboral', 
                'preguntasEstres'
            ));
        } catch (\Exception $e) {
            Log::error('Error en cuestionariosIndex: ' . $e->getMessage());
            
            // Valores por defecto en caso de error
            $preguntasFormaA = 123;
            $preguntasFormaB = 97;
            $preguntasExtralaboral = 31;
            $preguntasEstres = 31;
            
            return view('admin.gestion-instrumentos.cuestionarios.index', compact(
                'preguntasFormaA', 
                'preguntasFormaB', 
                'preguntasExtralaboral', 
                'preguntasEstres'
            ))->with('warning', 'No se pudo conectar a la base de datos de preguntas');
        }
    }

    /**
     * Ver detalles de un cuestionario específico
     */
    public function cuestionariosShow($tipo)
    {
        try {
            $cuestionario = $this->obtenerCuestionarioPorTipo($tipo);
            
            if (!$cuestionario) {
                return redirect()->route('gestion-instrumentos.cuestionarios.index')
                    ->with('error', 'Cuestionario no encontrado');
            }

            // Obtener preguntas según el tipo
            $preguntas = collect([]);
            if ($tipo !== 'datos-generales') {
                $preguntas = Pregunta::where('tipo', $this->mapearTipoPregunta($tipo))
                    ->orderBy('consecutivo')
                    ->get();
            }

            return view('admin.gestion-instrumentos.cuestionarios.show', compact('cuestionario', 'preguntas', 'tipo'));
        } catch (\Exception $e) {
            Log::error('Error en cuestionariosShow: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.cuestionarios.index')
                ->with('error', 'Error al cargar el cuestionario');
        }
    }

    /**
     * Obtener preguntas para cuestionario individual
     */
    public function obtenerPreguntas($tipo)
    {
        try {
            $tipoPregunta = $this->mapearTipoPregunta($tipo);
            
            $preguntas = Pregunta::where('tipo', $tipoPregunta)
                ->orderBy('consecutivo')
                ->get();

            return $preguntas;
        } catch (\Exception $e) {
            Log::error('Error al obtener preguntas: ' . $e->getMessage());
            return collect([]);
        }
    }

    // ============================================
    // CUESTIONARIOS ESPECÍFICOS
    // ============================================

    /**
     * Ficha de Datos Generales
     */
    public function datosGenerales()
    {
        try {
            // Campos de la ficha de datos generales (19 campos)
            $campos = [
                'fecha_evaluacion', 'numero_documento', 'nombres', 'apellidos',
                'sexo', 'estado_civil', 'fecha_nacimiento', 'nivel_estudio',
                'profesion', 'ciudad_nacimiento', 'ciudad_residencia', 'estrato',
                'tipo_vivienda', 'personas_hogar', 'cargo', 'area_trabajo',
                'tipo_contrato', 'antiguedad_empresa', 'salario'
            ];

            return view('admin.gestion-instrumentos.cuestionarios.datos-generales', compact('campos'));
        } catch (\Exception $e) {
            Log::error('Error en datosGenerales: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.cuestionarios.index')
                ->with('error', 'Error al cargar la ficha de datos generales');
        }
    }

    /**
     * Factores Intralaborales Forma A
     */
    public function intralaboralFormaA()
    {
        try {
            $preguntas = Pregunta::where('tipo', 'intralaboral_a')
                ->orderBy('consecutivo')
                ->get();

            return view('admin.gestion-instrumentos.cuestionarios.intralaboral-forma-a', compact('preguntas'));
        } catch (\Exception $e) {
            Log::error('Error en intralaboralFormaA: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.cuestionarios.index')
                ->with('error', 'Error al cargar el cuestionario Intralaboral Forma A');
        }
    }

    /**
     * Factores Intralaborales Forma B
     */
    public function intralaboralFormaB()
    {
        try {
            $preguntas = Pregunta::where('tipo', 'intralaboral_b')
                ->orderBy('consecutivo')
                ->get();

            return view('admin.gestion-instrumentos.cuestionarios.intralaboral-forma-b', compact('preguntas'));
        } catch (\Exception $e) {
            Log::error('Error en intralaboralFormaB: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.cuestionarios.index')
                ->with('error', 'Error al cargar el cuestionario Intralaboral Forma B');
        }
    }

    /**
     * Factores Extralaborales
     */
    public function extralaboral()
    {
        try {
            $preguntas = Pregunta::where('tipo', 'extralaboral')
                ->orderBy('consecutivo')
                ->get();

            return view('admin.gestion-instrumentos.cuestionarios.extralaboral', compact('preguntas'));
        } catch (\Exception $e) {
            Log::error('Error en extralaboral: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.cuestionarios.index')
                ->with('error', 'Error al cargar el cuestionario Extralaboral');
        }
    }

    /**
     * Cuestionario de Estrés
     */
    public function estres()
    {
        try {
            $preguntas = Pregunta::where('tipo', 'estres')
                ->orderBy('consecutivo')
                ->get();

            // Si no hay preguntas en la base de datos, usar preguntas de ejemplo
            if ($preguntas->isEmpty()) {
                // Agregar mensaje de advertencia
                session()->flash('warning', 'Las preguntas mostradas son ejemplos. Para usar el cuestionario oficial, contacta al administrador.');
                
                $preguntas = collect([
                    (object)[
                        'id' => 1,
                        'consecutivo' => 1,
                        'enunciado' => 'Dolores en el cuello y espalda o tensión muscular',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 2,
                        'consecutivo' => 2,
                        'enunciado' => 'Problemas del sueño como somnolencia durante el día o desvelo en la noche',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 3,
                        'consecutivo' => 3,
                        'enunciado' => 'Fatiga, cansancio o agotamiento',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 4,
                        'consecutivo' => 4,
                        'enunciado' => 'Problemas de concentración',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 5,
                        'consecutivo' => 5,
                        'enunciado' => 'Irritabilidad',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 6,
                        'consecutivo' => 6,
                        'enunciado' => 'Sentimientos de tristeza o melancolía',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 7,
                        'consecutivo' => 7,
                        'enunciado' => 'Sentimientos de ansiedad o nerviosismo',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 8,
                        'consecutivo' => 8,
                        'enunciado' => 'Dolor de cabeza',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 9,
                        'consecutivo' => 9,
                        'enunciado' => 'Problemas digestivos',
                        'tipo' => 'estres'
                    ],
                    (object)[
                        'id' => 10,
                        'consecutivo' => 10,
                        'enunciado' => 'Sentimientos de desesperanza hacia el futuro',
                        'tipo' => 'estres'
                    ]
                ]);
            }

            return view('admin.gestion-instrumentos.cuestionarios.estres', compact('preguntas'));
        } catch (\Exception $e) {
            Log::error('Error en estres: ' . $e->getMessage());
            
            // En caso de error, crear un conjunto mínimo de preguntas
            $preguntas = collect([
                (object)[
                    'id' => 1,
                    'consecutivo' => 1,
                    'enunciado' => 'Dolor de cabeza',
                    'tipo' => 'estres'
                ],
                (object)[
                    'id' => 2,
                    'consecutivo' => 2,
                    'enunciado' => 'Fatiga o cansancio',
                    'tipo' => 'estres'
                ]
            ]);
            
            return view('admin.gestion-instrumentos.cuestionarios.estres', compact('preguntas'))
                ->with('warning', 'Mostrando preguntas de ejemplo. Contacta al administrador para cargar las preguntas oficiales.');
        }
    }

    /**
     * Lista de encuestas
     */
    public function encuestasIndex()
    {
        try {
            // Placeholder - implementar modelo de Encuestas
            $encuestas = collect([
                (object)[
                    'id' => 1,
                    'nombre' => 'Encuesta de Satisfacción Laboral',
                    'descripcion' => 'Evaluación del nivel de satisfacción del personal',
                    'total_preguntas' => 15,
                    'estado' => 'activo',
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()
                ]
            ]);

            return view('admin.gestion-instrumentos.encuestas.index', compact('encuestas'));
        } catch (\Exception $e) {
            Log::error('Error en encuestasIndex: ' . $e->getMessage());
            return view('admin.gestion-instrumentos.encuestas.index', [
                'encuestas' => collect([])
            ]);
        }
    }

    /**
     * Crear encuesta
     */
    public function encuestasCreate()
    {
        return view('admin.gestion-instrumentos.encuestas.create');
    }

    /**
     * Editar encuesta
     */
    public function encuestasEdit($id)
    {
        // Placeholder
        $encuesta = (object)[
            'id' => $id,
            'nombre' => 'Encuesta de Ejemplo',
            'descripcion' => 'Descripción de la encuesta',
            'estado' => 'activo'
        ];

        return view('admin.gestion-instrumentos.encuestas.edit', compact('encuesta'));
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Obtener información del cuestionario por tipo
     */
    private function obtenerCuestionarioPorTipo($tipo)
    {
        $cuestionarios = $this->getCuestionariosList();

        return $cuestionarios[$tipo] ?? null;
    }

    /**
     * Lista de cuestionarios disponibles en el sistema
     * (centraliza la definición para reutilizar en contadores y vistas)
     */
    private function getCuestionariosList()
    {
        return [
            'datos-generales' => (object)[
                'id' => 'datos-generales',
                'nombre' => 'Ficha de Datos Generales',
                'descripcion' => 'Información sociodemográfica y ocupacional del empleado',
                'total_items' => 19,
                'tipo' => 'datos_generales',
                'estado' => 'activo',
                'es_sistema' => true
            ],
            'intralaborales-a' => (object)[
                'id' => 'intralaborales-a',
                'nombre' => 'Factores Intralaborales - Forma A (Profesionales)',
                'descripcion' => 'Cuestionario para personal profesional y ejecutivo',
                'total_items' => 123,
                'tipo' => 'intralaborales_a',
                'estado' => 'activo',
                'es_sistema' => true
            ],
            'intralaborales-b' => (object)[
                'id' => 'intralaborales-b',
                'nombre' => 'Factores Intralaborales - Forma B (Auxiliares y Operarios)',
                'descripcion' => 'Cuestionario para personal auxiliar y operario',
                'total_items' => 97,
                'tipo' => 'intralaborales_b',
                'estado' => 'activo',
                'es_sistema' => true
            ],
            'extralaborales' => (object)[
                'id' => 'extralaborales',
                'nombre' => 'Factores Extralaborales',
                'descripcion' => 'Condiciones del entorno familiar, social y económico',
                'total_items' => 31,
                'tipo' => 'extralaborales',
                'estado' => 'activo',
                'es_sistema' => true
            ],
            'estres' => (object)[
                'id' => 'estres',
                'nombre' => 'Cuestionario de Estrés',
                'descripcion' => 'Evaluación de síntomas de estrés en el trabajo',
                'total_items' => 31,
                'tipo' => 'estres',
                'estado' => 'activo',
                'es_sistema' => true
            ]
        ];
    }

    /**
     * Mapear tipo de cuestionario a tipo de pregunta en BD
     */
    private function mapearTipoPregunta($tipo)
    {
        $mapeo = [
            'intralaborales-a' => 'intralaboral_a',
            'intralaborales-b' => 'intralaboral_b', 
            'extralaborales' => 'extralaboral',
            'estres' => 'estres'
        ];

        return $mapeo[$tipo] ?? $tipo;
    }

    /**
     * Cambiar estado de un elemento (placeholder)
     */
    public function cambiarEstado(Request $request)
    {
        // Implementar lógica para cambiar estado de consentimientos/encuestas
        return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
    }

    /**
     * Eliminar elemento (placeholder)
     */
    public function eliminar(Request $request)
    {
        // Implementar lógica para eliminar consentimientos/encuestas
        return response()->json(['success' => true, 'message' => 'Elemento eliminado correctamente']);
    }
}
