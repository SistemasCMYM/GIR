<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'mongodb_empresas';
    protected $collection = 'empresas';
    
    protected $fillable = [
        'id',
        'landingBlanco',
        'colorSecundario',
        'colorPrimario',
        'logo',
        'web',
        'nit',
        'nombre',
        'razon_social',
        'telefono',
        'direccion',
        'ciudad',
        'sector',
        'estado'
    ];
    
    protected $casts = [
        'landingBlanco' => 'boolean',
        'estado' => 'boolean'
    ];
}
