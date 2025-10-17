<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Usuario;
use App\Models\Auth\Perfil;
use App\Models\Empresas\Empresa;
use App\Models\Empresas\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of users
     */    public function index()
    {
        try {
            $usuarios = Usuario::all()->sortByDesc('created_at')->chunk(20);

            $perfiles = Perfil::where('activo', true)->get();
            $empresas = Empresa::where('activo', true)->get();

            return view('modules.auth.users.index', compact('usuarios', 'perfiles', 'empresas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new user
     */    public function create()
    {
        try {
            $perfiles = Perfil::where('activo', true)->get();
            $empresas = Empresa::where('activo', true)->get();
            $empleados = Empleado::where('activo', true)->get();

            return view('modules.auth.users.create', compact('perfiles', 'empresas', 'empleados'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'perfil_id' => 'required|string',
            'empresa_id' => 'required|string',
            'empleado_id' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }        try {
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'perfil_id' => $request->perfil_id,
                'empresa_id' => $request->empresa_id,
                'empleado_id' => $request->empleado_id ?: null,
                'telefono' => $request->telefono,
                'activo' => $request->has('activo'),
                'email_verificado' => false,
                'ultimo_acceso' => null,
                'configuracion' => [
                    'tema' => 'light',
                    'idioma' => 'es',
                    'notificaciones' => true
                ],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('auth.users.index')
                ->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear usuario: ' . $e->getMessage())
                ->withInput();
        }
    }    /**
     * Display the specified user
     */    public function show($id)
    {
        try {
            $usuario = Usuario::where('_id', $id)->first();

            if (!$usuario) {
                return back()->with('error', 'Usuario no encontrado');
            }

            $sesiones = $usuario->sesiones()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('modules.auth.users.show', compact('usuario', 'sesiones'));
        } catch (\Exception $e) {
            return back()->with('error', 'Usuario no encontrado');
        }
    }/**
     * Show the form for editing the specified user
     */    public function edit($id)
    {
        try {
            $usuario = Usuario::where('_id', $id)->first();

            if (!$usuario) {
                return back()->with('error', 'Usuario no encontrado');
            }

            $perfiles = Perfil::where('activo', true)->get();
            $empresas = Empresa::where('activo', true)->get();
            $empleados = Empleado::where('activo', true)->get();

            return view('modules.auth.users.edit', compact('usuario', 'perfiles', 'empresas', 'empleados'));
        } catch (\Exception $e) {
            return back()->with('error', 'Usuario no encontrado');
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'perfil_id' => 'required|string',
            'empresa_id' => 'required|string',
            'empleado_id' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }        try {
            $usuario = Usuario::where('_id', $id)->first();
            
            if (!$usuario) {
                return back()->with('error', 'Usuario no encontrado')->withInput();
            }
            
            $updateData = [
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'perfil_id' => $request->perfil_id,
                'empresa_id' => $request->empresa_id,
                'empleado_id' => $request->empleado_id ?: null,
                'telefono' => $request->telefono,
                'activo' => $request->has('activo'),
                'updated_at' => now()
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            
            $usuario->update($updateData);

            return redirect()->route('auth.users.index')
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar usuario: ' . $e->getMessage())
                ->withInput();
        }
    }    /**
     * Remove the specified user from storage
     */    public function destroy($id)
    {        try {
            $usuario = Usuario::where('_id', $id)->first();
            
            if (!$usuario) {
                return back()->with('error', 'Usuario no encontrado');
            }
            
            // Soft delete - just mark as inactive
            $usuario->update([
                'activo' => false,
                'updated_at' => now()
            ]);

            return redirect()->route('auth.users.index')
                ->with('success', 'Usuario desactivado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar usuario: ' . $e->getMessage());
        }
    }/**
     * Toggle user status (activate/deactivate)
     */    public function toggleStatus($id)
    {
        try {            $usuario = Usuario::where('_id', $id)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }
              $usuario->update([
                'activo' => !$usuario->activo,
                'updated_at' => now()
            ]);

            $status = $usuario->activo ? 'activado' : 'desactivado';
            return response()->json([
                'success' => true,
                'message' => "Usuario {$status} exitosamente",
                'status' => $usuario->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users data for DataTables
     */    public function getData(Request $request)
    {
        try {
            $usuarios = Usuario::all();

            // Search functionality
            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $usuarios = $usuarios->filter(function($usuario) use ($search) {
                    return stripos($usuario->nombre, $search) !== false ||
                           stripos($usuario->apellido, $search) !== false ||
                           stripos($usuario->email, $search) !== false;
                });
            }

            // Filter by profile
            if ($request->has('perfil_filter') && $request->perfil_filter) {
                $usuarios = $usuarios->where('perfil_id', $request->perfil_filter);
            }

            // Filter by company
            if ($request->has('empresa_filter') && $request->empresa_filter) {
                $usuarios = $usuarios->where('empresa_id', $request->empresa_filter);
            }

            // Filter by status
            if ($request->has('status_filter') && $request->status_filter !== '') {
                $usuarios = $usuarios->where('activo', (bool) $request->status_filter);
            }

            $total = $usuarios->count();

            // Ordering
            if ($request->has('order')) {
                $orderColumn = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                
                $columns = ['nombre', 'email', 'perfil_id', 'empresa_id', 'activo', 'created_at'];
                if (isset($columns[$orderColumn])) {
                    $field = $columns[$orderColumn];
                    if ($orderDir === 'desc') {
                        $usuarios = $usuarios->sortByDesc($field);
                    } else {
                        $usuarios = $usuarios->sortBy($field);
                    }
                }
            } else {
                $usuarios = $usuarios->sortByDesc('created_at');
            }

            // Pagination
            if ($request->has('start') && $request->has('length')) {
                $usuarios = $usuarios->slice($request->start, $request->length);
            }

            $data = $usuarios->map(function($usuario) {
                return [
                    'id' => (string) $usuario->_id,
                    'nombre' => $usuario->nombre . ' ' . $usuario->apellido,
                    'email' => $usuario->email,
                    'perfil' => $usuario->perfil->nombre ?? 'Sin perfil',
                    'empresa' => $usuario->empresa->nombre ?? 'Sin empresa',
                    'activo' => $usuario->activo,
                    'ultimo_acceso' => $usuario->ultimo_acceso ? $usuario->ultimo_acceso->format('d/m/Y H:i') : 'Nunca',
                    'created_at' => $usuario->created_at->format('d/m/Y H:i')
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar datos: ' . $e->getMessage()
            ], 500);
        }
    }
}

