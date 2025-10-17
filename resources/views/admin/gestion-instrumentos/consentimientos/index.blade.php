@extends('layouts.dashboard')

@section('title', 'Consentimientos')

@section('content')
    <div class="container-fluid py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.index') }}">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-briefcase"></i> Consentimientos
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Gestión de Consentimientos</h1>
                        <p class="text-muted">Administración de consentimientos informados</p>
                    </div>
                    <div>
                        <a href="{{ route('gestion-instrumentos.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Módulo
                        </a>
                        <a href="{{ route('gestion-instrumentos.consentimientos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nuevo Consentimiento
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de estado -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tabla de Consentimientos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-signature me-2"></i>Lista de Consentimientos
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">CONSENTIMIENTO</th>
                                <th class="px-4 py-3">DETALLES</th>
                                <th class="px-4 py-3">ÍTEMS</th>
                                <th class="px-4 py-3">CREADO</th>
                                <th class="px-4 py-3">MODIFICADO</th>
                                <th class="px-4 py-3">TIPO</th>
                                <th class="px-4 py-3">ESTADO</th>
                                <th class="px-4 py-3">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consentimientos as $consentimiento)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('gestion-instrumentos.consentimientos.show', $consentimiento->_id) }}"
                                            class="text-decoration-none fw-bold">
                                            {{ $consentimiento->titulo ?? 'Sin título' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ Str::limit($consentimiento->descripcion ?? 'Sin descripción', 50) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-info">
                                            {{ $consentimiento->items_total ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $consentimiento->fecha_creacion ? $consentimiento->fecha_creacion->format('d/m/Y H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $consentimiento->fecha_modificacion ? $consentimiento->fecha_modificacion->format('d/m/Y H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge {{ $consentimiento->tipo_color ?? 'bg-secondary' }}">
                                            {{ $consentimiento->tipo_texto ?? ($consentimiento->tipo ?? 'General') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form
                                            action="{{ route('gestion-instrumentos.consentimientos.toggle-estado', $consentimiento->_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $consentimiento->estado ? 'btn-success' : 'btn-outline-danger' }}">
                                                {{ $consentimiento->estado ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('gestion-instrumentos.consentimientos.show', $consentimiento->_id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('gestion-instrumentos.consentimientos.edit', $consentimiento->_id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('gestion-instrumentos.consentimientos.informes', $consentimiento->_id) }}"
                                                class="btn btn-sm btn-outline-info" title="Informes">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <form
                                                action="{{ route('gestion-instrumentos.consentimientos.destroy', $consentimiento->_id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de eliminar este consentimiento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-2">No hay consentimientos registrados</p>
                                        <a href="{{ route('gestion-instrumentos.consentimientos.create') }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-2"></i>Crear el primer consentimiento
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (isset($consentimientos) &&
                        $consentimientos instanceof \Illuminate\Contracts\Pagination\Paginator &&
                        method_exists($consentimientos, 'links') &&
                        $consentimientos->hasPages())
                    <div class="p-3">
                        {{ $consentimientos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
