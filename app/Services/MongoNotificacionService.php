<?php

namespace App\Services;

use App\Models\Mongo\Notificacion;

class MongoNotificacionService
{
    public function getNotificacionesByCuenta($cuentaId)
    {
        return Notificacion::where('cuenta_id', $cuentaId)->get();
    }

    public function crearNotificacion($data)
    {
        return Notificacion::create($data);
    }
}