<?php

namespace App\Services;

use App\Models\Configuracion\Configuracion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ConfiguracionService
{
    protected $cacheTime = 3600; // 1 hora en cache

    /**
     * Obtener configuraciones de empresa con cache
     */
    public function getEmpresaConfigurations($empresaId)
    {
        try {
            $cacheKey = "empresa_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                return $this->getDefaultEmpresaConfig($empresaId);
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de empresa: ' . $e->getMessage());
            return collect([]); // Devolver colección vacía en caso de error
        }
    }

    /**
     * Obtener configuraciones por defecto para empresa
     */
    protected function getDefaultEmpresaConfig($empresaId)
    {
        try {
            $configs = Configuracion::getEmpresaConfig($empresaId);
            
            // Si no existen configuraciones, crear las por defecto
            if ($configs->isEmpty()) {
                $this->createDefaultEmpresaConfig($empresaId);
                $configs = Configuracion::getEmpresaConfig($empresaId);
            }

            return $configs;
        } catch (\Exception $e) {
            Log::warning('Error en getDefaultEmpresaConfig: ' . $e->getMessage());
            return collect([]); // Devolver colección vacía
        }
    }

    /**
     * Crear configuraciones por defecto para empresa
     */
    protected function createDefaultEmpresaConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'empresa',
                'componente' => 'informacion_general',
                'clave' => 'empresa_nombre_corto',
                'valor' => [''],
                'tipo_dato' => 'string',
                'descripcion' => 'Nombre corto de la empresa',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 1
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'empresa',
                'componente' => 'informacion_general',
                'clave' => 'empresa_logo_url',
                'valor' => [''],
                'tipo_dato' => 'string',
                'descripcion' => 'URL del logo de la empresa',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 2
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'empresa',
                'componente' => 'contacto',
                'clave' => 'empresa_email_contacto',
                'valor' => [''],
                'tipo_dato' => 'email',
                'descripcion' => 'Email de contacto principal',
                'activo' => true,
                'aplicar_a_modulos' => ['notificaciones', 'hallazgos'],
                'orden' => 3
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Configuraciones para módulo de Hallazgos
     */
    public function getHallazgosConfigurations($empresaId)
    {
        try {
            $cacheKey = "hallazgos_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::getHallazgosConfig($empresaId);
                
                if ($configs->isEmpty()) {
                    $this->createDefaultHallazgosConfig($empresaId);
                    $configs = Configuracion::getHallazgosConfig($empresaId);
                }

                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de hallazgos: ' . $e->getMessage());
            return collect([]); // Devolver colección vacía en caso de error
        }
    }

    /**
     * Crear configuraciones por defecto para Hallazgos
     */
    protected function createDefaultHallazgosConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'reportes',
                'componente' => 'hallazgos',
                'clave' => 'hallazgos_auto_numeracion',
                'valor' => ['enabled' => true, 'formato' => 'HAL-{YYYY}-{####}'],
                'tipo_dato' => 'object',
                'descripcion' => 'Configuración de numeración automática para hallazgos',
                'activo' => true,
                'aplicar_a_modulos' => ['hallazgos'],
                'orden' => 1
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'notificaciones',
                'componente' => 'hallazgos',
                'clave' => 'hallazgos_notif_email',
                'valor' => ['enabled' => true, 'roles' => ['admin', 'supervisor']],
                'tipo_dato' => 'object',
                'descripcion' => 'Configuración de notificaciones por email para hallazgos',
                'activo' => true,
                'aplicar_a_modulos' => ['hallazgos'],
                'orden' => 2
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'seguridad',
                'componente' => 'hallazgos',
                'clave' => 'hallazgos_categorias_severidad',
                'valor' => ['bajo', 'medio', 'alto', 'critico'],
                'tipo_dato' => 'array',
                'descripcion' => 'Categorías de severidad para hallazgos',
                'activo' => true,
                'aplicar_a_modulos' => ['hallazgos'],
                'orden' => 3
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Configuraciones para módulo Psicosocial
     */
    public function getPsicosocialConfigurations($empresaId)
    {
        try {
            $cacheKey = "psicosocial_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::getPsicosocialConfig($empresaId);
                
                if ($configs->isEmpty()) {
                    $this->createDefaultPsicosocialConfig($empresaId);
                    $configs = Configuracion::getPsicosocialConfig($empresaId);
                }

                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones psicosociales: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Psicosocial
     */
    protected function createDefaultPsicosocialConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'procesos',
                'componente' => 'psicosocial',
                'clave' => 'psicosocial_instrumentos_disponibles',
                'valor' => ['ISTAS21', 'FPSICO', 'Personalizado'],
                'tipo_dato' => 'array',
                'descripcion' => 'Instrumentos de evaluación psicosocial disponibles',
                'activo' => true,
                'aplicar_a_modulos' => ['psicosocial'],
                'orden' => 1
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'reportes',
                'componente' => 'psicosocial',
                'clave' => 'psicosocial_auto_numeracion',
                'valor' => ['enabled' => true, 'formato' => 'PSI-{YYYY}-{####}'],
                'tipo_dato' => 'object',
                'descripcion' => 'Configuración de numeración automática para evaluaciones psicosociales',
                'activo' => true,
                'aplicar_a_modulos' => ['psicosocial'],
                'orden' => 2
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'seguridad',
                'componente' => 'psicosocial',
                'clave' => 'psicosocial_niveles_riesgo',
                'valor' => ['sin_riesgo', 'riesgo_bajo', 'riesgo_medio', 'riesgo_alto'],
                'tipo_dato' => 'array',
                'descripcion' => 'Niveles de riesgo psicosocial',
                'activo' => true,
                'aplicar_a_modulos' => ['psicosocial'],
                'orden' => 3
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'fechahora',
                'componente' => 'psicosocial',
                'clave' => 'psicosocial_periodicidad_evaluacion',
                'valor' => ['value' => 12, 'unit' => 'meses'],
                'tipo_dato' => 'object',
                'descripcion' => 'Periodicidad para realizar evaluaciones psicosociales',
                'activo' => true,
                'aplicar_a_modulos' => ['psicosocial'],
                'orden' => 4
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Estructura Organizacional
     */
    public function getEstructuraConfigurations($empresaId)
    {
        try {
            $cacheKey = "estructura_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'estructura')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultEstructuraConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'estructura')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de estructura: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Estructura
     */
    protected function createDefaultEstructuraConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'organigrama',
                'clave' => 'estructura_jerarquica_habilitada',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar estructura jerárquica'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'areas',
                'clave' => 'areas_departamentos_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar gestión de áreas y departamentos'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'cargos',
                'clave' => 'cargos_personalizados_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar cargos personalizados'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Fecha y Hora
     */
    public function getFechaHoraConfigurations($empresaId)
    {
        try {
            $cacheKey = "fechahora_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'fechahora')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultFechaHoraConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'fechahora')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de fecha y hora: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Fecha y Hora
     */
    protected function createDefaultFechaHoraConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'fechahora',
                'componente' => 'formato',
                'clave' => 'formato_fecha',
                'valor' => 'DD/MM/YYYY',
                'tipo_dato' => 'string',
                'descripcion' => 'Formato de fecha del sistema'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'fechahora',
                'componente' => 'formato',
                'clave' => 'formato_hora',
                'valor' => '24',
                'tipo_dato' => 'string',
                'descripcion' => 'Formato de hora (12/24 horas)'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'fechahora',
                'componente' => 'zona_horaria',
                'clave' => 'zona_horaria_principal',
                'valor' => 'America/Bogota',
                'tipo_dato' => 'string',
                'descripcion' => 'Zona horaria principal de la empresa'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Reportes
     */
    public function getReportesConfigurations($empresaId)
    {
        try {
            $cacheKey = "reportes_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'reportes')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultReportesConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'reportes')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de reportes: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Reportes
     */
    protected function createDefaultReportesConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'reportes',
                'componente' => 'generacion',
                'clave' => 'reportes_automaticos_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar generación automática de reportes'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'reportes',
                'componente' => 'formato',
                'clave' => 'formato_reporte_predeterminado',
                'valor' => 'PDF',
                'tipo_dato' => 'string',
                'descripcion' => 'Formato predeterminado para reportes'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'reportes',
                'componente' => 'plantillas',
                'clave' => 'plantillas_personalizadas_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar plantillas personalizadas'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Seguridad
     */
    public function getSeguridadConfigurations($empresaId)
    {
        try {
            $cacheKey = "seguridad_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'seguridad')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultSeguridadConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'seguridad')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de seguridad: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Seguridad
     */
    protected function createDefaultSeguridadConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'seguridad',
                'componente' => 'autenticacion',
                'clave' => 'autenticacion_doble_factor',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar autenticación de doble factor'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'seguridad',
                'componente' => 'sesiones',
                'clave' => 'tiempo_expiracion_sesion',
                'valor' => '120',
                'tipo_dato' => 'integer',
                'descripcion' => 'Tiempo de expiración de sesión en minutos'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'seguridad',
                'componente' => 'backup',
                'clave' => 'backup_automatico_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar backup automático'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Notificaciones
     */
    public function getNotificacionesConfigurations($empresaId)
    {
        try {
            $cacheKey = "notificaciones_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'notificaciones')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultNotificacionesConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'notificaciones')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de notificaciones: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Notificaciones
     */
    protected function createDefaultNotificacionesConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'notificaciones',
                'componente' => 'email',
                'clave' => 'notificaciones_email_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar notificaciones por email'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'notificaciones',
                'componente' => 'sistema',
                'clave' => 'notificaciones_sistema_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar notificaciones del sistema'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'notificaciones',
                'componente' => 'plantillas',
                'clave' => 'plantillas_personalizadas_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar plantillas personalizadas'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Integraciones
     */
    public function getIntegracionesConfigurations($empresaId)
    {
        try {
            $cacheKey = "integraciones_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'integraciones')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultIntegracionesConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'integraciones')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de integraciones: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Integraciones
     */
    protected function createDefaultIntegracionesConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'integraciones',
                'componente' => 'api',
                'clave' => 'api_externa_habilitado',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar integraciones API externas'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'integraciones',
                'componente' => 'webhooks',
                'clave' => 'webhooks_habilitado',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar webhooks'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'integraciones',
                'componente' => 'sso',
                'clave' => 'sso_habilitado',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar Single Sign-On'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Autenticación
     */
    public function getAutenticacionConfigurations($empresaId)
    {
        try {
            $cacheKey = "autenticacion_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'autenticacion')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultAutenticacionConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'autenticacion')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de autenticación: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Autenticación
     */
    protected function createDefaultAutenticacionConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'autenticacion',
                'componente' => 'politicas',
                'clave' => 'politica_contrasena_compleja',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Exigir contraseñas complejas'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'autenticacion',
                'componente' => 'intentos',
                'clave' => 'max_intentos_login',
                'valor' => '5',
                'tipo_dato' => 'integer',
                'descripcion' => 'Máximo número de intentos de login'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'autenticacion',
                'componente' => 'bloqueo',
                'clave' => 'tiempo_bloqueo_cuenta',
                'valor' => '30',
                'tipo_dato' => 'integer',
                'descripcion' => 'Tiempo de bloqueo de cuenta en minutos'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Obtener configuraciones de Procesos
     */
    public function getProcesosConfigurations($empresaId)
    {
        try {
            $cacheKey = "procesos_config_{$empresaId}";
            
            return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId) {
                $configs = Configuracion::where('empresa_id', $empresaId)
                    ->where('modulo', 'procesos')
                    ->get();
                
                if ($configs->isEmpty()) {
                    $this->createDefaultProcesosConfig($empresaId);
                    $configs = Configuracion::where('empresa_id', $empresaId)
                        ->where('modulo', 'procesos')
                        ->get();
                }
                
                return $configs;
            });
        } catch (\Exception $e) {
            Log::warning('Error al obtener configuraciones de procesos: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Crear configuraciones por defecto para Procesos
     */
    protected function createDefaultProcesosConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'procesos',
                'componente' => 'automatizacion',
                'clave' => 'procesos_automaticos_habilitado',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar procesos automáticos'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'procesos',
                'componente' => 'flujo_trabajo',
                'clave' => 'flujos_trabajo_personalizados',
                'valor' => 'true',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar flujos de trabajo personalizados'
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'procesos',
                'componente' => 'aprobaciones',
                'clave' => 'aprobaciones_multinivel',
                'valor' => 'false',
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar aprobaciones multinivel'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Actualizar configuración
     */
    public function updateConfiguration($empresaId, $clave, $valor, $modulo = null)
    {
        try {
            $config = Configuracion::setValue($empresaId, $clave, $valor, $modulo);
            
            // Limpiar cache relacionado
            $this->clearConfigurationCache($empresaId);            Log::info("Configuración actualizada", [
                'empresa_id' => $empresaId,
                'clave' => $clave,
                'modulo' => $modulo,
                'usuario' => Auth::check() && Auth::user() ? Auth::user()->id : null
            ]);

            return $config;
        } catch (\Exception $e) {
            Log::error("Error actualizando configuración", [
                'empresa_id' => $empresaId,
                'clave' => $clave,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener configuraciones por componente
     */
    public function getConfigurationsByComponent($empresaId, $componente)
    {
        $cacheKey = "config_component_{$empresaId}_{$componente}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId, $componente) {
            return Configuracion::getByComponente($empresaId, $componente);
        });
    }

    /**
     * Obtener valor específico de configuración
     */
    public function getConfigValue($empresaId, $clave, $default = null)
    {
        $cacheKey = "config_value_{$empresaId}_{$clave}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($empresaId, $clave, $default) {
            return Configuracion::getValue($empresaId, $clave, $default);
        });
    }

    /**
     * Limpiar cache de configuraciones
     */
    public function clearConfigurationCache($empresaId)
    {
        $patterns = [
            "empresa_config_{$empresaId}",
            "hallazgos_config_{$empresaId}",
            "psicosocial_config_{$empresaId}",
            "config_component_{$empresaId}_*",
            "config_value_{$empresaId}_*"
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // Para patrones con comodín, necesitaríamos implementar una limpieza más específica
                // Por ahora, limpiamos todo el cache de configuraciones
                Cache::forget(str_replace('*', '', $pattern));
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Validar configuraciones antes de guardar
     */
    public function validateConfiguration($clave, $valor, $tipo_dato)
    {
        switch ($tipo_dato) {
            case 'email':
                return filter_var($valor, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($valor, FILTER_VALIDATE_URL) !== false;
            case 'integer':
                return is_numeric($valor) && intval($valor) == $valor;
            case 'boolean':
                return is_bool($valor) || in_array($valor, ['true', 'false', '1', '0']);
            case 'array':
                return is_array($valor);
            case 'object':
                return is_array($valor) || is_object($valor);
            default:
                return true; // Para string y otros tipos
        }
    }

    /**
     * Exportar configuraciones de empresa
     */
    public function exportConfigurations($empresaId)
    {
        return Configuracion::where('empresa_id', $empresaId)
                          ->where('activo', true)
                          ->orderBy('modulo')
                          ->orderBy('componente')
                          ->orderBy('orden')
                          ->get()
                          ->toArray();
    }

    /**
     * Importar configuraciones a empresa
     */
    public function importConfigurations($empresaId, $configurations)
    {
        try {
            foreach ($configurations as $config) {
                unset($config['_id'], $config['created_at'], $config['updated_at']);
                $config['empresa_id'] = $empresaId;
                
                Configuracion::updateOrCreate(
                    [
                        'empresa_id' => $empresaId,
                        'clave' => $config['clave']
                    ],
                    $config
                );
            }
            
            $this->clearConfigurationCache($empresaId);
            return true;
        } catch (\Exception $e) {
            Log::error("Error importando configuraciones", [
                'empresa_id' => $empresaId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Método helper para ejecutar consultas de configuración de forma segura
     * Retorna colección vacía en caso de error (útil cuando la DB no está configurada)
     */
    protected function safeConfigQuery($callback, $errorContext = 'configuración')
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::warning("Error al obtener {$errorContext}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]); // Devolver colección vacía en caso de error
        }
    }
}
