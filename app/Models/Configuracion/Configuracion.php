<?php

namespace App\Models\Configuracion;

use MongoDB\Laravel\Eloquent\Model;
use App\Traits\HasEmpresaScope;
use App\Traits\GeneratesUniqueId;
use App\Traits\SafeJson;

class Configuracion extends Model
{
    use HasEmpresaScope, GeneratesUniqueId;
    use SafeJson;

    // Cambio de mongodb_admin a mongodb_cmym (base de datos de administración)
    protected $connection = 'mongodb_cmym';
    protected $collection = 'configuraciones';
    protected $idPrefix = 'CFG';

    protected $fillable = [
        'empresa_id',
        'modulo',
        'componente',
        'subcomponente',
        'clave',
        'valor',
        'tipo_dato',
        'descripcion',
        'categoria',
        'grupo',
        'orden',
        'activo',
        'aplicar_a_modulos',
        'validaciones',
        'valores_permitidos',
        'configuracion_padre_id',
        'metadatos',
        'fecha_aplicacion',
        'usuario_modificacion'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_aplicacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Safe accessors for fields that may arrive as arrays or JSON strings
    public function getValorAttribute($value)
    {
        return $this->safeJsonDecode($value, $value);
    }

    public function getValidacionesAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    public function getValoresPermitidosAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    public function getAplicarAModulosAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    public function getMetadatosAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    /**
     * Configuraciones para el módulo de Empresa
     */
    public static function getEmpresaConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'empresa')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para el módulo de Estructura Organizacional
     */
    public static function getEstructuraConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'estructura')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Fecha y Hora
     */
    public static function getFechaHoraConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'fechahora')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Reportes
     */
    public static function getReportesConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'reportes')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Seguridad
     */
    public static function getSeguridadConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'seguridad')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Notificaciones
     */
    public static function getNotificacionesConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'notificaciones')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Integraciones
     */
    public static function getIntegracionesConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'integraciones')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Autenticación
     */
    public static function getAutenticacionConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'autenticacion')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones para Procesos
     */
    public static function getProcesosConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('modulo', 'procesos')
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones específicas para módulo de Hallazgos
     */
    public static function getHallazgosConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->whereIn('aplicar_a_modulos', [['hallazgos'], 'all'])
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Configuraciones específicas para módulo Psicosocial
     */
    public static function getPsicosocialConfig($empresaId)
    {
        return self::where('empresa_id', $empresaId)
                   ->whereIn('aplicar_a_modulos', [['psicosocial'], 'all'])
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }

    /**
     * Obtener valor de configuración específica
     */
    public static function getValue($empresaId, $clave, $default = null)
    {
        $config = self::where('empresa_id', $empresaId)
                     ->where('clave', $clave)
                     ->where('activo', true)
                     ->first();

        return $config ? $config->valor : $default;
    }

    /**
     * Establecer valor de configuración
     */
    public static function setValue($empresaId, $clave, $valor, $modulo = null, $descripcion = null)
    {
        return self::updateOrCreate(
            [
                'empresa_id' => $empresaId,
                'clave' => $clave
            ],
            [
                'valor' => $valor,
                'modulo' => $modulo,
                'descripcion' => $descripcion,
                'activo' => true,
                'usuario_modificacion' => auth()->check() ? auth()->id() : null
            ]
        );
    }

    /**
     * Configuraciones por componente
     */
    public static function getByComponente($empresaId, $componente)
    {
        return self::where('empresa_id', $empresaId)
                   ->where('componente', $componente)
                   ->where('activo', true)
                   ->orderBy('orden')
                   ->get();
    }

    /**
     * Configuraciones aplicables a módulos específicos
     */
    public static function getForModule($empresaId, $modulo)
    {
        return self::where('empresa_id', $empresaId)
                   ->where(function($query) use ($modulo) {
                       $query->where('aplicar_a_modulos', 'like', "%{$modulo}%")
                             ->orWhere('aplicar_a_modulos', 'all');
                   })
                   ->where('activo', true)
                   ->get()
                   ->keyBy('clave');
    }
}
