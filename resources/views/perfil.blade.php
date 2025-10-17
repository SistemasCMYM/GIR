@extends('layouts.dashboard')

@section('title', 'Mi Perfil - ' . ($empresaData->nombre ?? 'GIR-365'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- Mensajes de éxito y error -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>
                            Mi Perfil
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-user-circle me-2"></i>Información Personal</h5>
                                <hr>
                                <p><strong>Email:</strong> {{ session('user_data.email') }}</p>
                                <p><strong>Usuario:</strong> {{ session('user_data.nick', 'No especificado') }}</p>
                                <p><strong>Rol:</strong> {{ ucfirst(session('user_data.rol', 'Usuario')) }}</p>
                                <p><strong>Tipo:</strong> {{ ucfirst(session('user_data.tipo', 'N/A')) }}</p>
                                @if (session('user_data.dni'))
                                    <p><strong>DNI:</strong> {{ session('user_data.dni') }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-building me-2"></i>Información de Empresa</h5>
                                <hr>
                                <p><strong>Empresa:</strong> {{ session('empresa_data.nombre') }}</p>
                                <p><strong>NIT:</strong> {{ session('empresa_data.nit') }}</p>
                                <p><strong>Razón Social:</strong> {{ session('empresa_data.razon_social', 'N/A') }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h5><i class="fas fa-key me-2"></i>Cambiar Contraseña</h5>
                                <form method="POST" action="{{ route('perfil.update-password') }}" class="mt-3">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                                <input type="password"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    id="current_password" name="current_password" required>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                                <input type="password"
                                                    class="form-control @error('new_password') is-invalid @enderror"
                                                    id="new_password" name="new_password" required>
                                                @error('new_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="new_password_confirmation" class="form-label">Confirmar Nueva
                                                    Contraseña</label>
                                                <input type="password" class="form-control" id="new_password_confirmation"
                                                    name="new_password_confirmation" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i>
                                        Actualizar Contraseña
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
