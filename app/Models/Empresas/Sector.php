<?php

namespace App\Models\Empresas;

use MongoDB\Laravel\Eloquent\Model as MongoModel;

class Sector extends MongoModel
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_empresas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'sectores';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
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
     * Get the areas in this sector.
     */
    public function areas()
    {
        return $this->hasMany(Area::class, 'sector_id');
    }

    /**
     * Get the empresas in this sector.
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'sector_id');
    }

    /**
     * Scope a query to only include active sectores.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }
}
