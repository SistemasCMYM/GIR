@extends('layouts.dashboard')

@section('title', 'Detalles del Plan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('planes.index') }}">Planes</a></li>
    <li class="breadcrumb-item active">Detalles</li>
@endsection

@section('content')
    <!-- Header with Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $plan->titulo ?? 'Sin título' }}
                    </h3>
                    <div class="card-tools">
                        <span
                            class="badge badge-{{ $plan->estado == 'completado' ? 'success' : ($plan->estado == 'en_progreso' ? 'warning' : ($plan->estado == 'aprobado' ? 'info' : 'secondary')) }} badge-lg">
                            {{ ucfirst(str_replace('_', ' ', $plan->estado ?? 'borrador')) }}
                        </span>
                        <div class="btn-group ml-2">
                            <a href="{{ route('planes.edit', $plan->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-info btn-sm" id="export-pdf">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Acciones
                                </button>
                                <ul class="dropdown-menu">
                                    @if (in_array($plan->estado ?? 'borrador', ['borrador', 'revision']))
                                        <li><a class="dropdown-item" href="#" id="submit-plan">
                                                <i class="fas fa-paper-plane"></i> Enviar a Revisión
                                            </a></li>
                                    @endif
                                    @if ($plan->estado == 'revision')
                                        <li><a class="dropdown-item" href="#" id="approve-plan">
                                                <i class="fas fa-check"></i> Aprobar
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" id="reject-plan">
                                                <i class="fas fa-times"></i> Rechazar
                                            </a></li>
                                    @endif
                                    @if (in_array($plan->estado ?? 'borrador', ['aprobado', 'en_progreso']))
                                        <li><a class="dropdown-item" href="#" id="update-progress">
                                                <i class="fas fa-tasks"></i> Actualizar Progreso
                                            </a></li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="#" id="delete-plan">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $plan->tipo ?? 'N/A')) }}<br>
                            <strong>Prioridad:</strong>
                            <span class="badge badge-prioridad-{{ $plan->prioridad ?? 'media' }}">
                                {{ ucfirst($plan->prioridad ?? 'Media') }}
                            </span><br>
                            <strong>Responsable:</strong> {{ $plan->responsable_nombre ?? 'No asignado' }}<br>
                            <strong>Empresa:</strong> {{ $plan->empresa_nombre ?? 'No asignada' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha Inicio:</strong>
                            {{ isset($plan->fecha_inicio) ? $plan->fecha_inicio->format('d/m/Y') : 'N/A' }}<br>
                            <strong>Fecha Fin:</strong>
                            {{ isset($plan->fecha_fin) ? $plan->fecha_fin->format('d/m/Y') : 'N/A' }}<br>
                            <strong>Creado:</strong>
                            {{ isset($plan->created_at) ? $plan->created_at->format('d/m/Y H:i') : 'N/A' }}<br>
                            <strong>Actualizado:</strong>
                            {{ isset($plan->updated_at) ? $plan->updated_at->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Progreso General
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: {{ $plan->progreso ?? 0 }}%" aria-valuenow="{{ $plan->progreso ?? 0 }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ $plan->progreso ?? 0 }}% Completado
                                </div>
                            </div>
                            @if (isset($plan->tareas) && is_array($plan->tareas))
                                @php
                                    $totalTareas = count($plan->tareas);
                                    $tareasCompletadas = 0;
                                    $tareasEnProgreso = 0;
                                    $tareasPendientes = 0;

                                    foreach ($plan->tareas as $tarea) {
                                        switch ($tarea['estado'] ?? 'pendiente') {
                                            case 'completada':
                                                $tareasCompletadas++;
                                                break;
                                            case 'en_progreso':
                                                $tareasEnProgreso++;
                                                break;
                                            default:
                                                $tareasPendientes++;
                                        }
                                    }
                                @endphp
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="description-block">
                                            <h5 class="description-header text-success">{{ $tareasCompletadas }}</h5>
                                            <span class="description-text">COMPLETADAS</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="description-block">
                                            <h5 class="description-header text-warning">{{ $tareasEnProgreso }}</h5>
                                            <span class="description-text">EN PROGRESO</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="description-block">
                                            <h5 class="description-header text-info">{{ $tareasPendientes }}</h5>
                                            <span class="description-text">PENDIENTES</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ $totalTareas }}</h5>
                                            <span class="description-text">TOTAL</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <canvas id="progressChart" width="200" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Information Panel -->
        <div class="col-md-8">
            <!-- Description and Objective -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Descripción y Objetivo
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><strong>Descripción:</strong></h6>
                        <p>{{ $plan->descripcion ?? 'No hay descripción disponible.' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6><strong>Objetivo:</strong></h6>
                        <p>{{ $plan->objetivo ?? 'No hay objetivo definido.' }}</p>
                    </div>
                    @if (isset($plan->observaciones) && !empty($plan->observaciones))
                        <div class="mb-3">
                            <h6><strong>Observaciones:</strong></h6>
                            <p>{{ $plan->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tasks -->
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Tareas del Plan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addTaskModal">
                            <i class="fas fa-plus"></i> Agregar Tarea
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (isset($plan->tareas) && is_array($plan->tareas) && count($plan->tareas) > 0)
                        <div class="timeline">
                            @foreach ($plan->tareas as $index => $tarea)
                                <div class="time-label">
                                    <span
                                        class="bg-{{ $tarea['estado'] == 'completada' ? 'success' : ($tarea['estado'] == 'en_progreso' ? 'warning' : 'secondary') }}">
                                        Tarea {{ $index + 1 }}
                                    </span>
                                </div>
                                <div>
                                    <i
                                        class="fas fa-{{ $tarea['estado'] == 'completada' ? 'check-circle' : ($tarea['estado'] == 'en_progreso' ? 'clock' : 'circle') }} bg-{{ $tarea['estado'] == 'completada' ? 'success' : ($tarea['estado'] == 'en_progreso' ? 'warning' : 'secondary') }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ isset($tarea['fecha_limite']) ? (is_string($tarea['fecha_limite']) ? $tarea['fecha_limite'] : $tarea['fecha_limite']->format('d/m/Y')) : 'Sin fecha' }}
                                        </span>
                                        <h3 class="timeline-header">
                                            {{ $tarea['descripcion'] ?? 'Sin descripción' }}
                                            <span class="badge badge-prioridad-{{ $tarea['prioridad'] ?? 'media' }} ml-2">
                                                {{ ucfirst($tarea['prioridad'] ?? 'Media') }}
                                            </span>
                                        </h3>
                                        <div class="timeline-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Responsable:</strong>
                                                    {{ $tarea['responsable_nombre'] ?? 'No asignado' }}<br>
                                                    <strong>Estado:</strong>
                                                    <span
                                                        class="badge badge-{{ $tarea['estado'] == 'completada' ? 'success' : ($tarea['estado'] == 'en_progreso' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $tarea['estado'] ?? 'pendiente')) }}
                                                    </span>
                                                </div>
                                                <div class="col-md-6">
                                                    @if (isset($tarea['progreso']))
                                                        <strong>Progreso:</strong>
                                                        <div class="progress mt-1">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $tarea['progreso'] }}%">
                                                                {{ $tarea['progreso'] }}%
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if (isset($tarea['notas']) && !empty($tarea['notas']))
                                                <div class="mt-2">
                                                    <strong>Notas:</strong> {{ $tarea['notas'] }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="timeline-footer">
                                            <button class="btn btn-sm btn-primary edit-task"
                                                data-task-index="{{ $index }}">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            @if ($tarea['estado'] != 'completada')
                                                <button class="btn btn-sm btn-success complete-task"
                                                    data-task-index="{{ $index }}">
                                                    <i class="fas fa-check"></i> Completar
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-danger delete-task"
                                                data-task-index="{{ $index }}">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay tareas definidas</h5>
                            <p class="text-muted">Agregue tareas para organizar la ejecución del plan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Team -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Equipo de Trabajo
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><strong>Responsable Principal:</strong></h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                {{ substr($plan->responsable_nombre ?? 'NA', 0, 2) }}
                            </div>
                            <span>{{ $plan->responsable_nombre ?? 'No asignado' }}</span>
                        </div>
                    </div>

                    @if (isset($plan->colaboradores) && is_array($plan->colaboradores) && count($plan->colaboradores) > 0)
                        <div class="mb-3">
                            <h6><strong>Colaboradores:</strong></h6>
                            @foreach ($plan->colaboradores as $colaborador)
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar avatar-sm bg-info text-white rounded-circle me-2">
                                        {{ substr($colaborador['nombre'] ?? 'NA', 0, 2) }}
                                    </div>
                                    <span>{{ $colaborador['nombre'] ?? 'Colaborador' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Files -->
            @if (isset($plan->archivos) && is_array($plan->archivos) && count($plan->archivos) > 0)
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-paperclip"></i> Archivos Adjuntos
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach ($plan->archivos as $archivo)
                            <div class="attachment-block clearfix mb-2">
                                <div class="attachment-img">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                                <div class="attachment-info">
                                    <a href="#"
                                        class="attachment-heading">{{ $archivo['nombre'] ?? 'Archivo' }}</a>
                                    <div class="attachment-meta">
                                        <small>{{ isset($archivo['tamaño']) ? number_format($archivo['tamaño'] / 1024, 2) . ' KB' : 'Tamaño desconocido' }}</small>
                                        <a href="#" class="float-right">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Timeline / History -->
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Historial de Cambios
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (isset($plan->historial) && is_array($plan->historial) && count($plan->historial) > 0)
                        <div class="timeline timeline-inverse">
                            @foreach ($plan->historial as $evento)
                                <div class="time-label">
                                    <span class="bg-secondary">
                                        {{ isset($evento['fecha']) ? $evento['fecha']->format('d/m/Y') : 'Fecha desconocida' }}
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-{{ $evento['icono'] ?? 'circle' }} bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i>
                                            {{ isset($evento['fecha']) ? $evento['fecha']->format('H:i') : '00:00' }}
                                        </span>
                                        <h3 class="timeline-header">{{ $evento['titulo'] ?? 'Evento' }}</h3>
                                        <div class="timeline-body">
                                            {{ $evento['descripcion'] ?? 'Sin descripción' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No hay historial disponible.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">
                        <i class="fas fa-plus"></i> Agregar Nueva Tarea
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-task-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="task-descripcion" class="form-label">Descripción <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="task-descripcion" name="descripcion"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="task-fecha-limite" class="form-label">Fecha Límite <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="task-fecha-limite"
                                        name="fecha_limite" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="task-prioridad" class="form-label">Prioridad <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="task-prioridad" name="prioridad" required>
                                        <option value="baja">Baja</option>
                                        <option value="media" selected>Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="critica">Crítica</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="task-responsable" class="form-label">Responsable <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="task-responsable" name="responsable_id" required>
                                <option value="">Seleccione el responsable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="task-notas" class="form-label">Notas</label>
                            <textarea class="form-control" id="task-notas" name="notas" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">
                        <i class="fas fa-edit"></i> Editar Tarea
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-task-form">
                    <div class="modal-body">
                        <input type="hidden" id="edit-task-index" name="task_index">
                        <div class="mb-3">
                            <label for="edit-task-descripcion" class="form-label">Descripción <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-task-descripcion" name="descripcion"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-task-fecha-limite" class="form-label">Fecha Límite <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit-task-fecha-limite"
                                        name="fecha_limite" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-task-prioridad" class="form-label">Prioridad <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit-task-prioridad" name="prioridad" required>
                                        <option value="baja">Baja</option>
                                        <option value="media">Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="critica">Crítica</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-task-responsable" class="form-label">Responsable <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit-task-responsable" name="responsable_id"
                                        required>
                                        <option value="">Seleccione el responsable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-task-estado" class="form-label">Estado <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit-task-estado" name="estado" required>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_progreso">En Progreso</option>
                                        <option value="completada">Completada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-task-progreso" class="form-label">Progreso (%)</label>
                            <input type="number" class="form-control" id="edit-task-progreso" name="progreso"
                                min="0" max="100" step="5" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit-task-notas" class="form-label">Notas</label>
                            <textarea class="form-control" id="edit-task-notas" name="notas" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .badge-prioridad-baja {
            background-color: #28a745;
            color: white;
        }

        .badge-prioridad-media {
            background-color: #ffc107;
            color: black;
        }

        .badge-prioridad-alta {
            background-color: #fd7e14;
            color: white;
        }

        .badge-prioridad-critica {
            background-color: #dc3545;
            color: white;
        }

        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 25px;
            height: 100%;
            width: 4px;
            background: #dee2e6;
        }

        .timeline .time-label {
            font-weight: 600;
            color: #495057;
            margin: 10px 0 10px 55px;
            background: #fff;
            display: inline-block;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .timeline .timeline-item {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            margin: 10px 0 10px 55px;
            position: relative;
        }

        .timeline .timeline-item:before {
            content: '';
            position: absolute;
            top: 10px;
            left: -15px;
            width: 0;
            height: 0;
            border: 8px solid transparent;
            border-right: 8px solid #dee2e6;
        }

        .timeline .timeline-item:after {
            content: '';
            position: absolute;
            top: 11px;
            left: -14px;
            width: 0;
            height: 0;
            border: 7px solid transparent;
            border-right: 7px solid #fff;
        }

        .timeline>div>.fas {
            position: absolute;
            left: 18px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            text-align: center;
            border: 2px solid #adb5bd;
            background: #f8f9fa;
            color: #495057;
            font-size: 10px;
            line-height: 11px;
        }

        .timeline .timeline-header {
            margin: 0;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 15px;
            font-size: 16px;
            line-height: 1.1;
        }

        .timeline .timeline-body {
            padding: 15px;
        }

        .timeline .timeline-footer {
            padding: 15px;
            background: #f8f9fa;
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
        }

        .timeline .time {
            color: #999;
            float: right;
            font-size: 12px;
        }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .attachment-block {
            border: 1px solid #dee2e6;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 3px;
        }

        .attachment-img {
            float: left;
            width: 50px;
            text-align: center;
            color: #6c757d;
        }

        .attachment-info {
            margin-left: 60px;
        }

        .attachment-heading {
            font-weight: 600;
            color: #495057;
            text-decoration: none;
        }

        .attachment-meta {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .description-block {
            margin: 10px 0;
        }

        .description-header {
            font-size: 1.875rem;
            margin: 0;
            font-weight: 400;
        }

        .description-text {
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.75rem;
            color: #6c757d;
        }
    </style>
@endpush

@push('js')
    <script src="//cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const planData = @json($plan ?? []);

            // Initialize progress chart
            function initializeProgressChart() {
                const ctx = document.getElementById('progressChart').getContext('2d');
                const progreso = planData.progreso || 0;

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completado', 'Pendiente'],
                        datasets: [{
                            data: [progreso, 100 - progreso],
                            backgroundColor: ['#28a745', '#e9ecef'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // Export to PDF
            $('#export-pdf').click(function() {
                window.open(`{{ route('planes.export-pdf', $plan->id ?? '') }}`, '_blank');
            });

            // Submit plan for review
            $('#submit-plan').click(function() {
                Swal.fire({
                    title: 'Enviar a Revisión',
                    text: '¿Está seguro de enviar este plan a revisión?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.submit', $plan->id ?? '') }}`,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Enviado', response.message, 'success')
                                        .then(() => {
                                            window.location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Ocurrió un error al enviar el plan',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Approve plan
            $('#approve-plan').click(function() {
                Swal.fire({
                    title: 'Aprobar Plan',
                    text: '¿Está seguro de aprobar este plan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.approve', $plan->id ?? '') }}`,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Aprobado', response.message, 'success')
                                        .then(() => {
                                            window.location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al aprobar el plan', 'error');
                            }
                        });
                    }
                });
            });

            // Reject plan
            $('#reject-plan').click(function() {
                Swal.fire({
                    title: 'Rechazar Plan',
                    input: 'textarea',
                    inputLabel: 'Motivo del rechazo',
                    inputPlaceholder: 'Explique el motivo del rechazo...',
                    inputAttributes: {
                        'aria-label': 'Motivo del rechazo'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Rechazar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Debe especificar el motivo del rechazo'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.reject', $plan->id ?? '') }}`,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                motivo: result.value
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Rechazado', response.message, 'success')
                                        .then(() => {
                                            window.location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al rechazar el plan', 'error');
                            }
                        });
                    }
                });
            });

            // Delete plan
            $('#delete-plan').click(function() {
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Esta acción eliminará el plan permanentemente',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.destroy', $plan->id ?? '') }}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Eliminado', response.message, 'success')
                                        .then(() => {
                                            window.location.href =
                                                '{{ route('planes.index') }}';
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al eliminar el plan', 'error');
                            }
                        });
                    }
                });
            });

            // Load employees for task modals
            function loadEmployees() {
                if (planData.empresa_id) {
                    $.get(`/api/empresas/${planData.empresa_id}/empleados`)
                        .done(function(data) {
                            const selects = $('#task-responsable, #edit-task-responsable');
                            selects.empty().append('<option value="">Seleccione el responsable</option>');

                            if (data.success && data.empleados) {
                                data.empleados.forEach(function(empleado) {
                                    selects.append(
                                        `<option value="${empleado.id}">${empleado.nombres} ${empleado.apellidos}</option>`
                                    );
                                });
                            }
                        })
                        .fail(function() {
                            console.error('Error loading employees');
                        });
                }
            }

            // Add task
            $('#add-task-form').submit(function(e) {
                e.preventDefault();

                const formData = {
                    _token: '{{ csrf_token() }}',
                    descripcion: $('#task-descripcion').val(),
                    fecha_limite: $('#task-fecha-limite').val(),
                    prioridad: $('#task-prioridad').val(),
                    responsable_id: $('#task-responsable').val(),
                    notas: $('#task-notas').val()
                };

                $.ajax({
                    url: `{{ route('planes.add-task', $plan->id ?? '') }}`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Agregado', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Ocurrió un error al agregar la tarea', 'error');
                    }
                });
            });

            // Edit task
            $(document).on('click', '.edit-task', function() {
                const taskIndex = $(this).data('task-index');
                const task = planData.tareas[taskIndex];

                if (task) {
                    $('#edit-task-index').val(taskIndex);
                    $('#edit-task-descripcion').val(task.descripcion || '');
                    $('#edit-task-fecha-limite').val(task.fecha_limite || '');
                    $('#edit-task-prioridad').val(task.prioridad || 'media');
                    $('#edit-task-responsable').val(task.responsable_id || '');
                    $('#edit-task-estado').val(task.estado || 'pendiente');
                    $('#edit-task-progreso').val(task.progreso || 0);
                    $('#edit-task-notas').val(task.notas || '');

                    $('#editTaskModal').modal('show');
                }
            });

            // Update task
            $('#edit-task-form').submit(function(e) {
                e.preventDefault();

                const taskIndex = $('#edit-task-index').val();
                const formData = {
                    _token: '{{ csrf_token() }}',
                    descripcion: $('#edit-task-descripcion').val(),
                    fecha_limite: $('#edit-task-fecha-limite').val(),
                    prioridad: $('#edit-task-prioridad').val(),
                    responsable_id: $('#edit-task-responsable').val(),
                    estado: $('#edit-task-estado').val(),
                    progreso: $('#edit-task-progreso').val(),
                    notas: $('#edit-task-notas').val()
                };

                $.ajax({
                    url: `{{ route('planes.update-task', ['id' => $plan->id ?? '', 'taskId' => '']) }}${taskIndex}`,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Actualizado', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Ocurrió un error al actualizar la tarea', 'error');
                    }
                });
            });

            // Complete task
            $(document).on('click', '.complete-task', function() {
                const taskIndex = $(this).data('task-index');

                Swal.fire({
                    title: 'Completar Tarea',
                    text: '¿Está seguro de marcar esta tarea como completada?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Completar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.update-task', ['id' => $plan->id ?? '', 'taskId' => '']) }}${taskIndex}`,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                estado: 'completada',
                                progreso: 100
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Completada', response.message, 'success')
                                        .then(() => {
                                            window.location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al completar la tarea',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Delete task
            $(document).on('click', '.delete-task', function() {
                const taskIndex = $(this).data('task-index');

                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Esta acción eliminará la tarea permanentemente',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.delete-task', ['id' => $plan->id ?? '', 'taskId' => '']) }}${taskIndex}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Eliminada', response.message, 'success')
                                        .then(() => {
                                            window.location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al eliminar la tarea', 'error'
                                );
                            }
                        });
                    }
                });
            });

            // Initialize everything
            initializeProgressChart();
            loadEmployees();

            // Set minimum date for new tasks
            const today = new Date().toISOString().split('T')[0];
            $('#task-fecha-limite, #edit-task-fecha-limite').attr('min', today);
        });
    </script>
@endpush
