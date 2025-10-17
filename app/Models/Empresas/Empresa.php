<?php

namespace App\Models\Empresas;

use App\Models\BaseMongoModel;
use App\Traits\GeneratesUniqueId;
use App\Traits\SafeJson;

class Empresa extends BaseMongoModel
{
    use GeneratesUniqueId;
    use SafeJson;

    protected $connection = 'mongodb_empresas';
    protected $collection = 'empresas';

    protected $fillable = [
        'nit',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'email',
        'representante_legal',
        'estado',
        'fecha_registro',
        'ciudad_id',
        'departamento_id',
        'sector_id',
        'centro_id',
        'numero_empleados',
        'configuracion',
        'datos_adicionales'
    ];

    protected $dates = [
        'fecha_registro',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'configuracion' => 'array',
        'datos_adicionales' => 'array',
        'estado' => 'boolean',
        'numero_empleados' => 'integer'
    ];

    public function getConfiguracionAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    public function getDatosAdicionalesAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    /**
     * Find empresa by NIT
     */
    public static function findByNit($nit)
    {
        return static::where('nit', $nit)->first();
    }

    /**
     * Get all active empresas
     */
    public static function getActive()
    {
        return static::where('estado', true)->get();
    }

    /**
     * Get empleados of this empresa
     */
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'empresa_id', 'id');
    }

    /**
     * Get cuentas of this empresa
     */
    public function cuentas()
    {
        return $this->hasMany(Cuenta::class, 'empresa_id', 'id');
    }

    /**
     * Get ciudad
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'id');
    }

    /**
     * Get departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
    }

    /**
     * Get sector
     */
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'id');
    }

    /**
     * Get centro
     */
    public function centro()
    {
        return $this->belongsTo(Centro::class, 'centro_id', 'id');
    }

    /**
     * Check if empresa has active empleados
     */
    public function hasActiveEmpleados()
    {
        return $this->empleados()->where('estado', true)->exists();
    }

    /**
     * Get empresa statistics
     */
    public function getStatistics()
    {
        return [
            'total_empleados' => $this->empleados()->count(),
            'empleados_activos' => $this->empleados()->where('estado', true)->count(),
            'total_cuentas' => $this->cuentas()->count(),
            'cuentas_activas' => $this->cuentas()->where('estado', true)->count()
        ];
    }

    /**
     * Get formatted NIT
     */
    public function getFormattedNitAttribute()
    {
        $nit = $this->nit;
        if (strlen($nit) > 1) {
            return substr($nit, 0, -1) . '-' . substr($nit, -1);
        }
        return $nit;
    }

    /**
     * Get display name (comercial or razon social)
     */
    public function getDisplayNameAttribute()
    {
        return $this->nombre_comercial ?: $this->razon_social;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->generateUniqueId();
            if (!$model->fecha_registro) {
                $model->fecha_registro = now();
            }
            if (!isset($model->estado)) {
                $model->estado = true;
            }
        });
    }
}
