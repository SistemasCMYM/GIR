@extends('layouts.dashboard')
@section('title', 'Administración de Usuarios')
@section('page-title', 'Administración de Usuarios')





@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">
                        <i class="fas fa-users-cog me-2"></i>
                        Administración de Usuarios
                    </h4>
                    <p class="text-muted mb-0">Gestione las cuentas de usuario del sistema con control total de acceso y
                        permisos</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Administración de Usuarios
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Silva Dashboard -->
        <div class="row mb-4 tw-gap-y-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div
                    class="card tw-flex tw-flex-col tw-justify-between tw-h-full tw-max-h-[220px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-border-0 tw-shadow-gir tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div class="card-body tw-flex tw-items-center tw-gap-4 tw-p-4 tw-h-full">
                        <div
                            class="flex-shrink-0 tw-relative tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-gir-primary-500/10 tw-flex tw-items-center tw-justify-center">
                            <span
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-11 tw-h-11 tw-rounded-xl tw-bg-gir-primary-500 tw-text-white">
                                <i class="fas fa-users tw-text-lg"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 tw-space-y-1">
                            <h4 class="mb-0 tw-text-2xl tw-font-semibold tw-text-gray-900">
                                {{ $estadisticas['total_usuarios'] ?? 3 }}
                            </h4>
                            <p class="text-muted mb-0 tw-text-sm tw-text-gray-500">Total Usuarios</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div
                    class="card tw-flex tw-flex-col tw-justify-between tw-h-full tw-max-h-[220px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-border-0 tw-shadow-gir tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div class="card-body tw-flex tw-items-center tw-gap-4 tw-p-4 tw-h-full">
                        <div
                            class="flex-shrink-0 tw-relative tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-gir-gold-500/10 tw-flex tw-items-center tw-justify-center">
                            <span
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-11 tw-h-11 tw-rounded-xl tw-bg-gir-gold-500 tw-text-gray-900">
                                <i class="fas fa-user-shield tw-text-lg"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 tw-space-y-1">
                            <h4 class="mb-0 tw-text-2xl tw-font-semibold tw-text-gray-900">
                                {{ $estadisticas['total_roles'] ?? 0 }}
                            </h4>
                            <p class="text-muted mb-0 tw-text-sm tw-text-gray-500">Roles Totales</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div
                    class="card tw-flex tw-flex-col tw-justify-between tw-h-full tw-max-h-[220px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-border-0 tw-shadow-gir tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div class="card-body tw-flex tw-items-center tw-gap-4 tw-p-4 tw-h-full">
                        <div
                            class="flex-shrink-0 tw-relative tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-gir-dark-gold-500/10 tw-flex tw-items-center tw-justify-center">
                            <span
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-11 tw-h-11 tw-rounded-xl tw-bg-gir-dark-gold-500 tw-text-white">
                                <i class="fas fa-key tw-text-lg"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 tw-space-y-1">
                            <h4 class="mb-0 tw-text-2xl tw-font-semibold tw-text-gray-900">
                                {{ $estadisticas['total_permisos'] ?? 8 }}
                            </h4>
                            <p class="text-muted mb-0 tw-text-sm tw-text-gray-500">Permisos</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div
                    class="card tw-flex tw-flex-col tw-justify-between tw-h-full tw-max-h-[220px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-border-0 tw-shadow-gir tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div class="card-body tw-flex tw-items-center tw-gap-4 tw-p-4 tw-h-full">
                        <div
                            class="flex-shrink-0 tw-relative tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-gir-primary-500/10 tw-flex tw-items-center tw-justify-center">
                            <span
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-11 tw-h-11 tw-rounded-xl tw-bg-success tw-text-white">
                                <i class="fas fa-clock tw-text-lg"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 tw-space-y-1">
                            <h4 class="mb-0 tw-text-2xl tw-font-semibold tw-text-gray-900">
                                {{ $estadisticas['sesiones_activas'] ?? 6 }}
                            </h4>
                            <p class="text-muted mb-0 tw-text-sm tw-text-gray-500">Roles Activos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Modules Silva Dashboard -->
        <div class="row mb-4 tw-gap-y-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div
                    class="card text-center tw-flex tw-flex-col tw-h-full tw-justify-between tw-max-h-[320px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-shadow-gir tw-border-0 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div
                        class="card-body tw-flex tw-flex-col tw-gap-4 tw-items-center tw-justify-between tw-px-5 tw-pt-6 tw-pb-5">
                        <div
                            class="tw-relative tw-w-20 tw-h-20 tw-rounded-3xl tw-bg-gir-primary-500/10 tw-flex tw-items-center tw-justify-center">
                            <div
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-gir-primary-500 tw-text-white">
                                <i class="fas fa-user-plus tw-text-2xl"></i>
                            </div>
                        </div>
                        <div class="tw-space-y-2">
                            <h5 class="mt-0 mb-0 tw-text-lg tw-font-semibold tw-text-gray-900">Creación de Cuentas</h5>
                            <p class="text-muted tw-mb-0 tw-text-sm tw-text-gray-500">Crear, editar, activar usuarios</p>
                        </div>
                        <a href="{{ route('usuarios.cuentas.index') }}" class="btn btn-primary tw-w-full tw-rounded-xl">
                            <i class="fas fa-arrow-right me-1"></i>
                            Acceder
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div
                    class="card text-center tw-flex tw-flex-col tw-h-full tw-justify-between tw-max-h-[320px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-shadow-gir tw-border-0 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div
                        class="card-body tw-flex tw-flex-col tw-gap-4 tw-items-center tw-justify-between tw-px-5 tw-pt-6 tw-pb-5">
                        <div
                            class="tw-relative tw-w-20 tw-h-20 tw-rounded-3xl tw-bg-gir-gold-500/10 tw-flex tw-items-center tw-justify-center">
                            <div
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-gir-gold-500 tw-text-gray-900">
                                <i class="fas fa-user-tag tw-text-2xl"></i>
                            </div>
                        </div>
                        <div class="tw-space-y-2">
                            <h5 class="mt-0 mb-0 tw-text-lg tw-font-semibold tw-text-gray-900">Perfiles</h5>
                            <p class="text-muted tw-mb-0 tw-text-sm tw-text-gray-500">Definición de perfiles y jerarquías
                            </p>
                        </div>
                        <a href="{{ route('usuarios.perfiles.index') }}" class="btn btn-secondary tw-w-full tw-rounded-xl">
                            <i class="fas fa-arrow-right me-1"></i>
                            Acceder
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div
                    class="card text-center tw-flex tw-flex-col tw-h-full tw-justify-between tw-max-h-[320px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-shadow-gir tw-border-0 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div
                        class="card-body tw-flex tw-flex-col tw-gap-4 tw-items-center tw-justify-between tw-px-5 tw-pt-6 tw-pb-5">
                        <div
                            class="tw-relative tw-w-20 tw-h-20 tw-rounded-3xl tw-bg-gir-dark-gold-500/10 tw-flex tw-items-center tw-justify-center">
                            <div
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-gir-dark-gold-500 tw-text-white">
                                <i class="fas fa-user-shield tw-text-2xl"></i>
                            </div>
                        </div>
                        <div class="tw-space-y-2">
                            <h5 class="mt-0 mb-0 tw-text-lg tw-font-semibold tw-text-gray-900">Roles</h5>
                            <p class="text-muted tw-mb-0 tw-text-sm tw-text-gray-500">Niveles de acceso y responsabilidades
                            </p>
                        </div>
                        <a href="{{ route('usuarios.roles.index') }}" class="btn btn-danger tw-w-full tw-rounded-xl">
                            <i class="fas fa-arrow-right me-1"></i>
                            Acceder
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div
                    class="card text-center tw-flex tw-flex-col tw-h-full tw-justify-between tw-max-h-[320px] smartwatch:tw-max-h-[280px] foldable:tw-max-h-[720px] 4k:tw-max-h-[3840px] tw-rounded-2xl tw-shadow-gir tw-border-0 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg hover:tw--translate-y-1">
                    <div
                        class="card-body tw-flex tw-flex-col tw-gap-4 tw-items-center tw-justify-between tw-px-5 tw-pt-6 tw-pb-5">
                        <div
                            class="tw-relative tw-w-20 tw-h-20 tw-rounded-3xl tw-bg-gir-gold-500/10 tw-flex tw-items-center tw-justify-center">
                            <div
                                class="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-warning tw-text-gray-900">
                                <i class="fas fa-key tw-text-2xl"></i>
                            </div>
                        </div>
                        <div class="tw-space-y-2">
                            <h5 class="mt-0 mb-0 tw-text-lg tw-font-semibold tw-text-gray-900">Permisos</h5>
                            <p class="text-muted tw-mb-0 tw-text-sm tw-text-gray-500">Permisos específicos por módulo</p>
                        </div>
                        <a href="{{ route('usuarios.permisos.index') }}" class="btn btn-warning tw-w-full tw-rounded-xl">
                            <i class="fas fa-arrow-right me-1"></i>
                            Acceder
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Silva Dashboard -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('usuarios.cuentas.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>
                                Crear Usuario
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('usuarios.roles.create') }}" class="btn btn-outline-danger">
                                <i class="fas fa-user-tag me-2"></i>
                                Crear Rol
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('usuarios.permisos.matrix') }}" class="btn btn-outline-warning">
                                <i class="fas fa-shield-alt me-2"></i>
                                Matriz Permisos
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-grid">
                            <a href="{{ route('usuarios.auditoria') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Auditoría
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
