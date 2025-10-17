@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios - GIR-365')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-users mr-2"></i>Gestión de Usuarios</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/Inicio">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="/modules/auth">Autenticación</a></li>
                            <li class="breadcrumb-item active">Usuarios</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

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

                <!-- Filters Card -->
                <div class="card card-outline card-primary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter mr-1"></i>
                            Filtros de Búsqueda
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="display: none;">
                        <form id="filtros-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Perfil</label>
                                        <select class="form-control" id="perfil-filter">
                                            <option value="">Todos los perfiles</option>
                                            @foreach ($perfiles as $perfil)
                                                <option value="{{ $perfil->_id }}">{{ $perfil->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Empresa</label>
                                        <select class="form-control" id="empresa-filter">
                                            <option value="">Todas las empresas</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->_id }}">{{ $empresa->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control" id="status-filter">
                                            <option value="">Todos</option>
                                            <option value="1">Activos</option>
                                            <option value="0">Inactivos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" class="btn btn-primary" id="aplicar-filtros">
                                                <i class="fas fa-search mr-1"></i>
                                                Filtrar
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="limpiar-filtros">
                                                <i class="fas fa-eraser mr-1"></i>
                                                Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Users Table Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Usuarios</h3>
                        <div class="card-tools">
                            <a href="{{ route('auth.users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i>
                                Nuevo Usuario
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usuarios-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Perfil</th>
                                        <th>Empresa</th>
                                        <th>Estado</th>
                                        <th>Último Acceso</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Status Toggle Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cambiar Estado</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="status-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-status-change">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#usuarios-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('auth.users.data') }}',
                    data: function(d) {
                        d.perfil_filter = $('#perfil-filter').val();
                        d.empresa_filter = $('#empresa-filter').val();
                        d.status_filter = $('#status-filter').val();
                    }
                },
                columns: [{
                        data: 'nombre',
                        name: 'nombre'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'perfil',
                        name: 'perfil'
                    },
                    {
                        data: 'empresa',
                        name: 'empresa'
                    },
                    {
                        data: 'activo',
                        name: 'activo',
                        render: function(data, type, row) {
                            var badgeClass = data ? 'badge-success' : 'badge-danger';
                            var text = data ? 'Activo' : 'Inactivo';
                            return '<span class="badge ' + badgeClass + '">' + text + '</span>';
                        }
                    },
                    {
                        data: 'ultimo_acceso',
                        name: 'ultimo_acceso'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var statusBtn = row.activo ?
                                '<button class="btn btn-warning btn-sm toggle-status" data-id="' +
                                row.id +
                                '" title="Desactivar"><i class="fas fa-times"></i></button>' :
                                '<button class="btn btn-success btn-sm toggle-status" data-id="' +
                                row.id + '" title="Activar"><i class="fas fa-check"></i></button>';

                            return '<div class="btn-group">' +
                                '<a href="/modules/auth/users/' + row.id +
                                '" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>' +
                                '<a href="/modules/auth/users/' + row.id +
                                '/edit" class="btn btn-primary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>' +
                                statusBtn +
                                '</div>';
                        }
                    }
                ],
                order: [
                    [6, 'desc']
                ],
                pageLength: 25,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });

            // Apply filters
            $('#aplicar-filtros').click(function() {
                table.draw();
            });

            // Clear filters
            $('#limpiar-filtros').click(function() {
                $('#filtros-form')[0].reset();
                table.draw();
            });

            // Handle status toggle
            var userIdToToggle = null;
            $(document).on('click', '.toggle-status', function() {
                userIdToToggle = $(this).data('id');
                var isActive = $(this).hasClass('btn-warning');
                var action = isActive ? 'desactivar' : 'activar';
                $('#status-message').text('¿Está seguro que desea ' + action + ' este usuario?');
                $('#statusModal').modal('show');
            });

            $('#confirm-status-change').click(function() {
                if (userIdToToggle) {
                    $.ajax({
                        url: '/modules/auth/users/' + userIdToToggle + '/toggle-status',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.draw(false);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error al cambiar el estado del usuario');
                        }
                    });
                }
                $('#statusModal').modal('hide');
                userIdToToggle = null;
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection

@section('styles')
    <style>
        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }
    </style>
@endsection
