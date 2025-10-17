@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">
                        <i class="fas fa-brain me-2"></i>
                        Módulo Psicosocial
                    </h4>
                    <p class="text-muted mb-0">Gestión y análisis de evaluaciones de riesgo psicosocial</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Inicio a
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Psicosocial
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Silva Dashboard -->
        <div class="row mb-4">
            @foreach ($estadisticas as $index => $card)
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="avatar-sm rounded-circle bg-{{ ['primary', 'success', 'warning', 'danger'][$index % 4] }}">
                                        <span
                                            class="avatar-title rounded-circle bg-{{ ['primary', 'success', 'warning', 'danger'][$index % 4] }}">
                                            <i class="{{ $card['icon'] }} fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">{{ $card['value'] }}</h4>
                                    <p class="text-muted mb-0">{{ $card['label'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tabla de evaluaciones reales -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Evaluaciones Recientes
                </h4>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Área</th>
                            <th>Nivel de Riesgo</th>
                            <th>Fecha Evaluación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluaciones as $eval)
                            <tr>
                                <td>{{ $eval->empleado_nombre }}</td>
                                <td>{{ $eval->area }}</td>
                                <td><span class="badge bg-{{ $eval->color_riesgo }}">{{ $eval->nivel_riesgo }}</span></td>
                                <td>{{ $eval->fecha_evaluacion }}</td>
                                <td><span class="badge bg-info">{{ $eval->estado }}</span></td>
                                <td>
                                    <a href="{{ route('psicosocial.show', $eval->diagnostico_id ?? ($eval->diagnostico ?? ($eval->id ?? $eval->_id))) }}"
                                        class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('psicosocial.resumen', $eval->diagnostico_id ?? ($eval->diagnostico ?? ($eval->id ?? $eval->_id))) }}"
                                        class="btn btn-sm btn-primary" title="Resumen"><i class="fas fa-list-alt"></i></a>
                                    <a href="{{ route('psicosocial.resultados', $eval->diagnostico_id ?? ($eval->diagnostico ?? ($eval->id ?? $eval->_id))) }}"
                                        class="btn btn-sm btn-success" title="Resultados"><i
                                            class="fas fa-chart-bar"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay evaluaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
