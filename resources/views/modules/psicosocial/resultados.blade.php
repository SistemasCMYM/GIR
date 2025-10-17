@extends('layouts.dashboard')

@section('titulo', 'Resultados de Evaluación Psicosocial')

@section('styles')
    <style>
        .nivel-riesgo {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            min-width: 1 backgroundColor: [ '#008235', // Sin Riesgo - Verde
                '#00D364', // Riesgo Bajo - Verde claro
                '#FFD600', // Riesgo Medio - Amarillo
                '#DA3D3D', // Riesgo Alto - Rojo
                '#D10000' // Riesgo Muy Alto - Rojo oscuro
                ], text-align: center;
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

        .tabla-resultados th,
        .tabla-resultados td {
            vertical-align: middle;
        }

        .recomendacion-card {
            background-color: #f8f9fa;
            border-left: 5px solid #6c757d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .domain-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
@endsection

@section('contenido')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Resultados de Evaluación Psicosocial</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('psicosocial.index') }}">Psicosocial</a></li>
            <li class="breadcrumb-item active">Resultados</li>
        </ol>

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-chart-bar me-1"></i>
                            Resumen de Resultados
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary me-2" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                            @if (isset($hoja))
                                <a href="{{ route('psicosocial.exportar', $hoja->id) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar
                                </a>
                            @elseif(isset($diagnostico))
                                <a href="{{ route('psicosocial.exportar', $diagnostico->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (isset($diagnostico))
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Información del Diagnóstico</h5>
                                    <p><strong>Empresa:</strong> {{ $diagnostico->empresa->nombre ?? 'N/A' }}</p>
                                    <p><strong>Fecha:</strong>
                                        {{ $diagnostico->fecha_creacion ? $diagnostico->fecha_creacion->format('d/m/Y') : 'Sin fecha' }}
                                    </p>
                                    <p><strong>Empleados evaluados:</strong> {{ $diagnostico->empleados_count ?? 0 }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('psicosocial.exportar', $diagnostico->id) }}"
                                            class="btn btn-primary me-2">
                                            <i class="fas fa-file-export"></i> Exportar
                                        </a>
                                        <a href="{{ route('psicosocial.imprimir', $diagnostico->id) }}"
                                            class="btn btn-secondary" target="_blank">
                                            <i class="fas fa-print"></i> Imprimir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Gráfico de distribución de niveles de riesgo -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <canvas id="riskDistributionChart" height="300"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Nivel de Riesgo</th>
                                                <th>Cantidad</th>
                                                <th>Porcentaje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge text-white" style="background-color: #008235;">Sin
                                                        riesgo</span></td>
                                                <td>{{ $distribucion['sin_riesgo'] ?? 0 }}</td>
                                                <td>{{ number_format((($distribucion['sin_riesgo'] ?? 0) / ($total_evaluaciones ?: 1)) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge text-white"
                                                        style="background-color: #00D364;">Bajo</span></td>
                                                <td>{{ $distribucion['bajo'] ?? 0 }}</td>
                                                <td>{{ number_format((($distribucion['bajo'] ?? 0) / ($total_evaluaciones ?: 1)) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge text-dark"
                                                        style="background-color: #FFD600;">Medio</span></td>
                                                <td>{{ $distribucion['medio'] ?? 0 }}</td>
                                                <td>{{ number_format((($distribucion['medio'] ?? 0) / ($total_evaluaciones ?: 1)) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge text-white"
                                                        style="background-color: #DA3D3D;">Alto</span></td>
                                                <td>{{ $distribucion['alto'] ?? 0 }}</td>
                                                <td>{{ number_format((($distribucion['alto'] ?? 0) / ($total_evaluaciones ?: 1)) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge text-white" style="background-color: #D10000;">Muy
                                                        Alto</span></td>
                                                <td>{{ $distribucion['muy_alto'] ?? 0 }}</td>
                                                <td>{{ number_format((($distribucion['muy_alto'] ?? 0) / ($total_evaluaciones ?: 1)) * 100, 1) }}%
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <td><strong>Total</strong></td>
                                                <td><strong>{{ $total_evaluaciones ?? 0 }}</strong></td>
                                                <td><strong>100%</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de resultados por dominio -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Resultados por Dominio</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Dominio</th>
                                                <th>Puntaje Bruto</th>
                                                <th>Puntaje Transformado</th>
                                                <th>Nivel de Riesgo</th>
                                                <th>Visualización</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($resultados))
                                                @foreach ($resultados as $dominio => $datos)
                                                    <tr>
                                                        <td>{{ ucfirst(str_replace('_', ' ', $dominio)) }}</td>
                                                        <td>{{ $datos['puntaje_bruto'] }}</td>
                                                        <td>{{ $datos['puntaje_transformado'] }}</td>
                                                        <td>
                                                            @php
                                                                $colorClass = match ($datos['nivel']) {
                                                                    'sin_riesgo' => 'success',
                                                                    'bajo' => 'info',
                                                                    'medio' => 'warning',
                                                                    'alto' => 'danger',
                                                                    'muy_alto' => 'dark',
                                                                    default => 'secondary',
                                                                };
                                                                $nivelTexto = match ($datos['nivel']) {
                                                                    'sin_riesgo' => 'Sin riesgo',
                                                                    'bajo' => 'Bajo',
                                                                    'medio' => 'Medio',
                                                                    'alto' => 'Alto',
                                                                    'muy_alto' => 'Muy alto',
                                                                    default => 'No determinado',
                                                                };
                                                            @endphp
                                                            <span
                                                                class="badge bg-{{ $colorClass }}">{{ $nivelTexto }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-{{ $colorClass }}"
                                                                    role="progressbar"
                                                                    style="width: {{ $datos['puntaje_transformado'] }}%;"
                                                                    aria-valuenow="{{ $datos['puntaje_transformado'] }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $datos['puntaje_transformado'] }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No hay resultados disponibles
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico radar de dimensiones -->
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <h5 class="text-center">Perfil de Riesgo Psicosocial</h5>
                                <canvas id="dimensionsRadarChart" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interpretación y recomendaciones -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list me-1"></i>
                        Interpretación y Recomendaciones
                    </div>
                    <div class="card-body">
                        @if (isset($interpretacion))
                            {!! $interpretacion !!}
                        @else
                            <div class="alert alert-info">
                                <h5>Interpretación general</h5>
                                <p>La interpretación se basa en los niveles de riesgo obtenidos en cada dominio:</p>
                                <ul>
                                    <li><strong>Sin riesgo:</strong> No se requieren intervenciones específicas.</li>
                                    <li><strong>Riesgo bajo:</strong> Se recomienda realizar seguimiento periódico.</li>
                                    <li><strong>Riesgo medio:</strong> Se sugiere implementar acciones preventivas.</li>
                                    <li><strong>Riesgo alto:</strong> Se deben implementar acciones correctivas a corto
                                        plazo.</li>
                                    <li><strong>Riesgo muy alto:</strong> Se requiere intervención inmediata.</li>
                                </ul>
                            </div>
                        @endif
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
            // Datos para el gráfico de distribución
            const distributionCtx = document.getElementById('riskDistributionChart');
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Sin riesgo', 'Bajo', 'Medio', 'Alto', 'Muy alto'],
                    datasets: [{
                        data: [
                            {{ $distribucion['sin_riesgo'] ?? 0 }},
                            {{ $distribucion['bajo'] ?? 0 }},
                            {{ $distribucion['medio'] ?? 0 }},
                            {{ $distribucion['alto'] ?? 0 }},
                            {{ $distribucion['muy_alto'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#077D3E', // Sin Riesgo - Verde oscuro
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
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Niveles de Riesgo'
                        }
                    }
                }
            });

            // Datos para el gráfico radar
            @if (isset($resultados))
                const dimensionsCtx = document.getElementById('dimensionsRadarChart');
                new Chart(dimensionsCtx, {
                    type: 'radar',
                    data: {
                        labels: [
                            @foreach ($resultados as $dominio => $datos)
                                '{{ ucfirst(str_replace('_', ' ', $dominio)) }}',
                            @endforeach
                        ],
                        datasets: [{
                            label: 'Puntaje Transformado',
                            data: [
                                @foreach ($resultados as $dominio => $datos)
                                    {{ $datos['puntaje_transformado'] }},
                                @endforeach
                            ],
                            fill: true,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgb(54, 162, 235)',
                            pointBackgroundColor: 'rgb(54, 162, 235)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgb(54, 162, 235)'
                        }, {
                            label: 'Nivel de Riesgo Medio',
                            data: Array(Object.keys({{ json_encode($resultados) }}).length).fill(
                                30),
                            fill: true,
                            backgroundColor: 'rgba(255, 205, 86, 0.2)',
                            borderColor: 'rgba(255, 205, 86, 1)',
                            borderDash: [5, 5],
                            pointRadius: 0,
                        }, {
                            label: 'Nivel de Riesgo Alto',
                            data: Array(Object.keys({{ json_encode($resultados) }}).length).fill(
                                40),
                            fill: false,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgb(66, 165, 132)',
                            borderDash: [5, 5],
                            pointRadius: 0,
                        }]
                    },
                    options: {
                        elements: {
                            line: {
                                tension: 0.2
                            }
                        },
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                },
                                suggestedMin: 0,
                                suggestedMax: 100
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@endsection
