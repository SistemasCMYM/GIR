@extends('layouts.dashboard')

@section('title', 'Panel Principal')

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
                    <i class="fas fa-tachometer-alt"></i> Panel
                </li>
            </ol>
        </nav>

        <!-- Mensajes de error si los hay -->
        @if (isset($error))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Encabezado del Panel -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-2">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Panel Principal - GIR365
                                </h4>
                                <p class="text-muted mb-0">
                                    Bienvenido al sistema de gestión integral
                                    @if (isset($empresaData->nombre))
                                        - {{ $empresaData->nombre }}
                                    @endif
                                </p>
                                @if (isset($userData->nombre))
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $userData->nombre }} {{ $userData->apellidos ?? '' }}
                                        @if (isset($isSuperAdmin) && $isSuperAdmin)
                                            <span class="badge bg-danger ms-2">Super Admin</span>
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $estadisticas['total_empleados'] ?? 0 }}</h3>
                                <p class="mb-0">Total Empleados</p>
                            </div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $estadisticas['empleados_activos'] ?? 0 }}</h3>
                                <p class="mb-0">Empleados Activos</p>
                            </div>
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $estadisticas['total_areas'] ?? 0 }}</h3>
                                <p class="mb-0">Áreas</p>
                            </div>
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $estadisticas['total_centros'] ?? 0 }}</h3>
                                <p class="mb-0">Centros</p>
                            </div>
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos Disponibles -->
        @if (isset($modulos_disponibles) && count($modulos_disponibles) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-th-large me-2"></i>
                                Módulos Disponibles
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($modulos_disponibles as $modulo)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="{{ $modulo['icono'] }} fa-3x text-primary mb-3"></i>
                                                <h5 class="card-title">{{ $modulo['nombre'] }}</h5>
                                                <p class="card-text text-muted">{{ $modulo['descripcion'] }}</p>
                                                <a href="{{ $modulo['ruta'] }}" class="btn btn-primary">
                                                    <i class="fas fa-arrow-right me-1"></i>
                                                    Acceder
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actividades Recientes -->
        @if (isset($actividades_recientes) && count($actividades_recientes) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Actividades Recientes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach ($actividades_recientes as $actividad)
                                    <div class="timeline-item mb-3">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="{{ $actividad['icono'] }} fa-lg"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1">{{ $actividad['descripcion'] }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($actividad['fecha'])->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Información del Sistema -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <p class="mb-0 text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Sistema GIR365 - Gestión Integral de Riesgos
                            <span class="mx-2">|</span>
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .timeline-item {
                border-left: 2px solid #dee2e6;
                padding-left: 1rem;
            }

            .card:hover {
                transform: translateY(-2px);
                transition: transform 0.3s ease;
            }

            .bg-primary {
                background: linear-gradient(45deg, #007bff, #0056b3) !important;
            }

            .bg-success {
                background: linear-gradient(45deg, #28a745, #1e7e34) !important;
            }

            .bg-info {
                background: linear-gradient(45deg, #17a2b8, #138496) !important;
            }

            .bg-warning {
                background: linear-gradient(45deg, #ffc107, #e0a800) !important;
            }
        </style>
    @endpush
@endsection
