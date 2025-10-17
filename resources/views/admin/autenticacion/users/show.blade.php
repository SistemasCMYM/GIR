@extends('layouts.dashboard')

@section('title', 'Detalles del Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auth.index') }}">Autenticación</a></li>
    <li class="breadcrumb-item"><a href="{{ route('auth.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">{{ $user->nombre }} {{ $user->apellido }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Information Card -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset('img/default-avatar.png') }}"
                                alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center">{{ $user->nombre }} {{ $user->apellido }}</h3>

                        <p class="text-muted text-center">
                            {{ $user->perfil ? $user->perfil->nombre : 'Sin perfil asignado' }}</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Email</b> <a class="float-right text-muted">{{ $user->email }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Teléfono</b> <a
                                    class="float-right text-muted">{{ $user->telefono ?: 'No especificado' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Estado</b>
                                <span class="float-right">
                                    <span class="badge badge-{{ $user->estado == 'activo' ? 'success' : 'danger' }}">
                                        {{ ucfirst($user->estado) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Último acceso</b>
                                <span class="float-right text-muted">
                                    {{ $user->ultimo_acceso ? $user->ultimo_acceso->format('d/m/Y H:i') : 'Nunca' }}
                                </span>
                            </li>
                        </ul>

                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('auth.users.edit', $user->_id) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="button"
                                    class="btn btn-{{ $user->estado == 'activo' ? 'warning' : 'success' }} btn-block"
                                    onclick="toggleUserStatus('{{ $user->_id }}', '{{ $user->estado }}')">
                                    <i class="fas fa-{{ $user->estado == 'activo' ? 'ban' : 'check' }}"></i>
                                    {{ $user->estado == 'activo' ? 'Desactivar' : 'Activar' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actividad Reciente</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-inverse">
                            <div class="time-label">
                                <span class="bg-success">
                                    {{ now()->format('d M Y') }}
                                </span>
                            </div>
                            <div>
                                <i class="fas fa-user bg-primary"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="far fa-clock"></i> hace 2 min</span>
                                    <h3 class="timeline-header">
                                        Usuario visualizado
                                    </h3>
                                    <div class="timeline-body">
                                        Perfil de usuario consultado desde el módulo de autenticación.
                                    </div>
                                </div>
                            </div>
                            @if ($user->ultimo_acceso)
                                <div>
                                    <i class="fas fa-sign-in-alt bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="far fa-clock"></i> {{ $user->ultimo_acceso->diffForHumans() }}
                                        </span>
                                        <h3 class="timeline-header">
                                            Último acceso al sistema
                                        </h3>
                                    </div>
                                </div>
                            @endif
                            <div>
                                <i class="fas fa-user-plus bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="far fa-clock"></i>
                                        {{ $user->created_at ? $user->created_at->diffForHumans() : 'Fecha desconocida' }}
                                    </span>
                                    <h3 class="timeline-header">
                                        Usuario creado
                                    </h3>
                                    <div class="timeline-body">
                                        El usuario fue registrado en el sistema.
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

            <div class="col-md-8">
                <!-- User Information -->
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Información
                                    General</a></li>
                            <li class="nav-item"><a class="nav-link" href="#timeline" href="#empresa"
                                    data-toggle="tab">Empresa</a></li>
                            <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Permisos</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">Datos Personales</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-user mr-1"></i> Nombre Completo</strong>
                                                        <p class="text-muted">{{ $user->nombre }} {{ $user->apellido }}
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                                                        <p class="text-muted">
                                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-phone mr-1"></i> Teléfono</strong>
                                                        <p class="text-muted">{{ $user->telefono ?: 'No especificado' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-user-tag mr-1"></i> Perfil</strong>
                                                        <p class="text-muted">
                                                            @if ($user->perfil)
                                                                <span
                                                                    class="badge badge-info">{{ $user->perfil->nombre }}</span>
                                                                <br><small>{{ $user->perfil->descripcion }}</small>
                                                            @else
                                                                Sin perfil asignado
                                                            @endif
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-calendar mr-1"></i> Fecha de
                                                            Registro</strong>
                                                        <p class="text-muted">
                                                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Fecha desconocida' }}
                                                        </p>
                                                        <hr>

                                                        <strong><i class="fas fa-clock mr-1"></i> Última
                                                            Actualización</strong>
                                                        <p class="text-muted">
                                                            {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'Nunca actualizado' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="timeline">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Información de la Empresa</h3>
                                    </div>
                                    <div class="card-body">
                                        @if ($user->empresa)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong><i class="fas fa-building mr-1"></i> Empresa</strong>
                                                    <p class="text-muted">{{ $user->empresa->nombre }}</p>
                                                    <hr>

                                                    <strong><i class="fas fa-id-card mr-1"></i> NIT</strong>
                                                    <p class="text-muted">{{ $user->empresa->nit ?: 'No especificado' }}
                                                    </p>
                                                    <hr>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección</strong>
                                                    <p class="text-muted">
                                                        {{ $user->empresa->direccion ?: 'No especificada' }}</p>
                                                    <hr>

                                                    <strong><i class="fas fa-phone mr-1"></i> Teléfono Empresa</strong>
                                                    <p class="text-muted">
                                                        {{ $user->empresa->telefono ?: 'No especificado' }}</p>
                                                </div>
                                            </div>

                                            @if ($user->empleado)
                                                <div class="alert alert-info">
                                                    <h5><i class="icon fas fa-info"></i> Empleado Vinculado</h5>
                                                    <strong>Nombre:</strong> {{ $user->empleado->nombre }}
                                                    {{ $user->empleado->apellido }}<br>
                                                    <strong>Cargo:</strong>
                                                    {{ $user->empleado->cargo ?: 'No especificado' }}<br>
                                                    <strong>Cédula:</strong>
                                                    {{ $user->empleado->cedula ?: 'No especificada' }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-warning">
                                                <h5><i class="icon fas fa-exclamation-triangle"></i> Sin Empresa</h5>
                                                Este usuario no tiene una empresa asignada.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="settings">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Permisos del Usuario</h3>
                                    </div>
                                    <div class="card-body">
                                        @if ($user->perfil && $user->perfil->permisos)
                                            <div class="row">
                                                @foreach ($user->perfil->permisos as $permiso)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">{{ $permiso }}</span>
                                                                <span class="info-box-number text-success">Permitido</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <h5><i class="icon fas fa-info"></i> Sin Permisos Específicos</h5>
                                                Este usuario no tiene permisos específicos asignados o no tiene un perfil
                                                configurado.
                                            </div>
                                        @endif
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
        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'activo' ? 'inactivo' : 'activo';
            const action = newStatus === 'activo' ? 'activar' : 'desactivar';

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas ${action} este usuario?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/auth/users/${userId}/toggle-status`,
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
                                    'Error al cambiar el estado del usuario');
                            }
                        },
                        error: function() {
                            toastr.error('Error al cambiar el estado del usuario');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            // Fix for tab navigation
            $('.nav-pills a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endpush
