@extends('layouts.dashboard')

@section('title', 'Resumen de Diagnóstico - ' . $diagnostico->descripcion)

@section('styles')
    <style>
        /* Variables de colores GIR-365 estandarizados */
        :root {
            --risk-sin-riesgo: #008235;
            --risk-bajo: #00D364;
            --risk-medio: #FFD600;
            --risk-alto: #DA3D3D;
            --risk-muy-alto: #D10000;
        }

        /* Clases de colores para niveles de riesgo */
        .risk-sin-riesgo {
            background-color: var(--risk-sin-riesgo) !important;
            color: white !important;
        }

        .risk-bajo {
            background-color: var(--risk-bajo) !important;
            color: white !important;
        }

        .risk-medio {
            background-color: var(--risk-medio) !important;
            color: black !important;
        }

        .risk-alto {
            background-color: var(--risk-alto) !important;
            color: white !important;
        }

        .risk-muy-alto {
            background-color: var(--risk-muy-alto) !important;
            color: white !important;
        }

        /* Estilos para gráficas 3D */
        .chart-container-3d {
            height: 300px;
            max-height: 300px;
            width: 100%;
            margin: 0 auto;
        }

        .chart-container-standard {
            height: 250px;
            max-height: 250px;
            width: 100%;
        }

        /* Filtros */
        .filter-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Cards de secciones */
        .section-card {
            margin-bottom: 30px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin: 0;
        }

        /* Estadísticas principales */
        .stat-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        /* Tablas modernas */
        .modern-table {
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .modern-table th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }

        .modern-table td {
            border: none;
            padding: 12px 15px;
            vertical-align: middle;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .chart-container-3d,
            .chart-container-standard {
                height: 200px;
                max-height: 200px;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-2">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Resumen de Diagnóstico Testing- {{ $diagnostico->descripcion }}
                                </h4>
                                <p class="text-muted mb-0">
                                    Análisis completo de resultados de la batería psicosocial
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('psicosocial.show', $diagnostico->id) }}"
                                    class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                                <button type="button" class="btn btn-primary" onclick="exportarPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CUADRO DE FILTROS -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-panel">
                    <h5 class="mb-3">
                        <i class="fas fa-filter me-2"></i>
                        Filtros del Informe Resumen Psicosocial
                    </h5>
                    <form id="filtrosForm" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="filtro_area" class="form-label">Área</label>
                                <select name="area" id="filtro_area" class="form-select">
                                    <option value="">Todas las áreas</option>
                                    @if (isset($opciones_filtros['areas']))
                                        @foreach ($opciones_filtros['areas'] as $area)
                                            <option value="{{ $area }}"
                                                {{ request('area') == $area ? 'selected' : '' }}>
                                                {{ $area }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_sede" class="form-label">Sede</label>
                                <select name="sede" id="filtro_sede" class="form-select">
                                    <option value="">Todas las sedes</option>
                                    @if (isset($opciones_filtros['sedes']))
                                        @foreach ($opciones_filtros['sedes'] as $sede)
                                            <option value="{{ $sede }}"
                                                {{ request('sede') == $sede ? 'selected' : '' }}>
                                                {{ $sede }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_ciudad" class="form-label">Ciudad</label>
                                <select name="ciudad" id="filtro_ciudad" class="form-select">
                                    <option value="">Todas las ciudades</option>
                                    @if (isset($opciones_filtros['ciudades']))
                                        @foreach ($opciones_filtros['ciudades'] as $ciudad)
                                            <option value="{{ $ciudad }}"
                                                {{ request('ciudad') == $ciudad ? 'selected' : '' }}>
                                                {{ $ciudad }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_contrato" class="form-label">Tipo Contrato</label>
                                <select name="tipo_contrato" id="filtro_contrato" class="form-select">
                                    <option value="">Todos los contratos</option>
                                    @if (isset($opciones_filtros['tipos_contrato']))
                                        @foreach ($opciones_filtros['tipos_contrato'] as $contrato)
                                            <option value="{{ $contrato }}"
                                                {{ request('tipo_contrato') == $contrato ? 'selected' : '' }}>
                                                {{ $contrato }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_proceso" class="form-label">Proceso</label>
                                <select name="proceso" id="filtro_proceso" class="form-select">
                                    <option value="">Todos los procesos</option>
                                    @if (isset($opciones_filtros['procesos']))
                                        @foreach ($opciones_filtros['procesos'] as $proceso)
                                            <option value="{{ $proceso }}"
                                                {{ request('proceso') == $proceso ? 'selected' : '' }}>
                                                {{ $proceso }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_forma" class="form-label">Forma de Evaluación</label>
                                <select name="forma" id="filtro_forma" class="form-select">
                                    <option value="">Todas las formas</option>
                                    <option value="A" {{ request('forma') == 'A' ? 'selected' : '' }}>Forma A</option>
                                    <option value="B" {{ request('forma') == 'B' ? 'selected' : '' }}>Forma B</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-2"></i>Aplicar Filtros
                                </button>
                                <a href="{{ route('psicosocial.resumen', $diagnostico->id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 1: ESTADÍSTICAS GENERALES -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            1. Estadísticas Generales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card stat-card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h3 class="mb-0">{{ $resumen['total_evaluaciones'] ?? 0 }}</h3>
                                        <p class="mb-0">Total Evaluaciones</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card stat-card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h3 class="mb-0">{{ $resumen['completadas'] ?? 0 }}</h3>
                                        <p class="mb-0">Completadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card stat-card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h3 class="mb-0">{{ $resumen['pendientes'] ?? 0 }}</h3>
                                        <p class="mb-0">Pendientes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: DISTRIBUCIÓN DE NIVELES DE RIESGO -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            2. Distribución de Niveles de Riesgo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-container-3d">
                                    <canvas id="distribucionRiesgoChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th>Nivel de Riesgo</th>
                                                <th>Cantidad</th>
                                                <th>%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($resumen['completo']['distribucion_riesgo']['niveles']))
                                                @foreach ($resumen['completo']['distribucion_riesgo']['niveles'] as $nivel => $datos)
                                                    <tr>
                                                        <td>
                                                            <span class="badge risk-{{ $nivel }}">
                                                                {{ ucfirst(str_replace('_', ' ', $nivel)) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $datos['cantidad'] ?? 0 }}</td>
                                                        <td>{{ number_format($datos['porcentaje'] ?? 0, 1) }}%</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: DATOS SOCIODEMOGRÁFICOS -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            3. Datos Sociodemográficos Generales
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">A continuación, se muestran las gráficas correspondientes a los datos
                            sociodemográficos de cada empleado que presentó la prueba:</p>

                        <div class="row" id="sociodemograficos-container">
                            <!-- Se llenará dinámicamente con JavaScript -->
                            <div class="row">
                                {{-- Gráficas de datos sociodemográficos --}}
                                @foreach (['genero', 'edad', 'estado_civil', 'tipo_vivienda', 'estrato', 'tipo_cargo'] as $campo)
                                    <div class="col-lg-6 mb-4">
                                        <div class="card border-light">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $campo)) }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="chart{{ ucfirst($campo) }}" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                @foreach (['escolaridad', 'antiguedad_empresa', 'antiguedad_cargo', 'tipo_salario', 'tipo_contrato', 'dependientes_economicos', 'horas_diarias'] as $campo)
                                    <div class="col-lg-6 mb-4">
                                        <div class="card border-light">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $campo)) }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="chart{{ ucfirst($campo) }}" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 4: TOTAL GENERAL PRUEBA PSICOSOCIAL -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-brain me-2"></i>
                            4. Total General Prueba Psicosocial
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-container-3d">
                                    <canvas id="totalPsicosocialChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table modern-table">
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
                                        <tbody id="totalPsicosocialTable">
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción Total -->
                        <div class="mt-4">
                            <h6>Descripción Total</h6>
                            <p>Como se puede observar en la anterior gráfica, se detallan los totales con sus respectivos
                                porcentajes a continuación:</p>
                            <div id="descripcionTotal">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 5: RESUMEN INTRALABORAL GENERAL -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            5. Resumen Prueba Intralaboral
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['intralaboral_general']['poblacion'] ?? 0 }} personas que aplicaron el
                            test.
                        </p>

                        <!-- Dominios -->
                        <h6 class="mb-3">Intralaboral por Dominios</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-container-3d">
                                    <canvas id="intralaboralDominiosChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th>Dominio</th>
                                                <th class="text-center">Distribución</th>
                                            </tr>
                                        </thead>
                                        <tbody id="intralaboralDominiosTable">
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Dimensiones -->
                        <h6 class="mb-3 mt-4">Intralaboral por Dimensiones</h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th>Dominio</th>
                                                <th>Dimensión</th>
                                                <th class="text-center">Sin Riesgo</th>
                                                <th class="text-center">Bajo</th>
                                                <th class="text-center">Medio</th>
                                                <th class="text-center">Alto</th>
                                                <th class="text-center">Muy Alto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="intralaboralDimensionesTable">
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción de dimensiones -->
                        <div class="mt-4" id="descripcionDimensiones">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 6: RESUMEN INTRALABORAL A -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            6. Resumen Prueba Intralaboral A
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['intralaboral_a']['poblacion'] ?? 0 }} personas que aplicaron el test.
                        </p>
                        <div id="intralaboralAContent">
                            <!-- Se llenará dinámicamente con datos de Forma A -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 7: RESUMEN INTRALABORAL B -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-cog me-2"></i>
                            7. Resumen Prueba Intralaboral B
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['intralaboral_b']['poblacion'] ?? 0 }} personas que aplicaron el test.
                        </p>
                        <div id="intralaboralBContent">
                            <!-- Se llenará dinámicamente con datos de Forma B -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 8: RESUMEN EXTRALABORAL -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-home me-2"></i>
                            8. Resumen Prueba Extralaboral
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Población Aplicable:</strong>
                            {{ $resumen['completo']['extralaboral']['poblacion'] ?? 0 }} personas que aplicaron el test.
                        </p>
                        <div id="extralaboralContent">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 9: RESUMEN ESTRÉS -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-heartbeat me-2"></i>
                            9. Resumen Prueba Estrés
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Población Aplicable:</strong> {{ $resumen['completo']['estres']['poblacion'] ?? 0 }}
                            personas que aplicaron el test.
                        </p>
                        <div id="estresContent">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Generando gráficas...</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-3d"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos del resumen desde el backend
            const resumenData = {!! json_encode($resumen['completo'] ?? []) !!};

            // Configuración de colores GIR-365
            const coloresGIR365 = {
                'sin_riesgo': '#008235',
                'bajo': '#00D364',
                'medio': '#FFD600',
                'alto': '#DA3D3D',
                'muy_alto': '#D10000'
            };

            // Inicializar todas las gráficas
            inicializarGraficas();

            function inicializarGraficas() {
                // Mostrar loading
                document.getElementById('loadingOverlay').style.display = 'flex';

                try {
                    // Gráfica de distribución de riesgo
                    crearGraficaDistribucionRiesgo();

                    // Gráficas sociodemográficas
                    crearGraficasSociodemograficas();

                    // Total psicosocial
                    crearGraficaTotalPsicosocial();

                    // Intralaboral dominios
                    crearGraficaIntralaboralDominios();

                    // Ocultar loading
                    setTimeout(() => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                    }, 500);

                } catch (error) {
                    console.error('Error inicializando gráficas:', error);
                    document.getElementById('loadingOverlay').style.display = 'none';
                }
            }

            function crearGraficaDistribucionRiesgo() {
                const ctx = document.getElementById('distribucionRiesgoChart');
                if (!ctx) return;

                const distribucion = resumenData.distribucion_riesgo?.niveles || {};
                const labels = Object.keys(distribucion).map(nivel =>
                    nivel.replace('_', ' ').toUpperCase()
                );
                const data = Object.values(distribucion).map(item => item.cantidad || 0);
                const colors = Object.keys(distribucion).map(nivel => coloresGIR365[nivel] || '#cccccc');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Empleados',
                            data: data,
                            backgroundColor: colors,
                            borderColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Distribución de Niveles de Riesgo'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            function crearGraficasSociodemograficas() {
                const container = document.getElementById('sociodemograficos-container');
                if (!container) return;

                const sociodemograficos = resumenData.datos_sociodemograficos || {};
                const categorias = [{
                        key: 'genero',
                        title: 'Género'
                    },
                    {
                        key: 'edad',
                        title: 'Edad'
                    },
                    {
                        key: 'estado_civil',
                        title: 'Estado Civil'
                    },
                    {
                        key: 'tipo_vivienda',
                        title: 'Tipo de Vivienda'
                    },
                    {
                        key: 'estrato_social',
                        title: 'Tipo de Estrato'
                    },
                    {
                        key: 'cargo',
                        title: 'Tipo de Cargo'
                    },
                    {
                        key: 'nivel_estudios',
                        title: 'Escolaridad - Nivel de Estudios'
                    },
                    {
                        key: 'antiguedad_empresa',
                        title: 'Antigüedad en la Empresa'
                    },
                    {
                        key: 'antiguedad_cargo',
                        title: 'Antigüedad en el Cargo'
                    },
                    {
                        key: 'tipo_salario',
                        title: 'Tipo de Salario'
                    },
                    {
                        key: 'tipo_contrato',
                        title: 'Tipo de Contrato'
                    },
                    {
                        key: 'dependientes_economicos',
                        title: 'Dependientes Económicos'
                    },
                    {
                        key: 'horas_laboradas',
                        title: 'Horas Diarias Laboradas'
                    }
                ];

                container.innerHTML = '';

                categorias.forEach(categoria => {
                    if (sociodemograficos[categoria.key] && Object.keys(sociodemograficos[categoria.key])
                        .length > 0) {
                        const colDiv = document.createElement('div');
                        colDiv.className = 'col-md-6 mb-4';

                        colDiv.innerHTML = `
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">${categoria.title}</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-standard">
                                <canvas id="chart_${categoria.key}"></canvas>
                            </div>
                        </div>
                    </div>
                `;

                        container.appendChild(colDiv);

                        // Crear gráfica después de que el elemento esté en el DOM
                        setTimeout(() => {
                            crearGraficaSociodemografica(categoria.key, categoria.title,
                                sociodemograficos[categoria.key]);
                        }, 100);
                    }
                });
            }

            function crearGraficaSociodemografica(key, title, data) {
                const ctx = document.getElementById(`chart_${key}`);
                if (!ctx) return;

                const labels = Object.keys(data);
                const valores = Object.values(data).map(item => item.cantidad || 0);
                const colores = labels.map((_, index) =>
                    `hsl(${(index * 360 / labels.length)}, 70%, 50%)`
                );

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: valores,
                            backgroundColor: colores,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }

            function crearGraficaTotalPsicosocial() {
                const ctx = document.getElementById('totalPsicosocialChart');
                const tableBody = document.getElementById('totalPsicosocialTable');

                if (!ctx || !tableBody) return;

                const totalPsicosocial = resumenData.total_psicosocial?.por_instrumento || {};

                // Preparar datos para la gráfica
                const instrumentos = ['intralaboral', 'extralaboral', 'estres'];
                const nivelesRiesgo = ['sin_riesgo', 'bajo', 'medio', 'alto', 'muy_alto'];

                const datasets = nivelesRiesgo.map(nivel => ({
                    label: nivel.replace('_', ' ').toUpperCase(),
                    data: instrumentos.map(inst => totalPsicosocial[inst]?.[nivel] || 0),
                    backgroundColor: coloresGIR365[nivel],
                    borderColor: coloresGIR365[nivel],
                    borderWidth: 1
                }));

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Intralaboral', 'Extralaboral', 'Estrés'],
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
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

                // Llenar tabla
                tableBody.innerHTML = '';
                instrumentos.forEach(instrumento => {
                    const datos = totalPsicosocial[instrumento] || {};
                    const total = Object.values(datos).reduce((sum, val) => sum + (val || 0), 0);

                    const row = `
                <tr>
                    <td><strong>${instrumento.charAt(0).toUpperCase() + instrumento.slice(1)}</strong></td>
                    <td class="text-center">${((datos.sin_riesgo || 0) / total * 100).toFixed(1)}% (${datos.sin_riesgo || 0})</td>
                    <td class="text-center">${((datos.bajo || 0) / total * 100).toFixed(1)}% (${datos.bajo || 0})</td>
                    <td class="text-center">${((datos.medio || 0) / total * 100).toFixed(1)}% (${datos.medio || 0})</td>
                    <td class="text-center">${((datos.alto || 0) / total * 100).toFixed(1)}% (${datos.alto || 0})</td>
                    <td class="text-center">${((datos.muy_alto || 0) / total * 100).toFixed(1)}% (${datos.muy_alto || 0})</td>
                </tr>
            `;
                    tableBody.innerHTML += row;
                });
            }

            function crearGraficaIntralaboralDominios() {
                const ctx = document.getElementById('intralaboralDominiosChart');
                const tableBody = document.getElementById('intralaboralDominiosTable');

                if (!ctx || !tableBody) return;

                const dominios = resumenData.intralaboral_general?.dominios || {};

                // Preparar datos para gráfica
                const nombresDominios = Object.keys(dominios);
                const datasets = ['sin_riesgo', 'bajo', 'medio', 'alto', 'muy_alto'].map(nivel => ({
                    label: nivel.replace('_', ' ').toUpperCase(),
                    data: nombresDominios.map(dom => dominios[dom]?.contadores?.[nivel] || 0),
                    backgroundColor: coloresGIR365[nivel],
                    borderColor: coloresGIR365[nivel],
                    borderWidth: 1
                }));

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: nombresDominios.map(dom =>
                            dom.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())
                        ),
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
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

                // Llenar tabla de dominios
                tableBody.innerHTML = '';
                Object.entries(dominios).forEach(([dominio, datos]) => {
                    const contadores = datos.contadores || {};
                    const total = datos.total || 0;

                    let distribucionHtml = '';
                    Object.entries(contadores).forEach(([nivel, cantidad]) => {
                        if (cantidad > 0) {
                            const porcentaje = total > 0 ? (cantidad / total * 100).toFixed(1) : 0;
                            distribucionHtml += `
                        <span class="badge risk-${nivel} me-1">
                            ${porcentaje}% (${cantidad})
                        </span>
                    `;
                        }
                    });

                    const row = `
                <tr>
                    <td>${datos.nombre || dominio}</td>
                    <td>${distribucionHtml}</td>
                </tr>
            `;
                    tableBody.innerHTML += row;
                });
            }
        });

        // Función para exportar PDF
        function exportarPDF() {
            const url =
                `{{ route('psicosocial.exportar-pdf', $diagnostico->id) }}?${new URLSearchParams(window.location.search).toString()}`;
            window.open(url, '_blank');
        }

        // Auto-submit del formulario cuando cambian los filtros
        document.getElementById('filtrosForm').addEventListener('change', function() {
            // Optional: Auto-submit when filters change
            // this.submit();
        });
    </script>
@endsection
