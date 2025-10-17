@extends('layouts.dashboard')
@section('title', 'roles')
@section('title', 'Perfiles/Roles - Gestión Administrativa')

@section('content')
    <div class="tw-container-fluid tw-py-6">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4">
            <!-- Breadcrumb con Tailwind -->
            <nav aria-label="breadcrumb" class="tw-mb-6">
                <ol class="breadcrumb tw-bg-transparent tw-mb-0 tw-p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}"
                            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                            <i class="fas fa-home tw-mr-1"></i> Inicio
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('usuarios.index') }}"
                            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                            <i class="fas fa-users-cog tw-mr-1"></i> Administración de Usuarios
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-user-tag tw-mr-1"></i> Roles
                    </li>
                </ol>
            </nav>

            <!-- Header con Tailwind -->
            <div
                class="tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-primary-700 tw-rounded-2xl tw-p-6 tw-text-white tw-mb-6 tw-shadow-gir-lg">
                <h1 class="tw-text-3xl tw-font-bold tw-mb-2 tw-drop-shadow-md">
                    <i class="fas fa-user-tag tw-mr-3"></i> Perfiles/Roles
                </h1>
                <p class="tw-text-base tw-opacity-90 tw-mb-0">Gestión de roles y perfiles de usuario del sistema</p>
            </div>

            <!-- Métricas con Tailwind -->
            <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 tw-mb-6">
                <div
                    class="tw-bg-white tw-rounded-xl tw-p-6 tw-text-center tw-shadow-gir tw-border-l-4 tw-border-gir-primary-500 tw-transition-transform hover:tw-scale-105">
                    <i class="fas fa-layer-group tw-text-2xl tw-text-gir-primary-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-3xl tw-font-bold tw-text-gir-warm-gray-900 tw-mb-1">{{ $stats['total_roles'] ?? 0 }}</span>
                    <span class="tw-block tw-text-sm tw-text-gir-warm-gray-600 tw-uppercase tw-tracking-wide">Roles</span>
                </div>
                <div
                    class="tw-bg-white tw-rounded-xl tw-p-6 tw-text-center tw-shadow-gir tw-border-l-4 tw-border-green-500 tw-transition-transform hover:tw-scale-105">
                    <i class="fas fa-toggle-on tw-text-2xl tw-text-green-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-3xl tw-font-bold tw-text-gir-warm-gray-900 tw-mb-1">{{ $stats['roles_activos'] ?? 0 }}</span>
                    <span class="tw-block tw-text-sm tw-text-gir-warm-gray-600 tw-uppercase tw-tracking-wide">Activos</span>
                </div>
                <div
                    class="tw-bg-white tw-rounded-xl tw-p-6 tw-text-center tw-shadow-gir tw-border-l-4 tw-border-gir-gold-500 tw-transition-transform hover:tw-scale-105">
                    <i class="fas fa-key tw-text-2xl tw-text-gir-gold-600 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-3xl tw-font-bold tw-text-gir-warm-gray-900 tw-mb-1">{{ $stats['permisos_totales'] ?? 0 }}</span>
                    <span
                        class="tw-block tw-text-sm tw-text-gir-warm-gray-600 tw-uppercase tw-tracking-wide">Permisos</span>
                </div>
                <div
                    class="tw-bg-white tw-rounded-xl tw-p-6 tw-text-center tw-shadow-gir tw-border-l-4 tw-border-blue-500 tw-transition-transform hover:tw-scale-105">
                    <i class="fas fa-user-shield tw-text-2xl tw-text-blue-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-3xl tw-font-bold tw-text-gir-warm-gray-900 tw-mb-1">{{ $stats['asignaciones'] ?? 0 }}</span>
                    <span
                        class="tw-block tw-text-sm tw-text-gir-warm-gray-600 tw-uppercase tw-tracking-wide">Asignaciones</span>
                </div>
            </div>

            <!-- Jerarquía de Roles con Tailwind -->
            <div class="tw-mb-6">
                <h5 class="tw-text-xl tw-font-semibold tw-text-gir-warm-gray-900 tw-mb-4">
                    <i class="fas fa-sitemap tw-mr-2 tw-text-gir-primary-600"></i> Jerarquía de Roles
                </h5>
                <div class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-border tw-border-gir-warm-gray-200">
                    <div class="tw-p-6">
                        <ul class="tw-list-none tw-m-0 tw-p-0 tw-space-y-2">
                            <li class="tw-pl-0">
                                <strong class="tw-text-gir-warm-gray-900">Super Administrador</strong>
                                <small class="tw-text-gir-warm-gray-500 tw-ml-2">(Acceso total - tipo: interna)</small>
                                <ul class="tw-list-none tw-ml-6 tw-mt-2 tw-space-y-1">
                                    <li>
                                        <strong class="tw-text-gir-warm-gray-800">Administrador de Empresa</strong>
                                        <small class="tw-text-gir-warm-gray-500 tw-ml-2">(Gestión empresa - tipo:
                                            cliente)</small>
                                        <ul class="tw-list-none tw-ml-6 tw-mt-2 tw-space-y-1">
                                            <li class="tw-text-gir-warm-gray-700">
                                                <strong>Profesional/Psicólogo</strong>
                                                <small class="tw-text-gir-warm-gray-500 tw-ml-2">(Módulo psicosocial - tipo:
                                                    profesional)</small>
                                            </li>
                                            <li class="tw-text-gir-warm-gray-700">
                                                <strong>Técnico de Hallazgos</strong>
                                                <small class="tw-text-gir-warm-gray-500 tw-ml-2">(Módulo hallazgos - tipo:
                                                    usuario)</small>
                                            </li>
                                            <li class="tw-text-gir-warm-gray-700">
                                                <strong>Supervisor</strong>
                                                <small class="tw-text-gir-warm-gray-500 tw-ml-2">(Lectura ambos módulos -
                                                    tipo: usuario)</small>
                                            </li>
                                            <li class="tw-text-gir-warm-gray-700">
                                                <strong>Usuario Final</strong>
                                                <small class="tw-text-gir-warm-gray-500 tw-ml-2">(Acceso limitado - tipo:
                                                    usuario)</small>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Grid de Roles con Tailwind -->
            <div class="tw-grid tw-grid-cols-1 xl:tw-grid-cols-2 tw-gap-6">
                @forelse($roles as $rol)
                    <div
                        class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-border tw-border-gir-warm-gray-200 tw-transition-transform hover:tw-scale-105">
                        <div
                            class="tw-flex tw-justify-between tw-items-center tw-p-6 tw-border-b tw-border-gir-warm-gray-200">
                            <h6 class="tw-text-lg tw-font-semibold tw-text-gir-warm-gray-900 tw-mb-0">
                                <i class="fas fa-user-tag tw-mr-2 tw-text-gir-primary-600"></i> {{ $rol->nombre }}
                            </h6>
                            <span
                                class="tw-inline-flex tw-items-center tw-px-3 tw-py-1 tw-rounded-full tw-text-xs tw-font-medium {{ $rol->activo ? 'tw-bg-green-100 tw-text-green-800' : 'tw-bg-gray-100 tw-text-gray-800' }}">
                                {{ $rol->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <div class="tw-p-6">
                            <p class="tw-text-gir-warm-gray-600 tw-text-sm tw-mb-4">
                                {{ $rol->descripcion ?? 'Sin descripción' }}</p>

                            <div class="tw-mb-4">
                                <strong class="tw-text-gir-warm-gray-900">Tipo:</strong>
                                <span
                                    class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-blue-100 tw-text-blue-800 tw-ml-2">
                                    {{ ucfirst($rol->tipo) }}
                                </span>
                            </div>

                            @if ($rol->modulos && count($rol->modulos) > 0)
                                <div class="tw-mb-4">
                                    <strong class="tw-text-gir-warm-gray-900">Módulos:</strong>
                                    <div class="tw-mt-2 tw-flex tw-flex-wrap tw-gap-1">
                                        @foreach ($rol->modulos as $modulo)
                                            <span
                                                class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-warm-gray-100 tw-text-gir-warm-gray-800">
                                                {{ ucfirst($modulo) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($rol->permisos && count($rol->permisos) > 0)
                                <div class="tw-mb-4">
                                    <strong class="tw-text-gir-warm-gray-900">Permisos:</strong>
                                    <div class="tw-mt-2 tw-flex tw-flex-wrap tw-gap-1">
                                        @foreach (array_slice($rol->permisos, 0, 3) as $permiso)
                                            <span
                                                class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-gold-100 tw-text-gir-gold-800">
                                                {{ ucfirst($permiso) }}
                                            </span>
                                        @endforeach
                                        @if (count($rol->permisos) > 3)
                                            <span
                                                class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-warm-gray-200 tw-text-gir-warm-gray-700">
                                                +{{ count($rol->permisos) - 3 }} más
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="tw-mb-4">
                                <strong class="tw-text-gir-warm-gray-900">Usuarios asignados:</strong>
                                <span
                                    class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-warm-gray-900 tw-text-white tw-ml-2">
                                    {{ $rol->cantidad_usuarios ?? 0 }}
                                </span>
                            </div>

                            <div class="tw-flex tw-gap-2">
                                <a href="#"
                                    class="tw-inline-flex tw-items-center tw-px-3 tw-py-1.5 tw-border tw-border-blue-300 tw-text-blue-700 tw-bg-transparent tw-rounded-md tw-text-sm tw-font-medium hover:tw-bg-blue-50 tw-transition-colors">
                                    Ver
                                </a>
                                <a href="#"
                                    class="tw-inline-flex tw-items-center tw-px-3 tw-py-1.5 tw-border tw-border-gir-gold-300 tw-text-gir-gold-700 tw-bg-transparent tw-rounded-md tw-text-sm tw-font-medium hover:tw-bg-gir-gold-50 tw-transition-colors">
                                    Editar
                                </a>
                                @if ($rol->nombre !== 'SuperAdmin')
                                    <button
                                        class="tw-inline-flex tw-items-center tw-px-3 tw-py-1.5 tw-border tw-border-red-300 tw-text-red-700 tw-bg-transparent tw-rounded-md tw-text-sm tw-font-medium hover:tw-bg-red-50 tw-transition-colors">
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay roles configurados</h5>
                    <p class="text-muted">Los roles se crean automáticamente para cada empresa.</p>
                    <a href="{{ route('usuarios.roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Crear Nuevo Rol
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        @if (
            $roles instanceof \Illuminate\Contracts\Pagination\Paginator &&
                method_exists($roles, 'links') &&
                $roles->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $roles->links() }}
            </div>
        @endif
    @endsection
