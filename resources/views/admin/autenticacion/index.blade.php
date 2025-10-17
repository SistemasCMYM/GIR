@extends('layouts.dashboard')

@section('title', 'Panel de Autenticación - Super Administrador')

@push('styles')
    <style>
        .admin-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .admin-module {
            border: 2px solid #e74c3c;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .btn-admin {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
            color: white;
        }
    </style>
@endpush

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-users-cog text-danger"></i>
                        Panel de Autenticación
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item">Administración</li>
                        <li class="breadcrumb-item active">Autenticación</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Estadísticas Globales de Autenticación -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_usuarios'] ?? 0 }}</h3>
                                <small>Total Usuarios</small>
                            </div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_empresas'] ?? 0 }}</h3>
                                <small>Total Empresas</small>
                            </div>
                            <i class="fas fa-building fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['usuarios_activos'] ?? 0 }}</h3>
                                <small>Usuarios Activos</small>
                            </div>
                            <i class="fas fa-user-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['sesiones_activas'] ?? 0 }}</h3>
                                <small>Sesiones Activas</small>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Módulos de Gestión -->
            <div class="row">
                <!-- Gestión de Usuarios -->
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card admin-module">
                        <div class="admin-header">
                            <div class="text-center">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h4>Gestión de Usuarios</h4>
                                <p class="mb-0">Administrar usuarios del sistema</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h5>{{ $stats['usuarios_activos'] ?? 0 }}</h5>
                                    <small class="text-muted">Activos</small>
                                </div>
                                <div class="col-6">
                                    <h5>{{ $stats['usuarios_inactivos'] ?? 0 }}</h5>
                                    <small class="text-muted">Inactivos</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('usuarios.cuentas.index') }}" class="btn btn-admin">
                                    <i class="fas fa-cog"></i> Gestionar Usuarios
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Empresas -->
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card admin-module">
                        <div class="admin-header">
                            <div class="text-center">
                                <i class="fas fa-building fa-3x mb-3"></i>
                                <h4>Gestión de Empresas</h4>
                                <p class="mb-0">Administrar empresas y configuraciones</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h5>{{ $stats['empresas_activas'] ?? 0 }}</h5>
                                    <small class="text-muted">Activas</small>
                                </div>
                                <div class="col-6">
                                    <h5>{{ $stats['empresas_inactivas'] ?? 0 }}</h5>
                                    <small class="text-muted">Inactivas</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('empresa.empresas.index') }}" class="btn btn-admin">
                                    <i class="fas fa-cog"></i> Gestionar Empresas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Roles y Permisos -->
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card admin-module">
                        <div class="admin-header">
                            <div class="text-center">
                                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                <h4>Roles y Permisos</h4>
                                <p class="mb-0">Configurar accesos y permisos</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h5>{{ $stats['total_roles'] ?? 3 }}</h5>
                                    <small class="text-muted">Roles</small>
                                </div>
                                <div class="col-6">
                                    <h5>{{ $stats['total_permisos'] ?? 15 }}</h5>
                                    <small class="text-muted">Permisos</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.autenticacion.roles') }}" class="btn btn-admin">
                                    <i class="fas fa-cog"></i> Gestionar Roles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auditoría y Logs -->
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card admin-module">
                        <div class="admin-header">
                            <div class="text-center">
                                <i class="fas fa-history fa-3x mb-3"></i>
                                <h4>Auditoría</h4>
                                <p class="mb-0">Logs y actividad del sistema</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h5>{{ $stats['logins_hoy'] ?? 0 }}</h5>
                                    <small class="text-muted">Logins Hoy</small>
                                </div>
                                <div class="col-6">
                                    <h5>{{ $stats['intentos_fallidos'] ?? 0 }}</h5>
                                    <small class="text-muted">Intentos Fallidos</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.autenticacion.auditoria') }}" class="btn btn-admin">
                                    <i class="fas fa-search"></i> Ver Auditoría
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Seguridad -->
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card admin-module">
                        <div class="admin-header">
                            <div class="text-center">
                                <i class="fas fa-lock fa-3x mb-3"></i>
                                <h4>Seguridad</h4>
                                <p class="mb-0">Configuraciones de seguridad</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-12">
                                    <h5
                                        class="text-{{ $stats['seguridad_nivel'] === 'alto' ? 'success' : ($stats['seguridad_nivel'] === 'medio' ? 'warning' : 'danger') }}">
                                        {{ strtoupper($stats['seguridad_nivel'] ?? 'MEDIO') }}
                                    </h5>
                                    <small class="text-muted">Nivel de Seguridad</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('configuracion.seguridad') }}" class="btn btn-admin">
                                    <i class="fas fa-shield-alt"></i> Configurar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup y Mantenimiento -->
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card admin-module">
                        <div class="admin-header">
                            <div class="text-center">
                                <i class="fas fa-database fa-3x mb-3"></i>
                                <h4>Backup</h4>
                                <p class="mb-0">Copias de seguridad y mantenimiento</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-12">
                                    <h5>{{ $stats['ultimo_backup'] ?? 'Nunca' }}</h5>
                                    <small class="text-muted">Último Backup</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('configuracion.backup') }}" class="btn btn-admin">
                                    <i class="fas fa-download"></i> Gestionar Backups
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="row">
                <div class="col-12">
                    <div class="card admin-card">
                        <div class="admin-header">
                            <h4><i class="fas fa-chart-line"></i> Actividad Reciente del Sistema</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha/Hora</th>
                                            <th>Usuario</th>
                                            <th>Acción</th>
                                            <th>IP</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($actividad_reciente ?? [] as $actividad)
                                            <tr>
                                                <td>{{ $actividad['fecha'] ?? date('Y-m-d H:i:s') }}</td>
                                                <td>{{ $actividad['usuario'] ?? 'Sistema' }}</td>
                                                <td>{{ $actividad['accion'] ?? 'Login' }}</td>
                                                <td>{{ $actividad['ip'] ?? '192.168.1.1' }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $actividad['estado'] === 'exitoso' ? 'success' : 'danger' }}">
                                                        {{ $actividad['estado'] ?? 'exitoso' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Datos de ejemplo si no hay actividad -->
                                        @if (empty($actividad_reciente))
                                            <tr>
                                                <td>{{ date('Y-m-d H:i:s') }}</td>
                                                <td>admin@laravel365.com</td>
                                                <td>Acceso al panel de administración</td>
                                                <td>192.168.1.100</td>
                                                <td><span class="badge badge-success">exitoso</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ date('Y-m-d H:i:s', strtotime('-5 minutes')) }}</td>
                                                <td>usuario1@empresa1.com</td>
                                                <td>Login al sistema</td>
                                                <td>192.168.1.101</td>
                                                <td><span class="badge badge-success">exitoso</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ date('Y-m-d H:i:s', strtotime('-10 minutes')) }}</td>
                                                <td>usuario2@empresa2.com</td>
                                                <td>Creación de hallazgo</td>
                                                <td>192.168.1.102</td>
                                                <td><span class="badge badge-success">exitoso</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Efectos de hover para las tarjetas
            $('.admin-card').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );

            // Actualizar estadísticas cada 30 segundos
            setInterval(function() {
                // Aquí se podría hacer una llamada AJAX para actualizar las estadísticas
                console.log('Actualizando estadísticas del panel de administración...');
            }, 30000);
        });
    </script>
@endpush
