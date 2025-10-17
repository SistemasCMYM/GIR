@extends('layouts.dashboard')

@section('title', 'Detalle del Reporte')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">{{ $reporte->nombre }}</li>
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

                <!-- Report Header -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-2"></i>
                            {{ $reporte->nombre }}
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                @if ($reporte->estado === 'pendiente')
                                    <a href="{{ route('reports.edit', $reporte->_id) }}"
                                        class="btn btn-outline-light btn-sm" title="Editar reporte">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                @if ($reporte->estado === 'completado' && $reporte->archivo_generado)
                                    <a href="{{ route('reports.download', $reporte->_id) }}"
                                        class="btn btn-outline-light btn-sm" title="Descargar reporte">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif

                                <button type="button" class="btn btn-outline-light btn-sm" onclick="refreshReport()"
                                    title="Actualizar estado">
                                    <i class="fas fa-sync-alt"></i>
                                </button>

                                <a href="{{ route('reports.reports') }}" class="btn btn-outline-light btn-sm"
                                    title="Volver a la lista">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Main Content -->
                    <div class="col-md-8">
                        <!-- Report Information Tab -->
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header p-0 pt-1 border-bottom-0">
                                <ul class="nav nav-tabs" id="report-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="info-tab" data-toggle="pill" href="#info"
                                            role="tab">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Información
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="parameters-tab" data-toggle="pill" href="#parameters"
                                            role="tab">
                                            <i class="fas fa-sliders-h mr-1"></i>
                                            Parámetros
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="progress-tab" data-toggle="pill" href="#progress"
                                            role="tab">
                                            <i class="fas fa-chart-line mr-1"></i>
                                            Progreso
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="history-tab" data-toggle="pill" href="#history"
                                            role="tab">
                                            <i class="fas fa-history mr-1"></i>
                                            Historial
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="report-tabsContent">
                                    <!-- Information Tab -->
                                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width: 200px;">Nombre</th>
                                                            <td>{{ $reporte->nombre }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Descripción</th>
                                                            <td>{{ $reporte->descripcion }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tipo</th>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    @switch($reporte->tipo)
                                                                        @case('hallazgos')
                                                                            Hallazgos
                                                                        @break

                                                                        @case('empleados')
                                                                            Empleados
                                                                        @break

                                                                        @case('psicosocial')
                                                                            Psicosocial
                                                                        @break

                                                                        @case('planes')
                                                                            Planes de Mejora
                                                                        @break

                                                                        @case('auditoria')
                                                                            Auditoría
                                                                        @break

                                                                        @default
                                                                            {{ ucfirst($reporte->tipo) }}
                                                                    @endswitch
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Empresa</th>
                                                            <td>{{ $reporte->empresa->nombre ?? 'Empresa no encontrada' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Período</th>
                                                            <td>
                                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                                {{ $reporte->fecha_inicio ? $reporte->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                                                <i class="fas fa-arrow-right mx-2"></i>
                                                                {{ $reporte->fecha_fin ? $reporte->fecha_fin->format('d/m/Y') : 'N/A' }}
                                                                <small class="text-muted">
                                                                    ({{ $reporte->fecha_inicio && $reporte->fecha_fin ? $reporte->fecha_inicio->diffInDays($reporte->fecha_fin) + 1 : 0 }}
                                                                    días)
                                                                </small>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Creado por</th>
                                                            <td>
                                                                @if ($reporte->usuario)
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="{{ asset('dist/img/user-default.png') }}"
                                                                            class="img-circle img-size-32 mr-2"
                                                                            alt="Usuario">
                                                                        <div>
                                                                            <strong>{{ $reporte->usuario->nombre }}</strong><br>
                                                                            <small
                                                                                class="text-muted">{{ $reporte->usuario->email }}</small>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">Usuario eliminado</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Fecha de Creación</th>
                                                            <td>
                                                                <i class="fas fa-clock mr-1"></i>
                                                                {{ $reporte->created_at ? $reporte->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                                                <small class="text-muted">
                                                                    ({{ $reporte->created_at ? $reporte->created_at->diffForHumans() : 'N/A' }})
                                                                </small>
                                                            </td>
                                                        </tr>
                                                        @if ($reporte->updated_at && $reporte->updated_at != $reporte->created_at)
                                                            <tr>
                                                                <th>Última Actualización</th>
                                                                <td>
                                                                    <i class="fas fa-sync-alt mr-1"></i>
                                                                    {{ $reporte->updated_at->format('d/m/Y H:i:s') }}
                                                                    <small class="text-muted">
                                                                        ({{ $reporte->updated_at->diffForHumans() }})
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($reporte->archivo_generado)
                                                            <tr>
                                                                <th>Archivo Generado</th>
                                                                <td>
                                                                    <i class="fas fa-file-pdf mr-1 text-danger"></i>
                                                                    {{ $reporte->archivo_generado }}
                                                                    <a href="{{ route('reports.download', $reporte->_id) }}"
                                                                        class="btn btn-outline-primary btn-sm ml-2">
                                                                        <i class="fas fa-download mr-1"></i>
                                                                        Descargar
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Parameters Tab -->
                                    <div class="tab-pane fade" id="parameters" role="tabpanel">
                                        @if (isset($reporte->parametros) && is_array($reporte->parametros) && count($reporte->parametros) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Parámetro</th>
                                                            <th>Valor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($reporte->parametros as $key => $value)
                                                            <tr>
                                                                <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                                                </td>
                                                                <td>
                                                                    @if (is_bool($value) || $value === '1' || $value === '0')
                                                                        <span
                                                                            class="badge {{ $value ? 'badge-success' : 'badge-secondary' }}">
                                                                            {{ $value ? 'Sí' : 'No' }}
                                                                        </span>
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-sliders-h fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Sin Parámetros Configurados</h5>
                                                <p class="text-muted">Este reporte no tiene parámetros adicionales
                                                    configurados.</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Progress Tab -->
                                    <div class="tab-pane fade" id="progress" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5>Estado del Procesamiento</h5>
                                                        <div class="progress mb-3" style="height: 20px;">
                                                            <div class="progress-bar 
                                                            @if ($reporte->estado == 'completado') bg-success
                                                            @elseif($reporte->estado == 'procesando') bg-info progress-bar-striped progress-bar-animated
                                                            @elseif($reporte->estado == 'error') bg-danger
                                                            @else bg-warning @endif"
                                                                role="progressbar"
                                                                style="width: {{ $reporte->progreso ?? 0 }}%"
                                                                aria-valuenow="{{ $reporte->progreso ?? 0 }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                                {{ $reporte->progreso ?? 0 }}%
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
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
                                                                        <span class="info-box-text">Estado</span>
                                                                        <span
                                                                            class="info-box-number">{{ ucfirst($reporte->estado) }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="info-box">
                                                                    <span class="info-box-icon bg-info">
                                                                        <i class="fas fa-percentage"></i>
                                                                    </span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Progreso</span>
                                                                        <span
                                                                            class="info-box-number">{{ $reporte->progreso ?? 0 }}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($reporte->estado == 'procesando')
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle mr-2"></i>
                                                                <strong>Procesando:</strong> El reporte se está generando en
                                                                segundo plano.
                                                                El proceso puede tomar varios minutos dependiendo de la
                                                                cantidad de datos.
                                                            </div>
                                                        @elseif($reporte->estado == 'error')
                                                            <div class="alert alert-danger">
                                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                                <strong>Error:</strong> Ocurrió un error durante la
                                                                generación del reporte.
                                                                Por favor, intente regenerar el reporte.
                                                            </div>
                                                        @elseif($reporte->estado == 'completado')
                                                            <div class="alert alert-success">
                                                                <i class="fas fa-check-circle mr-2"></i>
                                                                <strong>Completado:</strong> El reporte se ha generado
                                                                exitosamente y está listo para descargar.
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- History Tab -->
                                    <div class="tab-pane fade" id="history" role="tabpanel">
                                        <div class="timeline">
                                            <div class="time-label">
                                                <span class="bg-primary">
                                                    {{ $reporte->created_at ? $reporte->created_at->format('d/m/Y') : 'N/A' }}
                                                </span>
                                            </div>

                                            <div>
                                                <i class="fas fa-plus bg-success"></i>
                                                <div class="timeline-item">
                                                    <span class="time">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $reporte->created_at ? $reporte->created_at->format('H:i') : 'N/A' }}
                                                    </span>
                                                    <h3 class="timeline-header">
                                                        <strong>Reporte Creado</strong>
                                                    </h3>
                                                    <div class="timeline-body">
                                                        El reporte "{{ $reporte->nombre }}" fue creado por
                                                        <strong>{{ $reporte->usuario->nombre ?? 'Usuario eliminado' }}</strong>.
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($reporte->updated_at && $reporte->updated_at != $reporte->created_at)
                                                <div>
                                                    <i class="fas fa-edit bg-warning"></i>
                                                    <div class="timeline-item">
                                                        <span class="time">
                                                            <i class="fas fa-clock"></i>
                                                            {{ $reporte->updated_at->format('H:i') }}
                                                        </span>
                                                        <h3 class="timeline-header">
                                                            <strong>Reporte Actualizado</strong>
                                                        </h3>
                                                        <div class="timeline-body">
                                                            El reporte fue modificado por última vez.
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($reporte->estado == 'completado')
                                                <div>
                                                    <i class="fas fa-check bg-success"></i>
                                                    <div class="timeline-item">
                                                        <span class="time">
                                                            <i class="fas fa-clock"></i>
                                                            {{ $reporte->updated_at ? $reporte->updated_at->format('H:i') : 'N/A' }}
                                                        </span>
                                                        <h3 class="timeline-header">
                                                            <strong>Generación Completada</strong>
                                                        </h3>
                                                        <div class="timeline-body">
                                                            El reporte se generó exitosamente y está disponible para
                                                            descarga.
                                                            @if ($reporte->archivo_generado)
                                                                <br>
                                                                <small class="text-muted">
                                                                    Archivo: {{ $reporte->archivo_generado }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div>
                                                <i class="fas fa-clock bg-gray"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-md-4">
                        <!-- Status Card -->
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
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
                                        <span class="info-box-text">Estado</span>
                                        <span class="info-box-number">{{ ucfirst($reporte->estado) }}</span>
                                        <div class="progress mt-2">
                                            <div class="progress-bar 
                                            @if ($reporte->estado == 'completado') bg-success
                                            @elseif($reporte->estado == 'procesando') bg-info
                                            @elseif($reporte->estado == 'error') bg-danger
                                            @else bg-warning @endif"
                                                style="width: {{ $reporte->progreso ?? 0 }}%">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $reporte->progreso ?? 0 }}% Completado
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Card -->
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cogs mr-1"></i>
                                    Acciones
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="btn-group-vertical w-100">
                                    @if ($reporte->estado === 'pendiente')
                                        <a href="{{ route('reports.edit', $reporte->_id) }}"
                                            class="btn btn-outline-primary mb-2">
                                            <i class="fas fa-edit mr-1"></i>
                                            Editar Reporte
                                        </a>
                                    @endif

                                    @if ($reporte->estado === 'completado' && $reporte->archivo_generado)
                                        <a href="{{ route('reports.download', $reporte->_id) }}"
                                            class="btn btn-outline-success mb-2">
                                            <i class="fas fa-download mr-1"></i>
                                            Descargar PDF
                                        </a>
                                    @endif

                                    @if ($reporte->estado === 'error' || $reporte->estado === 'pendiente')
                                        <button type="button" class="btn btn-outline-info mb-2"
                                            onclick="regenerateReport()">
                                            <i class="fas fa-redo mr-1"></i>
                                            Regenerar
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-outline-secondary mb-2"
                                        onclick="refreshReport()">
                                        <i class="fas fa-sync-alt mr-1"></i>
                                        Actualizar Estado
                                    </button>

                                    <div class="dropdown-divider"></div>

                                    <button type="button" class="btn btn-outline-danger" onclick="deleteReport()">
                                        <i class="fas fa-trash mr-1"></i>
                                        Eliminar Reporte
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Estadísticas Rápidas
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-success">
                                                <i class="fas fa-calendar-day"></i>
                                            </span>
                                            <h5 class="description-header">
                                                {{ $reporte->fecha_inicio && $reporte->fecha_fin ? $reporte->fecha_inicio->diffInDays($reporte->fecha_fin) + 1 : 0 }}
                                            </h5>
                                            <span class="description-text">DÍAS DE PERÍODO</span>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="description-block">
                                            <span class="description-percentage text-info">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <h5 class="description-header">{{ $reporte->empresa->nombre ?? 'N/A' }}</h5>
                                            <span class="description-text">EMPRESA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            // Auto-refresh if report is processing
            @if ($reporte->estado == 'procesando')
                setInterval(function() {
                    refreshReport();
                }, 30000); // Refresh every 30 seconds
            @endif
        });

        function refreshReport() {
            window.location.reload();
        }

        function regenerateReport() {
            Swal.fire({
                title: '¿Regenerar Reporte?',
                text: "Se iniciará nuevamente el proceso de generación del reporte.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, regenerar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would implement the regenerate logic
                    // For now, we'll just show a message
                    Swal.fire({
                        title: 'Regenerando...',
                        text: 'El reporte se está regenerando en segundo plano.',
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    // Simulate regeneration start
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            });
        }

        function deleteReport() {
            Swal.fire({
                title: '¿Eliminar Reporte?',
                text: "Esta acción no se puede deshacer. El reporte y su archivo serán eliminados permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form for deletion
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('reports.destroy', $reporte->_id) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Initialize tooltips
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection

@section('styles')
    <style>
        .nav-tabs .nav-link {
            border-bottom-color: transparent;
        }

        .nav-tabs .nav-link.active {
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .timeline {
            position: relative;
            margin: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 31px;
            height: 100%;
            width: 4px;
            background: #dee2e6;
        }

        .timeline>div {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline>div>.timeline-item {
            background: #fff;
            border-radius: 3px;
            margin-left: 60px;
            margin-right: 15px;
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .timeline>div>.fa,
        .timeline>div>.fas,
        .timeline>div>.far,
        .timeline>div>.fab,
        .timeline>div>.fal,
        .timeline>div>.fad {
            width: 30px;
            height: 30px;
            font-size: 15px;
            line-height: 30px;
            position: absolute;
            color: #666;
            background: #fff;
            border-radius: 50%;
            text-align: center;
            left: 18px;
            top: 0;
        }

        .timeline>.time-label>span {
            font-weight: 600;
            color: #fff;
            border-radius: 4px;
            line-height: 1.8;
            margin-left: 60px;
            padding: 5px 10px;
        }

        .timeline-header {
            margin: 0 0 5px 0;
            color: #555;
            font-size: 16px;
            line-height: 1.1;
        }

        .timeline-body,
        .timeline-footer {
            padding: 5px 0;
        }

        .time {
            color: #999;
            float: right;
            font-size: 12px;
        }

        .description-block {
            text-align: center;
            margin-bottom: 15px;
        }

        .description-header {
            margin: 0;
            padding: 0;
            font-weight: 600;
            font-size: 20px;
        }

        .description-text {
            text-transform: uppercase;
            font-size: 11px;
            color: #999;
        }

        .description-percentage {
            color: green;
            font-size: 14px;
        }
    </style>
@endsection
