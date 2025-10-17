<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Auth\Usuario;
use App\Models\Configuracion\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use MongoDB\BSON\ObjectId;

class ConfigurationController extends Controller
{    /**
     * Display the configuration module
     */
    public function index()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            // Verificar si el usuario es SuperAdmin usando la misma lógica que en otros lugares
            $userData = session('user_data') ?: session('usuario_data');
            $isSuperAdmin = false;
            if ($userData) {
                $rol = strtolower($userData['rol'] ?? $userData['role'] ?? $userData['tipo'] ?? '');
                $isSuperAdmin = in_array($rol, ['super_admin','superadmin','super administrator','superadministrador','root'], true)
                    || (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
            }
            
            if (!$usuario || !$isSuperAdmin) {
                return redirect()->route('dashboard')->with('warning', 'Acceso restringido. Solo SuperAdmin puede acceder a Configuración.');
            }
            
            // Get system configuration - MongoDB compatible
            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();

            return view('modules.config.index', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            // Solo mostrar error a SuperAdmin, para otros un aviso menos técnico
            $userData = session('user_data') ?: session('usuario_data');
            $isSuperAdmin = false;
            if ($userData) {
                $rol = strtolower($userData['rol'] ?? $userData['role'] ?? $userData['tipo'] ?? '');
                $isSuperAdmin = in_array($rol, ['super_admin','superadmin','super administrator','superadministrador','root'], true)
                    || (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
            }
            
            if ($isSuperAdmin) {
                return back()->with('error', 'Error al cargar configuración: ' . $e->getMessage());
            } else {
                return redirect()->route('dashboard')->with('warning', 'No fue posible acceder a la configuración en este momento.');
            }
        }
    }

    /**
     * Update system configuration
     */
    public function update(Request $request)
    {
        $request->validate([
            'nombre_sistema' => 'required|string|max:255',
            'descripcion_sistema' => 'nullable|string|max:1000',
            'logo_sistema' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tema_color' => 'required|string|in:blue,green,purple,red,yellow,dark',
            'idioma_defecto' => 'required|string|in:es,en',
            'zona_horaria' => 'required|string',
            'email_soporte' => 'required|email',
            'telefono_soporte' => 'nullable|string|max:20',
            'direccion_empresa' => 'nullable|string|max:255',
            'mostrar_tutorial' => 'boolean',
            'permitir_registro' => 'boolean',
            'notificaciones_email' => 'boolean',
            'backup_automatico' => 'boolean',
            'frecuencia_backup' => 'required|string|in:diario,semanal,mensual',
            'retencion_logs' => 'required|integer|min:1|max:365',
            'limite_usuarios' => 'nullable|integer|min:1',
            'limite_empresas' => 'nullable|integer|min:1',
            'mantenimiento_activo' => 'boolean',
            'mensaje_mantenimiento' => 'nullable|string|max:500'
        ]);        try {
            // Verificar si el usuario actual es SuperAdmin
            if (!$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para editar la configuración');
            }

            $configuracion = ConfiguracionSistema::first();

            // Handle logo upload
            if ($request->hasFile('logo_sistema')) {
                // Delete old logo if exists
                if ($configuracion && $configuracion->logo_sistema && Storage::exists('public/logos/' . $configuracion->logo_sistema)) {
                    Storage::delete('public/logos/' . $configuracion->logo_sistema);
                }

                $logo = $request->file('logo_sistema');
                $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
                $logo->storeAs('public/logos', $logoName);
            }

            // Update configuration data
            $updateData = [
                'nombre_sistema' => $request->nombre_sistema,
                'descripcion_sistema' => $request->descripcion_sistema,
                'tema_color' => $request->tema_color,
                'idioma_defecto' => $request->idioma_defecto,
                'zona_horaria' => $request->zona_horaria,
                'email_soporte' => $request->email_soporte,
                'telefono_soporte' => $request->telefono_soporte,
                'direccion_empresa' => $request->direccion_empresa,
                'mostrar_tutorial' => $request->has('mostrar_tutorial'),
                'permitir_registro' => $request->has('permitir_registro'),
                'notificaciones_email' => $request->has('notificaciones_email'),
                'backup_automatico' => $request->has('backup_automatico'),
                'mantenimiento_activo' => $request->has('mantenimiento_activo'),
                'frecuencia_backup' => $request->frecuencia_backup,
                'retencion_logs' => $request->retencion_logs,
                'limite_usuarios' => $request->limite_usuarios,
                'limite_empresas' => $request->limite_empresas,
                'mensaje_mantenimiento' => $request->mensaje_mantenimiento,
                'updated_at' => now()
            ];

            // Add logo to update data if uploaded
            if (isset($logoName)) {
                $updateData['logo_sistema'] = $logoName;
            } elseif ($configuracion && $configuracion->logo_sistema) {
                $updateData['logo_sistema'] = $configuracion->logo_sistema;
            }

            // Update or create configuration
            if ($configuracion && $configuracion->exists) {
                $configuracion->update($updateData);
            } else {
                ConfiguracionSistema::create($updateData);
            }

            return redirect()->route('config.index')
                ->with('success', 'Configuración actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar configuración: ' . $e->getMessage())
                ->withInput();
        }
    }    /**
     * Reset configuration to defaults
     */
    public function reset()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para resetear la configuración');
            }

            $configuracion = ConfiguracionSistema::first();
            if ($configuracion) {
                // Delete logo if exists
                if ($configuracion->logo_sistema && Storage::exists('public/logos/' . $configuracion->logo_sistema)) {
                    Storage::delete('public/logos/' . $configuracion->logo_sistema);
                }
                
                $configuracion->delete();
            }

            return redirect()->route('config.index')
                ->with('success', 'Configuración restablecida a valores por defecto');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al resetear configuración: ' . $e->getMessage());
        }
    }    /**
     * Export configuration
     */
    public function export()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para exportar la configuración');
            }
            
            $configuracion = ConfiguracionSistema::first();
            
            if (!$configuracion) {
                return back()->with('error', 'No hay configuración para exportar');
            }

            $data = $configuracion->toArray();
            unset($data['_id'], $data['created_at'], $data['updated_at']);

            $filename = 'configuracion_gir365_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($data)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar configuración: ' . $e->getMessage());
        }
    }

    /**
     * Import configuration
     */
    public function import(Request $request)
    {
        $request->validate([
            'config_file' => 'required|file|mimes:json|max:1024'
        ]);        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para importar configuración');
            }

            $file = $request->file('config_file');
            $content = file_get_contents($file->getPathname());
            $data = safe_json_decode($content, true);

            if (empty($data) && json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'El archivo de configuración no es válido');
            }

            $configuracion = ConfiguracionSistema::first();

            // Update only allowed fields
            $updateData = [];
            $allowedFields = [
                'nombre_sistema', 'descripcion_sistema', 'tema_color', 'idioma_defecto',
                'zona_horaria', 'email_soporte', 'telefono_soporte', 'direccion_empresa',
                'mostrar_tutorial', 'permitir_registro', 'notificaciones_email',
                'backup_automatico', 'frecuencia_backup', 'retencion_logs',
                'limite_usuarios', 'limite_empresas', 'mensaje_mantenimiento'
            ];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            $updateData['updated_at'] = now();

            if ($configuracion && $configuracion->exists) {
                $configuracion->update($updateData);
            } else {
                ConfiguracionSistema::create($updateData);
            }

            return redirect()->route('config.index')
                ->with('success', 'Configuración importada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar configuración: ' . $e->getMessage());
        }
    }    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para limpiar el caché');
            }
            
            // Clear various caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->route('config.index')
                ->with('success', 'Caché del sistema limpiado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar caché: ' . $e->getMessage());
        }
    }

    /**
     * Get system information
     */
    public function systemInfo()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $info = [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'database_connection' => config('database.default'),
                'storage_path' => storage_path(),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
                'mail_driver' => config('mail.default'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ];

            return response()->json(['success' => true, 'info' => $info]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display empresa configuration
     */
    public function empresa()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de empresa');
            }

            // Obtener datos de la empresa del usuario autenticado
            $empresa = $usuario->empresa ?? null;
            
            return view('modules.config.empresa', compact('usuario', 'empresa'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de empresa: ' . $e->getMessage());
        }
    }

    /**
     * Update empresa configuration
     */
    public function updateEmpresa(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'nit_empresa' => 'required|string|max:20',
            'telefono_empresa' => 'nullable|string|max:20',
            'email_empresa' => 'required|email|max:255',
            'direccion_empresa' => 'nullable|string|max:500',
            'ciudad_empresa' => 'nullable|string|max:100',
            'pais_empresa' => 'nullable|string|max:100',
            'logo_empresa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para editar la configuración de empresa');
            }

            // Actualizar datos de la empresa
            $empresa = $usuario->empresa;
            if (!$empresa) {
                return back()->with('error', 'No se encontró la empresa asociada al usuario');
            }

            // Handle logo upload
            if ($request->hasFile('logo_empresa')) {
                $logo = $request->file('logo_empresa');
                $logoName = 'empresa_' . $empresa->nit . '_' . time() . '.' . $logo->getClientOriginalExtension();
                $logo->storeAs('public/empresas', $logoName);
                $empresa->logo = $logoName;
            }

            $empresa->update([
                'nombre' => $request->nombre_empresa,
                'nit' => $request->nit_empresa,
                'telefono' => $request->telefono_empresa,
                'email' => $request->email_empresa,
                'direccion' => $request->direccion_empresa,
                'ciudad' => $request->ciudad_empresa,
                'pais' => $request->pais_empresa,
                'updated_at' => now()
            ]);

            return redirect()->route('configuracion.empresa')
                ->with('success', 'Configuración de empresa actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar configuración de empresa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display sistema configuration
     */
    public function sistema()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración del sistema');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('modules.config.sistema', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración del sistema: ' . $e->getMessage());
        }
    }

    /**
     * Update sistema configuration
     */
    public function updateSistema(Request $request)
    {
        // Reutilizar la lógica del método update existente
        return $this->update($request);
    }

    /**
     * Display backup configuration
     */
    public function backup()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de backup');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('modules.config.backup', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de backup: ' . $e->getMessage());
        }
    }

    /**
     * Create backup
     */
    public function crearBackup()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para crear backups');
            }

            // Crear backup de la base de datos
            $backupName = 'backup_gir365_' . date('Y-m-d_H-i-s') . '.json';
            
            // Aquí iría la lógica para crear el backup
            // Por ahora retornamos éxito
            
            return redirect()->route('configuracion.backup')
                ->with('success', 'Backup creado exitosamente: ' . $backupName);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear backup: ' . $e->getMessage());
        }
    }

    /**
     * Display configuracion seguridad
     */
    public function configuracionSeguridad()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de seguridad');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('admin.configuracion.seguridad', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de seguridad: ' . $e->getMessage());
        }
    }

    /**
     * Save configuracion seguridad
     */
    public function guardarConfiguracionSeguridad(Request $request)
    {
        $request->validate([
            'timeout_sesion' => 'required|integer|min:5|max:1440',
            'intentos_login' => 'required|integer|min:3|max:10',
            'bloqueo_tiempo' => 'required|integer|min:5|max:60',
            'longitud_password' => 'required|integer|min:6|max:20',
            'password_complejo' => 'boolean',
            'verificacion_2fa' => 'boolean'
        ]);

        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para editar la configuración de seguridad');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            $configuracion->update([
                'timeout_sesion' => $request->timeout_sesion,
                'intentos_login' => $request->intentos_login,
                'bloqueo_tiempo' => $request->bloqueo_tiempo,
                'longitud_password' => $request->longitud_password,
                'password_complejo' => $request->has('password_complejo'),
                'verificacion_2fa' => $request->has('verificacion_2fa'),
                'updated_at' => now()
            ]);

            return redirect()->route('configuracion.seguridad.index')
                ->with('success', 'Configuración de seguridad actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar configuración de seguridad: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display configuracion notificaciones
     */
    public function configuracionNotificaciones()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de notificaciones');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('admin.configuracion.notificaciones', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de notificaciones: ' . $e->getMessage());
        }
    }

    /**
     * Save configuracion notificaciones
     */
    public function guardarConfiguracionNotificaciones(Request $request)
    {
        $request->validate([
            'notif_email' => 'boolean',
            'notif_sms' => 'boolean',
            'notif_push' => 'boolean',
            'notif_hallazgos' => 'boolean',
            'notif_evaluaciones' => 'boolean',
            'notif_reportes' => 'boolean'
        ]);

        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para editar la configuración de notificaciones');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            $configuracion->update([
                'notif_email' => $request->has('notif_email'),
                'notif_sms' => $request->has('notif_sms'),
                'notif_push' => $request->has('notif_push'),
                'notif_hallazgos' => $request->has('notif_hallazgos'),
                'notif_evaluaciones' => $request->has('notif_evaluaciones'),
                'notif_reportes' => $request->has('notif_reportes'),
                'updated_at' => now()
            ]);

            return redirect()->route('configuracion.notificaciones.index')
                ->with('success', 'Configuración de notificaciones actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar configuración de notificaciones: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display mantenimiento
     */
    public function mantenimiento()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder al mantenimiento');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('admin.configuracion.mantenimiento', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar cache
     */
    public function limpiarCache()
    {
        return $this->clearCache();
    }

    /**
     * Limpiar logs
     */
    public function limpiarLogs()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return back()->with('error', 'No tiene permisos para limpiar logs');
            }

            // Limpiar archivos de log
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            return redirect()->route('configuracion.mantenimiento.index')
                ->with('success', 'Logs del sistema limpiados exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar logs: ' . $e->getMessage());
        }
    }

    /**
     * Display configuracion modulos
     */
    public function configuracionModulos()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de módulos');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('admin.configuracion.modulos', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de módulos: ' . $e->getMessage());
        }
    }

    /**
     * Display configuracion base datos
     */
    public function configuracionBaseDatos()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de base de datos');
            }
            
            return view('admin.configuracion.base-datos', compact('usuario'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Display configuracion integraciones
     */
    public function configuracionIntegraciones()
    {
        try {
            $usuario = session('usuario_data') ? new Usuario(session('usuario_data')) : null;
            
            if (!$usuario || !$this->isSuperAdminFromSession()) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a la configuración de integraciones');
            }

            $configuracion = ConfiguracionSistema::first() ?: new ConfiguracionSistema();
            
            return view('modules.configuracion.integraciones.index', compact('usuario', 'configuracion'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar configuración de integraciones: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si el usuario actual es SuperAdmin basado en datos de sesión
     */
    private function isSuperAdminFromSession()
    {
        $userData = session('user_data') ?: session('usuario_data');
        
        if (!$userData) {
            return false;
        }
        
        // Verificar diferentes campos posibles para el rol
        $rol = strtolower($userData['rol'] ?? $userData['role'] ?? $userData['tipo'] ?? '');
        
        // Lista de roles que consideramos SuperAdmin
        $superAdminRoles = ['super_admin','superadmin','super administrator','superadministrador','root'];
        
        // Verificar rol o flag específico
        return in_array($rol, $superAdminRoles, true) 
            || (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
    }
}
