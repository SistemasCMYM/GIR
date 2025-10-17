@extends('layouts.dashboard')

@section('title', 'Estadísticas de Encuesta')

@push('styles')
    <link href="{{ asset('css/estadisticas-encuestas.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.index') }}">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.encuestas.index') }}">
                        <i class="fas fa-poll"></i> Encuestas
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-chart-bar text-info"></i>
                            Estadísticas de Encuesta
                        </h1>
                        <p class="text-muted mb-0">
                            {{ $encuesta->titulo ?? 'Encuesta sin título' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.encuestas.index') }}"
                            class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button class="btn btn-export btn-custom" onclick="exportarEstadisticas()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Ejecutivo -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 bg-gradient-primary text-white fade-in-up">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $estadisticas['total_respuestas'] ?? 0 }}</h3>
                                <p class="mb-0 opacity-75">Total Respuestas</p>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="status-indicator status-info"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 bg-gradient-success text-white fade-in-up">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $estadisticas['respuestas_completas'] ?? 0 }}</h3>
                                <p class="mb-0 opacity-75">Completas</p>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="status-indicator status-success"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 bg-gradient-warning text-white fade-in-up">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 fw-bold">{{ number_format($estadisticas['tasa_completitud'] ?? 0, 1) }}%
                                </h3>
                                <p class="mb-0 opacity-75">Tasa de Completitud</p>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <div class="status-indicator status-warning"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 bg-gradient-info text-white fade-in-up">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $estadisticas['tiempo_promedio'] ?? 0 }}m</h3>
                                <p class="mb-0 opacity-75">Tiempo Promedio</p>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="status-indicator status-info"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Principales -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card chart-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line text-primary"></i>
                            Tendencia de Respuestas
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="respuestasTendenciaChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card chart-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie text-success"></i>
                            Estado de Respuestas
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="estadoRespuestasChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis por Tipo de Pregunta -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle text-info"></i>
                            Análisis Detallado por Pregunta
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="preguntasAnalisis">
                            @if (isset($preguntas) && count($preguntas) > 0)
                                @foreach ($preguntas as $index => $pregunta)
                                    <div class="col-lg-6 mb-4">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    Pregunta {{ $index + 1 }}: {{ $pregunta['tipo'] ?? 'Sin tipo' }}
                                                </h6>
                                                <small
                                                    class="text-muted">{{ Str::limit($pregunta['texto'] ?? 'Sin texto', 100) }}</small>
                                            </div>
                                            <div class="card-body">
                                                @switch($pregunta['tipo'] ?? 'escala_likert')
                                                    @case('escala_likert')
                                                        <canvas id="pregunta{{ $index + 1 }}Chart" height="150"></canvas>
                                                    @break

                                                    @case('escala_numerica')
                                                        <canvas id="pregunta{{ $index + 1 }}Chart" height="150"></canvas>
                                                    @break

                                                    @case('opcion_multiple')
                                                    @case('seleccion_multiple')
                                                        <canvas id="pregunta{{ $index + 1 }}Chart" height="200"></canvas>
                                                    @break

                                                    @case('si_no')
                                                        <canvas id="pregunta{{ $index + 1 }}Chart" height="150"></canvas>
                                                    @break

                                                    @case('matriz_calificacion')
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Aspecto</th>
                                                                        <th>Promedio</th>
                                                                        <th>Respuestas</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($pregunta['filas'] ?? [] as $fila)
                                                                        <tr>
                                                                            <td>{{ $fila }}</td>
                                                                            <td>
                                                                                <div class="progress" style="height: 20px;">
                                                                                    <div class="progress-bar bg-info"
                                                                                        style="width: {{ rand(60, 95) }}%">
                                                                                        {{ number_format(rand(30, 50) / 10, 1) }}
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>{{ rand(15, 50) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @break

                                                    @case('respuesta_abierta')
                                                        <div class="alert alert-info">
                                                            <h6><i class="fas fa-comments"></i> Resumen de Respuestas Abiertas</h6>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <strong>Total respuestas:</strong> {{ rand(10, 30) }}
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <strong>Promedio palabras:</strong> {{ rand(15, 45) }}
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <strong>Palabras más frecuentes:</strong>
                                                                @php
                                                                    $palabras = [
                                                                        'comunicación',
                                                                        'trabajo',
                                                                        'equipo',
                                                                        'ambiente',
                                                                        'gestión',
                                                                        'proceso',
                                                                        'tiempo',
                                                                        'recursos',
                                                                    ];
                                                                @endphp
                                                                @foreach (array_slice($palabras, 0, 5) as $palabra)
                                                                    <span
                                                                        class="badge bg-secondary me-1">{{ $palabra }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @break

                                                    @default
                                                        <div class="text-center text-muted">
                                                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                                            <p>Gráfico no disponible para este tipo de pregunta</p>
                                                        </div>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                        <h5>No hay datos de preguntas disponibles</h5>
                                        <p class="mb-0">Esta encuesta aún no tiene respuestas o las preguntas no están
                                            configuradas.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmentación y Filtros -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter text-warning"></i>
                            Segmentación de Respuestas
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="segmentacionChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs text-secondary"></i>
                            Filtros de Análisis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Período</label>
                            <select class="form-select" id="filtro_periodo" onchange="actualizarGraficos()">
                                <option value="todo">Todo el período</option>
                                <option value="ultima_semana">Última semana</option>
                                <option value="ultimo_mes">Último mes</option>
                                <option value="trimestre">Último trimestre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Área/Departamento</label>
                            <select class="form-select" id="filtro_area" onchange="actualizarGraficos()">
                                <option value="">Todas las áreas</option>
                                <option value="administracion">Administración</option>
                                <option value="ventas">Ventas</option>
                                <option value="operaciones">Operaciones</option>
                                <option value="rrhh">Recursos Humanos</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado de Respuesta</label>
                            <select class="form-select" id="filtro_estado" onchange="actualizarGraficos()">
                                <option value="">Todos los estados</option>
                                <option value="completa">Completas</option>
                                <option value="parcial">Parciales</option>
                                <option value="iniciada">Iniciadas</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100" onclick="actualizarGraficos()">
                            <i class="fas fa-sync"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights y Recomendaciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Insights y Recomendaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6>📈 Fortalezas Identificadas</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Alta tasa de participación en preguntas de escala Likert
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Respuestas consistentes en preguntas de satisfacción
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Buen engagement en preguntas abiertas
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <h6>⚠️ Áreas de Mejora</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Considerar simplificar preguntas de matriz
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Aumentar claridad en preguntas de selección múltiple
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Revisar longitud de respuestas abiertas
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @php
            // Colores disponibles para los gráficos
            $colores = [
                '#0d6efd', // primary
                '#198754', // success
                '#ffc107', // warning
                '#0dcaf0', // info
                '#dc3545', // danger
                '#6f42c1', // purple
                '#fd7e14', // orange
                '#20c997', // teal
                '#d63384', // pink
                '#6c757d', // secondary
            ];
        @endphp

        // Datos de ejemplo para los gráficos
        const datosEjemplo = {
            tendencia: {
                labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                data: [12, 25, 45, 67]
            },
            estado: {
                labels: ['Completas', 'Parciales', 'Iniciadas'],
                data: [67, 15, 8]
            },
            segmentacion: {
                labels: ['Administración', 'Ventas', 'Operaciones', 'RRHH'],
                data: [25, 30, 20, 15]
            }
        };

        // Configuración de colores
        const colores = {
            primary: '#0d6efd',
            success: '#198754',
            warning: '#ffc107',
            info: '#0dcaf0',
            danger: '#dc3545'
        };

        // Gráfico de Tendencia
        const ctxTendencia = document.getElementById('respuestasTendenciaChart').getContext('2d');
        new Chart(ctxTendencia, {
            type: 'line',
            data: {
                labels: datosEjemplo.tendencia.labels,
                datasets: [{
                    label: 'Respuestas',
                    data: datosEjemplo.tendencia.data,
                    borderColor: colores.primary,
                    backgroundColor: colores.primary + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Estado
        const ctxEstado = document.getElementById('estadoRespuestasChart').getContext('2d');
        new Chart(ctxEstado, {
            type: 'doughnut',
            data: {
                labels: datosEjemplo.estado.labels,
                datasets: [{
                    data: datosEjemplo.estado.data,
                    backgroundColor: [colores.success, colores.warning, colores.info]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Segmentación
        const ctxSegmentacion = document.getElementById('segmentacionChart').getContext('2d');
        new Chart(ctxSegmentacion, {
            type: 'bar',
            data: {
                labels: datosEjemplo.segmentacion.labels,
                datasets: [{
                    label: 'Respuestas por Área',
                    data: datosEjemplo.segmentacion.data,
                    backgroundColor: [colores.primary, colores.success, colores.warning, colores.info]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráficos por pregunta
        @if (isset($preguntas) && count($preguntas) > 0)
            @foreach ($preguntas as $index => $pregunta)
                @if (in_array($pregunta['tipo'] ?? 'escala_likert', [
                        'escala_likert',
                        'escala_numerica',
                        'opcion_multiple',
                        'seleccion_multiple',
                        'si_no',
                    ]))
                    const ctx{{ $index + 1 }} = document.getElementById('pregunta{{ $index + 1 }}Chart');
                    if (ctx{{ $index + 1 }}) {
                        new Chart(ctx{{ $index + 1 }}.getContext('2d'), {
                            type: '{{ in_array($pregunta['tipo'] ?? 'escala_likert', ['opcion_multiple', 'seleccion_multiple']) ? 'bar' : 'bar' }}',
                            data: {
                                labels: {!! json_encode($pregunta['opciones'] ?? ['Opción 1', 'Opción 2', 'Opción 3']) !!},
                                datasets: [{
                                    label: 'Respuestas',
                                    data: [
                                        {{ implode(', ',array_map(function () {return rand(5, 25);}, range(1, count($pregunta['opciones'] ?? [1, 2, 3])))) }}
                                    ],
                                    backgroundColor: '{{ $colores[array_rand($colores)] ?? '#0d6efd' }}'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }
                @endif
            @endforeach
        @endif

        function actualizarGraficos() {
            // Simular actualización de gráficos basada en filtros
            console.log('Actualizando gráficos con filtros...');

            // Aquí iría la lógica para actualizar los gráficos
            // basándose en los filtros seleccionados
        }

        function exportarEstadisticas() {
            // Simular exportación
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'success',
                title: 'Exportando estadísticas...'
            });
        }
    </script>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #087990 100%);
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .progress {
            border-radius: 10px;
        }

        .badge {
            font-size: 0.75rem;
        }

        .list-unstyled li {
            padding: 0.25rem 0;
        }
    </style>
@endsection
