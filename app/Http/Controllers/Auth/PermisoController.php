<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Permiso;
use App\Models\Auth\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador centralizado para gestión de permisos
 * Compatible con el schema de permisos especificado para base de datos cmym
 */
class PermisoController extends Controller
{
    /**
     * Mostrar lista de permisos con paginación
     */
    public function index(Request $request)
    {
        try {
            $query = Permiso::query();

            // Filtros
            if ($request->filled('cuenta_id')) {
                $query->porCuenta($request->cuenta_id);
            }

            if ($request->filled('tipo')) {
                $query->porTipo($request->tipo);
            }

            if ($request->filled('modulo')) {
                $query->porModulo($request->modulo);
            }

            if ($request->filled('accion')) {
                $query->conAccion($request->accion);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('cuenta_id', 'like', "%{$search}%")
                      ->orWhere('tipo', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortField = $request->get('sort', 'cuenta_id');
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $permisos = $query->paginate($perPage);

            // Convertir los modelos a arrays seguros antes de serializar
            $items = array_map(function($permiso) {
                return method_exists($permiso, 'toNodejsArray') ? $permiso->toNodejsArray() : $permiso->toArray();
            }, $permisos->items());

            return response()->json([
                'success' => true,
                'data' => $items,
                'pagination' => [
                    'current_page' => $permisos->currentPage(),
                    'last_page' => $permisos->lastPage(),
                    'per_page' => $permisos->perPage(),
                    'total' => $permisos->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener permisos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de permisos'
            ], 500);
        }
    }

    /**
     * Crear un nuevo permiso
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cuenta_id' => 'required|string|exists:cuentas,id',
                'modulo' => 'nullable|array',
                'modulo.*' => 'string|in:' . implode(',', Permiso::$modulos),
                'tipo' => 'nullable|string|in:' . implode(',', Permiso::$tipos),
                'acciones' => 'nullable|array',
                'acciones.*' => 'string|in:' . implode(',', Permiso::$acciones),
                'link' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que no exista ya un permiso similar para la misma cuenta
            $permisoExistente = Permiso::where('cuenta_id', $request->cuenta_id)
                                      ->where('tipo', $request->tipo)
                                      ->first();
            
            if ($permisoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un permiso de este tipo para esta cuenta'
                ], 409);
            }

            $permiso = Permiso::create($request->all());

            Log::info('Permiso creado exitosamente', ['permiso_id' => $permiso->id]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado exitosamente',
                'data' => $permiso->toNodejsArray()
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear permiso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el permiso'
            ], 500);
        }
    }

    /**
     * Mostrar un permiso específico
     */
    public function show($id)
    {
        try {
            $permiso = Permiso::with('cuenta')->find($id);

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $permiso->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener permiso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el permiso'
            ], 500);
        }
    }

    /**
     * Mostrar formulario de edición de permiso
     */
    public function edit($id)
    {
        try {
            $permiso = Permiso::with('cuenta')->find($id);

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $permiso->toNodejsArray(),
                'opciones' => [
                    'tipos' => Permiso::$tipos,
                    'modulos' => Permiso::$modulos,
                    'acciones' => Permiso::$acciones
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el formulario de edición'
            ], 500);
        }
    }

    /**
     * Actualizar un permiso existente
     */
    public function update(Request $request, $id)
    {
        try {
            $permiso = Permiso::find($id);

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'cuenta_id' => 'string|exists:cuentas,id',
                'modulo' => 'nullable|array',
                'modulo.*' => 'string|in:' . implode(',', Permiso::$modulos),
                'tipo' => 'nullable|string|in:' . implode(',', Permiso::$tipos),
                'acciones' => 'nullable|array',
                'acciones.*' => 'string|in:' . implode(',', Permiso::$acciones),
                'link' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permiso->update($request->all());

            Log::info('Permiso actualizado exitosamente', ['permiso_id' => $permiso->id]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado exitosamente',
                'data' => $permiso->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar permiso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el permiso'
            ], 500);
        }
    }

    /**
     * Eliminar un permiso
     */
    public function destroy($id)
    {
        try {
            $permiso = Permiso::find($id);

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado'
                ], 404);
            }

            $permiso->delete();

            Log::info('Permiso eliminado exitosamente', ['permiso_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar permiso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el permiso'
            ], 500);
        }
    }

    /**
     * Buscar permisos por criterios específicos
     */
    public function buscar(Request $request)
    {
        try {
            $query = Permiso::query();

            // Búsqueda por texto
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function($query) use ($q) {
                    $query->where('cuenta_id', 'like', "%{$q}%")
                          ->orWhere('tipo', 'like', "%{$q}%");
                });
            }

            // Filtros específicos
            if ($request->filled('tipo')) {
                $query->porTipo($request->tipo);
            }

            if ($request->filled('cuenta_id')) {
                $query->porCuenta($request->cuenta_id);
            }

            if ($request->filled('modulo')) {
                $query->porModulo($request->modulo);
            }

            $limit = $request->get('limit', 10);
            $permisos = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $permisos->map(function($permiso) {
                    return $permiso->toNodejsArray();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de permisos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda de permisos'
            ], 500);
        }
    }

    /**
     * Obtener permisos por cuenta
     */
    public function porCuenta($cuentaId)
    {
        try {
            $permisos = Permiso::porCuenta($cuentaId)->get();

            return response()->json([
                'success' => true,
                'data' => $permisos->map(function($permiso) {
                    return $permiso->toNodejsArray();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener permisos por cuenta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos de la cuenta'
            ], 500);
        }
    }

    /**
     * Obtener permisos por tipo
     */
    public function porTipo($tipo)
    {
        try {
            $permisos = Permiso::porTipo($tipo)->get();

            return response()->json([
                'success' => true,
                'data' => $permisos->map(function($permiso) {
                    return $permiso->toNodejsArray();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener permisos por tipo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos del tipo'
            ], 500);
        }
    }

    /**
     * Crear permisos por lotes para una cuenta
     */
    public function crearLote(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cuenta_id' => 'required|string|exists:cuentas,id',
                'permisos' => 'required|array|min:1',
                'permisos.*.modulo' => 'nullable|array',
                'permisos.*.modulo.*' => 'string|in:' . implode(',', Permiso::$modulos),
                'permisos.*.tipo' => 'nullable|string|in:' . implode(',', Permiso::$tipos),
                'permisos.*.acciones' => 'nullable|array',
                'permisos.*.acciones.*' => 'string|in:' . implode(',', Permiso::$acciones)
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permisosCreados = [];
            $cuentaId = $request->cuenta_id;

            foreach ($request->permisos as $permisoData) {
                $permisoData['cuenta_id'] = $cuentaId;
                $permiso = Permiso::create($permisoData);
                $permisosCreados[] = $permiso;
            }

            Log::info('Permisos creados por lotes', [
                'cuenta_id' => $cuentaId,
                'cantidad' => count($permisosCreados)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permisos creados exitosamente',
                'data' => collect($permisosCreados)->map(function($permiso) {
                    return $permiso->toNodejsArray();
                })
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear permisos por lotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear los permisos'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de permisos
     */
    public function estadisticas()
    {
        try {
            $estadisticas = [
                'total_permisos' => Permiso::count(),
                'por_tipo' => [
                    'interna' => Permiso::porTipo('interna')->count(),
                    'cliente' => Permiso::porTipo('cliente')->count(),
                    'crm-cliente' => Permiso::porTipo('crm-cliente')->count(),
                    'profesional' => Permiso::porTipo('profesional')->count(),
                    'usuario' => Permiso::porTipo('usuario')->count()
                ],
                'por_modulo' => [],
                'por_accion' => []
            ];

            // Estadísticas por módulo
            foreach (Permiso::$modulos as $modulo) {
                $estadisticas['por_modulo'][$modulo] = Permiso::porModulo($modulo)->count();
            }

            // Estadísticas por acción
            foreach (Permiso::$acciones as $accion) {
                $estadisticas['por_accion'][$accion] = Permiso::conAccion($accion)->count();
            }

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de permisos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas'
            ], 500);
        }
    }

    /**
     * Obtener opciones disponibles para formularios
     */
    public function opciones()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'tipos' => Permiso::$tipos,
                'modulos' => Permiso::$modulos,
                'acciones' => Permiso::$acciones
            ]
        ]);
    }

    /**
     * Verificar acceso de un permiso
     */
    public function verificarAcceso($id, Request $request)
    {
        try {
            $permiso = Permiso::find($id);

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado'
                ], 404);
            }

            $modulo = $request->get('modulo');
            $accion = $request->get('accion');

            $resultado = [
                'permiso' => $permiso->toNodejsArray(),
                'verificaciones' => []
            ];

            if ($modulo) {
                $resultado['verificaciones']['modulo'] = [
                    'modulo' => $modulo,
                    'tiene_acceso' => $permiso->tieneAccesoModulo($modulo)
                ];
            }

            if ($accion) {
                $resultado['verificaciones']['accion'] = [
                    'accion' => $accion,
                    'puede_realizar' => $permiso->puedeRealizarAccion($accion)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar acceso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar acceso'
            ], 500);
        }
    }

    /**
     * Sincronizar permisos de una cuenta con los de su rol
     */
    public function sincronizarConRol($cuentaId)
    {
        try {
            $cuenta = Cuenta::find($cuentaId);
            
            if (!$cuenta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuenta no encontrada'
                ], 404);
            }

            // Obtener configuración del rol de la cuenta
            $configuracionRol = getRoleConfiguration($cuenta->rol);
            
            if (!$configuracionRol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuración de rol no encontrada'
                ], 404);
            }

            // Eliminar permisos existentes de la cuenta
            Permiso::where('cuenta_id', $cuentaId)->delete();

            // Crear nuevo permiso basado en el rol
            $permiso = Permiso::create([
                'cuenta_id' => $cuentaId,
                'modulo' => $configuracionRol['modulos'] ?? [],
                'tipo' => $cuenta->tipo,
                'acciones' => $configuracionRol['acciones'] ?? []
            ]);

            Log::info('Permisos sincronizados con rol', [
                'cuenta_id' => $cuentaId,
                'rol' => $cuenta->rol
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permisos sincronizados exitosamente',
                'data' => $permiso->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al sincronizar permisos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar permisos'
            ], 500);
        }
    }
}
