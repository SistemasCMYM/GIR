<?php

namespace App\Models\Empresas;

use MongoDB\Laravel\Eloquent\Model as MongoModel;

class Ciudad extends MongoModel
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_empresas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'ciudades';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'departamento_id',
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
     * Get the departamento that the ciudad belongs to.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * Get the empresas in this ciudad.
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'ciudad_id');
    }

    /**
     * Scope a query to only include active ciudades.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
