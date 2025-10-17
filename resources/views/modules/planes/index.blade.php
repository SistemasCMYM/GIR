@extends('layouts.dashboard')

@section('title', 'Gestión de Planes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Planes</li>
@endsection

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-clipboard-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Planes</span>
                    <span class="info-box-number" id="total-planes">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completados</span>
                    <span class="info-box-number" id="planes-completados">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">En Progreso</span>
                    <span class="info-box-number" id="planes-progreso">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Vencidos</span>
                    <span class="info-box-number" id="planes-vencidos">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Lista de Planes
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <a href="{{ route('planes.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Plan
                        </a>
                        <button type="button" class="btn btn-info btn-sm" id="export-excel">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="planes-table" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Progreso</th>
                                    <th>Responsable</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Prioridad</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Distribución por Estado
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="estadoChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Planes por Mes
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="tendenciaChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter"></i> Filtros Avanzados
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter-estado" class="form-label">Estado</label>
                                    <select class="form-select" id="filter-estado" name="estado">
                                        <option value="">Todos</option>
                                        <option value="borrador">Borrador</option>
                                        <option value="revision">En Revisión</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="en_progreso">En Progreso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter-tipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="filter-tipo" name="tipo">
                                        <option value="">Todos</option>
                                        <option value="accion_correctiva">Acción Correctiva</option>
                                        <option value="accion_preventiva">Acción Preventiva</option>
                                        <option value="mejora_continua">Mejora Continua</option>
                                        <option value="capacitacion">Capacitación</option>
                                        <option value="inspeccion">Inspección</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter-prioridad" class="form-label">Prioridad</label>
                                    <select class="form-select" id="filter-prioridad" name="prioridad">
                                        <option value="">Todas</option>
                                        <option value="baja">Baja</option>
                                        <option value="media">Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="critica">Crítica</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter-responsable" class="form-label">Responsable</label>
                                    <input type="text" class="form-control" id="filter-responsable"
                                        name="responsable" placeholder="Nombre del responsable">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter-fecha-inicio" class="form-label">Fecha Inicio Desde</label>
                                    <input type="date" class="form-control" id="filter-fecha-inicio"
                                        name="fecha_inicio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter-fecha-fin" class="form-label">Fecha Fin Hasta</label>
                                    <input type="date" class="form-control" id="filter-fecha-fin" name="fecha_fin">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clear-filters">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                    <button type="button" class="btn btn-primary" id="apply-filters">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css">
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge-prioridad-baja {
            background-color: #28a745;
            color: white;
        }

        .badge-prioridad-media {
            background-color: #ffc107;
            color: black;
        }

        .badge-prioridad-alta {
            background-color: #fd7e14;
            color: white;
        }

        .badge-prioridad-critica {
            background-color: #dc3545;
            color: white;
        }

        .progress {
            height: 20px;
        }

        .info-box-number {
            font-size: 1.8rem !important;
            font-weight: 700;
        }

        .card-header {
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endpush

@push('js')
    <script src="//cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="//cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="//cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="//cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let planesTable;
            let estadoChart, tendenciaChart;

            // Initialize DataTable
            function initializeDataTable() {
                planesTable = $('#planes-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: '{{ route('planes.data') }}',
                        data: function(d) {
                            d.estado = $('#filter-estado').val();
                            d.tipo = $('#filter-tipo').val();
                            d.prioridad = $('#filter-prioridad').val();
                            d.responsable = $('#filter-responsable').val();
                            d.fecha_inicio = $('#filter-fecha-inicio').val();
                            d.fecha_fin = $('#filter-fecha-fin').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'titulo',
                            name: 'titulo'
                        },
                        {
                            data: 'tipo',
                            name: 'tipo'
                        },
                        {
                            data: 'estado',
                            name: 'estado'
                        },
                        {
                            data: 'progreso',
                            name: 'progreso',
                            orderable: false
                        },
                        {
                            data: 'responsable',
                            name: 'responsable'
                        },
                        {
                            data: 'fecha_inicio',
                            name: 'fecha_inicio'
                        },
                        {
                            data: 'fecha_fin',
                            name: 'fecha_fin'
                        },
                        {
                            data: 'prioridad',
                            name: 'prioridad'
                        },
                        {
                            data: 'acciones',
                            name: 'acciones',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    }]
                });
            }

            // Load statistics
            function loadStats() {
                $.get('{{ route('planes.stats') }}')
                    .done(function(data) {
                        $('#total-planes').html(data.total || 0);
                        $('#planes-completados').html(data.completados || 0);
                        $('#planes-progreso').html(data.en_progreso || 0);
                        $('#planes-vencidos').html(data.vencidos || 0);
                    })
                    .fail(function() {
                        $('#total-planes, #planes-completados, #planes-progreso, #planes-vencidos').html(
                            'Error');
                    });
            }

            // Initialize charts
            function initializeCharts() {
                // Estado Chart
                const estadoCtx = document.getElementById('estadoChart').getContext('2d');
                estadoChart = new Chart(estadoCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [
                                '#28a745', '#ffc107', '#17a2b8',
                                '#fd7e14', '#dc3545', '#6c757d'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Tendencia Chart
                const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
                tendenciaChart = new Chart(tendenciaCtx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Planes Creados',
                            data: [],
                            backgroundColor: '#007bff'
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

            // Load chart data
            function loadChartData() {
                $.get('{{ route('planes.chart-data') }}')
                    .done(function(data) {
                        // Update estado chart
                        if (data.estado) {
                            estadoChart.data.labels = data.estado.labels;
                            estadoChart.data.datasets[0].data = data.estado.data;
                            estadoChart.update();
                        }

                        // Update tendencia chart
                        if (data.tendencia) {
                            tendenciaChart.data.labels = data.tendencia.labels;
                            tendenciaChart.data.datasets[0].data = data.tendencia.data;
                            tendenciaChart.update();
                        }
                    })
                    .fail(function() {
                        console.error('Error loading chart data');
                    });
            }

            // Filter functionality
            $('#apply-filters').click(function() {
                planesTable.ajax.reload();
                $('#filterModal').modal('hide');
            });

            $('#clear-filters').click(function() {
                $('#filter-form')[0].reset();
                planesTable.ajax.reload();
                $('#filterModal').modal('hide');
            });

            // Export functionality
            $('#export-excel').click(function() {
                window.location = '{{ route('planes.export') }}';
            });

            // Delete functionality
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const titulo = $(this).data('titulo');

                Swal.fire({
                    title: '¿Está seguro?',
                    text: `¿Desea eliminar el plan "${titulo}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.index') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Eliminado', response.message, 'success');
                                    planesTable.ajax.reload();
                                    loadStats();
                                    loadChartData();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al eliminar el plan', 'error');
                            }
                        });
                    }
                });
            });

            // Approve/Reject functionality
            $(document).on('click', '.btn-approve', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Aprobar Plan',
                    text: '¿Está seguro de aprobar este plan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('planes.index') }}/${id}/approve`,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Aprobado', response.message, 'success');
                                    planesTable.ajax.reload();
                                    loadStats();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al aprobar el plan', 'error');
                            }
                        });
                    }
                });
            });

            // Initialize everything
            initializeDataTable();
            loadStats();
            initializeCharts();
            loadChartData();

            // Refresh data every 5 minutes
            setInterval(function() {
                loadStats();
                loadChartData();
            }, 300000);
        });
    </script>
@endpush
