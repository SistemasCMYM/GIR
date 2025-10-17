<?php

namespace App\Models\Auth;

use App\Models\BaseMongoModel;

/**
 * Modelo Permiso compatible con schema Node.js PermisoSchema
 * Base de datos: cmym, Colección: permisos
 * Enlazado a cuentas por cuenta_id
 */
class Permiso extends BaseMongoModel
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_cmym';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'permisos';

    /**
     * Tipos permitidos según schema Node.js
     */
    public static $tipos = [
        'interna',
        'cliente', 
        'crm-cliente',
        'profesional',
        'usuario'
    ];

    /**
     * Módulos disponibles en el sistema
     */
    public static $modulos = [
        'dashboard',
        'administracion',
        'hallazgos',
        'psicosocial',
        'configuracion',
        'informes',
        'empleados',
        'usuarios',
        'empresas'
    ];

    /**
     * Acciones disponibles en el sistema
     */
    public static $acciones = [
        'create',
        'read', 
        'update',
        'delete',
        'export',
        'import',
        'admin',
        'view',
        'edit',
        'manage'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'cuenta_id',
        'modulo',
        'tipo',
        'acciones',
        'link'
    ];

    /**
     * The attributes that should be cast.
     */
    // Avoid using Laravel's Json cast here because the MongoDB driver
    // may already return arrays. Casting to 'array' triggers
    // Illuminate\Database\Eloquent\Casts\Json which calls json_decode
    // and can receive an array causing a TypeError. We implement
    // safe accessors below instead.
    protected $casts = [
        // keep lightweight casts only when safe; leave arrays to accessors
    ];

    /**
     * Valores por defecto según schema Node.js
     */
    protected $attributes = [
        'cuenta_id' => null,
        'modulo' => null,
        'tipo' => null,
        'acciones' => [],
        'link' => null
    ];

    /**
     * Relación con cuenta
     */
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }

    /**
     * Virtual para $link compatible con Node.js
     */
    public function getLinkAttribute()
    {
        return "/v2.0/permisos/{$this->id}";
    }

    /**
     * Scope para filtrar por cuenta
     */
    public function scopePorCuenta($query, $cuentaId)
    {
        return $query->where('cuenta_id', $cuentaId);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por módulo específico
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para filtrar por acción específica
     */
    public function scopeConAccion($query, $accion)
    {
        return $query->where('acciones', $accion);
    }

    /**
     * Verificar si tiene acceso a un módulo específico
     */
    public function tieneAccesoModulo($modulo)
    {
        $modulos = $this->modulo ?? [];
        return in_array($modulo, $modulos);
    }

    /**
     * Verificar si puede realizar una acción específica
     */
    public function puedeRealizarAccion($accion)
    {
        $acciones = $this->acciones ?? [];
        return in_array($accion, $acciones);
    }

    /**
     * Verificar si es de tipo específico
     */
    public function esTipo($tipo)
    {
        return $this->tipo === $tipo;
    }

    /**
     * Obtener todos los módulos asignados
     */
    public function getModulosAsignados()
    {
        return $this->modulo ?? [];
    }

    /**
     * Obtener todas las acciones permitidas
     */
    public function getAccionesPermitidas()
    {
        return $this->acciones ?? [];
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permiso) {
            // Generar ID único si no se proporciona
            if (empty($permiso->id)) {
                $permiso->id = generateBase64UrlId(16);
            }

            // Validar tipo
            if ($permiso->tipo && !in_array($permiso->tipo, self::$tipos)) {
                throw new \InvalidArgumentException("Tipo '{$permiso->tipo}' no válido. Tipos permitidos: " . implode(', ', self::$tipos));
            }

            // Validar módulos si se proporcionan
            if ($permiso->modulo && is_array($permiso->modulo)) {
                $modulosInvalidos = array_diff($permiso->modulo, self::$modulos);
                if (!empty($modulosInvalidos)) {
                    throw new \InvalidArgumentException("Módulos inválidos: " . implode(', ', $modulosInvalidos));
                }
            }

            // Validar acciones si se proporcionan
            if ($permiso->acciones && is_array($permiso->acciones)) {
                $accionesInvalidas = array_diff($permiso->acciones, self::$acciones);
                if (!empty($accionesInvalidas)) {
                    throw new \InvalidArgumentException("Acciones inválidas: " . implode(', ', $accionesInvalidas));
                }
            }

            // Asegurar que modulo y acciones sean arrays
            $permiso->modulo = is_array($permiso->modulo) ? $permiso->modulo : [];
            $permiso->acciones = is_array($permiso->acciones) ? $permiso->acciones : [];
        });
    }

    /**
     * Convertir a array compatible con Node.js
     */
    public function toNodejsArray()
    {
        return [
            'id' => $this->id,
            'cuenta_id' => $this->cuenta_id,
            'modulo' => $this->modulo ?? [],
            'tipo' => $this->tipo,
            'acciones' => $this->acciones ?? [],
            'link' => $this->link,
            '$link' => $this->link
        ];
    }

    /**
     * Safe accessor for acciones: tolerate arrays or JSON strings.
     */
    public function getAccionesAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Safe accessor for modulo: tolerate arrays or JSON strings.
     */
    public function getModuloAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
