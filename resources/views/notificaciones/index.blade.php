@extends('layouts.dashboard')

@section('title', 'Notificaciones - GIR-365')
@section('page-title', 'Notificaciones')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">
                        <i class="fas fa-bell me-2"></i>
                        Notificaciones
                    </h4>
                    <p class="text-muted mb-0">Centro de notificaciones y alertas del sistema</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Notificaciones
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
                                <div class="avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="fas fa-envelope fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">0</h4>
                                <p class="text-muted mb-0">Total Notificaciones</p>
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
                                <h4 class="mb-0">0</h4>
                                <p class="text-muted mb-0">Leídas</p>
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
                                        <i class="fas fa-exclamation fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">0</h4>
                                <p class="text-muted mb-0">Pendientes</p>
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
                                        <i class="fas fa-bell fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">0</h4>
                                <p class="text-muted mb-0">Alertas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>
                            Lista de Notificaciones
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No hay notificaciones</h5>
                            <p class="text-muted">Cuando recibas notificaciones, aparecerán aquí.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
