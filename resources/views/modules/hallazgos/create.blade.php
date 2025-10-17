@extends('layouts.dashboard')

@section('title', 'Nuevo Hallazgo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('hallazgos.index') }}">Hallazgos</a></li>
    <li class="breadcrumb-item active">Nuevo</li>
@endsection

@section('content')
    <form action="{{ route('hallazgos.store') }}" method="POST" enctype="multipart/form-data" id="hallazgoForm">
        @csrf

        <!-- Información Básica -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Básica</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo">Tipo de Hallazgo <span class="text-danger">*</span></label>
                            <select name="tipo" id="tipo" class="form-control select2" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="no_conformidad">No Conformidad</option>
                                <option value="observacion">Observación</option>
                                <option value="oportunidad_mejora">Oportunidad de Mejora</option>
                                <option value="incidente">Incidente</option>
                                <option value="accidente">Accidente</option>
                            </select>
                            @error('tipo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="area">Área <span class="text-danger">*</span></label>
                            <select name="area" id="area" class="form-control select2" required>
                                <option value="">Seleccione un área</option>
                                <option value="seguridad">Seguridad</option>
                                <option value="calidad">Calidad</option>
                                <option value="ambiental">Ambiental</option>
                                <option value="operacional">Operacional</option>
                                <option value="recursos_humanos">Recursos Humanos</option>
                                <option value="mantenimiento">Mantenimiento</option>
                                <option value="logistica">Logística</option>
                                <option value="administracion">Administración</option>
                            </select>
                            @error('area')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion">Descripción del Hallazgo <span class="text-danger">*</span></label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required
                                placeholder="Describa detalladamente el hallazgo identificado...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ubicacion">Ubicación</label>
                            <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                                value="{{ old('ubicacion') }}" placeholder="Ej: Planta 2, Oficina 301">
                            @error('ubicacion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_identificacion">Fecha de Identificación <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_identificacion" id="fecha_identificacion" class="form-control"
                                value="{{ old('fecha_identificacion', date('Y-m-d')) }}" required>
                            @error('fecha_identificacion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluación de Riesgo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Evaluación de Riesgo</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="probabilidad">Probabilidad <span class="text-danger">*</span></label>
                            <select name="probabilidad" id="probabilidad" class="form-control" required
                                onchange="calcularRiesgo()">
                                <option value="">Seleccione</option>
                                <option value="1">1 - Muy Baja</option>
                                <option value="2">2 - Baja</option>
                                <option value="3">3 - Media</option>
                                <option value="4">4 - Alta</option>
                                <option value="5">5 - Muy Alta</option>
                            </select>
                            @error('probabilidad')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="impacto">Impacto <span class="text-danger">*</span></label>
                            <select name="impacto" id="impacto" class="form-control" required
                                onchange="calcularRiesgo()">
                                <option value="">Seleccione</option>
                                <option value="1">1 - Muy Bajo</option>
                                <option value="2">2 - Bajo</option>
                                <option value="3">3 - Medio</option>
                                <option value="4">4 - Alto</option>
                                <option value="5">5 - Muy Alto</option>
                            </select>
                            @error('impacto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nivel_riesgo">Nivel de Riesgo</label>
                            <input type="text" id="nivel_riesgo_display" class="form-control" readonly>
                            <input type="hidden" name="nivel_riesgo" id="nivel_riesgo">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Matriz de Riesgo:</h5>
                            <p><strong>Bajo (1-6):</strong> Riesgo aceptable, monitoreo rutinario</p>
                            <p><strong>Medio (8-12):</strong> Riesgo tolerable, requiere controles</p>
                            <p><strong>Alto (15-16):</strong> Riesgo significativo, acción inmediata</p>
                            <p><strong>Crítico (20-25):</strong> Riesgo inaceptable, acción urgente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Correctivas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools"></i> Acciones Correctivas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="acciones_correctivas">Acciones Correctivas Propuestas</label>
                            <textarea name="acciones_correctivas" id="acciones_correctivas" class="form-control" rows="3"
                                placeholder="Describa las acciones propuestas para corregir el hallazgo...">{{ old('acciones_correctivas') }}</textarea>
                            @error('acciones_correctivas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="responsable">Responsable <span class="text-danger">*</span></label>
                            <input type="text" name="responsable" id="responsable" class="form-control" required
                                value="{{ old('responsable') }}" placeholder="Nombre del responsable">
                            @error('responsable')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_limite">Fecha Límite</label>
                            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control"
                                value="{{ old('fecha_limite') }}">
                            @error('fecha_limite')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archivos Adjuntos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paperclip"></i> Archivos Adjuntos</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="archivos">Archivos de Evidencia</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archivos" name="archivos[]" multiple
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
                        <label class="custom-file-label" for="archivos">Seleccionar archivos...</label>
                    </div>
                    <small class="text-muted">Formatos permitidos: JPG, PNG, PDF, DOC, XLS. Máximo 5MB por archivo.</small>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="card">
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('hallazgos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Hallazgo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet"
        href="//cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@endsection

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap'
            });

            // Custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this)[0].files.length > 1 ?
                    $(this)[0].files.length + ' archivos seleccionados' :
                    $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
            });

            // Form validation
            $('#hallazgoForm').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        function calcularRiesgo() {
            const probabilidad = parseInt($('#probabilidad').val()) || 0;
            const impacto = parseInt($('#impacto').val()) || 0;

            if (probabilidad > 0 && impacto > 0) {
                const riesgo = probabilidad * impacto;
                let nivel = '';
                let color = '';

                if (riesgo >= 20) {
                    nivel = 'Crítico';
                    color = 'bg-danger';
                } else if (riesgo >= 15) {
                    nivel = 'Alto';
                    color = 'bg-warning';
                } else if (riesgo >= 8) {
                    nivel = 'Medio';
                    color = 'bg-info';
                } else {
                    nivel = 'Bajo';
                    color = 'bg-success';
                }

                $('#nivel_riesgo').val(nivel.toLowerCase());
                $('#nivel_riesgo_display').val(`${nivel} (${riesgo})`).removeClass().addClass(
                    `form-control text-white ${color}`);
            } else {
                $('#nivel_riesgo').val('');
                $('#nivel_riesgo_display').val('').removeClass().addClass('form-control');
            }
        }

        function validateForm() {
            let valid = true;
            const required = ['tipo', 'area', 'descripcion', 'fecha_identificacion', 'probabilidad', 'impacto',
                'responsable'
            ];

            required.forEach(function(field) {
                const value = $(`#${field}`).val();
                if (!value || value.trim() === '') {
                    $(`#${field}`).addClass('is-invalid');
                    valid = false;
                } else {
                    $(`#${field}`).removeClass('is-invalid');
                }
            });

            if (!valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Requeridos',
                    text: 'Por favor complete todos los campos marcados con (*)'
                });
            }

            return valid;
        }
    </script>
@stop
