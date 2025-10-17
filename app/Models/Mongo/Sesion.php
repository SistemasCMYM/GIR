<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Sesion extends Model
{
    protected $connection = 'mongodb_cuentas'; // Conexión a MongoDB
    protected $collection = 'sesiones'; // Nombre de la colección
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'cuenta_id', 'modulo', 'estado', 'fecha_inicio', 'fecha_fin', '_tags', '_slug',
        '_esBorrado', '_esPublico', '_fechaModificado', '_fechaBorrado', '_fechaCreado'
    ];

    // Relación con cuentas
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }
}
