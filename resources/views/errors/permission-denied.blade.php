@extends('layouts.dashboard')

@section('title', 'Acceso Denegado')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> Acceso Denegado</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-5x text-danger mb-3"></i>
                            <h5 class="font-weight-bold">No tiene permisos suficientes para acceder a este módulo o realizar
                                esta acción.</h5>
                        </div>

                        <p>Posibles motivos:</p>
                        <ul>
                            <li>Su perfil no tiene los permisos necesarios para este módulo.</li>
                            <li>No tiene acceso a esta empresa para realizar esta acción.</li>
                            <li>Se requiere un perfil específico como administrador o técnico para esta sección.</li>
                        </ul>

                        <div class="alert alert-info">
                            <p class="mb-0"><i class="fas fa-info-circle mr-2"></i> Si considera que debería tener acceso
                                a este módulo, contacte al administrador de su empresa o al administrador del sistema.</p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('empleados.index') }}" class="btn btn-primary">
                                <i class="fas fa-home mr-2"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Empresa: {{ session('empresa_data')['nombre'] ?? 'N/A' }}</small>
                        <small class="float-right">Usuario: {{ session('user_data')['email'] ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
