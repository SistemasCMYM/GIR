@extends('layouts.dashboard')

@section('title', 'Reporte Resumen - Módulo Psicosocial')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Reporte Resumen Psicosocial</h1>
                        <p class="text-muted">Análisis general de evaluaciones psicosociales</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </button>
                        <button class="btn btn-outline-danger" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </button>
                        <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Reporte</h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label for="area" class="form-label">Área</label>
                        <select class="form-select form-select-sm" id="area" name="area">
                            <option value="">Todas</option>
                            <option value="administrativa" {{ request('area') === 'administrativa' ? 'selected' : '' }}>
                                Administrativa</option>
                            <option value="operativa" {{ request('area') === 'operativa' ? 'selected' : '' }}>Operativa
                            </option>
                            <option value="comercial" {{ request('area') === 'comercial' ? 'selected' : '' }}>Comercial
                            </option>
                            <option value="financiera" {{ request('area') === 'financiera' ? 'selected' : '' }}>Financiera
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sede" class="form-label">Sede</label>
                        <select class="form-select form-select-sm" id="sede" name="sede">
                            <option value="">Todas</option>
                            <option value="principal" {{ request('sede') === 'principal' ? 'selected' : '' }}>Principal
                            </option>
                            <option value="norte" {{ request('sede') === 'norte' ? 'selected' : '' }}>Norte</option>
                            <option value="sur" {{ request('sede') === 'sur' ? 'selected' : '' }}>Sur</option>
                            <option value="centro" {{ request('sede') === 'centro' ? 'selected' : '' }}>Centro</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <select class="form-select form-select-sm" id="ciudad" name="ciudad">
                            <option value="">Todas</option>
                            <option value="bogota" {{ request('ciudad') === 'bogota' ? 'selected' : '' }}>Bogotá</option>
                            <option value="medellin" {{ request('ciudad') === 'medellin' ? 'selected' : '' }}>Medellín
                            </option>
                            <option value="cali" {{ request('ciudad') === 'cali' ? 'selected' : '' }}>Cali</option>
                            <option value="barranquilla" {{ request('ciudad') === 'barranquilla' ? 'selected' : '' }}>
                                Barranquilla</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="contrato" class="form-label">Contrato</label>
                        <select class="form-select form-select-sm" id="contrato" name="contrato">
                            <option value="">Todos</option>
                            <option value="indefinido" {{ request('contrato') === 'indefinido' ? 'selected' : '' }}>
                                Indefinido</option>
                            <option value="temporal" {{ request('contrato') === 'temporal' ? 'selected' : '' }}>Temporal
                            </option>
                            <option value="obra_labor" {{ request('contrato') === 'obra_labor' ? 'selected' : '' }}>Obra o
                                Labor</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="forma" class="form-label">Forma</label>
                        <select class="form-select form-select-sm" id="forma" name="forma">
                            <option value="">Todas</option>
                            <option value="A" {{ request('forma') === 'A' ? 'selected' : '' }}>Forma A</option>
                            <option value="B" {{ request('forma') === 'B' ? 'selected' : '' }}>Forma B</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-3d bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $summaryReport['statistics']['diagnosticos'] ?? 0 }}</h3>
                        <p class="mb-0">Diagnósticos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-3d bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $summaryReport['statistics']['evaluaciones_completadas'] ?? 0 }}</h3>
                        <p class="mb-0">Completadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-3d bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $summaryReport['statistics']['evaluaciones_pendientes'] ?? 0 }}</h3>
                        <p class="mb-0">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-3d bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $summaryReport['statistics']['empleados_evaluados'] ?? 0 }}</h3>
                        <p class="mb-0">Empleados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Risk Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribución de Riesgo</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-3d">
                            <canvas id="riskDistributionChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Progreso por Área</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-3d">
                            <canvas id="progressChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
            <!-- Diagnostics Summary -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Resumen de Diagnósticos</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleView('card')">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleView('table')">
                                <i class="fas fa-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Card View -->
                        <div id="cardView" class="p-3">
                            <div class="row">
                                @forelse($summaryReport['diagnostics'] as $diagnostic)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    {{ $diagnostic->nombre ?? ($diagnostic->descripcion ?? 'Diagnóstico #' . substr($diagnostic->_id, -6)) }}
                                                </h6>
                                                <div class="row text-center mb-2">
                                                    <div class="col-4">
                                                        <div class="fw-bold text-primary">
                                                            {{ $diagnostic->empleados_asignados ?? 0 }}</div>
                                                        <small class="text-muted">Total</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="fw-bold text-success">
                                                            {{ $diagnostic->evaluaciones_completadas ?? 0 }}</div>
                                                        <small class="text-muted">Completadas</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="fw-bold text-warning">
                                                            {{ $diagnostic->evaluaciones_pendientes ?? 0 }}</div>
                                                        <small class="text-muted">Pendientes</small>
                                                    </div>
                                                </div>
                                                <div class="progress mb-2" style="height: 6px;">
                                                    <div class="progress-bar"
                                                        style="width: {{ $diagnostic->progreso ?? 0 }}%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">{{ $diagnostic->progreso ?? 0 }}%
                                                        completado</small>
                                                    <a href="{{ route('psicosocial.show-application-card', $diagnostic->_id) }}"
                                                        class="btn btn-sm btn-outline-primary">Ver</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4">
                                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No hay diagnósticos con los filtros aplicados</h6>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Table View -->
                        <div id="tableView" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Diagnóstico</th>
                                            <th>Ubicación</th>
                                            <th>Profesional</th>
                                            <th>Total</th>
                                            <th>Completadas</th>
                                            <th>Pendientes</th>
                                            <th>Progreso</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($summaryReport['diagnostics'] as $diagnostic)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">
                                                        {{ $diagnostic->nombre ?? ($diagnostic->descripcion ?? 'Diagnóstico #' . substr($diagnostic->_id, -6)) }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $diagnostic->created_at ? $diagnostic->created_at->format('d/m/Y') : '' }}</small>
                                                </td>
                                                <td>
                                                    <div>{{ $diagnostic->sede ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $diagnostic->area ?? 'N/A' }} -
                                                        {{ $diagnostic->ciudad ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    @if (isset($diagnostic->profesional_info))
                                                        <div>{{ $diagnostic->profesional_info['nombre'] }}</div>
                                                        <small
                                                            class="text-muted">{{ $diagnostic->profesional_info['especialidad'] }}</small>
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td><span
                                                        class="badge bg-primary">{{ $diagnostic->empleados_asignados ?? 0 }}</span>
                                                </td>
                                                <td><span
                                                        class="badge bg-success">{{ $diagnostic->evaluaciones_completadas ?? 0 }}</span>
                                                </td>
                                                <td><span
                                                        class="badge bg-warning">{{ $diagnostic->evaluaciones_pendientes ?? 0 }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                            <div class="progress-bar"
                                                                style="width: {{ $diagnostic->progreso ?? 0 }}%"></div>
                                                        </div>
                                                        <small>{{ $diagnostic->progreso ?? 0 }}%</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                @if ($diagnostic->estado === 'completado') bg-success
                                                @elseif($diagnostic->estado === 'en_proceso') bg-warning
                                                @else bg-secondary @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $diagnostic->estado ?? 'pendiente')) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('psicosocial.show-application-card', $diagnostic->_id) }}"
                                                            class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if ($diagnostic->estado === 'completado')
                                                            <a href="{{ route('psicosocial.detailed-report', $diagnostic->_id) }}"
                                                                class="btn btn-outline-success">
                                                                <i class="fas fa-chart-bar"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No hay diagnósticos para mostrar</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-info-circle me-2"></i>Información del Reporte</h6>
                                <p class="small text-muted mb-1">
                                    <strong>Generado:</strong>
                                    {{ $summaryReport['generated_at'] ?? now()->format('Y-m-d H:i:s') }}
                                </p>
                                <p class="small text-muted mb-1">
                                    <strong>Filtros aplicados:</strong>
                                    @if (count($summaryReport['filters_applied'] ?? []) > 0)
                                        @foreach ($summaryReport['filters_applied'] as $key => $value)
                                            @if ($value)
                                                {{ ucfirst($key) }}: {{ $value }}{{ !$loop->last ? ', ' : '' }}
                                            @endif
                                        @endforeach
                                    @else
                                        Ninguno
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="small text-muted mb-0">
                                    <strong>Total de registros:</strong> {{ count($summaryReport['diagnostics'] ?? []) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .chart-container-3d {
            position: relative;
            height: 300px;
        }

        .card-3d {
            transform: perspective(1000px) rotateX(0deg);
            transition: transform 0.3s ease;
        }

        .card-3d:hover {
            transform: perspective(1000px) rotateX(5deg);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Risk Distribution Chart
        const riskCanvas = document.getElementById('riskDistributionChart');
        if (riskCanvas) {
            const riskCtx = riskCanvas.getContext('2d');
            const riskData = @json($summaryReport['risk_distribution'] ?? []);

            new Chart(riskCtx, {
                type: 'doughnut',
                data: {
                    labels: riskData.map(item => item.label),
                    datasets: [{
                        data: riskData.map(item => item.value),
                        backgroundColor: riskData.map(item => item.color),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = riskData[context.dataIndex]?.percentage || 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Progress Chart (sample data)
        const progressCanvas = document.getElementById('progressChart');
        if (progressCanvas) {
            const progressCtx = progressCanvas.getContext('2d');
            new Chart(progressCtx, {
                type: 'bar',
                data: {
                    labels: ['Administrativa', 'Operativa', 'Comercial', 'Financiera'],
                    datasets: [{
                        label: 'Progreso (%)',
                        data: [85, 72, 90, 78],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(0, 123, 255, 0.8)',
                            'rgba(108, 117, 125, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(0, 123, 255, 1)',
                            'rgba(108, 117, 125, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // View toggle functions
        function toggleView(view) {
            const cardView = document.getElementById('cardView');
            const tableView = document.getElementById('tableView');

            if (view === 'card') {
                cardView.classList.remove('d-none');
                tableView.classList.add('d-none');
            } else {
                cardView.classList.add('d-none');
                tableView.classList.remove('d-none');
            }
        }

        // Export functions
        function exportToExcel() {
            const params = new URLSearchParams(window.location.search);
            window.open(`/psicosocial/export-excel?${params.toString()}`, '_blank');
        }

        function exportToPDF() {
            const params = new URLSearchParams(window.location.search);
            window.open(`/psicosocial/export-pdf?${params.toString()}`, '_blank');
        }
    </script>
@endpush
