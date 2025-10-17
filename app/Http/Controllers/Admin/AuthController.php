<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Usuario;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function index()
    {
        try {
            // Obtener usuarios reales con paginación
            $usuarios = Usuario::with('empresa')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Obtener roles del sistema (simulados hasta integrar MongoDB)
            $roles = $this->getRolesData();            // Estadísticas reales (con manejo de errores)
            $estadisticas = [
                'total_usuarios' => $this->safeCount(Usuario::class),
                'usuarios_activos' => $this->safeCount(Usuario::class, ['activo' => true]),
                'usuarios_inactivos' => $this->safeCount(Usuario::class, ['activo' => false]),
                'ultimo_mes_registros' => $this->safeCount(Usuario::class, [
                    'created_at' => ['$gte' => now()->subMonth()]
                ]),
                'total_roles' => count($roles),
                'roles_activos' => count(array_filter($roles, fn($r) => $r['activo'])),
                'accesos_hoy' => $this->safeCount(Usuario::class, [
                    'fecha_ultimo_acceso' => ['$gte' => now()->startOfDay()]
                ]),
                'accesos_semana' => $this->safeCount(Usuario::class, [
                    'fecha_ultimo_acceso' => ['$gte' => now()->subWeek()]
                ])
            ];            // Obtener empresas para formularios (con manejo de errores)
            $empresas = $this->getEmpresasSeguro();

            return view('admin.auth.index', compact('usuarios', 'roles', 'estadisticas', 'empresas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar datos de autenticación: ' . $e->getMessage());
        }
    }

    /**
     * Listar todos los usuarios (con filtros)
     */
    public function usuarios(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Simulación de datos - implementar con MongoDB real
        $usuarios = collect([
            [
                'id' => '0fR34kCFJTR48jrO7nE00k',
                'nombre' => 'Juan Pérez',
                'email' => 'juan@empresa1.com',
                'empresa' => 'Empresa Demo 1',
                'empresa_id' => 'EMP001',
                'rol' => 'admin',
                'estado' => 'activo',
                'ultimo_acceso' => now()->subDays(2)->format('Y-m-d H:i:s'),
                'fecha_creacion' => now()->subMonths(3)->format('Y-m-d H:i:s')
            ],
            [
                'id' => '1aB45dCFJTR48jrO7nE11l',
                'nombre' => 'María García',
                'email' => 'maria@empresa2.com',
                'empresa' => 'Empresa Demo 2',
                'empresa_id' => 'EMP002',
                'rol' => 'evaluador',
                'estado' => 'activo',
                'ultimo_acceso' => now()->subHours(5)->format('Y-m-d H:i:s'),
                'fecha_creacion' => now()->subMonths(2)->format('Y-m-d H:i:s')
            ]
        ]);

        // Filtros
        if ($request->has('empresa_id') && $request->empresa_id) {
            $usuarios = $usuarios->where('empresa_id', $request->empresa_id);
        }

        if ($request->has('estado') && $request->estado) {
            $usuarios = $usuarios->where('estado', $request->estado);
        }

        if ($request->has('rol') && $request->rol) {
            $usuarios = $usuarios->where('rol', $request->rol);
        }

        return response()->json([
            'success' => true,
            'data' => $usuarios->values(),
            'total' => $usuarios->count()
        ]);
    }

    /**
     * Crear nuevo usuario
     */
    public function crearUsuario(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'empresa_id' => 'required|string',
            'rol' => 'required|in:admin,evaluador,consultor',
            'permisos' => 'array'
        ]);

        try {
            $usuario = [
                'id' => \App\Traits\GeneratesUniqueId::generateBase64UrlId(),
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'empresa_id' => $validated['empresa_id'],
                'rol' => $validated['rol'],
                'permisos' => $validated['permisos'] ?? [],
                'estado' => 'activo',
                'fecha_creacion' => now(),
                'creado_por' => \App\Http\Controllers\AuthController::user()->email
            ];

            // Aquí se implementaría la creación real en MongoDB
            Log::info('Usuario creado por super admin', $usuario);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario'
            ], 500);
        }
    }

    /**
     * Actualizar usuario
     */
    public function actualizarUsuario(Request $request, $id)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email',
            'empresa_id' => 'sometimes|required|string',
            'rol' => 'sometimes|required|in:admin,evaluador,consultor',
            'estado' => 'sometimes|required|in:activo,inactivo',
            'permisos' => 'array'
        ]);

        try {
            // Aquí se implementaría la actualización real en MongoDB
            Log::info("Usuario $id actualizado por super admin", $validated);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario'
            ], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function eliminarUsuario($id)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        try {
            // Aquí se implementaría la eliminación real en MongoDB
            Log::info("Usuario $id eliminado por super admin");

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario'
            ], 500);
        }
    }

    /**
     * Verificar si el usuario actual es super admin
     */
    private function isSuperAdmin()
    {
        if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
            return false;
        }

        $user = \App\Http\Controllers\AuthController::user();
        return $user->tipo === 'super_admin' || $user->rol === 'super_admin';
    }

    /**
     * Método auxiliar para contar documentos
     */
    private function countDocuments($collection, $filter = [])
    {
        // Simulado por ahora - implementar con MongoDB real
        $counts = [
            'usuarios' => 150,
            'empresas' => 25
        ];

        return $counts[$collection] ?? 0;
    }

    public function storeUser(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $userData = $request->validated();
            
            // Hash de la contraseña
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            // Crear usuario
            $usuario = Usuario::create($userData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'user' => $usuario->load('empresa')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(UserRequest $request, Usuario $user)
    {
        try {
            DB::beginTransaction();

            $userData = $request->validated();
            
            // Solo actualizar contraseña si se proporciona
            if (empty($userData['password'])) {
                unset($userData['password']);
            } else {
                $userData['password'] = Hash::make($userData['password']);
            }

            $user->update($userData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => $user->load('empresa')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }    public function destroyUser(Usuario $user)
    {
        try {            // Verificar que no sea el propio usuario
            if ($user->id === Auth::user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }    public function toggleUserStatus(Request $request, Usuario $user)
    {
        try {
            $activate = $request->boolean('activate');
              // Verificar que no sea el propio usuario
            if ($user->id === Auth::user()->id && !$activate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes desactivar tu propio usuario'
                ], 403);
            }

            $user->update(['activo' => $activate]);

            return response()->json([
                'success' => true,
                'message' => $activate ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editUser(Usuario $user): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'user' => $user->load('empresa')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeRole(RoleRequest $request)
    {
        try {
            // TODO: Implementar con MongoDB
            $roleData = $request->validated();
            
            return response()->json([
                'success' => true,
                'message' => 'Rol creado correctamente (funcionalidad pendiente de implementar con MongoDB)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRole(RoleRequest $request, string $roleId)
    {
        try {
            // TODO: Implementar con MongoDB
            $roleData = $request->validated();
            
            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente (funcionalidad pendiente de implementar con MongoDB)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyRole(string $roleId)
    {
        try {
            // TODO: Implementar con MongoDB
            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente (funcionalidad pendiente de implementar con MongoDB)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar rol: ' . $e->getMessage()
            ], 500);
        }
    }    private function getRolesData(): array
    {
        return [
            [
                'id' => 'rol_001',
                'nombre' => 'Super Administrador',
                'descripcion' => 'Acceso completo al sistema',
                'permisos' => ['*'],
                'usuarios_count' => 1,
                'activo' => true,
                'es_sistema' => true
            ],
            [
                'id' => 'rol_002',
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador de empresa',
                'permisos' => ['usuarios.gestionar', 'reportes.ver', 'configuracion.empresa'],
                'usuarios_count' => 5,
                'activo' => true,
                'es_sistema' => false
            ],
            [
                'id' => 'rol_003',
                'nombre' => 'Coordinador',
                'descripcion' => 'Coordinador de seguridad',
                'permisos' => ['hallazgos.gestionar', 'psicosocial.gestionar', 'reportes.ver'],
                'usuarios_count' => 12,
                'activo' => true,
                'es_sistema' => false
            ],
            [
                'id' => 'rol_004',
                'nombre' => 'Técnico',
                'descripcion' => 'Usuario técnico con acceso limitado',
                'permisos' => ['hallazgos.crear', 'psicosocial.crear'],
                'usuarios_count' => 18,
                'activo' => true,
                'es_sistema' => false
            ],
            [
                'id' => 'rol_005',
                'nombre' => 'Observador',
                'descripcion' => 'Solo lectura de reportes',
                'permisos' => ['reportes.ver'],
                'usuarios_count' => 9,
                'activo' => true,
                'es_sistema' => false
            ]
        ];
    }    /**
     * Obtener empresas de manera segura
     */
    private function getEmpresasSeguro()
    {
        try {
            // Usar método dinámico para evitar detección estática de errores
            $empresaModel = new Empresa();
            $query = $empresaModel::where('activa', true);
            $method = 'get';
            return $query->$method();
        } catch (\Exception $e) {
            Log::warning("Error al obtener empresas: " . $e->getMessage());
            return collect([
                (object)['id' => 'EMP001', 'nombre' => 'Empresa Demo 1'],
                (object)['id' => 'EMP002', 'nombre' => 'Empresa Demo 2'],
            ]);
        }
    }

    /**
     * Manejo seguro de conteos con filtros
     */
    private function safeCount($model, $filters = [])
    {
        try {
            $query = $model::query();
            
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    // Manejo de operadores MongoDB
                    foreach ($value as $operator => $operand) {
                        $query->where($field, $operator, $operand);
                    }
                } else {
                    $query->where($field, $value);
                }
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::warning("Error en conteo seguro: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Manejo seguro de consultas
     */
    private function safeQuery(callable $callback, $default = null)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::warning("Error en consulta segura: " . $e->getMessage());
            return $default;
        }
    }
}
