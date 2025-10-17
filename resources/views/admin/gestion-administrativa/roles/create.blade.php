@extends('layouts.dashboard')

@section('title', 'Crear Rol | GIR-365')
@section('page-title', 'Gestión de Roles')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('usuarios.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Crear</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1 fw-bold text-primary">Nuevo rol del sistema</h4>
                        <p class="mb-0 text-muted">Define un rol corporativo con permisos y módulos específicos.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('usuarios.roles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.gestion-administrativa.roles._form', [
        'formAction' => route('usuarios.roles.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Guardar rol',
        'rolData' => $rolData ?? [],
        'opciones' => $opciones,
        'modo' => 'create',
    ])
@endsection
