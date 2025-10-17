@extends('layouts.dashboard')

@section('title', 'Módulo Psicosocial')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-brain me-2"></i>
                            Módulo Psicosocial
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-heart me-2"></i>Evaluación de Riesgos Psicosociales</h5>
                                    <p class="mb-0">Este módulo permite la evaluación y gestión de riesgos psicosociales
                                        en la empresa <strong>{{ session('empresa_data.nombre') }}</strong>.</p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-primary">
                                            <div class="inner">
                                                <h3>0</h3>
                                                <p>Evaluaciones Activas</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-clipboard-list"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-success mt">
                                            <div class="inner">
                                                <h3>0</h3>
                                                <p>Completadas</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-check-double"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>0</h3>
                                                <p>Empleados Evaluados</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3>0</h3>
                                                <p>Riesgos Identificados</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h4>Funcionalidades del Módulo</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><i
                                                            class="fas fa-survey text-primary me-2"></i> Creación de
                                                        encuestas psicosociales</li>
                                                    <li class="list-group-item"><i
                                                            class="fas fa-user-check text-success me-2"></i> Evaluación de
                                                        empleados</li>
                                                    <li class="list-group-item"><i
                                                            class="fas fa-chart-line text-info me-2"></i> Análisis de
                                                        resultados</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><i
                                                            class="fas fa-shield-alt text-warning me-2"></i> Identificación
                                                        de riesgos</li>
                                                    <li class="list-group-item"><i
                                                            class="fas fa-recommendations text-secondary me-2"></i> Planes
                                                        de mejora</li>
                                                    <li class="list-group-item"><i
                                                            class="fas fa-file-pdf text-danger me-2"></i> Reportes
                                                        especializados</li>
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
