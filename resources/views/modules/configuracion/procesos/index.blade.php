@extends('layouts.dashboard')

@section('title', 'Configuración de Procesos')

@push('styles')
    <style>
        .processes-header {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(40, 167, 69, 0.3);
        }

        .process-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .process-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
        }

        .section-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: white;
            font-size: 14px;
        }

        .general-icon {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .automation-icon {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .monitoring-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .scheduler-icon {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 123, 255, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.4);
        }

        .stats-card {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(30, 126, 52, 0.1));
            border: 1px solid rgba(40, 167, 69, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1e7e34;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .table thead th {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            border: none;
            font-weight: 500;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.4);
        }

        .status-inactive {
            background: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.4);
        }

        .status-scheduled {
            background: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(30, 126, 52, 0.1));
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.1));
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            transition: width 0.3s ease;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            border: none;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="processes-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-cogs me-3"></i>Configuración de Procesos
                    </h1>
                    <p class="mb-0 opacity-90">Gestiona los procesos automatizados y flujos de trabajo del sistema</p>
                </div>
                <a href="{{ route('configuracion.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Pestañas de navegación -->
        <div class="process-card">
            <ul class="nav nav-tabs" id="processTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="automation-tab" data-bs-toggle="tab" data-bs-target="#automation"
                        type="button" role="tab">
                        <i class="fas fa-robot me-2"></i>Automatización
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring"
                        type="button" role="tab">
                        <i class="fas fa-chart-line me-2"></i>Monitoreo
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4" id="processTabContent">
                <!-- Tab General -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row">
                        <!-- Configuración General -->
                        <div class="col-md-6">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon general-icon">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    Configuración General
                                </h5>

                                <form action="{{ route('configuracion.procesos.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="general">

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="procesos_automaticos"
                                            id="procesos_automaticos"
                                            {{ $configuracion['procesos_automaticos'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="procesos_automaticos">
                                            Habilitar procesos automáticos
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="frecuencia_ejecucion" class="form-label">Frecuencia de ejecución</label>
                                        <select class="form-select" name="frecuencia_ejecucion" id="frecuencia_ejecucion">
                                            <option value="minutos"
                                                {{ ($configuracion['frecuencia_ejecucion'] ?? 'horas') == 'minutos' ? 'selected' : '' }}>
                                                Cada 15 minutos</option>
                                            <option value="horas"
                                                {{ ($configuracion['frecuencia_ejecucion'] ?? 'horas') == 'horas' ? 'selected' : '' }}>
                                                Cada hora</option>
                                            <option value="diario"
                                                {{ ($configuracion['frecuencia_ejecucion'] ?? 'horas') == 'diario' ? 'selected' : '' }}>
                                                Diario</option>
                                            <option value="semanal"
                                                {{ ($configuracion['frecuencia_ejecucion'] ?? 'horas') == 'semanal' ? 'selected' : '' }}>
                                                Semanal</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="max_procesos_concurrentes" class="form-label">Máximo procesos
                                            concurrentes</label>
                                        <input type="number" class="form-control" name="max_procesos_concurrentes"
                                            id="max_procesos_concurrentes"
                                            value="{{ $configuracion['max_procesos_concurrentes'] ?? 5 }}" min="1"
                                            max="20">
                                        <small class="text-muted">Número máximo de procesos que pueden ejecutarse
                                            simultáneamente</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="timeout_proceso" class="form-label">Timeout por proceso
                                            (minutos)</label>
                                        <input type="number" class="form-control" name="timeout_proceso"
                                            id="timeout_proceso" value="{{ $configuracion['timeout_proceso'] ?? 30 }}"
                                            min="5" max="120">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="col-md-6">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon monitoring-icon">
                                        <i class="fas fa-chart-bar"></i>
                                    </span>
                                    Estadísticas de Procesos
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['total_procesos'] ?? 0) ? $configuracion['total_procesos'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Total Procesos</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['procesos_activos'] ?? 0) ? $configuracion['procesos_activos'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Activos</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['procesos_completados_hoy'] ?? 0) ? $configuracion['procesos_completados_hoy'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Completados Hoy</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['procesos_fallidos'] ?? 0) ? $configuracion['procesos_fallidos'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Fallidos</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Rendimiento del Sistema</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ is_numeric($configuracion['rendimiento_sistema'] ?? 85) ? $configuracion['rendimiento_sistema'] ?? 85 : 85 }}%">
                                        </div>
                                    </div>
                                    <small
                                        class="text-muted">{{ is_numeric($configuracion['rendimiento_sistema'] ?? 85) ? $configuracion['rendimiento_sistema'] ?? 85 : 85 }}%
                                        de eficiencia</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Automatización -->
                <div class="tab-pane fade" id="automation" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon automation-icon">
                                        <i class="fas fa-robot"></i>
                                    </span>
                                    Procesos Automatizados
                                </h5>

                                @if (!empty($configuracion['procesos_automatizados']) && is_array($configuracion['procesos_automatizados']))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Proceso</th>
                                                    <th>Frecuencia</th>
                                                    <th>Última Ejecución</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['procesos_automatizados'] as $proceso)
                                                    <tr>
                                                        <td class="fw-bold">
                                                            {{ is_string($proceso['nombre'] ?? '') ? $proceso['nombre'] ?? 'Sin nombre' : 'Sin nombre' }}
                                                        </td>
                                                        <td>{{ is_string($proceso['frecuencia'] ?? '') ? $proceso['frecuencia'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td>{{ is_string($proceso['ultima_ejecucion'] ?? '') ? $proceso['ultima_ejecucion'] ?? 'Nunca' : 'Nunca' }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="status-indicator {{ $proceso['activo'] ?? false ? 'status-active' : 'status-inactive' }}"></span>
                                                            <span
                                                                class="badge {{ $proceso['activo'] ?? false ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $proceso['activo'] ?? false ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-success btn-sm me-1"
                                                                onclick="ejecutarProceso('{{ is_string($proceso['id'] ?? '') ? $proceso['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                            @if ($proceso['activo'] ?? false)
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="detenerProceso('{{ is_string($proceso['id'] ?? '') ? $proceso['id'] ?? '' : '' }}')">
                                                                    <i class="fas fa-stop"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-primary btn-sm"
                                                                    onclick="activarProceso('{{ is_string($proceso['id'] ?? '') ? $proceso['id'] ?? '' : '' }}')">
                                                                    <i class="fas fa-power-off"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay procesos automatizados configurados</p>
                                        <button class="btn btn-primary" onclick="crearProceso()">
                                            <i class="fas fa-plus me-2"></i>Crear Proceso
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon scheduler-icon">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    Programador de Tareas
                                </h5>

                                <form action="{{ route('configuracion.procesos.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="programar">
                                    <div class="mb-3">
                                        <label for="nombre_tarea" class="form-label">Nombre de la tarea</label>
                                        <input type="text" class="form-control" name="nombre_tarea" id="nombre_tarea"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tipo_tarea" class="form-label">Tipo de tarea</label>
                                        <select class="form-select" name="tipo_tarea" id="tipo_tarea" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="backup">Respaldo de datos</option>
                                            <option value="limpieza">Limpieza de archivos</option>
                                            <option value="reporte">Generación de reportes</option>
                                            <option value="notificacion">Envío de notificaciones</option>
                                            <option value="custom">Personalizada</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="horario" class="form-label">Horario de ejecución</label>
                                        <input type="time" class="form-control" name="horario" id="horario"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="dias_semana" class="form-label">Días de la semana</label>
                                        <select class="form-select" name="dias_semana[]" id="dias_semana" multiple>
                                            <option value="1">Lunes</option>
                                            <option value="2">Martes</option>
                                            <option value="3">Miércoles</option>
                                            <option value="4">Jueves</option>
                                            <option value="5">Viernes</option>
                                            <option value="6">Sábado</option>
                                            <option value="0">Domingo</option>
                                        </select>
                                        <small class="text-muted">Mantén Ctrl presionado para seleccionar múltiples
                                            días</small>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-calendar-plus me-2"></i>Programar Tarea
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Monitoreo -->
                <div class="tab-pane fade" id="monitoring" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon monitoring-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </span>
                                    Monitoreo en Tiempo Real
                                </h5>

                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-number text-primary">
                                                {{ is_numeric($configuracion['cpu_usage'] ?? 0) ? $configuracion['cpu_usage'] ?? 0 : 0 }}%
                                            </div>
                                            <div class="stats-label">Uso de CPU</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-number text-info">
                                                {{ is_numeric($configuracion['memory_usage'] ?? 0) ? $configuracion['memory_usage'] ?? 0 : 0 }}%
                                            </div>
                                            <div class="stats-label">Uso de Memoria</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-number text-warning">
                                                {{ is_numeric($configuracion['disk_usage'] ?? 0) ? $configuracion['disk_usage'] ?? 0 : 0 }}%
                                            </div>
                                            <div class="stats-label">Uso de Disco</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card">
                                            <div class="stats-number text-success">
                                                {{ is_string($configuracion['uptime'] ?? '0h') ? $configuracion['uptime'] ?? '0h' : '0h' }}
                                            </div>
                                            <div class="stats-label">Tiempo Activo</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logs de Procesos -->
                                @if (!empty($configuracion['logs_procesos']) && is_array($configuracion['logs_procesos']))
                                    <h6 class="mb-3">Registro de Actividad</h6>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Fecha/Hora</th>
                                                    <th>Proceso</th>
                                                    <th>Tipo</th>
                                                    <th>Estado</th>
                                                    <th>Duración</th>
                                                    <th>Mensaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['logs_procesos'] as $log)
                                                    <tr>
                                                        <td>{{ is_string($log['fecha_hora'] ?? '') ? $log['fecha_hora'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td class="fw-bold">
                                                            {{ is_string($log['proceso'] ?? '') ? $log['proceso'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td>{{ is_string($log['tipo'] ?? '') ? $log['tipo'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $estado = is_string($log['estado'] ?? '')
                                                                    ? $log['estado'] ?? 'desconocido'
                                                                    : 'desconocido';
                                                            @endphp
                                                            <span
                                                                class="badge {{ $estado == 'completado' ? 'bg-success' : ($estado == 'error' ? 'bg-danger' : 'bg-warning') }}">
                                                                {{ ucfirst($estado) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ is_string($log['duracion'] ?? '') ? $log['duracion'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td>{{ is_string($log['mensaje'] ?? '') ? $log['mensaje'] ?? 'Sin mensaje' : 'Sin mensaje' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay registros de actividad disponibles</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function ejecutarProceso(procesoId) {
            if (confirm('¿Estás seguro de que deseas ejecutar este proceso ahora?')) {
                fetch('{{ route('configuracion.procesos.ejecutar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            proceso_id: procesoId,
                            accion: 'ejecutar'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Proceso ejecutado exitosamente');
                            location.reload();
                        } else {
                            alert('Error al ejecutar el proceso: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }

        function detenerProceso(procesoId) {
            if (confirm('¿Estás seguro de que deseas detener este proceso?')) {
                fetch('{{ route('configuracion.procesos.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            proceso_id: procesoId,
                            seccion: 'detener',
                            accion: 'detener'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Proceso detenido exitosamente');
                            location.reload();
                        } else {
                            alert('Error al detener el proceso: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }

        function activarProceso(procesoId) {
            if (confirm('¿Estás seguro de que deseas activar este proceso?')) {
                fetch('{{ route('configuracion.procesos.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            proceso_id: procesoId,
                            seccion: 'activar',
                            accion: 'activar'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Proceso activado exitosamente');
                            location.reload();
                        } else {
                            alert('Error al activar el proceso: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }

        function crearProceso() {
            // Redirigir a la página de creación de procesos o abrir modal
            alert('Funcionalidad de creación de procesos - Por implementar');
        }

        // Auto-refresh para el monitoreo en tiempo real
        if (document.querySelector('#monitoring').classList.contains('show')) {
            setInterval(function() {
                // Aquí se puede implementar la actualización automática de estadísticas
                console.log('Actualizando estadísticas de monitoreo...');
            }, 30000); // Cada 30 segundos
        }
    </script>
@endpush
