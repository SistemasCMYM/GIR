@extends('layouts.dashboard')

@section('title', 'Roles y Permisos')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Roles y Permisos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item">Autenticación</li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- Roles --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Roles del Sistema</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo Rol
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Administrador</h6>
                                        <p class="mb-1 text-muted">Acceso completo al sistema</p>
                                        <small class="text-muted">5 usuarios asignados</small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Supervisor</h6>
                                        <p class="mb-1 text-muted">Supervisión y aprobación de reportes</p>
                                        <small class="text-muted">12 usuarios asignados</small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Usuario Estándar</h6>
                                        <p class="mb-1 text-muted">Acceso básico a módulos asignados</p>
                                        <small class="text-muted">133 usuarios asignados</small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Permisos --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Matriz de Permisos</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Módulo/Función</th>
                                            <th class="text-center">Admin</th>
                                            <th class="text-center">Supervisor</th>
                                            <th class="text-center">Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Inicio</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gestión Usuarios</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hallazgos - Ver</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hallazgos - Crear</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Psicosocial - Ver</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Reportes - Generar</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Configuración</strong></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Crear nuevo rol --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Asignación de Permisos por Rol</h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Seleccionar Rol</label>
                                            <select class="form-control">
                                                <option>Seleccione un rol...</option>
                                                <option>Administrador</option>
                                                <option>Supervisor</option>
                                                <option>Usuario Estándar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label>Permisos Disponibles</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm1">
                                                    <label class="form-check-label" for="perm1">Ver Inicio</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm2">
                                                    <label class="form-check-label" for="perm2">Gestionar
                                                        Usuarios</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm3">
                                                    <label class="form-check-label" for="perm3">Ver Hallazgos</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm4">
                                                    <label class="form-check-label" for="perm4">Crear Hallazgos</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm5">
                                                    <label class="form-check-label" for="perm5">Ver Psicosocial</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm6">
                                                    <label class="form-check-label" for="perm6">Generar
                                                        Reportes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm7">
                                                    <label class="form-check-label" for="perm7">Configuración
                                                        Sistema</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="perm8">
                                                    <label class="form-check-label" for="perm8">Backup y
                                                        Restauración</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Actualizar Permisos</button>
                                        <button type="button" class="btn btn-secondary ml-2">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
