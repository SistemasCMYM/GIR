@extends('layouts.dashboard')

@section('title', 'Detalles del Perfil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auth.index') }}">Autenticación</a></li>
    <li class="breadcrumb-item"><a href="{{ route('auth.profiles.index') }}">Perfiles</a></li>
    <li class="breadcrumb-item active">{{ $perfil->nombre }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Information Card -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset('img/profile-icon.png') }}"
                                alt="Profile icon">
                        </div>

                        <h3 class="profile-username text-center">{{ $perfil->nombre }}</h3>

                        <p class="text-muted text-center">
                            {{ $perfil->empresa ? $perfil->empresa->nombre : 'Perfil General' }}</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Descripción</b>
                                <span class="float-right text-muted">
                                    {{ $perfil->descripcion ?: 'Sin descripción' }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Estado</b>
                                <span class="float-right">
                                    <span class="badge badge-{{ $perfil->estado == 'activo' ? 'success' : 'danger' }}">
                                        {{ ucfirst($perfil->estado) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Permisos</b>
                                <span class="float-right">
                                    <span class="badge badge-info">{{ count($perfil->permisos ?? []) }} permisos</span>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Usuarios Asignados</b>
                                <span class="float-right">
                                    <span class="badge badge-primary">{{ $usuariosAsignados ?? 0 }} usuarios</span>
                                </span>
                            </li>
                        </ul>

                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('auth.profiles.edit', $perfil->_id) }}"
                                    class="btn btn-primary btn-block">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="button"
                                    class="btn btn-{{ $perfil->estado == 'activo' ? 'warning' : 'success' }} btn-block"
                                    onclick="togglePerfilStatus('{{ $perfil->_id }}', '{{ $perfil->estado }}')">
                                    <i class="fas fa-{{ $perfil->estado == 'activo' ? 'ban' : 'check' }}"></i>
                                    {{ $perfil->estado == 'activo' ? 'Desactivar' : 'Activar' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Acciones Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical btn-block">
                            <button type="button" class="btn btn-info" onclick="duplicatePerfil('{{ $perfil->_id }}')">
                                <i class="fas fa-copy"></i> Duplicar Perfil
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportPerfil('{{ $perfil->_id }}')">
                                <i class="fas fa-download"></i> Exportar Configuración
                            </button>
                            <button type="button" class="btn btn-warning" onclick="viewUsuarios('{{ $perfil->_id }}')">
                                <i class="fas fa-users"></i> Ver Usuarios Asignados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Profile Information Tabs -->
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#information" data-toggle="tab">Información General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#permissions" data-toggle="tab">Permisos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#users" data-toggle="tab">Usuarios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#history" data-toggle="tab">Historial</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Information Tab -->
                            <div class="active tab-pane" id="information">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">Datos del Perfil</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-user-tag mr-1"></i> Nombre</strong>
                                                        <p class="text-muted">{{ $perfil->nombre }}</p>
                                                        <hr>

                                                        <strong><i class="fas fa-building mr-1"></i> Empresa</strong>
                                                        <p class="text-muted">
                                                            @if ($perfil->empresa)
                                                                {{ $perfil->empresa->nombre }}
                                                                <br><small>Perfil específico para esta empresa</small>
                                                            @else
                                                                Perfil General
                                                                <br><small>Aplicable a todas las empresas</small>
                                                            @endif
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-info-circle mr-1"></i> Descripción</strong>
                                                        <p class="text-muted">
                                                            {{ $perfil->descripcion ?: 'Sin descripción proporcionada' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-calendar mr-1"></i> Fecha de
                                                            Creación</strong>
                                                        <p class="text-muted">
                                                            {{ $perfil->created_at ? $perfil->created_at->format('d/m/Y H:i') : 'Fecha desconocida' }}
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-clock mr-1"></i> Última
                                                            Actualización</strong>
                                                        <p class="text-muted">
                                                            {{ $perfil->updated_at ? $perfil->updated_at->format('d/m/Y H:i') : 'Nunca actualizado' }}
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-chart-bar mr-1"></i> Estadísticas</strong>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="info-box bg-info">
                                                                    <span class="info-box-icon"><i
                                                                            class="fas fa-key"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Permisos</span>
                                                                        <span
                                                                            class="info-box-number">{{ count($perfil->permisos ?? []) }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="info-box bg-success">
                                                                    <span class="info-box-icon"><i
                                                                            class="fas fa-users"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Usuarios</span>
                                                                        <span
                                                                            class="info-box-number">{{ $usuariosAsignados ?? 0 }}</span>
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
                            </div>

                            <!-- Permissions Tab -->
                            <div class="tab-pane" id="permissions">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Permisos Asignados</h3>
                                    </div>
                                    <div class="card-body">
                                        @if ($perfil->permisos && count($perfil->permisos) > 0)
                                            @php
                                                $permisosAgrupados = [];
                                                foreach ($perfil->permisos as $permiso) {
                                                    $categoria = explode('.', $permiso)[0];
                                                    $permisosAgrupados[$categoria][] = $permiso;
                                                }
                                            @endphp

                                            <div class="row">
                                                @foreach ($permisosAgrupados as $categoria => $permisos)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="card card-outline card-success">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ ucfirst($categoria) }}</h5>
                                                            </div>
                                                            <div class="card-body p-2">
                                                                @foreach ($permisos as $permiso)
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-1">
                                                                        <span
                                                                            class="text-sm">{{ str_replace(['_', '.'], [' ', ' › '], $permiso) }}</span>
                                                                        <i class="fas fa-check text-success"></i>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <h5><i class="icon fas fa-info"></i> Sin Permisos Asignados</h5>
                                                Este perfil no tiene permisos específicos configurados.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Users Tab -->
                            <div class="tab-pane" id="users">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Usuarios con este Perfil</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="usuarios-list">
                                            <!-- Content will be loaded via AJAX -->
                                            <div class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando usuarios...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane" id="history">
                                <div class="timeline timeline-inverse">
                                    <div class="time-label">
                                        <span class="bg-success">
                                            {{ now()->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <i class="fas fa-eye bg-primary"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> ahora</span>
                                            <h3 class="timeline-header">
                                                Perfil visualizado
                                            </h3>
                                            <div class="timeline-body">
                                                Detalles del perfil consultados desde el módulo de autenticación.
                                            </div>
                                        </div>
                                    </div>
                                    @if ($perfil->updated_at)
                                        <div>
                                            <i class="fas fa-edit bg-yellow"></i>
                                            <div class="timeline-item">
                                                <span class="time">
                                                    <i class="far fa-clock"></i>
                                                    {{ $perfil->updated_at->diffForHumans() }}
                                                </span>
                                                <h3 class="timeline-header">
                                                    Perfil actualizado
                                                </h3>
                                                <div class="timeline-body">
                                                    La última modificación del perfil fue realizada.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div>
                                        <i class="fas fa-user-plus bg-blue"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="far fa-clock"></i>
                                                {{ $perfil->created_at ? $perfil->created_at->diffForHumans() : 'Fecha desconocida' }}
                                            </span>
                                            <h3 class="timeline-header">
                                                Perfil creado
                                            </h3>
                                            <div class="timeline-body">
                                                El perfil fue registrado en el sistema.
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <i class="far fa-clock bg-gray"></i>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load users when the users tab is clicked
            $('a[href="#users"]').on('click', function() {
                loadUsuarios();
            });

            // Fix for tab navigation
            $('.nav-pills a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        function loadUsuarios() {
            const usuariosList = $('#usuarios-list');

            $.get(`/api/perfiles/{{ $perfil->_id }}/usuarios`)
                .done(function(usuarios) {
                    if (usuarios.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-striped">';
                        html +=
                            '<thead><tr><th>Nombre</th><th>Email</th><th>Empresa</th><th>Estado</th><th>Último Acceso</th></tr></thead><tbody>';

                        usuarios.forEach(function(usuario) {
                            const estado = usuario.estado === 'activo' ?
                                '<span class="badge badge-success">Activo</span>' :
                                '<span class="badge badge-danger">Inactivo</span>';

                            const ultimoAcceso = usuario.ultimo_acceso ?
                                new Date(usuario.ultimo_acceso).toLocaleDateString('es-ES') :
                                'Nunca';

                            const empresa = usuario.empresa ? usuario.empresa.nombre : 'Sin empresa';

                            html += `
                        <tr>
                            <td><strong>${usuario.nombre} ${usuario.apellido}</strong></td>
                            <td>${usuario.email}</td>
                            <td>${empresa}</td>
                            <td>${estado}</td>
                            <td>${ultimoAcceso}</td>
                        </tr>
                    `;
                        });

                        html += '</tbody></table></div>';
                        usuariosList.html(html);
                    } else {
                        usuariosList.html(`
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Sin Usuarios Asignados</h5>
                        No hay usuarios con este perfil asignado.
                    </div>
                `);
                    }
                })
                .fail(function() {
                    usuariosList.html(`
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Error</h5>
                    No se pudieron cargar los usuarios asignados a este perfil.
                </div>
            `);
                });
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
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
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

        function duplicatePerfil(perfilId) {
            Swal.fire({
                title: 'Duplicar Perfil',
                text: '¿Deseas crear una copia de este perfil?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, duplicar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to create form with perfil data
                    window.location.href = `/auth/profiles/create?duplicate=${perfilId}`;
                }
            });
        }

        function exportPerfil(perfilId) {
            window.location.href = `/auth/profiles/${perfilId}/export`;
        }

        function viewUsuarios(perfilId) {
            // Switch to users tab
            $('a[href="#users"]').tab('show');
            loadUsuarios();
        }
    </script>
@endpush
