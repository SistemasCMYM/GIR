<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Rol;
use App\Models\Auth\Cuenta;
use App\Models\Auth\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador centralizado para gestión de roles
 * Compatible con el schema de roles especificado para base de datos cmym
 */
class RolController extends Controller
{
    /**
     * Mostrar lista de roles con paginación
     */
    public function index(Request $request)
    {
        try {
            $query = Rol::query();

            // Filtros
            if ($request->filled('empresa_id')) {
                $query->paraEmpresa($request->empresa_id);
            }

            if ($request->filled('cuenta_id')) {
                $query->where('cuenta_id', $request->cuenta_id);
            }

            if ($request->filled('tipo')) {
                $query->porTipo($request->tipo);
            }

            if ($request->filled('nombre')) {
                $query->porNombre($request->nombre);
            }

            if ($request->filled('activo')) {
                if ($request->activo === 'true' || $request->activo === '1') {
                    $query->activos();
                } else {
                    $query->where('activo', false);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortField = $request->get('sort', 'nombre');
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $roles = $query->paginate($perPage);

            // Si la petición espera JSON (API/AJAX), devolver JSON como antes
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $roles->items(),
                    'pagination' => [
                        'current_page' => $roles->currentPage(),
                        'last_page' => $roles->lastPage(),
                        'per_page' => $roles->perPage(),
                        'total' => $roles->total()
                    ]
                ]);
            }

            // Petición desde navegador: renderizar la vista de gestión administrativa
            $rolesSistema = Rol::$rolesConfig ?? [];
            $stats = [
                'total_roles' => Rol::where('empresa_id', null)->where('activo', true)->count(),
                'roles_activos' => Rol::where('empresa_id', null)->where('activo', true)->count(),
                'permisos_totales' => collect(Rol::$rolesConfig ?? [])->sum(function($rol) {
                    return count($rol['permisos'] ?? []);
                }),
                'asignaciones' => Perfil::whereNotNull('rol_id')->whereNotNull('cuenta_id')->count()
            ];

            return view('admin.gestion-administrativa.roles.index', compact('roles', 'rolesSistema', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error al obtener roles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de roles'
            ], 500);
        }
    }

    /**
     * Crear un nuevo rol
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|in:' . implode(',', Rol::$roles),
                'descripcion' => 'nullable|string|max:1000',
                'tipo' => 'required|string|in:' . implode(',', Rol::$tipos),
                'empresa_id' => 'nullable|string',
                'cuenta_id' => 'nullable|string|exists:cuentas,id',
                'modulos' => 'nullable|array',
                'modulos.*' => 'string|in:' . implode(',', Rol::$modulos),
                'permisos' => 'nullable|array',
                'permisos.*' => 'string|in:' . implode(',', Rol::$permisos),
                'activo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que no exista ya un rol con el mismo nombre para la misma empresa
            $rolExistente = Rol::where('nombre', $request->nombre)
                               ->where('empresa_id', $request->empresa_id)
                               ->first();
            
            if ($rolExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un rol con este nombre para esta empresa'
                ], 409);
            }

            $rol = Rol::create($request->all());

            Log::info('Rol creado exitosamente', ['rol_id' => $rol->id]);

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente',
                'data' => $rol->toNodejsArray()
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear rol: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol'
            ], 500);
        }
    }

    /**
     * Mostrar formulario para crear un nuevo rol
     */
    public function create(Request $request)
    {
        try {
            $rol = [
                'nombre' => null,
                'descripcion' => null,
                'tipo' => null,
                'empresa_id' => null,
                'cuenta_id' => null,
                'modulos' => [],
                'permisos' => [],
                'activo' => true,
            ];

            $opciones = [
                'roles' => Rol::$roles,
                'tipos' => Rol::$tipos,
                'modulos' => Rol::$modulos,
                'permisos' => Rol::$permisos,
                'configuraciones' => Rol::$rolesConfig,
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $rol,
                    'opciones' => $opciones,
                ]);
            }

            return view('admin.gestion-administrativa.roles.create', [
                'rolData' => $rol,
                'opciones' => $opciones,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al preparar formulario de creación de rol: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos para crear un rol',
            ], 500);
        }
    }

    /**
     * Mostrar un rol específico
     */
    public function show($id)
    {
        try {
            $rol = Rol::with(['cuenta', 'cuentas'])->find($id);

            if (!$rol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $rol->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener rol: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el rol'
            ], 500);
        }
    }

    /**
     * Mostrar formulario de edición de rol
     */
    public function edit(Request $request, $id)
    {
        try {
            $rol = Rol::with(['cuenta', 'cuentas'])->find($id);

            if (!$rol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            $payload = [
                'success' => true,
                'data' => $rol->toNodejsArray(),
                'opciones' => [
                    'roles' => Rol::$roles,
                    'tipos' => Rol::$tipos,
                    'modulos' => Rol::$modulos,
                    'permisos' => Rol::$permisos,
                    'configuraciones' => Rol::$rolesConfig
                ],
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($payload);
            }

            return view('admin.gestion-administrativa.roles.edit', [
                'rol' => $rol,
                'rolData' => $payload['data'],
                'opciones' => $payload['opciones'],
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
     * Actualizar un rol existente
     */
    public function update(Request $request, $id)
    {
        try {
            $rol = Rol::find($id);

            if (!$rol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'string|in:' . implode(',', Rol::$roles),
                'descripcion' => 'nullable|string|max:1000',
                'tipo' => 'string|in:' . implode(',', Rol::$tipos),
                'empresa_id' => 'nullable|string',
                'cuenta_id' => 'nullable|string|exists:cuentas,id',
                'modulos' => 'nullable|array',
                'modulos.*' => 'string|in:' . implode(',', Rol::$modulos),
                'permisos' => 'nullable|array',
                'permisos.*' => 'string|in:' . implode(',', Rol::$permisos),
                'activo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rol->update($request->all());

            Log::info('Rol actualizado exitosamente', ['rol_id' => $rol->id]);

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado exitosamente',
                'data' => $rol->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar rol: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol'
            ], 500);
        }
    }

    /**
     * Eliminar un rol
     */
    public function destroy($id)
    {
        try {
            $rol = Rol::find($id);

            if (!$rol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            // Verificar si hay cuentas asociadas a este rol
            $cuentasAsociadas = Cuenta::where('rol', $rol->nombre)->count();
            
            if ($cuentasAsociadas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar el rol porque tiene {$cuentasAsociadas} cuentas asociadas"
                ], 409);
            }

            $rol->delete();

            Log::info('Rol eliminado exitosamente', ['rol_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar rol: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol'
            ], 500);
        }
    }

    /**
     * Buscar roles por criterios específicos
     */
    public function buscar(Request $request)
    {
        try {
            $query = Rol::query();

            // Búsqueda por texto
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function($query) use ($q) {
                    $query->where('nombre', 'like', "%{$q}%")
                          ->orWhere('descripcion', 'like', "%{$q}%");
                });
            }

            // Filtros específicos
            if ($request->filled('tipo')) {
                $query->porTipo($request->tipo);
            }

            if ($request->filled('empresa_id')) {
                $query->paraEmpresa($request->empresa_id);
            }

            if ($request->filled('activo')) {
                $query->activos();
            }

            $limit = $request->get('limit', 10);
            $roles = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $roles->map(function($rol) {
                    return $rol->toNodejsArray();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de roles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda de roles'
            ], 500);
        }
    }

    /**
     * Obtener roles por empresa
     */
    public function porEmpresa($empresaId)
    {
        try {
            $roles = Rol::paraEmpresa($empresaId)->activos()->get();

            return response()->json([
                'success' => true,
                'data' => $roles->map(function($rol) {
                    return $rol->toNodejsArray();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener roles por empresa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener roles de la empresa'
            ], 500);
        }
    }

    /**
     * Crear roles predefinidos del sistema para una empresa
     */
    public function crearRolesSistema(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'empresa_id' => 'nullable|string',
                'cuenta_id' => 'nullable|string|exists:cuentas,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rolesCreados = Rol::crearRolesSistema(
                $request->empresa_id,
                $request->cuenta_id
            );

            Log::info('Roles del sistema creados', [
                'empresa_id' => $request->empresa_id,
                'cantidad' => count($rolesCreados)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Roles del sistema creados exitosamente',
                'data' => collect($rolesCreados)->map(function($rol) {
                    return $rol->toNodejsArray();
                })
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear roles del sistema: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear los roles del sistema'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de roles
     */
    public function estadisticas()
    {
        try {
            $estadisticas = [
                'total_roles' => Rol::count(),
                'por_tipo' => [
                    'interna' => Rol::porTipo('interna')->count(),
                    'cliente' => Rol::porTipo('cliente')->count(),
                    'profesional' => Rol::porTipo('profesional')->count(),
                    'usuario' => Rol::porTipo('usuario')->count()
                ],
                'por_nombre' => [
                    'SuperAdmin' => Rol::porNombre('SuperAdmin')->count(),
                    'administrador' => Rol::porNombre('administrador')->count(),
                    'profesional' => Rol::porNombre('profesional')->count(),
                    'tecnico' => Rol::porNombre('tecnico')->count(),
                    'supervisor' => Rol::porNombre('supervisor')->count(),
                    'usuario' => Rol::porNombre('usuario')->count()
                ],
                'activos' => Rol::activos()->count(),
                'inactivos' => Rol::where('activo', false)->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de roles: ' . $e->getMessage());
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
                'roles' => Rol::$roles,
                'tipos' => Rol::$tipos,
                'modulos' => Rol::$modulos,
                'permisos' => Rol::$permisos,
                'configuraciones' => Rol::$rolesConfig
            ]
        ]);
    }

    /**
     * Verificar permisos de un rol
     */
    public function verificarPermisos($id, Request $request)
    {
        try {
            $rol = Rol::find($id);

            if (!$rol) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            $permiso = $request->get('permiso');
            $modulo = $request->get('modulo');

            $resultado = [
                'rol' => $rol->toNodejsArray(),
                'verificaciones' => []
            ];

            if ($permiso) {
                $resultado['verificaciones']['permiso'] = [
                    'permiso' => $permiso,
                    'tiene_acceso' => $rol->tienePermiso($permiso)
                ];
            }

            if ($modulo) {
                $resultado['verificaciones']['modulo'] = [
                    'modulo' => $modulo,
                    'puede_acceder' => $rol->puedeAccederModulo($modulo)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar permisos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar permisos'
            ], 500);
        }
    }
}
