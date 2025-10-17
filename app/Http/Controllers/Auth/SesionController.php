<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Sesion;
use App\Models\Auth\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Controlador centralizado para gestión de sesiones
 * 
 * Maneja todas las operaciones CRUD y funcionalidades especializadas
 * para el sistema de sesiones siguiendo el SesionSchema de Node.js
 */
class SesionController extends Controller
{
    /**
     * Listado de sesiones con filtros y paginación
     * 
     * @param Request $request
     * @return JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Sesion::query();

            // Filtros de búsqueda
            if ($request->filled('usuario_id')) {
                $query->where('usuario_id', $request->usuario_id);
            }

            if ($request->filled('activa')) {
                $query->where('activa', $request->boolean('activa'));
            }

            if ($request->filled('ip_origen')) {
                $query->where('ip_origen', 'like', '%' . $request->ip_origen . '%');
            }

            if ($request->filled('fecha_desde')) {
                $query->where('inicio_sesion', '>=', Carbon::parse($request->fecha_desde));
            }

            if ($request->filled('fecha_hasta')) {
                $query->where('inicio_sesion', '<=', Carbon::parse($request->fecha_hasta));
            }

            // Incluir información del usuario
            $query->with('usuario:_id,nombre,email');

            // Ordenamiento
            $orden = $request->get('orden', 'inicio_sesion');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($orden, $direccion);

            // Paginación
            $porPagina = $request->get('por_pagina', 15);
            $sesiones = $query->paginate($porPagina);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $sesiones->items(),
                    'pagination' => [
                        'current_page' => $sesiones->currentPage(),
                        'last_page' => $sesiones->lastPage(),
                        'per_page' => $sesiones->perPage(),
                        'total' => $sesiones->total()
                    ]
                ]);
            }

            return view('auth.sesiones.index', compact('sesiones'));

        } catch (\Exception $e) {
            Log::error('Error al listar sesiones', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar las sesiones',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al cargar las sesiones');
        }
    }

    /**
     * Crear nueva sesión
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_id' => 'required|string',
                'token_sesion' => 'required|string|unique:sesiones,token_sesion',
                'ip_origen' => 'nullable|string',
                'user_agent' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que el usuario existe
            $usuario = Usuario::find($request->usuario_id);
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $sesion = Sesion::create([
                'usuario_id' => $request->usuario_id,
                'token_sesion' => $request->token_sesion,
                'inicio_sesion' => now(),
                'ultima_actividad' => now(),
                'ip_origen' => $request->ip_origen,
                'user_agent' => $request->user_agent,
                'activa' => true
            ]);

            Log::info('Sesión creada exitosamente', [
                'sesion_id' => $sesion->id,
                'usuario_id' => $sesion->usuario_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesión creada exitosamente',
                'data' => $sesion->load('usuario:_id,nombre,email')
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear sesión', [
                'error' => $e->getMessage(),
                'request' => $request->except(['token_sesion'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar información de una sesión específica
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $sesion = Sesion::with('usuario:_id,nombre,email')->find($id);

            if (!$sesion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $sesion,
                'info_sesion' => $sesion->getInfoSesion()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar sesión', [
                'sesion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar sesión
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $sesion = Sesion::find($id);

            if (!$sesion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'ip_origen' => 'nullable|string',
                'user_agent' => 'nullable|string',
                'activa' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datosActualizacion = $request->only(['ip_origen', 'user_agent', 'activa']);
            
            // Si se está desactivando la sesión, establecer fin_sesion
            if (isset($datosActualizacion['activa']) && !$datosActualizacion['activa']) {
                $datosActualizacion['fin_sesion'] = now();
            }

            $sesion->update($datosActualizacion);

            return response()->json([
                'success' => true,
                'message' => 'Sesión actualizada exitosamente',
                'data' => $sesion->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar sesión', [
                'sesion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar sesión (cerrar definitivamente)
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $sesion = Sesion::find($id);

            if (!$sesion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión no encontrada'
                ], 404);
            }

            // Cerrar sesión antes de eliminar
            $sesion->cerrarSesion();
            $sesion->delete();

            Log::info('Sesión eliminada', ['sesion_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Sesión eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar sesión', [
                'sesion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión específica
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function cerrar($id)
    {
        try {
            $sesion = Sesion::find($id);

            if (!$sesion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión no encontrada'
                ], 404);
            }

            $resultado = $sesion->cerrarSesion();

            return response()->json([
                'success' => $resultado,
                'message' => $resultado ? 'Sesión cerrada exitosamente' : 'Error al cerrar la sesión',
                'data' => $sesion->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cerrar sesión', [
                'sesion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sesiones por usuario
     * 
     * @param Request $request
     * @param string $usuario_id
     * @return JsonResponse
     */
    public function porUsuario(Request $request, $usuario_id)
    {
        try {
            $query = Sesion::porUsuario($usuario_id);

            if ($request->filled('activas_solamente')) {
                $query->activas();
            }

            $sesiones = $query->orderBy('inicio_sesion', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $sesiones,
                'total' => $sesiones->count(),
                'activas' => $sesiones->where('activa', true)->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener sesiones por usuario', [
                'usuario_id' => $usuario_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las sesiones del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar actividad de sesión
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function actualizarActividad($id)
    {
        try {
            $sesion = Sesion::find($id);

            if (!$sesion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión no encontrada'
                ], 404);
            }

            $resultado = $sesion->actualizarActividad();

            return response()->json([
                'success' => $resultado,
                'message' => $resultado ? 'Actividad actualizada' : 'No se pudo actualizar la actividad',
                'data' => $sesion->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar actividad', [
                'sesion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar token de sesión
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verificarToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token_sesion' => 'required|string',
                'usuario_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Sesion::where('token_sesion', $request->token_sesion);
            
            if ($request->filled('usuario_id')) {
                $query->where('usuario_id', $request->usuario_id);
            }

            $sesion = $query->first();

            if (!$sesion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de sesión no válido',
                    'valido' => false
                ], 404);
            }

            $valido = $sesion->verificarToken($request->token_sesion);

            return response()->json([
                'success' => true,
                'valido' => $valido,
                'message' => $valido ? 'Token válido' : 'Token expirado o inactivo',
                'data' => $valido ? $sesion->getInfoSesion() : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar token', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar todas las sesiones de un usuario
     * 
     * @param string $usuario_id
     * @return JsonResponse
     */
    public function cerrarTodasUsuario($usuario_id)
    {
        try {
            $sesionesActivas = Sesion::porUsuario($usuario_id)->activas()->get();
            $cerradas = 0;

            foreach ($sesionesActivas as $sesion) {
                if ($sesion->cerrarSesion()) {
                    $cerradas++;
                }
            }

            Log::info('Sesiones cerradas masivamente', [
                'usuario_id' => $usuario_id,
                'total_cerradas' => $cerradas
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se cerraron {$cerradas} sesiones activas",
                'sesiones_cerradas' => $cerradas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cerrar sesiones del usuario', [
                'usuario_id' => $usuario_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar las sesiones del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpiar sesiones expiradas
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function limpiarExpiradas(Request $request)
    {
        try {
            $horasInactividad = $request->get('horas_inactividad', 2);
            $sesionesExpiradas = Sesion::expiradas($horasInactividad)->get();
            $eliminadas = 0;

            foreach ($sesionesExpiradas as $sesion) {
                if ($sesion->activa) {
                    $sesion->cerrarSesion();
                }
                $eliminadas++;
            }

            Log::info('Limpieza de sesiones expiradas', [
                'horas_inactividad' => $horasInactividad,
                'sesiones_procesadas' => $eliminadas
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se procesaron {$eliminadas} sesiones expiradas",
                'sesiones_procesadas' => $eliminadas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al limpiar sesiones expiradas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar sesiones expiradas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas de sesiones
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
                'total_sesiones' => Sesion::whereBetween('inicio_sesion', [$fechaDesde, $fechaHasta])->count(),
                'sesiones_activas' => Sesion::activas()->count(),
                'sesiones_expiradas' => Sesion::expiradas()->count(),
                'usuarios_unicos' => Sesion::whereBetween('inicio_sesion', [$fechaDesde, $fechaHasta])
                    ->distinct('usuario_id')->count(),
                'duracion_promedio_minutos' => Sesion::whereNotNull('fin_sesion')
                    ->whereBetween('inicio_sesion', [$fechaDesde, $fechaHasta])
                    ->get()
                    ->avg(function ($sesion) {
                        return $sesion->inicio_sesion->diffInMinutes($sesion->fin_sesion);
                    }),
                'ips_unicas' => Sesion::whereNotNull('ip_origen')
                    ->whereBetween('inicio_sesion', [$fechaDesde, $fechaHasta])
                    ->distinct('ip_origen')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'periodo' => [
                    'desde' => $fechaDesde,
                    'hasta' => $fechaHasta
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar estadísticas de sesiones', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
