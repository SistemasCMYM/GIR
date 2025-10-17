<?php

namespace App\Models\Empresas;

use App\Models\BaseMongoModel;
use App\Traits\HasEmpresaScope;
use App\Models\Empresa;

/**
 * Modelo Area con generación automática de IDs personalizados
 * 
 * Ejemplo de implementación del sistema de IDs base64url para el proyecto GIR-365
 */
class Area extends BaseMongoModel
{
    use HasEmpresaScope;
    /**
     * Prefijo personalizado para IDs de área
     * 
     * @var string
     */
    protected $idPrefix = 'AREA';

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_empresas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'areas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'responsable_id',
        'estado'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'estado' => 'boolean'
        ];
    }

    /**
     * Get the empresa that the area belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Get the responsable of this area.
     */
    public function responsable()
    {
        return $this->belongsTo(Empleado::class, 'responsable_id');
    }

    /**
     * Get the centros in this area.
     */
    public function centros()
    {
        return $this->hasMany(Centro::class, 'area_id');
    }

    /**
     * Get the empleados in this area.
     */
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'area_id');
    }

    /**
     * Scope a query to only include active areas.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
