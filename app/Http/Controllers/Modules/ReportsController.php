<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Auth\Usuario;
use App\Models\Empresa;
use App\Models\Hallazgo\Reporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports module dashboard
     */
    public function index()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario) {
                return redirect()->route('login')->with('error', 'Sesión expirada');
            }            // Get user's available companies
            $empresasDisponibles = [];
            if ($usuario->hasPermission('reports.view')) {
                if ($usuario->perfil->nivel >= 4) {
                    // Admin users can see all companies
                    $empresasDisponibles = Empresa::query()
                        ->where('estado', true)
                        ->where('_esBorrado', false)
                        ->get();
                } else {                    // Regular users can only see their company
                    $empresasDisponibles = Empresa::query()
                        ->where('_id', $usuario->empresa_id)
                        ->get();
                }
            }

            // Get statistics
            $estadisticas = $this->getReportsStatistics($usuario);

            return view('modules.reports.index', compact('usuario', 'empresasDisponibles', 'estadisticas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar módulo de reportes: ' . $e->getMessage());
        }
    }

    /**
     * Display reports list
     */
    public function reports(Request $request)
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.view')) {
                return back()->with('error', 'No tiene permisos para ver reportes');
            }

            // Filter parameters
            $empresaId = $request->get('empresa_id');
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');
            $tipo = $request->get('tipo');

            // Build query
            $query = Reporte::with(['empresa']);            // Apply company filter based on user permissions
            if ($usuario->perfil->nivel < 4) {            // Regular users can only see their company reports
                $query->where('empresa_id', $usuario->empresa_id);
            } elseif ($empresaId) {
                // Admin users can filter by company
                $query->where('empresa_id', $empresaId);
            }

            // Apply date filters
            if ($fechaInicio) {
                $query->where('created_at', '>=', Carbon::parse($fechaInicio)->startOfDay());
            }
            if ($fechaFin) {
                $query->where('created_at', '<=', Carbon::parse($fechaFin)->endOfDay());
            }

            // Apply type filter
            if ($tipo) {
                $query->where('tipo', $tipo);
            }

            $reportes = $query->orderBy('created_at', 'desc')->paginate(20);            // Get filter options
            $empresas = [];
            if ($usuario->perfil->nivel >= 4) {
                $empresas = Empresa::query()
                    ->where('estado', true)
                    ->where('_esBorrado', false)
                    ->get();
            }

            $tiposReporte = [
                'hallazgos' => 'Reporte de Hallazgos',
                'empleados' => 'Reporte de Empleados',
                'psicosocial' => 'Evaluación Psicosocial',
                'planes' => 'Planes de Mejora',
                'auditoria' => 'Auditoría del Sistema'
            ];            return view('modules.reports.list', compact(
                'reportes', 
                'empresas', 
                'tiposReporte', 
                'empresaId', 
                'fechaInicio', 
                'fechaFin', 
                'tipo'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar reportes: ' . $e->getMessage());
        }
    }    /**
     * Show report creation form
     */
    public function create()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.create')) {
                return back()->with('error', 'No tiene permisos para crear reportes');
            }

            // Get available companies
            $empresas = [];
            if ($usuario->perfil->nivel >= 4) {
                $empresas = Empresa::query()
                    ->where('estado', true)
                    ->where('_esBorrado', false)
                    ->get();
            } else {
                $empresas = Empresa::query()
                    ->where('_id', $usuario->empresa_id)
                    ->get();
            }

            $tiposReporte = [
                'hallazgos' => 'Reporte de Hallazgos',
                'empleados' => 'Reporte de Empleados',
                'psicosocial' => 'Evaluación Psicosocial',
                'planes' => 'Planes de Mejora',
                'auditoria' => 'Auditoría del Sistema'
            ];

            return view('modules.reports.create', compact('empresas', 'tiposReporte'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a new report
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'tipo' => 'required|string',
            'empresa_id' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'parametros' => 'nullable|array'
        ]);

        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.create')) {
                return back()->with('error', 'No tiene permisos para crear reportes');
            }            // Validate company access
            if ($usuario->perfil->nivel < 4 && $request->empresa_id != (string) $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para crear reportes de esta empresa');
            }

            $reporteData = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'empresa_id' => $request->empresa_id,
                'usuario_id' => $usuario->_id,
                'fecha_inicio' => Carbon::parse($request->fecha_inicio),
                'fecha_fin' => Carbon::parse($request->fecha_fin),
                'parametros' => $request->parametros ?? [],
                'estado' => 'pendiente',
                'progreso' => 0,
                'archivo_generado' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $reporte = Reporte::create($reporteData);

            // Queue report generation
            $this->generateReportAsync($reporte);

            return redirect()->route('reports.index')
                ->with('success', 'Reporte creado exitosamente. Se generará en segundo plano.');        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear reporte: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified report
     */
    public function edit($id)
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.edit')) {
                return back()->with('error', 'No tiene permisos para editar reportes');
            }            $reporte = Reporte::with(['empresa'])
                ->where('_id', $id)
                ->first();

            if (!$reporte) {
                return back()->with('error', 'Reporte no encontrado');
            }

            // Check access permissions
            if ($usuario->perfil->nivel < 4 && $reporte->empresa_id != $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para editar este reporte');
            }

            // Only allow editing of pending reports
            if ($reporte->estado !== 'pendiente') {
                return back()->with('error', 'Solo se pueden editar reportes en estado pendiente');
            }            // Get available companies
            $empresas = [];
            if ($usuario->perfil->nivel >= 4) {
                $empresas = Empresa::query()
                    ->where('estado', true)
                    ->where('_esBorrado', false)
                    ->get();
            } else {
                $empresas = Empresa::query()
                    ->where('_id', $usuario->empresa_id)
                    ->get();
            }

            $tiposReporte = [
                'hallazgos' => 'Reporte de Hallazgos',
                'empleados' => 'Reporte de Empleados',
                'psicosocial' => 'Evaluación Psicosocial',
                'planes' => 'Planes de Mejora',
                'auditoria' => 'Auditoría del Sistema'
            ];

            return view('modules.reports.edit', compact('reporte', 'empresas', 'tiposReporte'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'tipo' => 'required|string',
            'empresa_id' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'parametros' => 'nullable|array'
        ]);        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
              if (!$usuario || !$usuario->hasPermission('reports.edit')) {
                return back()->with('error', 'No tiene permisos para editar reportes');
            }

            $reporte = Reporte::where('_id', $id)->first();

            if (!$reporte) {
                return back()->with('error', 'Reporte no encontrado')->withInput();
            }

            // Check access permissions
            if ($usuario->perfil->nivel < 4 && $reporte->empresa_id != $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para editar este reporte');
            }

            // Only allow editing of pending reports
            if ($reporte->estado !== 'pendiente') {
                return back()->with('error', 'Solo se pueden editar reportes en estado pendiente');
            }            // Validate company access
            if ($usuario->perfil->nivel < 4 && $request->empresa_id != (string) $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para asignar reportes a esta empresa');
            }

            $updateData = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'empresa_id' => $request->empresa_id,
                'fecha_inicio' => Carbon::parse($request->fecha_inicio),
                'fecha_fin' => Carbon::parse($request->fecha_fin),
                'parametros' => $request->parametros ?? [],
                'updated_at' => now()
            ];

            $reporte->update($updateData);

            return redirect()->route('reports.show', $reporte->_id)
                ->with('success', 'Reporte actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar reporte: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified report
     */
    public function show($id)
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.view')) {
                return back()->with('error', 'No tiene permisos para ver reportes');
            }            $reporte = Reporte::with(['empresa', 'usuario'])
                ->where('_id', $id)
                ->first();

            if (!$reporte) {
                return back()->with('error', 'Reporte no encontrado');
            }

            // Check access permissions
            if ($usuario->perfil->nivel < 4 && $reporte->empresa_id != $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para ver este reporte');
            }

            return view('modules.reports.show', compact('reporte'));
        } catch (\Exception $e) {
            return back()->with('error', 'Reporte no encontrado');
        }
    }

    /**
     * Download the specified report
     */
    public function download($id)
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
              if (!$usuario || !$usuario->hasPermission('reports.export')) {
                return back()->with('error', 'No tiene permisos para descargar reportes');
            }

            $reporte = Reporte::where('_id', $id)->first();

            if (!$reporte) {
                return back()->with('error', 'Reporte no encontrado');
            }

            // Check access permissions
            if ($usuario->perfil->nivel < 4 && $reporte->empresa_id != $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para descargar este reporte');
            }

            if (!$reporte->archivo_generado || $reporte->estado !== 'completado') {
                return back()->with('error', 'El reporte aún no está disponible para descarga');
            }

            $filePath = storage_path('app/reports/' . $reporte->archivo_generado);
            
            if (!file_exists($filePath)) {
                return back()->with('error', 'Archivo de reporte no encontrado');
            }

            return response()->download($filePath, $reporte->nombre . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al descargar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified report
     */
    public function destroy($id)
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
              if (!$usuario || !$usuario->hasPermission('reports.admin')) {
                return back()->with('error', 'No tiene permisos para eliminar reportes');
            }

            $reporte = Reporte::where('_id', $id)->first();

            if (!$reporte) {
                return back()->with('error', 'Reporte no encontrado');
            }

            // Check access permissions
            if ($usuario->perfil->nivel < 4 && $reporte->empresa_id != $usuario->empresa_id) {
                return back()->with('error', 'No tiene permisos para eliminar este reporte');
            }

            // Delete file if exists
            if ($reporte->archivo_generado) {
                $filePath = storage_path('app/reports/' . $reporte->archivo_generado);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $reporte->delete();

            return redirect()->route('reports.reports')
                ->with('success', 'Reporte eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics for reports
     */
    private function getReportsStatistics($usuario): array
    {
        try {
            $stats = [
                'total_reportes' => 0,
                'reportes_pendientes' => 0,
                'reportes_completados' => 0,
                'reportes_mes_actual' => 0
            ];            $query = Reporte::query();

            // Apply company filter based on user permissions
            if ($usuario->perfil->nivel < 4) {
                $query->where('empresa_id', $usuario->empresa_id);
            }

            $stats['total_reportes'] = $query->count();
            $stats['reportes_pendientes'] = (clone $query)->where('estado', 'pendiente')->count();
            $stats['reportes_completados'] = (clone $query)->where('estado', 'completado')->count();
            $stats['reportes_mes_actual'] = (clone $query)
                ->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])->count();

            return $stats;
        } catch (\Exception $e) {
            return [
                'total_reportes' => 0,
                'reportes_pendientes' => 0,
                'reportes_completados' => 0,
                'reportes_mes_actual' => 0
            ];
        }
    }    /**
     * Generate report asynchronously (placeholder for queue implementation)
     */
    private function generateReportAsync($reporte)
    {
        // TODO: Implement queue system for report generation
        // For now, we'll just mark it as processing
        try {
            $reporte->update([
                'estado' => 'procesando',
                'progreso' => 10
            ]);

            // Simulate report generation
            // In a real implementation, this would be a queued job
        } catch (\Exception $e) {
            $reporte->update(['estado' => 'error']);
        }
    }

    /**
     * Get report progress (for AJAX polling)
     */    public function getProgress($id)
    {
        try {
            $reporte = Reporte::where('_id', $id)->first();
            
            if (!$reporte) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reporte no encontrado'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'estado' => $reporte->estado,
                'progreso' => $reporte->progreso,
                'archivo_generado' => $reporte->archivo_generado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener progreso'
            ], 500);
        }
    }

    /**
     * Get recent reports for dashboard
     */
    public function getRecentReports()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.view')) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
            }            $query = Reporte::with(['empresa']);

            // Apply company filter based on user permissions
            if ($usuario->perfil->nivel < 4) {
                $query->where('empresa_id', $usuario->empresa_id);
            }

            $reportes = $query->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $data = $reportes->map(function($reporte) {
                return [
                    'id' => (string) $reporte->_id,
                    'nombre' => $reporte->nombre,
                    'tipo' => ucfirst($reporte->tipo),
                    'estado' => $reporte->estado,
                    'archivo_generado' => $reporte->archivo_generado,
                    'created_at' => $reporte->created_at->format('d/m/Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'reportes' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar reportes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics by report type
     */
    public function getStatsByType()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.view')) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
            }            $query = Reporte::query();

            // Apply company filter based on user permissions
            if ($usuario->perfil->nivel < 4) {
                $query->where('empresa_id', $usuario->empresa_id);
            }

            $stats = $query->selectRaw('tipo, COUNT(*) as count')
                ->groupBy('tipo')
                ->get();

            $labels = [];
            $data = [];

            foreach ($stats as $stat) {
                $labels[] = ucfirst($stat->tipo);
                $data[] = $stat->count;
            }

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly trend statistics
     */
    public function getStatsTrend()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$usuario->hasPermission('reports.view')) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
            }            $query = Reporte::query();

            // Apply company filter based on user permissions
            if ($usuario->perfil->nivel < 4) {
                $query->where('empresa_id', $usuario->empresa_id);
            }

            // Get last 6 months
            $monthsData = [];
            $labels = [];
            $data = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $count = (clone $query)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

                $labels[] = $date->format('M Y');
                $data[] = $count;
            }

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar tendencia: ' . $e->getMessage()
            ], 500);
        }
    }
}

