@extends('layouts.dashboard')

@section('title', 'Lista de Reportes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Lista</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Stats Cards Row -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-reportes">{{ $stats['total'] ?? 0 }}</h3>
                        <p>Total de Reportes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="reportes-completados">{{ $stats['completados'] ?? 0 }}</h3>
                        <p>Completados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="reportes-pendientes">{{ $stats['pendientes'] ?? 0 }}</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="reportes-mes">{{ $stats['este_mes'] ?? 0 }}</h3>
                        <p>Este Mes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Administración de Reportes</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="location.href='{{ route('reports.create') }}'">
                        <i class="fas fa-plus"></i> Nuevo Reporte
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportReportes()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter-estado">Estado:</label>
                            <select class="form-control" id="filter-estado">
                                <option value="">Todos</option>
                                <option value="borrador">Borrador</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter-tipo">Tipo:</label>
                            <select class="form-control" id="filter-tipo">
                                <option value="">Todos</option>
                                <option value="sst">Seguridad y Salud en el Trabajo</option>
                                <option value="ambiental">Ambiental</option>
                                <option value="calidad">Calidad</option>
                                <option value="psicosocial">Riesgo Psicosocial</option>
                                <option value="capacitacion">Capacitación</option>
                                <option value="inspeccion">Inspección</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter-empresa">Empresa:</label>
                            <select class="form-control select2" id="filter-empresa">
                                <option value="">Todas las empresas</option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->_id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter-fecha-desde">Desde:</label>
                            <input type="date" class="form-control" id="filter-fecha-desde">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter-fecha-hasta">Hasta:</label>
                            <input type="date" class="form-control" id="filter-fecha-hasta">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-secondary btn-block" onclick="clearFilters()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control" id="filter-search"
                                placeholder="Buscar por título, descripción o responsable...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchReportes()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="reportes-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Empresa</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th>Progreso</th>
                                <th>Fecha Límite</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let reportesTable;

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccionar empresa...'
            });

            // Initialize DataTable
            reportesTable = $('#reportes-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('reports.data') }}',
                    data: function(d) {
                        d.estado = $('#filter-estado').val();
                        d.tipo = $('#filter-tipo').val();
                        d.empresa_id = $('#filter-empresa').val();
                        d.fecha_desde = $('#filter-fecha-desde').val();
                        d.fecha_hasta = $('#filter-fecha-hasta').val();
                        d.search_custom = $('#filter-search').val();
                    }
                },
                columns: [{
                        data: '_id',
                        name: '_id',
                        visible: false
                    },
                    {
                        data: 'titulo',
                        name: 'titulo',
                        render: function(data, type, row) {
                            return `<strong>${data}</strong><br><small class="text-muted">${row.descripcion || 'Sin descripción'}</small>`;
                        }
                    },
                    {
                        data: 'tipo',
                        name: 'tipo',
                        render: function(data, type, row) {
                            const tipos = {
                                'sst': {
                                    label: 'SST',
                                    class: 'badge-primary'
                                },
                                'ambiental': {
                                    label: 'Ambiental',
                                    class: 'badge-success'
                                },
                                'calidad': {
                                    label: 'Calidad',
                                    class: 'badge-info'
                                },
                                'psicosocial': {
                                    label: 'Psicosocial',
                                    class: 'badge-warning'
                                },
                                'capacitacion': {
                                    label: 'Capacitación',
                                    class: 'badge-secondary'
                                },
                                'inspeccion': {
                                    label: 'Inspección',
                                    class: 'badge-dark'
                                }
                            };
                            const tipo = tipos[data] || {
                                label: data,
                                class: 'badge-light'
                            };
                            return `<span class="badge ${tipo.class}">${tipo.label}</span>`;
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'empresa',
                        name: 'empresa.nombre',
                        render: function(data, type, row) {
                            if (data && data.nombre) {
                                return `<span class="badge badge-info">${data.nombre}</span>`;
                            }
                            return '<span class="text-muted">Sin empresa</span>';
                        },
                        orderable: false
                    },
                    {
                        data: 'responsable',
                        name: 'responsable.nombre',
                        render: function(data, type, row) {
                            if (data) {
                                return `${data.nombre} ${data.apellido}<br><small class="text-muted">${data.email}</small>`;
                            }
                            return '<span class="text-muted">Sin responsable</span>';
                        },
                        orderable: false
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function(data, type, row) {
                            const estados = {
                                'borrador': {
                                    label: 'Borrador',
                                    class: 'badge-secondary'
                                },
                                'pendiente': {
                                    label: 'Pendiente',
                                    class: 'badge-warning'
                                },
                                'en_proceso': {
                                    label: 'En Proceso',
                                    class: 'badge-info'
                                },
                                'completado': {
                                    label: 'Completado',
                                    class: 'badge-success'
                                },
                                'cancelado': {
                                    label: 'Cancelado',
                                    class: 'badge-danger'
                                }
                            };
                            const estado = estados[data] || {
                                label: data,
                                class: 'badge-light'
                            };
                            return `<span class="badge ${estado.class}">${estado.label}</span>`;
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'progreso',
                        name: 'progreso',
                        render: function(data, type, row) {
                            const progreso = data || 0;
                            const colorClass = progreso < 30 ? 'bg-danger' : progreso < 70 ?
                                'bg-warning' : 'bg-success';
                            return `
                        <div class="progress progress-sm">
                            <div class="progress-bar ${colorClass}" style="width: ${progreso}%"></div>
                        </div>
                        <small>${progreso}%</small>
                    `;
                        },
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'fecha_limite',
                        name: 'fecha_limite',
                        render: function(data, type, row) {
                            if (data) {
                                const fecha = new Date(data);
                                const hoy = new Date();
                                const isOverdue = fecha < hoy && row.estado !== 'completado';
                                const dateStr = fecha.toLocaleDateString('es-ES');

                                if (isOverdue) {
                                    return `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${dateStr}</span>`;
                                }
                                return dateStr;
                            }
                            return '<span class="text-muted">Sin fecha</span>';
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            if (data) {
                                const date = new Date(data);
                                return date.toLocaleDateString('es-ES') +
                                    '<br><small class="text-muted">' +
                                    date.toLocaleTimeString('es-ES', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) + '</small>';
                            }
                            return '<span class="text-muted">Sin fecha</span>';
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        render: function(data, type, row) {
                            let actions = `
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info" onclick="location.href='/reports/${row._id}'"
                                    title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                    `;

                            if (row.estado !== 'completado' && row.estado !== 'cancelado') {
                                actions += `
                            <button type="button" class="btn btn-sm btn-warning" onclick="location.href='/reports/${row._id}/edit'"
                                    title="Editar reporte">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                            }

                            actions += `
                            <button type="button" class="btn btn-sm btn-success" onclick="downloadReporte('${row._id}')"
                                    title="Descargar reporte">
                                <i class="fas fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteReporte('${row._id}', '${row.titulo}')"
                                    title="Eliminar reporte">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;

                            return actions;
                        },
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [8, 'desc']
                ], // Order by created_at desc
                pageLength: 25,
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                drawCallback: function() {
                    // Update stats after table draw
                    updateStats();
                }
            });

            // Filter events
            $('#filter-estado, #filter-tipo, #filter-empresa, #filter-fecha-desde, #filter-fecha-hasta').change(
                function() {
                    reportesTable.draw();
                });

            $('#filter-search').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    reportesTable.draw();
                }
            });
        });

        function clearFilters() {
            $('#filter-estado, #filter-tipo').val('');
            $('#filter-empresa').val('').trigger('change');
            $('#filter-fecha-desde, #filter-fecha-hasta, #filter-search').val('');
            reportesTable.draw();
        }

        function searchReportes() {
            reportesTable.draw();
        }

        function deleteReporte(reporteId, reporteTitulo) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar el reporte "${reporteTitulo}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/reports/${reporteId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                reportesTable.draw(false);
                                updateStats();
                            } else {
                                toastr.error(response.message || 'Error al eliminar el reporte');
                            }
                        },
                        error: function(xhr) {
                            let message = 'Error al eliminar el reporte';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            toastr.error(message);
                        }
                    });
                }
            });
        }

        function downloadReporte(reporteId) {
            window.location.href = `/reports/${reporteId}/download`;
        }

        function exportReportes() {
            const filters = {
                estado: $('#filter-estado').val(),
                tipo: $('#filter-tipo').val(),
                empresa_id: $('#filter-empresa').val(),
                fecha_desde: $('#filter-fecha-desde').val(),
                fecha_hasta: $('#filter-fecha-hasta').val(),
                search: $('#filter-search').val()
            };

            const queryString = Object.keys(filters)
                .filter(key => filters[key])
                .map(key => `${key}=${encodeURIComponent(filters[key])}`)
                .join('&');

            window.location.href = `/reports/export?${queryString}`;
        }

        function updateStats() {
            $.get('{{ route('reports.stats') }}')
                .done(function(data) {
                    $('#total-reportes').text(data.total || 0);
                    $('#reportes-completados').text(data.completados || 0);
                    $('#reportes-pendientes').text(data.pendientes || 0);
                    $('#reportes-mes').text(data.este_mes || 0);
                });
        }
    </script>
@endsection
