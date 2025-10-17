@extends('layouts.dashboard')

@section('title', 'Dashboard - GIR-365')
@section('page-title', 'Dashboard Principal')

@php
    // Verificar si el usuario es SuperAdmin
    $userData = session('user_data') ?: session('usuario_data');
    $isSuperAdmin = false;
    if ($userData) {
        $rol = strtolower($userData['rol'] ?? ($userData['role'] ?? ($userData['tipo'] ?? '')));
        $isSuperAdmin =
            in_array($rol, ['super_admin', 'superadmin', 'super administrator', 'superadministrador', 'root'], true) ||
            (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
    }
@endphp

@push('styles')
    <style>
        /* Silva Dashboard Grid System */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .silva-metric-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .silva-metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        /* Stats Cards 3D Enhanced */
        .stat-card-3d {
            background: linear-gradient(180deg, var(--card-bg), #ebebe3);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .stat-card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--info-color), var(--success-color));
            border-radius: 20px 20px 0 0;
        }

        .stat-card-3d::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transition: all 0.4s ease;
        }

        .stat-card-3d:hover {
            transform: translateY(-12px) rotateX(5deg) rotateY(2deg);
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.4),
                0 0 50px rgba(37, 99, 235, 0.2);
        }

        .stat-card-3d:hover::after {
            top: -30%;
            right: -30%;
            width: 300px;
            height: 300px;
        }

        .stat-number-3d {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .stat-label-3d {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }

        .stat-icon-3d {
            position: absolute;
            top: 2rem;
            right: 2rem;
            font-size: 3rem;
            color: var(--primary-color);
            opacity: 0.2;
            transition: all 0.4s ease;
            transform: rotateY(0deg);
        }

        .stat-card-3d:hover .stat-icon-3d {
            opacity: 0.4;
            transform: rotateY(15deg) scale(1.1);
        }

        /* Chart Cards 3D */
        .chart-card-3d {
            background: linear-gradient(135deg, var(--card-bg), #334155);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 450px;
            /* Altura fija para controlar el tamaño */
        }

        .chart-card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--warning-color), var(--danger-color), var(--success-color));
        }

        .chart-card-3d:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* Contenedor de gráficos con tamaño controlado */
        .chart-container {
            position: relative;
            height: 300px !important;
            width: 100% !important;
            max-height: 300px !important;
            overflow: hidden;
        }

        .chart-container canvas {
            max-height: 300px !important;
            width: 100% !important;
            height: 300px !important;
        }

        /* Module Cards 3D */
        .module-card-3d {
            background: linear-gradient(135deg, var(--card-bg), #475569);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
        }

        .module-card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(6, 182, 212, 0.05));
            opacity: 0;
            transition: all 0.4s ease;
        }

        .module-card-3d:hover {
            transform: translateY(-8px) rotateX(2deg);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            color: inherit;
            text-decoration: none;
        }

        .module-card-3d:hover::before {
            opacity: 1;
        }

        .module-icon-3d {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            transition: all 0.4s ease;
        }

        .module-card-3d:hover .module-icon-3d {
            transform: scale(1.1) rotateY(10deg);
            color: var(--info-color);
        }

        .module-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .module-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Activity Feed */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(37, 99, 235, 0.05);
            border-radius: 8px;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, #D1A854, #EDC979, #D1A854);
            border-radius: 20px;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        /* Responsive Grid */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 1.5rem;
            }

            .welcome-subtitle {
                font-size: 0.9rem;
            }

            .stat-number-3d {
                font-size: 2.5rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Clickable Cards and Components */
        .clickable-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
        }

        .clickable-component:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .clickable-component:hover h6 {
            color: #0d6efd !important;
        }

        .clickable-component:hover .rounded-circle {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Módulos Principales - Estilo imagen */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card .card-body {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .card:hover .card-body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .card .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .card .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        /* Silva Dashboard specific styles */
        .silva-module-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: var(--card-bg);
            height: 100%;
        }

        .silva-module-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .silva-component-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: var(--card-bg);
        }

        .silva-component-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .silva-admin-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: var(--card-bg);
        }

        .silva-admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        /* Avatar components Silva Dashboard style */
        .avatar-sm {
            width: 2.25rem;
            height: 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-md {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-lg {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        /* Soft background utilities Silva Dashboard style */
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        /* 4K Resolution Support */
        @media (min-width: 3840px) {
            .welcome-title {
                font-size: 3.5rem;
            }

            .stat-number-3d {
                font-size: 4rem;
            }

            .module-icon-3d {
                font-size: 4rem;
            }

            .chart-card-3d,
            .stat-card-3d,
            .module-card-3d {
                padding: 3rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid gir-silva-dashboard">
        {{-- 
      INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA 
      @last_verified: 2025-08-14 
      @security_level: critical
    --}}

        <!-- Welcome Section -->
        <div class="welcome-section animate-slide-up alaign-center">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    @php
                        $userData = session('user_data') ? (object) session('user_data') : null;
                        $empresaData = session('empresa_data') ? (object) session('empresa_data') : null;
                        $userName = $userData
                            ? trim(
                                ($userData->nombre ?? ($userData->nick ?? 'Usuario')) .
                                    ' ' .
                                    ($userData->apellido ?? ''),
                            )
                            : 'Usuario';
                        $empresaName = $empresaData->razon_social ?? ($empresaData->nombre_comercial ?? 'su empresa');
                    @endphp
                    <h1 class="welcome-title">Bienvenido/a</h1>
                    <h1 class="welcome-title" id="greeting">Bienvenido/a, {{ $userName }}</h1>
                    <p class="welcome-subtitle">
                        ¡Damos la bienvenida al Sistema Integral de Gestión de Riesgos GIR-365 para la empresa:
                        {{ $empresaName }} !
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        <span id="current-date">Cargando...</span>
                    </p>
                    <p class="tw-mb-0">
                        <i class="fas fa-clock me-2"></i>
                        <span id="current-time">00:00:00</span>
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <i class="fas fa-shield-alt" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Silva Dashboard -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card silva-metric-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="fas fa-exclamation-triangle fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $totalHallazgos ?? 0 }}</h4>
                                <p class="text-muted mb-0">Total Hallazgos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card silva-metric-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title rounded-circle bg-info">
                                        <i class="fas fa-brain fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $evaluacionesPsicosociales ?? 0 }}</h4>
                                <p class="text-muted mb-0">Evaluaciones Psicosociales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card silva-metric-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title rounded-circle bg-success">
                                        <i class="fas fa-users fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $totalEmpleados ?? 0 }}</h4>
                                <p class="text-muted mb-0">Empleados Registrados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card silva-metric-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="fas fa-tasks fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $planesAccion ?? 0 }}</h4>
                                <p class="text-muted mb-0">Planes de Acción</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules and Activity Row -->
        <div class="row">
            <!-- Main Modules -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-th-large me-2"></i>
                            Módulos Principales
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Gestión de Hallazgos Module -->
                            <div class="col-md-6 mb-4">
                                <div class="card silva-module-card">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-4">
                                            <div class="avatar-title bg-soft-warning text-warning rounded-circle">
                                                <i class="fas fa-exclamation-triangle fs-1"></i>
                                            </div>
                                        </div>
                                        <h5 class="mt-0 mb-3">Gestión de Hallazgos</h5>
                                        <p class="text-muted">
                                            Administre y realice seguimiento a los hallazgos identificados en las
                                            evaluaciones de riesgo. Gestione planes de acción y mejora continua.
                                        </p>
                                        <div class="mb-4">
                                            <span class="badge bg-soft-primary text-primary">
                                                <i class="fas fa-chart-bar me-1"></i>
                                                {{ $totalHallazgos ?? 190 }} registros
                                            </span>
                                        </div>
                                        <a href="{{ route('hallazgos.index') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-right me-1"></i>
                                            Acceder
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Evaluación Psicosocial Module -->
                            <div class="col-md-6 mb-4">
                                <div class="card silva-module-card">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-4">
                                            <div class="avatar-title bg-soft-info text-info rounded-circle">
                                                <i class="fas fa-brain fs-1"></i>
                                            </div>
                                        </div>
                                        <h5 class="mt-0 mb-3">Evaluación Psicosocial</h5>
                                        <p class="text-muted">
                                            Aplicación de la Batería del Riesgo Psicosocial según parámetros del
                                            Ministerio de la Protección Social y la Universidad Javeriana.
                                        </p>
                                        <div class="mb-4">
                                            <span class="badge bg-soft-success text-success me-2">
                                                <i class="fas fa-check me-1"></i>
                                                {{ $evaluacionesCompletadas ?? 0 }} completadas
                                            </span>
                                            <span class="badge bg-soft-warning text-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $evaluacionesPendientes ?? 0 }} pendientes
                                            </span>
                                        </div>
                                        <a href="{{ route('psicosocial.index') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-right me-1"></i>
                                            Acceder
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batería de Riesgo Psicosocial - Componentes -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title mb-1">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Batería de Riesgo Psicosocial - Componentes del Ministerio de Protección Social
                        </h4>
                        <p class="text-muted mb-0">Herramientas desarrolladas por la Universidad Javeriana según
                            lineamientos oficiales</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Ficha de Datos Generales -->
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('psicosocial.index') }}" class="text-decoration-none">
                                    <div class="card silva-component-card h-100">
                                        <div class="card-body text-center">
                                            <div class="avatar-md mx-auto mb-3">
                                                <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                    <i class="fas fa-user-circle fs-4"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Ficha de Datos Generales</h6>
                                            <p class="text-muted mb-0 small">
                                                Información sociodemográfica y ocupacional
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Cuestionario de Estrés -->
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('psicosocial.index') }}" class="text-decoration-none">
                                    <div class="card silva-component-card h-100">
                                        <div class="card-body text-center">
                                            <div class="avatar-md mx-auto mb-3">
                                                <div class="avatar-title bg-soft-info text-info rounded-circle">
                                                    <i class="fas fa-heart-pulse fs-4"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Cuestionario de Estrés</h6>
                                            <p class="text-muted mb-0 small">
                                                Evaluación de síntomas de estrés
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Factores Extralaborales -->
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('psicosocial.index') }}" class="text-decoration-none">
                                    <div class="card silva-component-card h-100">
                                        <div class="card-body text-center">
                                            <div class="avatar-md mx-auto mb-3">
                                                <div class="avatar-title bg-soft-warning text-warning rounded-circle">
                                                    <i class="fas fa-home fs-4"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Factores Extralaborales</h6>
                                            <p class="text-muted mb-0 small">
                                                Entorno familiar, social y económico
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Factores Intralaborales -->
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('psicosocial.index') }}" class="text-decoration-none">
                                    <div class="card silva-component-card h-100">
                                        <div class="card-body text-center">
                                            <div class="avatar-md mx-auto mb-3">
                                                <div class="avatar-title bg-soft-danger text-danger rounded-circle">
                                                    <i class="fas fa-building fs-4"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Factores Intralaborales</h6>
                                            <p class="text-muted mb-0 small">
                                                Forma A (Profesionales) y Forma B (Auxiliares)
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Marco Normativo -->
                <div class="card border-info mt-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-info text-info rounded-circle">
                                        <i class="fas fa-info-circle fs-5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-2">Marco Normativo</h6>
                                <p class="text-muted mb-0">
                                    Basado en la Resolución 2646 de 2008 del Ministerio de la Protección Social, que
                                    establece
                                    disposiciones y define responsabilidades para la identificación, evaluación, prevención,
                                    intervención y monitoreo permanente de la exposición a factores de riesgo psicosocial en
                                    el
                                    trabajo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Modules (Solo para SuperAdmin) -->
                @if ($isSuperAdmin)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-crown me-2"></i>
                                Administración del Sistema
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- User Management -->
                                <div class="col-md-4 mb-3">
                                    <div class="card silva-admin-card">
                                        <div class="card-body text-center">
                                            <div class="avatar-lg mx-auto mb-3">
                                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    <i class="fas fa-users fs-1"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Usuarios</h6>
                                            <p class="text-muted mb-3 small">
                                                Gestión de cuentas y perfiles
                                            </p>
                                            <a href="#" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                Gestionar
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Management -->
                                <div class="col-md-4 mb-3">
                                    <div class="card silva-admin-card">
                                        <div class="card-body text-center">
                                            <div class="avatar-lg mx-auto mb-3">
                                                <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                    <i class="fas fa-building fs-1"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Empresas</h6>
                                            <p class="text-muted mb-3 small">
                                                Configuración empresarial
                                            </p>
                                            <a href="#" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                Gestionar
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- System Config -->
                                <div class="col-md-4 mb-3">
                                    <div class="card silva-admin-card">
                                        <div class="card-body text-center">
                                            <div class="avatar-lg mx-auto mb-3">
                                                <div class="avatar-title bg-soft-warning text-warning rounded-circle">
                                                    <i class="fas fa-cog fs-1"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-2">Configuración</h6>
                                            <p class="text-muted mb-3 small">
                                                Ajustes del sistema
                                            </p>
                                            <a href="{{ route('configuracion.index') }}"
                                                class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                Gestionar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Analytics Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Distribución de Riesgos
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="riskDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Tendencia Mensual
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="monthlyTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Acciones Rápidas
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('hallazgos.create') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Nuevo Hallazgo
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('psicosocial.create') }}" class="btn btn-info btn-lg">
                                        <i class="fas fa-brain me-2"></i>
                                        Nueva Evaluación
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-grid">
                                    <button class="btn btn-success btn-lg" onclick="alert('Función en desarrollo')">
                                        <i class="fas fa-file-download me-2"></i>
                                        Generar Reporte
                                    </button>
                                </div>
                            </div>
                            @if ($isSuperAdmin)
                                <div class="col-md-3 mb-3">
                                    <div class="d-grid">
                                        <a href="{{ route('configuracion.index') }}" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-cog me-2"></i>
                                            Configuración
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        {{-- 
      INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA 
      @last_verified: 2025-08-14 
      @security_level: critical
    --}}

        document.addEventListener('DOMContentLoaded', function() {
            // Risk Distribution Pie Chart - 3D Style
            const riskCanvas = document.getElementById('riskDistributionChart');
            if (riskCanvas) {
                const riskCtx = riskCanvas.getContext('2d');
                new Chart(riskCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Crítico', 'Alto', 'Medio', 'Bajo'],
                        datasets: [{
                            data: [{{ $riesgoCritico ?? 5 }}, {{ $riesgoAlto ?? 15 }},
                                {{ $riesgoMedio ?? 25 }}, {{ $riesgoBajo ?? 35 }}
                            ],
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.8)', // Red - Crítico
                                'rgba(245, 158, 11, 0.8)', // Orange - Alto
                                'rgba(34, 197, 94, 0.8)', // Green - Medio
                                'rgba(59, 130, 246, 0.8)' // Blue - Bajo
                            ],
                            borderColor: [
                                'rgb(239, 68, 68)',
                                'rgb(245, 158, 11)',
                                'rgb(34, 197, 94)',
                                'rgb(59, 130, 246)'
                            ],
                            borderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#212529',
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#f8fafc',
                                bodyColor: '#cbd5e1',
                                borderColor: '#475569',
                                borderWidth: 1
                            }
                        },
                        cutout: '60%',
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            // Monthly Trend Line Chart - 3D Style
            const trendCanvas = document.getElementById('monthlyTrendChart');
            if (trendCanvas) {
                const trendCtx = trendCanvas.getContext('2d');
                const gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct',
                            'Nov',
                            'Dic'
                        ],
                        datasets: [{
                            label: 'Hallazgos',
                            data: [12, 19, 15, 22, 28, 25, 32, 29, 35, 31, 28, 24],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: 'rgb(37, 99, 235)',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#f8fafc',
                                bodyColor: '#cbd5e1',
                                borderColor: '#475569',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(71, 85, 105, 0.3)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#212529',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(71, 85, 105, 0.3)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#212529',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            // Animation observers for cards
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.animate-slide-up').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                observer.observe(el);
            });

            // Real-time clock
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                // Update if time element exists
                const timeElement = document.querySelector('.current-time');
                if (timeElement) {
                    timeElement.textContent = timeString;
                }
            }

            // Update time every second
            setInterval(updateTime, 1000);
            updateTime(); // Initial call

            // Enhanced card interactions
            document.querySelectorAll('.stat-card-3d, .module-card-3d, .chart-card-3d').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.zIndex = '10';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.zIndex = '1';
                });
            });

            // Hacer clickeable toda la card de módulos principales
            document.querySelectorAll('.card .card-body').forEach(cardBody => {
                const parentCard = cardBody.closest('.card');
                const link = parentCard.querySelector('a');

                if (link && parentCard.style.cursor === 'pointer') {
                    cardBody.addEventListener('click', function(e) {
                        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
                            window.location.href = link.href;
                        }
                    });
                }
            });

            // Parallax effect for welcome section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const welcomeSection = document.querySelector('.welcome-section');
                if (welcomeSection) {
                    welcomeSection.style.transform = `translateY(${scrolled * 0.1}px)`;
                }
            });

            console.log('🚀 Dashboard GIR-365 cargado exitosamente - Estilo Silva 3D');
        });



        document.addEventListener('DOMContentLoaded', function() {
            // Actualizar fecha y hora en tiempo real
            function updateDateTime() {
                const now = new Date();
                const horaActual = now.getHours();

                // Actualizar saludo según la hora
                let greeting = 'Buenos días';
                if (horaActual >= 12 && horaActual < 18) greeting = 'Buenas tardes';
                if (horaActual >= 18) greeting = 'Buenas noches';

                document.getElementById('greeting').textContent = `${greeting}, {{ $userName }}`;

                // Formatear fecha
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    timeZone: 'America/Bogota'
                };
                const fecha = now.toLocaleDateString('es-CO', options);
                document.getElementById('current-date').textContent = fecha;

                // Formatear hora con ceros iniciales
                const horas = String(now.getHours()).padStart(2, '0');
                const minutos = String(now.getMinutes()).padStart(2, '0');
                const segundos = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('current-time').textContent = `${horas}:${minutos}:${segundos}`;
            }

            // Actualizar inmediatamente y cada segundo
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });
    </script>
@endpush
