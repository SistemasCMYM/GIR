@extends('layouts.dashboard')

@section('title', 'Encuestas')

@section('content')
    <div class="container-fluid py-4">

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                    <i class="fas fa-briefcase"></i> Encuestas
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Gestión de Encuestas</h1>
                        <p class="text-muted">Administración de encuestas personalizadas</p>
                    </div>
                    <div>
                        <a href="{{ route('gestion-instrumentos.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Módulo
                        </a>
                        <a href="{{ route('gestion-instrumentos.encuestas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Encuesta
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Encuestas -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-poll me-2"></i>Lista de Encuestas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">ENCUESTA</th>
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
                            @forelse($encuestas as $encuesta)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('gestion-instrumentos.encuestas.show', $encuesta->id) }}"
                                            class="text-decoration-none fw-bold">
                                            {{ $encuesta->titulo ?? ($encuesta->nombre ?? 'Sin nombre') }}
                                        </a>
                                        @if (!session('empresa_id'))
                                            <br><small class="text-muted">Empresa ID:
                                                {{ $encuesta->empresa_id ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ Str::limit($encuesta->descripcion ?? 'Sin descripción', 50) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-info">
                                            {{ count($encuesta->preguntas ?? []) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $encuesta->created_at ? $encuesta->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        {{ $encuesta->updated_at ? $encuesta->updated_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($encuesta->tipo ?? 'General') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form
                                            action="{{ route('gestion-instrumentos.encuestas.toggle-estado', $encuesta->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $encuesta->estado ?? false ? 'btn-success' : 'btn-outline-danger' }}">
                                                {{ $encuesta->estado ?? false ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('gestion-instrumentos.encuestas.show', $encuesta->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($encuesta->publicada ?? false)
                                                <a href="{{ route('gestion-instrumentos.encuestas.responder', $encuesta->id) }}"
                                                    class="btn btn-sm btn-outline-success" title="Responder">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('gestion-instrumentos.encuestas.edit', $encuesta->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route('gestion-instrumentos.encuestas.toggle-publicacion', $encuesta->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $encuesta->publicada ?? false ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                                                    title="Publicar">
                                                    <i
                                                        class="fas fa-{{ $encuesta->publicada ?? false ? 'globe' : 'eye-slash' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('gestion-instrumentos.encuestas.informes', $encuesta->id) }}"
                                                class="btn btn-sm btn-outline-info" title="Informes">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <form
                                                action="{{ route('gestion-instrumentos.encuestas.destroy', $encuesta->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta encuesta?')">
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
                                        <p class="mb-2">No hay encuestas registradas</p>
                                        <a href="{{ route('gestion-instrumentos.encuestas.create') }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-2"></i>Crear la primera encuesta
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (isset($encuestas) &&
                        $encuestas instanceof \Illuminate\Contracts\Pagination\Paginator &&
                        method_exists($encuestas, 'links') &&
                        $encuestas->hasPages())
                    <div class="p-3">
                        {{ $encuestas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
