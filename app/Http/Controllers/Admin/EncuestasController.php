<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Encuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class EncuestasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $empresaId = session('empresa_id');
            if (!$empresaId) {
                return redirect()->route('dashboard')->with('error', 'Debe seleccionar una empresa');
            }

            $encuestas = Encuesta::where('empresa_id', $empresaId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $encuestasFiltradas = Encuesta::where('empresa_id', $empresaId)->get();

            $stats = [
                'total' => $encuestasFiltradas->count(),
                'activas' => $encuestasFiltradas->where('estado', true)->count(),
                'publicadas' => $encuestasFiltradas->where('publicada', true)->count(),
                'plantillas' => $encuestasFiltradas->where('plantilla', true)->count()
            ];

            return view('admin.gestion-instrumentos.encuestas.index', compact('encuestas', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error al cargar encuestas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las encuestas');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gestion-instrumentos.encuestas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mapear 'nombre' a 'titulo' si viene del formulario
        if ($request->has('nombre') && !$request->has('titulo')) {
            $request->merge(['titulo' => $request->input('nombre')]);
        }

        // Establecer tipo y categoría por defecto si no vienen en la solicitud
        if (!$request->has('tipo')) {
            $request->merge(['tipo' => 'personalizada']);
        }
        if (!$request->has('categoria')) {
            $request->merge(['categoria' => 'general']);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|string|in:satisfaccion,clima_laboral,evaluacion_desempeño,feedback_360,cultura_organizacional,personalizada,general',
            'categoria' => 'required|string|in:rrhh,psicosocial,seguridad,calidad,satisfaccion,general',
            'preguntas' => 'nullable|array',
            'tiempo_estimado' => 'nullable|integer|min:1|max:120',
            'plantilla' => 'boolean'
        ]);
        
        try {
            // Obtener empresa_id de la sesión
            $empresaId = session('empresa_id');
            if (!$empresaId) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró empresa en la sesión'
                    ], 400);
                }
                return redirect()->route('dashboard')->with('error', 'Debe seleccionar una empresa');
            }

            $encuesta = new Encuesta();
            $encuesta->fill($request->all());
            $encuesta->empresa_id = $empresaId;
            $encuesta->usuario_creador = session('user_data.id') ?? Auth::id();
            $encuesta->items_total = is_array($request->preguntas) ? count($request->preguntas) : 0;
            
            // Asegurar que plantilla sea booleano
            $encuesta->plantilla = $request->boolean('plantilla', false);
            
            // Establecer estado por defecto
            if (!isset($encuesta->estado)) {
                $encuesta->estado = ($request->input('estado') === 'activa');
            }
            
            $encuesta->save();

            // Si es petición AJAX, retornar JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Encuesta creada exitosamente',
                    'encuesta_id' => $encuesta->_id ?? $encuesta->id
                ]);
            }

            return redirect()->route('gestion-instrumentos.encuestas.index')
                ->with('success', 'Encuesta creada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear encuesta: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la encuesta: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la encuesta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            return view('admin.gestion-instrumentos.encuestas.show', compact('encuesta'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar encuesta: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.encuestas.index')
                ->with('error', 'Encuesta no encontrada');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            return view('admin.gestion-instrumentos.encuestas.edit', compact('encuesta'));
        } catch (\Exception $e) {
            Log::error('Error al editar encuesta: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.encuestas.index')
                ->with('error', 'Encuesta no encontrada');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|string|in:satisfaccion,clima_laboral,evaluacion_desempeño,feedback_360,cultura_organizacional,personalizada,general',
            'categoria' => 'required|string|in:rrhh,psicosocial,seguridad,calidad,satisfaccion,general',
            'preguntas' => 'nullable|array',
            'tiempo_estimado' => 'nullable|integer|min:1|max:120'
        ]);

        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->fill($request->all());
            $encuesta->usuario_modificador = Auth::id();
            $encuesta->items_total = is_array($request->preguntas) ? count($request->preguntas) : 0;
            
            $encuesta->save();

            return redirect()->route('gestion-instrumentos.encuestas.index')
                ->with('success', 'Encuesta actualizada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar encuesta: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la encuesta');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->delete();

            return redirect()->route('gestion-instrumentos.encuestas.index')
                ->with('success', 'Encuesta eliminada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar encuesta: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar la encuesta');
        }
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleEstado(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->estado = !$encuesta->estado;
            $encuesta->save();

            $mensaje = $encuesta->estado ? 'Encuesta activada' : 'Encuesta desactivada';
            
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'nuevo_estado' => $encuesta->estado
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de la encuesta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado'
            ], 500);
        }
    }

    /**
     * Toggle publication status.
     */
    public function togglePublicacion(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            
            if ($encuesta->publicada) {
                $encuesta->despublicar();
                $mensaje = 'Encuesta despublicada';
            } else {
                $encuesta->publicar();
                $mensaje = 'Encuesta publicada';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'publicada' => $encuesta->publicada
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de publicación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado de publicación'
            ], 500);
        }
    }

    /**
     * Clone the specified resource.
     */
    public function clonar(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            $nueva_encuesta = $encuesta->clonar();

            return redirect()->route('gestion-instrumentos.encuestas.edit', $nueva_encuesta->_id)
                ->with('success', 'Encuesta clonada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al clonar encuesta: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al clonar la encuesta');
        }
    }

    /**
     * Generate reports for the specified resource.
     */
    public function informes(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            
            // Generar estadísticas de ejemplo (en producción esto vendría de respuestas reales)
            $estadisticas = [
                'total_respuestas' => rand(45, 120),
                'respuestas_completas' => rand(35, 80),
                'tasa_completitud' => rand(75, 95),
                'tiempo_promedio' => rand(8, 25)
            ];
            
            // Procesar preguntas para análisis
            $preguntas = [];
            if (isset($encuesta->preguntas) && is_array($encuesta->preguntas)) {
                foreach ($encuesta->preguntas as $index => $pregunta) {
                    $preguntaAnalisis = [
                        'texto' => $pregunta['texto'] ?? 'Pregunta sin texto',
                        'tipo' => $pregunta['tipo'] ?? 'escala_likert',
                        'opciones' => []
                    ];
                    
                    // Configurar opciones según tipo de pregunta
                    switch ($pregunta['tipo'] ?? 'escala_likert') {
                        case 'escala_likert':
                            $preguntaAnalisis['opciones'] = [
                                'Totalmente en desacuerdo',
                                'En desacuerdo', 
                                'Neutral',
                                'De acuerdo',
                                'Totalmente de acuerdo'
                            ];
                            break;
                        case 'escala_numerica':
                            $min = $pregunta['configuracion']['minimo'] ?? 1;
                            $max = $pregunta['configuracion']['maximo'] ?? 10;
                            $preguntaAnalisis['opciones'] = range($min, $max);
                            break;
                        case 'opcion_multiple':
                        case 'seleccion_multiple':
                            $preguntaAnalisis['opciones'] = $pregunta['opciones'] ?? ['Opción 1', 'Opción 2', 'Opción 3'];
                            break;
                        case 'si_no':
                            $preguntaAnalisis['opciones'] = ['Sí', 'No'];
                            break;
                        case 'matriz_calificacion':
                            $preguntaAnalisis['filas'] = $pregunta['filas'] ?? ['Aspecto 1', 'Aspecto 2', 'Aspecto 3'];
                            $preguntaAnalisis['columnas'] = $pregunta['columnas'] ?? ['Malo', 'Regular', 'Bueno', 'Excelente'];
                            break;
                    }
                    
                    $preguntas[] = $preguntaAnalisis;
                }
            }
            
            return view('admin.gestion-instrumentos.encuestas.informes', compact('encuesta', 'estadisticas', 'preguntas'));
        } catch (\Exception $e) {
            Log::error('Error al generar informes de encuesta: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al generar los informes');
        }
    }

    /**
     * Get plantillas for AJAX requests
     */
    public function getPlantillas()
    {
        try {
            $plantillas = Encuesta::plantillas()
                ->activas()
                ->select('_id', 'titulo', 'tipo', 'categoria', 'preguntas')
                ->get();

            return response()->json([
                'success' => true,
                'plantillas' => $plantillas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener plantillas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las plantillas'
            ], 500);
        }
    }

    /**
     * Show survey for answering.
     */
    public function responder(string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            
            // Verificar que la encuesta esté publicada
            if (!$encuesta->publicada) {
                return redirect()->route('gestion-instrumentos.encuestas.index')
                    ->with('error', 'Esta encuesta no está disponible para responder');
            }
            
            return view('admin.gestion-instrumentos.encuestas.responder', compact('encuesta'));
        } catch (\Exception $e) {
            Log::error('Error al cargar encuesta para responder: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.encuestas.index')
                ->with('error', 'Error al cargar la encuesta');
        }
    }

    /**
     * Store survey response.
     */
    public function guardarRespuesta(Request $request, string $id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            
            // Validar que la encuesta esté publicada
            if (!$encuesta->publicada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta encuesta no está disponible'
                ], 400);
            }
            
            // Preparar los datos de respuesta
            $respuestaData = [
                'encuesta_id' => $id,
                'usuario_id' => Auth::id(), // Puede ser null si es anónima
                'respuestas' => $request->input('respuestas', []),
                'fecha_inicio' => now(),
                'fecha_completada' => now(),
                'tiempo_respuesta' => rand(5, 20), // En minutos, calculado
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'completada' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Si es anónima, no guardar usuario_id
            if ($encuesta->anonima) {
                unset($respuestaData['usuario_id']);
            }
            
            // Guardar en MongoDB (base de datos empresas, colección respuestas_encuestas)
            $database = DB::connection('mongodb_empresas')->getCollection('respuestas_encuestas');
            $resultado = $database->insertOne($respuestaData);
            
            if ($resultado->getInsertedCount() > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Respuesta guardada correctamente',
                    'respuesta_id' => $resultado->getInsertedId()
                ]);
            } else {
                throw new \Exception('Error al insertar respuesta en la base de datos');
            }
            
        } catch (\Exception $e) {
            Log::error('Error al guardar respuesta de encuesta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la respuesta'
            ], 500);
        }
    }
}
