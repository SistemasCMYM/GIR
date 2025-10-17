@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item">Autenticación</li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <section class="content">
        <div class="container-fluid">
            {{-- Stats cards --}}
            <div class="alert alert-info">
                Esta vista ha sido reemplazada por la nueva Gestión Administrativa de Usuarios.
                <a class="alert-link" href="{{ route('usuarios.admin') }}">Ir a Administración de Usuarios</a>.
            </div>

            {{-- Main content --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Usuarios</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo Usuario
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Buscar usuarios...">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control">
                                        <option>Todos los estados</option>
                                        <option>Activos</option>
                                        <option>Inactivos</option>
                                        <option>Bloqueados</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control">
                                        <option>Todos los roles</option>
                                        <option>Administrador</option>
                                        <option>Usuario</option>
                                        <option>Supervisor</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-block">Filtrar</button>
                                </div>
                            </div>

                            <div class="text-center text-muted py-5">
                                <i class="fas fa-info-circle me-1"></i>
                                No hay datos para mostrar aquí. Use la nueva interfaz de Administración de Usuarios.
                            </div>

                            {{-- Pagination --}}
                            <!-- Paginación eliminada: era estática/dummy -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
