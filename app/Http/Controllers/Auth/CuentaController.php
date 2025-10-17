<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador centralizado para gestión de cuentas
 * Compatible con el schema de Node.js especificado
 */
class CuentaController extends Controller
{
    /**
     * Mostrar lista de cuentas con paginación
     */
    public function index(Request $request)
    {
        try {
            $query = Cuenta::query();

            // Filtros
            if ($request->filled('empresa_id')) {
                $query->porEmpresa($request->empresa_id);
            }

            if ($request->filled('rol')) {
                $query->porRol($request->rol);
            }

            if ($request->filled('estado')) {
                $query->porEstado($request->estado);
            }

            if ($request->filled('tipo')) {
                $query->porTipo($request->tipo);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('nick', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'email');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $cuentas = $query->paginate($perPage);

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuentas->items(),
                    'pagination' => [
                        'current_page' => $cuentas->currentPage(),
                        'per_page' => $cuentas->perPage(),
                        'total' => $cuentas->total(),
                        'last_page' => $cuentas->lastPage()
                    ]
                ]);
            }

            // Para vista web
            return view('admin.gestion-administrativa.cuentas.index', compact('cuentas'));

        } catch (\Exception $e) {
            Log::error('Error listando cuentas: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener cuentas'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al obtener cuentas']);
        }
    }

    /**
     * Mostrar formulario para crear nueva cuenta
     */
    public function create()
    {
        return view('admin.gestion-administrativa.cuentas.create', [
            'roles' => Cuenta::$roles,
            'estados' => Cuenta::$estados,
            'tipos' => Cuenta::$tipos
        ]);
    }

    /**
     * Almacenar nueva cuenta
     */
    public function store(Request $request)
    {
        $validator = $this->validateCuenta($request);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();
            
            // Preparar datos según schema Node.js
            $cuentaData = [
                'empleado_id' => $data['empleado_id'] ?? null,
                'nick' => $data['nick'] ?? null,
                'email' => $data['email'],
                'contrasena' => $data['contrasena'],
                'dni' => $data['dni'] ?? null,
                'rol' => $data['rol'] ?? 'usuario',
                'estado' => $data['estado'] ?? 'inactiva',
                'tipo' => $data['tipo'] ?? 'cliente',
                'empresas' => $data['empresas'] ?? [],
                'canales' => $data['canales'] ?? [],
                'centro_key' => $data['centro_key'] ?? null
            ];

            $cuenta = Cuenta::create($cuentaData);

            Log::info('Cuenta creada: ' . $cuenta->id, ['email' => $cuenta->email]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuenta->toNodejsArray(),
                    'message' => 'Cuenta creada exitosamente'
                ], 201);
            }

            return redirect()
                ->route('admin.cuentas.show', $cuenta->id)
                ->with('success', 'Cuenta creada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error creando cuenta: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear cuenta'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al crear cuenta'])->withInput();
        }
    }

    /**
     * Mostrar una cuenta específica
     */
    public function show($id)
    {
        try {
            $cuenta = Cuenta::where('id', $id)->firstOrFail();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuenta->toNodejsArray()
                ]);
            }

            return view('admin.gestion-administrativa.cuentas.show', compact('cuenta'));

        } catch (\Exception $e) {
            Log::error('Error obteniendo cuenta: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuenta no encontrada'
                ], 404);
            }

            return back()->withErrors(['error' => 'Cuenta no encontrada']);
        }
    }

    /**
     * Mostrar formulario para editar cuenta
     */
    public function edit($id)
    {
        try {
            $cuenta = Cuenta::where('id', $id)->firstOrFail();

            return view('admin.gestion-administrativa.cuentas.edit', [
                'cuenta' => $cuenta,
                'roles' => Cuenta::$roles,
                'estados' => Cuenta::$estados,
                'tipos' => Cuenta::$tipos
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Cuenta no encontrada']);
        }
    }

    /**
     * Actualizar cuenta
     */
    public function update(Request $request, $id)
    {
        try {
            $cuenta = Cuenta::where('id', $id)->firstOrFail();

            $validator = $this->validateCuenta($request, $cuenta);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();

            // Actualizar solo campos modificados
            $updateData = [];
            foreach ($data as $key => $value) {
                if ($key !== 'contrasena' || !empty($value)) {
                    $updateData[$key] = $value;
                }
            }

            $cuenta->update($updateData);

            Log::info('Cuenta actualizada: ' . $cuenta->id, ['email' => $cuenta->email]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuenta->fresh()->toNodejsArray(),
                    'message' => 'Cuenta actualizada exitosamente'
                ]);
            }

            return redirect()
                ->route('admin.cuentas.show', $cuenta->id)
                ->with('success', 'Cuenta actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error actualizando cuenta: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar cuenta'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al actualizar cuenta'])->withInput();
        }
    }

    /**
     * Eliminar cuenta (soft delete)
     */
    public function destroy($id)
    {
        try {
            $cuenta = Cuenta::where('id', $id)->firstOrFail();

            // En lugar de eliminar, cambiar estado a inactiva
            $cuenta->update(['estado' => 'inactiva']);

            Log::info('Cuenta desactivada: ' . $cuenta->id, ['email' => $cuenta->email]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cuenta desactivada exitosamente'
                ]);
            }

            return redirect()
                ->route('admin.cuentas.index')
                ->with('success', 'Cuenta desactivada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error desactivando cuenta: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al desactivar cuenta'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al desactivar cuenta']);
        }
    }

    /**
     * Activar/Desactivar cuenta
     */
    public function toggleEstado($id)
    {
        try {
            $cuenta = Cuenta::where('id', $id)->firstOrFail();

            $nuevoEstado = $cuenta->estado === 'activa' ? 'inactiva' : 'activa';
            $cuenta->update(['estado' => $nuevoEstado]);

            Log::info('Estado de cuenta cambiado: ' . $cuenta->id, [
                'email' => $cuenta->email,
                'nuevo_estado' => $nuevoEstado
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuenta->fresh()->toNodejsArray(),
                    'message' => 'Estado de cuenta actualizado'
                ]);
            }

            return back()->with('success', 'Estado de cuenta actualizado');

        } catch (\Exception $e) {
            Log::error('Error cambiando estado de cuenta: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar estado'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al cambiar estado']);
        }
    }

    /**
     * Validar datos de cuenta
     */
    private function validateCuenta(Request $request, Cuenta $cuenta = null)
    {
        $rules = [
            'empleado_id' => 'nullable|string|max:255',
            'nick' => 'nullable|string|max:255',
            'email' => 'required|email|unique:cuentas,email' . ($cuenta ? ",{$cuenta->id},id" : ''),
            'contrasena' => $cuenta ? 'nullable|string|min:6' : 'required|string|min:6',
            'dni' => 'nullable|string|max:20',
            'rol' => 'required|in:' . implode(',', Cuenta::$roles),
            'estado' => 'required|in:' . implode(',', Cuenta::$estados),
            'tipo' => 'required|in:' . implode(',', Cuenta::$tipos),
            'empresas' => 'nullable|array',
            'empresas.*' => 'string',
            'canales' => 'nullable|array',
            'centro_key' => 'nullable|string|max:255'
        ];

        $messages = [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'Este email ya está registrado',
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres',
            'rol.required' => 'El rol es obligatorio',
            'rol.in' => 'El rol seleccionado no es válido',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado seleccionado no es válido',
            'tipo.required' => 'El tipo es obligatorio',
            'tipo.in' => 'El tipo seleccionado no es válido'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Buscar cuentas por email o nick
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query de búsqueda requerido'
            ], 400);
        }

        try {
            $cuentas = Cuenta::where('email', 'like', "%{$query}%")
                ->orWhere('nick', 'like', "%{$query}%")
                ->orWhere('dni', 'like', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function($cuenta) {
                    return $cuenta->toNodejsArray();
                });

            return response()->json([
                'success' => true,
                'data' => $cuentas
            ]);

        } catch (\Exception $e) {
            Log::error('Error buscando cuentas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de cuentas
     */
    public function estadisticas()
    {
        try {
            $estadisticas = [
                'total' => Cuenta::count(),
                'activas' => Cuenta::porEstado('activa')->count(),
                'inactivas' => Cuenta::porEstado('inactiva')->count(),
                'suspendidas' => Cuenta::porEstado('suspendida')->count(),
                'por_rol' => [],
                'por_tipo' => []
            ];

            // Estadísticas por rol
            foreach (Cuenta::$roles as $rol) {
                $estadisticas['por_rol'][$rol] = Cuenta::porRol($rol)->count();
            }

            // Estadísticas por tipo
            foreach (Cuenta::$tipos as $tipo) {
                $estadisticas['por_tipo'][$tipo] = Cuenta::porTipo($tipo)->count();
            }

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }
}
