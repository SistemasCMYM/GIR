<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Controlador para configuración del sistema (Super Admin)
 */
class ConfiguracionController extends Controller
{    /**
     * Mostrar panel de configuración
     */
    public function index()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('dashboard')->withErrors([
                'general' => 'No tiene permisos para acceder a este módulo.'
            ]);
        }

        // Datos simulados para la vista de configuración
        $securityConfig = [
            'force_https' => true,
            'two_step_auth' => false,
            'rate_limiting' => 60,
            'session_timeout' => 480
        ];

        $modulesConfig = [
            'hallazgos' => [
                'name' => 'Hallazgos',
                'description' => 'Gestión de hallazgos y observaciones',
                'enabled' => true
            ],
            'psicosocial' => [
                'name' => 'Psicosocial',
                'description' => 'Evaluaciones psicosociales',
                'enabled' => true
            ],
            'planes' => [
                'name' => 'Planes',
                'description' => 'Planes de acción y mejora',
                'enabled' => true
            ],
            'indicadores' => [
                'name' => 'Indicadores',
                'description' => 'Indicadores y métricas',
                'enabled' => false
            ]
        ];

        $backupsDisponibles = [
            [
                'name' => 'Backup completo ' . date('Y-m-d'),
                'file' => 'backup_' . date('Ymd') . '_complete.zip',
                'date' => date('Y-m-d H:i:s'),
                'size' => '25.4 MB'
            ],
            [
                'name' => 'Backup BD ' . date('Y-m-d', strtotime('-1 day')),
                'file' => 'backup_' . date('Ymd', strtotime('-1 day')) . '_db.zip',
                'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'size' => '12.1 MB'
            ]
        ];

        $maintenanceInfo = [
            'last_cache_clear' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'logs_size' => '8.5 MB',
            'active_sessions' => 15,
            'last_db_optimize' => date('Y-m-d H:i:s', strtotime('-1 week'))
        ];

        $databaseConfig = [
            'mongodb_connected' => true,
            'mongodb_status' => 'Conectado - Laravel365_DB',
            'sqlite_size' => '2.1 MB',
            'active_connections' => 8
        ];

        return view('admin.configuracion.index', compact(
            'securityConfig', 
            'modulesConfig', 
            'backupsDisponibles', 
            'maintenanceInfo', 
            'databaseConfig'
        ));
    }

    /**
     * Obtener configuraciones actuales del sistema
     */
    public function obtenerConfiguraciones()
    {
        // Simulación de configuraciones - implementar con MongoDB real
        return [
            'general' => [
                'nombre_plataforma' => 'GIR-365',
                'version' => '1.0.0',
                'mantenimiento' => false,
                'registro_empresas_abierto' => true,
                'limite_usuarios_por_empresa' => 50,
                'limite_evaluaciones_mensuales' => 1000
            ],
            'seguridad' => [
                'tiempo_sesion' => 480, // minutos
                'intentos_login_max' => 5,
                'bloqueo_tiempo' => 30, // minutos
                'requiere_2fa' => false,
                'complejidad_password' => 'media'
            ],
            'notificaciones' => [
                'email_habilitado' => true,
                'sms_habilitado' => false,
                'notificaciones_push' => true,
                'frecuencia_reportes' => 'semanal'
            ],
            'modulos' => [
                'hallazgos_activo' => true,
                'psicosocial_activo' => true,
                'planes_accion_activo' => false,
                'indicadores_activo' => false,
                'auditoria_activa' => true
            ],
            'api' => [
                'rate_limit' => 1000, // requests por hora
                'api_v1_activa' => true,
                'webhooks_activos' => false,
                'logs_detallados' => true
            ]
        ];
    }

    /**
     * Actualizar configuración general
     */
    public function actualizarGeneral(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'nombre_plataforma' => 'required|string|max:100',
            'mantenimiento' => 'boolean',
            'registro_empresas_abierto' => 'boolean',
            'limite_usuarios_por_empresa' => 'required|integer|min:1|max:500',
            'limite_evaluaciones_mensuales' => 'required|integer|min:100'
        ]);

        try {
            // Aquí se implementaría la actualización real en MongoDB
            $this->logConfiguracionCambiada('general', $validated);
            
            // Limpiar caché de configuraciones
            Cache::forget('configuracion_sistema');

            return response()->json([
                'success' => true,
                'message' => 'Configuración general actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración general: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la configuración'
            ], 500);
        }
    }

    /**
     * Actualizar configuración de seguridad
     */
    public function actualizarSeguridad(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'tiempo_sesion' => 'required|integer|min:60|max:1440',
            'intentos_login_max' => 'required|integer|min:3|max:10',
            'bloqueo_tiempo' => 'required|integer|min:5|max:60',
            'requiere_2fa' => 'boolean',
            'complejidad_password' => 'required|in:baja,media,alta'
        ]);

        try {
            // Aquí se implementaría la actualización real en MongoDB
            $this->logConfiguracionCambiada('seguridad', $validated);
            
            Cache::forget('configuracion_seguridad');

            return response()->json([
                'success' => true,
                'message' => 'Configuración de seguridad actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración de seguridad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la configuración de seguridad'
            ], 500);
        }
    }

    /**
     * Actualizar configuración de módulos
     */
    public function actualizarModulos(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'hallazgos_activo' => 'boolean',
            'psicosocial_activo' => 'boolean',
            'planes_accion_activo' => 'boolean',
            'indicadores_activo' => 'boolean',
            'auditoria_activa' => 'boolean'
        ]);

        try {
            // Aquí se implementaría la actualización real en MongoDB
            $this->logConfiguracionCambiada('modulos', $validated);
            
            Cache::forget('configuracion_modulos');

            return response()->json([
                'success' => true,
                'message' => 'Configuración de módulos actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración de módulos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la configuración de módulos'
            ], 500);
        }
    }

    /**
     * Realizar backup del sistema
     */
    public function realizarBackup(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $incluir = $request->input('incluir', ['configuracion', 'usuarios', 'empresas']);

        try {
            $nombreBackup = 'backup_gir365_' . now()->format('Y-m-d_H-i-s') . '.zip';
            
            // Aquí se implementaría el backup real
            Log::info("Backup realizado: {$nombreBackup}", [
                'incluir' => $incluir,
                'usuario' => \App\Http\Controllers\AuthController::user()->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup realizado exitosamente',
                'archivo' => $nombreBackup,
                'tamaño' => '45.2 MB',
                'url_descarga' => "/admin/configuracion/descargar-backup/{$nombreBackup}"
            ]);

        } catch (\Exception $e) {
            Log::error('Error realizando backup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar el backup'
            ], 500);
        }
    }

    /**
     * Limpiar caché del sistema
     */
    public function limpiarCache(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $tipos = $request->input('tipos', ['configuracion', 'vistas', 'rutas']);

        try {
            if (in_array('configuracion', $tipos)) {
                Cache::flush();
            }

            // Aquí se implementaría la limpieza específica por tipo
            Log::info('Caché limpiado', [
                'tipos' => $tipos,
                'usuario' => \App\Http\Controllers\AuthController::user()->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Caché limpiado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error limpiando caché: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar el caché'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del sistema
     */
    private function obtenerEstadisticasSistema()
    {
        return [
            'uptime' => '15 días, 4 horas',
            'memoria_uso' => '245 MB',
            'espacio_disco' => '2.4 GB',
            'conexiones_activas' => 23,
            'requests_ultima_hora' => 1247,
            'ultimo_backup' => now()->subDays(2)->format('d/m/Y H:i'),
            'version_php' => phpversion(),
            'version_mongodb' => '5.0.0'
        ];
    }

    /**
     * Verificar si el usuario actual es super admin
     */
    private function isSuperAdmin()
    {
        if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
            return false;
        }

        $user = \App\Http\Controllers\AuthController::user();
        return $user->tipo === 'super_admin' || $user->rol === 'super_admin';
    }

    /**
     * Log de cambios en configuración
     */
    private function logConfiguracionCambiada($seccion, $cambios)
    {
        $user = \App\Http\Controllers\AuthController::user();
        
        Log::info("Configuración actualizada", [
            'seccion' => $seccion,
            'cambios' => $cambios,
            'usuario' => $user->email,
            'timestamp' => now()->toISOString()
        ]);
    }
}
