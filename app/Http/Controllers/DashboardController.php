<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\BateriaPsicosocialService;

class DashboardController extends Controller
{
    protected $psicosocialService;

    public function __construct(BateriaPsicosocialService $psicosocialService)
    {
        $this->psicosocialService = $psicosocialService;
    }

    public function index()
    {
        try {
            // SEGURIDAD: Verificar autenticación completa
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                return redirect()->route('login.nit');
            }

            $empresaData = \App\Http\Controllers\AuthController::empresa();
            $userData = \App\Http\Controllers\AuthController::user();
            
            // VALIDACIÓN: Empresa debe estar presente
            if (!$empresaData || !isset($empresaData['id'])) {
                return redirect()->route('login.nit')->with('error', 'Sesión de empresa inválida');
            }

            $empresaId = $empresaData['id'];
        
            // Obtener estadísticas SOLO de la empresa actual
            try {
                $estadisticasGenerales = $this->psicosocialService->obtenerEstadisticasGenerales($empresaId);
                $distribucionRiesgo = $this->psicosocialService->obtenerDistribucionRiesgo($empresaId);
                
                $stats = [
                    'total_empleados' => $estadisticasGenerales['total_empleados'] ?? 0,
                    'cuestionarios_completados' => $estadisticasGenerales['cuestionarios_completados'] ?? 0,
                    'cuestionarios_pendientes' => $estadisticasGenerales['cuestionarios_pendientes'] ?? 0,
                    'evaluaciones_recientes' => $estadisticasGenerales['evaluaciones_recientes'] ?? 0
                ];
                
            } catch (\Exception $e) {
                $estadisticasGenerales = ['total' => 0];
                $distribucionRiesgo = [
                    'sin_riesgo' => 0,
                    'riesgo_bajo' => 0, 
                    'riesgo_medio' => 0,
                    'riesgo_alto' => 0,
                    'riesgo_muy_alto' => 0
                ];
                $stats = [
                    'total_empleados' => 0,
                    'cuestionarios_completados' => 0,
                    'cuestionarios_pendientes' => 0,
                    'evaluaciones_recientes' => 0
                ];
            }

            // Contadores reales para dashboard - filtrados por empresa
            $totalHallazgos = 0;
            $evaluacionesPsicosociales = 0;
            $totalEmpleados = 0;
            $planesAccion = 0;

            try {
                // Empleados registrados de la empresa actual
                $totalEmpleados = \App\Models\Empresas\Empleado::where('empresa_id', $empresaId)->count();
                
                // Hallazgos registrados y activos de la empresa actual
                $totalHallazgos = \App\Models\Hallazgo::where('empresa_id', $empresaId)->count();
                
                // Evaluaciones psicosociales de la empresa actual
                $evaluacionesPsicosociales = \App\Models\EvaluacionPsicosocial::where('empresa_id', $empresaId)->count();
                
                // Planes de acción de la empresa actual
                $planesAccion = \App\Models\Plan::where('empresa_id', $empresaId)->count();
                
            } catch (\Exception $e) {
                // Error silencioso, mantener valores en 0
            }
            
            // Variables para los gráficos
            $riesgoCritico = $distribucionRiesgo['riesgo_muy_alto'] ?? 0;
            $riesgoAlto = $distribucionRiesgo['riesgo_alto'] ?? 0;
            $riesgoMedio = $distribucionRiesgo['riesgo_medio'] ?? 0;
            $riesgoBajo = $distribucionRiesgo['riesgo_bajo'] ?? 0;

            return view('dashboard', compact(
                'empresaData', 
                'userData', 
                'estadisticasGenerales', 
                'distribucionRiesgo',
                'stats',
                'totalHallazgos',
                'evaluacionesPsicosociales',
                'totalEmpleados',
                'planesAccion',
                'riesgoCritico',
                'riesgoAlto',
                'riesgoMedio',
                'riesgoBajo'
            ));
            
        } catch (\Exception $e) {
            // Si hay cualquier error, mantener datos básicos de sesión
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            $userData = \App\Http\Controllers\AuthController::user();
            
            // Variables por defecto
            $estadisticasGenerales = ['total' => 0];
            $distribucionRiesgo = ['sin_riesgo' => 0, 'riesgo_bajo' => 0, 'riesgo_medio' => 0, 'riesgo_alto' => 0, 'riesgo_muy_alto' => 0];
            $stats = ['total_empleados' => 0, 'cuestionarios_completados' => 0, 'cuestionarios_pendientes' => 0, 'evaluaciones_recientes' => 0];
            $totalHallazgos = 0;
            $evaluacionesPsicosociales = 0;
            $totalEmpleados = 0;
            $planesAccion = 0;
            $riesgoCritico = 0;
            $riesgoAlto = 0;
            $riesgoMedio = 0;
            $riesgoBajo = 0;

            return view('dashboard', compact(
                'empresaData', 
                'userData', 
                'estadisticasGenerales', 
                'distribucionRiesgo',
                'stats',
                'totalHallazgos',
                'evaluacionesPsicosociales',
                'totalEmpleados',
                'planesAccion',
                'riesgoCritico',
                'riesgoAlto',
                'riesgoMedio',
                'riesgoBajo'
            ));
        }
    }

    public function getStatsApi()
    {
        $empresaData = \App\Http\Controllers\AuthController::empresa();
        // empresaData may be array or object; normalize safely
        $empresa_id = null;
        if (!empty($empresaData)) {
            if (is_array($empresaData)) {
                $empresa_id = $empresaData['id'] ?? $empresaData['_id'] ?? null;
            } elseif (is_object($empresaData)) {
                $empresa_id = $empresaData->id ?? $empresaData->_id ?? null;
            }
            if (is_object($empresa_id) && method_exists($empresa_id, '__toString')) {
                $empresa_id = (string) $empresa_id;
            }
        }
        
        try {
            $estadisticas = $this->psicosocialService->obtenerEstadisticasGenerales($empresa_id);
            $distribucionRiesgo = $this->psicosocialService->obtenerDistribucionRiesgo($empresa_id);
            
            $stats = [
                'total_empleados' => $estadisticas['total_empleados'] ?? 0,
                'cuestionarios_completados' => $estadisticas['cuestionarios_completados'] ?? 0,
                'cuestionarios_pendientes' => $estadisticas['cuestionarios_pendientes'] ?? 0,
                'evaluaciones_recientes' => $estadisticas['evaluaciones_recientes'] ?? 0,
                'distribucion_riesgo' => $distribucionRiesgo
            ];
            
            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener estadísticas'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            return response()->json(['success' => true, 'message' => 'Operación completada']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error en la operación'], 500);
        }
    }
}
