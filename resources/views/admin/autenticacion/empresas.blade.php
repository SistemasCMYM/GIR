@extends('layouts.dashboard')

@section('title', 'Administración de Empresas - ' . ($empresaData->nombre ?? 'GIR-365'))

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-building mr-2"></i>Administración de Empresas
                        </h1>
                        <p class="mb-0 text-muted">Gestión de empresas, áreas, centros y procesos</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createEmpresaModal">
                            <i class="fas fa-plus mr-2"></i>Nueva Empresa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opciones de Gestión -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 mb-4">
                <div class="card shadow module-card">
                    <div class="card-body text-center">
                        <div class="module-icon text-primary mb-3">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <h6 class="card-title">Crear Empresas</h6>
                        <p class="card-text text-muted">Registrar nuevas empresas.</p>
                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#createEmpresaModal">Crear</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-4">
                <div class="card shadow module-card">
                    <div class="card-body text-center">
                        <div class="module-icon text-success mb-3">
                            <i class="fas fa-sitemap fa-2x"></i>
                        </div>
                        <h6 class="card-title">Cargue de Áreas</h6>
                        <p class="card-text text-muted">Cargar áreas de trabajo.</p>
                        <button class="btn btn-success btn-sm">Cargar</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-4">
                <div class="card shadow module-card">
                    <div class="card-body text-center">
                        <div class="module-icon text-info mb-3">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h6 class="card-title">Cargue de Centros</h6>
                        <p class="card-text text-muted">Cargar centros de trabajo.</p>
                        <button class="btn btn-info btn-sm">Cargar</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-4">
                <div class="card shadow module-card">
                    <div class="card-body text-center">
                        <div class="module-icon text-warning mb-3">
                            <i class="fas fa-city fa-2x"></i>
                        </div>
                        <h6 class="card-title">Cargue de Ciudades</h6>
                        <p class="card-text text-muted">Cargar ciudades.</p>
                        <button class="btn btn-warning btn-sm">Cargar</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-4">
                <div class="card shadow module-card">
                    <div class="card-body text-center">
                        <div class="module-icon text-secondary mb-3">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                        <h6 class="card-title">Cargue de Procesos</h6>
                        <p class="card-text text-muted">Cargar procesos.</p>
                        <button class="btn btn-secondary btn-sm">Cargar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Empresas -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Empresas Registradas</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>NIT</th>
                                        <th>Nombre</th>
                                        <th>Sector</th>
                                        <th>Ciudad</th>
                                        <th>Estado</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $empresaData->nit ?? '123456789' }}</td>
                                        <td>{{ $empresaData->nombre ?? 'Empresa Actual' }}</td>
                                        <td>{{ $empresaData->sector ?? 'Servicios' }}</td>
                                        <td>{{ $empresaData->ciudad ?? 'Bogotá' }}</td>
                                        <td><span class="badge badge-success">Activa</span></td>
                                        <td>{{ now()->format('d/m/Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

    <!-- Modal para crear empresa -->
    <div class="modal fade" id="createEmpresaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nueva Empresa</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createEmpresaForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIT</label>
                                    <input type="text" class="form-control" name="nit" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Razón Social</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sector</label>
                                    <select class="form-control" name="sector" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Servicios">Servicios</option>
                                        <option value="Manufactura">Manufactura</option>
                                        <option value="Construcción">Construcción</option>
                                        <option value="Minería">Minería</option>
                                        <option value="Agricultura">Agricultura</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ciudad</label>
                                    <input type="text" class="form-control" name="ciudad" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="direccion">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Crear Empresa</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .module-card {
            transition: transform 0.2s;
            border: none;
        }

        .module-card:hover {
            transform: translateY(-3px);
        }
    </style>
@endsection
