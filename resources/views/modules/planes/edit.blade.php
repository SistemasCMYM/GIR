@extends('layouts.dashboard')

@section('title', 'Editar Plan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('planes.index') }}">Planes</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <!-- Estado del Plan -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Estado del Plan
                    </h3>
                    <div class="card-tools">
                        <span
                            class="badge badge-{{ $plan->estado == 'completado' ? 'success' : ($plan->estado == 'en_progreso' ? 'warning' : 'secondary') }} badge-lg">
                            {{ ucfirst(str_replace('_', ' ', $plan->estado ?? 'borrador')) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>Progreso General</h5>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped" role="progressbar"
                                        style="width: {{ $plan->progreso ?? 0 }}%"
                                        aria-valuenow="{{ $plan->progreso ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $plan->progreso ?? 0 }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Creación:</strong><br>
                            {{ isset($plan->created_at) ? $plan->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Última Actualización:</strong><br>
                            {{ isset($plan->updated_at) ? $plan->updated_at->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Creado por:</strong><br>
                            {{ $plan->creado_por ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="plan-form" action="{{ route('planes.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Información Básica -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información Básica
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
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">
                                        Título del Plan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('titulo') is-invalid @enderror"
                                        id="titulo" name="titulo" value="{{ old('titulo', $plan->titulo) }}" required
                                        placeholder="Ingrese el título del plan">
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="codigo" class="form-label">
                                        Código <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                        id="codigo" name="codigo" value="{{ old('codigo', $plan->codigo) }}"
                                        placeholder="Ej: PL-2024-001">
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">
                                        Tipo de Plan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('tipo') is-invalid @enderror" id="tipo"
                                        name="tipo" required>
                                        <option value="">Seleccione el tipo</option>
                                        <option value="accion_correctiva"
                                            {{ old('tipo', $plan->tipo) == 'accion_correctiva' ? 'selected' : '' }}>
                                            Acción Correctiva
                                        </option>
                                        <option value="accion_preventiva"
                                            {{ old('tipo', $plan->tipo) == 'accion_preventiva' ? 'selected' : '' }}>
                                            Acción Preventiva
                                        </option>
                                        <option value="mejora_continua"
                                            {{ old('tipo', $plan->tipo) == 'mejora_continua' ? 'selected' : '' }}>
                                            Mejora Continua
                                        </option>
                                        <option value="capacitacion"
                                            {{ old('tipo', $plan->tipo) == 'capacitacion' ? 'selected' : '' }}>
                                            Capacitación
                                        </option>
                                        <option value="inspeccion"
                                            {{ old('tipo', $plan->tipo) == 'inspeccion' ? 'selected' : '' }}>
                                            Inspección
                                        </option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prioridad" class="form-label">
                                        Prioridad <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('prioridad') is-invalid @enderror" id="prioridad"
                                        name="prioridad" required>
                                        <option value="">Seleccione la prioridad</option>
                                        <option value="baja"
                                            {{ old('prioridad', $plan->prioridad) == 'baja' ? 'selected' : '' }}>
                                            Baja
                                        </option>
                                        <option value="media"
                                            {{ old('prioridad', $plan->prioridad) == 'media' ? 'selected' : '' }}>
                                            Media
                                        </option>
                                        <option value="alta"
                                            {{ old('prioridad', $plan->prioridad) == 'alta' ? 'selected' : '' }}>
                                            Alta
                                        </option>
                                        <option value="critica"
                                            {{ old('prioridad', $plan->prioridad) == 'critica' ? 'selected' : '' }}>
                                            Crítica
                                        </option>
                                    </select>
                                    @error('prioridad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                rows="4" required placeholder="Describa detalladamente el plan a implementar">{{ old('descripcion', $plan->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="objetivo" class="form-label">
                                Objetivo <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('objetivo') is-invalid @enderror" id="objetivo" name="objetivo" rows="3"
                                required placeholder="Defina el objetivo que se busca alcanzar con este plan">{{ old('objetivo', $plan->objetivo) }}</textarea>
                            @error('objetivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fechas y Responsabilidad -->
        <div class="row">
            <div class="col-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt"></i> Fechas y Responsabilidad
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">
                                        Fecha de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
                                        id="fecha_inicio" name="fecha_inicio"
                                        value="{{ old('fecha_inicio', isset($plan->fecha_inicio) ? $plan->fecha_inicio->format('Y-m-d') : '') }}"
                                        required>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_fin" class="form-label">
                                        Fecha de Finalización <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror"
                                        id="fecha_fin" name="fecha_fin"
                                        value="{{ old('fecha_fin', isset($plan->fecha_fin) ? $plan->fecha_fin->format('Y-m-d') : '') }}"
                                        required>
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="responsable_id" class="form-label">
                                        Responsable Principal <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('responsable_id') is-invalid @enderror"
                                        id="responsable_id" name="responsable_id" required>
                                        <option value="">Seleccione el responsable</option>
                                        <!-- Options will be loaded via AJAX -->
                                    </select>
                                    @error('responsable_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa_id" class="form-label">
                                        Empresa <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('empresa_id') is-invalid @enderror" id="empresa_id"
                                        name="empresa_id" required>
                                        <option value="">Seleccione la empresa</option>
                                        <!-- Options will be loaded via AJAX -->
                                    </select>
                                    @error('empresa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="colaboradores" class="form-label">
                                Colaboradores <span class="text-muted">(Opcional)</span>
                            </label>
                            <select class="form-select @error('colaboradores') is-invalid @enderror" id="colaboradores"
                                name="colaboradores[]" multiple>
                                <!-- Options will be loaded via AJAX -->
                            </select>
                            <div class="form-text">Seleccione los colaboradores que participarán en el plan</div>
                            @error('colaboradores')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tareas del Plan -->
        <div class="row">
            <div class="col-12">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i> Tareas del Plan
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" id="add-task">
                                <i class="fas fa-plus"></i> Agregar Tarea
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="tasks-container">
                            @if (isset($plan->tareas) && is_array($plan->tareas) && count($plan->tareas) > 0)
                                @foreach ($plan->tareas as $index => $tarea)
                                    <div class="task-item" data-task="{{ $index }}">
                                        <div class="card card-secondary">
                                            <div class="card-header">
                                                <h5 class="card-title">Tarea {{ $index + 1 }}</h5>
                                                <div class="card-tools">
                                                    @if (isset($tarea['estado']))
                                                        <span
                                                            class="badge badge-{{ $tarea['estado'] == 'completada' ? 'success' : ($tarea['estado'] == 'en_progreso' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $tarea['estado'])) }}
                                                        </span>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger remove-task">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label">Descripción de la Tarea</label>
                                                            <input type="text" class="form-control"
                                                                name="tareas[{{ $index }}][descripcion]"
                                                                value="{{ $tarea['descripcion'] ?? '' }}"
                                                                placeholder="Describa la tarea a realizar" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Fecha Límite</label>
                                                            <input type="date" class="form-control"
                                                                name="tareas[{{ $index }}][fecha_limite]"
                                                                value="{{ isset($tarea['fecha_limite']) ? (is_string($tarea['fecha_limite']) ? $tarea['fecha_limite'] : $tarea['fecha_limite']->format('Y-m-d')) : '' }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Responsable</label>
                                                            <select class="form-select task-responsable"
                                                                name="tareas[{{ $index }}][responsable_id]"
                                                                required>
                                                                <option value="">Seleccione el responsable</option>
                                                            </select>
                                                            <input type="hidden" class="task-responsable-value"
                                                                value="{{ $tarea['responsable_id'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Prioridad</label>
                                                            <select class="form-select"
                                                                name="tareas[{{ $index }}][prioridad]" required>
                                                                <option value="baja"
                                                                    {{ ($tarea['prioridad'] ?? '') == 'baja' ? 'selected' : '' }}>
                                                                    Baja</option>
                                                                <option value="media"
                                                                    {{ ($tarea['prioridad'] ?? 'media') == 'media' ? 'selected' : '' }}>
                                                                    Media</option>
                                                                <option value="alta"
                                                                    {{ ($tarea['prioridad'] ?? '') == 'alta' ? 'selected' : '' }}>
                                                                    Alta</option>
                                                                <option value="critica"
                                                                    {{ ($tarea['prioridad'] ?? '') == 'critica' ? 'selected' : '' }}>
                                                                    Crítica</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Estado</label>
                                                            <select class="form-select"
                                                                name="tareas[{{ $index }}][estado]">
                                                                <option value="pendiente"
                                                                    {{ ($tarea['estado'] ?? 'pendiente') == 'pendiente' ? 'selected' : '' }}>
                                                                    Pendiente</option>
                                                                <option value="en_progreso"
                                                                    {{ ($tarea['estado'] ?? '') == 'en_progreso' ? 'selected' : '' }}>
                                                                    En Progreso</option>
                                                                <option value="completada"
                                                                    {{ ($tarea['estado'] ?? '') == 'completada' ? 'selected' : '' }}>
                                                                    Completada</option>
                                                                <option value="cancelada"
                                                                    {{ ($tarea['estado'] ?? '') == 'cancelada' ? 'selected' : '' }}>
                                                                    Cancelada</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Notas</label>
                                                    <textarea class="form-control" name="tareas[{{ $index }}][notas]" rows="2"
                                                        placeholder="Información adicional sobre la tarea">{{ $tarea['notas'] ?? '' }}</textarea>
                                                </div>
                                                @if (isset($tarea['progreso']))
                                                    <div class="mb-3">
                                                        <label class="form-label">Progreso (%)</label>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <input type="number" class="form-control"
                                                                    name="tareas[{{ $index }}][progreso]"
                                                                    value="{{ $tarea['progreso'] ?? 0 }}" min="0"
                                                                    max="100" step="5">
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="progress mt-2">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: {{ $tarea['progreso'] ?? 0 }}%">
                                                                        {{ $tarea['progreso'] ?? 0 }}%
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="task-item" data-task="0">
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h5 class="card-title">Tarea 1</h5>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-sm btn-danger remove-task">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- ...existing task form fields... -->
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archivos Adjuntos -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success card-outline">
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
                        @if (isset($plan->archivos) && is_array($plan->archivos) && count($plan->archivos) > 0)
                            <div class="mb-3">
                                <h6>Archivos Existentes:</h6>
                                <div class="row" id="existing-files">
                                    @foreach ($plan->archivos as $index => $archivo)
                                        <div class="col-md-6 col-lg-4 mb-2" data-file-index="{{ $index }}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $archivo['nombre'] ?? 'Archivo' }}</h6>
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            Tamaño:
                                                            {{ isset($archivo['tamaño']) ? number_format($archivo['tamaño'] / 1024, 2) . ' KB' : 'N/A' }}
                                                        </small>
                                                    </p>
                                                    <button type="button" class="btn btn-sm btn-danger delete-file"
                                                        data-file-index="{{ $index }}">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="archivos" class="form-label">
                                Subir Nuevos Archivos <span class="text-muted">(Opcional)</span>
                            </label>
                            <input type="file" class="form-control @error('archivos.*') is-invalid @enderror"
                                id="archivos" name="archivos[]" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <div class="form-text">
                                Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Máximo 10MB por archivo.
                            </div>
                            @error('archivos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="file-preview" class="row"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comment-alt"></i> Observaciones
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">
                                Observaciones Adicionales <span class="text-muted">(Opcional)</span>
                            </label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones"
                                rows="4" placeholder="Agregue cualquier observación adicional relevante para el plan">{{ old('observaciones', $plan->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <a href="{{ route('planes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        @if (in_array($plan->estado ?? 'borrador', ['borrador', 'revision']))
                            <button type="submit" name="action" value="draft" class="btn btn-warning">
                                <i class="fas fa-save"></i> Guardar como Borrador
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Enviar a Revisión
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Plan
                            </button>
                        @endif
                        @if (in_array($plan->estado ?? 'borrador', ['revision']))
                            <button type="button" class="btn btn-success" id="approve-plan">
                                <i class="fas fa-check"></i> Aprobar Plan
                            </button>
                            <button type="button" class="btn btn-danger" id="reject-plan">
                                <i class="fas fa-times"></i> Rechazar Plan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .task-item {
            margin-bottom: 1rem;
        }

        .file-preview {
            margin-top: 10px;
        }

        .file-preview .card {
            margin-bottom: 10px;
        }

        .progress {
            height: 20px;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let taskCounter = {{ isset($plan->tareas) && is_array($plan->tareas) ? count($plan->tareas) : 1 }};
            const planData = @json($plan ?? []);

            // Initialize Select2
            function initializeSelect2() {
                $('#responsable_id, #empresa_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Seleccione una opción'
                });

                $('#colaboradores').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Seleccione colaboradores'
                });

                $('.task-responsable').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Seleccione el responsable'
                });
            }

            // Load companies and set current value
            function loadCompanies() {
                $.get('/api/empresas')
                    .done(function(data) {
                        const empresaSelect = $('#empresa_id');
                        empresaSelect.empty().append('<option value="">Seleccione la empresa</option>');

                        if (data.success && data.empresas) {
                            data.empresas.forEach(function(empresa) {
                                const selected = empresa.id == (planData.empresa_id || '') ?
                                    'selected' : '';
                                empresaSelect.append(
                                    `<option value="${empresa.id}" ${selected}>${empresa.nombre}</option>`
                                );
                            });
                        }

                        // Trigger change to load employees
                        if (planData.empresa_id) {
                            empresaSelect.trigger('change');
                        }
                    })
                    .fail(function() {
                        console.error('Error loading companies');
                    });
            }

            // Load employees based on company
            $('#empresa_id').change(function() {
                const empresaId = $(this).val();
                if (empresaId) {
                    $.get(`/api/empresas/${empresaId}/empleados`)
                        .done(function(data) {
                            const responsableSelect = $('#responsable_id');
                            const colaboradoresSelect = $('#colaboradores');
                            const taskResponsables = $('.task-responsable');

                            // Clear all selects
                            responsableSelect.empty().append(
                                '<option value="">Seleccione el responsable</option>');
                            colaboradoresSelect.empty();
                            taskResponsables.empty().append(
                                '<option value="">Seleccione el responsable</option>');

                            if (data.success && data.empleados) {
                                data.empleados.forEach(function(empleado) {
                                    const option =
                                        `<option value="${empleado.id}">${empleado.nombres} ${empleado.apellidos}</option>`;

                                    // Main responsable
                                    const responsableSelected = empleado.id == (planData
                                        .responsable_id || '') ? 'selected' : '';
                                    responsableSelect.append(
                                        `<option value="${empleado.id}" ${responsableSelected}>${empleado.nombres} ${empleado.apellidos}</option>`
                                    );

                                    // Colaboradores
                                    const colaboradorSelected = (planData.colaboradores || [])
                                        .includes(empleado.id) ? 'selected' : '';
                                    colaboradoresSelect.append(
                                        `<option value="${empleado.id}" ${colaboradorSelected}>${empleado.nombres} ${empleado.apellidos}</option>`
                                    );

                                    // Task responsables
                                    taskResponsables.append(option);
                                });

                                // Set task responsables
                                $('.task-responsable').each(function() {
                                    const hiddenValue = $(this).siblings(
                                        '.task-responsable-value').val();
                                    if (hiddenValue) {
                                        $(this).val(hiddenValue);
                                    }
                                });
                            }
                        })
                        .fail(function() {
                            console.error('Error loading employees');
                        });
                }
            });

            // Add task functionality
            $('#add-task').click(function() {
                const taskTemplate = `
                    <div class="task-item" data-task="${taskCounter}">
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h5 class="card-title">Tarea ${taskCounter + 1}</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-danger remove-task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Descripción de la Tarea</label>
                                            <input type="text" class="form-control" 
                                                   name="tareas[${taskCounter}][descripcion]" 
                                                   placeholder="Describa la tarea a realizar" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha Límite</label>
                                            <input type="date" class="form-control" 
                                                   name="tareas[${taskCounter}][fecha_limite]" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Responsable</label>
                                            <select class="form-select task-responsable" 
                                                    name="tareas[${taskCounter}][responsable_id]" required>
                                                <option value="">Seleccione el responsable</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Prioridad</label>
                                            <select class="form-select" name="tareas[${taskCounter}][prioridad]" required>
                                                <option value="baja">Baja</option>
                                                <option value="media" selected>Media</option>
                                                <option value="alta">Alta</option>
                                                <option value="critica">Crítica</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" name="tareas[${taskCounter}][estado]">
                                                <option value="pendiente" selected>Pendiente</option>
                                                <option value="en_progreso">En Progreso</option>
                                                <option value="completada">Completada</option>
                                                <option value="cancelada">Cancelada</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notas</label>
                                    <textarea class="form-control" name="tareas[${taskCounter}][notas]" rows="2"
                                              placeholder="Información adicional sobre la tarea"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#tasks-container').append(taskTemplate);

                // Initialize Select2 for new task
                $(`.task-item[data-task="${taskCounter}"] .task-responsable`).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Seleccione el responsable'
                });

                // Copy employees from main select to new task
                const mainEmployees = $('#responsable_id option');
                const newTaskSelect = $(`.task-item[data-task="${taskCounter}"] .task-responsable`);
                newTaskSelect.append(mainEmployees.clone());

                taskCounter++;
            });

            // Remove task functionality
            $(document).on('click', '.remove-task', function() {
                if ($('.task-item').length > 1) {
                    $(this).closest('.task-item').remove();
                    updateTaskNumbers();
                } else {
                    Swal.fire('Advertencia', 'Debe mantener al menos una tarea', 'warning');
                }
            });

            // Update task numbers
            function updateTaskNumbers() {
                $('.task-item').each(function(index) {
                    $(this).find('.card-title').text(`Tarea ${index + 1}`);
                });
            }

            // File preview functionality
            $('#archivos').change(function() {
                const files = this.files;
                const preview = $('#file-preview');
                preview.empty();

                if (files.length > 0) {
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);

                        preview.append(`
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">${file.name}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">Tamaño: ${fileSize} MB</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                }
            });

            // Delete existing file
            $(document).on('click', '.delete-file', function() {
                const fileIndex = $(this).data('file-index');
                const card = $(this).closest('[data-file-index]');

                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Esta acción eliminará el archivo permanentemente',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.index') }}/{{ $plan->id ?? '' }}/file`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}',
                                file_index: fileIndex
                            },
                            success: function(response) {
                                if (response.success) {
                                    card.remove();
                                    Swal.fire('Eliminado',
                                        'Archivo eliminado correctamente', 'success'
                                    );
                                } else {
                                    Swal.fire('Error', response.message ||
                                        'Error al eliminar el archivo', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al eliminar el archivo',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Progress update for tasks
            $(document).on('input', 'input[name*="[progreso]"]', function() {
                const value = $(this).val();
                $(this).closest('.row').find('.progress-bar').css('width', value + '%').text(value + '%');
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

            // Form validation
            $('#plan-form').submit(function(e) {
                const fechaInicio = new Date($('#fecha_inicio').val());
                const fechaFin = new Date($('#fecha_fin').val());

                if (fechaInicio >= fechaFin) {
                    e.preventDefault();
                    Swal.fire('Error', 'La fecha de fin debe ser posterior a la fecha de inicio', 'error');
                    return false;
                }
            });

            // Initialize everything
            initializeSelect2();
            loadCompanies();
        });
    </script>
@endpush
