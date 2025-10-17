<?php

namespace App\Http\Controllers\Psicosocial;

use App\Http\Controllers\Controller;
use App\Models\Pregunta;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GestionInstrumentosController extends Controller
{
    /**
     * Muestra el cuestionario Intralaboral Forma A conforme al manual oficial
     */
    public function intralaboralFormaA(Request $request)
    {
        try {
            // Verificar si hay preguntas en la base de datos
            $totalPreguntas = Pregunta::where('tipo', 'intralaboral_a')->count();
            
            if ($totalPreguntas == 0) {
                return view('psicosocial.error-instrumentos', [
                    'mensaje' => 'No se encontraron preguntas para Forma A en la base de datos',
                    'detalle' => 'Se requieren preguntas tipo "intralaboral_a" para mostrar este cuestionario'
                ]);
            }
            
            // Cargar todas las preguntas de la Forma A desde la base de datos
            $todasPreguntasA = Pregunta::where('tipo', 'intralaboral_a')
                                     ->orderBy('numero', 'asc')
                                     ->get();
            
            // Log para depuración
            Log::info('Cargando Forma A', [
                'total_preguntas' => $totalPreguntas,
                'preguntas_encontradas' => $todasPreguntasA->count()
            ]);
            
            // Organizar preguntas según estructura del manual
            $preguntasOrganizadas = $this->organizarPreguntasFormaA($todasPreguntasA);
            
            return view('psicosocial.intralaboral-forma-a', [
                'preguntasClientes' => $preguntasOrganizadas['clientes'] ?? collect(),
                'preguntasJefe' => $preguntasOrganizadas['jefe'] ?? collect(),
                'preguntasGenerales' => $preguntasOrganizadas['generales'] ?? collect(),
                'preguntasCantidadTrabajo' => $preguntasOrganizadas['cantidad_trabajo'] ?? collect(),
                'preguntasEsfuerzoMental' => $preguntasOrganizadas['esfuerzo_mental'] ?? collect(),
                'preguntasResponsabilidades' => $preguntasOrganizadas['responsabilidades'] ?? collect(),
                'hojaId' => $request->get('hoja_id'),
                'debug_info' => [
                    'total_preguntas' => $totalPreguntas,
                    'total_organizadas' => collect($preguntasOrganizadas)->flatten()->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error cargando Forma A: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('psicosocial.error-instrumentos', [
                'mensaje' => 'Error al cargar el cuestionario Forma A',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    /**
     * Muestra el cuestionario Intralaboral Forma B conforme al manual oficial
     */
    public function intralaboralFormaB(Request $request)
    {
        try {
            // Cargar todas las preguntas de la Forma B desde la base de datos
            $todasPreguntasB = Pregunta::where('tipo', 'intralaboral_b')
                                     ->orderBy('numero', 'asc')
                                     ->get();
            
            // Organizar preguntas según estructura del manual
            $preguntasOrganizadas = $this->organizarPreguntasFormaB($todasPreguntasB);
            
            return view('psicosocial.intralaboral-forma-b', [
                'preguntasClientes' => $preguntasOrganizadas['clientes'] ?? collect(),
                'preguntasJefe' => $preguntasOrganizadas['jefe'] ?? collect(),
                'preguntasGenerales' => $preguntasOrganizadas['generales'] ?? collect(),
                'todasLasPreguntas' => $preguntasOrganizadas,
                'hojaId' => $request->get('hoja_id')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error cargando Forma B: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario');
        }
    }

    /**
     * Organiza las preguntas de la Forma A según la estructura del manual oficial
     */
    private function organizarPreguntasFormaA($preguntas)
    {
        // Estructura según manual oficial y imágenes adjuntas
        $estructura = [
            'clientes' => $preguntas->filter(function($p) {
                // Preguntas relacionadas con atención a clientes (números según manual)
                return in_array($p->numero, [106, 107, 108, 109, 110, 111, 112, 113, 114]);
            }),
            'jefe' => $preguntas->filter(function($p) {
                // Preguntas relacionadas con supervisión (números según manual)
                return in_array($p->numero, [115, 116, 117, 118, 119, 120, 121, 122, 123]);
            }),
            'generales' => $preguntas->filter(function($p) {
                // Preguntas ambientales/condiciones del sitio de trabajo
                return in_array($p->numero, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
            }),
            'cantidad_trabajo' => $preguntas->filter(function($p) {
                // Preguntas sobre cantidad de trabajo
                return in_array($p->numero, [13, 14, 15]);
            }),
            'esfuerzo_mental' => $preguntas->filter(function($p) {
                // Preguntas sobre esfuerzo mental
                return in_array($p->numero, [16, 17, 18, 19, 20, 21]);
            }),
            'responsabilidades' => $preguntas->filter(function($p) {
                // Preguntas sobre responsabilidades
                return in_array($p->numero, [22]) || $p->numero >= 23;
            })
        ];
        
        return $estructura;
    }

    /**
     * Organiza las preguntas de la Forma B según la estructura del manual oficial
     */
    private function organizarPreguntasFormaB($preguntas)
    {
        // Estructura similar a Forma A pero adaptada para Forma B (97 preguntas)
        $estructura = [
            'clientes' => $preguntas->filter(function($p) {
                // Preguntas de clientes para Forma B
                return in_array($p->numero, [86, 87, 88, 89, 90, 91]);
            }),
            'jefe' => $preguntas->filter(function($p) {
                // Preguntas de jefe para Forma B
                return in_array($p->numero, [92, 93, 94, 95, 96, 97]);
            }),
            'generales' => $preguntas->filter(function($p) {
                // Preguntas ambientales para Forma B
                return in_array($p->numero, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
            }),
            'cantidad_trabajo' => $preguntas->filter(function($p) {
                return in_array($p->numero, [13, 14, 15]);
            }),
            'esfuerzo_mental' => $preguntas->filter(function($p) {
                return in_array($p->numero, [16, 17, 18, 19, 20, 21]);
            }),
            'responsabilidades' => $preguntas->filter(function($p) {
                return $p->numero >= 22 && $p->numero <= 85;
            })
        ];
        
        return $estructura;
    }

    /**
     * Cuestionario extralaboral
     */
    public function extralaboral(Request $request)
    {
        try {
            $preguntas = Pregunta::where('tipo', 'extralaboral')
                               ->orderBy('numero', 'asc')
                               ->get();
            
            return view('psicosocial.extralaboral', [
                'preguntas' => $preguntas,
                'hojaId' => $request->get('hoja_id')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error cargando extralaboral: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario extralaboral');
        }
    }

    /**
     * Cuestionario de estrés
     */
    public function estres(Request $request)
    {
        try {
            $preguntas = Pregunta::where('tipo', 'estres')
                               ->orderBy('numero', 'asc')
                               ->get();
            
            return view('psicosocial.estres', [
                'preguntas' => $preguntas,
                'hojaId' => $request->get('hoja_id')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error cargando estrés: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cuestionario de estrés');
        }
    }

    /**
     * Guardar respuestas en la base de datos
     */
    public function guardarRespuestas(Request $request)
    {
        try {
            $tipoInstrumento = $request->input('tipo_instrumento');
            $hojaId = $request->input('hoja_id');
            
            // Validar datos requeridos
            if (!$tipoInstrumento || !$hojaId) {
                return back()->with('error', 'Datos incompletos para guardar respuestas');
            }
            
            // Procesar respuestas
            $respuestasGuardadas = 0;
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'respuesta_') === 0) {
                    $preguntaId = str_replace('respuesta_', '', $key);
                    
                    // Crear o actualizar respuesta
                    Respuesta::updateOrCreate([
                        'hoja_id' => $hojaId,
                        'pregunta_id' => $preguntaId
                    ], [
                        'valor' => (int)$value,
                        'tipo_instrumento' => $tipoInstrumento
                    ]);
                    
                    $respuestasGuardadas++;
                }
            }
            
            // Guardar respuestas adicionales (es_jefe, atiende_clientes)
            if ($request->has('es_jefe')) {
                Respuesta::updateOrCreate([
                    'hoja_id' => $hojaId,
                    'pregunta_id' => 'es_jefe'
                ], [
                    'valor' => $request->input('es_jefe'),
                    'tipo_instrumento' => $tipoInstrumento
                ]);
            }
            
            if ($request->has('atiende_clientes')) {
                Respuesta::updateOrCreate([
                    'hoja_id' => $hojaId,
                    'pregunta_id' => 'atiende_clientes'
                ], [
                    'valor' => $request->input('atiende_clientes'),
                    'tipo_instrumento' => $tipoInstrumento
                ]);
            }
            
            return redirect()->route('psicosocial.index')
                           ->with('success', "Respuestas guardadas exitosamente. Total: $respuestasGuardadas");
            
        } catch (\Exception $e) {
            Log::error('Error guardando respuestas: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar las respuestas');
        }
    }
}
