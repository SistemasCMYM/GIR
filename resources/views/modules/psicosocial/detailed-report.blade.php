@extends('layouts.dashboard')

@section('title', 'Reporte Detallado - Módulo Psicosocial')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Reporte Detallado de Evaluación</h1>
                        <p class="text-muted">Análisis completo de la evaluación psicosocial</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </button>
                        <button class="btn btn-outline-danger" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </button>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                        <a href="{{ route('psicosocial.summary-report') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if ($detailedReport)
            <!-- Employee Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información del Empleado</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <strong>Edad:</strong><br>
                                    <span class="text-muted">{{ $detailedReport['sociodemographic']['age'] ?? 'N/A' }}
                                        años</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <strong>Género:</strong><br>
                                    <span
                                        class="text-muted">{{ ucfirst($detailedReport['sociodemographic']['gender'] ?? 'N/A') }}</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <strong>Nivel Educativo:</strong><br>
                                    <span
                                        class="text-muted">{{ $detailedReport['sociodemographic']['education'] ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <strong>Cargo:</strong><br>
                                    <span
                                        class="text-muted">{{ $detailedReport['sociodemographic']['position'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <strong>Experiencia en el Cargo:</strong><br>
                                    <span
                                        class="text-muted">{{ $detailedReport['sociodemographic']['experience'] ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <strong>Tipo de Contrato:</strong><br>
                                    <span
                                        class="text-muted">{{ ucfirst(str_replace('_', ' ', $detailedReport['sociodemographic']['contract_type'] ?? 'N/A')) }}</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <strong>Jornada de Trabajo:</strong><br>
                                    <span
                                        class="text-muted">{{ $detailedReport['sociodemographic']['work_schedule'] ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <strong>Nivel de Riesgo General:</strong><br>
                                    <span
                                        class="badge risk-badge risk-{{ str_replace('_', '-', $detailedReport['overall_risk']) }}">
                                        {{ ucfirst(str_replace('_', ' ', $detailedReport['overall_risk'])) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Overview Charts -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-building me-2"></i>Intralaboral</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="chart-container-3d" style="height: 200px;">
                                <canvas id="intralaboralChart"></canvas>
                            </div>
                            <div class="mt-3">
                                <h4 class="mb-1">{{ $detailedReport['intralaboral']['raw_score'] }}</h4>
                                <span
                                    class="badge risk-badge risk-{{ str_replace('_', '-', array_search($detailedReport['intralaboral']['risk_level'], ['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5]) ? array_keys(['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5])[$detailedReport['intralaboral']['risk_level'] - 1] : 'sin-riesgo') }}">
                                    {{ $detailedReport['intralaboral']['risk_label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-home me-2"></i>Extralaboral</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="chart-container-3d" style="height: 200px;">
                                <canvas id="extralaboralChart"></canvas>
                            </div>
                            <div class="mt-3">
                                <h4 class="mb-1">{{ $detailedReport['extralaboral']['raw_score'] }}</h4>
                                <span
                                    class="badge risk-badge risk-{{ str_replace('_', '-', array_search($detailedReport['extralaboral']['risk_level'], ['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5]) ? array_keys(['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5])[$detailedReport['extralaboral']['risk_level'] - 1] : 'sin-riesgo') }}">
                                    {{ $detailedReport['extralaboral']['risk_label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-heart-broken me-2"></i>Estrés</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="chart-container-3d" style="height: 200px;">
                                <canvas id="stressChart"></canvas>
                            </div>
                            <div class="mt-3">
                                <h4 class="mb-1">{{ $detailedReport['stress']['raw_score'] }}</h4>
                                <span
                                    class="badge risk-badge risk-{{ str_replace('_', '-', array_search($detailedReport['stress']['risk_level'], ['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5]) ? array_keys(['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5])[$detailedReport['stress']['risk_level'] - 1] : 'sin-riesgo') }}">
                                    {{ $detailedReport['stress']['risk_label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Intralaboral Dimensions -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Dimensiones Intralaborales</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Dimensión</th>
                                    <th>Puntaje</th>
                                    <th>Nivel de Riesgo</th>
                                    <th>Gráfico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailedReport['intralaboral']['dimensions'] as $key => $dimension)
                                    <tr>
                                        <td class="fw-bold">{{ $dimension['label'] }}</td>
                                        <td>{{ $dimension['score'] }}</td>
                                        <td>
                                            <span
                                                class="badge risk-badge risk-{{ str_replace('_', '-', array_search($dimension['risk_level'], ['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5]) ? array_keys(['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5])[$dimension['risk_level'] - 1] : 'sin-riesgo') }}">
                                                {{ $dimension['risk_label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min(($dimension['score'] / 100) * 100, 100) }}%"
                                                    aria-valuenow="{{ $dimension['score'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $dimension['score'] }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Extralaboral Dimensions -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dimensiones Extralaborales</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Dimensión</th>
                                    <th>Puntaje</th>
                                    <th>Nivel de Riesgo</th>
                                    <th>Gráfico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailedReport['extralaboral']['dimensions'] as $key => $dimension)
                                    <tr>
                                        <td class="fw-bold">{{ $dimension['label'] }}</td>
                                        <td>{{ $dimension['score'] }}</td>
                                        <td>
                                            <span
                                                class="badge risk-badge risk-{{ str_replace('_', '-', array_search($dimension['risk_level'], ['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5]) ? array_keys(['sin_riesgo' => 1, 'riesgo_bajo' => 2, 'riesgo_medio' => 3, 'riesgo_alto' => 4, 'riesgo_muy_alto' => 5])[$dimension['risk_level'] - 1] : 'sin-riesgo') }}">
                                                {{ $dimension['risk_label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: {{ min(($dimension['score'] / 100) * 100, 100) }}%"
                                                    aria-valuenow="{{ $dimension['score'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $dimension['score'] }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            @if (isset($detailedReport['recommendations']) && count($detailedReport['recommendations']) > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Recomendaciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($detailedReport['recommendations'] as $recommendation)
                                <div class="col-md-6 mb-3">
                                    <div
                                        class="alert alert-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'info') }} border-0 shadow-sm">
                                        <div class="d-flex align-items-start">
                                            <i
                                                class="fas fa-{{ $recommendation['priority'] === 'high' ? 'exclamation-triangle' : ($recommendation['priority'] === 'medium' ? 'info-circle' : 'check-circle') }} me-3 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading mb-2">{{ ucfirst($recommendation['type']) }}</h6>
                                                <p class="mb-0">{{ $recommendation['recommendation'] }}</p>
                                                <small class="text-muted">Prioridad:
                                                    {{ ucfirst($recommendation['priority']) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Report Summary -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Resumen del Reporte</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Interpretación de Resultados</h6>
                            <p class="text-muted small">
                                Este reporte presenta los resultados de la evaluación de factores de riesgo psicosocial
                                basada en la Batería de Instrumentos para la Evaluación de Factores de Riesgo Psicosocial
                                del Ministerio de la Protección Social de Colombia.
                            </p>
                            <p class="text-muted small">
                                Los resultados deben ser interpretados por un profesional especializado en psicología
                                ocupacional o seguridad y salud en el trabajo.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Información Técnica</h6>
                            <p class="text-muted small mb-1">
                                <strong>Fecha de Evaluación:</strong>
                                {{ $detailedReport['evaluation']->created_at ? $detailedReport['evaluation']->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                            <p class="text-muted small mb-1">
                                <strong>Fecha de Finalización:</strong>
                                {{ $detailedReport['evaluation']->fecha_finalizacion ? \Carbon\Carbon::parse($detailedReport['evaluation']->fecha_finalizacion)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                            <p class="text-muted small mb-1">
                                <strong>ID de Evaluación:</strong> {{ $detailedReport['evaluation']->_id }}
                            </p>
                            <p class="text-muted small">
                                <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-muted">Reporte no disponible</h5>
                    <p class="text-muted">No se encontraron datos para generar el reporte detallado.</p>
                    <a href="{{ route('psicosocial.summary-report') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Reporte Resumen
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .btn,
            .navbar,
            .sidebar {
                display: none !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                margin-bottom: 20px !important;
            }

            .chart-container-3d {
                height: 150px !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if ($detailedReport)
            // Intralaboral Chart
            const intralaboralCtx = document.getElementById('intralaboralChart').getContext('2d');
            new Chart(intralaboralCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Puntaje', 'Restante'],
                    datasets: [{
                        data: [{{ $detailedReport['intralaboral']['raw_score'] }},
                            {{ 100 - $detailedReport['intralaboral']['raw_score'] }}
                        ],
                        backgroundColor: ['#28a745', '#e9ecef'],
                        borderWidth: 0
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
                    cutout: '70%'
                }
            });

            // Extralaboral Chart
            const extralaboralCtx = document.getElementById('extralaboralChart').getContext('2d');
            new Chart(extralaboralCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Puntaje', 'Restante'],
                    datasets: [{
                        data: [{{ $detailedReport['extralaboral']['raw_score'] }},
                            {{ 100 - $detailedReport['extralaboral']['raw_score'] }}
                        ],
                        backgroundColor: ['#ffc107', '#e9ecef'],
                        borderWidth: 0
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
                    cutout: '70%'
                }
            });

            // Stress Chart
            const stressCtx = document.getElementById('stressChart').getContext('2d');
            new Chart(stressCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Puntaje', 'Restante'],
                    datasets: [{
                        data: [{{ $detailedReport['stress']['raw_score'] }},
                            {{ 100 - $detailedReport['stress']['raw_score'] }}
                        ],
                        backgroundColor: ['#dc3545', '#e9ecef'],
                        borderWidth: 0
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
                    cutout: '70%'
                }
            });
        @endif

        function exportToExcel() {
            window.open('/psicosocial/export-excel/{{ $detailedReport['evaluation']->_id ?? '' }}', '_blank');
        }

        function exportToPDF() {
            window.open('/psicosocial/export-pdf/{{ $detailedReport['evaluation']->_id ?? '' }}', '_blank');
        }
    </script>
@endpush
