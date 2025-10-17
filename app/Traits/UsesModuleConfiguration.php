<?php

namespace App\Traits;

use App\Services\ConfiguracionService;
use App\Http\Controllers\Modules\Configuracion\HallazgosConfigController;
use App\Http\Controllers\Modules\Configuracion\PsicosocialConfigController;
use Illuminate\Support\Facades\Log;

trait UsesModuleConfiguration
{
    protected $configuracionService;
    
    /**
     * Obtener el servicio de configuración
     */
    protected function getConfiguracionService()
    {
        if (!$this->configuracionService) {
            $this->configuracionService = app(ConfiguracionService::class);
        }
        return $this->configuracionService;
    }

    /**
     * Obtener configuraciones específicas para el módulo actual
     */
    protected function getModuleConfigurations($empresaId, $modulo)
    {
        try {
            $service = $this->getConfiguracionService();
            
            switch ($modulo) {
                case 'hallazgos':
                    return $service->getHallazgosConfigurations($empresaId);
                case 'psicosocial':
                    return $service->getPsicosocialConfigurations($empresaId);
                case 'empresa':
                    return $service->getEmpresaConfigurations($empresaId);
                default:
                    return $service->getConfigurationsByComponent($empresaId, $modulo);
            }
        } catch (\Exception $e) {
            Log::error("Error obteniendo configuraciones para módulo {$modulo}: " . $e->getMessage());
            return collect([]);
        }
    }    /**
     * Obtener valor específico de configuración
     */
    protected function getConfigValue($empresaId, $clave, $default = null)
    {
        try {
            $service = $this->getConfiguracionService();
            $configs = $service->getConfigurationsByComponent($empresaId, 'all');
            
            foreach ($configs as $config) {
                if ($config->clave === $clave) {
                    $valor = $config->valor;
                    return is_array($valor) && count($valor) === 1 ? $valor[0] : $valor;
                }
            }
            
            return $default;
        } catch (\Exception $e) {
            Log::error("Error obteniendo valor de configuración {$clave}: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Aplicar configuraciones para crear un nuevo hallazgo
     */
    protected function applyHallazgoConfigurations($empresaId, $hallazgoData)
    {
        try {
            $controller = new HallazgosConfigController($this->getConfiguracionService());
            $configs = $controller->getHallazgosConfig($empresaId);
            
            // Aplicar numeración automática si está habilitada
            if (isset($configs['auto_numeracion']['enabled']) && $configs['auto_numeracion']['enabled']) {
                if (!isset($hallazgoData['codigo']) || empty($hallazgoData['codigo'])) {
                    $hallazgoData['codigo'] = $this->generateHallazgoCode($configs['auto_numeracion']['formato'], $empresaId);
                }
            }

            // Establecer estado inicial si no está definido
            if (!isset($hallazgoData['estado']) && !empty($configs['estados_disponibles'])) {
                $hallazgoData['estado'] = $configs['estados_disponibles'][0];
            }

            // Validar datos según configuraciones
            $errors = $this->validateHallazgoByConfig($hallazgoData, $configs);
            if (!empty($errors)) {
                throw new \InvalidArgumentException('Datos de hallazgo no válidos: ' . implode(', ', $errors));
            }

            return $hallazgoData;
        } catch (\Exception $e) {
            Log::error("Error aplicando configuraciones de hallazgo: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aplicar configuraciones para crear una nueva evaluación psicosocial
     */
    protected function applyPsicosocialConfigurations($empresaId, $evaluacionData)
    {
        try {
            $controller = new PsicosocialConfigController($this->getConfiguracionService());
            $configs = $controller->getPsicosocialConfig($empresaId);
            
            // Aplicar numeración automática si está habilitada
            if (isset($configs['auto_numeracion']['enabled']) && $configs['auto_numeracion']['enabled']) {
                if (!isset($evaluacionData['codigo']) || empty($evaluacionData['codigo'])) {
                    $evaluacionData['codigo'] = $this->generatePsicosocialCode($configs['auto_numeracion']['formato'], $empresaId);
                }
            }

            // Establecer estado inicial si no está definido
            if (!isset($evaluacionData['estado']) && !empty($configs['estados_evaluacion'])) {
                $evaluacionData['estado'] = $configs['estados_evaluacion'][0];
            }

            // Aplicar configuraciones de confidencialidad
            if (isset($configs['confidencialidad'])) {
                $evaluacionData['confidencial'] = $configs['confidencialidad'];
            }

            // Validar datos según configuraciones
            $errors = $this->validatePsicosocialByConfig($evaluacionData, $configs);
            if (!empty($errors)) {
                throw new \InvalidArgumentException('Datos de evaluación no válidos: ' . implode(', ', $errors));
            }

            return $evaluacionData;
        } catch (\Exception $e) {
            Log::error("Error aplicando configuraciones psicosociales: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validar datos de hallazgo según configuraciones
     */
    protected function validateHallazgoByConfig($hallazgoData, $configs)
    {
        $errors = [];

        if (isset($hallazgoData['estado']) && !in_array($hallazgoData['estado'], $configs['estados_disponibles'] ?? [])) {
            $errors[] = 'Estado no válido';
        }

        if (isset($hallazgoData['tipo']) && !in_array($hallazgoData['tipo'], $configs['tipos_disponibles'] ?? [])) {
            $errors[] = 'Tipo no válido';
        }

        if (isset($hallazgoData['severidad']) && !in_array($hallazgoData['severidad'], $configs['severidades'] ?? [])) {
            $errors[] = 'Severidad no válida';
        }

        return $errors;
    }

    /**
     * Validar datos de evaluación psicosocial según configuraciones
     */
    protected function validatePsicosocialByConfig($evaluacionData, $configs)
    {
        $errors = [];

        if (isset($evaluacionData['estado']) && !in_array($evaluacionData['estado'], $configs['estados_evaluacion'] ?? [])) {
            $errors[] = 'Estado no válido';
        }

        if (isset($evaluacionData['instrumento_utilizado']) && !in_array($evaluacionData['instrumento_utilizado'], $configs['instrumentos_disponibles'] ?? [])) {
            $errors[] = 'Instrumento no válido';
        }

        if (isset($evaluacionData['nivel_riesgo_general']) && !in_array($evaluacionData['nivel_riesgo_general'], $configs['niveles_riesgo'] ?? [])) {
            $errors[] = 'Nivel de riesgo no válido';
        }

        return $errors;
    }

    /**
     * Generar código automático para hallazgo
     */
    protected function generateHallazgoCode($formato, $empresaId)
    {
        try {
            $año = date('Y');
            $ultimoNumero = $this->getUltimoNumeroHallazgo($empresaId, $año);
            $siguienteNumero = $ultimoNumero + 1;
            
            $codigo = str_replace('{YYYY}', $año, $formato);
            $codigo = str_replace('{####}', str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT), $codigo);
            
            return $codigo;
        } catch (\Exception $e) {
            Log::error("Error generando código de hallazgo: " . $e->getMessage());
            return 'HAL-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Generar código automático para evaluación psicosocial
     */
    protected function generatePsicosocialCode($formato, $empresaId)
    {
        try {
            $año = date('Y');
            $ultimoNumero = $this->getUltimoNumeroPsicosocial($empresaId, $año);
            $siguienteNumero = $ultimoNumero + 1;
            
            $codigo = str_replace('{YYYY}', $año, $formato);
            $codigo = str_replace('{####}', str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT), $codigo);
            
            return $codigo;
        } catch (\Exception $e) {
            Log::error("Error generando código psicosocial: " . $e->getMessage());
            return 'PSI-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Obtener último número de hallazgo del año
     */
    protected function getUltimoNumeroHallazgo($empresaId, $año)
    {
        // Esta función debería implementarse en el controlador que use el trait
        // Ya que depende del modelo específico
        return 0;
    }

    /**
     * Obtener último número de evaluación psicosocial del año
     */
    protected function getUltimoNumeroPsicosocial($empresaId, $año)
    {
        // Esta función debería implementarse en el controlador que use el trait
        // Ya que depende del modelo específico
        return 0;
    }

    /**
     * Obtener configuraciones para formularios dinámicos
     */
    protected function getFormConfigurations($empresaId, $modulo)
    {
        try {
            $configs = $this->getModuleConfigurations($empresaId, $modulo);
            
            $formConfigs = [];
            foreach ($configs as $config) {
                if (str_contains($config->clave, 'form_') || str_contains($config->clave, 'campos_')) {
                    $formConfigs[$config->clave] = [
                        'valor' => $config->valor,
                        'tipo' => $config->tipo_dato,
                        'descripcion' => $config->descripcion
                    ];
                }
            }
            
            return $formConfigs;
        } catch (\Exception $e) {
            Log::error("Error obteniendo configuraciones de formulario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si un módulo está habilitado según configuración
     */
    protected function isModuleEnabled($empresaId, $modulo)
    {
        $habilitado = $this->getConfigValue($empresaId, "modulo_{$modulo}_habilitado", true);
        return $habilitado === true || $habilitado === '1' || $habilitado === 1;
    }

    /**
     * Obtener configuraciones de notificaciones para un módulo
     */
    protected function getNotificationConfigurations($empresaId, $modulo)
    {
        $configs = $this->getModuleConfigurations($empresaId, 'notificaciones');
        
        $notificationConfigs = [];
        foreach ($configs as $config) {
            if (str_contains($config->clave, $modulo) && str_contains($config->clave, 'notif')) {
                $notificationConfigs[$config->clave] = $config->valor;
            }
        }
        
        return $notificationConfigs;
    }
}
