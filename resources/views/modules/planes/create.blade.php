@extends('layouts.dashboard')

@section('title', 'Crear Nuevo Plan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('planes.index') }}">Planes</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
    <form id="plan-form" action="{{ route('planes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

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
                                        id="titulo" name="titulo" value="{{ old('titulo') }}" required
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
                                        id="codigo" name="codigo" value="{{ old('codigo') }}"
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
                                            {{ old('tipo') == 'accion_correctiva' ? 'selected' : '' }}>
                                            Acción Correctiva
                                        </option>
                                        <option value="accion_preventiva"
                                            {{ old('tipo') == 'accion_preventiva' ? 'selected' : '' }}>
                                            Acción Preventiva
                                        </option>
                                        <option value="mejora_continua"
                                            {{ old('tipo') == 'mejora_continua' ? 'selected' : '' }}>
                                            Mejora Continua
                                        </option>
                                        <option value="capacitacion" {{ old('tipo') == 'capacitacion' ? 'selected' : '' }}>
                                            Capacitación
                                        </option>
                                        <option value="inspeccion" {{ old('tipo') == 'inspeccion' ? 'selected' : '' }}>
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
                                        <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>
                                            <span class="badge bg-success">Baja</span>
                                        </option>
                                        <option value="media" {{ old('prioridad') == 'media' ? 'selected' : '' }}>
                                            <span class="badge bg-warning">Media</span>
                                        </option>
                                        <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>
                                            <span class="badge bg-orange">Alta</span>
                                        </option>
                                        <option value="critica" {{ old('prioridad') == 'critica' ? 'selected' : '' }}>
                                            <span class="badge bg-danger">Crítica</span>
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
                                rows="4" required placeholder="Describa detalladamente el plan a implementar">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="objetivo" class="form-label">
                                Objetivo <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('objetivo') is-invalid @enderror" id="objetivo" name="objetivo" rows="3"
                                required placeholder="Defina el objetivo que se busca alcanzar con este plan">{{ old('objetivo') }}</textarea>
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
                                        id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
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
                                        id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}" required>
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
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label">Descripción de la Tarea</label>
                                                    <input type="text" class="form-control"
                                                        name="tareas[0][descripcion]"
                                                        placeholder="Describa la tarea a realizar" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Fecha Límite</label>
                                                    <input type="date" class="form-control"
                                                        name="tareas[0][fecha_limite]" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Responsable</label>
                                                    <select class="form-select task-responsable"
                                                        name="tareas[0][responsable_id]" required>
                                                        <option value="">Seleccione el responsable</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Prioridad</label>
                                                    <select class="form-select" name="tareas[0][prioridad]" required>
                                                        <option value="baja">Baja</option>
                                                        <option value="media" selected>Media</option>
                                                        <option value="alta">Alta</option>
                                                        <option value="critica">Crítica</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notas</label>
                                            <textarea class="form-control" name="tareas[0][notas]" rows="2"
                                                placeholder="Información adicional sobre la tarea"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        <div class="mb-3">
                            <label for="archivos" class="form-label">
                                Subir Archivos <span class="text-muted">(Opcional)</span>
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
                                rows="4" placeholder="Agregue cualquier observación adicional relevante para el plan">{{ old('observaciones') }}</textarea>
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
                        <button type="submit" name="action" value="draft" class="btn btn-warning">
                            <i class="fas fa-save"></i> Guardar como Borrador
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar a Revisión
                        </button>
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

        .required {
            color: #dc3545;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let taskCounter = 1;

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

            // Load companies
            function loadCompanies() {
                $.get('/api/empresas')
                    .done(function(data) {
                        const empresaSelect = $('#empresa_id');
                        empresaSelect.empty().append('<option value="">Seleccione la empresa</option>');

                        if (data.success && data.empresas) {
                            data.empresas.forEach(function(empresa) {
                                empresaSelect.append(
                                    `<option value="${empresa.id}">${empresa.nombre}</option>`);
                            });
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
                                    responsableSelect.append(option);
                                    colaboradoresSelect.append(option);
                                    taskResponsables.append(option);
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Responsable</label>
                                            <select class="form-select task-responsable" 
                                                    name="tareas[${taskCounter}][responsable_id]" required>
                                                <option value="">Seleccione el responsable</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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

            // Form validation
            $('#plan-form').submit(function(e) {
                const fechaInicio = new Date($('#fecha_inicio').val());
                const fechaFin = new Date($('#fecha_fin').val());

                if (fechaInicio >= fechaFin) {
                    e.preventDefault();
                    Swal.fire('Error', 'La fecha de fin debe ser posterior a la fecha de inicio', 'error');
                    return false;
                }

                // Validate tasks
                let tasksValid = true;
                $('.task-item').each(function() {
                    const descripcion = $(this).find('input[name*="[descripcion]"]').val();
                    const fechaLimite = $(this).find('input[name*="[fecha_limite]"]').val();
                    const responsable = $(this).find('select[name*="[responsable_id]"]').val();

                    if (!descripcion || !fechaLimite || !responsable) {
                        tasksValid = false;
                        return false;
                    }

                    const fechaLimiteDate = new Date(fechaLimite);
                    if (fechaLimiteDate < fechaInicio || fechaLimiteDate > fechaFin) {
                        tasksValid = false;
                        Swal.fire('Error',
                            'Las fechas límite de las tareas deben estar dentro del rango del plan',
                            'error');
                        return false;
                    }
                });

                if (!tasksValid) {
                    e.preventDefault();
                    if (tasksValid !== false) {
                        Swal.fire('Error', 'Por favor complete todos los campos requeridos de las tareas',
                            'error');
                    }
                    return false;
                }
            });

            // Initialize everything
            initializeSelect2();
            loadCompanies();

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_inicio, #fecha_fin').attr('min', today);
        });
    </script>
@endpush
