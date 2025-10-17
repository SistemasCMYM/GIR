<?php

namespace App\Models\Empresas;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Models\Empresa;

class Centro extends MongoModel
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_empresas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'centros';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empresa_id',
        'area_id',
        'nombre',
        'direccion',
        'ciudad_id',
        'telefono',
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
     * Get the empresa that the centro belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Get the area that the centro belongs to.
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Get the ciudad that the centro belongs to.
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    /**
     * Get the empleados in this centro.
     */
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'centro_id');
    }

    /**
     * Get the responsable of this centro.
     */
    public function responsable()
    {
        return $this->belongsTo(Empleado::class, 'responsable_id');
    }

    /**
     * Scope a query to only include active centros.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
