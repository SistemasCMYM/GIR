@extends('layouts.dashboard')

@section('title', 'Gestión de Empleados - GIR365')

@section('page-title', 'Gestión de Empleados')

@section('content')
    {{-- GIR-365 Silva Dashboard - Empleados Module --}}
    {{-- Optimized for 4K Ultra HD & Ultra Responsive (280px - 3840px+) --}}
    <div class="tw-container-fluid tw-py-6">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4">
            <div class="container-fluid py-4 gir-override">

                <nav aria-label="breadcrumb" class="tw-mb-6">
                    <ol class="breadcrumb tw-bg-transparent tw-mb-0 tw-p-0 tw-gap-2">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}"
                                class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                                <i class="fas fa-home tw-mr-1"></i>Inicio
                            </a>
                        </li>
                        <li class="tw-text-gir-warm-gray-400">
                            <i class="fas fa-chevron-right tw-text-[10px]"></i>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('empresa.index') }}"
                                class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                                Administracion de Empresa
                            </a>
                        </li>
                        <li class="tw-text-gir-warm-gray-400">
                            <i class="fas fa-chevron-right tw-text-[10px]"></i>
                        </li>
                        <li class="breadcrumb-item active tw-text-gir-primary-600" aria-current="page">
                            Empleados
                        </li>
                    </ol>
                </nav>

                <div class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-upload me-2 tw-text-blue-600"></i>
                            Gestión de Empleados
                        </h4>
                        <p class="text-muted tw-text-sm">Importa y gestiona la información de los empleados de
                            la empresa de forma masiva o individual.</p>
                    </div>
                </div>
                {{-- Statistics Cards Grid --}}
                <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4 lg:tw-gap-6 tw-mb-6">
                    {{-- Card 1: Total Empleados --}}
                    <div class="tw-group tw-relative tw-overflow-hidden 
                                       tw-bg-white tw-rounded-xl lg:tw-rounded-2xl 4k:tw-rounded-3xl 
                                       tw-p-5 sm:tw-p-6 4k:tw-p-10 
                                       tw-border tw-border-gir-warm-gray-100 
                                       tw-shadow-lg hover:tw-shadow-2xl 
                                       tw-transition-all tw-duration-500 
                                       hover:tw--translate-y-1 hover:tw-scale-[1.02] 
                                       tw-cursor-pointer
                                       tw-animate-fade-in"
                        style="animation-delay: 0.1s">
                        <div
                            class="tw-absolute tw-left-0 tw-top-0 tw-bottom-0 tw-w-1 
                                           tw-bg-gradient-to-b tw-from-gir-primary-500 tw-to-gir-gold-500 
                                           group-hover:tw-w-1.5 tw-transition-all tw-duration-300">
                        </div>

                        <div
                            class="tw-absolute tw-inset-0 tw-bg-gradient-to-br tw-from-gir-primary-50/0 tw-to-gir-gold-50/0 
                                           group-hover:tw-from-gir-primary-50/50 group-hover:tw-to-gir-gold-50/50 
                                           tw-transition-all tw-duration-500">
                        </div>

                        <div class="tw-relative tw-z-10 tw-flex tw-items-start tw-justify-between">
                            <div class="tw-flex-1">
                                <div
                                    class="tw-text-gir-warm-gray-500 group-hover:tw-text-gir-primary-600 
                                                   tw-text-xs sm:tw-text-sm 4k:tw-text-base 
                                                   tw-font-semibold tw-uppercase tw-tracking-wide tw-mb-2 sm:tw-mb-3 
                                                   tw-transition-colors tw-duration-300">
                                    Total Empleados
                                </div>
                                <div
                                    class="tw-text-gir-warm-gray-900 
                                                   tw-text-3xl sm:tw-text-4xl lg:tw-text-5xl 4k:tw-text-7xl 
                                                   tw-font-bold tw-leading-none tw-mb-1">
                                    {{ $estadisticas['total'] ?? 0 }}
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-1.5 tw-mt-2 sm:tw-mt-3">
                                    <span
                                        class="tw-inline-flex tw-items-center tw-gap-1 
                                                        tw-px-2 tw-py-0.5 
                                                        tw-rounded-full 
                                                        tw-bg-gir-primary-100 tw-text-gir-primary-700 
                                                        tw-text-[10px] sm:tw-text-xs 4k:tw-text-sm 
                                                        tw-font-medium">
                                        <i class="fas fa-users tw-text-[8px]"></i>
                                        100%
                                    </span>
                                </div>
                            </div>

                            <div
                                class="tw-flex-shrink-0 
                                               tw-w-12 sm:tw-w-14 lg:tw-w-16 4k:tw-w-24 
                                               tw-h-12 sm:tw-h-14 lg:tw-h-16 4k:tw-h-24 
                                               tw-bg-gradient-to-br tw-from-gir-primary-100 tw-to-gir-gold-100 
                                               group-hover:tw-from-gir-primary-200 group-hover:tw-to-gir-gold-200 
                                               tw-rounded-xl lg:tw-rounded-2xl 
                                               tw-flex tw-items-center tw-justify-center 
                                               tw-transition-all tw-duration-500 
                                               group-hover:tw-rotate-6 group-hover:tw-scale-110 
                                               tw-shadow-lg group-hover:tw-shadow-xl">
                                <i
                                    class="fas fa-users 
                                                 tw-text-gir-primary-600 
                                                 tw-text-lg sm:tw-text-xl lg:tw-text-2xl 4k:tw-text-4xl"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Empleados Activos --}}
                    <div class="tw-group tw-relative tw-overflow-hidden 
                                       tw-bg-white tw-rounded-xl lg:tw-rounded-2xl 4k:tw-rounded-3xl 
                                       tw-p-5 sm:tw-p-6 4k:tw-p-10 
                                       tw-border tw-border-gir-warm-gray-100 
                                       tw-shadow-lg hover:tw-shadow-2xl 
                                       tw-transition-all tw-duration-500 
                                       hover:tw--translate-y-1 hover:tw-scale-[1.02] 
                                       tw-cursor-pointer
                                       tw-animate-fade-in"
                        style="animation-delay: 0.2s">
                        <div
                            class="tw-absolute tw-left-0 tw-top-0 tw-bottom-0 tw-w-1 
                                           tw-bg-gradient-to-b tw-from-green-500 tw-to-green-600 
                                           group-hover:tw-w-1.5 tw-transition-all tw-duration-300">
                        </div>

                        <div
                            class="tw-absolute tw-inset-0 tw-bg-gradient-to-br tw-from-green-50/0 tw-to-green-50/0 
                                           group-hover:tw-from-green-50/50 group-hover:tw-to-green-100/50 
                                           tw-transition-all tw-duration-500">
                        </div>

                        <div class="tw-relative tw-z-10 tw-flex tw-items-start tw-justify-between">
                            <div class="tw-flex-1">
                                <div
                                    class="tw-text-gir-warm-gray-500 group-hover:tw-text-green-600 
                                                   tw-text-xs sm:tw-text-sm 4k:tw-text-base 
                                                   tw-font-semibold tw-uppercase tw-tracking-wide tw-mb-2 sm:tw-mb-3 
                                                   tw-transition-colors tw-duration-300">
                                    Activos
                                </div>
                                <div
                                    class="tw-text-gir-warm-gray-900 
                                                   tw-text-3xl sm:tw-text-4xl lg:tw-text-5xl 4k:tw-text-7xl 
                                                   tw-font-bold tw-leading-none tw-mb-1">
                                    {{ $estadisticas['activos'] ?? 0 }}
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-1.5 tw-mt-2 sm:tw-mt-3">
                                    <span
                                        class="tw-inline-flex tw-items-center tw-gap-1 
                                                        tw-px-2 tw-py-0.5 
                                                        tw-rounded-full 
                                                        tw-bg-green-100 tw-text-green-700 
                                                        tw-text-[10px] sm:tw-text-xs 4k:tw-text-sm 
                                                        tw-font-medium">
                                        <i class="fas fa-arrow-up tw-text-[8px]"></i>
                                        {{ $estadisticas['porcentaje_activos'] ?? 0 }}%
                                    </span>
                                </div>
                            </div>

                            <div
                                class="tw-flex-shrink-0 
                                               tw-w-12 sm:tw-w-14 lg:tw-w-16 4k:tw-w-24 
                                               tw-h-12 sm:tw-h-14 lg:tw-h-16 4k:tw-h-24 
                                               tw-bg-gradient-to-br tw-from-green-100 tw-to-green-200 
                                               group-hover:tw-from-green-200 group-hover:tw-to-green-300 
                                               tw-rounded-xl lg:tw-rounded-2xl 
                                               tw-flex tw-items-center tw-justify-center 
                                               tw-transition-all tw-duration-500 
                                               group-hover:tw-rotate-6 group-hover:tw-scale-110 
                                               tw-shadow-lg group-hover:tw-shadow-xl">
                                <i
                                    class="fas fa-user-check 
                                                 tw-text-green-600 
                                                 tw-text-lg sm:tw-text-xl lg:tw-text-2xl 4k:tw-text-4xl"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Empleados Inactivos --}}
                    <div class="tw-group tw-relative tw-overflow-hidden 
                           tw-bg-white tw-rounded-xl lg:tw-rounded-2xl 4k:tw-rounded-3xl 
                           tw-p-5 sm:tw-p-6 4k:tw-p-10 
                           tw-border tw-border-gir-warm-gray-100 
                           tw-shadow-lg hover:tw-shadow-2xl 
                           tw-transition-all tw-duration-500 
                           hover:tw--translate-y-1 hover:tw-scale-[1.02] 
                           tw-cursor-pointer
                           tw-animate-fade-in"
                        style="animation-delay: 0.3s">
                        <div
                            class="tw-absolute tw-left-0 tw-top-0 tw-bottom-0 tw-w-1 
                               tw-bg-gradient-to-b tw-from-amber-500 tw-to-amber-600 
                               group-hover:tw-w-1.5 tw-transition-all tw-duration-300">
                        </div>

                        <div
                            class="tw-absolute tw-inset-0 tw-bg-gradient-to-br tw-from-amber-50/0 tw-to-amber-50/0 
                               group-hover:tw-from-amber-50/50 group-hover:tw-to-amber-100/50 
                               tw-transition-all tw-duration-500">
                        </div>

                        <div class="tw-relative tw-z-10 tw-flex tw-items-start tw-justify-between">
                            <div class="tw-flex-1">
                                <div
                                    class="tw-text-gir-warm-gray-500 group-hover:tw-text-amber-600 
                                       tw-text-xs sm:tw-text-sm 4k:tw-text-base 
                                       tw-font-semibold tw-uppercase tw-tracking-wide tw-mb-2 sm:tw-mb-3 
                                       tw-transition-colors tw-duration-300">
                                    Inactivos
                                </div>
                                <div
                                    class="tw-text-gir-warm-gray-900 
                                       tw-text-3xl sm:tw-text-4xl lg:tw-text-5xl 4k:tw-text-7xl 
                                       tw-font-bold tw-leading-none tw-mb-1">
                                    {{ $estadisticas['inactivos'] ?? 0 }}
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-1.5 tw-mt-2 sm:tw-mt-3">
                                    <span
                                        class="tw-inline-flex tw-items-center tw-gap-1 
                                            tw-px-2 tw-py-0.5 
                                            tw-rounded-full 
                                            tw-bg-amber-100 tw-text-amber-700 
                                            tw-text-[10px] sm:tw-text-xs 4k:tw-text-sm 
                                            tw-font-medium">
                                        <i class="fas fa-minus tw-text-[8px]"></i>
                                        {{ 100 - ($estadisticas['porcentaje_activos'] ?? 0) }}%
                                    </span>
                                </div>
                            </div>

                            <div
                                class="tw-flex-shrink-0 
                                   tw-w-12 sm:tw-w-14 lg:tw-w-16 4k:tw-w-24 
                                   tw-h-12 sm:tw-h-14 lg:tw-h-16 4k:tw-h-24 
                                   tw-bg-gradient-to-br tw-from-amber-100 tw-to-amber-200 
                                   group-hover:tw-from-amber-200 group-hover:tw-to-amber-300 
                                   tw-rounded-xl lg:tw-rounded-2xl 
                                   tw-flex tw-items-center tw-justify-center 
                                   tw-transition-all tw-duration-500 
                                   group-hover:tw-rotate-6 group-hover:tw-scale-110 
                                   tw-shadow-lg group-hover:tw-shadow-xl">
                                <i
                                    class="fas fa-user-times 
                                     tw-text-amber-600 
                                     tw-text-lg sm:tw-text-xl lg:tw-text-2xl 4k:tw-text-4xl"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Tasa de Actividad --}}
                    <div class="tw-group tw-relative tw-overflow-hidden 
                           tw-bg-white tw-rounded-xl lg:tw-rounded-2xl 4k:tw-rounded-3xl 
                           tw-p-5 sm:tw-p-6 4k:tw-p-10 
                           tw-border tw-border-gir-warm-gray-100 
                           tw-shadow-lg hover:tw-shadow-2xl 
                           tw-transition-all tw-duration-500 
                           hover:tw--translate-y-1 hover:tw-scale-[1.02] 
                           tw-cursor-pointer
                           tw-animate-fade-in"
                        style="animation-delay: 0.4s">
                        <div
                            class="tw-absolute tw-left-0 tw-top-0 tw-bottom-0 tw-w-1 
                               tw-bg-gradient-to-b tw-from-blue-500 tw-to-blue-600 
                               group-hover:tw-w-1.5 tw-transition-all tw-duration-300">
                        </div>

                        <div
                            class="tw-absolute tw-inset-0 tw-bg-gradient-to-br tw-from-blue-50/0 tw-to-blue-50/0 
                               group-hover:tw-from-blue-50/50 group-hover:tw-to-blue-100/50 
                               tw-transition-all tw-duration-500">
                        </div>

                        <div class="tw-relative tw-z-10 tw-flex tw-items-start tw-justify-between">
                            <div class="tw-flex-1">
                                <div
                                    class="tw-text-gir-warm-gray-500 group-hover:tw-text-blue-600 
                                       tw-text-xs sm:tw-text-sm 4k:tw-text-base 
                                       tw-font-semibold tw-uppercase tw-tracking-wide tw-mb-2 sm:tw-mb-3 
                                       tw-transition-colors tw-duration-300">
                                    Tasa Activos
                                </div>
                                <div
                                    class="tw-text-gir-warm-gray-900 
                                       tw-text-3xl sm:tw-text-4xl lg:tw-text-5xl 4k:tw-text-7xl 
                                       tw-font-bold tw-leading-none tw-mb-1">
                                    {{ $estadisticas['porcentaje_activos'] ?? 0 }}<span
                                        class="tw-text-2xl sm:tw-text-3xl lg:tw-text-4xl 4k:tw-text-6xl">%</span>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-1.5 tw-mt-2 sm:tw-mt-3">
                                    <div class="tw-w-full tw-bg-gir-warm-gray-200 tw-rounded-full tw-h-2 4k:tw-h-3">
                                        <div class="tw-bg-gradient-to-r tw-from-blue-500 tw-to-blue-600 tw-h-full tw-rounded-full tw-transition-all tw-duration-1000"
                                            style="width: {{ $estadisticas['porcentaje_activos'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="tw-flex-shrink-0 
                                   tw-w-12 sm:tw-w-14 lg:tw-w-16 4k:tw-w-24 
                                   tw-h-12 sm:tw-h-14 lg:tw-h-16 4k:tw-h-24 
                                   tw-bg-gradient-to-br tw-from-blue-100 tw-to-blue-200 
                                   group-hover:tw-from-blue-200 group-hover:tw-to-blue-300 
                                   tw-rounded-xl lg:tw-rounded-2xl 
                                   tw-flex tw-items-center tw-justify-center 
                                   tw-transition-all tw-duration-500 
                                   group-hover:tw-rotate-6 group-hover:tw-scale-110 
                                   tw-shadow-lg group-hover:tw-shadow-xl">
                                <i
                                    class="fas fa-chart-pie 
                                     tw-text-blue-600 
                                     tw-text-lg sm:tw-text-xl lg:tw-text-2xl 4k:tw-text-4xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions - Desktop/Tablet --}}
                <div class="tw-hidden sm:tw-flex tw-gap-2 lg:tw-gap-3 tw-mb-6">


                </div>



                {{-- DataTable Silva Style - Responsive --}}
                <div class="card">
                    <div class="card-body">
                        <div
                            class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center sm:tw-justify-between tw-mb-4 tw-gap-3">
                            {{-- Title Section --}}
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <i class="fas fa-users tw-text-gray-700"> - </i>
                                <h4 class="tw-text-base tw-font-semibold tw-text-black tw-mb-0">
                                    Lista de Empleados
                                    <span class="tw-text-sm tw-font-normal tw-text-gray-500 tw-ml-2">
                                        @if (request()->hasAny([
                                                'search',
                                                'numero_documento',
                                                'email',
                                                'cargo',
                                                'genero',
                                                'tipo_cargo',
                                                'psicosocial_tipo',
                                                'area_id',
                                                'centro_id',
                                                'proceso_id',
                                                'ciudad',
                                                'estado',
                                            ]))
                                            ({{ $empleadosData->total() }} filtrados de {{ $estadisticas['total'] ?? 0 }})
                                        @else
                                            ({{ $estadisticas['total'] ?? 0 }})
                                        @endif
                                    </span>
                                </h4>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="tw-flex tw-flex-wrap tw-gap-2">
                                <button type="button"
                                    class="tw-inline-flex tw-items-center tw-gap-2 
                                     tw-px-5 lg:tw-px-6 4k:tw-px-10 
                                     tw-py-2.5 lg:tw-py-3 4k:tw-py-5 
                                     tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 
                                     hover:tw-from-gir-primary-600 hover:tw-to-gir-gold-600 
                                     tw-text-white tw-font-semibold 
                                     tw-text-sm 4k:tw-text-lg
                                     tw-rounded-xl lg:tw-rounded-2xl 
                                     tw-shadow-xl hover:tw-shadow-2xl 
                                     tw-transition-all tw-duration-300 
                                     hover:tw-scale-105 hover:tw--translate-y-0.5 btn-sm btn-primary"
                                    data-bs-toggle="modal" data-bs-target="#crearEmpleadoModal">
                                    <i class="fas fa-plus"></i>
                                    <span>Nuevo Empleado</span>
                                </button>

                                <button type="button"
                                    class="tw-inline-flex tw-items-center tw-gap-2 
                                     tw-px-5 lg:tw-px-6 4k:tw-px-10 
                                     tw-py-2.5 lg:tw-py-3 4k:tw-py-5 
                                     tw-bg-gradient-to-r tw-from-blue-500 tw-to-blue-600 
                                     hover:tw-from-blue-600 hover:tw-to-blue-700 
                                     tw-text-white tw-font-semibold 
                                     tw-text-sm 4k:tw-text-lg
                                     tw-rounded-xl lg:tw-rounded-2xl 
                                     tw-shadow-xl hover:tw-shadow-2xl 
                                     tw-transition-all tw-duration-300 
                                     hover:tw-scale-105 hover:tw--translate-y-0.5 btn btn-sm btn-info"
                                    data-bs-toggle="modal" data-bs-target="#cargaMasivaModal">
                                    <i class="fas fa-upload me-1"></i>
                                    <span>Carga Masiva</span>
                                </button>

                                <button type="button"
                                    class="tw-inline-flex tw-items-center tw-gap-2 
                                     tw-px-5 lg:tw-px-6 4k:tw-px-10 
                                     tw-py-2.5 lg:tw-py-3 4k:tw-py-5 
                                     tw-bg-gradient-to-r tw-from-blue-500 tw-to-blue-600 
                                     hover:tw-from-blue-600 hover:tw-to-blue-700 
                                     tw-text-white tw-font-semibold 
                                     tw-text-sm 4k:tw-text-lg
                                     tw-rounded-xl lg:tw-rounded-2xl 
                                     tw-shadow-xl hover:tw-shadow-2xl 
                                     tw-transition-all tw-duration-300 
                                     hover:tw-scale-105 hover:tw--translate-y-0.5 btn btn-sm btn-success"
                                    id="exportExcel">
                                    <i class="fas fa-file-excel me-1"></i>
                                    <span>Exportar </span>
                                </button><br>
                                <button type="button"
                                    class="tw-inline-flex tw-items-center tw-gap-2 
                                          tw-px-4 lg:tw-px-5 4k:tw-px-8 
                                          tw-py-2.5 lg:tw-py-3 4k:tw-py-5 
                                          tw-bg-white/90 hover:tw-bg-white 
                                          tw-backdrop-blur-md 
                                          tw-text-gir-primary-600 tw-font-medium 
                                          tw-text-sm 4k:tw-text-lg
                                          tw-rounded-xl lg:tw-rounded-2xl 
                                          tw-border-2 tw-border-gir-primary-500/30 
                                          tw-transition-all tw-duration-300 
                                          tw-shadow-lg hover:tw-shadow-xl hover:tw-scale-105"
                                    data-bs-toggle="modal" data-bs-target="#filtrosModal">
                                    <i class="fas fa-filter"></i>
                                    <span>Filtros</span>
                                </button>


                            </div>
                        </div>

                        {{-- Filtros Activos --}}
                        @if (request()->hasAny([
                                'search',
                                'numero_documento',
                                'email',
                                'cargo',
                                'genero',
                                'tipo_cargo',
                                'psicosocial_tipo',
                                'area_id',
                                'centro_id',
                                'proceso_id',
                                'ciudad',
                                'estado',
                            ]))
                            <div class="alert alert-info d-flex align-items-center justify-content-between py-2 px-3 mb-3"
                                style="font-size: 12px; border-radius: 8px;">
                                <div>
                                    <i class="fas fa-filter me-2"></i>
                                    <strong>Filtros activos:</strong>
                                    @if (request('search'))
                                        <span class="badge bg-primary ms-1">Búsqueda: {{ request('search') }}</span>
                                    @endif
                                    @if (request('numero_documento'))
                                        <span class="badge bg-primary ms-1">DNI: {{ request('numero_documento') }}</span>
                                    @endif
                                    @if (request('email'))
                                        <span class="badge bg-primary ms-1">Email: {{ request('email') }}</span>
                                    @endif
                                    @if (request('cargo'))
                                        <span class="badge bg-primary ms-1">Cargo: {{ request('cargo') }}</span>
                                    @endif
                                    @if (request('genero'))
                                        <span class="badge bg-primary ms-1">Género:
                                            {{ ucfirst(request('genero')) }}</span>
                                    @endif
                                    @if (request('tipo_cargo'))
                                        <span class="badge bg-primary ms-1">Tipo:
                                            {{ strtoupper(request('tipo_cargo')) }}</span>
                                    @endif
                                    @if (request('psicosocial_tipo'))
                                        <span class="badge bg-primary ms-1">Prueba:
                                            {{ request('psicosocial_tipo') }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('empresa.empleados.index') }}" class="btn btn-sm btn-outline-danger"
                                    style="font-size: 11px; padding: 3px 10px;">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        @endif

                        {{-- DataTable --}}
                        <div class="table-responsive">
                            <table id="empleadosTable" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>DNI</th>
                                        <th>Primer Nombre</th>
                                        <th>Segundo Nombre</th>
                                        <th>Primer Apellido</th>
                                        <th>Segundo Apellido</th>
                                        <th>Género</th>
                                        <th>Email</th>
                                        <th>Cargo</th>
                                        <th>Tipo Cargo</th>
                                        <th>Área</th>
                                        <th>Proceso</th>
                                        <th>Sede</th>
                                        <th>Ciudad</th>
                                        <th>Tipo Prueba</th>
                                        <th style="text-align: center;">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($empleados as $empleado)
                                        <tr>
                                            {{-- DNI --}}
                                            <td style="color: #000; font-weight: 600; font-size: 12px;">
                                                {{ $empleado['dni'] ?? 'N/A' }}
                                            </td>

                                            {{-- Primer Nombre --}}
                                            <td style="color: #000; font-size: 12px;">
                                                {{ $empleado['primerNombre'] ?? 'N/A' }}
                                            </td>

                                            {{-- Segundo Nombre --}}
                                            <td style="color: #6c757d; font-size: 12px;">
                                                {{ $empleado['segundoNombre'] ?? '-' }}
                                            </td>

                                            {{-- Primer Apellido --}}
                                            <td style="color: #000; font-size: 12px;">
                                                {{ $empleado['primerApellido'] ?? 'N/A' }}
                                            </td>

                                            {{-- Segundo Apellido --}}
                                            <td style="color: #6c757d; font-size: 12px;">
                                                {{ $empleado['segundoApellido'] ?? '-' }}
                                            </td>

                                            {{-- Género --}}
                                            <td>
                                                @if (isset($empleado['genero']))
                                                    @if ($empleado['genero'] === 'masculino')
                                                        <span class="badge"
                                                            style="background-color: #cfe2ff; color: #084298; font-size: 11px;">
                                                            <i class="fas fa-mars"></i> Masculino
                                                        </span>
                                                    @elseif($empleado['genero'] === 'femenino')
                                                        <span class="badge"
                                                            style="background-color: #f8d7da; color: #842029; font-size: 11px;">
                                                            <i class="fas fa-venus"></i> Femenino
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary" style="font-size: 11px;">
                                                            <i class="fas fa-genderless"></i>
                                                            {{ ucfirst($empleado['genero']) }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span style="color: #6c757d; font-size: 11px;">-</span>
                                                @endif
                                            </td>

                                            {{-- Email --}}
                                            <td>
                                                <a href="mailto:{{ $empleado['email'] ?? '' }}"
                                                    style="color: #0d6efd; text-decoration: none; font-size: 12px;">
                                                    {{ $empleado['email'] ?? 'Sin email' }}
                                                </a>
                                            </td>

                                            {{-- Cargo --}}
                                            <td style="color: #000; font-size: 12px;">
                                                {{ $empleado['cargo'] ?? 'Sin cargo' }}
                                            </td>

                                            {{-- Tipo Cargo --}}
                                            <td>
                                                @if (isset($empleado['tipo_cargo']))
                                                    @if ($empleado['tipo_cargo'] === 'gerencial')
                                                        <span class="badge"
                                                            style="background-color: #e2d9f3; color: #59316b; font-size: 10px;">GERENCIAL</span>
                                                    @elseif($empleado['tipo_cargo'] === 'profesional')
                                                        <span class="badge"
                                                            style="background-color: #cfe2ff; color: #084298; font-size: 10px;">PROFESIONAL</span>
                                                    @elseif($empleado['tipo_cargo'] === 'tecnico')
                                                        <span class="badge"
                                                            style="background-color: #d1ecf1; color: #0c5460; font-size: 10px;">TÉCNICO</span>
                                                    @elseif($empleado['tipo_cargo'] === 'auxiliar')
                                                        <span class="badge"
                                                            style="background-color: #d1e7dd; color: #0f5132; font-size: 10px;">AUXILIAR</span>
                                                    @else
                                                        <span class="badge bg-secondary"
                                                            style="font-size: 10px;">{{ strtoupper($empleado['tipo_cargo']) }}</span>
                                                    @endif
                                                @else
                                                    <span style="color: #6c757d; font-size: 11px;">-</span>
                                                @endif
                                            </td>

                                            {{-- Área --}}
                                            <td>
                                                @if (isset($empleado['area_label']))
                                                    <span class="badge"
                                                        style="background-color: #cfe2ff; color: #084298; font-size: 10px;">
                                                        <i class="fas fa-sitemap"></i> {{ $empleado['area_label'] }}
                                                    </span>
                                                @else
                                                    <span style="color: #6c757d; font-size: 11px;">Sin área</span>
                                                @endif
                                            </td>

                                            {{-- Proceso --}}
                                            <td>
                                                @if (isset($empleado['proceso_label']))
                                                    <span class="badge"
                                                        style="background-color: #e0cffc; color: #3d0a91; font-size: 10px;">
                                                        <i class="fas fa-project-diagram"></i>
                                                        {{ $empleado['proceso_label'] }}
                                                    </span>
                                                @else
                                                    <span style="color: #6c757d; font-size: 11px;">Sin proceso</span>
                                                @endif
                                            </td>

                                            {{-- Sede (Centro) --}}
                                            <td>
                                                @if (isset($empleado['centro_label']) || isset($empleado['sede_label']))
                                                    <span class="badge"
                                                        style="background-color: #e2d9f3; color: #59316b; font-size: 10px;">
                                                        <i class="fas fa-building"></i>
                                                        {{ $empleado['centro_label'] ?? ($empleado['sede_label'] ?? 'Sin sede') }}
                                                    </span>
                                                @else
                                                    <span style="color: #6c757d; font-size: 11px;">Sin sede</span>
                                                @endif
                                            </td>

                                            {{-- Ciudad --}}
                                            <td style="color: #000; font-size: 12px;">
                                                {{ $empleado['ciudad'] ?? 'N/A' }}
                                            </td>

                                            {{-- Tipo de Prueba --}}
                                            <td>
                                                @if (isset($empleado['psicosocial_tipo']))
                                                    @if ($empleado['psicosocial_tipo'] === 'A')
                                                        <span class="badge rounded-circle"
                                                            style="background-color: #d1e7dd; color: #0f5132; font-size: 11px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">A</span>
                                                    @else
                                                        <span class="badge rounded-circle"
                                                            style="background-color: #fff3cd; color: #997404; font-size: 11px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">B</span>
                                                    @endif
                                                @else
                                                    <span style="color: #6c757d; font-size: 11px;">-</span>
                                                @endif
                                            </td>

                                            {{-- Acciones --}}
                                            <td style="text-align: center;">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-info"
                                                        onclick="verEmpleado('{{ $empleado['_id'] }}')"
                                                        title="Ver detalles" style="padding: 4px 8px;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning"
                                                        onclick="editarEmpleado('{{ $empleado['_id'] }}')" title="Editar"
                                                        style="padding: 4px 8px;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="eliminarEmpleado('{{ $empleado['_id'] }}', '{{ $empleado['primerNombre'] }} {{ $empleado['primerApellido'] }}')"
                                                        title="Eliminar" style="padding: 4px 8px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" style="text-align: center; padding: 30px; color: #6c757d;">
                                                <i class="fas fa-users"
                                                    style="font-size: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                                                <p style="margin: 0; font-size: 14px;">No hay empleados registrados</p>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    style="margin-top: 15px;" data-bs-toggle="modal"
                                                    data-bs-target="#crearEmpleadoModal">
                                                    <i class="fas fa-plus-circle"></i> Crear Primer Empleado
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>{{-- Fin table-responsive --}}
                    </div>{{-- Fin card-body --}}

                    {{-- Compact Pagination --}}
                    @if (isset($empleadosData) && $empleadosData->hasPages())
                        <div class="tw-px-4 tw-py-2 tw-border-t tw-border-gray-200 tw-bg-gray-50">
                            <div class="tw-flex tw-items-center tw-justify-between tw-text-xs tw-text-gray-600">
                                <span>
                                    Mostrando <strong class="tw-text-black">{{ $empleadosData->firstItem() }}</strong> a
                                    <strong class="tw-text-black">{{ $empleadosData->lastItem() }}</strong> de
                                    <strong class="tw-text-black">{{ $empleadosData->total() }}</strong> registros
                                </span>

                                {{-- Links de paginación --}}
                                <div>
                                    {{ $empleadosData->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Modal: Filtros Avanzados --}}
            <div class="modal fade" id="filtrosModal" tabindex="-1" aria-labelledby="filtrosModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 700px;">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-gradient-to-r text-white border-0"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 12px 12px 0 0; padding: 12px 20px;">
                            <h5 class="modal-title fw-bold" id="filtrosModalLabel" style="font-size: 15px;">
                                <i class="fas fa-filter me-2" style="font-size: 13px;"></i>
                                Filtros de Búsqueda
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar" style="font-size: 12px;"></button>
                        </div>
                        <div class="modal-body" style="padding: 16px 20px;">
                            <form id="filtrosForm" method="GET" action="{{ route('empresa.empleados.index') }}">
                                <div class="row g-2">
                                    {{-- Búsqueda General --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-search text-primary me-1" style="font-size: 11px;"></i>
                                            Búsqueda General
                                        </label>
                                        <input type="text" name="search"
                                            class="form-control form-control-sm shadow-sm"
                                            placeholder="Nombre, DNI, email, cargo..." value="{{ request('search') }}"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                    </div>

                                    {{-- DNI --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-id-card text-info me-1" style="font-size: 11px;"></i>
                                            DNI
                                        </label>
                                        <input type="text" name="numero_documento"
                                            class="form-control form-control-sm shadow-sm" placeholder="Número documento"
                                            value="{{ request('numero_documento') }}"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-envelope text-danger me-1" style="font-size: 11px;"></i>
                                            Email
                                        </label>
                                        <input type="email" name="email"
                                            class="form-control form-control-sm shadow-sm"
                                            placeholder="Correo electrónico" value="{{ request('email') }}"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                    </div>

                                    {{-- Cargo --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-briefcase text-success me-1" style="font-size: 11px;"></i>
                                            Cargo
                                        </label>
                                        <input type="text" name="cargo"
                                            class="form-control form-control-sm shadow-sm" placeholder="Nombre del cargo"
                                            value="{{ request('cargo') }}"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                    </div>

                                    {{-- Género --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-venus-mars text-warning me-1" style="font-size: 11px;"></i>
                                            Género
                                        </label>
                                        <select name="genero" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todos</option>
                                            <option value="masculino"
                                                {{ request('genero') == 'masculino' ? 'selected' : '' }}>Masculino
                                            </option>
                                            <option value="femenino"
                                                {{ request('genero') == 'femenino' ? 'selected' : '' }}>Femenino
                                            </option>
                                            <option value="otro" {{ request('genero') == 'otro' ? 'selected' : '' }}>
                                                Otro</option>
                                        </select>
                                    </div>

                                    {{-- Tipo de Cargo --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-user-tag text-primary me-1" style="font-size: 11px;"></i>
                                            Tipo de Cargo
                                        </label>
                                        <select name="tipo_cargo" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todos</option>
                                            <option value="gerencial"
                                                {{ request('tipo_cargo') == 'gerencial' ? 'selected' : '' }}>Gerencial
                                            </option>
                                            <option value="profesional"
                                                {{ request('tipo_cargo') == 'profesional' ? 'selected' : '' }}>
                                                Profesional</option>
                                            <option value="tecnico"
                                                {{ request('tipo_cargo') == 'tecnico' ? 'selected' : '' }}>Técnico
                                            </option>
                                            <option value="auxiliar"
                                                {{ request('tipo_cargo') == 'auxiliar' ? 'selected' : '' }}>Auxiliar
                                            </option>
                                            <option value="operativo"
                                                {{ request('tipo_cargo') == 'operativo' ? 'selected' : '' }}>Operativo
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Tipo de Prueba --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-clipboard-list text-secondary me-1"
                                                style="font-size: 11px;"></i>
                                            Tipo de Prueba
                                        </label>
                                        <select name="psicosocial_tipo" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todos</option>
                                            <option value="A"
                                                {{ request('psicosocial_tipo') == 'A' ? 'selected' : '' }}>Tipo A
                                            </option>
                                            <option value="B"
                                                {{ request('psicosocial_tipo') == 'B' ? 'selected' : '' }}>Tipo B
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Área --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-sitemap text-info me-1" style="font-size: 11px;"></i>
                                            Área
                                        </label>
                                        <select name="area_id" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todas las áreas</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area['_id'] ?? $area->_id }}"
                                                    {{ request('area_id') == ($area['_id'] ?? $area->_id) ? 'selected' : '' }}>
                                                    {{ $area['nombre'] ?? $area->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Centro --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-building text-warning me-1" style="font-size: 11px;"></i>
                                            Centro/Sede
                                        </label>
                                        <select name="centro_id" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todos los centros</option>
                                            @foreach ($centros as $centro)
                                                <option value="{{ $centro['_id'] ?? $centro->_id }}"
                                                    {{ request('centro_id') == ($centro['_id'] ?? $centro->_id) ? 'selected' : '' }}>
                                                    {{ $centro['nombre'] ?? $centro->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Proceso --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-project-diagram text-success me-1"
                                                style="font-size: 11px;"></i>
                                            Proceso
                                        </label>
                                        <select name="proceso_id" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todos los procesos</option>
                                            @foreach ($procesos as $proceso)
                                                <option value="{{ $proceso['_id'] ?? $proceso->_id }}"
                                                    {{ request('proceso_id') == ($proceso['_id'] ?? $proceso->_id) ? 'selected' : '' }}>
                                                    {{ $proceso['nombre'] ?? $proceso->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Ciudad --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-city text-primary me-1" style="font-size: 11px;"></i>
                                            Ciudad
                                        </label>
                                        <input type="text" name="ciudad"
                                            class="form-control form-control-sm shadow-sm"
                                            placeholder="Nombre de la ciudad" value="{{ request('ciudad') }}"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                    </div>

                                    {{-- Estado --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-toggle-on text-danger me-1" style="font-size: 11px;"></i>
                                            Estado
                                        </label>
                                        <select name="estado" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="">Todos</option>
                                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>
                                                Activo</option>
                                            <option value="inactivo"
                                                {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Registros por página --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 12px;">
                                            <i class="fas fa-list-ol text-info me-1" style="font-size: 11px;"></i>
                                            Registros por página
                                        </label>
                                        <select name="per_page" class="form-select form-select-sm shadow-sm"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 7px 10px; font-size: 12px;">
                                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>
                                                20</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>
                                                50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>
                                                100</option>
                                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>
                                                500</option>
                                            <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>
                                                1000</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer bg-light border-0 d-flex gap-2 justify-content-end"
                            style="padding: 10px 20px; border-radius: 0 0 12px 12px;">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px;">
                                <i class="fas fa-times me-1" style="font-size: 11px;"></i>
                                Cerrar
                            </button>
                            <a href="{{ route('empresa.empleados.index') }}" class="btn btn-sm btn-outline-secondary"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px;">
                                <i class="fas fa-eraser me-1" style="font-size: 11px;"></i>
                                Limpiar
                            </a>
                            <button type="submit" form="filtrosForm" class="btn btn-sm btn-primary"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                                <i class="fas fa-check me-1" style="font-size: 11px;"></i>
                                Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal 1: Crear Empleado Individual --}}
            <div class="modal fade" id="crearEmpleadoModal" tabindex="-1" aria-labelledby="crearEmpleadoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 800px;">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-gradient text-white border-0"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 12px 12px 0 0; padding: 12px 20px;">
                            <h5 class="modal-title fw-bold" id="crearEmpleadoModalLabel" style="font-size: 15px;">
                                <i class="fas fa-user-plus me-2" style="font-size: 13px;"></i>
                                Crear Nuevo Empleado
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar" style="font-size: 12px;"></button>
                        </div>
                        <div class="modal-body" style="padding: 16px 20px;">
                            <form id="crearEmpleadoForm" method="POST" action="{{ route('empresa.empleados.store') }}">
                                @csrf
                                @php
                                    $empleado = [
                                        'primerNombre' => old('primer_nombre', ''),
                                        'segundoNombre' => old('segundo_nombre', ''),
                                        'primerApellido' => old('primer_apellido', ''),
                                        'segundoApellido' => old('segundo_apellido', ''),
                                        'numeroDocumento' => old('numero_documento', ''),
                                        'tipoDocumento' => old('tipo_documento', 'CC'),
                                        'genero' => old('genero', ''),
                                        'email' => old('email', ''),
                                        'telefono' => old('telefono', ''),
                                        'cargo' => old('cargo', ''),
                                        'tipoCargo' => old('tipo_cargo', ''),
                                        'areaId' => old('area_id', ''),
                                        'procesoId' => old('proceso_id', ''),
                                        'centroId' => old('centro_id', ''),
                                        'ciudad' => old('ciudad', ''),
                                        'psicosocialTipo' => old('psicosocial_tipo', ''),
                                        'direccion' => old('direccion', ''),
                                    ];
                                    $fieldIdPrefix = 'crear_';
                                @endphp
                                @include('admin.gestion-administrativa.empresa.empleados._form', [
                                    'empleado' => $empleado,
                                    'areas' => $areas,
                                    'centros' => $centros,
                                    'procesos' => $procesos,
                                    'fieldIdPrefix' => $fieldIdPrefix,
                                ])
                            </form>
                        </div>
                        <div class="modal-footer bg-light border-0 d-flex gap-2 justify-content-end"
                            style="padding: 10px 20px; border-radius: 0 0 12px 12px;">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px;">
                                <i class="fas fa-times me-1" style="font-size: 11px;"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-sm btn-primary"
                                onclick="document.getElementById('crearEmpleadoForm').submit()"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                                <i class="fas fa-save me-1" style="font-size: 11px;"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal 2: Carga Masiva CSV --}}
            <div class="modal fade" id="cargaMasivaModal" tabindex="-1" aria-labelledby="cargaMasivaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="cargaMasivaModalLabel">
                                <i class="fas fa-upload me-2"></i>
                                Carga Masiva de Empleados (CSV)
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Información de Ayuda --}}
                            <div class="alert alert-info">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-info-circle mt-1"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">Instrucciones de Carga</h6>
                                        <ul class="small mb-3 ps-3">
                                            <li>El archivo debe estar en formato CSV</li>
                                            <li>Máximo 2000 registros por carga</li>
                                            <li>Todos los campos obligatorios deben incluirse</li>
                                            <li>No se permiten documentos duplicados</li>
                                        </ul>
                                        <a href="{{ route('empresa.empleados.plantilla') }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-download me-1"></i>
                                            Descargar Plantilla CSV
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Formulario de Carga --}}
                            <form id="cargaMasivaForm" method="POST"
                                action="{{ route('empresa.empleados.storeMasivo') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">
                                        Archivo CSV <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="archivo_csv" accept=".csv,.txt"
                                        class="form-control form-control-sm" required>
                                    <small class="form-text text-muted">
                                        Formatos aceptados: .csv, .txt (Max: 10MB)
                                    </small>
                                </div>

                                {{-- Estructura del CSV --}}
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-2">Estructura del CSV:</h6>
                                        <div class="overflow-auto">
                                            <code class="small d-block p-2 bg-white rounded">
                                                primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,genero,numero_documento,tipo_documento,cargo,email,telefono,area_trabajo,proceso,sede,ciudad,psicosocial_tipo,tipo_cargo,contrato
                                            </code>
                                        </div>
                                        <p class="small text-muted mt-2 mb-0">
                                            <strong>Ejemplo:</strong><br>
                                            Juan,Carlos,Pérez,Gómez,masculino,12345678,CC,Operativo,juan.perez@example.com,3001234567,OPERATIVA,OPERACIONES,REGIONAL
                                            CARIBE,Bogotá,A,auxiliar,SOLMEX 2025
                                        </p>
                                        <p class="small text-info mt-2 mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Nota:</strong> Use los nombres completos de área, proceso, sede y
                                            contrato tal como aparecen en el sistema.
                                        </p>
                                        <p class="small text-warning mt-2 mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>psicosocial_tipo:</strong> Debe ser 'A' o 'B'. Si está vacío, el
                                            empleado no tendrá evaluación psicosocial activa.
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary btn-sm"
                                onclick="document.getElementById('cargaMasivaForm').submit()">
                                <i class="fas fa-upload me-2"></i>
                                Cargar Archivo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal 3: Editar Empleado --}}
            {{-- 
                NOTA: Este modal utiliza la misma estructura de formulario que:
                - resources/views/admin/gestion-administrativa/empresa/empleados/edit.blade.php (Página completa)
                - resources/views/admin/gestion-administrativa/empresa/empleados/_form.blade.php (Formulario parcial reutilizable)
                
                Los datos se cargan vía AJAX desde EmpleadoController@edit
                El formulario se envía a EmpleadoController@update con método PUT
            --}}
            <div class="modal fade" id="editarEmpleadoModal" tabindex="-1" aria-labelledby="editarEmpleadoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 800px;">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-gradient text-white border-0"
                            style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border-radius: 12px 12px 0 0; padding: 12px 20px;">
                            <h5 class="modal-title fw-bold" id="editarEmpleadoModalLabel" style="font-size: 15px;">
                                <i class="fas fa-user-edit me-2" style="font-size: 13px;"></i>
                                Editar Empleado
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar" style="font-size: 12px;"></button>
                        </div>
                        <div class="modal-body" id="editarEmpleadoModalBody" style="padding: 16px 20px;">
                            <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                                <div class="spinner-border text-warning mb-3" role="status"
                                    style="width: 2.5rem; height: 2.5rem;">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mb-0" style="font-size: 13px;">Cargando información del empleado...</p>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 d-flex gap-2 justify-content-end"
                            style="padding: 10px 20px; border-radius: 0 0 12px 12px;">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px;">
                                <i class="fas fa-times me-1" style="font-size: 11px;"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="actualizarEmpleado()"
                                style="padding: 6px 14px; font-size: 12px; border-radius: 6px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border: none; color: white;">
                                <i class="fas fa-save me-1" style="font-size: 11px;"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario oculto para desactivación --}}
            <form id="desactivarForm" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>

            @push('scripts')
                {{-- Estilos adicionales para asegurar visibilidad --}}
                <style>
                    /* Asegurar que todo el texto sea visible */
                    #empleadosTable,
                    #empleadosTable * {
                        color: #000 !important;
                    }

                    /* Estilos específicos para la tabla */
                    #empleadosTable {
                        background-color: #fff !important;
                    }

                    #empleadosTable thead {
                        background-color: #f8f9fa !important;
                    }

                    #empleadosTable thead th {
                        color: #000 !important;
                        font-weight: 600 !important;
                        border-bottom: 2px solid #dee2e6 !important;
                    }

                    #empleadosTable tbody tr {
                        background-color: #fff !important;
                    }

                    #empleadosTable tbody tr:hover {
                        background-color: #f8f9fa !important;
                    }

                    /* Links */
                    #empleadosTable a {
                        color: #0d6efd !important;
                        text-decoration: none;
                    }

                    #empleadosTable a:hover {
                        color: #0a58ca !important;
                        text-decoration: underline;
                    }

                    /* Badges - mantener sus colores de fondo pero asegurar texto visible */
                    .badge {
                        display: inline-block;
                        padding: 0.35em 0.65em;
                        font-size: 0.75em;
                        font-weight: 600;
                        line-height: 1;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: baseline;
                        border-radius: 0.25rem;
                    }

                    /* DataTables controls */
                    .dataTables_wrapper .dataTables_length,
                    .dataTables_wrapper .dataTables_filter,
                    .dataTables_wrapper .dataTables_info,
                    .dataTables_wrapper .dataTables_paginate {
                        color: #000 !important;
                    }

                    .dataTables_wrapper .dataTables_filter input,
                    .dataTables_wrapper .dataTables_length select {
                        color: #000 !important;
                        background-color: #fff !important;
                        border: 1px solid #ced4da !important;
                    }

                    /* Botones de paginación */
                    .page-link {
                        color: #0d6efd !important;
                        background-color: #fff !important;
                        border: 1px solid #dee2e6 !important;
                    }

                    .page-link:hover {
                        color: #0a58ca !important;
                        background-color: #e9ecef !important;
                    }

                    .page-item.active .page-link {
                        background-color: #0d6efd !important;
                        border-color: #0d6efd !important;
                        color: #fff !important;
                    }

                    /* Select de filtros en headers */
                    .form-select-sm {
                        color: #000 !important;
                        background-color: #fff !important;
                        border: 1px solid #ced4da !important;
                        font-size: 11px !important;
                        padding: 2px 5px !important;
                    }

                    /* Asegurar que los botones tengan buen contraste */
                    .btn {
                        font-weight: 500 !important;
                    }

                    .btn-info {
                        color: #fff !important;
                        background-color: #0dcaf0 !important;
                        border-color: #0dcaf0 !important;
                    }

                    .btn-warning {
                        color: #000 !important;
                        background-color: #ffc107 !important;
                        border-color: #ffc107 !important;
                    }

                    .btn-danger {
                        color: #fff !important;
                        background-color: #dc3545 !important;
                        border-color: #dc3545 !important;
                    }

                    /* Card del contenedor */
                    .card {
                        background-color: #fff !important;
                        border: 1px solid #dee2e6 !important;
                        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
                    }

                    .card-body {
                        padding: 1.25rem !important;
                    }
                </style>

                {{-- DataTables CSS --}}
                <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
                <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
                <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

                {{-- DataTables JS --}}
                <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
                <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
                <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

                <script>
                    $(document).ready(function() {
                        // Inicializar DataTable con configuración Silva Theme
                        var table = $('#empleadosTable').DataTable({
                            responsive: true,
                            pageLength: 50,
                            lengthMenu: [
                                [50, 100, 500, 1000, -1],
                                [50, 100, 500, 1000, "Todos"]
                            ],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                            },
                            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
                                "<'row'<'col-sm-12'tr>>",
                            order: [
                                [0, 'asc']
                            ],
                            columnDefs: [{
                                    responsivePriority: 1,
                                    targets: 0
                                }, // DNI
                                {
                                    responsivePriority: 2,
                                    targets: 1
                                }, // Primer Nombre
                                {
                                    responsivePriority: 3,
                                    targets: -1
                                }, // Acciones
                                {
                                    orderable: false,
                                    targets: -1
                                } // No ordenar acciones
                            ],
                            initComplete: function() {
                                // Agregar filtros por columna
                                this.api().columns([5, 8, 9, 10, 11, 12, 13]).every(function() {
                                    var column = this;
                                    var columnIndex = column.index();
                                    var columnName = $(column.header()).text();

                                    // Crear select solo para columnas específicas
                                    if ([5, 8, 9, 10, 11, 12, 13].includes(columnIndex)) {
                                        var select = $(
                                                '<select class="form-select form-select-sm mt-1"><option value="">Todos</option></select>'
                                            )
                                            .appendTo($(column.header()))
                                            .on('change', function() {
                                                var val = $.fn.dataTable.util.escapeRegex($(this)
                                                    .val());
                                                column.search(val ? '^' + val + '$' : '', true, false)
                                                    .draw();
                                            })
                                            .on('click', function(e) {
                                                e.stopPropagation();
                                            });

                                        column.data().unique().sort().each(function(d, j) {
                                            // Extraer texto de badges/spans si existen
                                            var text = $('<div>').html(d).text().trim();
                                            if (text && text !== '-' && text !== 'N/A' && text !==
                                                'Sin área' &&
                                                text !== 'Sin proceso' && text !== 'Sin sede' &&
                                                text !== 'Sin cargo' &&
                                                text !== 'Sin email') {
                                                select.append('<option value="' + text + '">' +
                                                    text + '</option>');
                                            }
                                        });
                                    }
                                });
                            }
                        });

                        // Exportar a Excel
                        $('#exportExcel').on('click', function() {
                            var wb = XLSX.utils.table_to_book(document.getElementById('empleadosTable'), {
                                sheet: "Empleados"
                            });
                            XLSX.writeFile(wb, 'empleados_' + new Date().toISOString().slice(0, 10) + '.xlsx');
                        });

                        // Búsqueda global mejorada
                        $('.dataTables_filter input').attr('placeholder', 'Buscar en todos los campos...');
                    });

                    // Funciones para acciones
                    function verEmpleado(id) {
                        window.location.href = '{{ route('empresa.empleados.index') }}/' + id;
                    }

                    // Función para editar empleado - carga formulario desde la vista edit.blade.php dentro del modal
                    function editarEmpleado(id) {
                        const modalElement = document.getElementById('editarEmpleadoModal');
                        const modalBody = document.getElementById('editarEmpleadoModalBody');

                        if (!modalElement || !modalBody) {
                            alert('No se pudo abrir el modal de edición.');
                            return;
                        }

                        // Mostrar estado de carga
                        modalBody.innerHTML = `
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                                        <div class="spinner-border text-warning mb-3" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mb-0">Cargando información del empleado...</p>
                                    </div>
                                `;

                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modalInstance.show();

                        const url = '{{ route('empresa.empleados.edit', ['id' => ':id']) }}'.replace(':id', id);

                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(async response => {
                                if (!response.ok) {
                                    const errorData = await response.json().catch(() => ({}));
                                    const message = errorData.error || 'Error al cargar los datos del empleado.';
                                    throw new Error(message);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (!data.html) {
                                    throw new Error('Respuesta inesperada del servidor.');
                                }
                                modalBody.innerHTML = data.html;
                            })
                            .catch(error => {
                                console.error('Error al cargar el formulario de edición:', error);
                                modalBody.innerHTML = `
                                            <div class="alert alert-danger mb-0">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                ${error.message || 'Error al cargar los datos del empleado. Intente nuevamente.'}
                                            </div>
                                        `;
                            });
                    }

                    // Envía el formulario actual del modal
                    function actualizarEmpleado() {
                        const modalBody = document.getElementById('editarEmpleadoModalBody');
                        if (!modalBody) {
                            console.error('No se encontró el modal body');
                            alert('No se encontró el formulario de edición.');
                            return;
                        }

                        // Buscar el formulario dentro del modal
                        const form = modalBody.querySelector('form[id^="editarEmpleadoForm"]');
                        if (!form) {
                            console.error('No se encontró el formulario');
                            alert('Debe cargar nuevamente el formulario de edición.');
                            return;
                        }

                        // Validar el formulario
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }

                        // Enviar el formulario
                        form.submit();
                    }

                    function eliminarEmpleado(id, nombre) {
                        if (confirm('¿Está seguro de eliminar al empleado ' + nombre + '?')) {
                            $.ajax({
                                url: '{{ route('empresa.empleados.index') }}/' + id,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    location.reload();
                                },
                                error: function(error) {
                                    alert('Error al eliminar el empleado');
                                }
                            });
                        }
                    }

                    // Original functions
                    function confirmarDesactivacion(empleadoId) {
                        if (confirm('¿Está seguro de que desea desactivar este empleado?')) {
                            const form = document.getElementById('desactivarForm');
                            form.action = '{{ route('empresa.empleados.destroy', ':id') }}'.replace(':id', empleadoId);
                            form.submit();
                        }
                    }

                    function aplicarFiltros() {
                        const area = document.getElementById('filtroArea').value;
                        const centro = document.getElementById('filtroCentro').value;
                        const estado = document.getElementById('filtroEstado').value;

                        console.log('Filtros aplicados:', {
                            area,
                            centro,
                            estado
                        });
                        $('#filtrosModal').modal('hide');
                    }

                    function limpiarFiltros() {
                        document.getElementById('filtrosForm').reset();
                        console.log('Filtros limpiados');
                    }
                </script>
            @endpush
        @endsection
