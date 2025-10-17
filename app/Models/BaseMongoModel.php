<?php

namespace App\Models;

use App\Traits\HasCustomId;
use MongoDB\Laravel\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo base para MongoDB con generación automática de IDs personalizados
 * 
 * Todos los modelos MongoDB del proyecto GIR-365 deben extender de esta clase
 * para obtener funcionalidad de IDs personalizados automáticamente.
 */
abstract class BaseMongoModel extends Eloquent
{
    use HasFactory, HasCustomId;

    /**
     * Conexión de base de datos MongoDB
     * 
     * Este valor será sobrescrito por los modelos hijos según su necesidad
     * 
     * @var string
     */
    // protected $connection = 'mongodb'; // Removed to allow child models to define their own connection

    /**
     * Nombre de la clave primaria
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Tipo de la clave primaria
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indica si el modelo debe auto-incrementar la clave primaria
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * Timestamps habilitados por defecto
     * 
     * @var bool
     */
    public $timestamps = true;

    /**
     * Formato de fecha personalizado para MongoDB
     * 
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * Campos que deben ser convertidos a fechas
     * 
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Campos que deben ser ocultados en arrays
     * 
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     * 
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
        'eliminado' => 'boolean',
    ];

    /**
     * Scope para obtener solo registros activos
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener solo registros no eliminados
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoEliminados($query)
    {
        return $query->where('eliminado', '!=', true);
    }

    /**
     * Obtener información del modelo formateada
     * 
     * @return array
     */
    public function getModelInfo()
    {
        return [
            'model' => class_basename($this),
            'id' => $this->getKey(),
            'table' => $this->getTable(),
            'connection' => $this->getConnectionName(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Convertir el modelo a array con información adicional
     * 
     * @return array
     */
    public function toArrayWithMeta()
    {
        $array = $this->toArray();
        $array['_meta'] = $this->getModelInfo();
        return $array;
    }

    /**
     * Boot del modelo base
     */
    protected static function boot()
    {
        parent::boot();

        // Configurar valores por defecto al crear
        static::creating(function ($model) {
            if (!isset($model->activo)) {
                $model->activo = true;
            }
            if (!isset($model->eliminado)) {
                $model->eliminado = false;
            }
        });        // Log de cambios importantes
        static::created(function ($model) {
            \Illuminate\Support\Facades\Log::info('Documento creado: ' . class_basename($model) . ' ID: ' . $model->getKey());
        });

        static::updated(function ($model) {
            \Illuminate\Support\Facades\Log::info('Documento actualizado: ' . class_basename($model) . ' ID: ' . $model->getKey());
        });
    }
}
