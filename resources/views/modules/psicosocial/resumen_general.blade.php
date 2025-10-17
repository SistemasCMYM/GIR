@extends('layouts.dashboard')

@section('title',
    isset($diagnostico)
    ? 'Resumen de Diagnóstico - ' . $diagnostico->descripcion
    : 'Resumen General
    Psicosocial')

@section('content')
    <div class="container-fluid py-4">

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
                                        Resumen de Diagnóstico - {{ $diagnostico->descripcion }}
                                    @else
                                        Resumen General - Batería de Riesgo Psicosocial
                                    @endif
                                </h4>
                                <p class="text-muted mb-0">
                                    @if (isset($diagnostico))
                                        Análisis completo de evaluaciones psicosociales del diagnóstico -
                                        {{ $empresaData['nombre'] ?? 'Empresa' }}
                                    @else
                                        Análisis completo de evaluaciones psicosociales -
                                        {{ $empresaData['nombre'] ?? 'Empresa' }}
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
                            <div class="text-end">
                                <a href="{{ route('psicosocial.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                                </a>
                                @if (isset($diagnostico))
                                    <a href="{{ route('psicosocial.show', $diagnostico->id) }}"
                                        class="btn btn-outline-primary ms-2">
                                        <i class="fas fa-eye me-2"></i>Ver Diagnóstico
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($mensaje))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>{{ $mensaje }}
                    </div>
                </div>
            </div>
        @else
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
                                    <div class="col-md-6 d-flex align-items-end">
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
                            <h3 class="mb-0">{{ $estadisticas['total'] }}</h3>
                            <p class="mb-0">Total Evaluaciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-0">{{ $estadisticas['completadas'] }}</h3>
                            <p class="mb-0">Completadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-0">{{ $estadisticas['pendientes'] }}</h3>
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
                                            $distribucion =
                                                $resumen['completo']['distribucion_riesgo']['niveles'] ?? [];
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
                                            <div class="card-body" style="min-height: 300px;">
                                                <canvas id="chart_{{ $categoria }}"></canvas>
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
                                                @php
                                                    $instrumentos =
                                                        $resumen['completo']['total_psicosocial']['por_instrumento'] ??
                                                        [];
                                                    $nombresInstrumentos = [
                                                        'intralaboral' => 'Intralaboral',
                                                        'extralaboral' => 'Extralaboral',
                                                        'estres' => 'Estrés',
                                                    ];
                                                @endphp
                                                @foreach ($instrumentos as $instrumento => $datos)
                                                    <tr>
                                                        <td><strong>{{ $nombresInstrumentos[$instrumento] }}</strong></td>
                                                        <td>{{ $datos['sin_riesgo'] ?? 0 }}</td>
                                                        <td>{{ $datos['bajo'] ?? 0 }}</td>
                                                        <td>{{ $datos['medio'] ?? 0 }}</td>
                                                        <td>{{ $datos['alto'] ?? 0 }}</td>
                                                        <td>{{ $datos['muy_alto'] ?? 0 }}</td>
                                                    </tr>
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

            <!-- Continuar con las siguientes secciones... -->

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
                            <p class="text-muted mb-3">
                                <strong>Población Aplicable:</strong>
                                {{ $resumen['completo']['intralaboral_general']['poblacion'] ?? 0 }} personas aplicaron
                                este test
                            </p>

                            <!-- Intralaboral por Dominios -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Intralaboral por Dominios</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="intralaboralDominiosChart" width="400"
                                                height="200"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Dominio</th>
                                                            <th>Sin Riesgo</th>
                                                            <th>Bajo</th>
                                                            <th>Medio</th>
                                                            <th>Alto</th>
                                                            <th>Muy Alto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $dominios =
                                                                $resumen['completo']['intralaboral_general'][
                                                                    'dominios'
                                                                ] ?? [];
                                                            $nombresDominios = [
                                                                'demandas_trabajo' => 'Demandas del trabajo',
                                                                'control' => 'Control sobre el trabajo',
                                                                'liderazgo' => 'Liderazgo y relaciones sociales',
                                                                'recompensas' => 'Recompensas',
                                                            ];
                                                        @endphp
                                                        @foreach ($dominios as $dominio => $datos)
                                                            <tr>
                                                                <td><strong>{{ $nombresDominios[$dominio] ?? $dominio }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción de dominios -->
                            <div class="alert alert-info">
                                <h6>Descripción de Dominios:</h6>
                                <p class="mb-1"><strong>1. Demandas del trabajo:</strong> Dominio - Intralaboral</p>
                                <p class="mb-1"><strong>2. Control sobre el trabajo:</strong> Dominio - Intralaboral</p>
                                <p class="mb-1"><strong>3. Liderazgo y relaciones sociales:</strong> Dominio -
                                    Intralaboral</p>
                                <p class="mb-0"><strong>4. Recompensas:</strong> Dominio - Intralaboral</p>
                            </div>

                            <!-- Intralaboral por Dimensiones -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Intralaboral por Dimensiones</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="intralaboralDimensionesChart" width="400"
                                                height="300"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                            $dimensiones =
                                                                $resumen['completo']['intralaboral_general'][
                                                                    'dimensiones'
                                                                ] ?? [];
                                                        @endphp
                                                        @foreach ($dimensiones as $dimension => $datos)
                                                            <tr>
                                                                <td><strong>{{ $datos['nombre'] ?? $dimension }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción de dimensiones -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <h6>Dimensiones con riesgo Muy Alto:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensiones as $dimension => $datos)
                                                @if (($datos['contadores']['muy_alto'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <h6>Dimensiones con riesgo Alto y Medio:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensiones as $dimension => $datos)
                                                @if (($datos['contadores']['alto'] ?? 0) > 0 || ($datos['contadores']['medio'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-success">
                                        <h6>Dimensiones con riesgo Bajo y Sin Riesgo:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensiones as $dimension => $datos)
                                                @if (($datos['contadores']['bajo'] ?? 0) > 0 || ($datos['contadores']['sin_riesgo'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
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
                            <p class="text-muted mb-3">
                                <strong>Población Aplicable:</strong>
                                {{ $resumen['completo']['intralaboral_a']['poblacion'] ?? 0 }} personas aplicaron este test
                            </p>

                            <!-- Intralaboral A por Dominios -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Intralaboral A por Dominios</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="intralaboralADominiosChart" width="400"
                                                height="200"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Dominio</th>
                                                            <th>Sin Riesgo</th>
                                                            <th>Bajo</th>
                                                            <th>Medio</th>
                                                            <th>Alto</th>
                                                            <th>Muy Alto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $dominiosA =
                                                                $resumen['completo']['intralaboral_a']['dominios'] ??
                                                                [];
                                                        @endphp
                                                        @foreach ($dominiosA as $dominio => $datos)
                                                            <tr>
                                                                <td><strong>{{ $nombresDominios[$dominio] ?? $dominio }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción dominios A -->
                            <div class="alert alert-info">
                                <h6>Descripción de Dominios:</h6>
                                <p class="mb-0">Como se puede observar en la anterior gráfica, se detallan los dominios
                                    con sus respectivos porcentajes para la Forma A del cuestionario intralaboral.</p>
                            </div>

                            <!-- Intralaboral A por Dimensiones -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Intralaboral A por Dimensiones</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="intralaboralADimensionesChart" width="400"
                                                height="300"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                            $dimensionesA =
                                                                $resumen['completo']['intralaboral_a']['dimensiones'] ??
                                                                [];
                                                        @endphp
                                                        @foreach ($dimensionesA as $dimension => $datos)
                                                            <tr>
                                                                <td><strong>{{ $datos['nombre'] ?? $dimension }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción dimensiones A -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <h6>Dimensiones con riesgo Muy Alto:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesA as $dimension => $datos)
                                                @if (($datos['contadores']['muy_alto'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <h6>Dimensiones con riesgo Alto y Medio:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesA as $dimension => $datos)
                                                @if (($datos['contadores']['alto'] ?? 0) > 0 || ($datos['contadores']['medio'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-success">
                                        <h6>Dimensiones con riesgo Bajo y Sin Riesgo:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesA as $dimension => $datos)
                                                @if (($datos['contadores']['bajo'] ?? 0) > 0 || ($datos['contadores']['sin_riesgo'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
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
                            <p class="text-muted mb-3">
                                <strong>Población Aplicable:</strong>
                                {{ $resumen['completo']['intralaboral_b']['poblacion'] ?? 0 }} personas aplicaron este test
                            </p>

                            <!-- Intralaboral B por Dominios -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Intralaboral B por Dominios</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="intralaboralBDominiosChart" width="400"
                                                height="200"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Dominio</th>
                                                            <th>Sin Riesgo</th>
                                                            <th>Bajo</th>
                                                            <th>Medio</th>
                                                            <th>Alto</th>
                                                            <th>Muy Alto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $dominiosB =
                                                                $resumen['completo']['intralaboral_b']['dominios'] ??
                                                                [];
                                                        @endphp
                                                        @foreach ($dominiosB as $dominio => $datos)
                                                            <tr>
                                                                <td><strong>{{ $nombresDominios[$dominio] ?? $dominio }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción dominios B -->
                            <div class="alert alert-info">
                                <h6>Descripción de Dominios:</h6>
                                <p class="mb-0">Como se puede observar en la anterior gráfica, se detallan los dominios
                                    con sus respectivos porcentajes para la Forma B del cuestionario intralaboral.</p>
                            </div>

                            <!-- Intralaboral B por Dimensiones -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Intralaboral B por Dimensiones</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="intralaboralBDimensionesChart" width="400"
                                                height="300"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                            $dimensionesB =
                                                                $resumen['completo']['intralaboral_b']['dimensiones'] ??
                                                                [];
                                                        @endphp
                                                        @foreach ($dimensionesB as $dimension => $datos)
                                                            <tr>
                                                                <td><strong>{{ $datos['nombre'] ?? $dimension }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción dimensiones B -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <h6>Dimensiones con riesgo Muy Alto:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesB as $dimension => $datos)
                                                @if (($datos['contadores']['muy_alto'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <h6>Dimensiones con riesgo Alto y Medio:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesB as $dimension => $datos)
                                                @if (($datos['contadores']['alto'] ?? 0) > 0 || ($datos['contadores']['medio'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-success">
                                        <h6>Dimensiones con riesgo Bajo y Sin Riesgo:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesB as $dimension => $datos)
                                                @if (($datos['contadores']['bajo'] ?? 0) > 0 || ($datos['contadores']['sin_riesgo'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
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
                            <p class="text-muted mb-3">
                                <strong>Población Aplicable:</strong>
                                {{ $resumen['completo']['extralaboral']['poblacion'] ?? 0 }} personas aplicaron este test
                            </p>

                            <!-- Extralaboral por Dimensiones -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Extralaboral por Dimensiones</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="extralaboralDimensionesChart" width="400"
                                                height="300"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                            $dimensionesExtra =
                                                                $resumen['completo']['extralaboral']['dimensiones'] ??
                                                                [];
                                                        @endphp
                                                        @foreach ($dimensionesExtra as $dimension => $datos)
                                                            <tr>
                                                                <td><strong>{{ $datos['nombre'] ?? $dimension }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción dimensiones extralaborales -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <h6>Dimensiones con riesgo Muy Alto:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesExtra as $dimension => $datos)
                                                @if (($datos['contadores']['muy_alto'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <h6>Dimensiones con riesgo Alto y Medio:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesExtra as $dimension => $datos)
                                                @if (($datos['contadores']['alto'] ?? 0) > 0 || ($datos['contadores']['medio'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-success">
                                        <h6>Dimensiones con riesgo Bajo y Sin Riesgo:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesExtra as $dimension => $datos)
                                                @if (($datos['contadores']['bajo'] ?? 0) > 0 || ($datos['contadores']['sin_riesgo'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
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
                                        <i class="fas fa-heart me-2"></i>Resumen Estrés
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
                            <p class="text-muted mb-3">
                                <strong>Población Aplicable:</strong>
                                {{ $resumen['completo']['estres']['poblacion'] ?? 0 }} personas aplicaron este test
                            </p>

                            <!-- Estrés por Dimensiones -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Estrés por Dimensiones</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="estresDimensionesChart" width="400" height="300"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
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
                                                            $dimensionesEstres =
                                                                $resumen['completo']['estres']['dimensiones'] ?? [];
                                                        @endphp
                                                        @foreach ($dimensionesEstres as $dimension => $datos)
                                                            <tr>
                                                                <td><strong>{{ $datos['nombre'] ?? $dimension }}</strong>
                                                                </td>
                                                                <td>{{ $datos['contadores']['sin_riesgo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['bajo'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['medio'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['alto'] ?? 0 }}</td>
                                                                <td>{{ $datos['contadores']['muy_alto'] ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción dimensiones de estrés -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <h6>Dimensiones con riesgo Muy Alto:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesEstres as $dimension => $datos)
                                                @if (($datos['contadores']['muy_alto'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <h6>Dimensiones con riesgo Alto y Medio:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesEstres as $dimension => $datos)
                                                @if (($datos['contadores']['alto'] ?? 0) > 0 || ($datos['contadores']['medio'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-success">
                                        <h6>Dimensiones con riesgo Bajo y Sin Riesgo:</h6>
                                        <ul class="mb-0">
                                            @foreach ($dimensionesEstres as $dimension => $datos)
                                                @if (($datos['contadores']['bajo'] ?? 0) > 0 || ($datos['contadores']['sin_riesgo'] ?? 0) > 0)
                                                    <li>{{ $datos['nombre'] ?? $dimension }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>
@endsection

@push('scripts')
    <style>
        :root {
            --risk-sin-riesgo: #008235;
            --risk-bajo: #00D364;
            --risk-medio: #FFD600;
            --risk-alto: #DD0505;
            --risk-muy-alto: #A30203;
        }

        .legend-container {
            padding: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .legend-label {
            flex-grow: 1;
            font-weight: 500;
        }

        .legend-value {
            font-weight: bold;
            color: #333;
        }

        canvas {
            max-height: 300px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script>
        // Registrar el plugin datalabels globalmente
        Chart.register(ChartDataLabels);

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Loaded - Iniciando resumen');

            // Datos del resumen
            const resumen = @json($resumen);
            console.log('Resumen completo:', resumen);
            console.log('Distribución riesgo:', resumen?.completo?.distribucion_riesgo);

            // Colores estandarizados GIR-365
            const colores = {
                'sin_riesgo': '#008235',
                'bajo': '#00D364',
                'medio': '#FFD600',
                'alto': '#DA3D3D',
                'muy_alto': '#D10000'
            };

            // Gráfico de Distribución de Riesgo
            if (resumen.completo && resumen.completo.distribucion_riesgo) {
                console.log('Intentando crear gráfico de distribución de riesgo');
                const distribucionData = resumen.completo.distribucion_riesgo.niveles;
                console.log('Datos distribución:', distribucionData);

                const canvas = document.getElementById('distribucionRiesgoChart');
                console.log('Canvas encontrado:', canvas);

                if (canvas) {
                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Sin Riesgo', 'Bajo', 'Medio', 'Alto', 'Muy Alto'],
                            datasets: [{
                                label: 'Porcentaje',
                                data: [
                                    distribucionData.sin_riesgo?.porcentaje || 0,
                                    distribucionData.bajo?.porcentaje || 0,
                                    distribucionData.medio?.porcentaje || 0,
                                    distribucionData.alto?.porcentaje || 0,
                                    distribucionData.muy_alto?.porcentaje || 0
                                ],
                                backgroundColor: [
                                    colores.sin_riesgo,
                                    colores.bajo,
                                    colores.medio,
                                    colores.alto,
                                    colores.muy_alto
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
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
                        }
                    });
                }
            }

            // Gráficos sociodemográficos - BARRAS VERTICALES
            if (resumen.completo && resumen.completo.datos_sociodemograficos) {
                console.log('Datos sociodemograficos:', resumen.completo.datos_sociodemograficos);
                const datosSociodemograficos = resumen.completo.datos_sociodemograficos;

                Object.keys(datosSociodemograficos).forEach(categoria => {
                    const canvas = document.getElementById(`chart_${categoria}`);
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        const datos = datosSociodemograficos[categoria];

                        if (datos && Object.keys(datos).length > 0) {
                            const labels = Object.keys(datos);
                            const values = Object.values(datos).map(item => item.cantidad || 0);
                            const porcentajes = Object.values(datos).map(item => item.porcentaje || 0);

                            new Chart(ctx, {
                                type: 'bar', // Cambiado de 'doughnut' a 'bar'
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Cantidad',
                                        data: values,
                                        backgroundColor: [
                                            '#1E3A8A', // Azul corporativo oscuro
                                            '#3B82F6', // Azul corporativo
                                            '#10B981', // Verde corporativo
                                            '#059669', // Verde oscuro
                                            '#F59E0B', // Naranja/Ámbar
                                            '#DC2626', // Rojo corporativo
                                            '#7C3AED', // Morado corporativo
                                            '#EC4899', // Rosa corporativo
                                            '#14B8A6', // Turquesa
                                            '#6366F1', // Índigo
                                            '#8B5CF6', // Violeta
                                            '#06B6D4', // Cian
                                            '#84CC16', // Lima
                                            '#EAB308' // Amarillo
                                        ],
                                        borderColor: [
                                            '#1E3A8A',
                                            '#3B82F6',
                                            '#10B981',
                                            '#059669',
                                            '#F59E0B',
                                            '#DC2626',
                                            '#7C3AED',
                                            '#EC4899',
                                            '#14B8A6',
                                            '#6366F1',
                                            '#8B5CF6',
                                            '#06B6D4',
                                            '#84CC16',
                                            '#EAB308'
                                        ],
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        datalabels: {
                                            anchor: 'end',
                                            align: 'end',
                                            formatter: function(value, context) {
                                                const porcentaje = porcentajes[context
                                                    .dataIndex];
                                                return value + ' (' + porcentaje.toFixed(1) +
                                                    '%)';
                                            },
                                            font: {
                                                weight: 'bold',
                                                size: 11
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            },
                                            title: {
                                                display: true,
                                                text: 'Cantidad de personas'
                                            }
                                        },
                                        x: {
                                            ticks: {
                                                autoSkip: false,
                                                maxRotation: 45,
                                                minRotation: 0
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            console.log('No hay datos para:', categoria);
                        }
                    } else {
                        console.log('Canvas no encontrado para:', categoria);
                    }
                });
            } else {
                console.log('No hay datos sociodemograficos en resumen.completo');
            }

            // Gráfico Total General
            if (resumen.completo && resumen.completo.total_psicosocial) {
                const totalData = resumen.completo.total_psicosocial.por_instrumento;
                const totalCanvas = document.getElementById('totalGeneralChart');
                if (totalCanvas) {
                    const ctx = totalCanvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Intralaboral', 'Extralaboral', 'Estrés'],
                            datasets: [{
                                    label: 'Sin Riesgo',
                                    data: [
                                        totalData.intralaboral?.sin_riesgo || 0,
                                        totalData.extralaboral?.sin_riesgo || 0,
                                        totalData.estres?.sin_riesgo || 0
                                    ],
                                    backgroundColor: colores.sin_riesgo
                                },
                                {
                                    label: 'Bajo',
                                    data: [
                                        totalData.intralaboral?.bajo || 0,
                                        totalData.extralaboral?.bajo || 0,
                                        totalData.estres?.bajo || 0
                                    ],
                                    backgroundColor: colores.bajo
                                },
                                {
                                    label: 'Medio',
                                    data: [
                                        totalData.intralaboral?.medio || 0,
                                        totalData.extralaboral?.medio || 0,
                                        totalData.estres?.medio || 0
                                    ],
                                    backgroundColor: colores.medio
                                },
                                {
                                    label: 'Alto',
                                    data: [
                                        totalData.intralaboral?.alto || 0,
                                        totalData.extralaboral?.alto || 0,
                                        totalData.estres?.alto || 0
                                    ],
                                    backgroundColor: colores.alto
                                },
                                {
                                    label: 'Muy Alto',
                                    data: [
                                        totalData.intralaboral?.muy_alto || 0,
                                        totalData.extralaboral?.muy_alto || 0,
                                        totalData.estres?.muy_alto || 0
                                    ],
                                    backgroundColor: colores.muy_alto
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }
        });

        function mostrarTodo() {
            // Limpiar todos los filtros
            document.querySelectorAll('#filtrosForm select').forEach(select => {
                select.selectedIndex = 0;
            });
            // Enviar formulario
            document.getElementById('filtrosForm').submit();
        }

        // Función para imprimir sección específica
        function imprimirSeccion(seccionId) {
            const seccion = document.getElementById(seccionId);
            if (seccion) {
                const ventanaImpresion = window.open('', '_blank');
                ventanaImpresion.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Resumen Psicosocial - Sección ${seccionId}</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                .card { border: 1px solid #ddd; margin-bottom: 20px; }
                                .card-header { background-color: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; }
                                .card-body { padding: 15px; }
                                .table { width: 100%; border-collapse: collapse; }
                                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                .table th { background-color: #f2f2f2; }
                                .alert { padding: 15px; margin: 10px 0; border-radius: 4px; }
                                .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
                                .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; }
                                .alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; }
                                .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; }
                                .row { display: flex; flex-wrap: wrap; }
                                .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
                                .col-md-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
                                .col-12 { flex: 0 0 100%; max-width: 100%; }
                            </style>
                        </head>
                        <body>
                        <h1>Resumen Psicosocial - ${seccionId}</h1>
                        ${seccion.innerHTML}
                        <script>
                            window.onload = function() {
                                window.print();
                            };
    <\/script>
    </body>

    </html>
    `);
                ventanaImpresion.document.close();
            }
        }

        // Función para exportar datos a CSV
        function exportarCSV() {
            const csv = [];
            const cabeceras = ['Sección', 'Tipo', 'Sin Riesgo', 'Bajo', 'Medio', 'Alto', 'Muy Alto'];
            csv.push(cabeceras.join(','));

            // Obtener datos de las tablas
            const tablas = document.querySelectorAll('.table tbody');
            tablas.forEach(tabla => {
                const filas = tabla.querySelectorAll('tr');
                filas.forEach(fila => {
                    const celdas = fila.querySelectorAll('td');
                    if (celdas.length > 0) {
                        const fila_datos = Array.from(celdas).map(celda => celda.textContent.trim());
                        csv.push(fila_datos.join(','));
                    }
                });
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'resumen_psicosocial.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Función para generar PDF completo
        function generarPDF() {
            // Implementar lógica para generar PDF
            alert('Función de generación de PDF en desarrollo');
        }

        // Hacer funciones globales
        window.imprimirSeccion = imprimirSeccion;
        window.exportarCSV = exportarCSV;
        window.generarPDF = generarPDF;
    </script>
@endpush
