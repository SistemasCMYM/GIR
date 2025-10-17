<?php

namespace App\Http\Controllers;

use App\Services\MongoNotificacionService;
use Illuminate\Http\Request;

class MongoNotificacionController extends Controller
{
    protected $notificacionService;

    public function __construct(MongoNotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    public function index($cuentaId)
    {
        $notificaciones = $this->notificacionService->getNotificacionesByCuenta($cuentaId);
        return view('notificaciones.index', compact('notificaciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|string',
            'modulo' => 'required|string',
            'cuenta_id' => 'required|string',
            'acciones' => 'nullable|array',
        ]);

        $notificacion = $this->notificacionService->crearNotificacion($data);
        return response()->json($notificacion, 201);
    }
}