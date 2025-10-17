<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EncuestaController extends Controller
{
    public function index()
    {
        try {
            $empresaId = session('empresa_id');
            
            $encuestas = DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->where('empresa_id', $empresaId)
                ->where('_esBorrado', false)
                ->orderBy('_fechaCreado', 'desc')
                ->get()
                ->map(function($item) {
                    return is_array($item) ? $item : $item->toArray();
                });

            $estadisticas = [
                'total' => $encuestas->count(),
                'activas' => $encuestas->where('estado', 'activa')->count(),
                'borradores' => $encuestas->where('estado', 'borrador')->count(),
                'finalizadas' => $encuestas->where('estado', 'finalizada')->count()
            ];

            return view('admin.gestion-instrumentos.encuestas.index', compact('encuestas', 'estadisticas'));
        } catch (\Exception $e) {
            Log::error('Error en EncuestaController@index: ' . $e->getMessage());
            return view('admin.gestion-instrumentos.encuestas.index', [
                'encuestas' => collect([]),
                'estadisticas' => [
                    'total' => 0,
                    'activas' => 0,
                    'borradores' => 0,
                    'finalizadas' => 0
                ]
            ]);
        }
    }

    public function create()
    {
        return view('admin.gestion-instrumentos.encuestas.create');
    }

    public function store(Request $request)
    {
        try {
            $empresaId = session('empresa_id');
            $userId = session('user_data.id');

            $request->validate([
                'nombre' => 'required|string|max:255',
                'template_tipo' => 'required|string'
            ]);

            // Procesar preguntas
            $preguntas = [];
            if ($request->has('preguntas')) {
                foreach ($request->preguntas as $index => $preguntaData) {
                    $pregunta = [
                        'id' => Str::random(22),
                        'orden' => $index + 1,
                        'texto' => $preguntaData['texto'] ?? '',
                        'tipo' => $preguntaData['tipo'] ?? 'escala_likert',
                        'obligatoria' => isset($preguntaData['obligatoria']) && $preguntaData['obligatoria'] == '1',
                        'opciones' => []
                    ];

                    // Procesar según tipo de pregunta
                    switch ($pregunta['tipo']) {
                        case 'opcion_multiple':
                        case 'seleccion_multiple':
                            $pregunta['opciones'] = array_filter($preguntaData['opciones'] ?? []);
                            break;

                        case 'escala_numerica':
                            $pregunta['min'] = $preguntaData['min'] ?? 1;
                            $pregunta['max'] = $preguntaData['max'] ?? 10;
                            $pregunta['etiqueta_min'] = $preguntaData['etiqueta_min'] ?? '';
                            $pregunta['etiqueta_max'] = $preguntaData['etiqueta_max'] ?? '';
                            break;

                        case 'matriz_calificacion':
                            $pregunta['filas'] = array_filter($preguntaData['filas'] ?? []);
                            $pregunta['columnas'] = array_filter($preguntaData['columnas'] ?? []);
                            break;

                        case 'respuesta_abierta':
                            $pregunta['min_length'] = $preguntaData['min_length'] ?? 10;
                            $pregunta['max_length'] = $preguntaData['max_length'] ?? 500;
                            $pregunta['placeholder'] = $preguntaData['placeholder'] ?? '';
                            break;
                    }

                    $preguntas[] = $pregunta;
                }
            }

            $encuestaId = Str::random(22);
            $now = now()->format('Y-m-d H:i:s');

            $encuesta = [
                'id' => $encuestaId,
                'empresa_id' => $empresaId,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion ?? '',
                'template_tipo' => $request->template_tipo,
                'estado' => $request->estado ?? 'borrador',
                'anonima' => isset($request->anonima) && $request->anonima == '1',
                'fecha_inicio' => $request->fecha_inicio ?? null,
                'fecha_cierre' => $request->fecha_cierre ?? null,
                'preguntas' => $preguntas,
                'total_preguntas' => count($preguntas),
                'total_respuestas' => 0,
                '_slug' => Str::slug($request->nombre) . '-' . substr($encuestaId, 0, 8),
                '_tags' => [$request->template_tipo, $request->estado ?? 'borrador'],
                '_esPublico' => false,
                '_esBorrado' => false,
                '_creadoPor' => $userId,
                '_fechaCreado' => $now,
                '_modificadoPor' => $userId,
                '_fechaModificado' => $now
            ];

            DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->insert($encuesta);

            return response()->json([
                'success' => true,
                'message' => 'Encuesta creada exitosamente',
                'encuesta_id' => $encuestaId
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear encuesta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la encuesta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $empresaId = session('empresa_id');

            $encuesta = DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->where('id', $id)
                ->where('empresa_id', $empresaId)
                ->where('_esBorrado', false)
                ->first();

            if (!$encuesta) {
                abort(404, 'Encuesta no encontrada');
            }

            $encuesta = is_array($encuesta) ? $encuesta : $encuesta->toArray();

            // Obtener respuestas
            $respuestas = DB::connection('mongodb_empresas')
                ->collection('respuestas_encuestas')
                ->where('encuesta_id', $id)
                ->get()
                ->map(function($item) {
                    return is_array($item) ? $item : $item->toArray();
                });

            return view('admin.gestion-instrumentos.encuestas.show', compact('encuesta', 'respuestas'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar encuesta: ' . $e->getMessage());
            abort(500, 'Error al cargar la encuesta');
        }
    }

    public function edit($id)
    {
        try {
            $empresaId = session('empresa_id');

            $encuesta = DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->where('id', $id)
                ->where('empresa_id', $empresaId)
                ->where('_esBorrado', false)
                ->first();

            if (!$encuesta) {
                abort(404, 'Encuesta no encontrada');
            }

            $encuesta = is_array($encuesta) ? $encuesta : $encuesta->toArray();

            return view('admin.gestion-instrumentos.encuestas.edit', compact('encuesta'));
        } catch (\Exception $e) {
            Log::error('Error al editar encuesta: ' . $e->getMessage());
            abort(500, 'Error al cargar la encuesta para edición');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $empresaId = session('empresa_id');
            $userId = session('user_data.id');

            $request->validate([
                'nombre' => 'required|string|max:255'
            ]);

            // Procesar preguntas (igual que en store)
            $preguntas = [];
            if ($request->has('preguntas')) {
                foreach ($request->preguntas as $index => $preguntaData) {
                    $pregunta = [
                        'id' => $preguntaData['id'] ?? Str::random(22),
                        'orden' => $index + 1,
                        'texto' => $preguntaData['texto'] ?? '',
                        'tipo' => $preguntaData['tipo'] ?? 'escala_likert',
                        'obligatoria' => isset($preguntaData['obligatoria']) && $preguntaData['obligatoria'] == '1',
                        'opciones' => []
                    ];

                    switch ($pregunta['tipo']) {
                        case 'opcion_multiple':
                        case 'seleccion_multiple':
                            $pregunta['opciones'] = array_filter($preguntaData['opciones'] ?? []);
                            break;

                        case 'escala_numerica':
                            $pregunta['min'] = $preguntaData['min'] ?? 1;
                            $pregunta['max'] = $preguntaData['max'] ?? 10;
                            $pregunta['etiqueta_min'] = $preguntaData['etiqueta_min'] ?? '';
                            $pregunta['etiqueta_max'] = $preguntaData['etiqueta_max'] ?? '';
                            break;

                        case 'matriz_calificacion':
                            $pregunta['filas'] = array_filter($preguntaData['filas'] ?? []);
                            $pregunta['columnas'] = array_filter($preguntaData['columnas'] ?? []);
                            break;

                        case 'respuesta_abierta':
                            $pregunta['min_length'] = $preguntaData['min_length'] ?? 10;
                            $pregunta['max_length'] = $preguntaData['max_length'] ?? 500;
                            $pregunta['placeholder'] = $preguntaData['placeholder'] ?? '';
                            break;
                    }

                    $preguntas[] = $pregunta;
                }
            }

            $updateData = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion ?? '',
                'estado' => $request->estado ?? 'borrador',
                'anonima' => isset($request->anonima) && $request->anonima == '1',
                'fecha_inicio' => $request->fecha_inicio ?? null,
                'fecha_cierre' => $request->fecha_cierre ?? null,
                'preguntas' => $preguntas,
                'total_preguntas' => count($preguntas),
                '_modificadoPor' => $userId,
                '_fechaModificado' => now()->format('Y-m-d H:i:s')
            ];

            DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->where('id', $id)
                ->where('empresa_id', $empresaId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Encuesta actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar encuesta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la encuesta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $empresaId = session('empresa_id');
            $userId = session('user_data.id');

            DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->where('id', $id)
                ->where('empresa_id', $empresaId)
                ->update([
                    '_esBorrado' => true,
                    '_borradoPor' => $userId,
                    '_fechaBorrado' => now()->format('Y-m-d H:i:s')
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Encuesta eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar encuesta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la encuesta'
            ], 500);
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        try {
            $empresaId = session('empresa_id');
            $userId = session('user_data.id');

            $request->validate([
                'estado' => 'required|in:borrador,activa,finalizada,pausada'
            ]);

            DB::connection('mongodb_empresas')
                ->collection('encuestas')
                ->where('id', $id)
                ->where('empresa_id', $empresaId)
                ->update([
                    'estado' => $request->estado,
                    '_modificadoPor' => $userId,
                    '_fechaModificado' => now()->format('Y-m-d H:i:s')
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado'
            ], 500);
        }
    }
}
