@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">
                        <i class="fas fa-search me-2"></i>
                        Gestión de Hallazgos
                    </h4>
                    <p class="text-muted mb-0">Administre y realice seguimiento a todos los hallazgos del sistema</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Hallazgos
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Silva Dashboard -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title rounded-circle bg-info">
                                        <i class="fas fa-list fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">150</h4>
                                <p class="text-muted mb-0">Total Hallazgos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title rounded-circle bg-success">
                                        <i class="fas fa-check-circle fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">87</h4>
                                <p class="text-muted mb-0">Cerrados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="fas fa-clock fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">63</h4>
                                <p class="text-muted mb-0">En Proceso</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-danger">
                                    <span class="avatar-title rounded-circle bg-danger">
                                        <i class="fas fa-exclamation-triangle fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">12</h4>
                                <p class="text-muted mb-0">Críticos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-table me-2"></i>
                                Lista de Hallazgos
                            </h4>
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Nuevo Hallazgo
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Filtrar por Estado:</label>
                                    <select class="form-control">
                                        <option>Todos</option>
                                        <option>Abierto</option>
                                        <option>En Proceso</option>
                                        <option>Cerrado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Filtrar por Prioridad:</label>
                                    <select class="form-control">
                                        <option>Todas</option>
                                        <option>Crítica</option>
                                        <option>Alta</option>
                                        <option>Media</option>
                                        <option>Baja</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Buscar:</label>
                                    <input type="text" class="form-control" placeholder="Buscar hallazgos...">
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Asignado a</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>001</td>
                                    <td>Falla en sistema de ventilación</td>
                                    <td>Sistema de ventilación presenta ruidos anómalos</td>
                                    <td><span class="badge badge-danger">Crítica</span></td>
                                    <td><span class="badge badge-warning">En Proceso</span></td>
                                    <td>Juan Pérez</td>
                                    <td>01/06/2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>002</td>
                                    <td>Iluminación deficiente</td>
                                    <td>Área de trabajo con iluminación insuficiente</td>
                                    <td><span class="badge badge-warning">Alta</span></td>
                                    <td><span class="badge badge-info">Abierto</span></td>
                                    <td>María González</td>
                                    <td>30/05/2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>003</td>
                                    <td>Equipos de protección</td>
                                    <td>Falta de equipos de protección personal</td>
                                    <td><span class="badge badge-danger">Crítica</span></td>
                                    <td><span class="badge badge-success">Cerrado</span></td>
                                    <td>Carlos Ruiz</td>
                                    <td>28/05/2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Hallazgos por Estado
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-responsive">
                            <div class="text-center">
                                <canvas id="pieChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Tendencia de Hallazgos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-responsive">
                            <canvas id="barChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Placeholder para gráficos cuando se implemente Chart.js
            console.log('Módulo Hallazgos cargado correctamente');
        });
    </script>
@endsection
</div>
<div class="card-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Hallazgo
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="search" class="form-control form-control-lg" placeholder="Buscar hallazgos...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-lg btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Responsable</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>001</td>
                <td>Falta de señalización en escaleras</td>
                <td><span class="badge bg-warning">Seguridad</span></td>
                <td><span class="badge bg-danger">Abierto</span></td>
                <td>Juan Pérez</td>
                <td>01/06/2025</td>
                <td>
                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>002</td>
                <td>Equipos de protección desactualizados</td>
                <td><span class="badge bg-info">Equipos</span></td>
                <td><span class="badge bg-warning">En proceso</span></td>
                <td>María García</td>
                <td>30/05/2025</td>
                <td>
                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>003</td>
                <td>Iluminación deficiente en área de trabajo</td>
                <td><span class="badge bg-secondary">Infraestructura</span></td>
                <td><span class="badge bg-success">Cerrado</span></td>
                <td>Carlos López</td>
                <td>28/05/2025</td>
                <td>
                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</div>
</div>
</div>
</div>
</div>
@endsection
