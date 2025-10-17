@extends('layouts.dashboard')

@section('title', 'Gestión de Sesiones Activas')

@section('page-title', 'Gestión de Sesiones Activas')

@section('content')
    <div class="container-fluid">
        <!-- Page Head -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">
                        <i class="fas fa-user-clock me-2"></i>
                        Gestión de Sesiones Activas
                    </h4>
                    <p class="page-title-desc">Monitoreo y control de sesiones de usuario</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas Silva Dashboard -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card gir-gpu-accelerated">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-primary-subtle">
                                    <span class="avatar-title bg-primary rounded-circle">
                                        <i class="fas fa-users fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-0 stat-number" id="total-sessions">{{ $totalSessions ?? 0 }}</h3>
                                <p class="text-muted mb-0">Sesiones Activas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card gir-gpu-accelerated">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-success-subtle">
                                    <span class="avatar-title bg-success rounded-circle">
                                        <i class="fas fa-user-check fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-0 stat-number" id="online-users">{{ $onlineUsers ?? 0 }}</h3>
                                <p class="text-muted mb-0">Usuarios Conectados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card gir-gpu-accelerated">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning-subtle">
                                    <span class="avatar-title bg-warning rounded-circle">
                                        <i class="fas fa-clock fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-0 stat-number" id="expired-sessions">{{ $expiredSessions ?? 0 }}</h3>
                                <p class="text-muted mb-0">Sesiones Expiradas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card gir-gpu-accelerated">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-danger-subtle">
                                    <span class="avatar-title bg-danger rounded-circle">
                                        <i class="fas fa-ban fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-0 stat-number" id="blocked-sessions">{{ $blockedSessions ?? 0 }}</h3>
                                <p class="text-muted mb-0">Sesiones Bloqueadas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Acciones Silva -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-filter me-2"></i>
                                Filtros y Acciones
                            </h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger btn-sm" id="btn-expire-all">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Expirar Todas
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refresh">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Estado de Sesión</label>
                                    <select class="form-select" id="filter-status">
                                        <option value="">Todos los estados</option>
                                        <option value="active">Activa</option>
                                        <option value="expired">Expirada</option>
                                        <option value="blocked">Bloqueada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Usuario</label>
                                    <input type="text" class="form-control" id="filter-user"
                                        placeholder="Buscar por usuario...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="filter-ip"
                                        placeholder="Buscar por IP...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="filter-ip" placeholder="Buscar por IP...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Rango de Fecha</label>
                                <input type="date" class="form-control" id="filter-date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Sesiones Silva -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Sesiones Activas
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap align-middle" id="sessions-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="select-all">
                                            <label class="form-check-label" for="select-all"></label>
                                        </div>
                                    </th>
                                    <th>Usuario</th>
                                    <th>Perfil</th>
                                    <th>IP Address</th>
                                    <th>Navegador</th>
                                    <th>Ubicación</th>
                                    <th>Inicio de Sesión</th>
                                    <th>Última Actividad</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions ?? [] as $session)
                                    <tr data-session-id="{{ $session->_id }}">
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" class="session-checkbox"
                                                    value="{{ $session->_id }}">
                                                <label></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar mr-2">
                                                    <img src="{{ $session->usuario->avatar ?? asset('img/default-avatar.png') }}"
                                                        class="img-circle elevation-2" width="30" height="30"
                                                        alt="Avatar">
                                                </div>
                                                <div>
                                                    <strong>{{ $session->usuario->nombre ?? 'N/A' }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $session->usuario->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $session->usuario->perfil->nombre ?? 'Sin perfil' }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $session->ip_address ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <small>{{ $session->user_agent ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                            {{ $session->location ?? 'Desconocida' }}
                                        </td>
                                        <td>
                                            <small>
                                                {{ $session->created_at ? $session->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $session->last_activity ? $session->last_activity->format('d/m/Y H:i:s') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = '';
                                                $statusText = '';
                                                $statusIcon = '';

                                                if ($session->estado === 'activa') {
                                                    $statusClass = 'badge-success';
                                                    $statusText = 'Activa';
                                                    $statusIcon = 'fa-check-circle';
                                                } elseif ($session->estado === 'expirada') {
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Expirada';
                                                    $statusIcon = 'fa-clock';
                                                } else {
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Bloqueada';
                                                    $statusIcon = 'fa-ban';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if ($session->estado === 'activa')
                                                    <button type="button" class="btn btn-warning btn-expire-session"
                                                        data-session-id="{{ $session->_id }}" title="Expirar Sesión">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-danger btn-delete-session"
                                                    data-session-id="{{ $session->_id }}" title="Eliminar Sesión">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                <button type="button" class="btn btn-info btn-session-details"
                                                    data-session-id="{{ $session->_id }}" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay sesiones activas registradas
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

    <!-- Modal de Detalles de Sesión -->
    <div class="modal fade" id="modal-session-details" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-info-circle"></i> Detalles de la Sesión
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="session-details-content">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .user-avatar img {
            object-fit: cover;
        }

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
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#sessions-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "order": [
                    [7, "desc"]
                ] // Order by last activity
            });

            // Auto-refresh every 30 seconds
            let autoRefreshInterval = setInterval(function() {
                refreshSessionData();
            }, 30000);

            // Manual refresh button
            $('#btn-refresh').click(function() {
                refreshSessionData();
            });

            // Expire all sessions
            $('#btn-expire-all').click(function() {
                if (confirm('¿Está seguro de que desea expirar todas las sesiones activas?')) {
                    expireAllSessions();
                }
            });

            // Select all checkbox
            $('#select-all').change(function() {
                $('.session-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Individual session actions
            $(document).on('click', '.btn-expire-session', function() {
                const sessionId = $(this).data('session-id');
                if (confirm('¿Está seguro de que desea expirar esta sesión?')) {
                    expireSession(sessionId);
                }
            });

            $(document).on('click', '.btn-delete-session', function() {
                const sessionId = $(this).data('session-id');
                if (confirm('¿Está seguro de que desea eliminar esta sesión?')) {
                    deleteSession(sessionId);
                }
            });

            $(document).on('click', '.btn-session-details', function() {
                const sessionId = $(this).data('session-id');
                showSessionDetails(sessionId);
            });

            // Filters
            $('#filter-status, #filter-user, #filter-ip, #filter-date').on('keyup change', function() {
                applyFilters();
            });

            // Functions
            function refreshSessionData() {
                $.ajax({
                    url: '{{ route('admin.autenticacion.sesiones.refresh') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            updateSessionStats(response.stats);
                            table.clear().rows.add(response.sessions).draw();
                        }
                    },
                    error: function() {
                        toastr.error('Error al actualizar los datos de sesiones');
                    }
                });
            }

            function expireAllSessions() {
                $.ajax({
                    url: '{{ route('admin.autenticacion.sesiones.expire-all') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Todas las sesiones han sido expiradas');
                            refreshSessionData();
                        } else {
                            toastr.error('Error al expirar las sesiones');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function expireSession(sessionId) {
                $.ajax({
                    url: '{{ route('admin.autenticacion.sesiones.expire', ':id') }}'.replace(':id',
                        sessionId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Sesión expirada correctamente');
                            refreshSessionData();
                        } else {
                            toastr.error('Error al expirar la sesión');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function deleteSession(sessionId) {
                $.ajax({
                    url: '{{ route('admin.autenticacion.sesiones.delete', ':id') }}'.replace(':id',
                        sessionId),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Sesión eliminada correctamente');
                            refreshSessionData();
                        } else {
                            toastr.error('Error al eliminar la sesión');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function showSessionDetails(sessionId) {
                $.ajax({
                    url: '{{ route('admin.autenticacion.sesiones.details', ':id') }}'.replace(':id',
                        sessionId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#session-details-content').html(response.html);
                            $('#modal-session-details').modal('show');
                        } else {
                            toastr.error('Error al cargar los detalles de la sesión');
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    }
                });
            }

            function updateSessionStats(stats) {
                $('#total-sessions').text(stats.total || 0);
                $('#online-users').text(stats.online || 0);
                $('#expired-sessions').text(stats.expired || 0);
                $('#blocked-sessions').text(stats.blocked || 0);
            }

            function applyFilters() {
                const status = $('#filter-status').val();
                const user = $('#filter-user').val();
                const ip = $('#filter-ip').val();
                const date = $('#filter-date').val();

                // Apply filters to DataTable
                if (status) {
                    table.column(8).search(status);
                }
                if (user) {
                    table.column(1).search(user);
                }
                if (ip) {
                    table.column(3).search(ip);
                }

                table.draw();
            }

            // Clear auto-refresh on page unload
            $(window).on('beforeunload', function() {
                clearInterval(autoRefreshInterval);
            });
        });
    </script>
@stop
