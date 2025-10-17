@extends('layouts.dashboard')

@section('title', 'Módulo de Hallazgos - ' . $empresaData->nombre)

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background-color: {{ $empresaData->colorPrimario }}; color: white;">
                        <h3 class="card-title">
                            <i class="fas fa-search mr-2"></i>
                            Módulo de Hallazgos
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('empleados.index') }}" class="btn btn-tool text-white">
                                <i class="fas fa-home"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            <strong>Usuario:</strong> {{ $userData->nick ?? $userData->email }} |
                            <strong>Empresa:</strong> {{ $empresaData->nombre }} |
                            <strong>Permisos:</strong> {{ implode(', ', $permisos) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>0</h3>
                    <p>Hallazgos Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>0</h3>
                    <p>Cerrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>0</h3>
                    <p>En Proceso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>0</h3>
                    <p>Críticos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h4>Funcionalidades Principales</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fas fa-plus text-success me-2"></i> Registro de nuevos
                            hallazgos</li>
                        <li class="list-group-item"><i class="fas fa-edit text-primary me-2"></i> Seguimiento y
                            actualización</li>
                        <li class="list-group-item"><i class="fas fa-chart-bar text-info me-2"></i> Reportes y estadísticas
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fas fa-calendar text-warning me-2"></i> Programación de
                            revisiones</li>
                        <li class="list-group-item"><i class="fas fa-users text-secondary me-2"></i> Asignación de
                            responsables</li>
                        <li class="list-group-item"><i class="fas fa-download text-dark me-2"></i> Exportación de datos</li>
                    </ul>
                </div>
            </div>
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
