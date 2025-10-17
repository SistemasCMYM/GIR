@extends('layouts.dashboard')

@section('title', 'Detalle del Hallazgo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('hallazgos.index') }}">Hallazgos</a></li>
    <li class="breadcrumb-item active">Detalle</li>
@endsection

@section('content')
    <!-- Estado y Acciones -->
    <div class="row mb-4">
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Estado:</strong>
                            <span
                                class="badge badge-{{ $hallazgo->estado == 'abierto' ? 'danger' : ($hallazgo->estado == 'en_proceso' ? 'warning' : 'success') }}">
                                {{ ucfirst(str_replace('_', ' ', $hallazgo->estado)) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Progreso:</strong>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $hallazgo->progreso ?? 0 }}%"
                                    aria-valuenow="{{ $hallazgo->progreso ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $hallazgo->progreso ?? 0 }}%
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <strong>Días Transcurridos:</strong>
                            {{ $hallazgo->fecha_identificacion ? \Carbon\Carbon::parse($hallazgo->fecha_identificacion)->diffInDays(now()) : 0 }}
                            días
                        </div>
                        <div class="col-md-3">
                            @if ($hallazgo->fecha_limite)
                                @php
                                    $fechaLimite = \Carbon\Carbon::parse($hallazgo->fecha_limite);
                                    $diasRestantes = $fechaLimite->diffInDays(now(), false);
                                    $vencido = $diasRestantes > 0;
                                @endphp
                                <strong>{{ $vencido ? 'Vencido:' : 'Días Restantes:' }}</strong>
                                <span class="text-{{ $vencido ? 'danger' : 'info' }}">
                                    {{ abs($diasRestantes) }} días
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="btn-group-vertical w-100">
                        <a href="{{ route('hallazgos.edit', $hallazgo->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-success" onclick="actualizarProgreso()">
                            <i class="fas fa-chart-line"></i> Actualizar Progreso
                        </button>
                        <button type="button" class="btn btn-info" onclick="agregarSeguimiento()">
                            <i class="fas fa-comment"></i> Agregar Seguimiento
                        </button>
                        <a href="{{ route('hallazgos.export-pdf', $hallazgo->id) }}" class="btn btn-warning"
                            target="_blank">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="row">
        <div class="col-md-8">
            <!-- Detalles del Hallazgo -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Detalles del Hallazgo</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Tipo:</dt>
                        <dd class="col-sm-9">{{ ucfirst(str_replace('_', ' ', $hallazgo->tipo)) }}</dd>

                        <dt class="col-sm-3">Área:</dt>
                        <dd class="col-sm-9">{{ ucfirst(str_replace('_', ' ', $hallazgo->area)) }}</dd>

                        <dt class="col-sm-3">Ubicación:</dt>
                        <dd class="col-sm-9">{{ $hallazgo->ubicacion ?? 'No especificada' }}</dd>

                        <dt class="col-sm-3">Fecha de Identificación:</dt>
                        <dd class="col-sm-9">
                            {{ $hallazgo->fecha_identificacion ? $hallazgo->fecha_identificacion->format('d/m/Y') : 'No especificada' }}
                        </dd>

                        <dt class="col-sm-3">Descripción:</dt>
                        <dd class="col-sm-9">{{ $hallazgo->descripcion }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Evaluación de Riesgo -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Evaluación de Riesgo</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Probabilidad</span>
                                    <span class="info-box-number">{{ $hallazgo->probabilidad }}/5</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-impact"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Impacto</span>
                                    <span class="info-box-number">{{ $hallazgo->impacto }}/5</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Puntaje</span>
                                    <span
                                        class="info-box-number">{{ ($hallazgo->probabilidad ?? 0) * ($hallazgo->impacto ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span
                                    class="info-box-icon bg-{{ $hallazgo->nivel_riesgo == 'critico'
                                        ? 'danger'
                                        : ($hallazgo->nivel_riesgo == 'alto'
                                            ? 'warning'
                                            : ($hallazgo->nivel_riesgo == 'medio'
                                                ? 'info'
                                                : 'success')) }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nivel de Riesgo</span>
                                    <span class="info-box-number">{{ ucfirst($hallazgo->nivel_riesgo) }}</span>
                                </div>
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
                    <dl class="row">
                        <dt class="col-sm-3">Responsable:</dt>
                        <dd class="col-sm-9">{{ $hallazgo->responsable ?? 'No asignado' }}</dd>

                        <dt class="col-sm-3">Fecha Límite:</dt>
                        <dd class="col-sm-9">
                            {{ $hallazgo->fecha_limite ? $hallazgo->fecha_limite->format('d/m/Y') : 'No definida' }}</dd>

                        <dt class="col-sm-3">Acciones Propuestas:</dt>
                        <dd class="col-sm-9">{{ $hallazgo->acciones_correctivas ?? 'No definidas' }}</dd>

                        @if ($hallazgo->observaciones_cierre)
                            <dt class="col-sm-3">Observaciones de Cierre:</dt>
                            <dd class="col-sm-9">{{ $hallazgo->observaciones_cierre }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Seguimientos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-comments"></i> Seguimientos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-success" onclick="agregarSeguimiento()">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="seguimientos-container">
                        @if (isset($hallazgo->seguimientos) && count($hallazgo->seguimientos) > 0)
                            @foreach ($hallazgo->seguimientos as $seguimiento)
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-user"></i> {{ $seguimiento['usuario'] ?? 'Usuario' }}
                                            <small
                                                class="text-muted">{{ isset($seguimiento['fecha']) ? \Carbon\Carbon::parse($seguimiento['fecha'])->format('d/m/Y H:i') : '' }}</small>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        {{ $seguimiento['comentario'] ?? '' }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay seguimientos registrados.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Archivos Adjuntos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-paperclip"></i> Archivos Adjuntos</h3>
                </div>
                <div class="card-body">
                    @if (isset($hallazgo->archivos) && count($hallazgo->archivos) > 0)
                        <div class="list-group">
                            @foreach ($hallazgo->archivos as $archivo)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file"></i>
                                        <span class="ml-2">{{ basename($archivo) }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ Storage::url($archivo) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ Storage::url($archivo) }}" download
                                            class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No hay archivos adjuntos.</p>
                    @endif
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info"></i> Información Adicional</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Creado por:</dt>
                        <dd>{{ $hallazgo->creado_por ?? 'Sistema' }}</dd>

                        <dt>Fecha de Creación:</dt>
                        <dd>{{ $hallazgo->created_at ? $hallazgo->created_at->format('d/m/Y H:i') : 'No disponible' }}</dd>

                        <dt>Última Actualización:</dt>
                        <dd>{{ $hallazgo->updated_at ? $hallazgo->updated_at->format('d/m/Y H:i') : 'No disponible' }}</dd>

                        @if ($hallazgo->fecha_cierre)
                            <dt>Fecha de Cierre:</dt>
                            <dd>{{ $hallazgo->fecha_cierre->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span
                                    class="description-percentage text-{{ $hallazgo->progreso >= 75 ? 'success' : ($hallazgo->progreso >= 50 ? 'warning' : 'danger') }}">
                                    <i
                                        class="fas fa-{{ $hallazgo->progreso >= 75 ? 'arrow-up' : ($hallazgo->progreso >= 50 ? 'minus' : 'arrow-down') }}"></i>
                                    {{ $hallazgo->progreso ?? 0 }}%
                                </span>
                                <h5 class="description-header">Progreso</h5>
                                <span class="description-text">Completado</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-info">
                                    <i class="fas fa-calendar"></i>
                                    {{ $hallazgo->fecha_identificacion ? \Carbon\Carbon::parse($hallazgo->fecha_identificacion)->diffInDays(now()) : 0 }}
                                </span>
                                <h5 class="description-header">Días</h5>
                                <span class="description-text">Transcurridos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Actualizar Progreso -->
    <div class="modal fade" id="progresoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Actualizar Progreso</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="progresoForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nuevo_progreso">Progreso (%)</label>
                            <input type="range" id="nuevo_progreso" name="progreso" class="form-control-range"
                                min="0" max="100" value="{{ $hallazgo->progreso ?? 0 }}"
                                oninput="updateProgresoValue(this.value)">
                            <div class="text-center mt-2">
                                <span id="progreso-value"
                                    class="badge badge-primary">{{ $hallazgo->progreso ?? 0 }}%</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comentario_progreso">Comentario (opcional)</label>
                            <textarea id="comentario_progreso" name="comentario" class="form-control" rows="3"
                                placeholder="Describa el avance realizado..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Seguimiento -->
    <div class="modal fade" id="seguimientoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Seguimiento</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="seguimientoForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comentario_seguimiento">Comentario <span class="text-danger">*</span></label>
                            <textarea id="comentario_seguimiento" name="comentario" class="form-control" rows="4" required
                                placeholder="Describa el seguimiento realizado..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar Seguimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function actualizarProgreso() {
            $('#progresoModal').modal('show');
        }

        function agregarSeguimiento() {
            $('#seguimientoModal').modal('show');
        }

        function updateProgresoValue(value) {
            $('#progreso-value').text(value + '%');
        }

        $('#progresoForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('hallazgos.update-progress', $hallazgo->id) }}',
                type: 'PUT',
                data: {
                    progreso: $('#nuevo_progreso').val(),
                    comentario: $('#comentario_progreso').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#progresoModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo actualizar el progreso', 'error');
                }
            });
        });

        $('#seguimientoForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('hallazgos.add-followup', $hallazgo->id) }}',
                type: 'POST',
                data: {
                    comentario: $('#comentario_seguimiento').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#seguimientoModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo agregar el seguimiento', 'error');
                }
            });
        });
    </script>
@stop
