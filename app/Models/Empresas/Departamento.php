<?php

namespace App\Models\Empresas;

use MongoDB\Laravel\Eloquent\Model as MongoModel;

class Departamento extends MongoModel
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_empresas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'departamentos';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
        'codigo',
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
     * Get the ciudades in this departamento.
     */
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'departamento_id');
    }

    /**
     * Get the empresas in this departamento.
     */
    public function empresas()
    {
        return $this->hasManyThrough(Empresa::class, Ciudad::class, 'departamento_id', 'ciudad_id');
    }

    /**
     * Scope a query to only include active departamentos.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
