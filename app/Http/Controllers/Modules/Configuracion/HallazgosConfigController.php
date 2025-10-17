<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use App\Services\ConfiguracionService;
use App\Models\Configuracion\Configuracion;
use App\Models\Hallazgo\Reporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HallazgosConfigController extends Controller
{
    protected $configuracionService;

    public function __construct(ConfiguracionService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Obtener configuraciones específicas para el módulo de Hallazgos
     */
    public function getHallazgosConfig($empresaId)
    {
        try {
            $configs = $this->configuracionService->getHallazgosConfigurations($empresaId);
            
            return [
                'estados_disponibles' => $this->getConfigValue($configs, 'hallazgos_estados_disponibles', ['abierto', 'en_proceso', 'cerrado']),
                'tipos_disponibles' => $this->getConfigValue($configs, 'hallazgos_tipos_disponibles', ['incidente', 'accidente']),
                'severidades' => $this->getConfigValue($configs, 'hallazgos_categorias_severidad', ['bajo', 'medio', 'alto', 'critico']),
                'auto_numeracion' => $this->getConfigValue($configs, 'hallazgos_auto_numeracion', ['enabled' => true, 'formato' => 'HAL-{YYYY}-{####}']),
                'notificaciones_email' => $this->getConfigValue($configs, 'hallazgos_notif_email', ['enabled' => true, 'roles' => ['admin']])
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones de hallazgos: ' . $e->getMessage());
            return $this->getDefaultHallazgosConfig();
        }
    }

    /**
     * Aplicar configuraciones al crear un nuevo hallazgo
     */
    public function applyConfigToHallazgo(Request $request, $empresaId)
    {
        try {
            $configs = $this->getHallazgosConfig($empresaId);
            $hallazgoData = $request->all();

            // Aplicar numeración automática si está habilitada
            if ($configs['auto_numeracion']['enabled']) {
                $formato = $configs['auto_numeracion']['formato'];
                $hallazgoData['codigo'] = $this->generateHallazgoCode($formato, $empresaId);
            }

            // Establecer estado inicial
            if (!isset($hallazgoData['estado']) && !empty($configs['estados_disponibles'])) {
                $hallazgoData['estado'] = $configs['estados_disponibles'][0];
            }

            // Validar severidad
            if (isset($hallazgoData['severidad']) && !in_array($hallazgoData['severidad'], $configs['severidades'])) {
                return response()->json(['error' => 'Severidad no válida'], 400);
            }

            // Validar tipo
            if (isset($hallazgoData['tipo']) && !in_array($hallazgoData['tipo'], $configs['tipos_disponibles'])) {
                return response()->json(['error' => 'Tipo de hallazgo no válido'], 400);
            }

            return response()->json([
                'success' => true,
                'hallazgo_data' => $hallazgoData,
                'configs_applied' => $configs
            ]);

        } catch (\Exception $e) {
            Log::error('Error aplicando configuraciones a hallazgo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al aplicar configuraciones'], 500);
        }
    }

    /**
     * Validar datos de hallazgo según configuraciones
     */
    public function validateHallazgoData($hallazgoData, $empresaId)
    {
        $configs = $this->getHallazgosConfig($empresaId);
        $errors = [];

        // Validar estado
        if (isset($hallazgoData['estado']) && !in_array($hallazgoData['estado'], $configs['estados_disponibles'])) {
            $errors[] = 'Estado no válido';
        }

        // Validar tipo
        if (isset($hallazgoData['tipo']) && !in_array($hallazgoData['tipo'], $configs['tipos_disponibles'])) {
            $errors[] = 'Tipo no válido';
        }

        // Validar severidad
        if (isset($hallazgoData['severidad']) && !in_array($hallazgoData['severidad'], $configs['severidades'])) {
            $errors[] = 'Severidad no válida';
        }

        return $errors;
    }

    /**
     * Generar código automático para hallazgo
     */
    private function generateHallazgoCode($formato, $empresaId)
    {
        $año = date('Y');
        $ultimoNumero = $this->getUltimoNumeroHallazgo($empresaId, $año);
        $siguienteNumero = $ultimoNumero + 1;
        
        $codigo = str_replace('{YYYY}', $año, $formato);
        $codigo = str_replace('{####}', str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT), $codigo);
        
        return $codigo;
    }

    /**
     * Obtener último número de hallazgo del año
     */
    private function getUltimoNumeroHallazgo($empresaId, $año)
    {
        try {
            $ultimoReporte = Reporte::where('empresa_id', $empresaId)
                                  ->whereYear('created_at', $año)
                                  ->orderBy('created_at', 'desc')
                                  ->first();
            
            if (!$ultimoReporte || !$ultimoReporte->codigo) {
                return 0;
            }

            // Extraer número del código (asumiendo formato HAL-YYYY-####)
            preg_match('/\-(\d+)$/', $ultimoReporte->codigo, $matches);
            return isset($matches[1]) ? intval($matches[1]) : 0;
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo último número de hallazgo: ' . $e->getMessage());
            return 0;
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
     * Configuraciones por defecto para hallazgos
     */
    private function getDefaultHallazgosConfig()
    {
        return [
            'estados_disponibles' => ['abierto', 'en_proceso', 'cerrado', 'cancelado'],
            'tipos_disponibles' => ['incidente', 'accidente', 'condicion_insegura', 'acto_inseguro'],
            'severidades' => ['bajo', 'medio', 'alto', 'critico'],
            'auto_numeracion' => ['enabled' => true, 'formato' => 'HAL-{YYYY}-{####}'],
            'notificaciones_email' => ['enabled' => true, 'roles' => ['admin', 'supervisor']]
        ];
    }

    /**
     * API para obtener configuraciones desde el frontend
     */
    public function getConfigurationsApi($empresaId)
    {
        try {
            $configs = $this->getHallazgosConfig($empresaId);
            
            return response()->json([
                'success' => true,
                'configuraciones' => $configs
            ]);
        } catch (\Exception $e) {
            Log::error('Error en API de configuraciones de hallazgos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener configuraciones'], 500);
        }
    }

    /**
     * Actualizar configuraciones específicas de hallazgos
     */
    public function updateHallazgosConfig(Request $request, $empresaId)
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
                    'hallazgos'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuraciones de hallazgos actualizadas'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuraciones de hallazgos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar configuraciones'], 500);
        }
    }
}
