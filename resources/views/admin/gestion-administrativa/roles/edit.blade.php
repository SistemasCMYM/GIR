@extends('layouts.dashboard')

@section('title', 'Editar Rol | GIR-365')
@section('page-title', 'Gestión de Roles')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('usuarios.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1 fw-bold text-primary">Editar rol</h4>
                        <p class="mb-0 text-muted">Actualiza la configuración y los permisos asignados al rol seleccionado.
                        </p>
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

    @php
        $rolIdentifier =
            $rolData['id'] ?? (isset($rol) && $rol instanceof \App\Models\Auth\Rol ? $rol->getKey() : null);
    @endphp

    @include('admin.gestion-administrativa.roles._form', [
        'formAction' => $rolIdentifier ? route('usuarios.roles.update', $rolIdentifier) : '#',
        'formMethod' => 'PUT',
        'submitLabel' => 'Actualizar rol',
        'rolData' => $rolData ?? [],
        'opciones' => $opciones,
        'modo' => 'edit',
    ])
@endsection
