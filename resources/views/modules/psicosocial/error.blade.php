@extends('layouts.dashboard')

@section('title', 'Error - Módulo Psicosocial')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Error en Módulo Psicosocial
                    </h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-header bg-warning text-white">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $error ?? 'Error' }}
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-brain fa-4x text-muted mb-4"></i>
                                <h4 class="text-danger">{{ $mensaje ?? 'Ha ocurrido un error inesperado' }}</h4>
                                <p class="text-muted">Por favor, intente una de las siguientes opciones:</p>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light border shadow-sm h-100">
                                        <div class="card-body">
                                            <i class="fas fa-sign-in-alt fa-2x text-primary mb-3"></i>
                                            <h5 class="card-title">Iniciar sesión nuevamente</h5>
                                            <p class="card-text small">Su sesión puede haber expirado o los datos necesarios
                                                no están disponibles.</p>
                                            <a href="{{ route('login.nit') }}" class="btn btn-primary btn-sm">
                                                Iniciar sesión
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light border shadow-sm h-100">
                                        <div class="card-body">
                                            <i class="fas fa-home fa-2x text-success mb-3"></i>
                                            <h5 class="card-title">Ir al Inicio</h5>
                                            <p class="card-text small">Regrese al panel principal para continuar con otras
                                                operaciones.</p>
                                            <a href="{{ route('empleados.index') }}" class="btn btn-success btn-sm">
                                                Inicio
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light border shadow-sm h-100">
                                        <div class="card-body">
                                            <i class="fas fa-sync-alt fa-2x text-info mb-3"></i>
                                            <h5 class="card-title">Intentar nuevamente</h5>
                                            <p class="card-text small">Intente acceder nuevamente al módulo psicosocial.</p>
                                            <a href="{{ route('psicosocial.index') }}" class="btn btn-info btn-sm">
                                                Reintentar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Si el problema persiste, contacte al administrador del sistema.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
