<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Perfil;
use App\Models\Auth\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Controlador centralizado para gestión de perfiles
 * Compatible con PerfilSchema de Node.js especificado
 */
class PerfilController extends Controller
{
    /**
     * Mostrar lista de perfiles con paginación
     */
    public function index(Request $request)
    {
        try {
            $query = Perfil::with('cuenta');

            // Filtros
            if ($request->filled('cuenta_id')) {
                $query->where('cuenta_id', $request->cuenta_id);
            }

            if ($request->filled('genero')) {
                $query->porGenero($request->genero);
            }

            if ($request->filled('modulos')) {
                $query->porModulo($request->modulos);
            }

            if ($request->filled('permisos')) {
                $query->porPermiso($request->permisos);
            }

            if ($request->filled('ocupacion')) {
                $query->porOcupacion($request->ocupacion);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%")
                      ->orWhere('ocupacion', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortField = $request->get('sort', 'nombre');
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $perfiles = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $perfiles->items(),
                'pagination' => [
                    'current_page' => $perfiles->currentPage(),
                    'last_page' => $perfiles->lastPage(),
                    'per_page' => $perfiles->perPage(),
                    'total' => $perfiles->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener perfiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de perfiles'
            ], 500);
        }
    }

    /**
     * Crear un nuevo perfil
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cuenta_id' => 'required|string|exists:cuentas,id',
                'nombre' => 'nullable|string|max:255',
                'apellido' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'genero' => 'nullable|string|in:' . implode(',', Perfil::$generos),
                'modulos' => 'required|string|in:' . implode(',', Perfil::$modulos),
                'permisos' => 'nullable|string|in:' . implode(',', Perfil::$permisos),
                'ocupacion' => 'nullable|string|max:255',
                'firma' => 'nullable|string',
                'pieFirma' => 'nullable|string',
                'licencia' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que no exista ya un perfil para esta cuenta
            $perfilExistente = Perfil::where('cuenta_id', $request->cuenta_id)->first();
            if ($perfilExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un perfil para esta cuenta'
                ], 409);
            }

            $perfil = Perfil::create($request->all());

            Log::info('Perfil creado exitosamente', ['perfil_id' => $perfil->id]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil creado exitosamente',
                'data' => $perfil->toNodejsArray()
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el perfil'
            ], 500);
        }
    }

    /**
     * Mostrar un perfil específico
     */
    public function show($id)
    {
        try {
            $perfil = Perfil::with('cuenta')->find($id);

            if (!$perfil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $perfil->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfil'
            ], 500);
        }
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit($id)
    {
        try {
            $perfil = Perfil::with('cuenta')->find($id);

            if (!$perfil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $perfil->toNodejsArray(),
                'opciones' => [
                    'generos' => Perfil::$generos,
                    'modulos' => Perfil::$modulos,
                    'permisos' => Perfil::$permisos
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
     * Actualizar un perfil existente
     */
    public function update(Request $request, $id)
    {
        try {
            $perfil = Perfil::find($id);

            if (!$perfil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'nullable|string|max:255',
                'apellido' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'genero' => 'nullable|string|in:' . implode(',', Perfil::$generos),
                'modulos' => 'string|in:' . implode(',', Perfil::$modulos),
                'permisos' => 'nullable|string|in:' . implode(',', Perfil::$permisos),
                'ocupacion' => 'nullable|string|max:255',
                'firma' => 'nullable|string',
                'pieFirma' => 'nullable|string',
                'licencia' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $perfil->update($request->all());

            Log::info('Perfil actualizado exitosamente', ['perfil_id' => $perfil->id]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => $perfil->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ], 500);
        }
    }

    /**
     * Eliminar un perfil
     */
    public function destroy($id)
    {
        try {
            $perfil = Perfil::find($id);

            if (!$perfil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil no encontrado'
                ], 404);
            }

            $perfil->delete();

            Log::info('Perfil eliminado exitosamente', ['perfil_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el perfil'
            ], 500);
        }
    }

    /**
     * Buscar perfiles por criterios específicos
     */
    public function buscar(Request $request)
    {
        try {
            $query = Perfil::with('cuenta');

            // Búsqueda por texto
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function($query) use ($q) {
                    $query->where('nombre', 'like', "%{$q}%")
                          ->orWhere('apellido', 'like', "%{$q}%")
                          ->orWhere('descripcion', 'like', "%{$q}%")
                          ->orWhere('ocupacion', 'like', "%{$q}%");
                });
            }

            // Filtros específicos
            if ($request->filled('genero')) {
                $query->porGenero($request->genero);
            }

            if ($request->filled('modulos')) {
                $query->porModulo($request->modulos);
            }

            if ($request->filled('permisos')) {
                $query->porPermiso($request->permisos);
            }

            $limit = $request->get('limit', 10);
            $perfiles = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $perfiles->map(function($perfil) {
                    return $perfil->toNodejsArray();
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de perfiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda de perfiles'
            ], 500);
        }
    }

    /**
     * Obtener perfil por cuenta_id
     */
    public function porCuenta($cuentaId)
    {
        try {
            $perfil = Perfil::with('cuenta')->where('cuenta_id', $cuentaId)->first();

            if (!$perfil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil no encontrado para esta cuenta'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $perfil->toNodejsArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener perfil por cuenta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfil de la cuenta'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de perfiles
     */
    public function estadisticas()
    {
        try {
            $estadisticas = [
                'total_perfiles' => Perfil::count(),
                'por_genero' => [
                    'masculino' => Perfil::porGenero('masculino')->count(),
                    'femenino' => Perfil::porGenero('femenino')->count(),
                    'otro' => Perfil::porGenero('otro')->count(),
                    'sin_definir' => Perfil::whereNull('genero')->count()
                ],
                'por_modulos' => [
                    'dashboard' => Perfil::porModulo('dashboard')->count(),
                    'administracion' => Perfil::porModulo('administracion')->count(),
                    'hallazgos' => Perfil::porModulo('hallazgos')->count(),
                    'psicosocial' => Perfil::porModulo('psicosocial')->count()
                ],
                'por_permisos' => [
                    'all' => Perfil::porPermiso('all')->count(),
                    'write' => Perfil::porPermiso('write')->count(),
                    'read' => Perfil::porPermiso('read')->count(),
                    'delete' => Perfil::porPermiso('delete')->count(),
                    'sin_definir' => Perfil::whereNull('permisos')->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de perfiles: ' . $e->getMessage());
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
                'generos' => Perfil::$generos,
                'modulos' => Perfil::$modulos,
                'permisos' => Perfil::$permisos
            ]
        ]);
    }
}
