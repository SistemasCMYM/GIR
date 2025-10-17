@extends('layouts.dashboard')

@section('title', 'Plan de Intervención - ' . $diagnostico->descripcion)

@section('content')
    <div class="container-fluid py-4">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-2">Plan de Intervención - {{ $diagnostico->descripcion }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-tasks me-2"></i>Estrategias de mejora basadas en resultados
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('psicosocial.show', $diagnostico->id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido del plan de intervención -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Actividades de Intervención
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Funcionalidad en desarrollo</h5>
                            <p class="text-muted">El plan de intervención personalizado estará disponible próximamente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
