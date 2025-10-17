@extends('layouts.dashboard')

@section('title', 'Editar Hallazgo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('hallazgos.index') }}">Hallazgos</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <form action="{{ route('hallazgos.update', $hallazgo->id) }}" method="POST" enctype="multipart/form-data"
        id="hallazgoForm">
        @csrf
        @method('PUT')

        <!-- Estado y Progreso -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Estado y Progreso</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado">Estado <span class="text-danger">*</span></label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="abierto" {{ $hallazgo->estado == 'abierto' ? 'selected' : '' }}>Abierto
                                </option>
                                <option value="en_proceso" {{ $hallazgo->estado == 'en_proceso' ? 'selected' : '' }}>En
                                    Proceso</option>
                                <option value="cerrado" {{ $hallazgo->estado == 'cerrado' ? 'selected' : '' }}>Cerrado
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="progreso">Progreso (%)</label>
                            <input type="number" name="progreso" id="progreso" class="form-control" min="0"
                                max="100" value="{{ $hallazgo->progreso ?? 0 }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Días Transcurridos</label>
                            <input type="text" class="form-control" readonly
                                value="{{ $hallazgo->fecha_identificacion ? \Carbon\Carbon::parse($hallazgo->fecha_identificacion)->diffInDays(now()) : 0 }} días">
                        </div>
                    </div>
                </div>

                @if ($hallazgo->fecha_limite)
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $fechaLimite = \Carbon\Carbon::parse($hallazgo->fecha_limite);
                                $diasRestantes = $fechaLimite->diffInDays(now(), false);
                                $vencido = $diasRestantes > 0;
                            @endphp
                            <div class="alert {{ $vencido ? 'alert-danger' : 'alert-info' }}">
                                <h5><i class="icon fas {{ $vencido ? 'fa-exclamation-triangle' : 'fa-clock' }}"></i>
                                    {{ $vencido ? 'Vencido' : 'Tiempo Restante' }}
                                </h5>
                                <p>
                                    @if ($vencido)
                                        Este hallazgo venció hace {{ abs($diasRestantes) }} días.
                                    @else
                                        Quedan {{ abs($diasRestantes) }} días para el vencimiento.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

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
                                <option value="no_conformidad" {{ $hallazgo->tipo == 'no_conformidad' ? 'selected' : '' }}>
                                    No Conformidad</option>
                                <option value="observacion" {{ $hallazgo->tipo == 'observacion' ? 'selected' : '' }}>
                                    Observación</option>
                                <option value="oportunidad_mejora"
                                    {{ $hallazgo->tipo == 'oportunidad_mejora' ? 'selected' : '' }}>Oportunidad de Mejora
                                </option>
                                <option value="incidente" {{ $hallazgo->tipo == 'incidente' ? 'selected' : '' }}>Incidente
                                </option>
                                <option value="accidente" {{ $hallazgo->tipo == 'accidente' ? 'selected' : '' }}>Accidente
                                </option>
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
                                <option value="seguridad" {{ $hallazgo->area == 'seguridad' ? 'selected' : '' }}>Seguridad
                                </option>
                                <option value="calidad" {{ $hallazgo->area == 'calidad' ? 'selected' : '' }}>Calidad
                                </option>
                                <option value="ambiental" {{ $hallazgo->area == 'ambiental' ? 'selected' : '' }}>Ambiental
                                </option>
                                <option value="operacional" {{ $hallazgo->area == 'operacional' ? 'selected' : '' }}>
                                    Operacional</option>
                                <option value="recursos_humanos"
                                    {{ $hallazgo->area == 'recursos_humanos' ? 'selected' : '' }}>Recursos Humanos</option>
                                <option value="mantenimiento" {{ $hallazgo->area == 'mantenimiento' ? 'selected' : '' }}>
                                    Mantenimiento</option>
                                <option value="logistica" {{ $hallazgo->area == 'logistica' ? 'selected' : '' }}>Logística
                                </option>
                                <option value="administracion" {{ $hallazgo->area == 'administracion' ? 'selected' : '' }}>
                                    Administración</option>
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
                                placeholder="Describa detalladamente el hallazgo identificado...">{{ old('descripcion', $hallazgo->descripcion) }}</textarea>
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
                                value="{{ old('ubicacion', $hallazgo->ubicacion) }}"
                                placeholder="Ej: Planta 2, Oficina 301">
                            @error('ubicacion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_identificacion">Fecha de Identificación <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_identificacion" id="fecha_identificacion"
                                class="form-control"
                                value="{{ old('fecha_identificacion', $hallazgo->fecha_identificacion ? $hallazgo->fecha_identificacion->format('Y-m-d') : '') }}"
                                required>
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
                                <option value="1" {{ $hallazgo->probabilidad == 1 ? 'selected' : '' }}>1 - Muy Baja
                                </option>
                                <option value="2" {{ $hallazgo->probabilidad == 2 ? 'selected' : '' }}>2 - Baja
                                </option>
                                <option value="3" {{ $hallazgo->probabilidad == 3 ? 'selected' : '' }}>3 - Media
                                </option>
                                <option value="4" {{ $hallazgo->probabilidad == 4 ? 'selected' : '' }}>4 - Alta
                                </option>
                                <option value="5" {{ $hallazgo->probabilidad == 5 ? 'selected' : '' }}>5 - Muy Alta
                                </option>
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
                                <option value="1" {{ $hallazgo->impacto == 1 ? 'selected' : '' }}>1 - Muy Bajo
                                </option>
                                <option value="2" {{ $hallazgo->impacto == 2 ? 'selected' : '' }}>2 - Bajo</option>
                                <option value="3" {{ $hallazgo->impacto == 3 ? 'selected' : '' }}>3 - Medio</option>
                                <option value="4" {{ $hallazgo->impacto == 4 ? 'selected' : '' }}>4 - Alto</option>
                                <option value="5" {{ $hallazgo->impacto == 5 ? 'selected' : '' }}>5 - Muy Alto
                                </option>
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
                            <input type="hidden" name="nivel_riesgo" id="nivel_riesgo"
                                value="{{ $hallazgo->nivel_riesgo }}">
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
                                placeholder="Describa las acciones propuestas para corregir el hallazgo...">{{ old('acciones_correctivas', $hallazgo->acciones_correctivas) }}</textarea>
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
                                value="{{ old('responsable', $hallazgo->responsable) }}"
                                placeholder="Nombre del responsable">
                            @error('responsable')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_limite">Fecha Límite</label>
                            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control"
                                value="{{ old('fecha_limite', $hallazgo->fecha_limite ? $hallazgo->fecha_limite->format('Y-m-d') : '') }}">
                            @error('fecha_limite')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if ($hallazgo->estado == 'cerrado')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones_cierre">Observaciones de Cierre</label>
                                <textarea name="observaciones_cierre" id="observaciones_cierre" class="form-control" rows="3"
                                    placeholder="Describa las acciones tomadas y los resultados obtenidos...">{{ old('observaciones_cierre', $hallazgo->observaciones_cierre) }}</textarea>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Archivos Existentes -->
        @if (isset($hallazgo->archivos) && count($hallazgo->archivos) > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file"></i> Archivos Existentes</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($hallazgo->archivos as $archivo)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file fa-2x mb-2"></i>
                                        <p class="card-text small">{{ basename($archivo) }}</p>
                                        <a href="{{ Storage::url($archivo) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">Ver</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="eliminarArchivo('{{ $archivo }}')">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Nuevos Archivos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paperclip"></i> Nuevos Archivos</h3>
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
                        <a href="{{ route('hallazgos.show', $hallazgo->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver Detalle
                        </a>
                        <a href="{{ route('hallazgos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Hallazgo
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

            // Calculate initial risk
            calcularRiesgo();

            // Custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this)[0].files.length > 1 ?
                    $(this)[0].files.length + ' archivos seleccionados' :
                    $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
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
                const nivelActual = $('#nivel_riesgo').val();
                if (nivelActual) {
                    const nivelCapitalizado = nivelActual.charAt(0).toUpperCase() + nivelActual.slice(1);
                    $('#nivel_riesgo_display').val(nivelCapitalizado);
                }
            }
        }

        function eliminarArchivo(archivo) {
            Swal.fire({
                title: '¿Eliminar archivo?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('hallazgos.delete-file', $hallazgo->id) }}',
                        type: 'DELETE',
                        data: {
                            archivo: archivo,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        }
                    });
                }
            });
        }
    </script>
@stop
