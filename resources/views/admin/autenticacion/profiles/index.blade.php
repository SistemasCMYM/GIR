@extends('layouts.dashboard')

@section('title', 'Gestión de Perfiles')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auth.index') }}">Autenticación</a></li>
    <li class="breadcrumb-item active">Perfiles</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Stats Cards Row -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-perfiles">{{ $stats['total'] ?? 0 }}</h3>
                        <p>Total de Perfiles</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="perfiles-activos">{{ $stats['activos'] ?? 0 }}</h3>
                        <p>Perfiles Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="perfiles-inactivos">{{ $stats['inactivos'] ?? 0 }}</h3>
                        <p>Perfiles Inactivos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="usuarios-asignados">{{ $stats['usuarios_asignados'] ?? 0 }}</h3>
                        <p>Usuarios Asignados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Perfiles</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary"
                        onclick="location.href='{{ route('auth.profiles.create') }}'">
                        <i class="fas fa-plus"></i> Nuevo Perfil
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter-estado">Estado:</label>
                            <select class="form-control" id="filter-estado">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filter-search">Búsqueda:</label>
                            <input type="text" class="form-control" id="filter-search"
                                placeholder="Buscar por nombre o descripción...">
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

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="perfiles-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Empresa</th>
                                <th>Permisos</th>
                                <th>Usuarios</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
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

@push('scripts')
    <!-- DataTables JS (requerido por esta vista) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let perfilesTable;

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccionar empresa...'
            });

            // Initialize DataTable
            perfilesTable = $('#perfiles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('auth.profiles.data') }}',
                    data: function(d) {
                        d.estado = $('#filter-estado').val();
                        d.empresa_id = $('#filter-empresa').val();
                        d.search_custom = $('#filter-search').val();
                    }
                },
                columns: [{
                        data: '_id',
                        name: '_id',
                        visible: false
                    },
                    {
                        data: 'nombre',
                        name: 'nombre',
                        render: function(data, type, row) {
                            return `<strong>${data}</strong>`;
                        }
                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion',
                        render: function(data, type, row) {
                            return data || '<span class="text-muted">Sin descripción</span>';
                        },
                        orderable: false
                    },
                    {
                        data: 'empresa',
                        name: 'empresa.nombre',
                        render: function(data, type, row) {
                            if (data && data.nombre) {
                                return `<span class="badge badge-info">${data.nombre}</span>`;
                            }
                            return '<span class="text-muted">General</span>';
                        },
                        orderable: false
                    },
                    {
                        data: 'permisos',
                        name: 'permisos',
                        render: function(data, type, row) {
                            if (data && data.length > 0) {
                                return `<span class="badge badge-primary">${data.length} permisos</span>`;
                            }
                            return '<span class="text-muted">Sin permisos</span>';
                        },
                        orderable: false
                    },
                    {
                        data: 'usuarios_count',
                        name: 'usuarios_count',
                        render: function(data, type, row) {
                            const count = data || 0;
                            const badgeClass = count > 0 ? 'badge-success' : 'badge-secondary';
                            return `<span class="badge ${badgeClass}">${count} usuarios</span>`;
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function(data, type, row) {
                            const badgeClass = data === 'activo' ? 'badge-success' : 'badge-danger';
                            const toggleClass = data === 'activo' ? 'btn-warning' : 'btn-success';
                            const toggleIcon = data === 'activo' ? 'fa-ban' : 'fa-check';
                            const toggleText = data === 'activo' ? 'Desactivar' : 'Activar';

                            return `
                        <span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>
                        <br><small>
                            <button class="btn btn-xs ${toggleClass} mt-1" onclick="togglePerfilStatus('${row._id}', '${data}')">
                                <i class="fas ${toggleIcon}"></i> ${toggleText}
                            </button>
                        </small>
                    `;
                        },
                        className: 'text-center',
                        orderable: false
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
                            return `
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info" onclick="location.href='/auth/profiles/${row._id}'"
                                    title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="location.href='/auth/profiles/${row._id}/edit'"
                                    title="Editar perfil">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deletePerfil('${row._id}', '${row.nombre}')"
                                    title="Eliminar perfil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                        },
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [7, 'desc']
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
            $('#filter-estado, #filter-empresa').change(function() {
                perfilesTable.draw();
            });

            $('#filter-search').on('keyup', function() {
                perfilesTable.draw();
            });
        });

        function clearFilters() {
            $('#filter-estado').val('');
            $('#filter-empresa').val('').trigger('change');
            $('#filter-search').val('');
            perfilesTable.draw();
        }

        function togglePerfilStatus(perfilId, currentStatus) {
            const newStatus = currentStatus === 'activo' ? 'inactivo' : 'activo';
            const action = newStatus === 'activo' ? 'activar' : 'desactivar';

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas ${action} este perfil?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/auth/profiles/${perfilId}/toggle-status`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            estado: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                perfilesTable.draw(false); // Reload without resetting pagination
                                updateStats();
                            } else {
                                toastr.error(response.message ||
                                    'Error al cambiar el estado del perfil');
                            }
                        },
                        error: function() {
                            toastr.error('Error al cambiar el estado del perfil');
                        }
                    });
                }
            });
        }

        function deletePerfil(perfilId, perfilNombre) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar el perfil "${perfilNombre}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/auth/profiles/${perfilId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                perfilesTable.draw(false);
                                updateStats();
                            } else {
                                toastr.error(response.message || 'Error al eliminar el perfil');
                            }
                        },
                        error: function(xhr) {
                            let message = 'Error al eliminar el perfil';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            toastr.error(message);
                        }
                    });
                }
            });
        }

        function updateStats() {
            $.get('{{ route('auth.profiles.stats') }}')
                .done(function(data) {
                    $('#total-perfiles').text(data.total || 0);
                    $('#perfiles-activos').text(data.activos || 0);
                    $('#perfiles-inactivos').text(data.inactivos || 0);
                    $('#usuarios-asignados').text(data.usuarios_asignados || 0);
                });
        }
    </script>
@endpush
