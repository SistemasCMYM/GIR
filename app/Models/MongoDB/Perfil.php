<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class Perfil extends Model
{
    protected $connection = 'mongodb_cmym';
    protected $collection = 'perfiles';
    
    protected $fillable = [
        'id',
        'cuenta_id',
        'nombre',
        'apellido',
        'descripcion',
        'genero',
        'modulos',
        'permisos',
        'ocupacion',
        'firma',
        'pieFirma',
        'licencia'
    ];
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }
}
