@extends('layouts.dashboard')

@section('title', 'Resumen Individual - Evaluación Psicosocial')

@push('styles')
    <style>
        /* Silva Theme - Psicosocial Individual Resume */
        .silva-resume-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .silva-card-3d {
            background: white;
            border-radius: var(--silva-border-radius);
            box-shadow: var(--silva-card-shadow);
            border: 1px solid rgba(209, 168, 84, 0.1);
            transition: var(--silva-transition);
            position: relative;
            overflow: hidden;
        }

        .silva-card-3d:hover {
            transform: translateY(-8px);
            box-shadow: var(--silva-card-hover);
        }

        .silva-card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--silva-gradient-primary);
        }

        .chart-container-3d {
            position: relative;
            height: 400px;
            padding: 2rem;
            perspective: 1000px;
        }

        .chart-canvas-3d {
            transition: transform 0.3s ease;
        }

        .chart-canvas-3d:hover {
            transform: rotateY(5deg) rotateX(2deg);
        }

        .silva-progress-ring {
            position: relative;
            display: inline-block;
        }

        .silva-progress-ring svg {
            transform: rotate(-90deg);
        }

        .silva-progress-ring-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: 700;
            color: var(--gir-dark);
        }

        .risk-badge-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .risk-badge-modern i {
            font-size: 1.2rem;
        }

        .dimension-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--gir-secondary);
            transition: var(--silva-transition);
        }

        .dimension-card:hover {
            transform: translateX(8px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .dimension-score {
            font-size: 2rem;
            font-weight: 800;
            background: var(--silva-gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .silva-resume-container {
                padding: 1rem 0;
            }

            .chart-container-3d {
                height: 300px;
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="silva-resume-container">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="silva-card-3d">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h1 class="h3 mb-3" style="color: var(--gir-dark); font-weight: 700;">
                                        <i class="fas fa-user-chart me-3" style="color: var(--gir-secondary);"></i>
                                        Resumen Individual - Evaluación Psicosocial
                                    </h1>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Empleado:</strong>
                                                @if ($empleado)
                                                    {{ $empleado->primerNombre ?? '' }}
                                                    {{ $empleado->primerApellido ?? '' }}
                                                @else
                                                    {{ $hoja->primerNombre ?? 'No especificado' }}
                                                    {{ $hoja->primerApellido ?? '' }}
                                                @endif
                                            </p>
                                            <p class="mb-2"><strong>Documento:</strong>
                                                @if ($empleado)
                                                    {{ $empleado->dni ?? 'N/A' }}
                                                @else
                                                    {{ $hoja->dni ?? 'No especificado' }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Email:</strong>
                                                @if ($empleado)
                                                    {{ $empleado->email ?? 'N/A' }}
                                                @else
                                                    {{ $hoja->email ?? 'No especificado' }}
                                                @endif
                                            </p>
                                            <p class="mb-2"><strong>Diagnóstico:</strong>
                                                {{ $diagnostico->descripcion ?? $diagnostico->id }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('psicosocial.show', $diagnostico->id) }}"
                                        class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                    <button class="btn btn-primary" onclick="window.print()">
                                        <i class="fas fa-print"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado de Evaluación y Puntaje Total -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="silva-card-3d">
                        <div class="card-body p-4">
                            <h4 class="mb-4" style="color: var(--gir-dark); font-weight: 700;">
                                <i class="fas fa-chart-line me-2"></i>
                                Estado de Evaluación
                            </h4>

                            <div class="row">
                                @php
                                    $estados = [
                                        'datos' => obtenerEstadoOficial($hoja->datos ?? 'pendiente'),
                                        'intralaboral' => obtenerEstadoOficial($hoja->intralaboral ?? 'pendiente'),
                                        'extralaboral' => obtenerEstadoOficial($hoja->extralaboral ?? 'pendiente'),
                                        'estres' => obtenerEstadoOficial($hoja->estres ?? 'pendiente'),
                                    ];

                                    $completados = array_filter($estados, function ($estado) {
                                        return $estado === 'completado';
                                    });

                                    $progreso = round((count($completados) / 4) * 100);
                                @endphp

                                <div class="col-md-3 mb-3">
                                    <div class="dimension-card text-center">
                                        <i class="fas fa-clipboard-user fa-2x mb-2" style="color: var(--gir-info);"></i>
                                        <h6 class="mb-2">Datos Generales</h6>
                                        @switch($estados['datos'])
                                            @case('completado')
                                                <span class="badge bg-success">Completado</span>
                                            @break

                                            @case('en_progreso')
                                                <span class="badge bg-warning">En Progreso</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Pendiente</span>
                                        @endswitch
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="dimension-card text-center">
                                        <i class="fas fa-building fa-2x mb-2" style="color: var(--gir-primary);"></i>
                                        <h6 class="mb-2">Intralaboral</h6>
                                        @switch($estados['intralaboral'])
                                            @case('completado')
                                                <span class="badge bg-success">Completado</span>
                                            @break

                                            @case('en_progreso')
                                                <span class="badge bg-warning">En Progreso</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Pendiente</span>
                                        @endswitch
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="dimension-card text-center">
                                        <i class="fas fa-home fa-2x mb-2" style="color: var(--gir-warning);"></i>
                                        <h6 class="mb-2">Extralaboral</h6>
                                        @switch($estados['extralaboral'])
                                            @case('completado')
                                                <span class="badge bg-success">Completado</span>
                                            @break

                                            @case('en_progreso')
                                                <span class="badge bg-warning">En Progreso</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Pendiente</span>
                                        @endswitch
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="dimension-card text-center">
                                        <i class="fas fa-heart-pulse fa-2x mb-2" style="color: var(--gir-danger);"></i>
                                        <h6 class="mb-2">Estrés</h6>
                                        @switch($estados['estres'])
                                            @case('completado')
                                                <span class="badge bg-success">Completado</span>
                                            @break

                                            @case('en_progreso')
                                                <span class="badge bg-warning">En Progreso</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Pendiente</span>
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="silva-card-3d">
                        <div class="card-body p-4 text-center">
                            <h4 class="mb-4" style="color: var(--gir-dark); font-weight: 700;">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Progreso Total
                            </h4>

                            <div class="silva-progress-ring mb-3">
                                <svg width="120" height="120">
                                    <circle cx="60" cy="60" r="50" fill="transparent" stroke="#e2e8f0"
                                        stroke-width="8" />
                                    <circle cx="60" cy="60" r="50" fill="transparent"
                                        stroke="url(#progressGradient)" stroke-width="8" stroke-linecap="round"
                                        style="stroke-dasharray: {{ 2 * pi() * 50 }}; stroke-dashoffset: {{ (2 * pi() * 50 * (100 - $progreso)) / 100 }}; 
                                               transition: stroke-dashoffset 1s ease-in-out;" />
                                    <defs>
                                        <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%"
                                            y2="100%">
                                            <stop offset="0%" style="stop-color:#D1A854;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#847D77;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="silva-progress-ring-text">
                                    <div style="font-size: 1.5rem;">{{ $progreso }}%</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">Completado</div>
                                </div>
                            </div>

                            @if ($progreso == 100)
                                @php
                                    // Calcular nivel de riesgo y puntaje total usando el servicio
                                    $nivelRiesgo = 'Pendiente';
                                    $puntajeTotal = 0;
                                    $colorRiesgo = 'secondary';

                                    try {
                                        $service = app(\App\Services\BateriaPsicosocialService::class);
                                        $resultados = $service->calcularResultadosCompletos($hoja);
                                        if (isset($resultados['interpretacion']['total'])) {
                                            $nivelRiesgo =
                                                $resultados['interpretacion']['total']['nivel'] ?? 'Pendiente';
                                            $puntajeTotal = $resultados['puntajes_transformados']['total'] ?? 0;

                                            switch (strtolower($nivelRiesgo)) {
                                                case 'sin riesgo':
                                                    $colorRiesgo = 'sin-riesgo';
                                                    break;
                                                case 'riesgo bajo':
                                                    $colorRiesgo = 'bajo';
                                                    break;
                                                case 'riesgo medio':
                                                    $colorRiesgo = 'medio';
                                                    break;
                                                case 'riesgo alto':
                                                    $colorRiesgo = 'alto';
                                                    break;
                                                case 'riesgo muy alto':
                                                    $colorRiesgo = 'muy-alto';
                                                    break;
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Si hay error en el cálculo, mantener valores por defecto
                                    }
                                @endphp

                                <div class="risk-badge-modern bg-{{ $colorRiesgo }}" style="margin-top: 1rem;">
                                    <i class="fas fa-shield-alt"></i>
                                    {{ $nivelRiesgo }}
                                </div>

                                <div class="mt-3">
                                    <div class="dimension-score">{{ number_format($puntajeTotal, 1) }}</div>
                                    <small class="text-muted">Puntaje Total</small>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Evaluación incompleta. Complete todos los cuestionarios para ver los resultados.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($progreso == 100)
                <!-- Gráficas 3D de Resultados -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="silva-card-3d">
                            <div class="card-body">
                                <h4 class="mb-4" style="color: var(--gir-dark); font-weight: 700;">
                                    <i class="fas fa-chart-pie me-2"></i>
                                    Distribución por Dominios
                                </h4>
                                <div class="chart-container-3d">
                                    <canvas id="dominiosChart" class="chart-canvas-3d"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="silva-card-3d">
                            <div class="card-body">
                                <h4 class="mb-4" style="color: var(--gir-dark); font-weight: 700;">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Puntajes por Dimensión
                                </h4>
                                <div class="chart-container-3d">
                                    <canvas id="dimensionesChart" class="chart-canvas-3d"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles de Dimensiones -->
                <div class="row">
                    <div class="col-12">
                        <div class="silva-card-3d">
                            <div class="card-body p-4">
                                <h4 class="mb-4" style="color: var(--gir-dark); font-weight: 700;">
                                    <i class="fas fa-list-alt me-2"></i>
                                    Detalles por Dimensiones
                                </h4>

                                @php
                                    try {
                                        $service = app(\App\Services\BateriaPsicosocialService::class);
                                        $resultados = $service->calcularResultadosCompletos($hoja);
                                        $dimensiones = $resultados['interpretacion']['dimensiones'] ?? [];
                                    } catch (\Exception $e) {
                                        $dimensiones = [];
                                    }
                                @endphp

                                @if (!empty($dimensiones))
                                    <div class="row">
                                        @foreach ($dimensiones as $nombre => $dimension)
                                            <div class="col-md-4 mb-3">
                                                <div class="dimension-card">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $nombre)) }}
                                                        </h6>
                                                        <span
                                                            class="badge bg-{{ str_replace(' ', '-', strtolower($dimension['nivel'] ?? 'pendiente')) }}">
                                                            {{ $dimension['nivel'] ?? 'Pendiente' }}
                                                        </span>
                                                    </div>
                                                    <div class="dimension-score">
                                                        {{ number_format($dimension['puntaje'] ?? 0, 1) }}</div>
                                                    <small class="text-muted">Puntaje Transformado</small>
                                                    @if (isset($dimension['interpretacion']))
                                                        <p class="mt-2 small">{{ $dimension['interpretacion'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No se pudieron calcular los resultados detallados. Verifique que todas las
                                        evaluaciones estén completas.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.js"></script>
        <script>
            function verTest(hojaId) {
                // Redirigir a la vista de evaluación usando la ruta correcta
                window.location.href = '{{ route('psicosocial.evaluacion', [$diagnostico->id, 'HOJA_ID']) }}'.replace(
                    'HOJA_ID', hojaId);
            }

            // Función para copiar link
            function copiarLink(link) {
                if (link) {
                    navigator.clipboard.writeText(link).then(function() {
                        alert('Link copiado al portapapeles');
                    }, function() {
                        alert('Error al copiar el link');
                    });
                } else {
                    alert('No hay link disponible para este empleado');
                }
            }

            // Configuración global de Chart.js con efectos 3D
            Chart.defaults.plugins.legend.display = true;
            Chart.defaults.plugins.legend.position = 'bottom';
            Chart.defaults.animation.duration = 2000;
            Chart.defaults.animation.easing = 'easeInOutQuart';

            // Configuración de colores Silva para gráficos
            const silvaColors = {
                primary: '#2C5F66',
                secondary: '#4A9B8E',
                accent: '#7BC8A4',
                warning: '#FFD600',
                danger: '#DD0505',
                success: '#008235'
            };

            // Datos del resumen psicosocial
            const resumenData = {
                intralaboral: @if (isset($hoja->intralaboral))
                    {{ obtenerEstadoOficial($hoja->intralaboral) === 'completado' ? '4' : '0' }}
                @else
                    0
                @endif ,
                extralaboral: @if (isset($hoja->extralaboral))
                    {{ obtenerEstadoOficial($hoja->extralaboral) === 'completado' ? '4' : '0' }}
                @else
                    0
                @endif ,
                estres: @if (isset($hoja->estres))
                    {{ obtenerEstadoOficial($hoja->estres) === 'completado' ? '4' : '0' }}
                @else
                    0
                @endif ,
                datos: @if (isset($hoja->datos))
                    {{ obtenerEstadoOficial($hoja->datos) === 'completado' ? '4' : '0' }}
                @else
                    0
                @endif
            };

            // Inicializar gráficos 3D cuando el DOM esté listo
            document.addEventListener('DOMContentLoaded', function() {
                initializeCharts();
                animateProgressRings();
                setupHoverEffects();
            });

            function initializeCharts() {
                // Gráfico de dominios (dona)
                const dominiosChartCtx = document.getElementById('dominiosChart');
                if (dominiosChartCtx) {
                    new Chart(dominiosChartCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Intralaboral', 'Extralaboral', 'Estrés', 'Datos Generales'],
                            datasets: [{
                                data: [
                                    resumenData.intralaboral || 0,
                                    resumenData.extralaboral || 0,
                                    resumenData.estres || 0,
                                    resumenData.datos || 0
                                ],
                                backgroundColor: [
                                    silvaColors.primary,
                                    silvaColors.secondary,
                                    silvaColors.accent,
                                    silvaColors.warning
                                ],
                                borderWidth: 4,
                                borderColor: '#ffffff',
                                hoverBorderWidth: 6,
                                hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 14,
                                            family: 'Segoe UI'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(44, 95, 102, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    borderColor: silvaColors.accent,
                                    borderWidth: 2,
                                    cornerRadius: 10,
                                    displayColors: true
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 2000,
                                easing: 'easeInOutBounce'
                            }
                        }
                    });
                }

                // Gráfico de dimensiones (barras)
                const dimensionesChartCtx = document.getElementById('dimensionesChart');
                if (dimensionesChartCtx) {
                    new Chart(dimensionesChartCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Intralaboral', 'Extralaboral', 'Estrés', 'General'],
                            datasets: [{
                                label: 'Nivel de Riesgo',
                                data: [
                                    resumenData.intralaboral || 0,
                                    resumenData.extralaboral || 0,
                                    resumenData.estres || 0,
                                    resumenData.datos || 0
                                ],
                                backgroundColor: [
                                    silvaColors.primary,
                                    silvaColors.secondary,
                                    silvaColors.accent,
                                    silvaColors.warning
                                ],
                                borderColor: silvaColors.primary,
                                borderWidth: 2,
                                borderRadius: 10,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 5,
                                    grid: {
                                        color: 'rgba(44, 95, 102, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        },
                                        color: silvaColors.primary
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        },
                                        color: silvaColors.primary
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(44, 95, 102, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    cornerRadius: 10
                                }
                            },
                            animation: {
                                duration: 2000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                }
            }

            function animateProgressRings() {
                const progressRings = document.querySelectorAll('.progress-ring circle:last-child');

                progressRings.forEach((ring, index) => {
                    const radius = ring.r.baseVal.value;
                    const circumference = 2 * Math.PI * radius;
                    const percentage = [
                        resumenData.intralaboral * 20 || 0,
                        resumenData.extralaboral * 20 || 0,
                        resumenData.estres * 20 || 0,
                        resumenData.datos * 20 || 0
                    ][index];

                    ring.style.strokeDasharray = circumference;
                    ring.style.strokeDashoffset = circumference;

                    // Animación con retraso escalonado
                    setTimeout(() => {
                        const offset = circumference - (percentage / 100) * circumference;
                        ring.style.transition = 'stroke-dashoffset 2s ease-in-out';
                        ring.style.strokeDashoffset = offset;
                    }, index * 200);
                });
            }

            function setupHoverEffects() {
                // Efectos hover para las tarjetas de dimensiones
                const dimensionCards = document.querySelectorAll('.dimension-card');

                dimensionCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-10px) rotateX(5deg) rotateY(5deg)';
                        this.style.boxShadow = '0 20px 40px rgba(44, 95, 102, 0.3)';
                        this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
                        this.style.boxShadow = '0 4px 15px rgba(44, 95, 102, 0.1)';
                    });
                });

                // Efectos hover para elementos interactivos
                const interactiveElements = document.querySelectorAll('.btn, .badge, .progress-ring');

                interactiveElements.forEach(element => {
                    element.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                        this.style.transition = 'transform 0.2s ease';
                    });

                    element.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            }
        </script>
    @endpush
@endsection
