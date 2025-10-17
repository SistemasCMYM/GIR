<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consentimiento;
use App\Models\RespuestaConsentimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ConsentimientoController extends Controller
{
    /**
     * Buscar un consentimiento por ID de forma segura
     */
    private function findConsentimiento($id)
    {
        try {
            // Intentar buscar por _id (MongoDB ObjectId)
            $consentimiento = Consentimiento::where('_id', $id)->first();
            
            if (!$consentimiento) {
                // Intentar buscar por id personalizado
                $consentimiento = Consentimiento::where('id', $id)->first();
            }
            
            if (!$consentimiento) {
                throw new \Exception('Consentimiento no encontrado');
            }
            
            return $consentimiento;
        } catch (\Exception $e) {
            Log::error('Error al buscar consentimiento con ID: ' . $id . ' - ' . $e->getMessage());
            throw new \Exception('No se pudo encontrar el consentimiento solicitado.');
        }
    }

    /**
     * Mostrar la lista de consentimientos
     */
    public function index()
    {
        try {
            $consentimientos = Consentimiento::orderBy('fecha_creacion', 'desc')->paginate(15);
            Log::info('Consentimientos cargados en index: ' . $consentimientos->count());
            
            return view('admin.gestion-instrumentos.consentimientos.index', compact('consentimientos'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar consentimientos: ' . $e->getMessage());
            
            return view('admin.gestion-instrumentos.consentimientos.index')
                ->with('error', 'Error al cargar los consentimientos: ' . $e->getMessage())
                ->with('consentimientos', new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15));
        }
    }

    /**
     * Mostrar el formulario para crear un nuevo consentimiento
     */
    public function create()
    {
        $tipos = [
            'general' => 'General',
            'datos_personales' => 'Datos Personales',
            'evaluacion_psicosocial' => 'Evaluación Psicosocial',
            'tratamiento_datos' => 'Tratamiento de Datos',
            'investigacion' => 'Investigación',
            'personalizado' => 'Personalizado'
        ];

        $plantilla_ejemplo = [
            'titulo' => 'CONSENTIMIENTO INFORMADO PARA EVALUACIÓN DE RIESGO PSICOSOCIAL',
            'introduccion' => 'Por medio del presente documento manifiesto que he sido informado(a) de manera clara y precisa sobre la evaluación de factores de riesgo psicosocial que se realizará en mi lugar de trabajo.',
            'items' => [
                'He sido informado(a) sobre los objetivos de la evaluación de riesgo psicosocial.',
                'Entiendo que mi participación es completamente voluntaria y confidencial.',
                'Sé que puedo retirarme de la evaluación en cualquier momento sin consecuencias.',
                'He sido informado(a) sobre la confidencialidad absoluta de mis respuestas.',
                'Autorizo el tratamiento de mis datos personales conforme a la ley de protección de datos.'
            ]
        ];

        return view('admin.gestion-instrumentos.consentimientos.create', compact('tipos', 'plantilla_ejemplo'));
    }

    /**
     * Almacenar un nuevo consentimiento
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'tipo' => 'required|string',
                'contenido' => 'required|string',
                'items' => 'nullable|array',
                'items.*' => 'string'
            ]);

            // Preparar datos del consentimiento
            $datos = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'contenido' => $request->contenido,
                'estado' => true,
                'items_total' => is_array($request->items) ? count($request->items) : 0,
                'usuario_creador' => Auth::id() ?? 1,
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
                'version' => '1.0'
            ];

            // Configuración del consentimiento
            $configuracion = [
                'requiere_firma' => $request->boolean('requiere_firma', true),
                'requiere_fecha' => $request->boolean('requiere_fecha', true)
            ];

            if ($request->has('items') && is_array($request->items)) {
                $items = array_filter($request->items, function($item) {
                    return !empty(trim($item));
                });
                $configuracion['items'] = array_values($items);
                $datos['items_total'] = count($items);
            }

            $datos['configuracion'] = $configuracion;

            // Crear y guardar el consentimiento
            $consentimiento = new Consentimiento();
            $consentimiento->fill($datos);
            
            $guardado = $consentimiento->save();

            if (!$guardado) {
                throw new \Exception('No se pudo guardar el consentimiento en la base de datos');
            }

            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('success', 'Consentimiento creado exitosamente con ID: ' . $consentimiento->_id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el consentimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar un consentimiento específico
     */
    public function show($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            // Cargar estadísticas
            $estadisticas = [
                'total_respuestas' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->count(),
                'aceptaron' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->where('acepta', true)->count(),
                'rechazaron' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->where('acepta', false)->count(),
                'firmados' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->whereNotNull('firma_digital')->count(),
                'porcentaje_aceptacion' => 0,
                'ultima_respuesta' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->orderBy('fecha_diligenciamiento', 'desc')->first()
            ];

            if ($estadisticas['total_respuestas'] > 0) {
                $estadisticas['porcentaje_aceptacion'] = round(($estadisticas['aceptaron'] / $estadisticas['total_respuestas']) * 100, 1);
            }

            return view('admin.gestion-instrumentos.consentimientos.show', compact('consentimiento', 'estadisticas'));
            
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Mostrar el formulario para editar un consentimiento
     */
    public function edit($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            $tipos = [
                'general' => 'General',
                'datos_personales' => 'Datos Personales',
                'evaluacion_psicosocial' => 'Evaluación Psicosocial',
                'tratamiento_datos' => 'Tratamiento de Datos',
                'investigacion' => 'Investigación',
                'personalizado' => 'Personalizado'
            ];

            return view('admin.gestion-instrumentos.consentimientos.edit', compact('consentimiento', 'tipos'));
            
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Actualizar un consentimiento
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'tipo' => 'required|string',
                'contenido' => 'required|string',
                'items' => 'nullable|array',
                'items.*' => 'string'
            ]);

            $consentimiento = $this->findConsentimiento($id);
            
            $consentimiento->titulo = $request->titulo;
            $consentimiento->descripcion = $request->descripcion;
            $consentimiento->tipo = $request->tipo;
            $consentimiento->contenido = $request->contenido;
            $consentimiento->items_total = is_array($request->items) ? count($request->items) : 0;
            $consentimiento->usuario_modificador = Auth::id() ?? 1;
            
            if ($request->has('items')) {
                $configuracion = $consentimiento->configuracion ?? [];
                $configuracion['items'] = $request->items;
                $configuracion['requiere_firma'] = $request->boolean('requiere_firma', true);
                $configuracion['requiere_fecha'] = $request->boolean('requiere_fecha', true);
                $consentimiento->configuracion = $configuracion;
            }

            $consentimiento->save();

            return redirect()->route('gestion-instrumentos.consentimientos.show', $id)
                ->with('success', 'Consentimiento actualizado exitosamente');
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el consentimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar un consentimiento
     */
    public function destroy($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            // Verificar si tiene respuestas asociadas
            $respuestas = RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->count();
            
            if ($respuestas > 0) {
                return redirect()->route('gestion-instrumentos.consentimientos.index')
                    ->with('error', 'No se puede eliminar el consentimiento porque tiene respuestas asociadas');
            }

            $consentimiento->delete();

            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('success', 'Consentimiento eliminado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Alternar el estado de un consentimiento
     */
    public function toggleEstado($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            $consentimiento->estado = !$consentimiento->estado;
            $consentimiento->save();

            $mensaje = $consentimiento->estado ? 'Consentimiento activado' : 'Consentimiento desactivado';

            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Mostrar el consentimiento para diligenciar
     */
    public function diligenciar($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            if (!$consentimiento->estado) {
                return redirect()->route('gestion-instrumentos.consentimientos.index')
                    ->with('error', 'Este consentimiento no está activo para diligenciar');
            }

            return view('admin.gestion-instrumentos.consentimientos.diligenciar', compact('consentimiento'));
            
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Procesar el consentimiento diligenciado
     */
    public function procesarConsentimiento(Request $request, $id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            $request->validate([
                'acepta' => 'required|boolean',
                'firma_digital' => 'nullable|string',
                'firma_imagen' => 'nullable|image|max:2048',
                'observaciones' => 'nullable|string|max:1000'
            ]);

            $respuesta = new RespuestaConsentimiento();
            $respuesta->consentimiento_id = $consentimiento->_id;
            $respuesta->usuario_id = Auth::id();
            $respuesta->acepta = $request->boolean('acepta');
            $respuesta->fecha_diligenciamiento = now();
            $respuesta->ip_address = $request->ip();
            $respuesta->user_agent = $request->userAgent();
            $respuesta->observaciones = $request->observaciones;

            // Manejar firma digital
            if ($request->filled('firma_digital')) {
                $respuesta->firma_digital = $request->firma_digital;
            }

            // Manejar imagen de firma
            if ($request->hasFile('firma_imagen')) {
                $path = $request->file('firma_imagen')->store('firmas', 'public');
                $respuesta->firma_imagen = $path;
            }

            $respuesta->save();

            $mensaje = $request->boolean('acepta') ? 
                'Consentimiento aceptado y registrado exitosamente' : 
                'Su respuesta ha sido registrada exitosamente';

            return redirect()->route('gestion-instrumentos.consentimientos.show', $id)
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            Log::error('Error al procesar consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al procesar el consentimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar informes del consentimiento
     */
    public function informes($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            $respuestas = RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)
                ->with(['usuario', 'empleado'])
                ->orderBy('fecha_diligenciamiento', 'desc')
                ->paginate(20);

            $estadisticas = [
                'total_respuestas' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->count(),
                'aceptaron' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->where('acepta', true)->count(),
                'rechazaron' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->where('acepta', false)->count(),
                'firmados' => RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)->whereNotNull('firma_digital')->count(),
                'porcentaje_aceptacion' => 0
            ];

            if ($estadisticas['total_respuestas'] > 0) {
                $estadisticas['porcentaje_aceptacion'] = round(($estadisticas['aceptaron'] / $estadisticas['total_respuestas']) * 100, 1);
            }

            return view('admin.gestion-instrumentos.consentimientos.informes', compact('consentimiento', 'respuestas', 'estadisticas'));
            
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Exportar respuestas del consentimiento
     */
    public function exportarRespuestas($id)
    {
        try {
            $consentimiento = $this->findConsentimiento($id);
            
            $respuestas = RespuestaConsentimiento::where('consentimiento_id', $consentimiento->_id)
                ->with(['usuario', 'empleado'])
                ->get();

            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="respuestas_consentimiento_' . $consentimiento->_id . '.csv"'
            ];

            $callback = function() use ($respuestas) {
                $file = fopen('php://output', 'w');
                
                // Encabezados
                fputcsv($file, [
                    'ID Respuesta',
                    'Usuario',
                    'Empleado',
                    'Acepta',
                    'Fecha Diligenciamiento',
                    'Tiene Firma',
                    'IP Address',
                    'Observaciones'
                ]);

                // Datos
                foreach ($respuestas as $respuesta) {
                    fputcsv($file, [
                        $respuesta->_id,
                        $respuesta->usuario->name ?? 'N/A',
                        $respuesta->empleado->nombre ?? 'N/A',
                        $respuesta->acepta ? 'Sí' : 'No',
                        $respuesta->fecha_diligenciamiento->format('d/m/Y H:i:s'),
                        ($respuesta->firma_digital || $respuesta->firma_imagen) ? 'Sí' : 'No',
                        $respuesta->ip_address,
                        $respuesta->observaciones
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', $e->getMessage());
        }
    }
}
