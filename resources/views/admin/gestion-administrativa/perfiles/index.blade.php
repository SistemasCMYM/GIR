@extends('layouts.dashboard')
@section('title', 'Perfiles')
@section('title', 'Perfiles - Gestión Administrativa')

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
                        <a href="{{ route('admin.gestion-administrativa.index') }}"
                            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                            <i class="fas fa-users-cog tw-mr-1"></i> Administración de Usuarios
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-id-badge tw-mr-1"></i> Perfiles
                    </li>
                </ol>
            </nav>

            <!-- Header con Tailwind -->
            <div
                class="tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-primary-700 tw-rounded-2xl tw-p-6 tw-text-white tw-mb-6 tw-shadow-gir-lg">
                <h1 class="tw-text-3xl tw-font-bold tw-mb-2 tw-drop-shadow-md">
                    <i class="fas fa-id-badge tw-mr-3"></i> Perfiles
                </h1>
                <p class="tw-text-base tw-opacity-90 tw-mb-0">Gestión de Perfiles de Usuario</p>
            </div>

            <!-- Métricas con Tailwind -->
            <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-text-center tw-border tw-border-gray-100 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <i class="fas fa-id-badge tw-text-3xl tw-text-gray-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-2xl tw-font-bold tw-text-gray-800">{{ $stats['total_perfiles'] ?? 0 }}</span>
                    <span class="tw-block tw-text-sm tw-text-gray-600 tw-uppercase tw-tracking-wide">Perfiles</span>
                </div>
                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-text-center tw-border tw-border-gray-100 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <i class="fas fa-toggle-on tw-text-3xl tw-text-green-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-2xl tw-font-bold tw-text-gray-800">{{ $stats['perfiles_activos'] ?? 0 }}</span>
                    <span class="tw-block tw-text-sm tw-text-gray-600 tw-uppercase tw-tracking-wide">Activos</span>
                </div>
                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-text-center tw-border tw-border-gray-100 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <i class="fas fa-key tw-text-3xl tw-text-gir-gold-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-2xl tw-font-bold tw-text-gray-800">{{ $stats['permisos_totales'] ?? 0 }}</span>
                    <span class="tw-block tw-text-sm tw-text-gray-600 tw-uppercase tw-tracking-wide">Permisos</span>
                </div>
                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-text-center tw-border tw-border-gray-100 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <i class="fas fa-users tw-text-3xl tw-text-gir-primary-500 tw-mb-3"></i>
                    <span
                        class="tw-block tw-text-2xl tw-font-bold tw-text-gray-800">{{ $stats['asignaciones'] ?? 0 }}</span>
                    <span class="tw-block tw-text-sm tw-text-gray-600 tw-uppercase tw-tracking-wide">Cuentas</span>
                </div>
            </div>
            <!-- Información introductoria con Tailwind -->
            <div class="tw-mb-6">
                <h5 class="tw-text-xl tw-font-semibold tw-text-gir-warm-gray-900 tw-mb-3">
                    <i class="fas fa-user-circle tw-mr-2 tw-text-gir-primary-600"></i> Gestión de Perfiles de Usuario
                </h5>
                <div class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-border-l-4 tw-border-gir-primary-500">
                    <p class="tw-text-gir-warm-gray-600 tw-mb-0">
                        Los perfiles conectan las cuentas de usuario con roles específicos, definiendo qué módulos y
                        permisos
                        tiene cada usuario en el sistema. Cada perfil establece la relación entre una cuenta y un rol.
                    </p>
                </div>
            </div>

            <!-- Grid de perfiles con Tailwind -->
            <div class="tw-grid tw-grid-cols-1 xl:tw-grid-cols-2 tw-gap-6">
                @forelse($perfiles as $perfil)
                    <div
                        class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-overflow-hidden tw-border tw-border-gir-warm-gray-200 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                        <div
                            class="tw-flex tw-justify-between tw-items-center tw-p-6 tw-bg-gradient-to-r tw-from-gir-primary-50 tw-to-gir-gold-50 tw-border-b tw-border-gir-warm-gray-200">
                            <h6 class="tw-text-lg tw-font-semibold tw-text-gir-warm-gray-900 tw-mb-0">
                                <i class="fas fa-id-badge tw-mr-2 tw-text-gir-primary-600"></i>
                                {{ $perfil->cuenta->nick ?? 'Usuario sin nombre' }}
                            </h6>
                            <span
                                class="tw-inline-flex tw-items-center tw-px-3 tw-py-1 tw-rounded-full tw-text-xs tw-font-medium {{ $perfil->cuenta->estado === 'activa' ? 'tw-bg-green-100 tw-text-green-800' : 'tw-bg-gray-100 tw-text-gray-800' }}">
                                {{ ucfirst($perfil->cuenta->estado ?? 'inactivo') }}
                            </span>
                        </div>
                        <div class="tw-p-6">
                            <div class="tw-mb-4">
                                <strong class="tw-text-gir-warm-gray-900">Email:</strong>
                                <span class="tw-text-gir-warm-gray-600">{{ $perfil->cuenta->email ?? 'N/A' }}</span>
                            </div>

                            <div class="tw-mb-4">
                                <strong class="tw-text-gir-warm-gray-900">Rol Asignado:</strong>
                                <span
                                    class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-primary-100 tw-text-gir-primary-800 tw-ml-2">{{ $perfil->rol->nombre ?? ($perfil->cuenta->rol ?? 'Sin rol') }}</span>
                            </div>

                            <div class="tw-mb-4">
                                <strong class="tw-text-gir-warm-gray-900">Tipo de Cuenta:</strong>
                                <span
                                    class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-blue-100 tw-text-blue-800 tw-ml-2">{{ ucfirst($perfil->cuenta->tipo ?? 'usuario') }}</span>
                            </div>

                            @if ($perfil->cuenta->empresas)
                                <div class="tw-mb-4">
                                    <strong class="tw-text-gir-warm-gray-900">Empresas:</strong>
                                    <div class="tw-mt-2 tw-flex tw-flex-wrap tw-gap-1">
                                        @if (is_array($perfil->cuenta->empresas))
                                            @foreach ($perfil->cuenta->empresas as $empresa)
                                                <span
                                                    class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-warm-gray-100 tw-text-gir-warm-gray-800">{{ $empresa }}</span>
                                            @endforeach
                                        @else
                                            <span
                                                class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-medium tw-bg-gir-warm-gray-100 tw-text-gir-warm-gray-800">{{ $perfil->cuenta->empresas }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="tw-mb-4">
                                <strong class="tw-text-gir-warm-gray-900">Creado:</strong>
                                <span class="tw-text-gir-warm-gray-600">
                                    @php
                                        $fecha = 'N/A';
                                        if (isset($perfil->created_at)) {
                                            if ($perfil->created_at instanceof \Carbon\Carbon) {
                                                $fecha = $perfil->created_at->format('d/m/Y H:i');
                                            } elseif (is_numeric($perfil->created_at)) {
                                                // asumir timestamp en milisegundos
                                                try {
                                                    $fecha = \Carbon\Carbon::createFromTimestampMs(
                                                        $perfil->created_at,
                                                    )->format('d/m/Y H:i');
                                                } catch (\Exception $e) {
                                                    $fecha = 'N/A';
                                                }
                                            } elseif (is_string($perfil->created_at)) {
                                                try {
                                                    $fecha = \Carbon\Carbon::parse($perfil->created_at)->format(
                                                        'd/m/Y H:i',
                                                    );
                                                } catch (\Exception $e) {
                                                    $fecha = 'N/A';
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $fecha }}
                                </span>
                            </div>

                            <div class="tw-flex tw-gap-2">
                                <a href="{{ route('usuarios.perfiles.show', ['id' => $perfil->id ?? '']) }}"
                                    class="tw-inline-flex tw-items-center tw-px-3 tw-py-1.5 tw-border tw-border-blue-300 tw-text-blue-700 tw-bg-transparent tw-rounded-md tw-text-sm tw-font-medium hover:tw-bg-blue-50 tw-transition-colors">Ver</a>
                                <a href="{{ route('usuarios.perfiles.edit', ['id' => $perfil->id ?? '']) }}"
                                    class="tw-inline-flex tw-items-center tw-px-3 tw-py-1.5 tw-border tw-border-gir-gold-300 tw-text-gir-gold-700 tw-bg-transparent tw-rounded-md tw-text-sm tw-font-medium hover:tw-bg-gir-gold-50 tw-transition-colors">Editar</a>
                                <button
                                    class="tw-inline-flex tw-items-center tw-px-3 tw-py-1.5 tw-border tw-border-red-300 tw-text-red-700 tw-bg-transparent tw-rounded-md tw-text-sm tw-font-medium hover:tw-bg-red-50 tw-transition-colors">Eliminar</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="tw-col-span-full">
                        <div class="tw-text-center tw-py-12">
                            <i class="fas fa-id-badge fa-3x tw-text-gir-warm-gray-300 tw-mb-4"></i>
                            <h5 class="tw-text-xl tw-font-medium tw-text-gir-warm-gray-600 tw-mb-2">No hay perfiles
                                configurados</h5>
                            <p class="tw-text-gir-warm-gray-500 tw-mb-4">Los perfiles se crean automáticamente cuando se
                                asignan roles a las cuentas de usuario.</p>
                            <a href="{{ route('usuarios.cuentas.index') }}"
                                class="tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-text-white tw-font-medium tw-rounded-lg tw-shadow-gir hover:tw-shadow-gir-md tw-transition-all tw-duration-300">
                                <i class="fas fa-users tw-mr-2"></i>Gestionar Cuentas
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginación con Tailwind -->
            @if (
                $perfiles instanceof \Illuminate\Contracts\Pagination\Paginator &&
                    method_exists($perfiles, 'links') &&
                    $perfiles->hasPages())
                <div class="tw-flex tw-justify-center tw-mt-6">
                    {{ $perfiles->links() }}
                </div>
            @endif
        </div>
    @endsection
