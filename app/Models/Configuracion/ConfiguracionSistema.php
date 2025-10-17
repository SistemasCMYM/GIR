<?php

namespace App\Models\Configuracion;

use MongoDB\Laravel\Eloquent\Model as MongoModel;

class ConfiguracionSistema extends MongoModel
{
    // Cambio de mongodb_admin a mongodb_cmym (base de datos de administración)
    protected $connection = 'mongodb_cmym';
    protected $collection = 'configuracion_sistema';

    protected $fillable = [
        'nombre_sistema',
        'descripcion_sistema',
        'logo_sistema',
        'tema_color',
        'idioma_defecto',
        'zona_horaria',
        'email_soporte',
        'telefono_soporte',
        'direccion_empresa',
        'mostrar_tutorial',
        'permitir_registro',
        'notificaciones_email',
        'backup_automatico',
        'frecuencia_backup',
        'retencion_logs',
        'limite_usuarios',
        'limite_empresas',
        'mantenimiento_activo',
        'mensaje_mantenimiento'
    ];

    protected $casts = [
        'mostrar_tutorial' => 'boolean',
        'permitir_registro' => 'boolean',
        'notificaciones_email' => 'boolean',
        'backup_automatico' => 'boolean',
        'mantenimiento_activo' => 'boolean',
        'retencion_logs' => 'integer',
        'limite_usuarios' => 'integer',
        'limite_empresas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'nombre_sistema' => 'GIR-365',
        'descripcion_sistema' => 'Sistema de Gestión Integral de Riesgos',
        'tema_color' => 'blue',
        'idioma_defecto' => 'es',
        'zona_horaria' => 'America/Bogota',
        'email_soporte' => env('SUPPORT_EMAIL', 'soporte@tudominio.com'),
        'mostrar_tutorial' => true,
        'permitir_registro' => false,
        'notificaciones_email' => true,
        'backup_automatico' => true,
        'frecuencia_backup' => 'diario',
        'retencion_logs' => 30,
        'mantenimiento_activo' => false
    ];

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_sistema) {
            return asset('storage/logos/' . $this->logo_sistema);
        }
        return asset('dist/img/AdminLTELogo.png');
    }

    /**
     * Get available themes
     */
    public static function getAvailableThemes()
    {
        return [
            'blue' => 'Azul',
            'green' => 'Verde',
            'purple' => 'Morado',
            'red' => 'Rojo',
            'yellow' => 'Amarillo',
            'dark' => 'Oscuro'
        ];
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages()
    {
        return [
            'es' => 'Español',
            'en' => 'English'
        ];
    }

    /**
     * Get available timezones
     */
    public static function getAvailableTimezones()
    {
        return [
            'America/Bogota' => 'Bogotá (UTC-5)',
            'America/New_York' => 'New York (UTC-5/-4)',
            'America/Mexico_City' => 'Ciudad de México (UTC-6/-5)',
            'America/Los_Angeles' => 'Los Angeles (UTC-8/-7)',
            'Europe/Madrid' => 'Madrid (UTC+1/+2)',
            'Europe/London' => 'Londres (UTC+0/+1)',
            'Asia/Tokyo' => 'Tokio (UTC+9)',
            'Australia/Sydney' => 'Sydney (UTC+10/+11)'
        ];
    }

    /**
     * Get backup frequencies
     */
    public static function getBackupFrequencies()
    {
        return [
            'diario' => 'Diario',
            'semanal' => 'Semanal',
            'mensual' => 'Mensual'
        ];
    }

    /**
     * Check if system is in maintenance mode
     */
    public function isInMaintenance()
    {
        return $this->mantenimiento_activo ?? false;
    }

    /**
     * Get maintenance message
     */
    public function getMaintenanceMessage()
    {
        return $this->mensaje_mantenimiento ?? 'El sistema está en mantenimiento. Intente más tarde.';
    }

    /**
     * Check if user registration is allowed
     */
    public function allowsRegistration()
    {
        return $this->permitir_registro ?? false;
    }

    /**
     * Check if tutorial should be shown
     */
    public function showsTutorial()
    {
        return $this->mostrar_tutorial ?? true;
    }

    /**
     * Check if email notifications are enabled
     */
    public function hasEmailNotifications()
    {
        return $this->notificaciones_email ?? true;
    }

    /**
     * Check if automatic backup is enabled
     */
    public function hasAutomaticBackup()
    {
        return $this->backup_automatico ?? true;
    }

    /**
     * Get user limit
     */
    public function getUserLimit()
    {
        return $this->limite_usuarios;
    }

    /**
     * Get company limit
     */
    public function getCompanyLimit()
    {
        return $this->limite_empresas;
    }

    /**
     * Get log retention days
     */
    public function getLogRetentionDays()
    {
        return $this->retencion_logs ?? 30;
    }

    /**
     * Get system name
     */
    public function getSystemName()
    {
        return $this->nombre_sistema ?? 'GIR-365';
    }

    /**
     * Get system description
     */
    public function getSystemDescription()
    {
        return $this->descripcion_sistema ?? 'Sistema de Gestión Integral de Riesgos';
    }

    /**
     * Get support email
     */
    public function getSupportEmail()
    {
        return $this->email_soporte ?? env('SUPPORT_EMAIL', 'soporte@tudominio.com');
    }

    /**
     * Get support phone
     */
    public function getSupportPhone()
    {
        return $this->telefono_soporte;
    }

    /**
     * Get company address
     */
    public function getCompanyAddress()
    {
        return $this->direccion_empresa;
    }

    /**
     * Get theme color
     */
    public function getThemeColor()
    {
        return $this->tema_color ?? 'blue';
    }

    /**
     * Get default language
     */
    public function getDefaultLanguage()
    {
        return $this->idioma_defecto ?? 'es';
    }

    /**
     * Get timezone
     */
    public function getTimezone()
    {
        return $this->zona_horaria ?? 'America/Bogota';
    }

    /**
     * Get backup frequency
     */
    public function getBackupFrequency()
    {
        return $this->frecuencia_backup ?? 'diario';
    }
}
