<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * Controlador centralizado para gestión de notificaciones
 * 
 * Maneja todas las operaciones CRUD y funcionalidades especializadas
 * para el sistema de notificaciones siguiendo el NotificacionSchema de Node.js
 */
class NotificacionController extends Controller
{
    /**
     * Listado de notificaciones con filtros y paginación
     * 
     * @param Request $request
     * @return JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Notificacion::query();

            // Filtros de búsqueda
            if ($request->filled('empresa_id')) {
                $query->porEmpresa($request->empresa_id);
            }

            if ($request->filled('modulo')) {
                $query->porModulo($request->modulo);
            }

            if ($request->filled('vista')) {
                if ($request->boolean('vista')) {
                    $query->vistas();
                } else {
                    $query->noVistas();
                }
            }

            if ($request->filled('canal')) {
                $query->conCanal($request->canal);
            }

            if ($request->filled('titulo')) {
                $query->where('titulo', 'like', '%' . $request->titulo . '%');
            }

            if ($request->filled('fecha_desde')) {
                $query->where('created_at', '>=', Carbon::parse($request->fecha_desde));
            }

            if ($request->filled('fecha_hasta')) {
                $query->where('created_at', '<=', Carbon::parse($request->fecha_hasta));
            }

            // Incluir información de la empresa
            $query->with('empresa:_id,nombre');

            // Ordenamiento
            $orden = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($orden, $direccion);

            // Paginación
            $porPagina = $request->get('por_pagina', 15);
            $notificaciones = $query->paginate($porPagina);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $notificaciones->items(),
                    'pagination' => [
                        'current_page' => $notificaciones->currentPage(),
                        'last_page' => $notificaciones->lastPage(),
                        'per_page' => $notificaciones->perPage(),
                        'total' => $notificaciones->total()
                    ]
                ]);
            }

            return view('auth.notificaciones.index', compact('notificaciones'));

        } catch (\Exception $e) {
            Log::error('Error al listar notificaciones', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar las notificaciones',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al cargar las notificaciones');
        }
    }

    /**
     * Crear nueva notificación
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'empresa_id' => 'nullable|string',
                'titulo' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'modulo' => 'nullable|string|in:' . implode(',', Notificacion::getModulosValidos()),
                'canales' => 'nullable|array',
                'vista' => 'nullable|boolean',
                'link' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notificacion = Notificacion::create($request->only([
                'empresa_id', 'titulo', 'descripcion', 'modulo', 'canales', 'vista', 'link'
            ]));

            Log::info('Notificación creada exitosamente', [
                'notificacion_id' => $notificacion->id,
                'empresa_id' => $notificacion->empresa_id,
                'modulo' => $notificacion->modulo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificación creada exitosamente',
                'data' => $notificacion->load('empresa:_id,nombre')
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear notificación', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la notificación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar información de una notificación específica
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $notificacion = Notificacion::with('empresa:_id,nombre')->find($id);

            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $notificacion,
                'info_notificacion' => $notificacion->getInfoNotificacion()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar notificación', [
                'notificacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la notificación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formulario de edición/creación
     * 
     * @param string|null $id
     * @return \Illuminate\View\View|JsonResponse
     */
    public function edit($id = null)
    {
        try {
            $notificacion = null;
            if ($id) {
                $notificacion = Notificacion::find($id);
                if (!$notificacion) {
                    return back()->with('error', 'Notificación no encontrada');
                }
            }

            $modulos = Notificacion::getModulosValidos();

            return view('auth.notificaciones.form', compact('notificacion', 'modulos'));

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de notificación', [
                'notificacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Actualizar notificación
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $notificacion = Notificacion::find($id);

            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'empresa_id' => 'nullable|string',
                'titulo' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'modulo' => 'nullable|string|in:' . implode(',', Notificacion::getModulosValidos()),
                'canales' => 'nullable|array',
                'vista' => 'nullable|boolean',
                'link' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datosActualizacion = $request->only([
                'empresa_id', 'titulo', 'descripcion', 'modulo', 'canales', 'vista', 'link'
            ]);

            $notificacion->update($datosActualizacion);

            return response()->json([
                'success' => true,
                'message' => 'Notificación actualizada exitosamente',
                'data' => $notificacion->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar notificación', [
                'notificacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la notificación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar notificación
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $notificacion = Notificacion::find($id);

            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }

            $notificacion->delete();

            Log::info('Notificación eliminada', ['notificacion_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Notificación eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar notificación', [
                'notificacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la notificación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar notificación como vista
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function marcarVista($id)
    {
        try {
            $notificacion = Notificacion::find($id);

            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }

            $resultado = $notificacion->marcarComoVista();

            return response()->json([
                'success' => $resultado,
                'message' => $resultado ? 'Notificación marcada como vista' : 'Error al marcar como vista',
                'data' => $notificacion->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al marcar notificación como vista', [
                'notificacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar la notificación como vista',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar notificación como no vista
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function marcarNoVista($id)
    {
        try {
            $notificacion = Notificacion::find($id);

            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }

            $resultado = $notificacion->marcarComoNoVista();

            return response()->json([
                'success' => $resultado,
                'message' => $resultado ? 'Notificación marcada como no vista' : 'Error al marcar como no vista',
                'data' => $notificacion->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al marcar notificación como no vista', [
                'notificacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar la notificación como no vista',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notificaciones por empresa
     * 
     * @param Request $request
     * @param string $empresa_id
     * @return JsonResponse
     */
    public function porEmpresa(Request $request, $empresa_id)
    {
        try {
            $query = Notificacion::porEmpresa($empresa_id);

            if ($request->filled('no_vistas_solamente')) {
                $query->noVistas();
            }

            if ($request->filled('modulo')) {
                $query->porModulo($request->modulo);
            }

            $notificaciones = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $notificaciones,
                'total' => $notificaciones->count(),
                'no_vistas' => $notificaciones->where('vista', false)->count(),
                'vistas' => $notificaciones->where('vista', true)->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener notificaciones por empresa', [
                'empresa_id' => $empresa_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las notificaciones de la empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notificaciones por módulo
     * 
     * @param Request $request
     * @param string $modulo
     * @return JsonResponse
     */
    public function porModulo(Request $request, $modulo)
    {
        try {
            if (!Notificacion::esModuloValido($modulo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo no válido',
                    'modulos_validos' => Notificacion::getModulosValidos()
                ], 400);
            }

            $query = Notificacion::porModulo($modulo);

            if ($request->filled('empresa_id')) {
                $query->porEmpresa($request->empresa_id);
            }

            $notificaciones = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $notificaciones,
                'modulo' => $modulo,
                'total' => $notificaciones->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener notificaciones por módulo', [
                'modulo' => $modulo,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las notificaciones del módulo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar todas las notificaciones como vistas para una empresa
     * 
     * @param string $empresa_id
     * @return JsonResponse
     */
    public function marcarTodasVistasEmpresa($empresa_id)
    {
        try {
            $notificacionesNoVistas = Notificacion::porEmpresa($empresa_id)->noVistas()->get();
            $marcadas = 0;

            foreach ($notificacionesNoVistas as $notificacion) {
                if ($notificacion->marcarComoVista()) {
                    $marcadas++;
                }
            }

            Log::info('Notificaciones marcadas como vistas masivamente', [
                'empresa_id' => $empresa_id,
                'total_marcadas' => $marcadas
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se marcaron {$marcadas} notificaciones como vistas",
                'notificaciones_marcadas' => $marcadas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al marcar notificaciones como vistas', [
                'empresa_id' => $empresa_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar las notificaciones como vistas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear notificación de modificación no autorizada
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function crearModificacionNoAutorizada(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'empresa_id' => 'required|string',
                'titulo' => 'nullable|string',
                'descripcion' => 'nullable|string',
                'modulo' => 'nullable|string|in:' . implode(',', Notificacion::getModulosValidos()),
                'canales' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notificacion = Notificacion::crearModificacionNoAutorizada($request->all());

            Log::warning('Notificación de modificación no autorizada creada', [
                'notificacion_id' => $notificacion->id,
                'empresa_id' => $notificacion->empresa_id,
                'modulo' => $notificacion->modulo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificación de modificación no autorizada creada',
                'data' => $notificacion
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear notificación de modificación no autorizada', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la notificación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas de notificaciones
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function estadisticas(Request $request)
    {
        try {
            $fechaDesde = $request->get('fecha_desde', now()->subDays(30));
            $fechaHasta = $request->get('fecha_hasta', now());

            $stats = [
                'total_notificaciones' => Notificacion::whereBetween('created_at', [$fechaDesde, $fechaHasta])->count(),
                'notificaciones_vistas' => Notificacion::vistas()->whereBetween('created_at', [$fechaDesde, $fechaHasta])->count(),
                'notificaciones_no_vistas' => Notificacion::noVistas()->whereBetween('created_at', [$fechaDesde, $fechaHasta])->count(),
                'por_modulo' => [],
                'empresas_con_notificaciones' => Notificacion::whereNotNull('empresa_id')
                    ->whereBetween('created_at', [$fechaDesde, $fechaHasta])
                    ->distinct('empresa_id')->count()
            ];

            // Estadísticas por módulo
            foreach (Notificacion::getModulosValidos() as $modulo) {
                $stats['por_modulo'][$modulo] = Notificacion::porModulo($modulo)
                    ->whereBetween('created_at', [$fechaDesde, $fechaHasta])
                    ->count();
            }

            return response()->json([
                'success' => true,
                'data' => $stats,
                'periodo' => [
                    'desde' => $fechaDesde,
                    'hasta' => $fechaHasta
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar estadísticas de notificaciones', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener opciones para formularios
     * 
     * @return JsonResponse
     */
    public function opciones()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'modulos' => Notificacion::getModulosValidos(),
                    'canales_disponibles' => [
                        'sistema', 'email', 'sms', 'push', 'webhook'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener opciones de notificaciones', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las opciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
