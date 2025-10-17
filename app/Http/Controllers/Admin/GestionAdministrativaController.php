<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\Cuenta;
use App\Models\Auth\Rol;
use App\Models\Auth\Permiso;
use App\Models\Auth\Perfil;
use App\Models\Auth\Sesion;
use App\Models\Auth\Notificacion;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GestionAdministrativaController extends Controller
{
    /**
     * Dashboard principal de Gestión Administrativa (Usuarios)
     */
    public function index()
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            // Estadísticas generales
            $totalUsuarios = Cuenta::when(!$isSuperAdmin && $empresaId, function($query) use ($empresaId) {
                return $query->whereIn('empresas', [$empresaId]);
            })->count();
            
            // Contar roles activos desde colección 'roles' de cmym
            // (si hay multiempresa en roles, podríamos filtrar por empresa_id cuando aplique)
            $rolesActivos = Rol::where('activo', true)
                ->when(!$isSuperAdmin && $empresaId, function($query) use ($empresaId) {
                    // Algunos esquemas tienen roles globales (empresa_id null) y por empresa
                    return $query->where(function($q) use ($empresaId) {
                        $q->whereNull('empresa_id')->orWhere('empresa_id', $empresaId);
                    });
                })
                ->count();

            // Contar permisos totales desde colección 'permisos' de cmym
            // En Mongo es más confiable usar whereIn con la lista de cuentas de la empresa
            if (!$isSuperAdmin && $empresaId) {
                $cuentasEmpresaIds = Cuenta::whereIn('empresas', [$empresaId])->pluck('id');
                $totalPermisos = Permiso::whereIn('cuenta_id', $cuentasEmpresaIds)->count();
            } else {
                $totalPermisos = Permiso::count();
            }
            
            // Usuarios activos hoy (desde cmym.cuentas): estado = 'activa'
            $usuariosActivosHoy = Cuenta::when(!$isSuperAdmin && $empresaId, function($query) use ($empresaId) {
                    return $query->whereIn('empresas', [$empresaId]);
                })
                ->where('estado', 'activa')
                ->count();

            // Estadísticas adicionales para el dashboard
            $usuariosNuevosEsteMes = Cuenta::when(!$isSuperAdmin && $empresaId, function($query) use ($empresaId) {
                return $query->whereIn('empresas', [$empresaId]);
            })->whereMonth('created_at', now()->month)->count();

            // Obtener usuarios por rol basado en perfiles
            $perfiles = Perfil::with('rol:id,nombre')
                ->when(!$isSuperAdmin && $empresaId, function($query) use ($empresaId) {
                    return $query->whereHas('cuenta', function($q) use ($empresaId) {
                        $q->whereIn('empresas', [$empresaId]);
                    });
                })
                ->whereNotNull('rol_id')
                ->get(['rol_id']);

            $usuariosPorRol = [];
            foreach ($perfiles as $perfil) {
                if ($perfil->rol) {
                    $rolNombre = $perfil->rol->nombre ?? 'sin_rol';
                    $usuariosPorRol[$rolNombre] = ($usuariosPorRol[$rolNombre] ?? 0) + 1;
                }
            }

            $estadisticas = [
                'total_usuarios' => $totalUsuarios,
                'roles_activos' => $rolesActivos,
                'total_permisos' => $totalPermisos,
                'usuarios_activos_hoy' => $usuariosActivosHoy,
                'usuarios_nuevos_mes' => $usuariosNuevosEsteMes,
                'usuarios_por_rol' => $usuariosPorRol
            ];

            // Obtener últimos usuarios creados
            $ultimosusuarios = Cuenta::when(!$isSuperAdmin && $empresaId, function($query) use ($empresaId) {
                return $query->whereIn('empresas', [$empresaId]);
            })->orderBy('created_at', 'desc')->limit(5)->get();

            return view('admin.gestion-administrativa.index', compact('estadisticas', 'ultimosusuarios'));
        } catch (\Exception $e) {
            Log::error('Error en GestionAdministrativaController@index: ' . $e->getMessage());
            return view('admin.gestion-administrativa.index', [
                'estadisticas' => [
                    'total_usuarios' => 0,
                    'roles_activos' => 0,
                    'total_permisos' => 0,
                    'usuarios_activos_hoy' => 0,
                    'usuarios_nuevos_mes' => 0,
                    'usuarios_por_rol' => []
                ],
                'ultimosusuarios' => collect([])
            ]);
        }
    }

    /**
     * Submódulo: Gestión de Cuentas
     */
    public function cuentasIndex(Request $request)
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            // Obtener cuentas directamente de MongoDB
            $query = DB::connection('mongodb_cmym')->collection('cuentas');
            
            // Para SuperAdmin: todas las cuentas
            // Para otros: solo cuentas de su empresa
            if (!$isSuperAdmin && $empresaId) {
                $query = $query->where('empresas', $empresaId);
            }
            
            // Filtros de búsqueda
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query = $query->where(function($q) use ($search) {
                    $q->where('nick', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('rol') && !empty($request->rol)) {
                $query = $query->where('rol', $request->rol);
            }
            
            if ($request->has('estado') && !empty($request->estado)) {
                $query = $query->where('estado', $request->estado);
            }
            
            // Obtener cuentas y convertir a collection
            $cuentasData = $query->orderBy('created_at', 'desc')->get();
            
            // Obtener perfiles para relacionar con las cuentas
            $perfiles = DB::connection('mongodb_cmym')->collection('perfiles')->get();
            $perfilesIndexed = collect($perfiles)->keyBy('cuenta_id');
            
            // Combinar datos de cuentas con perfiles
            $cuentas = collect($cuentasData)->map(function($cuenta) use ($perfilesIndexed) {
                $cuentaObj = (object) $cuenta;
                // Usar el campo 'id' string en lugar de '_id' ObjectId para las relaciones
                $cuentaId = $cuenta['id'] ?? '';
                
                // Buscar perfil asociado
                $perfil = $perfilesIndexed->get($cuentaId);
                if ($perfil) {
                    $cuentaObj->nombre = $perfil['nombre'] ?? '';
                    $cuentaObj->apellido = $perfil['apellido'] ?? '';
                    $cuentaObj->genero = $perfil['genero'] ?? '';
                    $cuentaObj->ocupacion = $perfil['ocupacion'] ?? '';
                } else {
                    // Valores por defecto si no hay perfil
                    $cuentaObj->nombre = '';
                    $cuentaObj->apellido = '';
                    $cuentaObj->genero = '';
                    $cuentaObj->ocupacion = '';
                }
                
                return $cuentaObj;
            });
            
            // Obtener roles desde MongoDB
            $rolesData = DB::connection('mongodb_cmym')->collection('roles')->get(['_id', 'nombre']);
            $roles = collect($rolesData)->pluck('nombre', '_id');
            
            // Estados disponibles
            $estados = ['activa', 'inactiva', 'suspendida'];
            
            // Calcular estadísticas para la vista
            $totalCuentas = $cuentas->count();
            $stats = [
                'total_usuarios' => $totalCuentas,
                'usuarios_activos' => $cuentas->where('estado', 'activa')->count(),
                'usuarios_inactivos' => $cuentas->where('estado', 'inactiva')->count(),
                'usuarios_bloqueados' => $cuentas->where('estado', 'suspendida')->count(),
            ];

            return view('admin.gestion-administrativa.cuentas.index', compact('cuentas', 'roles', 'estados', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error en cuentasIndex: ' . $e->getMessage());
            return view('admin.gestion-administrativa.cuentas.index', [
                'cuentas' => collect([]),
                'roles' => collect([]),
                'estados' => collect([]),
                'stats' => [
                    'total_usuarios' => 0,
                    'usuarios_activos' => 0,
                    'usuarios_inactivos' => 0,
                    'usuarios_bloqueados' => 0,
                ]
            ]);
        }
    }

    /**
     * Mostrar formulario para crear nueva cuenta
     */
    public function mostrarCrearCuenta(Request $request)
    {
        try {
            // Conectar directamente a las bases de datos MongoDB
            
            // 1. Obtener roles desde cmym.roles
            $roles = collect();
            try {
                $rolesData = DB::connection('mongodb_cmym')
                    ->collection('roles')
                    ->where('activo', true)
                    ->get(['id', 'nombre', 'tipo', 'modulos', 'permisos']);
                
                foreach ($rolesData as $role) {
                    // Normalize document shape: it may be an array or stdClass, and id may be in 'id' or '_id' and could be an ObjectId or string
                    $idValue = null;
                    $nombre = '';
                    $tipo = 'usuario';
                    $modulosVal = ['dashboard'];
                    $permisosVal = ['read'];

                    if (is_array($role)) {
                        $idValue = $role['id'] ?? $role['_id'] ?? null;
                        $nombre = $role['nombre'] ?? '';
                        $tipo = $role['tipo'] ?? 'usuario';
                        $modulosVal = $role['modulos'] ?? ['dashboard'];
                        $permisosVal = $role['permisos'] ?? ['read'];
                    } elseif (is_object($role)) {
                        $idValue = $role->id ?? $role->_id ?? null;
                        $nombre = $role->nombre ?? '';
                        $tipo = $role->tipo ?? 'usuario';
                        $modulosVal = $role->modulos ?? ['dashboard'];
                        $permisosVal = $role->permisos ?? ['read'];
                    }

                    // Convert possible ObjectId or object to string safely
                    $idStr = '';
                    if (is_object($idValue) && method_exists($idValue, '__toString')) {
                        $idStr = (string) $idValue;
                    } elseif (is_string($idValue)) {
                        $idStr = $idValue;
                    } elseif (is_array($idValue) && isset($idValue['$oid'])) {
                        $idStr = (string) $idValue['$oid'];
                    } else {
                        $idStr = (string) ($idValue ?? '');
                    }

                    // Ensure modulos/permisos are arrays
                    if (!is_array($modulosVal)) {
                        $decoded = @json_decode($modulosVal, true);
                        $modulosVal = is_array($decoded) ? $decoded : ['dashboard'];
                    }
                    if (!is_array($permisosVal)) {
                        $decoded = @json_decode($permisosVal, true);
                        $permisosVal = is_array($decoded) ? $decoded : ['read'];
                    }

                    $roles->push((object)[
                        'id' => $idStr,
                        'nombre' => $nombre,
                        'tipo' => $tipo,
                        'modulos' => $modulosVal,
                        'permisos' => $permisosVal
                    ]);
                }
                Log::info('Roles cargados desde cmym.roles: ' . $roles->count());
            } catch (\Exception $e) {
                Log::error('Error cargando roles: ' . $e->getMessage());
            }
            
            // 2. Obtener empresas desde empresas.empresas 
            $empresas = collect();
            try {
                $empresasData = DB::connection('mongodb_empresas')
                    ->collection('empresas')
                    ->where('_esBorrado', false)
                    ->get(['_id', 'nombre', 'razon_social', 'nit']);
                
                foreach ($empresasData as $empresa) {
                    $empresas->push((object)[
                        '_id' => $empresa['_id']->__toString(),
                        'nombre_comercial' => $empresa['nombre'] ?? $empresa['razon_social'] ?? 'Sin nombre',
                        'nit' => $empresa['nit'] ?? ''
                    ]);
                }
                Log::info('Empresas cargadas desde empresas.empresas: ' . $empresas->count());
            } catch (\Exception $e) {
                Log::error('Error cargando empresas: ' . $e->getMessage());
            }
            
            // Obtener tipos de cuenta disponibles
            $tiposCuenta = ['interna', 'cliente', 'profesional', 'usuario'];
            
            // Obtener géneros disponibles
            $generos = ['masculino', 'femenino', 'otro'];
            
            // Si es solicitud para modal, devolver solo el formulario modal
            if ($request->has('modal') || $request->wantsJson()) {
                return response()->json([
                    'roles' => $roles,
                    'empresas' => $empresas,
                    'tiposCuenta' => $tiposCuenta,
                    'generos' => $generos
                ]);
            }
            
            // Si es solicitud AJAX pero no para modal, devolver vista parcial
            if ($request->ajax()) {
                return view('admin.gestion-administrativa.cuentas.create-modal', compact(
                    'roles', 
                    'empresas', 
                    'tiposCuenta', 
                    'generos'
                ))->render();
            }
            
            Log::info('Enviando datos a vista: roles=' . $roles->count() . ', empresas=' . $empresas->count());
            
            // Solicitud normal, devolver vista completa
            return view('admin.gestion-administrativa.cuentas.create', compact(
                'roles', 
                'empresas', 
                'tiposCuenta', 
                'generos'
            ));
        } catch (\Exception $e) {
            Log::error('Error en mostrarCrearCuenta: ' . $e->getMessage());
            
            // En caso de error, enviar valores por defecto
            $roles = collect([]);
            $empresas = collect([]);
            $tiposCuenta = ['interna', 'cliente', 'profesional', 'usuario'];
            $generos = ['masculino', 'femenino', 'otro'];
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'roles' => $roles,
                    'empresas' => $empresas,
                    'tiposCuenta' => $tiposCuenta,
                    'generos' => $generos,
                    'error' => 'Error al cargar algunos datos'
                ], 200);
            }
            
            return view('admin.gestion-administrativa.cuentas.create', compact(
                'roles', 
                'empresas', 
                'tiposCuenta', 
                'generos'
            ))->with('warning', 'Algunos datos no pudieron cargarse correctamente');
        }
    }

    /**
     * Crear nueva cuenta con perfil y permisos
     */
    public function crearCuenta(Request $request)
    {
        Log::info('Iniciando creación de cuenta', $request->all());
        
        // Validación completa de las tres secciones
        $validator = Validator::make($request->all(), [
            // Sección 1: Datos de la cuenta (colección cuentas)
            'nick' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'email' => 'required|email',
            'contrasena' => 'required|string|min:8|confirmed',
            'contrasena_confirmation' => 'required|string|min:8',
            'tipo' => 'required|string|in:interna,cliente,profesional,usuario',
            'rol_id' => 'required|string',
            'estado' => 'required|in:activa,inactiva,suspendida',
            'empresas' => 'required|array|min:1',
            'empresas.*' => 'required|string',
            
            // Sección 2: Datos del perfil (colección perfiles)
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'genero' => 'required|in:masculino,femenino,otro',
            'ocupacion' => 'nullable|string|max:255',
            'firma' => 'nullable|string',
            'piefirma' => 'nullable|string',
            'licencia' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        try {
            Log::info('Iniciando proceso de guardado en MongoDB');
            
            // Verificar si el email ya existe
            $emailExists = DB::connection('mongodb_cmym')
                ->collection('cuentas')
                ->where('email', $request->email)
                ->exists();
                
            if ($emailExists) {
                Log::warning('Email ya existe: ' . $request->email);
                return back()->withErrors(['email' => 'El email ya está en uso'])->withInput();
            }
            
            // Verificar si el nick ya existe
            $nickExists = DB::connection('mongodb_cmym')
                ->collection('cuentas')
                ->where('nick', $request->nick)
                ->exists();
                
            if ($nickExists) {
                Log::warning('Nick ya existe: ' . $request->nick);
                return back()->withErrors(['nick' => 'El nick ya está en uso'])->withInput();
            }
            
            // Verificar si el DNI ya existe
            $dniExists = DB::connection('mongodb_cmym')
                ->collection('cuentas')
                ->where('dni', $request->dni)
                ->exists();
                
            if ($dniExists) {
                Log::warning('DNI ya existe: ' . $request->dni);
                return back()->withErrors(['dni' => 'El DNI ya está en uso'])->withInput();
            }
            
            // 1. SECCIÓN 1: Crear cuenta en cmym.cuentas
            $cuentaStringId = generateBase64UrlId(22); // Generar ID único string
            
            $cuentaData = [
                'id' => $cuentaStringId, // ID string para relaciones
                'nick' => $request->nick,
                'dni' => $request->dni,
                'email' => $request->email,
                'contrasena' => Hash::make($request->contrasena),
                'tipo' => $request->tipo,
                'rol' => $request->rol_id, // Guardar el ID del rol
                'estado' => $request->estado,
                'empresas' => $request->empresas, // Array de IDs de empresas
                'created_at' => now()->timestamp * 1000, // Timestamp en milisegundos como las cuentas existentes
                'updated_at' => now()->timestamp * 1000
            ];
            
            Log::info('Insertando cuenta en cmym.cuentas', $cuentaData);
            $cuentaId = DB::connection('mongodb_cmym')
                ->collection('cuentas')
                ->insertGetId($cuentaData);
            
            if (!$cuentaId) {
                throw new \Exception('Error al crear la cuenta en la base de datos');
            }
            
            Log::info('Cuenta creada en cmym.cuentas con _id: ' . $cuentaId . ' e id: ' . $cuentaStringId);
            
            // 2. SECCIÓN 2: Crear perfil en cmym.perfiles
            $perfilData = [
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'genero' => $request->genero,
                'ocupacion' => $request->ocupacion,
                'cuenta_id' => $cuentaStringId, // Usar el ID string para la relación
                'firma' => $request->firma,
                'piefirma' => $request->piefirma,
                'licencia' => $request->licencia,
                'created_at' => now()->timestamp * 1000, // Timestamp en milisegundos
                'updated_at' => now()->timestamp * 1000
            ];
            
            Log::info('Insertando perfil en cmym.perfiles', $perfilData);
            $perfilId = DB::connection('mongodb_cmym')
                ->collection('perfiles')
                ->insertGetId($perfilData);
                
            if (!$perfilId) {
                throw new \Exception('Error al crear el perfil en la base de datos');
            }
            
            Log::info('Perfil creado en cmym.perfiles con ID: ' . $perfilId);
            
            // 3. SECCIÓN 3: Obtener datos del rol y crear permisos en cmym.permisos
            $rolData = DB::connection('mongodb_cmym')
                ->collection('roles')
                ->where('_id', $request->rol_id)
                ->first();
            
            // Normalize rolData: it can be an array or stdClass depending on driver
            $rolTipo = $request->tipo;
            $rolModulos = ['dashboard'];
            $rolPermisos = ['read'];
            if ($rolData) {
                if (is_object($rolData)) {
                    $rolTipo = $rolData->tipo ?? $request->tipo;
                    $rolModulos = $rolData->modulos ?? ['dashboard'];
                    $rolPermisos = $rolData->permisos ?? ['read'];
                } elseif (is_array($rolData)) {
                    $rolTipo = $rolData['tipo'] ?? $request->tipo;
                    $rolModulos = $rolData['modulos'] ?? ['dashboard'];
                    $rolPermisos = $rolData['permisos'] ?? ['read'];
                }
            }

            // Ensure modulos/permisos are arrays
            if (!is_array($rolModulos)) {
                $decoded = @json_decode($rolModulos, true);
                $rolModulos = is_array($decoded) ? $decoded : ['dashboard'];
            }
            if (!is_array($rolPermisos)) {
                $decoded = @json_decode($rolPermisos, true);
                $rolPermisos = is_array($decoded) ? $decoded : ['read'];
            }
            
            if ($rolData) {
                $permisoData = [
                    'tipo' => $rolTipo ?? $request->tipo, // Traer tipo del rol
                    'modulo' => $rolModulos ?? ['dashboard'], // Traer módulos del rol
                    'acciones' => $rolPermisos ?? ['read'], // Traer permisos del rol
                    'cuenta_id' => $cuentaStringId, // Usar el ID string para la relación
                    'created_at' => now()->timestamp * 1000, // Timestamp en milisegundos
                    'updated_at' => now()->timestamp * 1000
                ];
                
                Log::info('Insertando permisos en cmym.permisos', $permisoData);
                $permisoId = DB::connection('mongodb_cmym')
                    ->collection('permisos')
                    ->insertGetId($permisoData);
                    
                if (!$permisoId) {
                    throw new \Exception('Error al crear los permisos en la base de datos');
                }
                
                Log::info('Permisos creados en cmym.permisos con ID: ' . $permisoId);
            } else {
                Log::warning('No se encontró el rol con ID: ' . $request->rol_id);
            }
            
            Log::info('Cuenta creada exitosamente. ID: ' . $cuentaId);
            
            return redirect()->route('usuarios.cuentas.index')
                ->with('success', 'Cuenta creada exitosamente. Se han guardado los datos en las tres secciones: cuenta, perfil y permisos.');
                
        } catch (\Exception $e) {
            Log::error('Error al crear cuenta: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al crear la cuenta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Editar cuenta existente
     */
    public function editarCuenta(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nick' => 'required|string|max:255|unique:mongodb_cmym.cuentas,nick,' . $id . ',id',
            'email' => 'required|email|unique:mongodb_cmym.cuentas,email,' . $id . ',id',
            'dni' => 'required|string|max:20|unique:mongodb_cmym.cuentas,dni,' . $id . ',id',
            'rol' => 'required|in:' . implode(',', Cuenta::$roles),
            'tipo' => 'required|in:' . implode(',', Cuenta::$tipos),
            'estado' => 'required|in:activa,suspendida,inactiva',
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'genero' => 'nullable|in:' . implode(',', Perfil::$generos),
            'ocupacion' => 'nullable|string|max:255',
            'contrasena' => 'nullable|string|min:8'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            $cuenta = Cuenta::find($id);
            if (!$cuenta) {
                return back()->withErrors(['error' => 'Cuenta no encontrada']);
            }

            // Verificar permisos - solo SuperAdmin puede editar cualquier cuenta
            if (!$isSuperAdmin && (!tieneAccesoEmpresa($cuenta, $empresaId))) {
                return back()->withErrors(['error' => 'No tienes permisos para editar esta cuenta']);
            }

            // Actualizar datos de la cuenta según schema de Node.js
            $cuenta->nick = $request->nick;
            $cuenta->email = $request->email;
            $cuenta->dni = $request->dni;
            $cuenta->rol = $request->rol;
            $cuenta->tipo = $request->tipo;
            $cuenta->estado = $request->estado;
            $cuenta->empleado_id = $request->empleado_id ?? $cuenta->empleado_id;
            $cuenta->centro_key = $request->centro_key ?? $cuenta->centro_key;

            // Cambiar contraseña solo si se proporciona
            if ($request->filled('contrasena')) {
                $cuenta->contrasena = hashPassword($request->contrasena);
            }

            // Actualizar empresas (solo SuperAdmin)
            if ($isSuperAdmin && $request->has('empresas')) {
                $cuenta->empresas = $request->empresas;
            }

            $cuenta->save();

            // Actualizar perfil asociado según schema de Node.js
            $perfil = Perfil::where('cuenta_id', $cuenta->id)->first();
            if ($perfil) {
                $perfil->nombre = $request->nombre ?? $perfil->nombre;
                $perfil->apellido = $request->apellido ?? $perfil->apellido;
                $perfil->genero = $request->genero ?? $perfil->genero;
                $perfil->ocupacion = $request->ocupacion ?? $perfil->ocupacion;
                $perfil->modulos = getModulosPorRol($request->rol)[0] ?? 'dashboard'; // Schema usa string
                $perfil->permisos = getPermisosPorRol($request->rol)[0] ?? 'read'; // Schema usa string
                $perfil->save();
            }

            // Actualizar permisos según el nuevo rol
            Permiso::where('cuenta_id', $cuenta->id)->delete();
            Permiso::crearPermisosPorRol($cuenta->id, $request->rol, $request->tipo);

            // Registrar notificación
            if ($empresaId) {
                Notificacion::crearNotificacionSistema(
                    $empresaId,
                    'Cuenta actualizada',
                    "Se ha actualizado la cuenta: {$request->nick}",
                    'administracion'
                );
            }

            DB::commit();
            
            return redirect()->route('usuarios.cuentas.index')
                           ->with('success', 'Cuenta actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error actualizando cuenta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar la cuenta: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Eliminar cuenta (soft delete)
     */
    public function eliminarCuenta($id)
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            $cuenta = Cuenta::find($id);
            if (!$cuenta) {
                return back()->withErrors(['error' => 'Cuenta no encontrada']);
            }

            // Verificar permisos
            if (!$isSuperAdmin && (!tieneAccesoEmpresa($cuenta, $empresaId))) {
                return back()->withErrors(['error' => 'No tienes permisos para eliminar esta cuenta']);
            }

            // Cambiar estado a inactiva en lugar de eliminar (según schema de Node.js)
            $cuenta->estado = 'inactiva';
            $cuenta->save();

            // Registrar notificación
            if ($empresaId) {
                Notificacion::crearNotificacionSistema(
                    $empresaId,
                    'Cuenta desactivada',
                    "Se ha desactivado la cuenta: {$cuenta->nick}",
                    'administracion'
                );
            }

            return redirect()->route('usuarios.cuentas.index')
                           ->with('success', 'Cuenta desactivada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error eliminando cuenta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al desactivar la cuenta: ' . $e->getMessage()]);
        }
    }

    /**
     * Activar/Suspender cuenta
     */
    public function cambiarEstadoCuenta(Request $request, $id)
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            $cuenta = Cuenta::find($id);
            if (!$cuenta) {
                return response()->json(['error' => 'Cuenta no encontrada'], 404);
            }

            // Verificar permisos
            if (!$isSuperAdmin && (!tieneAccesoEmpresa($cuenta, $empresaId))) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }

            $nuevoEstado = $request->estado;
            if (!in_array($nuevoEstado, ['activa', 'suspendida', 'inactiva'])) {
                return response()->json(['error' => 'Estado inválido'], 400);
            }

            $cuenta->estado = $nuevoEstado;
            $cuenta->save();

            // Registrar notificación
            if ($empresaId) {
                Notificacion::crearNotificacionSistema(
                    $empresaId,
                    'Estado de cuenta cambiado',
                    "El estado de la cuenta {$cuenta->nick} cambió a: {$nuevoEstado}",
                    'administracion'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado de cuenta actualizado',
                'estado' => $nuevoEstado
            ]);

        } catch (\Exception $e) {
            Log::error('Error cambiando estado de cuenta: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Submódulo: Gestión de Roles
     */
    public function rolesIndex()
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            // Los roles del sistema son únicos (no por empresa)
            $roles = Rol::where('empresa_id', null) // Roles del sistema
                ->where('activo', true)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Roles del sistema predefinidos
            $rolesSistema = Rol::$rolesConfig;

            // Estadísticas reales - contar usuarios asignados a cada rol
            $stats = [
                'total_roles' => Rol::where('empresa_id', null)->where('activo', true)->count(),
                
                'roles_activos' => Rol::where('empresa_id', null)->where('activo', true)->count(),
                
                'permisos_totales' => collect(Rol::$rolesConfig)->sum(function($rol) {
                    return count($rol['permisos'] ?? []);
                }),
                
                'asignaciones' => Perfil::whereNotNull('rol_id')->whereNotNull('cuenta_id')->count()
            ];

            return view('admin.gestion-administrativa.roles.index', compact('roles', 'rolesSistema', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error en rolesIndex: ' . $e->getMessage());
           
        }
    }

    /**
     * Submódulo: Gestión de Perfiles
     */
    public function perfilesIndex()
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);
            
            // Los perfiles están asociados a cuentas individuales
            $perfiles = Perfil::with(['cuenta', 'rol'])
                ->when($empresaId, function($query) use ($empresaId) {
                    return $query->whereHas('cuenta', function($q) use ($empresaId) {
                        $q->where('empresas', $empresaId);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Estadísticas reales
            $stats = [
                'total_perfiles' => Perfil::when($empresaId, function($query) use ($empresaId) {
                    return $query->whereHas('cuenta', function($q) use ($empresaId) {
                        $q->where('empresas', $empresaId);
                    });
                })->count(),
                
                'perfiles_activos' => Perfil::when($empresaId, function($query) use ($empresaId) {
                    return $query->whereHas('cuenta', function($q) use ($empresaId) {
                        $q->where('empresas', $empresaId)->where('estado', 'activa');
                    });
                })->count(),
                
                'permisos_totales' => Permiso::when($empresaId, function($query) use ($empresaId) {
                    return $query->whereHas('cuenta', function($q) use ($empresaId) {
                        $q->where('empresas', $empresaId);
                    });
                })->count(),
                
                'asignaciones' => Perfil::whereNotNull('rol_id')->whereNotNull('cuenta_id')->count()
            ];

            return view('admin.gestion-administrativa.perfiles.index', compact('perfiles', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error en perfilesIndex: ' . $e->getMessage());
            // Devolver un paginador vacío para que la vista pueda llamar a hasPages() sin errores
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            return view('admin.gestion-administrativa.perfiles.index', [
                'perfiles' => $emptyPaginator,
                'stats' => [
                    'total_perfiles' => 0,
                    'perfiles_activos' => 0,
                    'permisos_totales' => 0,
                    'asignaciones' => 0
                ]
            ]);
        }
    }

    /**
     * Submódulo: Gestión de Permisos
     */
    public function permisosIndex()
    {
        try {
            $empresaId = session('empresa_id');
            $permisos = Permiso::with(['cuenta'])
                ->when($empresaId, function($query) use ($empresaId) {
                    return $query->whereHas('cuenta', function($q) use ($empresaId) {
                        $q->where('empresas', $empresaId);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $modulos = ['dashboard', 'administracion', 'hallazgos', 'psicosocial', 'configuracion', 'informes'];
            $tiposPermiso = ['interna', 'cliente', 'profesional', 'crm-cliente', 'usuario'];

            return view('admin.gestion-administrativa.permisos.index', compact('permisos', 'modulos', 'tiposPermiso'));
        } catch (\Exception $e) {
            Log::error('Error en permisosIndex: ' . $e->getMessage());
            return view('admin.gestion-administrativa.permisos.index', [
                'permisos' => collect([]),
                'modulos' => [],
                'tiposPermiso' => []
            ]);
        }
    }

    /**
     * Mostrar detalle de una cuenta (vista)
     */
    public function mostrarCuenta($id)
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);

            $cuenta = Cuenta::with(['perfil', 'permisos'])->find($id);
            if (!$cuenta) {
                return redirect()->route('usuarios.cuentas.index')->withErrors(['error' => 'Cuenta no encontrada']);
            }

            if (!$isSuperAdmin && (!tieneAccesoEmpresa($cuenta, $empresaId))) {
                return redirect()->route('usuarios.cuentas.index')->withErrors(['error' => 'No tienes permisos para ver esta cuenta']);
            }

            return view('admin.gestion-administrativa.cuentas.show', compact('cuenta'));
        } catch (\Exception $e) {
            Log::error('Error en mostrarCuenta: ' . $e->getMessage());
            return redirect()->route('usuarios.cuentas.index')->withErrors(['error' => 'Error al cargar la cuenta']);
        }
    }

    /**
     * Mostrar formulario de edición para una cuenta
     */
    public function mostrarEditarCuenta($id)
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);

            $cuenta = Cuenta::with(['perfil'])->find($id);
            if (!$cuenta) {
                return redirect()->route('usuarios.cuentas.index')->withErrors(['error' => 'Cuenta no encontrada']);
            }

            if (!$isSuperAdmin && (!tieneAccesoEmpresa($cuenta, $empresaId))) {
                return redirect()->route('usuarios.cuentas.index')->withErrors(['error' => 'No tienes permisos para editar esta cuenta']);
            }

            $roles = Cuenta::$roles;
            $estados = Cuenta::$estados;

            return view('admin.gestion-administrativa.cuentas.edit', compact('cuenta', 'roles', 'estados'));
        } catch (\Exception $e) {
            Log::error('Error en mostrarEditarCuenta: ' . $e->getMessage());
            return redirect()->route('usuarios.cuentas.index')->withErrors(['error' => 'Error al cargar el formulario de edición']);
        }
    }

    /**
     * Gestión Administrativa de Empresa - dashboard
     */
    public function empresaIndex()
    {
        try {
            // Estadísticas por defecto
            $totalEmpleados = 0;
            $totalAreas = 0;
            $totalCentros = 0;
            $totalProcesos = 0;

            // Obtener estadísticas desde MongoDB
            try {
                // Contar empleados
                $totalEmpleados = DB::connection('mongodb_empresas')
                    ->collection('empleados')
                    ->count();
                
                // Contar áreas
                $totalAreas = DB::connection('mongodb_empresas')
                    ->collection('areas')
                    ->count();
                
                // Contar centros
                $totalCentros = DB::connection('mongodb_empresas')
                    ->collection('centros')
                    ->count();
                
                // Contar procesos
                $totalProcesos = DB::connection('mongodb_empresas')
                    ->collection('procesos')
                    ->count();
                    
            } catch (\Exception $e) {
                Log::warning('Error obteniendo estadísticas de empresa: ' . $e->getMessage());
                // Las estadísticas ya están en 0 por defecto
            }

            return view('admin.gestion-administrativa.empresa.index', compact(
                'totalEmpleados',
                'totalAreas', 
                'totalCentros',
                'totalProcesos'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error en empresaIndex: ' . $e->getMessage());
            
            // En caso de error, enviar estadísticas vacías
            $totalEmpleados = 0;
            $totalAreas = 0;
            $totalCentros = 0;
            $totalProcesos = 0;
            
            return view('admin.gestion-administrativa.empresa.index', compact(
                'totalEmpleados',
                'totalAreas',
                'totalCentros',
                'totalProcesos'
            ));
        }
    }

    public function empleadosIndex()
    {
        try {
            $estadisticas = [
                'total' => 0,
                'activos' => 0,
                'inactivos' => 0,
                'porcentaje_activos' => 0
            ];
            
            $empleados = [];
            $areas = [];
            $centros = [];

            try {
                // Obtener empleados desde MongoDB
                $empleados = DB::connection('mongodb_empresas')
                    ->collection('empleados')
                    ->get();
                
                // Obtener áreas para el filtro y relaciones
                $areas = DB::connection('mongodb_empresas')
                    ->collection('areas')
                    ->get();
                
                // Obtener centros para el filtro y relaciones
                $centros = DB::connection('mongodb_empresas')
                    ->collection('centros')
                    ->get();
                
                $total = $empleados->count();
                $activos = $empleados->where('activo', true)->count();
                
                $estadisticas['total'] = $total;
                $estadisticas['activos'] = $activos;
                $estadisticas['inactivos'] = $total - $activos;
                $estadisticas['porcentaje_activos'] = $total > 0 ? round(($activos / $total) * 100, 2) : 0;
                
            } catch (\Exception $e) {
                Log::warning('Error obteniendo datos de empleados: ' . $e->getMessage());
            }

            return view('admin.gestion-administrativa.empresa.empleados.index', compact('estadisticas', 'empleados', 'areas', 'centros'));
        } catch (\Exception $e) {
            Log::error('Error en empleadosIndex: ' . $e->getMessage());
            
            $estadisticas = [
                'total' => 0,
                'activos' => 0,
                'inactivos' => 0,
                'porcentaje_activos' => 0
            ];
            $empleados = [];
            $areas = [];
            $centros = [];
            
            return view('admin.gestion-administrativa.empresa.empleados.index', compact('estadisticas', 'empleados', 'areas', 'centros'));
        }
    }

    public function empresasIndex()
    {
        try {
            $empresas = [];
            $totalEmpresas = 0;

            try {
                $empresas = DB::connection('mongodb_empresas')
                    ->collection('empresas')
                    ->get();
                $totalEmpresas = $empresas->count();
                
            } catch (\Exception $e) {
                Log::warning('Error obteniendo empresas: ' . $e->getMessage());
            }

            return view('admin.gestion-administrativa.empresa.empresas.index', compact('empresas', 'totalEmpresas'));
        } catch (\Exception $e) {
            Log::error('Error en empresasIndex: ' . $e->getMessage());
            
            return view('admin.gestion-administrativa.empresa.empresas.index', [
                'empresas' => [],
                'totalEmpresas' => 0
            ]);
        }
    }

    public function areasIndex()
    {
        try {
            $areas = [];
            $totalAreas = 0;

            try {
                $areas = DB::connection('mongodb_empresas')
                    ->collection('areas')
                    ->get();
                $totalAreas = $areas->count();
                
            } catch (\Exception $e) {
                Log::warning('Error obteniendo áreas: ' . $e->getMessage());
            }

            return view('admin.gestion-administrativa.empresa.areas.index', compact('areas', 'totalAreas'));
        } catch (\Exception $e) {
            Log::error('Error en areasIndex: ' . $e->getMessage());
            
            return view('admin.gestion-administrativa.empresa.areas.index', [
                'areas' => [],
                'totalAreas' => 0
            ]);
        }
    }

    public function centrosIndex()
    {
        try {
            $centros = [];
            $totalCentros = 0;

            try {
                $centros = DB::connection('mongodb_empresas')
                    ->collection('centros')
                    ->get();
                $totalCentros = $centros->count();
                
            } catch (\Exception $e) {
                Log::warning('Error obteniendo centros: ' . $e->getMessage());
            }

            return view('admin.gestion-administrativa.empresa.centros.index', compact('centros', 'totalCentros'));
        } catch (\Exception $e) {
            Log::error('Error en centrosIndex: ' . $e->getMessage());
            
            return view('admin.gestion-administrativa.empresa.centros.index', [
                'centros' => [],
                'totalCentros' => 0
            ]);
        }
    }

    public function ciudadesIndex()
    {
        try {
            $ciudades = [];
            $totalCiudades = 0;

            try {
                $ciudades = DB::connection('mongodb_empresas')
                    ->collection('ciudades')
                    ->get();
                $totalCiudades = $ciudades->count();
                
            } catch (\Exception $e) {
                Log::warning('Error obteniendo ciudades: ' . $e->getMessage());
            }

            return view('admin.gestion-administrativa.empresa.ciudades.index', compact('ciudades', 'totalCiudades'));
        } catch (\Exception $e) {
            Log::error('Error en ciudadesIndex: ' . $e->getMessage());
            
            return view('admin.gestion-administrativa.empresa.ciudades.index', [
                'ciudades' => [],
                'totalCiudades' => 0
            ]);
        }
    }

    public function procesosIndex()
    {
        try {
            $procesos = [];
            $totalProcesos = 0;

            try {
                $procesos = DB::connection('mongodb_empresas')
                    ->collection('procesos')
                    ->get();
                $totalProcesos = $procesos->count();
                
            } catch (\Exception $e) {
                Log::warning('Error obteniendo procesos: ' . $e->getMessage());
            }

            return view('admin.gestion-administrativa.empresa.procesos.index', compact('procesos', 'totalProcesos'));
        } catch (\Exception $e) {
            Log::error('Error en procesosIndex: ' . $e->getMessage());
            
            return view('admin.gestion-administrativa.empresa.procesos.index', [
                'procesos' => [],
                'totalProcesos' => 0
            ]);
        }
    }

    /**
     * Helper: Obtener módulos según rol
     */
    private function getModulosPorRol($rol)
    {
        $roleConfig = Rol::$rolesConfig[$rol] ?? [];
        return $roleConfig['modulos'] ?? ['dashboard'];
    }

    /**
     * Helper: Obtener permisos según rol
     */
    private function getPermisosPorRol($rol)
    {
        $roleConfig = Rol::$rolesConfig[$rol] ?? [];
        return $roleConfig['permisos'] ?? ['read'];
    }

    /**
     * Helper: Asignar permisos automáticos según el rol
     */
    private function asignarPermisosPorRol($cuentaId, $rol, $tipo)
    {
        $roleConfig = Rol::$rolesConfig[$rol] ?? [];
        $modulos = $roleConfig['modulos'] ?? ['dashboard'];
        $acciones = $roleConfig['permisos'] ?? ['read'];

        foreach ($modulos as $modulo) {
            Permiso::create([
                'id' => generateBase64UrlId(),
                'cuenta_id' => $cuentaId,
                'modulo' => $modulo,
                'tipo' => $tipo,
                'acciones' => $acciones,
                'link' => "/v2.0/permisos/" . generateBase64UrlId()
            ]);
        }
    }

    /**
     * Helper: Actualizar permisos por rol
     */
    private function actualizarPermisosPorRol($cuentaId, $rol, $tipo)
    {
        // Eliminar permisos existentes
        Permiso::where('cuenta_id', $cuentaId)->delete();
        
        // Asignar nuevos permisos
        $this->asignarPermisosPorRol($cuentaId, $rol, $tipo);
    }

    /**
     * API: Validar sesión y permisos
     */
    public function validarSesion(Request $request)
    {
        try {
            $token = $request->header('Authorization') ?? $request->input('token');
            
            if (!$token) {
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            $sesion = Sesion::findActiveByToken($token);
            
            if (!$sesion || !$sesion->isActive()) {
                return response()->json(['error' => 'Sesión inválida o expirada'], 401);
            }

            // Actualizar actividad
            $sesion->updateActivity();

            // Obtener datos de la cuenta con permisos
            $cuenta = $sesion->cuenta()->with(['permisos'])->first();
            
            if (!$cuenta) {
                return response()->json(['error' => 'Cuenta no encontrada'], 404);
            }

            return response()->json([
                'valida' => true,
                'cuenta' => [
                    'id' => $cuenta->id,
                    'nick' => $cuenta->nick,
                    'email' => $cuenta->email,
                    'rol' => $cuenta->rol,
                    'tipo' => $cuenta->tipo,
                    'estado' => $cuenta->estado,
                    'empresas' => $cuenta->empresas
                ],
                'permisos' => $cuenta->permisos->map(function($p) {
                    return [
                        'modulo' => $p->modulo,
                        'acciones' => $p->acciones
                    ];
                }),
                'sesion' => [
                    'inicio' => $sesion->inicio_sesion,
                    'ultima_actividad' => $sesion->ultima_actividad
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en validarSesion: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Crear un nuevo rol
     */
    public function crearRol(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'empresa_id' => 'nullable|string',
                'modulos' => 'required|array',
                'modulos.*' => 'string|in:empleados,hallazgos,psicosocial,informes,configuracion'
            ]);

            $rol = new Rol();
            $rol->nombre = $request->nombre;
            $rol->descripcion = $request->descripcion;
            $rol->empresa_id = $request->empresa_id;
            $rol->modulos = $request->modulos;
            $rol->activo = true;
            $rol->save();

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente',
                'rol' => $rol
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando rol: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Crear un nuevo permiso
     */
    public function crearPermiso(Request $request)
    {
        try {
            $request->validate([
                'modulo' => 'required|string|in:empleados,hallazgos,psicosocial,informes,configuracion',
                'acciones' => 'required|array',
                'acciones.*' => 'string|in:crear,leer,actualizar,eliminar,exportar',
                'descripcion' => 'nullable|string'
            ]);

            $permiso = new Permiso();
            $permiso->modulo = $request->modulo;
            $permiso->acciones = $request->acciones;
            $permiso->descripcion = $request->descripcion;
            $permiso->activo = true;
            $permiso->save();

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado exitosamente',
                'permiso' => $permiso
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando permiso: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Asignar permisos por rol (API)
     */
    public function asignarPermisosRol(Request $request)
    {
        try {
            $request->validate([
                'rol_nombre' => 'required|string',
                'permisos' => 'required|array',
                'permisos.*.modulo' => 'required|string',
                'permisos.*.acciones' => 'required|array'
            ]);

            $rol = Rol::where('nombre', $request->rol_nombre)->first();
            
            if (!$rol) {
                return response()->json(['error' => 'Rol no encontrado'], 404);
            }

            // Crear/actualizar permisos para este rol
            foreach ($request->permisos as $permisoData) {
                $permiso = Permiso::updateOrCreate(
                    [
                        'modulo' => $permisoData['modulo'],
                        'rol_id' => $rol->id
                    ],
                    [
                        'acciones' => $permisoData['acciones'],
                        'descripcion' => "Permisos para {$permisoData['modulo']} - {$rol->nombre}",
                        'activo' => true
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Permisos asignados exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error asignando permisos: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener datos de sesiones para AJAX
     */
    public function sesionesData(Request $request)
    {
        try {
            $sesiones = Sesion::with('cuenta:id,nick,email,rol')
                ->where('activa', true)
                ->orderBy('ultima_actividad', 'desc')
                ->get()
                ->map(function($sesion) {
                    return [
                        'id' => $sesion->id,
                        'usuario' => $sesion->cuenta->nick ?? 'Desconocido',
                        'email' => $sesion->cuenta->email ?? '',
                        'rol' => $sesion->cuenta->rol ?? 'usuario',
                        'ip' => $sesion->ip,
                        'inicio' => $sesion->inicio_sesion->format('d/m/Y H:i:s'),
                        'ultima_actividad' => $sesion->ultima_actividad->diffForHumans(),
                        'tiempo_activa' => $sesion->inicio_sesion->diffForHumans($sesion->ultima_actividad, true),
                        'es_activa' => $sesion->isActive()
                    ];
                });

            return response()->json(['sesiones' => $sesiones]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos de sesiones: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Refrescar lista de sesiones
     */
    public function refreshSesiones()
    {
        return $this->sesionesData(request());
    }

    /**
     * Expirar todas las sesiones
     */
    public function expireAllSesiones()
    {
        try {
            $count = Sesion::where('activa', true)->update([
                'activa' => false,
                'fin_sesion' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se expiraron {$count} sesiones activas"
            ]);
        } catch (\Exception $e) {
            Log::error('Error expirando todas las sesiones: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Expirar una sesión específica
     */
    public function expireSesion($id)
    {
        try {
            $sesion = Sesion::find($id);
            
            if (!$sesion) {
                return response()->json(['error' => 'Sesión no encontrada'], 404);
            }

            $sesion->close();

            return response()->json([
                'success' => true,
                'message' => 'Sesión expirada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error expirando sesión: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Eliminar una sesión específica
     */
    public function deleteSesion($id)
    {
        try {
            $sesion = Sesion::find($id);
            
            if (!$sesion) {
                return response()->json(['error' => 'Sesión no encontrada'], 404);
            }

            $sesion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error eliminando sesión: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener detalles de una sesión específica
     */
    public function getSesionDetails($id)
    {
        try {
            $sesion = Sesion::with('cuenta:id,nick,email,rol,estado,fecha_creacion')
                ->find($id);
            
            if (!$sesion) {
                return response()->json(['error' => 'Sesión no encontrada'], 404);
            }

            return response()->json([
                'details' => [
                    'id' => $sesion->id,
                    'token' => substr($sesion->token, 0, 20) . '...',
                    'usuario' => [
                        'nick' => $sesion->cuenta->nick,
                        'email' => $sesion->cuenta->email,
                        'rol' => $sesion->cuenta->rol,
                        'estado' => $sesion->cuenta->estado,
                        'registrado' => $sesion->cuenta->fecha_creacion->format('d/m/Y H:i:s')
                    ],
                    'sesion' => [
                        'inicio' => $sesion->inicio_sesion->format('d/m/Y H:i:s'),
                        'ultima_actividad' => $sesion->ultima_actividad->format('d/m/Y H:i:s'),
                        'duracion' => $sesion->inicio_sesion->diffForHumans($sesion->ultima_actividad, true),
                        'ip' => $sesion->ip,
                        'user_agent' => $sesion->user_agent,
                        'activa' => $sesion->activa,
                        'expira_en' => $sesion->isActive() ? 
                            $sesion->ultima_actividad->addMinutes(config('session.lifetime'))->diffForHumans() : 
                            'Expirada'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo detalles de sesión: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}
