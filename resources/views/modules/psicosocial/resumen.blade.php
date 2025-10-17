@extends('layouts.dashboard')

@section('title',
    isset($diagnostico)
    ? 'Resumen de Diagnóstico - ' . $diagnostico->descripcion
    : 'Resumen General
    Psicosocial')

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
                <li class="breadcrumb-item">
                    <a href="{{ route('psicosocial.show', $diagnostico->id) }}">
                        <i class="fas fa-brain"></i> Diagnóstico
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-chart-bar"></i>
                    {{ isset($diagnostico) ? 'Resumen General del Diagnóstico' : 'Resumen General' }}
                </li>
            </ol>
        </nav>

        {{-- Definir dimensionesA para mostrar correctamente las dimensiones de Intralaboral A --}}
        @php
            $dimensionesA = $resumen['completo']['intralaboral_a']['dimensiones'] ?? [];
        @endphp

        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-2">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    @if (isset($diagnostico))
                                        Resumen de Diagnóstico testing - {{ $diagnostico->descripcion }}
                                    @else
                                        Resumen General - Batería de Riesgo Psicosocial
                                    @endif
                                </h4>
                                <p class="text-muted mb-0">
                                    @if (isset($diagnostico))
                                        Análisis completo de evaluaciones psicosociales del diagnóstico -
                                        {{ $empresaData->nombre ?? 'Empresa' }}
                                    @else
                                        Análisis completo de evaluaciones psicosociales -
                                        {{ $empresaData->nombre ?? 'Empresa' }}
                                    @endif
                                </p>
                                @if (isset($diagnostico))
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Creado: {{ \Carbon\Carbon::parse($diagnostico->created_at)->format('d/m/Y') }}
                                        @if ($diagnostico->fecha_cierre)
                                            | Cerrado:
                                            {{ \Carbon\Carbon::parse($diagnostico->fecha_cierre)->format('d/m/Y') }}
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filtros del Informe
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="filtrosForm" method="GET"
                            action="{{ isset($diagnostico) ? route('psicosocial.resumen', $diagnostico->id) : route('psicosocial.resumen-general') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Área</label>
                                    <select name="area" class="form-select">
                                        <option value="">Todas las áreas</option>
                                        @foreach ($opciones['areas'] ?? [] as $area)
                                            <option value="{{ $area }}"
                                                {{ request('area') == $area ? 'selected' : '' }}>
                                                {{ $area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sede</label>
                                    <select name="sede" class="form-select">
                                        <option value="">Todas las sedes</option>
                                        @foreach ($opciones['sedes'] ?? [] as $sede)
                                            <option value="{{ $sede }}"
                                                {{ request('sede') == $sede ? 'selected' : '' }}>
                                                {{ $sede }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ciudad</label>
                                    <select name="ciudad" class="form-select">
                                        <option value="">Todas las ciudades</option>
                                        @foreach ($opciones['ciudades'] ?? [] as $ciudad)
                                            <option value="{{ $ciudad }}"
                                                {{ request('ciudad') == $ciudad ? 'selected' : '' }}>
                                                {{ $ciudad }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Contrato</label>
                                    <select name="tipo_contrato" class="form-select">
                                        <option value="">Todos los tipos</option>
                                        @foreach ($opciones['tipos_contrato'] ?? [] as $tipo)
                                            <option value="{{ $tipo }}"
                                                {{ request('tipo_contrato') == $tipo ? 'selected' : '' }}>
                                                {{ $tipo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">Proceso</label>
                                    <select name="proceso" class="form-select">
                                        <option value="">Todos los procesos</option>
                                        @foreach ($opciones['procesos'] ?? [] as $proceso)
                                            <option value="{{ $proceso }}"
                                                {{ request('proceso') == $proceso ? 'selected' : '' }}>
                                                {{ $proceso }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Forma de Cuestionario</label>
                                    <select name="forma" class="form-select">
                                        <option value="">Todas las formas</option>
                                        <option value="A" {{ request('forma') == 'A' ? 'selected' : '' }}>Forma A
                                        </option>
                                        <option value="B" {{ request('forma') == 'B' ? 'selected' : '' }}>Forma B
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dominio</label>
                                    <select name="dominio" class="form-select">
                                        <option value="">Todos los dominios</option>
                                        {{-- Las opciones se podrían cargar dinámicamente --}}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dimensión</label>
                                    <select name="dimension" class="form-select">
                                        <option value="">Todas las dimensiones</option>
                                        {{-- Las opciones se podrían cargar dinámicamente --}}
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                                    </button>
                                    <a href="{{ isset($diagnostico) ? route('psicosocial.resumen', $diagnostico->id) : route('psicosocial.resumen-general') }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Limpiar
                                    </a>
                                    <button type="button" class="btn btn-success ms-2" onclick="mostrarTodo()">
                                        <i class="fas fa-eye me-2"></i>Mostrar Todo
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 1: Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['total'] ?? 0 }}</h3>
                        <p class="mb-0">Total Evaluaciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['completadas'] ?? 0 }}</h3>
                        <p class="mb-0">Completadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['pendientes'] ?? 0 }}</h3>
                        <p class="mb-0">Pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 2: Distribución de Niveles de Riesgo -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Distribución de Niveles de Riesgo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="distribucionRiesgoChart" width="400" height="200"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="legend-container">
                                    @php
                                        $distribucion = $resumen['completo']['distribucion_riesgo']['niveles'] ?? [];
                                        $colores = [
                                            'sin_riesgo' => '#008235',
                                            'bajo' => '#00D364',
                                            'medio' => '#FFD600',
                                            'alto' => '#DD0505',
                                            'muy_alto' => '#A30203',
                                        ];
                                        $etiquetas = [
                                            'sin_riesgo' => 'Sin Riesgo',
                                            'bajo' => 'Riesgo Bajo',
                                            'medio' => 'Riesgo Medio',
                                            'alto' => 'Riesgo Alto',
                                            'muy_alto' => 'Riesgo Muy Alto',
                                        ];
                                    @endphp
                                    @foreach ($distribucion as $nivel => $datos)
                                        <div class="legend-item mb-2">
                                            <span class="legend-color"
                                                style="background-color: {{ $colores[$nivel] }}"></span>
                                            <span class="legend-label">{{ $etiquetas[$nivel] }}</span>
                                            <span class="legend-value">{{ number_format($datos['porcentaje'], 1) }}%
                                                ({{ $datos['cantidad'] }})
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Datos Sociodemográficos -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Datos Sociodemográficos Generales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $categoriasSociodemograficas = [
                                    'genero' => 'Género',
                                    'edad' => 'Edad',
                                    'estado_civil' => 'Estado Civil',
                                    'tipo_vivienda' => 'Tipo de Vivienda',
                                    'estrato_social' => 'Estrato Social',
                                    'cargo' => 'Tipo de Cargo',
                                    'nivel_estudios' => 'Nivel de Estudios',
                                    'antiguedad_empresa' => 'Antigüedad en la Empresa',
                                    'antiguedad_cargo' => 'Antigüedad en el Cargo',
                                    'tipo_salario' => 'Tipo de Salario',
                                    'tipo_contrato' => 'Tipo de Contrato',
                                    'dependientes_economicos' => 'Dependientes Económicos',
                                    'horas_laboradas' => 'Horas Diarias Laboradas',
                                ];
                            @endphp
                            @foreach ($categoriasSociodemograficas as $categoria => $titulo)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ $titulo }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="chart_{{ $categoria }}" width="200"
                                                height="150"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 4: Total General Prueba Psicosocial -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Total General Prueba Psicosocial
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="totalGeneralChart" width="400" height="200"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Instrumento</th>
                                                <th>Sin Riesgo</th>
                                                <th>Bajo</th>
                                                <th>Medio</th>
                                                <th>Alto</th>
                                                <th>Muy Alto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($resumen['completo']['total_psicosocial']['por_instrumento'] ?? [] as $key => $datos)
                                                @if (is_array($datos) && (isset($datos['nombre']) || is_string($key)))
                                                    <tr>
                                                        <td><strong>{{ $datos['nombre'] ?? ucfirst($key) }}</strong></td>
                                                        <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                        <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                        <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                        <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                        <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 5: Resumen Prueba Intralaboral -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-building me-2"></i>Resumen Prueba Intralaboral
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-primary" onclick="imprimirSeccion('seccion-5')">
                                    <i class="fas fa-print me-1"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="seccion-5">
                        <p class="text-muted mb-4">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['intralaboral_general']['poblacion'] ?? 0 }}
                            personas aplicaron este test.
                        </p>

                        <!-- Gráfico y Tabla de Dominios -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h6>Intralaboral por Dominios</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <canvas id="intralaboralDominiosChart" height="200"></canvas>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Dominio</th>
                                                        <th>Nivel de Riesgo</th>
                                                        <th>Resultado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($resumen['completo']['intralaboral_general']['dominios'] ?? [] as $dominio)
                                                        @if (is_array($dominio) && isset($dominio['nombre']))
                                                            <tr>
                                                                <td rowspan="5">{{ safeString($dominio['nombre']) }}
                                                                </td>
                                                                <td>Sin Riesgo</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['sin_riesgo'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['sin_riesgo'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Bajo</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['bajo'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['bajo'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Medio</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['medio'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['medio'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Alto</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['alto'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['alto'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Muy Alto</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['muy_alto'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['muy_alto'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Dimensiones -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mt-4">Intralaboral por Dimensiones</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>Dominio</th>
                                                <th>Dimensión</th>
                                                <th>Sin Riesgo</th>
                                                <th>Bajo</th>
                                                <th>Medio</th>
                                                <th>Alto</th>
                                                <th>Muy Alto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $dominios = collect(
                                                    $resumen['completo']['intralaboral_general']['dimensiones'] ?? [],
                                                )->groupBy('dominio');
                                            @endphp
                                            @foreach ($dominios as $nombreDominio => $dimensiones)
                                                @foreach ($dimensiones as $index => $dimension)
                                                    @if (is_array($dimension) && isset($dimension['nombre']))
                                                        <tr>
                                                            @if ($index === 0)
                                                                <td rowspan="{{ count($dimensiones) }}"
                                                                    class="align-middle text-center">
                                                                    <strong>{{ $nombreDominio }}</strong>
                                                                </td>
                                                            @endif
                                                            <td>{{ safeString($dimension['nombre']) }}</td>
                                                            @foreach (['sin_riesgo', 'bajo', 'medio', 'alto', 'muy_alto'] as $nivel)
                                                                <td class="text-center"
                                                                    style="background-color: {{ $colores[$nivel] ?? '#fff' }}33;">
                                                                    {{ number_format(safeNumeric($dimension['porcentajes'][$nivel] ?? 0), 2) }}%
                                                                    <br>
                                                                    <small>({{ safeNumeric($dimension['contadores'][$nivel] ?? 0) }}
                                                                        de
                                                                        {{ safeNumeric($dimension['total'] ?? 0) }})</small>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción de Dimensiones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Análisis de Dimensiones</h6>
                                <p><strong>Dimensiones con puntajes de riesgo Muy Alto:</strong> ...</p>
                                <p><strong>Dimensiones con puntajes de riesgo Alto y Medio:</strong> ...</p>
                                <p><strong>Dimensiones con puntajes de riesgo Bajo y Sin Riesgo:</strong> ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 6: Resumen Prueba Intralaboral A -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard-check me-2"></i>Resumen Prueba Intralaboral A
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-primary" onclick="imprimirSeccion('seccion-6')">
                                    <i class="fas fa-print me-1"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="seccion-6">
                        <p class="text-muted mb-4">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['intralaboral_a']['poblacion'] ?? 0 }} personas
                            aplicaron este test.
                        </p>

                        <!-- Gráfico y Tabla de Dominios -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h6>Intralaboral A por Dominios</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <canvas id="intralaboralADominiosChart" height="200"></canvas>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Dominio</th>
                                                        <th>Nivel de Riesgo</th>
                                                        <th>Resultado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($resumen['completo']['intralaboral_a']['dominios'] ?? [] as $dominio)
                                                        @if (is_array($dominio) && isset($dominio['nombre']))
                                                            <tr>
                                                                <td rowspan="5">{{ safeString($dominio['nombre']) }}
                                                                </td>
                                                                <td>Sin Riesgo</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['sin_riesgo'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['sin_riesgo'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Bajo</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['bajo'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['bajo'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Medio</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['medio'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['medio'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Alto</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['alto'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['alto'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Muy Alto</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['muy_alto'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['muy_alto'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Dimensiones -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mt-4">Intralaboral A por Dimensiones</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>Dominio</th>
                                                <th>Dimensión</th>
                                                <th>Sin Riesgo</th>
                                                <th>Bajo</th>
                                                <th>Medio</th>
                                                <th>Alto</th>
                                                <th>Muy Alto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $dominiosA = collect(
                                                    $resumen['completo']['intralaboral_a']['dimensiones'] ?? [],
                                                )->groupBy('dominio');
                                            @endphp
                                            @foreach ($dominiosA as $nombreDominio => $dimensiones)
                                                @foreach ($dimensiones as $index => $dimension)
                                                    @if (is_array($dimension) && isset($dimension['nombre']))
                                                        <tr>
                                                            @if ($index === 0)
                                                                <td rowspan="{{ count($dimensiones) }}"
                                                                    class="align-middle text-center">
                                                                    <strong>{{ $nombreDominio }}</strong>
                                                                </td>
                                                            @endif
                                                            <td>{{ safeString($dimension['nombre']) }}</td>
                                                            @foreach (['sin_riesgo', 'bajo', 'medio', 'alto', 'muy_alto'] as $nivel)
                                                                <td class="text-center"
                                                                    style="background-color: {{ $colores[$nivel] ?? '#fff' }}33;">
                                                                    {{ number_format(safeNumeric($dimension['porcentajes'][$nivel] ?? 0), 2) }}%
                                                                    <br>
                                                                    <small>({{ safeNumeric($dimension['contadores'][$nivel] ?? 0) }}
                                                                        de
                                                                        {{ safeNumeric($dimension['total'] ?? 0) }})</small>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Descripción de Dimensiones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Análisis de Dimensiones (Forma A)</h6>
                                <p><strong>Dimensiones con puntajes de riesgo Muy Alto:</strong> ...</p>
                                <p><strong>Dimensiones con puntajes de riesgo Alto y Medio:</strong> ...</p>
                                <p><strong>Dimensiones con puntajes de riesgo Bajo y Sin Riesgo:</strong> ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 7: Resumen Prueba Intralaboral B -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard-list me-2"></i>Resumen Prueba Intralaboral B
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-primary" onclick="imprimirSeccion('seccion-7')">
                                    <i class="fas fa-print me-1"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="seccion-7">
                        <p class="text-muted mb-4">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['intralaboral_b']['poblacion'] ?? 0 }} personas
                            aplicaron este test.
                        </p>

                        <!-- Gráfico y Tabla de Dominios -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h6>Intralaboral B por Dominios</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <canvas id="intralaboralBDominiosChart" height="200"></canvas>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Dominio</th>
                                                        <th>Nivel de Riesgo</th>
                                                        <th>Resultado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($resumen['completo']['intralaboral_b']['dominios'] ?? [] as $dominio)
                                                        @if (is_array($dominio) && isset($dominio['nombre']))
                                                            <tr>
                                                                <td rowspan="5">{{ safeString($dominio['nombre']) }}
                                                                </td>
                                                                <td>Sin Riesgo</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['sin_riesgo'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['sin_riesgo'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Bajo</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['bajo'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['bajo'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Medio</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['medio'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['medio'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Alto</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['alto'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['alto'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Muy Alto</td>
                                                                <td>{{ number_format(safeNumeric($dominio['porcentajes']['muy_alto'] ?? 0), 2) }}%
                                                                    ({{ safeNumeric($dominio['contadores']['muy_alto'] ?? 0) }}
                                                                    de {{ safeNumeric($dominio['total'] ?? 0) }})</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Dimensiones -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mt-4">Intralaboral B por Dimensiones</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>Dominio</th>
                                                <th>Dimensión</th>
                                                <th>Sin Riesgo</th>
                                                <th>Bajo</th>
                                                <th>Medio</th>
                                                <th>Alto</th>
                                                <th>Muy Alto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $dominiosB = collect(
                                                    $resumen['completo']['intralaboral_b']['dimensiones'] ?? [],
                                                )->groupBy('dominio');
                                            @endphp
                                            @foreach ($dominiosB as $nombreDominio => $dimensiones)
                                                @foreach ($dimensiones as $index => $dimension)
                                                    @if (is_array($dimension) && isset($dimension['nombre']))
                                                        <tr>
                                                            @if ($index === 0)
                                                                <td rowspan="{{ count($dimensiones) }}"
                                                                    class="align-middle text-center">
                                                                    <strong>{{ $nombreDominio }}</strong>
                                                                </td>
                                                            @endif
                                                            <td>{{ safeString($dimension['nombre']) }}</td>
                                                            @foreach (['sin_riesgo', 'bajo', 'medio', 'alto', 'muy_alto'] as $nivel)
                                                                <td class="text-center"
                                                                    style="background-color: {{ $colores[$nivel] ?? '#fff' }}33;">
                                                                    {{ number_format(safeNumeric($dimension['porcentajes'][$nivel] ?? 0), 2) }}%
                                                                    <br>
                                                                    <small>({{ safeNumeric($dimension['contadores'][$nivel] ?? 0) }}
                                                                        de
                                                                        {{ safeNumeric($dimension['total'] ?? 0) }})</small>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Descripción de Dimensiones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Análisis de Dimensiones (Forma B)</h6>
                                <p><strong>Dimensiones con puntajes de riesgo Muy Alto:</strong> ...</p>
                                <p><strong>Dimensiones con puntajes de riesgo Alto y Medio:</strong> ...</p>
                                <p><strong>Dimensiones con puntajes de riesgo Bajo y Sin Riesgo:</strong> ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 8: Resumen Extralaboral -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-home me-2"></i>Resumen Extralaboral
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-primary" onclick="imprimirSeccion('seccion-8')">
                                    <i class="fas fa-print me-1"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="seccion-8">
                        <p class="text-muted mb-4">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['extralaboral']['poblacion'] ?? 0 }} personas
                            aplicaron este test.
                        </p>

                        <!-- Gráfico y Tabla de Dimensiones -->
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="extralaboralChart" height="200"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Dimensión</th>
                                                <th>Nivel de Riesgo</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($resumen['completo']['extralaboral']['dimensiones'] ?? [] as $dimension)
                                                @if (is_array($dimension) && isset($dimension['nombre']))
                                                    <tr>
                                                        <td rowspan="5">{{ safeString($dimension['nombre']) }}</td>
                                                        <td>Sin Riesgo</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['sin_riesgo'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['sin_riesgo'] ?? 0) }}
                                                            de {{ safeNumeric($dimension['total'] ?? 0) }})
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bajo</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['bajo'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['bajo'] ?? 0) }} de
                                                            {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Medio</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['medio'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['medio'] ?? 0) }} de
                                                            {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alto</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['alto'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['alto'] ?? 0) }} de
                                                            {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Muy Alto</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['muy_alto'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['muy_alto'] ?? 0) }}
                                                            de {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 9: Resumen Estrés -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-heart-pulse me-2"></i>Resumen Estrés
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-primary" onclick="imprimirSeccion('seccion-9')">
                                    <i class="fas fa-print me-1"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="seccion-9">
                        <p class="text-muted mb-4">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['estres']['poblacion'] ?? 0 }} personas
                            aplicaron este test.
                        </p>

                        <!-- Gráfico y Tabla de Dimensiones -->
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="estresChart" height="200"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Dimensión</th>
                                                <th>Nivel de Riesgo</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($resumen['completo']['estres']['dimensiones'] ?? [] as $dimension)
                                                @if (is_array($dimension) && isset($dimension['nombre']))
                                                    <tr>
                                                        <td rowspan="5">{{ safeString($dimension['nombre']) }}</td>
                                                        <td>Sin Riesgo</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['sin_riesgo'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['sin_riesgo'] ?? 0) }}
                                                            de {{ safeNumeric($dimension['total'] ?? 0) }})
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bajo</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['bajo'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['bajo'] ?? 0) }} de
                                                            {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Medio</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['medio'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['medio'] ?? 0) }} de
                                                            {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alto</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['alto'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['alto'] ?? 0) }} de
                                                            {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Muy Alto</td>
                                                        <td>{{ number_format(safeNumeric($dimension['porcentajes']['muy_alto'] ?? 0), 2) }}%
                                                            ({{ safeNumeric($dimension['contadores']['muy_alto'] ?? 0) }}
                                                            de {{ safeNumeric($dimension['total'] ?? 0) }})</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- cierre .container-fluid py-4 -->

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const riskColors = {
                sin_riesgo: 'rgba(0, 130, 53, 0.7)',
                bajo: 'rgba(0, 211, 100, 0.7)',
                medio: 'rgba(255, 214, 0, 0.7)',
                alto: 'rgba(221, 5, 5, 0.7)',
                muy_alto: 'rgba(163, 2, 3, 0.7)'
            };

            const riskBorderColors = {
                sin_riesgo: 'rgba(0, 130, 53, 1)',
                bajo: 'rgba(0, 211, 100, 1)',
                medio: 'rgba(255, 214, 0, 1)',
                alto: 'rgba(221, 5, 5, 1)',
                muy_alto: 'rgba(163, 2, 3, 1)'
            };

            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + '%';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            };

            // Lógica para inicializar todos los gráficos...
            // Ejemplo para un gráfico:
            const intralaboralDominiosData = @json($resumen['completo']['intralaboral_general']['dominios'] ?? []);
            if (document.getElementById('intralaboralDominiosChart') && intralaboralDominiosData.length > 0) {
                const labels = intralaboralDominiosData.map(d => d.nombre);
                const datasets = Object.keys(riskColors).map(level => ({
                    label: level.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                    data: intralaboralDominiosData.map(d => d.porcentajes[level] || 0),
                    backgroundColor: riskColors[level],
                    borderColor: riskBorderColors[level],
                    borderWidth: 1
                }));

                new Chart(document.getElementById('intralaboralDominiosChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets
                    },
                    options: chartOptions
                });
            }
        });

        function mostrarTodo() {
            // ... (código existente)
        }

        function imprimirSeccion(seccionId) {
            // ... (código existente)
        }
    </script>
@endpush
