@extends('layouts.dashboard')

@section('title', 'Gestión de Hallazgos - ' . ($empresaData->nombre ?? 'GIR-365'))

@section('breadcrumb')
    <li class="breadcrumb-item active">Hallazgos</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>Gestión de Hallazgos
                        </h1>
                        <p class="mb-0 text-muted">Seguimiento y gestión de hallazgos de seguridad y salud en el trabajo</p>
                    </div>
                    <div>
                        <a href="{{ route('hallazgos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Nuevo Hallazgo
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="criticalCount">0</h3>
                        <p>Críticos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="highCount">0</h3>
                        <p>Alto Riesgo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="mediumCount">0</h3>
                        <p>Medio Riesgo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="lowCount">0</h3>
                        <p>Bajo Riesgo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('hallazgos.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo Hallazgo
                        </a>
                        <button type="button" class="btn btn-info" onclick="exportHallazgos()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Área</label>
                            <select name="area" class="form-control select2" style="width: 100%;">
                                <option value="">Todas las áreas</option>
                                <option value="seguridad">Seguridad</option>
                                <option value="calidad">Calidad</option>
                                <option value="ambiental">Ambiental</option>
                                <option value="operacional">Operacional</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nivel de Riesgo</label>
                            <select name="nivel_riesgo" class="form-control select2" style="width: 100%;">
                                <option value="">Todos los niveles</option>
                                <option value="critico">Crítico</option>
                                <option value="alto">Alto</option>
                                <option value="medio">Medio</option>
                                <option value="bajo">Bajo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" class="form-control select2" style="width: 100%;">
                                <option value="">Todos los estados</option>
                                <option value="abierto">Abierto</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="cerrado">Cerrado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Búsqueda</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar hallazgos...">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hallazgos Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Lista de Hallazgos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hallazgosTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Área</th>
                                <th>Nivel de Riesgo</th>
                                <th>Estado</th>
                                <th>Responsable</th>
                                <th>Fecha Límite</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Distribución por Riesgo</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="riskChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Tendencia Mensual</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet"
        href="//cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@stop

@section('js')
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize components
            $('.select2').select2({
                theme: 'bootstrap'
            });

            // Initialize DataTable
            const table = $('#hallazgosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('hallazgos.data') }}',
                    data: function(d) {
                        d.area = $('select[name="area"]').val();
                        d.nivel_riesgo = $('select[name="nivel_riesgo"]').val();
                        d.estado = $('select[name="estado"]').val();
                        d.search = $('input[name="search"]').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion'
                    },
                    {
                        data: 'area',
                        name: 'area'
                    },
                    {
                        data: 'nivel_riesgo',
                        name: 'nivel_riesgo'
                    },
                    {
                        data: 'estado',
                        name: 'estado'
                    },
                    {
                        data: 'responsable',
                        name: 'responsable'
                    },
                    {
                        data: 'fecha_limite',
                        name: 'fecha_limite'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 25,
                responsive: true
            });

            // Filter form changes
            $('#filterForm select, #filterForm input').on('change keyup', function() {
                table.draw();
            });

            // Load statistics
            loadStats();
            loadCharts();
        });

        function loadStats() {
            $.get('{{ route('hallazgos.stats') }}', function(data) {
                $('#criticalCount').text(data.critico || 0);
                $('#highCount').text(data.alto || 0);
                $('#mediumCount').text(data.medio || 0);
                $('#lowCount').text(data.bajo || 0);
            });
        }

        function loadCharts() {
            // Risk Distribution Chart
            $.get('{{ route('hallazgos.chart-data') }}', {
                type: 'risk'
            }, function(data) {
                const ctx = document.getElementById('riskChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#28a745']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });

            // Trend Chart
            $.get('{{ route('hallazgos.chart-data') }}', {
                type: 'trend'
            }, function(data) {
                const ctx = document.getElementById('trendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Hallazgos por mes',
                            data: data.values,
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
            });
        }

        function exportHallazgos() {
            const filters = new URLSearchParams({
                area: $('select[name="area"]').val(),
                nivel_riesgo: $('select[name="nivel_riesgo"]').val(),
                estado: $('select[name="estado"]').val(),
                search: $('input[name="search"]').val()
            });

            window.open('{{ route('hallazgos.export') }}?' + filters.toString(), '_blank');
        }

        function deleteHallazgo(id) {
            Swal.fire({
                title: '¿Está seguro?',
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
                        url: '/hallazgos/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#hallazgosTable').DataTable().ajax.reload();
                            loadStats();
                            Swal.fire('Eliminado', 'El hallazgo ha sido eliminado', 'success');
                        }
                    });
                }
            });
        }
    </script>
@stop
