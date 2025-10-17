<?php

namespace App\Services;

use App\Models\Mongo\Sesion;

class MongoSesionService
{
    public function getSesionesByCuenta($cuentaId)
    {
        return Sesion::where('cuenta_id', $cuentaId)->get();
    }

    public function crearSesion($data)
    {
        return Sesion::create($data);
    }    public function cerrarSesion($sesionId)
    {
        $sesion = Sesion::where('_id', $sesionId)->first();
        if ($sesion) {
            $sesion->update(['estado' => 'cerrada', 'fecha_fin' => now()]);
        }
        return $sesion;
    }
}