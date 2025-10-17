@extends('layouts.dashboard')

@section('title', 'Generador de Reportes')

@section('content_header')
    <h1>
        <i class="fas fa-chart-bar"></i> Generador de Reportes
        <small>Análisis y reportes del sistema</small>
    </h1>
@stop

@section('content')
    <div class="row">
        <!-- Reportes Rápidos -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt"></i> Reportes Rápidos
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalUsers ?? 0 }}</h3>
                                    <p>Total Usuarios</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <a href="#" class="small-box-footer btn-generate-report" data-type="users">
                                    Generar Reporte <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $activeSessions ?? 0 }}</h3>
                                    <p>Sesiones Activas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <a href="#" class="small-box-footer btn-generate-report" data-type="sessions">
                                    Generar Reporte <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $systemEvents ?? 0 }}</h3>
                                    <p>Eventos del Sistema</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <a href="#" class="small-box-footer btn-generate-report" data-type="system">
                                    Generar Reporte <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $errors ?? 0 }}</h3>
                                    <p>Errores Registrados</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <a href="#" class="small-box-footer btn-generate-report" data-type="errors">
                                    Generar Reporte <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurador de Reportes Personalizados -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Configurar Reporte Personalizado
                    </h3>
                </div>
                <div class="card-body">
                    <form id="custom-report-form">
                        <div class="form-group">
                            <label>Tipo de Reporte</label>
                            <select class="form-control" id="report-type" name="type" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="usuarios">Usuarios</option>
                                <option value="sesiones">Sesiones</option>
                                <option value="actividad">Actividad del Sistema</option>
                                <option value="errores">Registro de Errores</option>
                                <option value="perfiles">Perfiles y Permisos</option>
                                <option value="seguridad">Eventos de Seguridad</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Rango de Fechas</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="date-from" name="date_from" required>
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">a</span>
                                </div>
                                <input type="date" class="form-control" id="date-to" name="date_to" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Formato de Salida</label>
                            <select class="form-control" id="output-format" name="format" required>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel (XLSX)</option>
                                <option value="csv">CSV</option>
                                <option value="html">HTML</option>
                            </select>
                        </div>

                        <div class="form-group" id="filters-section" style="display: none;">
                            <label>Filtros Adicionales</label>
                            <div id="dynamic-filters">
                                <!-- Filters will be loaded dynamically based on report type -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="include-charts" name="include_charts">
                                Incluir Gráficos
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="schedule-report" name="schedule">
                                Programar Envío Automático
                            </label>
                        </div>

                        <div class="form-group" id="schedule-options" style="display: none;">
                            <label>Frecuencia</label>
                            <select class="form-control" id="schedule-frequency" name="frequency">
                                <option value="daily">Diario</option>
                                <option value="weekly">Semanal</option>
                                <option value="monthly">Mensual</option>
                            </select>

                            <label class="mt-2">Email de Destino</label>
                            <input type="email" class="form-control" id="schedule-email" name="email"
                                placeholder="ejemplo@empresa.com">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-file-download"></i> Generar Reporte
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Historial de Reportes -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Historial de Reportes
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-danger" id="btn-clear-history">
                            <i class="fas fa-trash"></i> Limpiar Historial
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="reports-history-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Parámetros</th>
                                    <th>Formato</th>
                                    <th>Estado</th>
                                    <th>Fecha Generación</th>
                                    <th>Tamaño</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportsHistory ?? [] as $report)
                                    <tr>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucfirst($report->tipo) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                Desde: {{ $report->fecha_inicio }}<br>
                                                Hasta: {{ $report->fecha_fin }}
                                                @if ($report->filtros)
                                                    <br><strong>Filtros:</strong> {{ $report->filtros }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ strtoupper($report->formato) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($report->estado === 'completado')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Completado
                                                </span>
                                            @elseif($report->estado === 'procesando')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-spinner fa-spin"></i> Procesando
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times"></i> Error
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $report->created_at ? $report->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>{{ $report->tamaño ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if ($report->estado === 'completado')
                                                    <button type="button" class="btn btn-success btn-download-report"
                                                        data-report-id="{{ $report->_id }}" title="Descargar">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-info btn-view-report"
                                                    data-report-id="{{ $report->_id }}" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-danger btn-delete-report"
                                                    data-report-id="{{ $report->_id }}" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay reportes generados aún
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes Programados -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Reportes Programados
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-new-schedule">
                            <i class="fas fa-plus"></i> Nuevo Programa
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="scheduled-reports-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Frecuencia</th>
                                    <th>Email Destino</th>
                                    <th>Próxima Ejecución</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scheduledReports ?? [] as $schedule)
                                    <tr>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucfirst($schedule->tipo) }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($schedule->frecuencia) }}</td>
                                        <td>{{ $schedule->email }}</td>
                                        <td>
                                            <small>
                                                {{ $schedule->proxima_ejecucion ? $schedule->proxima_ejecucion->format('d/m/Y H:i:s') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($schedule->activo)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-play"></i> Activo
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-pause"></i> Pausado
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if ($schedule->activo)
                                                    <button type="button" class="btn btn-warning btn-pause-schedule"
                                                        data-schedule-id="{{ $schedule->_id }}" title="Pausar">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success btn-resume-schedule"
                                                        data-schedule-id="{{ $schedule->_id }}" title="Reanudar">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-info btn-edit-schedule"
                                                    data-schedule-id="{{ $schedule->_id }}" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <button type="button" class="btn btn-danger btn-delete-schedule"
                                                    data-schedule-id="{{ $schedule->_id }}" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay reportes programados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Vista Previa de Reporte -->
    <div class="modal fade" id="modal-report-preview" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-eye"></i> Vista Previa del Reporte
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="report-preview-content">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-download-preview">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box h3 {
            font-size: 2.2rem;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.85em;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.775rem;
        }

        #dynamic-filters .form-group {
            margin-bottom: 0.5rem;
        }

        .report-progress {
            margin-top: 10px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#reports-history-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "order": [
                    [4, "desc"]
                ]
            });

            $('#scheduled-reports-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });

            // Set default date range (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

            $('#date-to').val(today.toISOString().split('T')[0]);
            $('#date-from').val(thirtyDaysAgo.toISOString().split('T')[0]);

            // Report type change handler
            $('#report-type').change(function() {
                const reportType = $(this).val();
                if (reportType) {
                    loadDynamicFilters(reportType);
                    $('#filters-section').show();
                } else {
                    $('#filters-section').hide();
                }
            });

            // Schedule report checkbox
            $('#schedule-report').change(function() {
                if ($(this).is(':checked')) {
                    $('#schedule-options').show();
                } else {
                    $('#schedule-options').hide();
                }
            });

            // Custom report form submission
            $('#custom-report-form').submit(function(e) {
                e.preventDefault();
                generateCustomReport();
            });

            // Quick report generation
            $('.btn-generate-report').click(function(e) {
                e.preventDefault();
                const reportType = $(this).data('type');
                generateQuickReport(reportType);
            });

            // Report actions
            $(document).on('click', '.btn-download-report', function() {
                const reportId = $(this).data('report-id');
                downloadReport(reportId);
            });

            $(document).on('click', '.btn-view-report', function() {
                const reportId = $(this).data('report-id');
                viewReportDetails(reportId);
            });

            $(document).on('click', '.btn-delete-report', function() {
                const reportId = $(this).data('report-id');
                if (confirm('¿Está seguro de que desea eliminar este reporte?')) {
                    deleteReport(reportId);
                }
            });

            // Schedule actions
            $(document).on('click', '.btn-pause-schedule, .btn-resume-schedule', function() {
                const scheduleId = $(this).data('schedule-id');
                const action = $(this).hasClass('btn-pause-schedule') ? 'pause' : 'resume';
                toggleSchedule(scheduleId, action);
            });

            $(document).on('click', '.btn-delete-schedule', function() {
                const scheduleId = $(this).data('schedule-id');
                if (confirm('¿Está seguro de que desea eliminar este programa de reportes?')) {
                    deleteSchedule(scheduleId);
                }
            });

            // Clear history
            $('#btn-clear-history').click(function() {
                if (confirm('¿Está seguro de que desea limpiar todo el historial de reportes?')) {
                    clearReportsHistory();
                }
            });

            // Functions
            function loadDynamicFilters(reportType) {
                $.ajax({
                    url: '{{ route('informes.filters') }}',
                    method: 'GET',
                    data: {
                        type: reportType
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#dynamic-filters').html(response.html);
                        }
                    },
                    error: function() {
                        toastr.error('Error al cargar los filtros');
                    }
                });
            }

            function generateCustomReport() {
                const formData = new FormData($('#custom-report-form')[0]);

                // Show progress indicator
                const submitBtn = $('#custom-report-form button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generando...').prop('disabled', true);

                $.ajax({
                    url: '{{ route('informes.generate') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Reporte generado correctamente');
                            if (response.download_url) {
                                window.location.href = response.download_url;
                            }
                            // Refresh history table
                            location.reload();
                        } else {
                            toastr.error('Error al generar el reporte: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Error al procesar la solicitud';
                        toastr.error(message);
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            }

            function generateQuickReport(reportType) {
                $.ajax({
                    url: '{{ route('informes.quick') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: reportType,
                        format: 'pdf'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Reporte generado correctamente');
                            if (response.download_url) {
                                window.location.href = response.download_url;
                            }
                        } else {
                            toastr.error('Error al generar el reporte');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function downloadReport(reportId) {
                window.location.href = '{{ route('informes.download', ':id') }}'.replace(':id', reportId);
            }

            function viewReportDetails(reportId) {
                $.ajax({
                    url: '{{ route('informes.preview', ':id') }}'.replace(':id', reportId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#report-preview-content').html(response.html);
                            $('#modal-report-preview').modal('show');
                        } else {
                            toastr.error('Error al cargar la vista previa');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function deleteReport(reportId) {
                $.ajax({
                    url: '{{ route('informes.delete', ':id') }}'.replace(':id', reportId),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Reporte eliminado correctamente');
                            location.reload();
                        } else {
                            toastr.error('Error al eliminar el reporte');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function toggleSchedule(scheduleId, action) {
                $.ajax({
                    url: '{{ route('informes.schedule.toggle', ':id') }}'.replace(':id', scheduleId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Programa de reportes actualizado');
                            location.reload();
                        } else {
                            toastr.error('Error al actualizar el programa');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function deleteSchedule(scheduleId) {
                $.ajax({
                    url: '{{ route('informes.schedule.delete', ':id') }}'.replace(':id', scheduleId),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Programa de reportes eliminado');
                            location.reload();
                        } else {
                            toastr.error('Error al eliminar el programa');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function clearReportsHistory() {
                $.ajax({
                    url: '{{ route('informes.clear-history') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Historial de reportes limpiado');
                            location.reload();
                        } else {
                            toastr.error('Error al limpiar el historial');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }
        });
    </script>
@stop
