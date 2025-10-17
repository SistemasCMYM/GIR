@extends('layouts.dashboard')

@section('title', 'Estado de Evaluación Psicosocial')
@section('page-title', 'Estado de Evaluación Psicosocial')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('psicosocial.index') }}">Evaluaciones Psicosociales</a></li>
    <li class="breadcrumb-item active">Estado de Evaluación</li>
@endsection

@push('styles')
    <style>
        .evaluation-status-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .evaluation-status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .status-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .status-item {
            transition: all 0.3s ease;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .status-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .status-badge.completed {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .status-badge.pending {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }

        .status-badge.not-started {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .view-btn {
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .employee-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .modal-header.custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0;
        }

        .progress-summary {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empleados.index') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-briefcase"></i> Gestión de Instrumentos
                </li>
            </ol>
        </nav>

        {{-- Información del Empleado --}}
        <div class="employee-info-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-3">
                        <i class="fas fa-user-circle me-2"></i>
                        {{ $evaluacion->empleado->nombres ?? 'N/A' }} {{ $evaluacion->empleado->apellidos ?? '' }}
                    </h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Documento:</strong><br>
                                {{ $evaluacion->empleado->documento ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Cargo:</strong><br>
                                {{ $evaluacion->empleado->cargo ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Área:</strong><br>
                                {{ $evaluacion->empleado->area ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Tipo de Evaluación:</strong><br>
                                Forma {{ $evaluacion->tipo ?? 'A' }}
                                ({{ $evaluacion->tipo == 'A' ? 'Profesionales' : 'Auxiliares/Operarios' }})
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="progress-summary">
                        @php
                            $completados = 0;
                            $total = 4;

                            if ($evaluacion->completado_datos ?? false) {
                                $completados++;
                            }
                            if ($evaluacion->completado_intralaboral ?? false) {
                                $completados++;
                            }
                            if ($evaluacion->completado_extralaboral ?? false) {
                                $completados++;
                            }
                            if ($evaluacion->completado_estres ?? false) {
                                $completados++;
                            }

                            $porcentaje = round(($completados / $total) * 100);
                            $color = $porcentaje == 100 ? '#28a745' : ($porcentaje >= 50 ? '#ffc107' : '#dc3545');
                        @endphp

                        <div class="progress-circle"
                            style="background: conic-gradient({{ $color }} {{ $porcentaje * 3.6 }}deg, #e9ecef 0deg);">
                            {{ $porcentaje }}%
                        </div>
                        <h5 class="text-dark mb-0">Progreso General</h5>
                        <small class="text-muted">{{ $completados }}/{{ $total }} cuestionarios</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de la Evaluación --}}
        <div class="card evaluation-status-card">
            <div class="card-header status-header">
                <h4 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Estado de la Evaluación
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    {{-- 1. Datos Personales --}}
                    <div class="list-group-item status-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-id-card me-3" style="font-size: 1.5rem; color: #28a745;"></i>
                            <div>
                                <h6 class="mb-1">Datos Personales</h6>
                                <small class="text-muted">Información sociodemográfica y ocupacional</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @if ($evaluacion->completado_datos ?? false)
                                <span class="status-badge completed me-3">
                                    <i class="fas fa-check me-1"></i> Completado
                                </span>
                                <button class="btn btn-outline-primary view-btn btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalDatosGenerales">
                                    <i class="fas fa-eye me-1"></i> Ver Respuestas
                                </button>
                            @else
                                <span class="status-badge pending me-3">
                                    <i class="fas fa-clock me-1"></i> Pendiente
                                </span>
                                <button class="btn btn-secondary view-btn btn-sm" disabled>
                                    <i class="fas fa-lock me-1"></i> No Disponible
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- 2. Intralaboral (A/B dinámico) --}}
                    <div class="list-group-item status-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building me-3" style="font-size: 1.5rem; color: #dc3545;"></i>
                            <div>
                                <h6 class="mb-1">
                                    Factores Intralaborales - Forma {{ $evaluacion->tipo ?? 'A' }}
                                </h6>
                                <small class="text-muted">
                                    {{ $evaluacion->tipo == 'A' ? 'Profesionales, analistas, técnicos, jefes' : 'Auxiliares y operarios' }}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @if ($evaluacion->completado_intralaboral ?? false)
                                <span class="status-badge completed me-3">
                                    <i class="fas fa-check me-1"></i> Completado
                                </span>
                                <button class="btn btn-outline-primary view-btn btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalIntralaboral{{ $evaluacion->tipo ?? 'A' }}">
                                    <i class="fas fa-eye me-1"></i> Ver Respuestas
                                </button>
                            @else
                                <span class="status-badge pending me-3">
                                    <i class="fas fa-clock me-1"></i> Pendiente
                                </span>
                                <button class="btn btn-secondary view-btn btn-sm" disabled>
                                    <i class="fas fa-lock me-1"></i> No Disponible
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Extralaboral --}}
                    <div class="list-group-item status-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-home me-3" style="font-size: 1.5rem; color: #ffc107;"></i>
                            <div>
                                <h6 class="mb-1">Factores Extralaborales</h6>
                                <small class="text-muted">Condiciones del entorno familiar, social y económico</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @if ($evaluacion->completado_extralaboral ?? false)
                                <span class="status-badge completed me-3">
                                    <i class="fas fa-check me-1"></i> Completado
                                </span>
                                <button class="btn btn-outline-primary view-btn btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalExtralaboral">
                                    <i class="fas fa-eye me-1"></i> Ver Respuestas
                                </button>
                            @else
                                <span class="status-badge pending me-3">
                                    <i class="fas fa-clock me-1"></i> Pendiente
                                </span>
                                <button class="btn btn-secondary view-btn btn-sm" disabled>
                                    <i class="fas fa-lock me-1"></i> No Disponible
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- 4. Estrés --}}
                    <div class="list-group-item status-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-heart-pulse me-3" style="font-size: 1.5rem; color: #6f42c1;"></i>
                            <div>
                                <h6 class="mb-1">Cuestionario de Estrés</h6>
                                <small class="text-muted">Evaluación de síntomas de estrés en el trabajo</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @if ($evaluacion->completado_estres ?? false)
                                <span class="status-badge completed me-3">
                                    <i class="fas fa-check me-1"></i> Completado
                                </span>
                                <button class="btn btn-outline-primary view-btn btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEstres">
                                    <i class="fas fa-eye me-1"></i> Ver Respuestas
                                </button>
                            @else
                                <span class="status-badge pending me-3">
                                    <i class="fas fa-clock me-1"></i> Pendiente
                                </span>
                                <button class="btn btn-secondary view-btn btn-sm" disabled>
                                    <i class="fas fa-lock me-1"></i> No Disponible
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones Adicionales --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 3rem;"></i>
                        <h5>Generar Reporte</h5>
                        <p class="text-muted">Exportar resultados completos en PDF</p>
                        @if ($porcentaje == 100)
                            <a href="{{ route('psicosocial.exportar-pdf', $evaluacion->id) }}" class="btn btn-danger">
                                <i class="fas fa-download me-1"></i> Descargar PDF
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i> Completar Evaluación
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line text-success mb-3" style="font-size: 3rem;"></i>
                        <h5>Ver Resultados</h5>
                        <p class="text-muted">Análisis detallado y recomendaciones</p>
                        @if ($porcentaje == 100)
                            <a href="{{ route('psicosocial.resultados', $evaluacion->id) }}" class="btn btn-success">
                                <i class="fas fa-chart-bar me-1"></i> Ver Análisis
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i> Completar Evaluación
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Incluir Modales (partials de lectura) --}}
    @if ($evaluacion->completado_datos ?? false)
        @include('gestion-instrumentos.cuestionarios.partials.modal-datos-generales', [
            'evaluacion' => $evaluacion,
            'empleado' => $evaluacion->empleado,
        ])
    @endif

    @if ($evaluacion->completado_extralaboral ?? false)
        @include('gestion-instrumentos.cuestionarios.partials.modal-extralaboral', [
            'evaluacion' => $evaluacion,
            'empleado' => $evaluacion->empleado,
        ])
    @endif

    @if ($evaluacion->completado_estres ?? false)
        @include('gestion-instrumentos.cuestionarios.partials.modal-estres', [
            'evaluacion' => $evaluacion,
            'empleado' => $evaluacion->empleado,
        ])
    @endif

    {{-- Modal dinámico para Intralaboral (A/B) --}}
    @if ($evaluacion->completado_intralaboral ?? false)
        @if (($evaluacion->tipo ?? 'A') == 'A')
            @include('gestion-instrumentos.cuestionarios.partials.modal-intralaboral-a', [
                'evaluacion' => $evaluacion,
                'empleado' => $evaluacion->empleado,
            ])
        @else
            @include('gestion-instrumentos.cuestionarios.partials.modal-intralaboral-b', [
                'evaluacion' => $evaluacion,
                'empleado' => $evaluacion->empleado,
            ])
        @endif
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animaciones de entrada
            const cards = document.querySelectorAll('.evaluation-status-card, .employee-info-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Efectos hover mejorados
            document.querySelectorAll('.status-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.background = '#e3f2fd';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.background = '#f8f9fa';
                });
            });

            // Tooltips para badges
            const badges = document.querySelectorAll('.status-badge');
            badges.forEach(badge => {
                badge.setAttribute('data-bs-toggle', 'tooltip');
                if (badge.classList.contains('completed')) {
                    badge.setAttribute('title', 'Cuestionario completado exitosamente');
                } else if (badge.classList.contains('pending')) {
                    badge.setAttribute('title', 'Pendiente de completar');
                }
            });

            // Inicializar tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            console.log('Estado de Evaluación Psicosocial cargado exitosamente');
        });
    </script>
@endpush
