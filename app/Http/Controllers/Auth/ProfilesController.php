<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Perfil;
use App\Models\Auth\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilesController extends Controller
{
    /**
     * Display a listing of profiles
     */    public function index()
    {
        try {
            $perfiles = Perfil::all()->sortByDesc('created_at');
            $modulosDisponibles = Perfil::getAvailableModules();

            return view('modules.auth.profiles.index', compact('perfiles', 'modulosDisponibles'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar perfiles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new profile
     */    public function create()
    {
        try {
            $modulosDisponibles = Perfil::getAvailableModules();
            $permisosDisponibles = Perfil::getAvailablePermissions();
            $permisos = Permiso::all()->groupBy('modulo');

            return view('modules.auth.profiles.create', compact('modulosDisponibles', 'permisosDisponibles', 'permisos'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created profile
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:perfiles,nombre',
            'descripcion' => 'required|string|max:1000',
            'nivel' => 'required|integer|min:1|max:5',
            'modulos' => 'required|array|min:1',
            'modulos.*' => 'required|string',
            'permisos' => 'required|array|min:1',
            'permisos.*' => 'required|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }        try {
            $perfil = Perfil::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'nivel' => $request->nivel,
                'modulos' => $request->modulos,
                'permisos' => $request->permisos,
                'activo' => $request->has('activo'),
                'es_sistema' => false,
                'configuracion' => [
                    'dashboard' => [
                        'widgets' => [],
                        'layout' => 'default'
                    ],
                    'notificaciones' => [
                        'email' => true,
                        'sistema' => true
                    ]
                ],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('auth.profiles.index')
                ->with('success', 'Perfil creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified profile
     */    public function show($id)
    {
        try {
            $perfil = Perfil::where('_id', $id)->first();
            if (!$perfil) {
                return back()->with('error', 'Perfil no encontrado');
            }
            
            $usuarios = $perfil->usuarios()->paginate(10);
            $permisosAgrupados = collect($perfil->permisos)->groupBy(function($permiso) {
                return explode('.', $permiso)[0];
            });

            return view('modules.auth.profiles.show', compact('perfil', 'usuarios', 'permisosAgrupados'));
        } catch (\Exception $e) {
            return back()->with('error', 'Perfil no encontrado');
        }
    }

    /**
     * Show the form for editing the specified profile
     */    public function edit($id)
    {        try {
            $perfil = Perfil::where('_id', $id)->first();
            if (!$perfil) {
                return back()->with('error', 'Perfil no encontrado');
            }

            // Check if it's a system profile
            if ($perfil->es_sistema) {
                return back()->with('warning', 'No se puede editar un perfil de sistema');
            }

            $modulosDisponibles = Perfil::getAvailableModules();
            $permisosDisponibles = Perfil::getAvailablePermissions();
            $permisos = Permiso::all()->groupBy('modulo');

            return view('modules.auth.profiles.edit', compact('perfil', 'modulosDisponibles', 'permisosDisponibles', 'permisos'));
        } catch (\Exception $e) {
            return back()->with('error', 'Perfil no encontrado');
        }
    }

    /**
     * Update the specified profile
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:perfiles,nombre,' . $id,
            'descripcion' => 'required|string|max:1000',
            'nivel' => 'required|integer|min:1|max:5',
            'modulos' => 'required|array|min:1',
            'modulos.*' => 'required|string',
            'permisos' => 'required|array|min:1',
            'permisos.*' => 'required|string',
            'activo' => 'boolean'
        ]);        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }        try {
            $perfil = Perfil::where('_id', $id)->first();
            if (!$perfil) {
                return back()->with('error', 'Perfil no encontrado');
            }

            // Check if it's a system profile
            if ($perfil->es_sistema) {
                return back()->with('error', 'No se puede modificar un perfil de sistema');
            }

            $perfil->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'nivel' => $request->nivel,
                'modulos' => $request->modulos,
                'permisos' => $request->permisos,
                'activo' => $request->has('activo'),
                'updated_at' => now()
            ]);

            return redirect()->route('auth.profiles.index')
                ->with('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified profile from storage
     */    public function destroy($id)
    {
        try {            $perfil = Perfil::where('_id', $id)->first();
            if (!$perfil) {
                return back()->with('error', 'Perfil no encontrado');
            }

            // Check if it's a system profile
            if ($perfil->es_sistema) {
                return back()->with('error', 'No se puede eliminar un perfil de sistema');
            }

            // Check if profile has users
            $usersCount = $perfil->usuarios()->count();
            if ($usersCount > 0) {
                return back()->with('error', "No se puede eliminar el perfil porque tiene {$usersCount} usuarios asignados");
            }

            $perfil->delete();

            return redirect()->route('auth.profiles.index')
                ->with('success', 'Perfil eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar perfil: ' . $e->getMessage());
        }
    }

    /**
     * Toggle profile status (activate/deactivate)
     */    public function toggleStatus($id)
    {
        try {
            $perfil = Perfil::where('_id', $id)->first();
            if (!$perfil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil no encontrado'
                ], 404);
            }

            if ($perfil->es_sistema) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar el estado de un perfil de sistema'
                ], 400);            }

            $perfil->update([
                'activo' => !$perfil->activo,
                'updated_at' => now()
            ]);

            $status = $perfil->activo ? 'activado' : 'desactivado';
            return response()->json([
                'success' => true,
                'message' => "Perfil {$status} exitosamente",
                'status' => $perfil->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get profiles data for DataTables
     */    public function getData(Request $request)
    {
        try {
            $perfiles = Perfil::all();

            // Search functionality
            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $perfiles = $perfiles->filter(function($perfil) use ($search) {
                    return stripos($perfil->nombre, $search) !== false ||
                           stripos($perfil->descripcion, $search) !== false;
                });
            }

            // Filter by level
            if ($request->has('nivel_filter') && $request->nivel_filter) {
                $perfiles = $perfiles->where('nivel', $request->nivel_filter);
            }

            // Filter by status
            if ($request->has('status_filter') && $request->status_filter !== '') {
                $perfiles = $perfiles->where('activo', (bool) $request->status_filter);
            }

            $total = $perfiles->count();            // Ordering
            if ($request->has('order')) {
                $orderColumn = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                
                $columns = ['nombre', 'descripcion', 'nivel', 'activo', 'created_at'];
                if (isset($columns[$orderColumn])) {
                    $field = $columns[$orderColumn];
                    if ($orderDir === 'desc') {
                        $perfiles = $perfiles->sortByDesc($field);
                    } else {
                        $perfiles = $perfiles->sortBy($field);
                    }
                }
            } else {
                $perfiles = $perfiles->sortByDesc('created_at');
            }

            // Pagination
            if ($request->has('start') && $request->has('length')) {
                $perfiles = $perfiles->slice($request->start, $request->length);
            }

            $data = $perfiles->map(function($perfil) {
                return [
                    'id' => (string) $perfil->_id,
                    'nombre' => $perfil->nombre,
                    'descripcion' => $perfil->descripcion,
                    'nivel' => $perfil->nivel,
                    'modulos_count' => count($perfil->modulos),
                    'permisos_count' => count($perfil->permisos),
                    'usuarios_count' => $perfil->usuarios()->count(),
                    'activo' => $perfil->activo,
                    'es_sistema' => $perfil->es_sistema,
                    'created_at' => $perfil->created_at->format('d/m/Y H:i')
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

    /**
     * Get permissions for a specific module
     */    public function getModulePermissions($module)
    {
        try {
            $permisosDisponibles = Perfil::getAvailablePermissions();
            $permisos = $permisosDisponibles[$module] ?? [];

            $permisosDetalle = Permiso::all()->filter(function($permiso) use ($permisos) {
                return in_array($permiso->clave, $permisos);
            });

            return response()->json([
                'success' => true,
                'permisos' => $permisosDetalle->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar permisos: ' . $e->getMessage()
            ], 500);
        }
    }
}
