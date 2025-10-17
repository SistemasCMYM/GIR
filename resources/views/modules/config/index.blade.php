@extends('layouts.dashboard')

@section('title', 'Configuración del Sistema')

@push('styles')
    <style>
        .config-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .config-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .config-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .config-option {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .config-option:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }

        .config-option.active {
            background: #d1ecf1;
            border-color: #bee5eb;
        }

        .section-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-bottom: 1rem;
        }

        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            position: absolute;
            top: -8px;
            right: -8px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background: #28a745;
        }

        .status-inactive {
            background: #dc3545;
        }

        .status-warning {
            background: #ffc107;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-cog"></i> Configuración del Sistema
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="config-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-cogs me-3"></i>
                        Configuración del Sistema
                    </h2>
                    <p class="mb-0 opacity-75">
                        Administre la configuración general del sistema, usuarios, seguridad y más
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex flex-column gap-2">
                        <small class="opacity-75">
                            <i class="fas fa-user me-2"></i>
                            Usuario: {{ $usuario->nombre ?? 'N/A' }}
                        </small>
                        <small class="opacity-75">
                            <i class="fas fa-clock me-2"></i>
                            {{ now()->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Mensaje de confirmación de que la vista está funcionando -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Sistema de Configuración Funcionando:</strong> El módulo de configuración está operativo.
            La vista <code>modules.config.index</code> está cargando correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Opciones de Configuración -->
        <div class="row">
            <!-- Configuración General -->
            <div class="col-md-4 mb-4">
                <div class="config-card text-center">
                    <div class="section-icon mx-auto">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h5 class="mb-3">Configuración General</h5>
                    <p class="text-muted mb-4">
                        Configuración básica del sistema, nombre, logo, tema y preferencias generales
                    </p>
                    <a href="{{ route('configuracion.general') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Configurar
                    </a>
                </div>
            </div>

            <!-- Gestión de Usuarios -->
            <div class="col-md-4 mb-4">
                <div class="config-card text-center">
                    <div class="section-icon mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="mb-3">Gestión de Usuarios</h5>
                    <p class="text-muted mb-4">
                        Administrar usuarios, roles, permisos y configuración de cuentas
                    </p>
                    <a href="{{ route('configuracion.usuarios') }}" class="btn btn-primary">
                        <i class="fas fa-user-cog me-2"></i>Administrar
                    </a>
                </div>
            </div>

            <!-- Seguridad -->
            <div class="col-md-4 mb-4">
                <div class="config-card text-center">
                    <div class="section-icon mx-auto">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="mb-3">Seguridad</h5>
                    <p class="text-muted mb-4">
                        Configuración de seguridad, autenticación de dos factores y políticas
                    </p>
                    <a href="{{ route('configuracion.seguridad') }}" class="btn btn-primary">
                        <i class="fas fa-lock me-2"></i>Configurar
                    </a>
                </div>
            </div>

            <!-- Base de Datos -->
            <div class="col-md-4 mb-4">
                <div class="config-card text-center">
                    <div class="section-icon mx-auto">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5 class="mb-3">Base de Datos</h5>
                    <p class="text-muted mb-4">
                        Configuración de conexiones de base de datos y respaldos
                    </p>
                    <a href="{{ route('configuracion.database') }}" class="btn btn-primary">
                        <i class="fas fa-server me-2"></i>Administrar
                    </a>
                </div>
            </div>

            <!-- Notificaciones -->
            <div class="col-md-4 mb-4">
                <div class="config-card text-center">
                    <div class="section-icon mx-auto">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h5 class="mb-3">Notificaciones</h5>
                    <p class="text-muted mb-4">
                        Configurar notificaciones por email, SMS y notificaciones push
                    </p>
                    <a href="{{ route('configuracion.notificaciones') }}" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Configurar
                    </a>
                </div>
            </div>

            <!-- Integraciones -->
            <div class="col-md-4 mb-4">
                <div class="config-card text-center">
                    <div class="section-icon mx-auto">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h5 class="mb-3">Integraciones</h5>
                    <p class="text-muted mb-4">
                        APIs externas, webhooks y servicios de terceros
                    </p>
                    <a href="{{ route('configuracion.integraciones') }}" class="btn btn-primary">
                        <i class="fas fa-link me-2"></i>Administrar
                    </a>
                </div>
            </div>
        </div>

        <!-- Estado del Sistema -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Estado del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="status-indicator status-active"></span>
                                    <strong>Base de Datos</strong>
                                </div>
                                <small class="text-muted">Conexión activa</small>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="status-indicator status-active"></span>
                                    <strong>Cache</strong>
                                </div>
                                <small class="text-muted">Funcionando correctamente</small>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="status-indicator status-active"></span>
                                    <strong>Almacenamiento</strong>
                                </div>
                                <small class="text-muted">Espacio disponible</small>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="status-indicator status-active"></span>
                                    <strong>Sistema</strong>
                                </div>
                                <small class="text-muted">Operativo</small>
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
        // Auto-refresh system status every 30 seconds
        setInterval(function() {
            // Here you could add AJAX call to check system status
            console.log('Checking system status...');
        }, 30000);
    </script>
@endpush
