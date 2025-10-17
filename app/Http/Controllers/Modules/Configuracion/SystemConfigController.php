<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use App\Services\ConfiguracionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SystemConfigController extends Controller
{
    protected $configuracionService;

    public function __construct(ConfiguracionService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Mostrar panel de configuración del sistema modular
     */
    public function index(Request $request)
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            // 🔹 BYPASS PARA SUPERADMIN: Permitir acceso sin empresa_data
            $isSuperAdmin = $this->isSuperAdmin($userData);
            
            if (!$empresaData || !isset($empresaData['id'])) {
                if ($isSuperAdmin) {
                    // SuperAdmin sin empresa: usar valores por defecto
                    Log::info('SuperAdmin accediendo a SystemConfig sin empresa_data');
                    $empresaData = [
                        'id' => null,
                        'razon_social' => 'Configuración Global del Sistema',
                        'nit' => 'N/A'
                    ];
                    $empresaId = null;
                } else {
                    // Usuario normal sin empresa: error
                    return redirect()->route('dashboard')->with('error', 'No se encontró información de empresa');
                }
            } else {
                $empresaId = $empresaData['id'];
            }

            // Obtener todas las configuraciones modulares (solo si hay empresa)
            $modularConfigurations = [];
            if ($empresaId) {
                try {
                    $modularConfigurations = [
                        'empresa' => $this->configuracionService->getEmpresaConfigurations($empresaId),
                        'estructura' => $this->configuracionService->getEstructuraConfigurations($empresaId),
                        'fechahora' => $this->configuracionService->getFechaHoraConfigurations($empresaId),
                        'reportes' => $this->configuracionService->getReportesConfigurations($empresaId),
                        'seguridad' => $this->configuracionService->getSeguridadConfigurations($empresaId),
                        'notificaciones' => $this->configuracionService->getNotificacionesConfigurations($empresaId),
                        'integraciones' => $this->configuracionService->getIntegracionesConfigurations($empresaId),
                        'autenticacion' => $this->configuracionService->getAutenticacionConfigurations($empresaId),
                        'procesos' => $this->configuracionService->getProcesosConfigurations($empresaId),
                        'hallazgos' => $this->configuracionService->getHallazgosConfigurations($empresaId),
                        'psicosocial' => $this->configuracionService->getPsicosocialConfigurations($empresaId)
                    ];
                } catch (\Exception $e) {
                    Log::warning('No se pudieron cargar configuraciones modulares: ' . $e->getMessage());
                    // Para SuperAdmin: Devolver colecciones vacías en lugar de error
                    $modularConfigurations = array_fill_keys([
                        'empresa', 'estructura', 'fechahora', 'reportes', 'seguridad',
                        'notificaciones', 'integraciones', 'autenticacion', 'procesos',
                        'hallazgos', 'psicosocial'
                    ], collect([]));
                }
            }

            // Estructurar datos para la vista basándome en la referencia SystemSettings
            $data = [
                'empresa' => $empresaData,
                'usuario' => $userData,
                'configuraciones' => $modularConfigurations,
                'tabs' => $this->getSystemTabs(),
                'modulos_activos' => $this->getModulosActivos($empresaId),
                'metadatos' => $this->getConfigurationMetadata(),
                'is_super_admin' => $isSuperAdmin,
                'database_error' => !$empresaId // Flag para indicar si hay problema de DB
            ];

            return view('modules.configuracion.system.index', $data);

        } catch (\Exception $e) {
            Log::error('Error al cargar configuración del sistema: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // 🔹 PARA SUPERADMIN: Mostrar el error pero NO bloquear acceso
            $isSuperAdmin = $this->isSuperAdmin(session('user_data'));
            
            if ($isSuperAdmin) {
                // Si es error de conexión a BD, permitir acceso con valores por defecto
                if (str_contains($e->getMessage(), 'Database connection') || 
                    str_contains($e->getMessage(), 'not configured')) {
                    
                    Log::warning('SuperAdmin: Error de conexión a BD detectado en SystemConfig, usando valores por defecto');
                    
                    return view('modules.configuracion.system.index', [
                        'empresa' => [
                            'id' => null,
                            'razon_social' => 'Configuración Global del Sistema',
                            'nit' => 'N/A'
                        ],
                        'usuario' => session('user_data'),
                        'configuraciones' => array_fill_keys([
                            'empresa', 'estructura', 'fechahora', 'reportes', 'seguridad',
                            'notificaciones', 'integraciones', 'autenticacion', 'procesos',
                            'hallazgos', 'psicosocial'
                        ], collect([])),
                        'tabs' => $this->getSystemTabs(),
                        'modulos_activos' => $this->getModulosActivos(null),
                        'metadatos' => $this->getConfigurationMetadata(),
                        'is_super_admin' => true,
                        'database_error' => true,
                        'error_message' => $e->getMessage()
                    ]);
                }
            }
            
            return back()->with('error', 'Error al cargar la configuración del sistema: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar configuración del sistema
     */
    public function updateConfiguration(Request $request)
    {
        try {
            $request->validate([
                'modulo' => 'required|string',
                'configuraciones' => 'required|array'
            ]);

            $empresaData = session('empresa_data');
            if (!$empresaData || !isset($empresaData['id'])) {
                return response()->json(['error' => 'No se encontró información de empresa'], 400);
            }

            $empresaId = $empresaData['id'];
            $modulo = $request->modulo;
            $configuraciones = $request->configuraciones;

            // Actualizar configuraciones por módulo
            foreach ($configuraciones as $clave => $valor) {
                $this->configuracionService->updateConfiguration(
                    $empresaId,
                    $clave,
                    $valor,
                    $modulo
                );
            }            // Limpiar cache del módulo
            Cache::forget("{$modulo}_config_{$empresaId}");

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración del sistema: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la configuración'], 500);
        }
    }

    /**
     * Obtener configuración de un módulo específico
     */
    public function getModuleConfig(Request $request, $module)
    {
        try {
            $empresaData = session('empresa_data');
            if (!$empresaData || !isset($empresaData['id'])) {
                return response()->json(['error' => 'No se encontró información de empresa'], 400);
            }

            $empresaId = $empresaData['id'];
            $configurations = [];

            // Obtener configuraciones según el módulo
            switch ($module) {
                case 'empresa':
                    $configurations = $this->configuracionService->getEmpresaConfigurations($empresaId);
                    break;
                case 'estructura':
                    $configurations = $this->configuracionService->getEstructuraConfigurations($empresaId);
                    break;
                case 'fechahora':
                    $configurations = $this->configuracionService->getFechaHoraConfigurations($empresaId);
                    break;
                case 'reportes':
                    $configurations = $this->configuracionService->getReportesConfigurations($empresaId);
                    break;
                case 'seguridad':
                    $configurations = $this->configuracionService->getSeguridadConfigurations($empresaId);
                    break;
                case 'notificaciones':
                    $configurations = $this->configuracionService->getNotificacionesConfigurations($empresaId);
                    break;
                case 'integraciones':
                    $configurations = $this->configuracionService->getIntegracionesConfigurations($empresaId);
                    break;
                case 'autenticacion':
                    $configurations = $this->configuracionService->getAutenticacionConfigurations($empresaId);
                    break;
                case 'procesos':
                    $configurations = $this->configuracionService->getProcesosConfigurations($empresaId);
                    break;
                case 'hallazgos':
                    $configurations = $this->configuracionService->getHallazgosConfigurations($empresaId);
                    break;
                case 'psicosocial':
                    $configurations = $this->configuracionService->getPsicosocialConfigurations($empresaId);
                    break;
                default:
                    return response()->json(['error' => 'Módulo no válido'], 400);
            }

            return response()->json([
                'success' => true,
                'configurations' => $configurations
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo configuración del módulo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener la configuración'], 500);
        }
    }

    /**
     * Actualizar configuración de un módulo específico
     */
    public function updateModuleConfig(Request $request, $module)
    {
        try {
            $request->validate([
                'configuraciones' => 'required|array'
            ]);

            $empresaData = session('empresa_data');
            if (!$empresaData || !isset($empresaData['id'])) {
                return response()->json(['error' => 'No se encontró información de empresa'], 400);
            }

            $empresaId = $empresaData['id'];
            $configuraciones = $request->configuraciones;

            // Actualizar configuraciones del módulo específico
            foreach ($configuraciones as $clave => $valor) {
                $this->configuracionService->updateConfiguration(
                    $empresaId,
                    $clave,
                    $valor,
                    $module
                );
            }            // Limpiar cache del módulo
            Cache::forget("{$module}_config_{$empresaId}");

            return response()->json([
                'success' => true,
                'message' => 'Configuración del módulo actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración del módulo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la configuración del módulo'], 500);
        }
    }

    /**
     * Obtener pestañas del sistema basándose en la referencia
     */
    private function getSystemTabs()
    {
        return [
            [
                'id' => 'empresa',
                'name' => 'Empresa',
                'icon' => 'fas fa-building',
                'description' => 'Configuración general de la empresa',
                'active' => true
            ],
            [
                'id' => 'estructura',
                'name' => 'Estructura',
                'icon' => 'fas fa-sitemap',
                'description' => 'Estructura organizacional'
            ],
            [
                'id' => 'fechahora',
                'name' => 'Fecha y Hora',
                'icon' => 'fas fa-clock',
                'description' => 'Configuración de fecha y hora'
            ],
            [
                'id' => 'reportes',
                'name' => 'Reportes',
                'icon' => 'fas fa-chart-bar',
                'description' => 'Configuración de reportes'
            ],
            [
                'id' => 'seguridad',
                'name' => 'Seguridad',
                'icon' => 'fas fa-shield-alt',
                'description' => 'Configuración de seguridad'
            ],
            [
                'id' => 'notificaciones',
                'name' => 'Notificaciones',
                'icon' => 'fas fa-bell',
                'description' => 'Configuración de notificaciones'
            ],
            [
                'id' => 'integraciones',
                'name' => 'Integraciones',
                'icon' => 'fas fa-plug',
                'description' => 'Configuración de integraciones'
            ],
            [
                'id' => 'autenticacion',
                'name' => 'Autenticación',
                'icon' => 'fas fa-lock',
                'description' => 'Configuración de autenticación'
            ],
            [
                'id' => 'procesos',
                'name' => 'Procesos',
                'icon' => 'fas fa-cogs',
                'description' => 'Configuración de procesos'
            ],
            [
                'id' => 'hallazgos',
                'name' => 'Hallazgos',
                'icon' => 'fas fa-search',
                'description' => 'Configuración del módulo de hallazgos'
            ],
            [
                'id' => 'psicosocial',
                'name' => 'Psicosocial',
                'icon' => 'fas fa-brain',
                'description' => 'Configuración del módulo psicosocial'
            ]
        ];
    }

    /**
     * Obtener módulos activos
     */
    private function getModulosActivos($empresaId)
    {
        return [
            'empresa' => true,
            'estructura' => true,
            'fechahora' => true,
            'reportes' => true,
            'seguridad' => true,
            'notificaciones' => true,
            'integraciones' => true,
            'autenticacion' => true,
            'procesos' => true,
            'hallazgos' => true,
            'psicosocial' => true
        ];
    }

    /**
     * Obtener metadatos de configuración
     */
    private function getConfigurationMetadata()
    {
        return [
            'version' => '1.0.0',
            'ultima_actualizacion' => now()->format('Y-m-d H:i:s'),
            'total_configuraciones' => 11,
            'configuraciones_criticas' => ['seguridad', 'autenticacion', 'hallazgos', 'psicosocial']
        ];
    }

    /**
     * Verificar si el usuario es Super Administrador
     * Compatible con múltiples formatos de datos de sesión
     */
    private function isSuperAdmin($userData): bool
    {
        if (!$userData) {
            return false;
        }

        // Convertir a array si es objeto
        $data = is_object($userData) ? (array)$userData : $userData;

        // Lista de roles/tipos válidos para SuperAdmin
        $validRoles = ['super_admin', 'superadmin', 'root', 'super administrator', 'superadministrador'];
        
        // 1. Verificar por campo 'rol'
        if (isset($data['rol']) && in_array(strtolower($data['rol']), $validRoles, true)) {
            return true;
        }
        
        // 2. Verificar por campo 'tipo'
        if (isset($data['tipo']) && in_array(strtolower($data['tipo']), $validRoles, true)) {
            return true;
        }

        // 3. Verificar por flag booleano isSuperAdmin
        if (isset($data['isSuperAdmin']) && $data['isSuperAdmin'] === true) {
            return true;
        }

        // 4. Verificar por flag booleano is_super_admin
        if (isset($data['is_super_admin']) && $data['is_super_admin'] === true) {
            return true;
        }

        // 5. Verificación por email predefinido (configurado en .env)
        $superAdminConfig = config('app.super_admins', []);
        $superAdminEmails = $superAdminConfig['emails'] ?? [];
        
        if (isset($data['email']) && in_array(strtolower($data['email']), array_map('strtolower', $superAdminEmails))) {
            return true;
        }

        return false;
    }
}
