@extends('layouts.dashboard')

@section('title', 'Módulo de Reportes - GIR-365')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reportes</li>
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- Alerts -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Reportes</span>
                                <span class="info-box-number">{{ $estadisticas['total_reportes'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pendientes</span>
                                <span class="info-box-number">{{ $estadisticas['reportes_pendientes'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Completados</span>
                                <span class="info-box-number">{{ $estadisticas['reportes_completados'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Este Mes</span>
                                <span class="info-box-number">{{ $estadisticas['reportes_mes_actual'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plus mr-2"></i>
                                    Generar Reportes
                                </h3>
                            </div>
                            <div class="card-body">
                                <p>Crea y genera nuevos reportes del sistema.</p>
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ route('reports.create') }}" class="btn btn-primary btn-block">
                                            <i class="fas fa-file-plus mr-1"></i>
                                            Nuevo Reporte
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('reports.reports') }}" class="btn btn-info btn-block">
                                            <i class="fas fa-list mr-1"></i>
                                            Ver Reportes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Reportes Predefinidos
                                </h3>
                            </div>
                            <div class="card-body">
                                <p>Accede a reportes predefinidos del sistema.</p>
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ route('reports.create') }}?tipo=hallazgos"
                                            class="btn btn-success btn-block">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Hallazgos
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('reports.create') }}?tipo=empleados"
                                            class="btn btn-warning btn-block">
                                            <i class="fas fa-users mr-1"></i>
                                            Empleados
                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <a href="{{ route('reports.create') }}?tipo=psicosocial"
                                            class="btn btn-secondary btn-block">
                                            <i class="fas fa-brain mr-1"></i>
                                            Psicosocial
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('reports.create') }}?tipo=planes"
                                            class="btn btn-primary btn-block">
                                            <i class="fas fa-tasks mr-1"></i>
                                            Planes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history mr-2"></i>
                                    Reportes Recientes
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('reports.reports') }}" class="btn btn-tool">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="recent-reports-container">
                                    <div class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando reportes recientes...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Companies Access (for admin users) -->
                @if (count($empresasDisponibles) > 1)
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-building mr-2"></i>
                                        Empresas Disponibles
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p>Tienes acceso a reportes de las siguientes empresas:</p>
                                    <div class="row">
                                        @foreach ($empresasDisponibles as $empresa)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="small-box bg-light">
                                                    <div class="inner">
                                                        <h4>{{ $empresa->nombre }}</h4>
                                                        <p>{{ $empresa->sector ?? 'Sin sector' }}</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <a href="{{ route('reports.reports') }}?empresa_id={{ $empresa->_id }}"
                                                        class="small-box-footer">
                                                        Ver Reportes <i class="fas fa-arrow-circle-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats Charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-2"></i>
                                    Reportes por Tipo
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="reportes-tipo-chart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Tendencia Mensual
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="reportes-tendencia-chart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Load recent reports
            loadRecentReports();

            // Load charts
            loadReportsCharts();

            // Auto-refresh every 30 seconds
            setInterval(function() {
                loadRecentReports();
            }, 30000);
        });

        function loadRecentReports() {
            $.ajax({
                url: '{{ route('reports.recent') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var html = '';
                        if (response.reportes.length > 0) {
                            html = '<div class="table-responsive"><table class="table table-sm">';
                            html +=
                                '<thead><tr><th>Nombre</th><th>Tipo</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead><tbody>';

                            response.reportes.forEach(function(reporte) {
                                var statusBadge = getStatusBadge(reporte.estado);
                                var actions = getReportActions(reporte);

                                html += '<tr>';
                                html += '<td>' + reporte.nombre + '</td>';
                                html += '<td>' + reporte.tipo + '</td>';
                                html += '<td>' + statusBadge + '</td>';
                                html += '<td>' + reporte.created_at + '</td>';
                                html += '<td>' + actions + '</td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table></div>';
                        } else {
                            html = '<div class="text-center text-muted">No hay reportes recientes</div>';
                        }

                        $('#recent-reports-container').html(html);
                    }
                },
                error: function() {
                    $('#recent-reports-container').html(
                        '<div class="text-center text-danger">Error al cargar reportes</div>');
                }
            });
        }

        function getStatusBadge(estado) {
            var badges = {
                'pendiente': '<span class="badge badge-warning">Pendiente</span>',
                'procesando': '<span class="badge badge-info">Procesando</span>',
                'completado': '<span class="badge badge-success">Completado</span>',
                'error': '<span class="badge badge-danger">Error</span>'
            };
            return badges[estado] || '<span class="badge badge-secondary">Desconocido</span>';
        }

        function getReportActions(reporte) {
            var actions = '<div class="btn-group btn-group-sm">';
            actions += '<a href="/modules/reports/' + reporte.id +
                '" class="btn btn-info btn-xs" title="Ver"><i class="fas fa-eye"></i></a>';

            if (reporte.estado === 'completado' && reporte.archivo_generado) {
                actions += '<a href="/modules/reports/' + reporte.id +
                    '/download" class="btn btn-success btn-xs" title="Descargar"><i class="fas fa-download"></i></a>';
            }

            actions += '</div>';
            return actions;
        }

        function loadReportsCharts() {
            // Reports by type chart
            $.ajax({
                url: '{{ route('reports.stats.tipo') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var canvas = document.getElementById('reportes-tipo-chart');
                        if (canvas) {
                            var ctx = canvas.getContext('2d');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: response.labels,
                                    datasets: [{
                                        data: response.data,
                                        backgroundColor: [
                                            '#007bff',
                                            '#28a745',
                                            '#ffc107',
                                            '#dc3545',
                                            '#6c757d'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            });
                        }
                    }
                }
            });

            // Monthly trend chart
            $.ajax({
                url: '{{ route('reports.stats.tendencia') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var trendCanvas = document.getElementById('reportes-tendencia-chart');
                        if (trendCanvas) {
                            var ctx = trendCanvas.getContext('2d');
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: response.labels,
                                    datasets: [{
                                        label: 'Reportes Generados',
                                        data: response.data,
                                        borderColor: '#007bff',
                                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                        tension: 0.1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
            });
        }
    </script>
@endsection
