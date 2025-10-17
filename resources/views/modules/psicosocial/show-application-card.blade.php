@extends('layouts.dashboard')

@section('title', 'Detalles de Tarjeta de Aplicación - Módulo Psicosocial')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            {{ $diagnostico->nombre ?? ($diagnostico->descripcion ?? 'Diagnóstico #' . substr($diagnostico->_id, -6)) }}
                        </h1>
                        <p class="text-muted">Detalles de la tarjeta de aplicación</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($diagnostico->estado === 'completado')
                            <a href="{{ route('psicosocial.detailed-report', $diagnostico->_id) }}" class="btn btn-success">
                                <i class="fas fa-chart-bar me-2"></i>Ver Reporte
                            </a>
                        @endif
                        <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Card Information -->
            <div class="col-md-4">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Estado:</strong>
                            <span
                                class="badge ms-2
                            @if ($diagnostico->estado === 'completado') bg-success
                            @elseif($diagnostico->estado === 'en_proceso') bg-warning
                            @else bg-secondary @endif">
                                {{ ucfirst(str_replace('_', ' ', $diagnostico->estado)) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Fecha de Creación:</strong><br>
                            <span class="text-muted">
                                {{ $diagnostico->created_at ? $diagnostico->created_at->format('d/m/Y H:i') : 'Sin fecha' }}
                            </span>
                        </div>

                        @if ($diagnostico->profesional_info)
                            <div class="mb-3">
                                <strong>Profesional Asignado:</strong><br>
                                <div class="d-flex align-items-center mt-1">
                                    <i class="fas fa-user-md me-2 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">{{ $diagnostico->profesional_info['nombre'] }}</div>
                                        <small
                                            class="text-muted">{{ $diagnostico->profesional_info['especialidad'] }}</small><br>
                                        <small class="text-muted">Registro:
                                            {{ $diagnostico->profesional_info['registro'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Ubicación:</strong><br>
                            <div class="row mt-1">
                                <div class="col-12 mb-1">
                                    <i class="fas fa-building me-2 text-muted"></i>
                                    <strong>Sede:</strong> {{ $diagnostico->sede ?? 'N/A' }}
                                </div>
                                <div class="col-12 mb-1">
                                    <i class="fas fa-sitemap me-2 text-muted"></i>
                                    <strong>Área:</strong> {{ $diagnostico->area ?? 'N/A' }}
                                </div>
                                <div class="col-12">
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    <strong>Ciudad:</strong> {{ $diagnostico->ciudad ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        @if ($diagnostico->descripcion)
                            <div class="mb-3">
                                <strong>Descripción:</strong><br>
                                <span class="text-muted">{{ $diagnostico->descripcion }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Estadísticas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="h3 text-primary mb-0">{{ $diagnostico->empleados_asignados ?? 0 }}</div>
                                <small class="text-muted">Total Empleados</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="h3 text-success mb-0">{{ $diagnostico->evaluaciones_completadas ?? 0 }}</div>
                                <small class="text-muted">Completadas</small>
                            </div>
                            <div class="col-6">
                                <div class="h3 text-warning mb-0">{{ $diagnostico->evaluaciones_pendientes ?? 0 }}</div>
                                <small class="text-muted">Pendientes</small>
                            </div>
                            <div class="col-6">
                                <div class="h3 text-info mb-0">{{ $diagnostico->progreso ?? 0 }}%</div>
                                <small class="text-muted">Progreso</small>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <label class="form-label small">Progreso General</label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $diagnostico->progreso ?? 0 }}%"
                                    aria-valuenow="{{ $diagnostico->progreso ?? 0 }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Distribution -->
                @if (isset($diagnostico->distribucion_riesgo) && count($diagnostico->distribucion_riesgo) > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark"><i class="fas fa-exclamation-triangle me-2"></i>Distribución de
                                Riesgo</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="riskChart" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Employee Evaluations -->
            <div class="col-md-8">
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros y Acciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" id="filterEstado" onchange="filterEvaluations()">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="completado">Completado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" id="searchEmployee"
                                    placeholder="Buscar empleado..." onkeyup="filterEvaluations()">
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="refreshData()">
                                        <i class="fas fa-sync-alt me-1"></i>Actualizar
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="exportToExcel()">
                                        <i class="fas fa-file-excel me-1"></i>Excel
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="exportToPDF()">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Evaluations Table -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-users me-2"></i>Evaluaciones de Empleados</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="evaluationsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Empleado</th>
                                        <th>Identificación</th>
                                        <th>Cargo</th>
                                        <th>Estado</th>
                                        <th>Progreso</th>
                                        <th>Última Actividad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluaciones as $evaluacion)
                                        <tr class="evaluation-row"
                                            data-estado="{{ $evaluacion->estado ?? 'pendiente' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle fa-lg me-2 text-muted"></i>
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $evaluacion->nombre_empleado ?? 'Empleado #' . substr($evaluacion->empleado_id, -6) }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ $evaluacion->email_empleado ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $evaluacion->identificacion_empleado ?? 'N/A' }}</td>
                                            <td>{{ $evaluacion->cargo_empleado ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                            @if ($evaluacion->estado === 'completado') bg-success
                                            @elseif($evaluacion->estado === 'en_proceso') bg-warning
                                            @else bg-secondary @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $evaluacion->estado ?? 'pendiente')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $evaluacion->progreso ?? 0 }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $evaluacion->progreso ?? 0 }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $evaluacion->updated_at ? $evaluacion->updated_at->format('d/m/Y H:i') : 'Sin actividad' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary"
                                                        onclick="viewEvaluation('{{ $evaluacion->_id }}')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if ($evaluacion->estado === 'completado')
                                                        <button type="button" class="btn btn-outline-success"
                                                            onclick="viewResults('{{ $evaluacion->_id }}')">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                        class="btn btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="sendReminder('{{ $evaluacion->_id }}')">
                                                                <i class="fas fa-bell me-2"></i>Enviar Recordatorio
                                                            </a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="resetEvaluation('{{ $evaluacion->_id }}')">
                                                                <i class="fas fa-redo me-2"></i>Reiniciar
                                                            </a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item text-danger" href="#"
                                                                onclick="removeEmployee('{{ $evaluacion->_id }}')">
                                                                <i class="fas fa-trash me-2"></i>Remover
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No hay empleados asignados a esta tarjeta.</p>
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
@endsection

@push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 250px;
        }

        .evaluation-row {
            transition: background-color 0.2s;
        }

        .evaluation-row:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Risk Distribution Chart
        @if (isset($diagnostico->distribucion_riesgo) && count($diagnostico->distribucion_riesgo) > 0)
            const riskCtx = document.getElementById('riskChart').getContext('2d');
            const riskData = @json($diagnostico->distribucion_riesgo);

            // Prepare chart data
            const riskLabels = Object.keys(riskData).map(key => {
                const labels = {
                    'sin_riesgo': 'Sin Riesgo',
                    'riesgo_bajo': 'Riesgo Bajo',
                    'riesgo_medio': 'Riesgo Medio',
                    'riesgo_alto': 'Riesgo Alto',
                    'riesgo_muy_alto': 'Riesgo Muy Alto'
                };
                return labels[key] || key;
            });

            const riskValues = Object.values(riskData);
            const riskColors = [
                '#28a745', // Sin riesgo
                '#ffc107', // Bajo
                '#fd7e14', // Medio
                '#dc3545', // Alto
                '#721c24' // Muy alto
            ];

            new Chart(riskCtx, {
                type: 'doughnut',
                data: {
                    labels: riskLabels,
                    datasets: [{
                        data: riskValues,
                        backgroundColor: riskColors,
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
                                usePointStyle: true,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        @endif

        // Filter functions
        function filterEvaluations() {
            const estadoFilter = document.getElementById('filterEstado').value;
            const searchFilter = document.getElementById('searchEmployee').value.toLowerCase();
            const rows = document.querySelectorAll('.evaluation-row');

            rows.forEach(row => {
                const estado = row.dataset.estado;
                const text = row.textContent.toLowerCase();

                const matchEstado = !estadoFilter || estado === estadoFilter;
                const matchSearch = !searchFilter || text.includes(searchFilter);

                if (matchEstado && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Action functions
        function viewEvaluation(evaluationId) {
            window.open(`/psicosocial/evaluation/${evaluationId}`, '_blank');
        }

        function viewResults(evaluationId) {
            window.open(`/psicosocial/results/${evaluationId}`, '_blank');
        }

        function sendReminder(evaluationId) {
            if (confirm('¿Desea enviar un recordatorio al empleado?')) {
                fetch(`/psicosocial/send-reminder/${evaluationId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Recordatorio enviado exitosamente');
                        } else {
                            alert('Error al enviar recordatorio');
                        }
                    });
            }
        }

        function resetEvaluation(evaluationId) {
            if (confirm('¿Está seguro de que desea reiniciar esta evaluación? Esto eliminará todo el progreso.')) {
                fetch(`/psicosocial/reset-evaluation/${evaluationId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error al reiniciar evaluación');
                        }
                    });
            }
        }

        function removeEmployee(evaluationId) {
            if (confirm('¿Está seguro de que desea remover este empleado de la evaluación?')) {
                fetch(`/psicosocial/remove-employee/${evaluationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error al remover empleado');
                        }
                    });
            }
        }

        function refreshData() {
            location.reload();
        }

        function exportToExcel() {
            window.open(`/psicosocial/export-excel/{{ $diagnostico->_id }}`, '_blank');
        }

        function exportToPDF() {
            window.open(`/psicosocial/export-pdf/{{ $diagnostico->_id }}`, '_blank');
        }
    </script>
@endpush
