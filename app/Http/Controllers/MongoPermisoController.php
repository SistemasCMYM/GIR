<?php

namespace App\Http\Controllers;

use App\Services\MongoPermisoService;
use Illuminate\Http\Request;

class MongoPermisoController extends Controller
{
    protected $permisoService;

    public function __construct(MongoPermisoService $permisoService)
    {
        $this->permisoService = $permisoService;
    }

    public function index($cuentaId)
    {
        $permisos = $this->permisoService->getPermisosByCuenta($cuentaId);
        return view('permisos.index', compact('permisos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'modulo' => 'required|string',
            'categoria' => 'required|string',
            'cuenta_id' => 'required|string',
            'acciones' => 'nullable|array',
        ]);

        $permiso = $this->permisoService->crearPermiso($data);
        return response()->json($permiso, 201);
    }
}