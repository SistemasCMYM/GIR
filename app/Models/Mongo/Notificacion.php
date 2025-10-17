<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Notificacion extends Model
{
    protected $connection = 'mongodb_cuentas'; // Conexión a MongoDB
    protected $collection = 'notificaciones'; // Nombre de la colección
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tipo', 'modulo', 'cuenta_id', 'acciones', '_tags', '_slug', '_esBorrado',
        '_esPublico', '_fechaModificado', '_fechaBorrado', '_fechaCreado'
    ];

    // Relación con cuentas
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }
}
