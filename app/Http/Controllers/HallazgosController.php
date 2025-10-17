<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hallazgo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class HallazgosController extends Controller
{
    /**
     * Display a listing of hallazgos for the current company.
     */    public function index()
    {
        try {
            // Verificar autenticación usando AuthController
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            $isSuperAdmin = $userData->isSuperAdmin ?? false;
            
            // El trait BelongsToEmpresa automáticamente filtra por empresa para usuarios normales
            // Para super admin, usamos scopeAllEmpresas para ver todos los hallazgos
            if ($isSuperAdmin) {
                $hallazgos = Hallazgo::allEmpresas()
                    ->with(['empresa', 'usuario'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            } else {
                // El trait automáticamente filtra por empresa_id del usuario actual
                $hallazgos = Hallazgo::with(['empresa', 'usuario'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            }
            
            return view('modules.hallazgos.index', compact('hallazgos', 'isSuperAdmin'));
            
        } catch (Exception $e) {
            Log::error('Error al cargar hallazgos: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los hallazgos. Por favor, intente nuevamente.');
        }
    }

    /**
     * Get hallazgos data for DataTables
     */
    public function data(Request $request)
    {
        try {
            // Verificar autenticación
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query = Hallazgo::where('empresa_id', $empresaData['id'])
                ->orderBy('created_at', 'desc');

            // Aplicar filtros de búsqueda si existen
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('titulo', 'like', "%{$searchValue}%")
                      ->orWhere('descripcion', 'like', "%{$searchValue}%")
                      ->orWhere('tipo', 'like', "%{$searchValue}%")
                      ->orWhere('area', 'like', "%{$searchValue}%")
                      ->orWhere('responsable', 'like', "%{$searchValue}%")
                      ->orWhere('estado', 'like', "%{$searchValue}%");
                });
            }

            $totalRecords = $query->count();
            
            // Aplicar paginación
            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $hallazgos = $query->get();

            $data = [];
            foreach ($hallazgos as $hallazgo) {
                $data[] = [
                    'id' => $hallazgo->_id,
                    'titulo' => $hallazgo->titulo,
                    'descripcion' => Str::limit($hallazgo->descripcion, 100),
                    'tipo' => $hallazgo->tipo,
                    'area' => $hallazgo->area,
                    'responsable' => $hallazgo->responsable,
                    'fecha_limite' => $hallazgo->fecha_limite ? \Carbon\Carbon::parse($hallazgo->fecha_limite)->format('d/m/Y') : '',
                    'estado' => $hallazgo->estado,
                    'created_at' => $hallazgo->created_at ? \Carbon\Carbon::parse($hallazgo->created_at)->format('d/m/Y H:i') : '',
                    'acciones' => view('modules.hallazgos.partials.actions', compact('hallazgo'))->render()
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (Exception $e) {
            Log::error('Error al cargar datos de hallazgos: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Get hallazgos statistics for current company
     */
    public function stats(Request $request)
    {
        try {
            // Verificar autenticación
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            // SEGURIDAD: TODOS los usuarios ven solo estadísticas de su empresa actual
            $query = Hallazgo::where('empresa_id', $empresaData['id']);

            $stats = [
                'total' => $query->count(),
                'abierto' => $query->where('estado', 'abierto')->count(),
                'en_proceso' => $query->where('estado', 'en_proceso')->count(),
                'cerrado' => $query->where('estado', 'cerrado')->count(),
                'critico' => $query->where('tipo', 'critico')->count(),
                'alto' => $query->where('tipo', 'alto')->count(),
                'medio' => $query->where('tipo', 'medio')->count(),
                'bajo' => $query->where('tipo', 'bajo')->count(),
            ];

            return response()->json($stats);

        } catch (Exception $e) {
            Log::error('Error al cargar estadísticas de hallazgos: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Get chart data for hallazgos visualizations
     */
    public function chartData(Request $request)
    {
        try {
            // Verificar autenticación
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query = Hallazgo::where('empresa_id', $empresaData['id']);
            
            $type = $request->get('type', 'risk');
            
            if ($type === 'risk') {
                // Datos para gráfico de riesgo
                $data = [
                    'labels' => ['Crítico', 'Alto', 'Medio', 'Bajo'],
                    'datasets' => [[
                        'data' => [
                            $query->where('tipo', 'critico')->count(),
                            $query->where('tipo', 'alto')->count(),
                            $query->where('tipo', 'medio')->count(),
                            $query->where('tipo', 'bajo')->count(),
                        ],
                        'backgroundColor' => ['#dc3545', '#fd7e14', '#ffc107', '#28a745']
                    ]]
                ];
            } elseif ($type === 'trend') {
                // Datos para gráfico de tendencia (últimos 6 meses)
                $months = [];
                $counts = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $months[] = $date->format('M Y');
                    $counts[] = $query->whereYear('created_at', $date->year)
                                   ->whereMonth('created_at', $date->month)
                                   ->count();
                }
                
                $data = [
                    'labels' => $months,
                    'datasets' => [[
                        'label' => 'Hallazgos por mes',
                        'data' => $counts,
                        'borderColor' => '#007bff',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                        'tension' => 0.4
                    ]]
                ];
            } else {
                return response()->json(['error' => 'Tipo de gráfico no válido'], 400);
            }

            return response()->json($data);

        } catch (Exception $e) {
            Log::error('Error al cargar datos de gráfico de hallazgos: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Export hallazgos to Excel for current company
     */
    public function export(Request $request)
    {
        try {
            // Verificar autenticación
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query = Hallazgo::where('empresa_id', $empresaData['id']);

            // Aplicar filtros si existen
            if ($request->has('area') && !empty($request->area)) {
                $query->where('area', $request->area);
            }
            if ($request->has('nivel_riesgo') && !empty($request->nivel_riesgo)) {
                $query->where('tipo', $request->nivel_riesgo);
            }
            if ($request->has('estado') && !empty($request->estado)) {
                $query->where('estado', $request->estado);
            }
            if ($request->has('search') && !empty($request->search)) {
                $searchValue = $request->search;
                $query->where(function($q) use ($searchValue) {
                    $q->where('titulo', 'like', "%{$searchValue}%")
                      ->orWhere('descripcion', 'like', "%{$searchValue}%")
                      ->orWhere('area', 'like', "%{$searchValue}%")
                      ->orWhere('responsable', 'like', "%{$searchValue}%");
                });
            }

            $hallazgos = $query->orderBy('created_at', 'desc')->get();

            // Crear array de datos para exportar
            $data = [];
            $data[] = ['ID', 'Título', 'Descripción', 'Tipo', 'Área', 'Responsable', 'Estado', 'Fecha Creación', 'Fecha Límite'];
            
            foreach ($hallazgos as $hallazgo) {
                $data[] = [
                    $hallazgo->id,
                    $hallazgo->titulo,
                    $hallazgo->descripcion,
                    $hallazgo->tipo,
                    $hallazgo->area,
                    $hallazgo->responsable,
                    $hallazgo->estado,
                    $hallazgo->created_at ? $hallazgo->created_at->format('Y-m-d H:i:s') : '',
                    $hallazgo->fecha_limite ? $hallazgo->fecha_limite : ''
                ];
            }

            // Crear CSV content
            $csvContent = '';
            foreach ($data as $row) {
                $csvContent .= '"' . implode('","', $row) . '"' . "\n";
            }

            $filename = 'hallazgos_' . $empresaData['nit'] . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (Exception $e) {
            Log::error('Error al exportar hallazgos: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al generar la exportación']);
        }
    }

    /**
     * Show the form for creating a new hallazgo.
     */
    public function create()
    {
        try {
            $user = session('user');
            
            // Verificar que el usuario tenga empresa asignada
            if (!isset($user['empresa_id'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tiene una empresa asignada para crear hallazgos.');
            }
            
            return view('modules.hallazgos.create');
            
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de hallazgos: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario. Por favor, intente nuevamente.');
        }
    }

    /**
     * Store a newly created hallazgo in storage.
     */    public function store(Request $request)
    {
        try {
            // Verificar autenticación
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            // Validar datos de entrada
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'tipo' => 'required|string|in:critico,alto,medio,bajo',
                'area' => 'required|string|max:100',
                'responsable' => 'required|string|max:100',
                'fecha_limite' => 'required|date|after:today',
                'ubicacion' => 'nullable|string|max:255',
                'evidencias' => 'nullable|array',
                'evidencias.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240'
            ]);
            
            // Crear el hallazgo - el trait BelongsToEmpresa automáticamente asignará empresa_id
            $hallazgo = new Hallazgo();
            $hallazgo->titulo = $validated['titulo'];
            $hallazgo->descripcion = $validated['descripcion'];
            $hallazgo->tipo = $validated['tipo'];
            $hallazgo->area = $validated['area'];
            $hallazgo->responsable = $validated['responsable'];
            $hallazgo->fecha_limite = $validated['fecha_limite'];
            $hallazgo->ubicacion = $validated['ubicacion'] ?? null;
            $hallazgo->usuario_id = $userData->_id;
            $hallazgo->estado = 'abierto';
            
            // Procesar evidencias si las hay
            if ($request->hasFile('evidencias')) {
                $evidencias = [];
                foreach ($request->file('evidencias') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('hallazgos/evidencias', $filename, 'public');
                    $evidencias[] = [
                        'nombre' => $file->getClientOriginalName(),
                        'ruta' => $path,
                        'tipo' => $file->getClientMimeType(),
                        'tamaño' => $file->getSize()
                    ];
                }
                $hallazgo->evidencias = $evidencias;
            }
            
            $hallazgo->save();
            
            return redirect()->route('hallazgos.index')
                ->with('success', 'Hallazgo creado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al crear hallazgo: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el hallazgo. Por favor, intente nuevamente.');
        }
    }

    /**
     * Display the specified hallazgo.
     */    public function show($id)
    {
        try {
            $userData = \App\Http\Controllers\AuthController::user();
            $isSuperAdmin = $userData->isSuperAdmin ?? false;
            
            $query = Hallazgo::where('_id', $id);
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query->where('empresa_id', $userData['empresa_id']);
            
            $hallazgo = $query->firstOrFail();
            
            return view('modules.hallazgos.show', compact('hallazgo', 'isSuperAdmin'));
            
        } catch (Exception $e) {
            Log::error('Error al cargar hallazgo: ' . $e->getMessage());
            return back()->with('error', 'Hallazgo no encontrado o no tiene permisos para verlo.');
        }
    }

    /**
     * Show the form for editing the specified hallazgo.
     */
    public function edit($id)
    {
        try {
            $user = session('user');
            $isSuperAdmin = session('is_super_admin', false);
            
            $query = Hallazgo::where('_id', $id);
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query->where('empresa_id', $user['empresa_id']);
            
            $hallazgo = $query->firstOrFail();
            
            return view('modules.hallazgos.edit', compact('hallazgo', 'isSuperAdmin'));
            
        } catch (Exception $e) {
            Log::error('Error al cargar hallazgo para editar: ' . $e->getMessage());
            return back()->with('error', 'Hallazgo no encontrado o no tiene permisos para editarlo.');
        }
    }

    /**
     * Update the specified hallazgo in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = session('user');
            $isSuperAdmin = session('is_super_admin', false);
            
            // Validar datos de entrada
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'tipo' => 'required|string|in:critico,alto,medio,bajo',
                'area' => 'required|string|max:100',
                'responsable' => 'required|string|max:100',
                'fecha_limite' => 'required|date',
                'ubicacion' => 'nullable|string|max:255',
                'estado' => 'required|string|in:abierto,en_proceso,cerrado',
                'observaciones' => 'nullable|string'
            ]);
            
            $query = Hallazgo::where('_id', $id);
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query->where('empresa_id', $user['empresa_id']);
            
            $hallazgo = $query->firstOrFail();
            
            // Actualizar campos
            $hallazgo->titulo = $validated['titulo'];
            $hallazgo->descripcion = $validated['descripcion'];
            $hallazgo->tipo = $validated['tipo'];
            $hallazgo->area = $validated['area'];
            $hallazgo->responsable = $validated['responsable'];
            $hallazgo->fecha_limite = $validated['fecha_limite'];
            $hallazgo->ubicacion = $validated['ubicacion'] ?? null;
            $hallazgo->estado = $validated['estado'];
            $hallazgo->observaciones = $validated['observaciones'] ?? null;
            $hallazgo->updated_at = now();
            
            $hallazgo->save();
            
            return redirect()->route('hallazgos.show', $id)
                ->with('success', 'Hallazgo actualizado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al actualizar hallazgo: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar el hallazgo. Por favor, intente nuevamente.');
        }
    }

    /**
     * Remove the specified hallazgo from storage.
     */
    public function destroy($id)
    {
        try {
            $user = session('user');
            $isSuperAdmin = session('is_super_admin', false);
            
            $query = Hallazgo::where('_id', $id);
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            $query->where('empresa_id', $user['empresa_id']);
            
            $hallazgo = $query->firstOrFail();
            $hallazgo->delete();
            
            return redirect()->route('hallazgos.index')
                ->with('success', 'Hallazgo eliminado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al eliminar hallazgo: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el hallazgo. Por favor, intente nuevamente.');
        }
    }
}