<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use App\Services\ConfiguracionService;
use App\Models\Configuracion\Configuracion;
use App\Models\EvaluacionPsicosocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PsicosocialConfigController extends Controller
{
    protected $configuracionService;

    public function __construct(ConfiguracionService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Obtener configuraciones específicas para el módulo Psicosocial
     */
    public function getPsicosocialConfig($empresaId)
    {
        try {
            $configs = $this->configuracionService->getPsicosocialConfigurations($empresaId);
            
            return [
                'instrumentos_disponibles' => $this->getConfigValue($configs, 'psicosocial_instrumentos_disponibles', ['ISTAS21', 'FPSICO']),
                'estados_evaluacion' => $this->getConfigValue($configs, 'psicosocial_estados_evaluacion', ['iniciada', 'en_progreso', 'completada', 'validada']),
                'niveles_riesgo' => $this->getConfigValue($configs, 'psicosocial_niveles_riesgo', ['sin_riesgo', 'riesgo_bajo', 'riesgo_medio', 'riesgo_alto']),
                'auto_numeracion' => $this->getConfigValue($configs, 'psicosocial_auto_numeracion', ['enabled' => true, 'formato' => 'PSI-{YYYY}-{####}']),
                'periodicidad_evaluacion' => $this->getConfigValue($configs, 'psicosocial_periodicidad_evaluacion', ['value' => 12, 'unit' => 'meses']),
                'confidencialidad' => $this->getConfigValue($configs, 'psicosocial_confidencialidad', true)
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones psicosociales: ' . $e->getMessage());
            return $this->getDefaultPsicosocialConfig();
        }
    }

    /**
     * Aplicar configuraciones al crear una nueva evaluación psicosocial
     */
    public function applyConfigToEvaluacion(Request $request, $empresaId)
    {
        try {
            $configs = $this->getPsicosocialConfig($empresaId);
            $evaluacionData = $request->all();

            // Aplicar numeración automática si está habilitada
            if ($configs['auto_numeracion']['enabled']) {
                $formato = $configs['auto_numeracion']['formato'];
                $evaluacionData['codigo'] = $this->generateEvaluacionCode($formato, $empresaId);
            }

            // Establecer estado inicial
            if (!isset($evaluacionData['estado']) && !empty($configs['estados_evaluacion'])) {
                $evaluacionData['estado'] = $configs['estados_evaluacion'][0];
            }

            // Validar instrumento
            if (isset($evaluacionData['instrumento_utilizado']) && !in_array($evaluacionData['instrumento_utilizado'], $configs['instrumentos_disponibles'])) {
                return response()->json(['error' => 'Instrumento no válido'], 400);
            }

            // Aplicar configuraciones de confidencialidad
            $evaluacionData['confidencial'] = $configs['confidencialidad'];

            // Calcular fecha de próxima evaluación
            if ($configs['periodicidad_evaluacion']) {
                $evaluacionData['proxima_evaluacion'] = $this->calculateNextEvaluationDate($configs['periodicidad_evaluacion']);
            }

            return response()->json([
                'success' => true,
                'evaluacion_data' => $evaluacionData,
                'configs_applied' => $configs
            ]);

        } catch (\Exception $e) {
            Log::error('Error aplicando configuraciones a evaluación psicosocial: ' . $e->getMessage());
            return response()->json(['error' => 'Error al aplicar configuraciones'], 500);
        }
    }

    /**
     * Validar datos de evaluación según configuraciones
     */
    public function validateEvaluacionData($evaluacionData, $empresaId)
    {
        $configs = $this->getPsicosocialConfig($empresaId);
        $errors = [];

        // Validar estado
        if (isset($evaluacionData['estado']) && !in_array($evaluacionData['estado'], $configs['estados_evaluacion'])) {
            $errors[] = 'Estado no válido';
        }

        // Validar instrumento
        if (isset($evaluacionData['instrumento_utilizado']) && !in_array($evaluacionData['instrumento_utilizado'], $configs['instrumentos_disponibles'])) {
            $errors[] = 'Instrumento no válido';
        }

        // Validar nivel de riesgo
        if (isset($evaluacionData['nivel_riesgo_general']) && !in_array($evaluacionData['nivel_riesgo_general'], $configs['niveles_riesgo'])) {
            $errors[] = 'Nivel de riesgo no válido';
        }

        return $errors;
    }

    /**
     * Generar código automático para evaluación psicosocial
     */
    private function generateEvaluacionCode($formato, $empresaId)
    {
        $año = date('Y');
        $ultimoNumero = $this->getUltimoNumeroEvaluacion($empresaId, $año);
        $siguienteNumero = $ultimoNumero + 1;
        
        $codigo = str_replace('{YYYY}', $año, $formato);
        $codigo = str_replace('{####}', str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT), $codigo);
        
        return $codigo;
    }

    /**
     * Obtener último número de evaluación del año
     */
    private function getUltimoNumeroEvaluacion($empresaId, $año)
    {
        try {
            $ultimaEvaluacion = EvaluacionPsicosocial::where('empresa_id', $empresaId)
                                                   ->whereYear('created_at', $año)
                                                   ->orderBy('created_at', 'desc')
                                                   ->first();
            
            if (!$ultimaEvaluacion || !$ultimaEvaluacion->codigo) {
                return 0;
            }

            // Extraer número del código (asumiendo formato PSI-YYYY-####)
            preg_match('/\-(\d+)$/', $ultimaEvaluacion->codigo, $matches);
            return isset($matches[1]) ? intval($matches[1]) : 0;
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo último número de evaluación: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calcular fecha de próxima evaluación
     */
    private function calculateNextEvaluationDate($periodicidad)
    {
        try {
            $valor = $periodicidad['value'];
            $unidad = $periodicidad['unit'];
            
            $fecha = now();
            
            switch ($unidad) {
                case 'meses':
                    $fecha->addMonths($valor);
                    break;
                case 'años':
                    $fecha->addYears($valor);
                    break;
                case 'dias':
                    $fecha->addDays($valor);
                    break;
                default:
                    $fecha->addMonths($valor); // Por defecto meses
            }
            
            return $fecha;
        } catch (\Exception $e) {
            Log::error('Error calculando próxima evaluación: ' . $e->getMessage());
            return now()->addMonths(12); // Por defecto 12 meses
        }
    }

    /**
     * Obtener valor de configuración específica
     */
    private function getConfigValue($configs, $clave, $default)
    {
        if (isset($configs[$clave]) && isset($configs[$clave]->valor)) {
            $valor = $configs[$clave]->valor;
            return is_array($valor) && count($valor) === 1 ? $valor[0] : $valor;
        }
        return $default;
    }

    /**
     * Configuraciones por defecto para psicosocial
     */
    private function getDefaultPsicosocialConfig()
    {
        return [
            'instrumentos_disponibles' => ['ISTAS21', 'FPSICO', 'Personalizado'],
            'estados_evaluacion' => ['iniciada', 'en_progreso', 'completada', 'validada'],
            'niveles_riesgo' => ['sin_riesgo', 'riesgo_bajo', 'riesgo_medio', 'riesgo_alto'],
            'auto_numeracion' => ['enabled' => true, 'formato' => 'PSI-{YYYY}-{####}'],
            'periodicidad_evaluacion' => ['value' => 12, 'unit' => 'meses'],
            'confidencialidad' => true
        ];
    }

    /**
     * API para obtener configuraciones desde el frontend
     */
    public function getConfigurationsApi($empresaId)
    {
        try {
            $configs = $this->getPsicosocialConfig($empresaId);
            
            return response()->json([
                'success' => true,
                'configuraciones' => $configs
            ]);
        } catch (\Exception $e) {
            Log::error('Error en API de configuraciones psicosociales: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener configuraciones'], 500);
        }
    }

    /**
     * Actualizar configuraciones específicas de psicosocial
     */
    public function updatePsicosocialConfig(Request $request, $empresaId)
    {
        try {
            $request->validate([
                'configuraciones' => 'required|array',
                'configuraciones.*.clave' => 'required|string',
                'configuraciones.*.valor' => 'required'
            ]);

            foreach ($request->configuraciones as $config) {
                $this->configuracionService->updateConfiguration(
                    $empresaId,
                    $config['clave'],
                    $config['valor'],
                    'psicosocial'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones psicosociales actualizadas'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuraciones psicosociales: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar configuraciones'], 500);
        }
    }

    /**
     * Obtener instrumentos de evaluación disponibles según configuración
     */
    public function getInstrumentosDisponibles($empresaId)
    {
        try {
            $configs = $this->getPsicosocialConfig($empresaId);
            
            return response()->json([
                'success' => true,
                'instrumentos' => $configs['instrumentos_disponibles']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener instrumentos'], 500);
        }
    }

    /**
     * Verificar si una evaluación necesita re-evaluación según periodicidad configurada
     */
    public function verificarReEvaluacion($evaluacionId, $empresaId)
    {
        try {
            $configs = $this->getPsicosocialConfig($empresaId);
            $evaluacion = EvaluacionPsicosocial::findOrFail($evaluacionId);
            
            $proximaFecha = $this->calculateNextEvaluationDate($configs['periodicidad_evaluacion']);
            $necesitaReEvaluacion = now()->gte($proximaFecha);
            
            return response()->json([
                'success' => true,
                'necesita_re_evaluacion' => $necesitaReEvaluacion,
                'proxima_fecha' => $proximaFecha->format('d/m/Y'),
                'dias_restantes' => $proximaFecha->diffInDays(now())
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error verificando re-evaluación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al verificar re-evaluación'], 500);
        }
    }
}
