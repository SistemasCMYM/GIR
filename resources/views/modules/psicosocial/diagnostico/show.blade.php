@extends('layouts.dashboard')

@section('title', 'Detalles del Diagnóstico Psicosocial')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-brain text-info"></i>
                        Diagnóstico Psicosocial
                    </h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="{{ route('psicosocial.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empleados.index') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('psicosocial.index') }}">
                        <i class="fas fa-brain"></i> Psicosocial
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-chart-bar"></i>
                    {{ isset($diagnostico) ? 'Información Diagnóstico' : 'Resumen General' }}
                </li>
            </ol>
        </nav>

        <div class="content">
            <div class="container-fluid">
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <a href="{{ route('psicosocial.diagnostico.resultados', $diagnostico->id) }}"
                            class="btn btn-success">
                            <i class="fas fa-chart-bar"></i> Ver Resultados Grupales
                        </a>
                        <a href="#" class="btn btn-primary" onclick="exportarInforme('{{ $diagnostico->id }}')">
                            <i class="fas fa-file-export"></i> Exportar Informe
                        </a>
                        <a href="#" class="btn btn-info" onclick="imprimirInforme('{{ $diagnostico->id }}')">
                            <i class="fas fa-print"></i> Imprimir
                        </a>
                        <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary ms-auto">
                            <i class="fas fa-arrow-left"></i> Volver al Inicio
                        </a>

                    </div>
                </div><br>
                <!-- INFORMACIÓN DEL DIAGNÓSTICO -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header badge gir-bg-gradient text-white">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i>
                                    Información del Diagnóstico seleccionado
                                </h3>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl class="row">
                                            <dt class="col-sm-4">Descripción:</dt>
                                            <dd class="col-sm-8">{{ $diagnostico->descripcion ?? 'No especificado' }}</dd>

                                            <dt class="col-sm-4">Clave:</dt>
                                            <dd class="col-sm-8">{{ $diagnostico->clave ?? 'No especificado' }}</dd>

                                            <dt class="col-sm-4">Empresa:</dt>
                                            <dd class="col-sm-8">
                                                <span class="badge gir-bg-gradient text-white px-3 py-2">
                                                    <i
                                                        class="fas fa-building me-1"></i>{{ $empresaNombre ?? 'No especificado' }}
                                                </span>
                                            </dd>

                                            <dt class="col-sm-4">Profesional:</dt>
                                            <dd class="col-sm-8">
                                                <span class="badge gir-bg-secondary text-dark px-3 py-2">
                                                    <i
                                                        class="fas fa-user-tie me-1"></i>{{ $profesionalNombre ?? 'No especificado' }}
                                                </span>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl class="row">
                                            <dt class="col-sm-4">Fecha Creación:</dt>
                                            <dd class="col-sm-8">
                                                {{ $diagnostico->created_at ? $diagnostico->created_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                            </dd>

                                            <dt class="col-sm-4">Última Actualización:</dt>
                                            <dd class="col-sm-8">
                                                {{ $diagnostico->updated_at ? $diagnostico->updated_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ESTADÍSTICAS DEL DIAGNÓSTICO -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i> Estadísticas
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $totalHojas }}</h3>
                                                <p>Total Hojas</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-clipboard-list"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3>{{ $totalCompletadas }}</h3>
                                                <p>Completadas</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3>{{ $totalPendientes }}</h3>
                                                <p>Pendientes</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="small-box bg-gradient-navy">
                                            <div class="inner">
                                                <h3>{{ $porcentajeCompletado }}%</h3>
                                                <p>Completado</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-percentage"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estados de cuestionarios -->
                                <div class="row mt-4">
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Estado Intralaboral</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="progress">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosIntralaboral['completado'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosIntralaboral['completado'] ?? 0 }} Completados
                                                    </div>
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosIntralaboral['en_progreso'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosIntralaboral['en_progreso'] ?? 0 }} En Progreso
                                                    </div>
                                                    <div class="progress-bar bg-secondary"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosIntralaboral['pendiente'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosIntralaboral['pendiente'] ?? 0 }} Pendientes
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Estado Extralaboral</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="progress">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosExtralaboral['completado'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosExtralaboral['completado'] ?? 0 }} Completados
                                                    </div>
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosExtralaboral['en_progreso'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosExtralaboral['en_progreso'] ?? 0 }} En Progreso
                                                    </div>
                                                    <div class="progress-bar bg-secondary"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosExtralaboral['pendiente'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosExtralaboral['pendiente'] ?? 0 }} Pendientes
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Estado Estrés</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="progress">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosEstres['completado'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosEstres['completado'] ?? 0 }} Completados
                                                    </div>
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosEstres['en_progreso'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosEstres['en_progreso'] ?? 0 }} En Progreso
                                                    </div>
                                                    <div class="progress-bar bg-secondary"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosEstres['pendiente'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosEstres['pendiente'] ?? 0 }} Pendientes
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Estado Datos</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="progress">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosDatos['completado'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosDatos['completado'] ?? 0 }} Completados
                                                    </div>
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosDatos['en_progreso'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosDatos['en_progreso'] ?? 0 }} En Progreso
                                                    </div>
                                                    <div class="progress-bar bg-secondary"
                                                        style="width: {{ $totalHojas > 0 ? (($estadosDatos['pendiente'] ?? 0) / $totalHojas) * 100 : 0 }}%">
                                                        {{ $estadosDatos['pendiente'] ?? 0 }} Pendientes
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i> Niveles de Riesgo
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="riesgoChart"></canvas>
                                <div class="mt-3 small text-muted">
                                    <span class="badge text-white" style="background-color: #008235;">Sin Riesgo:
                                        {{ $nivelesRiesgo['sin_riesgo'] ?? 0 }}</span>
                                    <span class="badge text-white" style="background-color: #00D364;">Bajo:
                                        {{ $nivelesRiesgo['bajo'] ?? 0 }}</span>
                                    <span class="badge text-dark" style="background-color: #FFD600;">Medio:
                                        {{ $nivelesRiesgo['medio'] ?? 0 }}</span>
                                    <span class="badge text-white" style="background-color: #DA3D3D;">Alto:
                                        {{ $nivelesRiesgo['alto'] ?? 0 }}</span>
                                    <span class="badge text-white" style="background-color: #D10000;">Muy Alto:
                                        {{ $nivelesRiesgo['muy_alto'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EVALUACIONES ASIGNADAS A EMPLEADOS -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-users"></i> Evaluaciones Asignadas a Empleados
                                </h3>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-primary btn-sm"
                                        onclick="agregarNuevaHoja('{{ $diagnostico->id }}')">
                                        <i class="fas fa-plus"></i> Agregar Evaluación
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($hojasPaginadas->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Empleado</th>
                                                    <th>Documento</th>
                                                    <th>E-mail</th>
                                                    <th>Nivel de riesgo</th>
                                                    <th>Puntaje</th>
                                                    <th>Datos SD</th>
                                                    <th>Intralaboral</th>
                                                    <th>Extralaboral</th>
                                                    <th>Estrés</th>
                                                    <th>Progreso</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($hojasPaginadas as $hoja)
                                                    @php
                                                        // Obtener datos del empleado de forma segura
                                                        $empleado = null;
                                                        $empleadoId = isset($hoja->empleado_id)
                                                            ? $hoja->empleado_id
                                                            : (isset($hoja['empleado_id'])
                                                                ? $hoja['empleado_id']
                                                                : null);
                                                        if ($empleadoId) {
                                                            $empleado = \App\Models\Empresas\Empleado::find(
                                                                $empleadoId,
                                                            );
                                                        }

                                                        // Obtener propiedades de forma segura
                                                        $datosValue = isset($hoja->datos)
                                                            ? $hoja->datos
                                                            : (isset($hoja['datos'])
                                                                ? $hoja['datos']
                                                                : null);
                                                        $intralaboralValue = isset($hoja->intralaboral)
                                                            ? $hoja->intralaboral
                                                            : (isset($hoja['intralaboral'])
                                                                ? $hoja['intralaboral']
                                                                : null);
                                                        $extralaboralValue = isset($hoja->extralaboral)
                                                            ? $hoja->extralaboral
                                                            : (isset($hoja['extralaboral'])
                                                                ? $hoja['extralaboral']
                                                                : null);
                                                        $estresValue = isset($hoja->estres)
                                                            ? $hoja->estres
                                                            : (isset($hoja['estres'])
                                                                ? $hoja['estres']
                                                                : null);

                                                        // Calcular progreso usando obtenerEstadoOficial para cada campo
                                                        $estadoDatos = function_exists('obtenerEstadoOficial')
                                                            ? obtenerEstadoOficial($datosValue)
                                                            : $datosValue ?? 'pendiente';
                                                        $estadoIntralaboral = function_exists('obtenerEstadoOficial')
                                                            ? obtenerEstadoOficial($intralaboralValue)
                                                            : $intralaboralValue ?? 'pendiente';
                                                        $estadoExtralaboral = function_exists('obtenerEstadoOficial')
                                                            ? obtenerEstadoOficial($extralaboralValue)
                                                            : $extralaboralValue ?? 'pendiente';
                                                        $estadoEstres = function_exists('obtenerEstadoOficial')
                                                            ? obtenerEstadoOficial($estresValue)
                                                            : $estresValue ?? 'pendiente';

                                                        $estados = [
                                                            $estadoDatos,
                                                            $estadoIntralaboral,
                                                            $estadoExtralaboral,
                                                            $estadoEstres,
                                                        ];
                                                        $completados = count(
                                                            array_filter($estados, function ($estado) {
                                                                return $estado === 'completado';
                                                            }),
                                                        );
                                                        $progreso = round(($completados / 4) * 100);

                                                        // Calcular nivel de riesgo y puntaje usando el Service
                                                        $nivelRiesgo = 'Pendiente';
                                                        $puntajeTotal = 0;
                                                        $colorRiesgo = 'secondary';

                                                        try {
                                                            if ($progreso == 100) {
                                                                $service = app(
                                                                    \App\Services\BateriaPsicosocialService::class,
                                                                );
                                                                $resultados = $service->calcularResultadosCompletos(
                                                                    $hoja,
                                                                );
                                                                if (isset($resultados['interpretacion']['total'])) {
                                                                    $nivelRiesgo =
                                                                        $resultados['interpretacion']['total'][
                                                                            'nivel'
                                                                        ] ?? 'Pendiente';
                                                                    $puntajeTotal =
                                                                        $resultados['puntajes_transformados'][
                                                                            'total'
                                                                        ] ?? 0;
                                                                    $colorRiesgo = str_replace(
                                                                        '_',
                                                                        '-',
                                                                        strtolower($nivelRiesgo),
                                                                    );
                                                                }
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Si hay error en el cálculo, mantener valores por defecto
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            @if ($empleado)
                                                                {{ $empleado->primerNombre ?? 'N/A' }}
                                                                {{ $empleado->primerApellido ?? '' }}
                                                            @else
                                                                {{ isset($hoja->nombre) ? $hoja->nombre : (isset($hoja['nombre']) ? $hoja['nombre'] : 'No especificado') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($empleado)
                                                                {{ $empleado->numeroDocumento ?? 'N/A' }}
                                                            @else
                                                                {{ isset($hoja->numero_documento) ? $hoja->numero_documento : (isset($hoja['numero_documento']) ? $hoja['numero_documento'] : 'N/A') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($empleado)
                                                                {{ $empleado->email ?? 'N/A' }}
                                                            @else
                                                                {{ isset($hoja->email) ? $hoja->email : (isset($hoja['email']) ? $hoja['email'] : 'N/A') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $nivelRiesgoTexto = 'Pendiente';
                                                                $colorRiesgo = 'secondary';

                                                                if (isset($hoja->nivel_riesgo)) {
                                                                    switch ($hoja->nivel_riesgo) {
                                                                        case 'sin_riesgo':
                                                                            $nivelRiesgoTexto = 'Sin Riesgo';
                                                                            $colorRiesgo = 'sin-riesgo';
                                                                            break;
                                                                        case 'bajo':
                                                                            $nivelRiesgoTexto = 'Riesgo Bajo';
                                                                            $colorRiesgo = 'bajo';
                                                                            break;
                                                                        case 'medio':
                                                                            $nivelRiesgoTexto = 'Riesgo Medio';
                                                                            $colorRiesgo = 'medio';
                                                                            break;
                                                                        case 'alto':
                                                                            $nivelRiesgoTexto = 'Riesgo Alto';
                                                                            $colorRiesgo = 'alto';
                                                                            break;
                                                                        case 'muy_alto':
                                                                            $nivelRiesgoTexto = 'Riesgo Muy Alto';
                                                                            $colorRiesgo = 'muy-alto';
                                                                            break;
                                                                    }
                                                                }
                                                            @endphp
                                                            <span
                                                                class="badge bg-{{ $colorRiesgo }}">{{ $nivelRiesgoTexto }}</span>
                                                        </td>
                                                        <td>
                                                            <strong>{{ number_format($hoja->puntaje_total ?? 0, 1) }}</strong>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $estadoDatos = function_exists('obtenerEstadoOficial')
                                                                    ? obtenerEstadoOficial($hoja->datos ?? null)
                                                                    : $hoja->datos ?? $hoja->datos;
                                                            @endphp
                                                            {!! function_exists('obtenerBadgeEstado')
                                                                ? obtenerBadgeEstado($hoja->datos)
                                                                : ($hoja->datos === 'completado'
                                                                    ? '<span class="badge bg-success">Completado</span>'
                                                                    : ($hoja->datos === 'en_progreso'
                                                                        ? '<span class="badge bg-warning text-dark">En Progreso</span>'
                                                                        : '<span class="badge bg-secondary">Pendiente</span>')) !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $estadoIntralaboral = function_exists(
                                                                    'obtenerEstadoOficial',
                                                                )
                                                                    ? obtenerEstadoOficial($hoja->intralaboral ?? null)
                                                                    : $hoja->intralaboral ?? 'pendiente';
                                                            @endphp
                                                            {!! function_exists('obtenerBadgeEstado')
                                                                ? obtenerBadgeEstado($hoja->intralaboral ?? null)
                                                                : ($estadoIntralaboral === 'completado'
                                                                    ? '<span class="badge bg-success">Completado</span>'
                                                                    : ($estadoIntralaboral === 'en_progreso'
                                                                        ? '<span class="badge bg-warning text-dark">En Progreso</span>'
                                                                        : '<span class="badge bg-secondary">Pendiente</span>')) !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $estadoExtralaboral = function_exists(
                                                                    'obtenerEstadoOficial',
                                                                )
                                                                    ? obtenerEstadoOficial($hoja->extralaboral ?? null)
                                                                    : $hoja->extralaboral ?? 'pendiente';
                                                            @endphp
                                                            {!! function_exists('obtenerBadgeEstado')
                                                                ? obtenerBadgeEstado($hoja->extralaboral ?? null)
                                                                : ($estadoExtralaboral === 'completado'
                                                                    ? '<span class="badge bg-success">Completado</span>'
                                                                    : ($estadoExtralaboral === 'en_progreso'
                                                                        ? '<span class="badge bg-warning text-dark">En Progreso</span>'
                                                                        : '<span class="badge bg-secondary">Pendiente</span>')) !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $estadoEstres = function_exists('obtenerEstadoOficial')
                                                                    ? obtenerEstadoOficial($hoja->estres ?? null)
                                                                    : $hoja->estres ?? 'pendiente';
                                                            @endphp
                                                            {!! function_exists('obtenerBadgeEstado')
                                                                ? obtenerBadgeEstado($hoja->estres ?? null)
                                                                : ($estadoEstres === 'completado'
                                                                    ? '<span class="badge bg-success">Completado</span>'
                                                                    : ($estadoEstres === 'en_progreso'
                                                                        ? '<span class="badge bg-warning text-dark">En Progreso</span>'
                                                                        : '<span class="badge bg-secondary">Pendiente</span>')) !!}
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar @if ($progreso == 100) bg-success @elseif($progreso >= 50) bg-warning @else bg-info @endif"
                                                                    role="progressbar"
                                                                    style="width: {{ $progreso }}%;"
                                                                    aria-valuenow="{{ $progreso }}" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                    {{ $progreso }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('psicosocial.evaluacion', ['diagnosticoId' => $diagnostico->id, 'hojaId' => $hoja->id]) }}"
                                                                    class="btn btn-info btn-sm" title="Ver Test">
                                                                    <i class="fas fa-clipboard-check"></i>
                                                                </a>
                                                                <a href="{{ route('psicosocial.resumen.individual', $hoja->id) }}"
                                                                    class="btn btn-primary btn-sm"
                                                                    title="Resumen Individual">
                                                                    <i class="fas fa-chart-line"></i>
                                                                </a>
                                                                <button class="btn btn-success btn-sm" title="Copiar Link"
                                                                    onclick="copiarLinkEmpleado('{{ $hoja->id }}')">
                                                                    <i class="fas fa-link"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Paginación -->
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $hojasPaginadas->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">No hay hojas registradas</h4>
                                        <p class="text-muted">No se han registrado hojas para este diagnóstico aún.</p>
                                        <a href="#" class="btn btn-primary"
                                            onclick="agregarNuevaHoja('{{ $diagnostico->id }}')">
                                            <i class="fas fa-plus"></i> Agregar Primera Hoja
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Chart.js y plugins para gráficas 3D -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js-plugin-3d@1.9.0/dist/chart.3d.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración para efecto 3D
            const threeDConfig = {
                enabled: true,
                alpha: 20, // rotación en el eje X
                beta: 30, // rotación en el eje Y
                depth: 30, // profundidad del efecto 3D
                shadowDepth: 2
            };

            // Detectar si hay datos significativos o solo ceros
            const riesgoData = [
                {{ $nivelesRiesgo['sin_riesgo'] ?? 0 }},
                {{ $nivelesRiesgo['bajo'] ?? 0 }},
                {{ $nivelesRiesgo['medio'] ?? 0 }},
                {{ $nivelesRiesgo['alto'] ?? 0 }},
                {{ $nivelesRiesgo['muy_alto'] ?? 0 }}
            ];

            const hasData = riesgoData.some(value => value > 0);

            // Si no hay datos, utilizar datos de ejemplo para visualizar el gráfico
            const displayData = hasData ? riesgoData : [5, 10, 8, 3, 2];

            // Gráfico de Niveles de Riesgo
            var ctx = document.getElementById('riesgoChart').getContext('2d');
            var riesgoChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sin Riesgo', 'Bajo', 'Medio', 'Alto', 'Muy Alto'],
                    datasets: [{
                        data: displayData,
                        backgroundColor: [
                            '#008235', // Sin Riesgo - Verde
                            '#00D364', // Riesgo Bajo - Verde claro
                            '#FFD600', // Riesgo Medio - Amarillo
                            '#DA3D3D', // Riesgo Alto - Rojo
                            '#D10000' // Riesgo Muy Alto - Rojo oscuro
                        ],
                        borderWidth: 2,
                        borderColor: [
                            '#008235',
                            '#00D364',
                            '#FFD600',
                            '#DA3D3D',
                            '#D10000'
                        ],
                        hoverOffset: 10
                    }]
                },
                plugins: [ChartJs3D],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '50%',
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            },
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#555',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true,
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            textStrokeColor: '#000',
                            textStrokeWidth: 1,
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: function(value, context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return percentage + '%';
                            },
                            display: function(context) {
                                return context.dataset.data[context.dataIndex] > 0;
                            }
                        },
                        '3d': threeDConfig
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });

            // Mensaje si no hay datos reales
            if (!hasData) {
                const noDataMsg = document.createElement('div');
                noDataMsg.className = 'text-center text-danger mt-2';
                noDataMsg.innerHTML =
                    '<small><i class="fas fa-exclamation-triangle"></i> Mostrando datos de ejemplo. No hay evaluaciones completadas con resultados.</small>';
                document.getElementById('riesgoChart').parentNode.appendChild(noDataMsg);
            }
        });

        // Función para copiar el link del empleado
        function copiarLinkEmpleado(hojaId) {
            const baseUrl = window.location.origin;
            const linkEmpleado = `${baseUrl}/psicosocial/evaluacion-publica/${hojaId}`;

            // Crear un elemento temporal para copiar el texto
            const tempElement = document.createElement('textarea');
            tempElement.value = linkEmpleado;
            document.body.appendChild(tempElement);
            tempElement.select();
            document.execCommand('copy');
            document.body.removeChild(tempElement);

            // Mostrar notificación
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Link copiado',
                    text: 'El enlace de evaluación ha sido copiado al portapapeles.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Link copiado al portapapeles: ' + linkEmpleado);
            }
        }

        // Función para agregar una nueva hoja
        function agregarNuevaHoja(diagnosticoId) {
            // Aquí puedes redirigir a la página de creación o mostrar un modal para agregar una nueva hoja
            alert('Funcionalidad de agregar hoja no implementada aún.');
        }

        // Función para exportar informe
        function exportarInforme(diagnosticoId) {
            window.location.href = `/psicosocial/diagnostico/${diagnosticoId}/exportar`;
        }

        // Función para imprimir informe
        function imprimirInforme(diagnosticoId) {
            window.location.href = `/psicosocial/diagnostico/${diagnosticoId}/imprimir`;
        }
    </script>
@endsection
