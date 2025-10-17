@extends('layouts.dashboard')

@section('titulo', 'Estadísticas Psicosociales')

@section('styles')
    <style>
        .nivel-riesgo {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            min-width: 100px;
            text-align: center;
        }

        .nivel-sin_riesgo {
            background-color: #008235;
            color: white;
        }

        .nivel-bajo {
            background-color: #00D364;
            color: white;
        }

        .nivel-medio {
            background-color: #FFD600;
            color: black;
        }

        .nivel-alto {
            background-color: #DA3D3D;
            color: white;
        }

        .nivel-muy_alto {
            background-color: #D10000;
            color: white;
        }

        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
            margin-bottom: 20px;
        }

        .domain-card {
            margin-bottom: 20px;
            border-radius: 6px;
            overflow: hidden;
        }

        .domain-card .card-header {
            font-weight: bold;
            border-bottom: 2px solid rgba(0, 0, 0, 0.125);
        }

        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
    </style>
@endsection

@section('contenido')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Estadísticas de Evaluaciones Psicosociales</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('psicosocial.index') }}">Psicosocial</a></li>
            <li class="breadcrumb-item active">Estadísticas</li>
        </ol>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-section">
                    <form action="{{ route('psicosocial.estadisticas') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="diagnostico" class="form-label">Diagnóstico</label>
                            <select name="diagnostico" id="diagnostico" class="form-select">
                                <option value="">Todos los diagnósticos</option>
                                <!-- Aquí se cargarían los diagnósticos disponibles -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="empresa" class="form-label">Empresa</label>
                            <select name="empresa" id="empresa" class="form-select" {{ $isSuperAdmin ? '' : 'disabled' }}>
                                <option value="">Todas las empresas</option>
                                <!-- Aquí se cargarían las empresas disponibles si es superadmin -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                        </div>
                        <div class="col-md-2">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-1"></i>
                        Distribución General de Niveles de Riesgo
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-container">
                                    <canvas id="generalRiskChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-center mb-3">Resumen General</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nivel</th>
                                                <th>Cantidad</th>
                                                <th>Porcentaje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalGeneralRiesgo = array_sum([
                                                    $distribucionRiesgoPorDominio['liderazgoRelacionesSociales'][
                                                        'sin_riesgo'
                                                    ] ?? 0,
                                                    $distribucionRiesgoPorDominio['liderazgoRelacionesSociales'][
                                                        'bajo'
                                                    ] ?? 0,
                                                    $distribucionRiesgoPorDominio['liderazgoRelacionesSociales'][
                                                        'medio'
                                                    ] ?? 0,
                                                    $distribucionRiesgoPorDominio['liderazgoRelacionesSociales'][
                                                        'alto'
                                                    ] ?? 0,
                                                    $distribucionRiesgoPorDominio['liderazgoRelacionesSociales'][
                                                        'muy_alto'
                                                    ] ?? 0,
                                                ]);
                                            @endphp
                                            <tr>
                                                <td><span class="nivel-riesgo nivel-sin_riesgo">Sin Riesgo</span></td>
                                                <td>{{ $distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['sin_riesgo'] ?? 0 }}
                                                </td>
                                                <td>{{ $totalGeneralRiesgo > 0 ? round((($distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['sin_riesgo'] ?? 0) / $totalGeneralRiesgo) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="nivel-riesgo nivel-bajo">Bajo</span></td>
                                                <td>{{ $distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['bajo'] ?? 0 }}
                                                </td>
                                                <td>{{ $totalGeneralRiesgo > 0 ? round((($distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['bajo'] ?? 0) / $totalGeneralRiesgo) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="nivel-riesgo nivel-medio">Medio</span></td>
                                                <td>{{ $distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['medio'] ?? 0 }}
                                                </td>
                                                <td>{{ $totalGeneralRiesgo > 0 ? round((($distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['medio'] ?? 0) / $totalGeneralRiesgo) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="nivel-riesgo nivel-alto">Alto</span></td>
                                                <td>{{ $distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['alto'] ?? 0 }}
                                                </td>
                                                <td>{{ $totalGeneralRiesgo > 0 ? round((($distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['alto'] ?? 0) / $totalGeneralRiesgo) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="nivel-riesgo nivel-muy_alto">Muy Alto</span></td>
                                                <td>{{ $distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['muy_alto'] ?? 0 }}
                                                </td>
                                                <td>{{ $totalGeneralRiesgo > 0 ? round((($distribucionRiesgoPorDominio['liderazgoRelacionesSociales']['muy_alto'] ?? 0) / $totalGeneralRiesgo) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <p><strong>Total Diagnósticos:</strong> {{ $totalDiagnosticos }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas por Dominio -->
        <h2 class="mt-5 mb-4">Estadísticas por Dominio</h2>
        <div class="row">
            <!-- Dominio Liderazgo y Relaciones Sociales -->
            <div class="col-md-6">
                <div class="card domain-card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-users me-1"></i>
                        Liderazgo y Relaciones Sociales
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="liderazgoChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <p><strong>Total evaluaciones:</strong>
                                {{ $hojasPorDominio['liderazgoRelacionesSociales'] ?? 0 }}</p>
                            <p><strong>Nivel de riesgo predominante:</strong>
                                @php
                                    $niveles = $distribucionRiesgoPorDominio['liderazgoRelacionesSociales'] ?? [];
                                    $nivelPredominate = !empty($niveles)
                                        ? array_search(max($niveles), $niveles)
                                        : 'N/A';
                                @endphp
                                <span class="nivel-riesgo nivel-{{ $nivelPredominate }}">
                                    {{ ucfirst(str_replace('_', ' ', $nivelPredominate)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dominio Control sobre el Trabajo -->
            <div class="col-md-6">
                <div class="card domain-card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-tasks me-1"></i>
                        Control sobre el Trabajo
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="controlChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <p><strong>Total evaluaciones:</strong> {{ $hojasPorDominio['controlSobreElTrabajo'] ?? 0 }}
                            </p>
                            <p><strong>Nivel de riesgo predominante:</strong>
                                @php
                                    $niveles = $distribucionRiesgoPorDominio['controlSobreElTrabajo'] ?? [];
                                    $nivelPredominate = !empty($niveles)
                                        ? array_search(max($niveles), $niveles)
                                        : 'N/A';
                                @endphp
                                <span class="nivel-riesgo nivel-{{ $nivelPredominate }}">
                                    {{ ucfirst(str_replace('_', ' ', $nivelPredominate)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dominio Demandas del Trabajo -->
            <div class="col-md-6 mt-4">
                <div class="card domain-card">
                    <div class="card-header bg-warning">
                        <i class="fas fa-briefcase me-1"></i>
                        Demandas del Trabajo
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="demandasChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <p><strong>Total evaluaciones:</strong> {{ $hojasPorDominio['demandasDelTrabajo'] ?? 0 }}</p>
                            <p><strong>Nivel de riesgo predominante:</strong>
                                @php
                                    $niveles = $distribucionRiesgoPorDominio['demandasDelTrabajo'] ?? [];
                                    $nivelPredominate = !empty($niveles)
                                        ? array_search(max($niveles), $niveles)
                                        : 'N/A';
                                @endphp
                                <span class="nivel-riesgo nivel-{{ $nivelPredominate }}">
                                    {{ ucfirst(str_replace('_', ' ', $nivelPredominate)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dominio Recompensas -->
            <div class="col-md-6 mt-4">
                <div class="card domain-card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-award me-1"></i>
                        Recompensas
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="recompensasChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <p><strong>Total evaluaciones:</strong> {{ $hojasPorDominio['recompensas'] ?? 0 }}</p>
                            <p><strong>Nivel de riesgo predominante:</strong>
                                @php
                                    $niveles = $distribucionRiesgoPorDominio['recompensas'] ?? [];
                                    $nivelPredominate = !empty($niveles)
                                        ? array_search(max($niveles), $niveles)
                                        : 'N/A';
                                @endphp
                                <span class="nivel-riesgo nivel-{{ $nivelPredominate }}">
                                    {{ ucfirst(str_replace('_', ' ', $nivelPredominate)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para crear gráficos de dominio
            function createDomainChart(chartId, domain, title) {
                var ctx = document.getElementById(chartId).getContext('2d');

                // Verificar si existen datos para este dominio
                var data = [
                    {{ isset($distribucionRiesgoPorDominio[domain]) ? $distribucionRiesgoPorDominio[domain]['sin_riesgo'] ?? 0 : 0 }},
                    {{ isset($distribucionRiesgoPorDominio[domain]) ? $distribucionRiesgoPorDominio[domain]['bajo'] ?? 0 : 0 }},
                    {{ isset($distribucionRiesgoPorDominio[domain]) ? $distribucionRiesgoPorDominio[domain]['medio'] ?? 0 : 0 }},
                    {{ isset($distribucionRiesgoPorDominio[domain]) ? $distribucionRiesgoPorDominio[domain]['alto'] ?? 0 : 0 }},
                    {{ isset($distribucionRiesgoPorDominio[domain]) ? $distribucionRiesgoPorDominio[domain]['muy_alto'] ?? 0 : 0 }}
                ];

                var chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sin Riesgo', 'Bajo', 'Medio', 'Alto', 'Muy Alto'],
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#008235', // Sin Riesgo - Verde
                                '#00D364', // Riesgo Bajo - Verde claro
                                '#FFD600', // Riesgo Medio - Amarillo
                                '#DA3D3D', // Riesgo Alto - Rojo
                                '#D10000' // Riesgo Muy Alto - Rojo oscuro
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },
                            title: {
                                display: true,
                                text: title,
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.raw || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = total > 0 ? Math.round((value / total) * 100) :
                                            0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });

                return chart;
            }

            // Crear gráficos para cada dominio
            var generalChart = createDomainChart('generalRiskChart', 'liderazgoRelacionesSociales',
                'Distribución General de Niveles de Riesgo');
            var liderazgoChart = createDomainChart('liderazgoChart', 'liderazgoRelacionesSociales',
                'Liderazgo y Relaciones Sociales');
            var controlChart = createDomainChart('controlChart', 'controlSobreElTrabajo',
                'Control sobre el Trabajo');
            var demandasChart = createDomainChart('demandasChart', 'demandasDelTrabajo', 'Demandas del Trabajo');
            var recompensasChart = createDomainChart('recompensasChart', 'recompensas', 'Recompensas');
        });
    </script>
@endsection
