@extends('layouts.dashboard')

@section('title', 'Editar Reporte')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reportes</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.show', $reporte->_id) }}">{{ $reporte->nombre }}</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Por favor corrige los siguientes errores:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Report Edit Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-2"></i>
                            Editar Reporte: {{ $reporte->nombre }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('reports.show', $reporte->_id) }}" class="btn btn-tool"
                                title="Volver al reporte">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('reports.update', $reporte->_id) }}" method="POST" id="editReportForm">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <!-- Report Status Info -->
                            @if ($reporte->estado !== 'pendiente')
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Advertencia:</strong> Solo se pueden editar reportes en estado pendiente.
                                </div>
                            @endif

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-8">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Información Básica
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="nombre" class="required">Nombre del Reporte</label>
                                                        <input type="text"
                                                            class="form-control @error('nombre') is-invalid @enderror"
                                                            id="nombre" name="nombre"
                                                            value="{{ old('nombre', $reporte->nombre) }}" required
                                                            maxlength="255" placeholder="Ingrese el nombre del reporte">
                                                        @error('nombre')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tipo" class="required">Tipo de Reporte</label>
                                                        <select
                                                            class="form-control select2 @error('tipo') is-invalid @enderror"
                                                            id="tipo" name="tipo" required>
                                                            <option value="">Seleccione el tipo</option>
                                                            @foreach ($tiposReporte as $key => $tipo)
                                                                <option value="{{ $key }}"
                                                                    {{ old('tipo', $reporte->tipo) == $key ? 'selected' : '' }}>
                                                                    {{ $tipo }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('tipo')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="empresa_id" class="required">Empresa</label>
                                                        <select
                                                            class="form-control select2 @error('empresa_id') is-invalid @enderror"
                                                            id="empresa_id" name="empresa_id" required>
                                                            <option value="">Seleccione la empresa</option>
                                                            @foreach ($empresas as $empresa)
                                                                <option value="{{ $empresa->_id }}"
                                                                    {{ old('empresa_id', (string) $reporte->empresa_id) == (string) $empresa->_id ? 'selected' : '' }}>
                                                                    {{ $empresa->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('empresa_id')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="descripcion" class="required">Descripción</label>
                                                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                                    rows="4" maxlength="1000" required placeholder="Describa el propósito y contenido del reporte">{{ old('descripcion', $reporte->descripcion) }}</textarea>
                                                @error('descripcion')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    <span id="descriptionCount">0</span>/1000 caracteres
                                                </small>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fecha_inicio" class="required">Fecha de Inicio</label>
                                                        <input type="date"
                                                            class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                            id="fecha_inicio" name="fecha_inicio"
                                                            value="{{ old('fecha_inicio', $reporte->fecha_inicio ? $reporte->fecha_inicio->format('Y-m-d') : '') }}"
                                                            required>
                                                        @error('fecha_inicio')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fecha_fin" class="required">Fecha de Fin</label>
                                                        <input type="date"
                                                            class="form-control @error('fecha_fin') is-invalid @enderror"
                                                            id="fecha_fin" name="fecha_fin"
                                                            value="{{ old('fecha_fin', $reporte->fecha_fin ? $reporte->fecha_fin->format('Y-m-d') : '') }}"
                                                            required>
                                                        @error('fecha_fin')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Report Status and Actions -->
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-cog mr-2"></i>
                                                Estado del Reporte
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="info-box">
                                                <span
                                                    class="info-box-icon 
                                                @if ($reporte->estado == 'pendiente') bg-warning
                                                @elseif($reporte->estado == 'procesando') bg-info
                                                @elseif($reporte->estado == 'completado') bg-success
                                                @else bg-danger @endif">
                                                    <i
                                                        class="fas 
                                                    @if ($reporte->estado == 'pendiente') fa-clock
                                                    @elseif($reporte->estado == 'procesando') fa-sync-alt fa-spin
                                                    @elseif($reporte->estado == 'completado') fa-check
                                                    @else fa-times @endif"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Estado Actual</span>
                                                    <span class="info-box-number">
                                                        {{ ucfirst($reporte->estado) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Progreso</label>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar 
                                                    @if ($reporte->estado == 'completado') bg-success
                                                    @elseif($reporte->estado == 'procesando') bg-info
                                                    @else bg-warning @endif"
                                                        role="progressbar" style="width: {{ $reporte->progreso ?? 0 }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $reporte->progreso ?? 0 }}%
                                                    completado</small>
                                            </div>

                                            <div class="form-group">
                                                <label>Creado por</label>
                                                <p class="text-muted">
                                                    {{ $reporte->usuario->nombre ?? 'Usuario eliminado' }}</p>
                                            </div>

                                            <div class="form-group">
                                                <label>Fecha de Creación</label>
                                                <p class="text-muted">
                                                    {{ $reporte->created_at ? $reporte->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                </p>
                                            </div>

                                            @if ($reporte->updated_at && $reporte->updated_at != $reporte->created_at)
                                                <div class="form-group">
                                                    <label>Última Actualización</label>
                                                    <p class="text-muted">{{ $reporte->updated_at->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Parameters -->
                            <div class="card card-outline card-info collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-sliders-h mr-2"></i>
                                        Parámetros Avanzados
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="display: none;">
                                    <div id="parametrosContainer">
                                        <!-- Dynamic parameters will be loaded here based on report type -->
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="addParameter">
                                            <i class="fas fa-plus mr-1"></i>
                                            Agregar Parámetro
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('reports.show', $reporte->_id) }}" class="btn btn-default">
                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary"
                                        {{ $reporte->estado !== 'pendiente' ? 'disabled' : '' }}>
                                        <i class="fas fa-save mr-1"></i>
                                        Actualizar Reporte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                theme: 'bootstrap4',
                width: '100%'
            });

            // Character counter for description
            $('#descripcion').on('input', function() {
                const current = $(this).val().length;
                $('#descriptionCount').text(current);

                if (current > 950) {
                    $('#descriptionCount').addClass('text-danger');
                } else if (current > 800) {
                    $('#descriptionCount').addClass('text-warning').removeClass('text-danger');
                } else {
                    $('#descriptionCount').removeClass('text-danger text-warning');
                }
            });

            // Trigger counter on page load
            $('#descripcion').trigger('input');

            // Date validation
            $('#fecha_inicio, #fecha_fin').on('change', function() {
                const fechaInicio = new Date($('#fecha_inicio').val());
                const fechaFin = new Date($('#fecha_fin').val());

                if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                    toastr.warning('La fecha de fin debe ser posterior a la fecha de inicio');
                    $('#fecha_fin').val('');
                }
            });

            // Load report type specific parameters
            $('#tipo').on('change', function() {
                loadReportParameters($(this).val());
            });

            // Load existing parameters
            @if (isset($reporte->parametros) && is_array($reporte->parametros))
                @foreach ($reporte->parametros as $key => $value)
                    addParameterField('{{ $key }}', '{{ $value }}');
                @endforeach
            @endif

            // Load parameters for current type
            if ($('#tipo').val()) {
                loadReportParameters($('#tipo').val());
            }

            // Add parameter functionality
            $('#addParameter').on('click', function() {
                addParameterField();
            });

            // Form validation
            $('#editReportForm').on('submit', function(e) {
                e.preventDefault();

                // Validate dates
                const fechaInicio = new Date($('#fecha_inicio').val());
                const fechaFin = new Date($('#fecha_fin').val());

                if (fechaFin < fechaInicio) {
                    toastr.error('La fecha de fin debe ser posterior a la fecha de inicio');
                    return false;
                }

                // Show loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Actualizando...').prop(
                    'disabled', true);

                // Submit form
                this.submit();
            });
        });

        function loadReportParameters(tipo) {
            const container = $('#parametrosContainer');

            // Clear existing parameters
            container.find('.parameter-field').remove();

            // Load type-specific parameters
            const parametrosPorTipo = {
                'hallazgos': [{
                        key: 'incluir_criticos',
                        label: 'Incluir Hallazgos Críticos',
                        type: 'checkbox'
                    },
                    {
                        key: 'incluir_resueltos',
                        label: 'Incluir Resueltos',
                        type: 'checkbox'
                    },
                    {
                        key: 'area_filtro',
                        label: 'Filtrar por Área',
                        type: 'text'
                    }
                ],
                'empleados': [{
                        key: 'incluir_inactivos',
                        label: 'Incluir Empleados Inactivos',
                        type: 'checkbox'
                    },
                    {
                        key: 'cargo_filtro',
                        label: 'Filtrar por Cargo',
                        type: 'text'
                    },
                    {
                        key: 'departamento_filtro',
                        label: 'Filtrar por Departamento',
                        type: 'text'
                    }
                ],
                'psicosocial': [{
                        key: 'incluir_anonimos',
                        label: 'Incluir Respuestas Anónimas',
                        type: 'checkbox'
                    },
                    {
                        key: 'nivel_riesgo',
                        label: 'Nivel de Riesgo Mínimo',
                        type: 'select'
                    }
                ],
                'planes': [{
                        key: 'estado_plan',
                        label: 'Estado del Plan',
                        type: 'select'
                    },
                    {
                        key: 'incluir_vencidos',
                        label: 'Incluir Planes Vencidos',
                        type: 'checkbox'
                    }
                ],
                'auditoria': [{
                        key: 'tipo_auditoria',
                        label: 'Tipo de Auditoría',
                        type: 'select'
                    },
                    {
                        key: 'incluir_no_conformidades',
                        label: 'Incluir No Conformidades',
                        type: 'checkbox'
                    }
                ]
            };

            if (parametrosPorTipo[tipo]) {
                parametrosPorTipo[tipo].forEach(param => {
                    addParameterField(param.key, '', param.label, param.type);
                });
            }
        }

        function addParameterField(key = '', value = '', label = '', type = 'text') {
            const container = $('#parametrosContainer');
            const index = container.find('.parameter-field').length;

            let input = '';
            if (type === 'checkbox') {
                input = `<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="param_${index}" name="parametros[${key}]" value="1" ${value ? 'checked' : ''}>
                    <label class="custom-control-label" for="param_${index}">${label || 'Activar'}</label>
                </div>`;
            } else if (type === 'select') {
                input = `<select class="form-control" name="parametros[${key}]">
                    <option value="">Seleccionar...</option>
                    <option value="bajo" ${value === 'bajo' ? 'selected' : ''}>Bajo</option>
                    <option value="medio" ${value === 'medio' ? 'selected' : ''}>Medio</option>
                    <option value="alto" ${value === 'alto' ? 'selected' : ''}>Alto</option>
                    <option value="critico" ${value === 'critico' ? 'selected' : ''}>Crítico</option>
                </select>`;
            } else {
                input =
                    `<input type="text" class="form-control" name="parametros[${key}]" value="${value}" placeholder="Valor del parámetro">`;
            }

            const fieldHtml = `
        <div class="parameter-field row mb-2">
            <div class="col-md-4">
                <input type="text" class="form-control param-key" placeholder="Nombre del parámetro" value="${key}" ${label ? 'readonly' : ''}>
            </div>
            <div class="col-md-6">
                ${input}
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-parameter">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

            container.append(fieldHtml);

            // Update parameter names when key changes
            container.find('.param-key').last().on('input', function() {
                const newKey = $(this).val();
                $(this).closest('.parameter-field').find('input[name^="parametros"], select[name^="parametros"]')
                    .attr('name', `parametros[${newKey}]`);
            });

            // Remove parameter functionality
            container.find('.remove-parameter').last().on('click', function() {
                $(this).closest('.parameter-field').remove();
            });
        }
    </script>
@endsection

@section('styles')
    <style>
        .required::after {
            content: " *";
            color: red;
        }

        .parameter-field {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }

        .parameter-field:last-child {
            border-bottom: none;
        }

        .info-box {
            border-radius: 10px;
        }

        .progress {
            height: 10px;
        }
    </style>
@endsection
