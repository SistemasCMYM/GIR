@extends('layouts.dashboard')

@section('title', 'Crear Reporte')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <form id="createReporteForm" action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Información Básica</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="titulo">Título del Reporte *</label>
                                        <input type="text" class="form-control @error('titulo') is-invalid @enderror"
                                            id="titulo" name="titulo" value="{{ old('titulo') }}" required
                                            placeholder="Ingrese el título del reporte">
                                        @error('titulo')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipo">Tipo de Reporte *</label>
                                        <select class="form-control @error('tipo') is-invalid @enderror" id="tipo"
                                            name="tipo" required>
                                            <option value="">Seleccionar tipo...</option>
                                            <option value="sst" {{ old('tipo') == 'sst' ? 'selected' : '' }}>Seguridad y
                                                Salud en el Trabajo</option>
                                            <option value="ambiental" {{ old('tipo') == 'ambiental' ? 'selected' : '' }}>
                                                Ambiental</option>
                                            <option value="calidad" {{ old('tipo') == 'calidad' ? 'selected' : '' }}>Calidad
                                            </option>
                                            <option value="psicosocial"
                                                {{ old('tipo') == 'psicosocial' ? 'selected' : '' }}>Riesgo Psicosocial
                                            </option>
                                            <option value="capacitacion"
                                                {{ old('tipo') == 'capacitacion' ? 'selected' : '' }}>Capacitación</option>
                                            <option value="inspeccion" {{ old('tipo') == 'inspeccion' ? 'selected' : '' }}>
                                                Inspección</option>
                                        </select>
                                        @error('tipo')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                    rows="4" placeholder="Descripción detallada del reporte, objetivos y alcance...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa_id">Empresa *</label>
                                        <select class="form-control select2 @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id" required>
                                            <option value="">Seleccionar empresa...</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->_id }}"
                                                    {{ old('empresa_id') == $empresa->_id ? 'selected' : '' }}>
                                                    {{ $empresa->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('empresa_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="responsable_id">Responsable *</label>
                                        <select class="form-control select2 @error('responsable_id') is-invalid @enderror"
                                            id="responsable_id" name="responsable_id" required>
                                            <option value="">Seleccionar responsable...</option>
                                            @foreach ($usuarios as $usuario)
                                                <option value="{{ $usuario->_id }}"
                                                    {{ old('responsable_id') == $usuario->_id ? 'selected' : '' }}>
                                                    {{ $usuario->nombre }} {{ $usuario->apellido }} -
                                                    {{ $usuario->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('responsable_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule and Priority Card -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Programación y Prioridad</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecha_inicio">Fecha de Inicio</label>
                                        <input type="date"
                                            class="form-control @error('fecha_inicio') is-invalid @enderror"
                                            id="fecha_inicio" name="fecha_inicio"
                                            value="{{ old('fecha_inicio', date('Y-m-d')) }}">
                                        @error('fecha_inicio')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecha_limite">Fecha Límite</label>
                                        <input type="date"
                                            class="form-control @error('fecha_limite') is-invalid @enderror"
                                            id="fecha_limite" name="fecha_limite" value="{{ old('fecha_limite') }}">
                                        @error('fecha_limite')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="prioridad">Prioridad</label>
                                        <select class="form-control @error('prioridad') is-invalid @enderror" id="prioridad"
                                            name="prioridad">
                                            <option value="baja"
                                                {{ old('prioridad', 'media') == 'baja' ? 'selected' : '' }}>Baja</option>
                                            <option value="media"
                                                {{ old('prioridad', 'media') == 'media' ? 'selected' : '' }}>Media</option>
                                            <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta
                                            </option>
                                            <option value="critica" {{ old('prioridad') == 'critica' ? 'selected' : '' }}>
                                                Crítica</option>
                                        </select>
                                        @error('prioridad')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado">Estado Inicial</label>
                                        <select class="form-control @error('estado') is-invalid @enderror" id="estado"
                                            name="estado">
                                            <option value="borrador"
                                                {{ old('estado', 'borrador') == 'borrador' ? 'selected' : '' }}>Borrador
                                            </option>
                                            <option value="pendiente"
                                                {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="en_proceso"
                                                {{ old('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        </select>
                                        @error('estado')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="progreso">Progreso Inicial (%)</label>
                                        <input type="number" class="form-control @error('progreso') is-invalid @enderror"
                                            id="progreso" name="progreso" min="0" max="100"
                                            value="{{ old('progreso', '0') }}">
                                        @error('progreso')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content and Attachments Card -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Contenido y Archivos</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="contenido">Contenido del Reporte</label>
                                <textarea class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido"
                                    rows="8" placeholder="Escriba aquí el contenido detallado del reporte...">{{ old('contenido') }}</textarea>
                                @error('contenido')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="archivos">Archivos Adjuntos</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="archivos" name="archivos[]"
                                        multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                                    <label class="custom-file-label" for="archivos">Seleccionar archivos...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, JPEG. Tamaño máximo: 10MB por
                                    archivo.
                                </small>
                            </div>

                            <div id="files-preview" class="mt-3">
                                <!-- Selected files will be shown here -->
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" data-action="save">
                                <i class="fas fa-save"></i> Guardar Reporte
                            </button>
                            <button type="submit" class="btn btn-success" data-action="save-and-continue">
                                <i class="fas fa-arrow-right"></i> Guardar y Continuar
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <!-- Help Card -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Ayuda</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Consejos para crear reportes</h5>
                            <ul>
                                <li><strong>Título:</strong> Use un título descriptivo y claro</li>
                                <li><strong>Descripción:</strong> Incluya objetivos y alcance</li>
                                <li><strong>Contenido:</strong> Estructure la información de forma clara</li>
                                <li><strong>Archivos:</strong> Adjunte documentos de soporte</li>
                            </ul>
                        </div>

                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Reportes Activos</span>
                                <span class="info-box-number">{{ $stats['activos'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Templates Card -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Plantillas Disponibles</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <button type="button" class="list-group-item list-group-item-action"
                                onclick="loadTemplate('sst')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Reporte SST</h6>
                                    <small><i class="fas fa-hard-hat"></i></small>
                                </div>
                                <p class="mb-1">Plantilla para reportes de seguridad y salud en el trabajo</p>
                            </button>

                            <button type="button" class="list-group-item list-group-item-action"
                                onclick="loadTemplate('ambiental')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Reporte Ambiental</h6>
                                    <small><i class="fas fa-leaf"></i></small>
                                </div>
                                <p class="mb-1">Plantilla para reportes de gestión ambiental</p>
                            </button>

                            <button type="button" class="list-group-item list-group-item-action"
                                onclick="loadTemplate('inspeccion')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Reporte de Inspección</h6>
                                    <small><i class="fas fa-search"></i></small>
                                </div>
                                <p class="mb-1">Plantilla para reportes de inspección</p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Initialize summernote for content
            $('#contenido').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // File input change handler
            $('#archivos').on('change', function() {
                const files = this.files;
                const preview = $('#files-preview');
                const label = $('.custom-file-label');

                if (files.length === 0) {
                    label.text('Seleccionar archivos...');
                    preview.empty();
                    return;
                }

                label.text(`${files.length} archivo(s) seleccionado(s)`);

                let html = '<div class="row">';
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const size = (file.size / 1024 / 1024).toFixed(2); // MB
                    const icon = getFileIcon(file.type);

                    html += `
                <div class="col-md-6 mb-2">
                    <div class="card card-outline card-info">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <i class="${icon} fa-2x mr-2"></i>
                                <div class="flex-grow-1">
                                    <small><strong>${file.name}</strong></small><br>
                                    <small class="text-muted">${size} MB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                }
                html += '</div>';
                preview.html(html);
            });

            // Form submission
            $('#createReporteForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $(e.originalEvent.submitter);
                const action = submitBtn.data('action');
                const originalText = submitBtn.html();

                // Add action to form data
                form.append(`<input type="hidden" name="form_action" value="${action}">`);

                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...').prop('disabled',
                    true);

                // Create FormData for file upload
                const formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Reporte creado correctamente');

                            if (action === 'save-and-continue' && response.reporte_id) {
                                setTimeout(() => {
                                    window.location.href =
                                        `/reports/${response.reporte_id}/edit`;
                                }, 1500);
                            } else {
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('reports.index') }}';
                                }, 1500);
                            }
                        } else {
                            toastr.error(response.message || 'Error al crear el reporte');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error al crear el reporte';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            message = errors.join('<br>');
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                        $('input[name="form_action"]').remove();
                    }
                });
            });
        });

        function getFileIcon(fileType) {
            if (fileType.includes('pdf')) return 'fas fa-file-pdf text-danger';
            if (fileType.includes('word') || fileType.includes('document')) return 'fas fa-file-word text-primary';
            if (fileType.includes('excel') || fileType.includes('spreadsheet')) return 'fas fa-file-excel text-success';
            if (fileType.includes('image')) return 'fas fa-file-image text-warning';
            return 'fas fa-file text-secondary';
        }

        function loadTemplate(tipo) {
            const templates = {
                'sst': {
                    titulo: 'Reporte de Seguridad y Salud en el Trabajo',
                    descripcion: 'Reporte mensual de actividades y indicadores de SST',
                    contenido: `<h3>1. RESUMEN EJECUTIVO</h3>
<p>Resumen de las principales actividades y resultados del período.</p>

<h3>2. INDICADORES DE GESTIÓN</h3>
<ul>
<li>Índice de frecuencia de accidentalidad</li>
<li>Índice de severidad</li>
<li>Ausentismo laboral</li>
</ul>

<h3>3. ACTIVIDADES REALIZADAS</h3>
<p>Detalle de las actividades ejecutadas durante el período.</p>

<h3>4. HALLAZGOS Y OBSERVACIONES</h3>
<p>Identificación de riesgos y oportunidades de mejora.</p>

<h3>5. PLAN DE ACCIÓN</h3>
<p>Acciones a implementar en el siguiente período.</p>`
                },
                'ambiental': {
                    titulo: 'Reporte de Gestión Ambiental',
                    descripcion: 'Reporte de indicadores y actividades ambientales',
                    contenido: `<h3>1. ASPECTOS AMBIENTALES SIGNIFICATIVOS</h3>
<p>Identificación y evaluación de impactos ambientales.</p>

<h3>2. CONSUMOS Y GENERACIÓN</h3>
<ul>
<li>Consumo de agua</li>
<li>Consumo de energía</li>
<li>Generación de residuos</li>
<li>Emisiones atmosféricas</li>
</ul>

<h3>3. CUMPLIMIENTO LEGAL</h3>
<p>Estado de cumplimiento de requisitos legales ambientales.</p>

<h3>4. PROGRAMAS AMBIENTALES</h3>
<p>Avance de programas de gestión ambiental.</p>`
                },
                'inspeccion': {
                    titulo: 'Reporte de Inspección',
                    descripcion: 'Reporte de inspección de seguridad/calidad',
                    contenido: `<h3>1. OBJETIVO Y ALCANCE</h3>
<p>Propósito y áreas cubiertas por la inspección.</p>

<h3>2. METODOLOGÍA</h3>
<p>Procedimientos y criterios utilizados.</p>

<h3>3. HALLAZGOS</h3>
<ul>
<li>Conformidades</li>
<li>No conformidades</li>
<li>Observaciones</li>
</ul>

<h3>4. RECOMENDACIONES</h3>
<p>Acciones sugeridas para mejorar las condiciones.</p>

<h3>5. CONCLUSIONES</h3>
<p>Resumen de resultados y estado general.</p>`
                }
            };

            const template = templates[tipo];
            if (template) {
                $('#titulo').val(template.titulo);
                $('#descripcion').val(template.descripcion);
                $('#contenido').summernote('code', template.contenido);
                $('#tipo').val(tipo);

                toastr.success('Plantilla cargada correctamente');
            }
        }
    </script>
@endsection
