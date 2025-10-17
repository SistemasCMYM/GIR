@extends('layouts.dashboard')

@section('title', 'Evaluación Individual')

@section('content')
    <div class="container-fluid py-4">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-2">Evaluación Individual</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-user me-2"></i>{{ $hoja->nombre_empleado }}
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

        <!-- Contenido de la evaluación -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Formularios de Evaluación
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fas fa-edit fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Funcionalidad en desarrollo</h5>
                            <p class="text-muted">Los formularios de evaluación estarán disponibles próximamente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
