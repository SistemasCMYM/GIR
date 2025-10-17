@extends('layouts.dashboard')

@section('title', 'Módulo Psicosocial')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empleados.index') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-brain"></i> Módulo Psicosocial
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Módulo Psicosocial</h1>
                        <p class="text-muted">Gestión de evaluaciones psicosociales</p>
                    </div>
                    <div>
                        <a href="{{ route('psicosocial.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Evaluación
                        </a>
                    </div>
                </div>
            </div>
        </div> <!-- Estadísticas generales -->
        <div class="row mb-4 g-3">
            <div class="col-md-2 d-flex">
                <div class="card bg-primary text-white w-100 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['total_diagnosticos'] ?? 0 }}</h3>
                        <p class="mb-0">Diagnósticos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex">
                <div class="card bg-success text-white w-100 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['evaluaciones_completadas'] ?? 0 }}</h3>
                        <p class="mb-0">Empleados Completados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex">
                <div class="card bg-info text-white w-100 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-spinner fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['evaluaciones_en_proceso'] ?? 0 }}</h3>
                        <p class="mb-0">Empleados En Proceso</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex">
                <div class="card bg-warning text-white w-100 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['evaluaciones_pendientes'] ?? 0 }}</h3>
                        <p class="mb-0">Empleados Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex">
                <div class="card bg-secondary text-white w-100 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['empleados_evaluados'] ?? 0 }}</h3>
                        <p class="mb-0">Empleados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex">
                <div class="card text-white w-100 h-100" style="background-color: #D1A854;">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['porcentaje_completado'] ?? 0 }}%</h3>
                        <p class="mb-0">Completado</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Distribución de Niveles de Riesgo
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Distribución de Niveles de Riesgo - Diseño Moderno -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="risk-distribution-container">
                                    <!-- Header -->
                                    <div class="risk-header">
                                        <div>
                                            <p class="risk-header-subtitle"> Análisis de riesgo psicosocial</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grid de Tarjetas Niveles de Riesgo - Estilo 3D -->
                        <div class="row g-3 mb-4">
                            <!-- Sin Riesgo -->
                            <div class="col-12 col-md-6 col-lg">
                                <div class="card border-0 shadow position-relative overflow-hidden"
                                    style="min-height: 220px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; transform: perspective(1000px) rotateX(0deg); box-shadow: 0 10px 30px rgba(0, 130, 53, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1);"
                                    onmouseover="this.style.transform='perspective(1000px) rotateX(5deg) translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(0, 130, 53, 0.3), 0 10px 20px rgba(0, 0, 0, 0.15)';"
                                    onmouseout="this.style.transform='perspective(1000px) rotateX(0deg) translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0, 130, 53, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1)';">
                                    <!-- Barra superior con gradiente 3D -->
                                    <div
                                        style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(135deg, #008235 0%, #00a842 100%); box-shadow: 0 2px 10px rgba(0, 130, 53, 0.4);">
                                    </div>

                                    <div class="card-body text-center pt-2 pb-2" style="padding: 2rem 1.5rem;">
                                        <!-- Icono 3D -->
                                        <div class="d-flex justify-content-center mb-3">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width: 72px; height: 72px; background: linear-gradient(135deg, #008235 0%, #00a842 100%); border-radius: 50%; box-shadow: 0 10px 25px rgba(0, 130, 53, 0.4), inset 0 -3px 10px rgba(0, 0, 0, 0.2);">
                                                <i class="fas fa-check-square text-white" style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                        <!-- Contenido -->
                                        <h3 class="fw-bold text-dark mb-2"
                                            style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                            {{ $estadisticas['niveles_riesgo']['Sin Riesgo'] ?? 0 }}</h3>
                                        <p class="text-muted fw-semibold mb-3" style="font-size: 1rem;">Sin Riesgo</p>
                                        <span class="badge rounded-pill px-4 py-2"
                                            style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #008235; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(0, 130, 53, 0.2);">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Riesgo Bajo -->
                            <div class="col-12 col-md-6 col-lg">
                                <div class="card border-0 shadow position-relative overflow-hidden"
                                    style="min-height: 220px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; transform: perspective(1000px) rotateX(0deg); box-shadow: 0 10px 30px rgba(0, 211, 100, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1);"
                                    onmouseover="this.style.transform='perspective(1000px) rotateX(5deg) translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(0, 211, 100, 0.3), 0 10px 20px rgba(0, 0, 0, 0.15)';"
                                    onmouseout="this.style.transform='perspective(1000px) rotateX(0deg) translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0, 211, 100, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1)';">
                                    <div
                                        style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(135deg, #00D364 0%, #00f57a 100%); box-shadow: 0 2px 10px rgba(0, 211, 100, 0.4);">
                                    </div>
                                    <div class="card-body text-center pt-2 pb-2" style="padding: 2rem 1.5rem;">
                                        <div class="d-flex justify-content-center mb-3">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width: 72px; height: 72px; background: linear-gradient(135deg, #00D364 0%, #00f57a 100%); border-radius: 50%; box-shadow: 0 10px 25px rgba(0, 211, 100, 0.4), inset 0 -3px 10px rgba(0, 0, 0, 0.2);">
                                                <i class="fas fa-check-circle text-white" style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-2"
                                            style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                            {{ $estadisticas['niveles_riesgo']['Riesgo Bajo'] ?? 0 }}</h3>
                                        <p class="text-muted fw-semibold mb-3" style="font-size: 1rem;">Riesgo Bajo</p>
                                        <span class="badge rounded-pill px-4 py-2"
                                            style="background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); color: #00D364; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(0, 211, 100, 0.2);">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Riesgo Medio -->
                            <div class="col-12 col-md-6 col-lg">
                                <div class="card border-0 shadow position-relative overflow-hidden"
                                    style="min-height: 220px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; transform: perspective(1000px) rotateX(0deg); box-shadow: 0 10px 30px rgba(255, 214, 0, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1);"
                                    onmouseover="this.style.transform='perspective(1000px) rotateX(5deg) translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(255, 214, 0, 0.3), 0 10px 20px rgba(0, 0, 0, 0.15)';"
                                    onmouseout="this.style.transform='perspective(1000px) rotateX(0deg) translateY(0)'; this.style.boxShadow='0 10px 30px rgba(255, 214, 0, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1)';">
                                    <div
                                        style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(135deg, #FFD600 0%, #ffed4e 100%); box-shadow: 0 2px 10px rgba(255, 214, 0, 0.4);">
                                    </div>

                                    <div class="card-body text-center pt-2 pb-2" style="padding: 2rem 1.5rem;">
                                        <div class="d-flex justify-content-center mb-3">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width: 72px; height: 72px; background: linear-gradient(135deg, #FFD600 0%, #ffed4e 100%); border-radius: 50%; box-shadow: 0 10px 25px rgba(255, 214, 0, 0.4), inset 0 -3px 10px rgba(0, 0, 0, 0.2);">
                                                <i class="fas fa-exclamation-circle text-white"
                                                    style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-2"
                                            style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                            {{ $estadisticas['niveles_riesgo']['Riesgo Medio'] ?? 0 }}</h3>
                                        <p class="text-muted fw-semibold mb-3" style="font-size: 1rem;">Riesgo Medio</p>
                                        <span class="badge rounded-pill px-4 py-2"
                                            style="background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%); color: #c89e00; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(255, 214, 0, 0.2);">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Riesgo Alto -->
                            <div class="col-12 col-md-6 col-lg">
                                <div class="card border-0 shadow position-relative overflow-hidden"
                                    style="min-height: 220px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; transform: perspective(1000px) rotateX(0deg); box-shadow: 0 10px 30px rgba(218, 80, 80, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1);"
                                    onmouseover="this.style.transform='perspective(1000px) rotateX(5deg) translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(218, 80, 80, 0.3), 0 10px 20px rgba(0, 0, 0, 0.15)';"
                                    onmouseout="this.style.transform='perspective(1000px) rotateX(0deg) translateY(0)'; this.style.boxShadow='0 10px 30px rgba(218, 80, 80, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1)';">
                                    <div
                                        style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(135deg, #DA5050 0%, #ff6b6b 100%); box-shadow: 0 2px 10px rgba(218, 80, 80, 0.4);">
                                    </div>

                                    <div class="card-body text-center pt-2 pb-2" style="padding: 2rem 1.5rem;">
                                        <div class="d-flex justify-content-center mb-3">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width: 72px; height: 72px; background: linear-gradient(135deg, #DA5050 0%, #ff6b6b 100%); border-radius: 50%; box-shadow: 0 10px 25px rgba(218, 80, 80, 0.4), inset 0 -3px 10px rgba(0, 0, 0, 0.2);">
                                                <i class="fas fa-exclamation-triangle text-white"
                                                    style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-2"
                                            style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                            {{ $estadisticas['niveles_riesgo']['Riesgo Alto'] ?? 0 }}</h3>
                                        <p class="text-muted fw-semibold mb-3" style="font-size: 1rem;">Riesgo Alto</p>
                                        <span class="badge rounded-pill px-4 py-2"
                                            style="background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%); color: #c53030; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(218, 80, 80, 0.2);">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Riesgo Muy Alto -->
                            <div class="col-12 col-md-6 col-lg">
                                <div class="card border-0 shadow position-relative overflow-hidden"
                                    style="min-height: 220px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; transform: perspective(1000px) rotateX(0deg); box-shadow: 0 10px 30px rgba(235, 1, 1, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1);"
                                    onmouseover="this.style.transform='perspective(1000px) rotateX(5deg) translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(235, 1, 1, 0.3), 0 10px 20px rgba(0, 0, 0, 0.15)';"
                                    onmouseout="this.style.transform='perspective(1000px) rotateX(0deg) translateY(0)'; this.style.boxShadow='0 10px 30px rgba(235, 1, 1, 0.15), 0 1px 8px rgba(0, 0, 0, 0.1)';">
                                    <div
                                        style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(135deg, #EB0101 0%, #ff3333 100%); box-shadow: 0 2px 10px rgba(235, 1, 1, 0.4);">
                                    </div>

                                    <div class="card-body text-center pt-2 pb-2" style="padding: 2rem 1.5rem;">
                                        <div class="d-flex justify-content-center mb-3">
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width: 72px; height: 72px; background: linear-gradient(135deg, #EB0101 0%, #ff3333 100%); border-radius: 50%; box-shadow: 0 10px 25px rgba(235, 1, 1, 0.4), inset 0 -3px 10px rgba(0, 0, 0, 0.2);">
                                                <i class="fas fa-times-circle text-white" style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-2"
                                            style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                            {{ $estadisticas['niveles_riesgo']['Muy Alto'] ?? 0 }}</h3>
                                        <p class="text-muted fw-semibold mb-3" style="font-size: 1rem;">Muy Alto</p>
                                        <span class="badge rounded-pill px-4 py-2"
                                            style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); color: #991b1b; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(235, 1, 1, 0.2);">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Separador -->
                        <hr class="my-4" style="border-top: 1px solid #e5e7eb;">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de diagnósticos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Tarjetas de Aplicación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($diagnosticos as $diagnostico)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $diagnostico->descripcion }}</h6>
                                        <p class="text-muted small">
                                            Creado:
                                            @php
                                                $fechaCreado = isset($diagnostico->_fechaCreado)
                                                    ? $diagnostico->_fechaCreado
                                                    : (isset($diagnostico->created_at)
                                                        ? $diagnostico->created_at
                                                        : null);
                                            @endphp
                                            @if ($fechaCreado)
                                                @if ($fechaCreado instanceof \MongoDB\BSON\UTCDateTime)
                                                    {{ $fechaCreado->toDateTime()->format('d/m/Y') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($fechaCreado)->format('d/m/Y') }}
                                                @endif
                                            @else
                                                Sin fecha
                                            @endif
                                        </p> <!-- Estadísticas del diagnóstico -->
                                        <div class="row text-center mb-3">
                                            <div class="col-3">
                                                <small class="text-muted">Total</small>
                                                <div class="fw-bold">{{ $diagnostico->total_empleados ?? 0 }}</div>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-success">Completados</small>
                                                <div class="fw-bold text-success">{{ $diagnostico->completados ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-info">En Proceso</small>
                                                <div class="fw-bold text-info">{{ $diagnostico->en_proceso ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-warning">Pendientes</small>
                                                <div class="fw-bold text-warning">{{ $diagnostico->pendientes ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Barra de progreso -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Progreso</small>
                                                <small
                                                    class="text-muted">{{ number_format($diagnostico->porcentaje_completado ?? 0, 1) }}%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $diagnostico->porcentaje_completado ?? 0 }}%">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Información de Filtro -->
                                        @if ($diagnostico->tiene_filtro ?? false)
                                            <div class="mb-3">
                                                <div class="alert alert-info py-2 mb-2">
                                                    <i class="fas fa-filter me-1"></i>
                                                    <small>
                                                        <strong>Filtro aplicado:</strong>
                                                        {{ $diagnostico->empleados_pendientes_asignar ?? 0 }} empleados
                                                        pendientes por asignar
                                                    </small>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Acciones -->
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('psicosocial.show', $diagnostico->id) }}"
                                                class="btn btn-gir-gold btn-sm">
                                                <i class="fas fa-eye me-1"></i>Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- SVG Definitions para gradientes -->
    <svg width="0" height="0" style="position: absolute;">
        <defs>
            <linearGradient id="gradient-completion" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
            </linearGradient>
        </defs>
    </svg>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/psicosocial-risk.css') }}">
@endpush
