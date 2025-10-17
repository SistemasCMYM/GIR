<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use App\Services\ConfiguracionService;
use App\Models\Configuracion\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
    protected $configuracionService;

    public function __construct(ConfiguracionService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Determina si el usuario en sesión es SuperAdmin
     */
    private function isSuperAdminFromSession(): bool
    {
        $userData = session('user_data') ?: session('usuario_data');
        if (!$userData) return false;

        $rol = strtolower($userData['rol'] ?? $userData['role'] ?? $userData['tipo'] ?? '');
        $isSuperFlag = !empty($userData['is_super_admin']) && $userData['is_super_admin'] === true;
        return in_array($rol, ['super_admin','superadmin','super administrator','superadministrador','root'], true) || $isSuperFlag;
    }

    /**
     * Mostrar dashboard de configuración principal
     */
    public function index()
    {
        try {
            Log::info('Accediendo al dashboard de configuración');
            
            $userData = session('user_data');
            $empresaData = session('empresa_data');

            // Defensa extra: si no es SuperAdmin, redirigir con aviso (el middleware ya lo hace, pero evitamos fugas)
            if (!$this->isSuperAdminFromSession()) {
                Log::warning('Acceso a Configuración bloqueado en controlador (no SuperAdmin)');
                return redirect()->route('dashboard')->with(['warning' => 'Acceso restringido. Solo SuperAdmin puede acceder a Configuración.']);
            }
            
            Log::info('Datos de sesión - Usuario:', ['user' => $userData ? 'presente' : 'ausente']);
            Log::info('Datos de sesión - Empresa:', ['empresa' => $empresaData ? ($empresaData['id'] ?? 'sin id') : 'ausente']);
            
            // 🔹 PARA SUPERADMIN: Permitir acceso incluso sin empresa_data
            // Si no hay empresa_data, establecer valores por defecto
            $empresaId = null;
            if ($empresaData && isset($empresaData['id'])) {
                $empresaId = $empresaData['id'];
            } else {
                // SuperAdmin sin empresa: usar valores por defecto
                Log::info('SuperAdmin accediendo sin empresa_data - usando valores por defecto');
                $empresaData = [
                    'id' => null,
                    'razon_social' => 'Configuración Global',
                    'nit' => 'N/A'
                ];
            }
            
            // Obtener configuraciones actuales (si hay empresa)
            $configuraciones = [];
            if ($empresaId) {
                try {
                    $configuraciones = [
                        'empresa' => $this->configuracionService->getEmpresaConfigurations($empresaId),
                        'hallazgos' => $this->configuracionService->getHallazgosConfigurations($empresaId),
                        'psicosocial' => $this->configuracionService->getPsicosocialConfigurations($empresaId)
                    ];
                } catch (\Exception $e) {
                    Log::warning('No se pudieron cargar configuraciones de empresa: ' . $e->getMessage());
                    // Para SuperAdmin: mostrar valores vacíos en lugar de error
                    $configuraciones = [
                        'empresa' => collect([]),
                        'hallazgos' => collect([]),
                        'psicosocial' => collect([])
                    ];
                }
            }
            
            // Datos para el dashboard de configuración
            $estadisticas = $this->getEstadisticasConfiguracion($empresaId);
            Log::info('Estadísticas generadas:', $estadisticas);
            
            $configuracionData = [
                'usuario' => $userData,
                'empresa' => $empresaData,
                'configuraciones' => $configuraciones,
                'modulos' => $this->getModulosConfiguracion(),
                'estadisticas' => $estadisticas,
                'database_error' => !$empresaId // Flag para indicar si hay problema de DB
            ];
            
            return view('modules.configuracion.index', $configuracionData);
            
        } catch (\Exception $e) {
            Log::error('Error en dashboard de configuración: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // 🔹 PARA SUPERADMIN: Mostrar el error pero NO bloquear acceso
            if ($this->isSuperAdminFromSession()) {
                // Si es error de conexión a BD, permitir acceso con valores por defecto
                if (str_contains($e->getMessage(), 'Database connection') || 
                    str_contains($e->getMessage(), 'not configured')) {
                    
                    Log::warning('SuperAdmin: Error de conexión a BD detectado, usando valores por defecto');
                    
                    return view('modules.configuracion.index', [
                        'usuario' => session('user_data'),
                        'empresa' => [
                            'id' => null,
                            'razon_social' => 'Configuración Global',
                            'nit' => 'N/A'
                        ],
                        'configuraciones' => [
                            'empresa' => collect([]),
                            'hallazgos' => collect([]),
                            'psicosocial' => collect([])
                        ],
                        'modulos' => $this->getModulosConfiguracion(),
                        'estadisticas' => $this->getEstadisticasConfiguracion(null),
                        'database_error' => true,
                        'error_message' => $e->getMessage()
                    ]);
                }
                
                return redirect()->route('dashboard')->with(['error' => 'Error al cargar la configuración del sistema: ' . $e->getMessage()]);
            }
            
            return redirect()->route('dashboard')->with(['warning' => 'No fue posible abrir Configuración en este momento.']);
        }
    }

    /**
     * Obtener módulos de configuración disponibles
     */
    private function getModulosConfiguracion()
    {
        return [
            'empresa' => [
                'nombre' => 'Empresa',
                'descripcion' => 'Gestión de información y configuración de la empresa',
                'icono' => 'fas fa-building',
                'color' => 'primary',
                'ruta' => 'configuracion.empresa.index',
                'habilitado' => true
            ],
            'estructura' => [
                'nombre' => 'Estructura',
                'descripcion' => 'Gestión de áreas, departamentos y cargos',
                'icono' => 'fas fa-sitemap',
                'color' => 'success',
                'ruta' => 'configuracion.estructura.index',
                'habilitado' => true
            ],
            'fechahora' => [
                'nombre' => 'Fecha y Hora',
                'descripcion' => 'Configuración de zona horaria y formatos de fecha',
                'icono' => 'fas fa-clock',
                'color' => 'warning',
                'ruta' => 'configuracion.fechahora.index',
                'habilitado' => true
            ],
            'reportes' => [
                'nombre' => 'Reportes',
                'descripcion' => 'Configuración de reportes y plantillas',
                'icono' => 'fas fa-file-alt',
                'color' => 'info',
                'ruta' => 'configuracion.reportes.index',
                'habilitado' => true
            ],
            'seguridad' => [
                'nombre' => 'Seguridad',
                'descripcion' => 'Políticas de seguridad y registros de actividad',
                'icono' => 'fas fa-shield-alt',
                'color' => 'danger',
                'ruta' => 'configuracion.seguridad.index',
                'habilitado' => true
            ],
            'notificaciones' => [
                'nombre' => 'Notificaciones',
                'descripcion' => 'Configuración de notificaciones y alertas',
                'icono' => 'fas fa-bell',
                'color' => 'secondary',
                'ruta' => 'configuracion.notificaciones.index',
                'habilitado' => true
            ],
            'integraciones' => [
                'nombre' => 'Integraciones',
                'descripcion' => 'API, webhooks y conexiones externas',
                'icono' => 'fas fa-plug',
                'color' => 'primary',
                'ruta' => 'configuracion.integraciones.index',
                'habilitado' => true
            ],
            'autenticacion' => [
                'nombre' => 'Autenticación',
                'descripcion' => 'Gestión de usuarios y métodos de autenticación',
                'icono' => 'fas fa-users-cog',
                'color' => 'dark',
                'ruta' => 'configuracion.autenticacion.index',
                'habilitado' => true
            ],
            'procesos' => [
                'nombre' => 'Procesos',
                'descripcion' => 'Configuración de procesos y workflows del sistema',
                'icono' => 'fas fa-cogs',
                'color' => 'purple',
                'ruta' => 'configuracion.procesos.index',
                'habilitado' => true
            ]        ];
    }

    /**
     * Obtener estadísticas de configuración
     */    private function getEstadisticasConfiguracion($empresaId = null)
    {
        $defaultStats = [
            'total_configuraciones' => 0,
            'configuraciones_empresa' => 0,
            'configuraciones_hallazgos' => 0,
            'configuraciones_psicosocial' => 0,
            'modulos_activos' => 0,
            'ultima_configuracion' => 'N/A',
            'ultima_modificacion' => null
        ];
        
        if (!$empresaId) {
            Log::info('getEstadisticasConfiguracion: No empresa ID provided, returning defaults');
            return $defaultStats;
        }
        
        try {
            Log::info('getEstadisticasConfiguracion: Processing empresa ID: ' . $empresaId);
            
            // Check if we can access the Configuracion model
            if (!class_exists('\App\Models\Configuracion\Configuracion')) {
                Log::error('getEstadisticasConfiguracion: Configuracion model not found');
                return $defaultStats;
            }
            
            $totalConfigs = Configuracion::where('empresa_id', $empresaId)->count();
            Log::info('getEstadisticasConfiguracion: Total configs found: ' . $totalConfigs);
            
            $configsEmpresa = Configuracion::where('empresa_id', $empresaId)
                                         ->where('modulo', 'empresa')
                                         ->count();
            $configsHallazgos = Configuracion::where('empresa_id', $empresaId)
                                            ->where('modulo', 'hallazgos')
                                            ->count();
            $configsPsicosocial = Configuracion::where('empresa_id', $empresaId)
                                              ->where('modulo', 'psicosocial')
                                              ->count();
            
            $ultimaModificacion = Configuracion::where('empresa_id', $empresaId)
                                              ->orderBy('updated_at', 'desc')
                                              ->first();

            // Calcular módulos activos (basándose en si tienen configuraciones)
            $modulosActivos = 0;
            $modulos = ['empresa', 'estructura', 'fechahora', 'reportes', 'seguridad', 'notificaciones', 'integraciones', 'autenticacion', 'procesos', 'hallazgos', 'psicosocial'];
            
            foreach ($modulos as $modulo) {
                $configCount = Configuracion::where('empresa_id', $empresaId)
                                           ->where('modulo', $modulo)
                                           ->count();
                if ($configCount > 0) {
                    $modulosActivos++;
                }
            }
            
            $estadisticas = [
                'total_configuraciones' => $totalConfigs,
                'configuraciones_empresa' => $configsEmpresa,
                'configuraciones_hallazgos' => $configsHallazgos,
                'configuraciones_psicosocial' => $configsPsicosocial,
                'modulos_activos' => $modulosActivos,
                'ultima_configuracion' => $ultimaModificacion ? $ultimaModificacion->updated_at->format('d/m/Y H:i') : 'N/A',
                'ultima_modificacion' => $ultimaModificacion ? $ultimaModificacion->updated_at : null
            ];
            
            Log::info('getEstadisticasConfiguracion: Successfully generated estadisticas', $estadisticas);
            return $estadisticas;
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de configuración: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $defaultStats;
        }
    }

    /**
     * Actualizar configuración específica
     */
    public function updateConfiguration(Request $request)
    {
        try {
            $request->validate([
                'clave' => 'required|string',
                'valor' => 'required',
                'modulo' => 'nullable|string',
                'descripcion' => 'nullable|string'
            ]);

            $empresaData = session('empresa_data');
            if (!$empresaData || !isset($empresaData['id'])) {
                return response()->json(['error' => 'No se encontró información de empresa'], 400);
            }

            $this->configuracionService->updateConfiguration(
                $empresaData['id'],
                $request->clave,
                $request->valor,
                $request->modulo
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la configuración'], 500);
        }
    }

    /**
     * Obtener configuraciones por componente
     */
    public function getConfigurationsByComponent(Request $request, $componente)
    {
        try {
            $empresaData = session('empresa_data');
            if (!$empresaData || !isset($empresaData['id'])) {
                return response()->json(['error' => 'No se encontró información de empresa'], 400);
            }

            $configuraciones = $this->configuracionService->getConfigurationsByComponent(
                $empresaData['id'],
                $componente
            );

            return response()->json([
                'success' => true,
                'configuraciones' => $configuraciones
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones por componente: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las configuraciones'], 500);
        }
    }}
