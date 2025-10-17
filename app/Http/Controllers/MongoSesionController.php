<?php

namespace App\Http\Controllers;

use App\Services\MongoSesionService;
use Illuminate\Http\Request;

class MongoSesionController extends Controller
{
    protected $sesionService;

    public function __construct(MongoSesionService $sesionService)
    {
        $this->sesionService = $sesionService;
    }

    public function index($cuentaId)
    {
        $sesiones = $this->sesionService->getSesionesByCuenta($cuentaId);
        return view('sesiones.index', compact('sesiones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cuenta_id' => 'required|string',
            'modulo' => 'required|string',
            'estado' => 'required|string',
            'fecha_inicio' => 'required|date',
        ]);

        $sesion = $this->sesionService->crearSesion($data);
        return response()->json($sesion, 201);
    }

    public function close($id)
    {
        $sesion = $this->sesionService->cerrarSesion($id);
        return response()->json($sesion, 200);
    }
}