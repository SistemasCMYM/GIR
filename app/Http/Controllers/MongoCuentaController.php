<?php

namespace App\Http\Controllers;

use App\Services\MongoCuentaService;

class MongoCuentaController extends Controller
{
    protected $cuentaService;

    public function __construct(MongoCuentaService $cuentaService)
    {
        $this->cuentaService = $cuentaService;
    }

    public function show($id)
    {
        $cuenta = $this->cuentaService->getCuentaById($id);
        return view('cuentas.show', compact('cuenta'));
    }
}