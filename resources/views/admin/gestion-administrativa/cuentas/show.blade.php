@extends('layouts.dashboard')

@php
    // Protege la vista si por algún motivo el controlador no pasa $cuenta.
    // Creamos un objeto con propiedades por defecto para evitar errores al acceder a propiedades.
    if (!isset($cuenta) || $cuenta === null) {
        $cuenta = (object) [
            'nombre' => '',
            'apellido' => '',
            'nick' => '',
            'email' => '',
            'estado' => 'activa',
            '_id' => null,
            'ultimo_acceso' => null,
            'empleado' => null,
        ];
    }
@endphp

@section('title', 'Detalle de Cuenta - Gestión Administrativa')

@section('content')
    <div class="tw-p-4 tw-min-h-screen tw-bg-gray-50">
        <!-- Header -->
        <div class="gir-card tw-mb-6">
            <div class="gir-card-header">
                <div
                    class="tw-flex tw-flex-col md:tw-flex-row tw-items-start md:tw-items-center tw-justify-between tw-gap-4">
                    <div class="gir-card-header-content">
                        <h1 class="gir-card-title">
                            <i class="fas fa-user tw-mr-3"></i>Detalle de Cuenta
                        </h1>
                        <p class="gir-card-subtitle">Información completa de la cuenta de usuario</p>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="tw-flex tw-items-center tw-space-x-2 tw-text-sm tw-text-white/80">
                            <li><a href="{{ route('empleados.index') }}"
                                    class="hover:tw-text-white tw-transition-colors tw-no-underline">Inicio</a></li>
                            <li class="tw-text-white/60">/</li>
                            <li><a href="{{ route('usuarios.index') }}"
                                    class="hover:tw-text-white tw-transition-colors tw-no-underline">Gestión
                                    Administrativa</a></li>
                            <li class="tw-text-white/60">/</li>
                            <li><a href="{{ route('usuarios.cuentas.index') }}"
                                    class="hover:tw-text-white tw-transition-colors tw-no-underline">Cuentas</a></li>
                            <li class="tw-text-white/60">/</li>
                            <li class="tw-text-white tw-font-medium">Detalle</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Información Principal -->
        <div class="gir-card tw-mb-6">
            <div class="gir-card-body">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-6">
                    <div class="tw-text-center">
                        <div
                            class="tw-w-20 tw-h-20 tw-rounded-xl tw-bg-gradient-to-br tw-from-primary-500 tw-to-primary-700 tw-flex tw-items-center tw-justify-center tw-text-white tw-font-bold tw-text-2xl tw-mx-auto tw-mb-4 tw-shadow-lg">
                            {{ strtoupper(substr($cuenta->nombre ?? ($cuenta->nick ?? $cuenta->email), 0, 2)) }}
                        </div>
                        <h5 class="tw-text-lg tw-font-bold tw-text-gray-900 tw-mb-1">{{ $cuenta->nombre ?? 'Sin nombre' }}
                            {{ $cuenta->apellido ?? '' }}</h5>
                        <p class="tw-text-gray-600 tw-text-sm tw-mb-2">{{ $cuenta->nick }}</p>
                        @php
                            $estado = $cuenta->estado ?? 'activa';
                            $badgeClass = match ($estado) {
                                'activa' => 'active',
                                'inactiva' => 'inactive',
                                'suspendida' => 'warning',
                                default => 'active',
                            };
                        @endphp
                        <span class="gir-badge gir-badge-{{ $badgeClass }}">{{ ucfirst($estado) }}</span>
                    </div>
                    <div class="md:tw-col-span-3">
                        <h5 class="tw-text-xl tw-font-bold tw-text-gray-900 tw-mb-4 tw-pb-2 tw-border-b tw-border-gray-200">
                            <i class="fas fa-info-circle tw-mr-2 tw-text-primary-600"></i>Información General
                        </h5>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                                <div
                                    class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                                    Email</div>
                                <div class="tw-text-sm tw-text-gray-900 tw-font-medium">{{ $cuenta->email }}</div>
                            </div>
                            <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                                <div
                                    class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                                    DNI/Cédula</div>
                                <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                                    {{ $cuenta->dni ?? 'No especificado' }}</div>
                            </div>
                            <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                                <div
                                    class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                                    Rol</div>
                                <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                                    <span
                                        class="gir-badge gir-badge-primary">{{ ucfirst($cuenta->rol ?? 'Sin rol') }}</span>
                                </div>
                            </div>
                            <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                                <div
                                    class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                                    Tipo de Cuenta</div>
                                <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                                    {{ ucfirst($cuenta->tipo ?? 'No especificado') }}</div>
                            </div>
                            <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                                <div
                                    class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                                    Género</div>
                                <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                                    {{ ucfirst($cuenta->genero ?? 'No especificado') }}</div>
                            </div>
                            <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                                <div
                                    class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                                    Ocupación</div>
                                <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                                    {{ $cuenta->ocupacion ?? 'No especificada' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="gir-card tw-mb-6">
            <div class="gir-card-header">
                <h5 class="gir-card-section-title">
                    <i class="fas fa-cog tw-mr-2"></i>Información del Sistema
                </h5>
            </div>
            <div class="gir-card-body">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-4">
                    <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                        <div class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">ID
                            de Cuenta</div>
                        <div class="tw-text-sm tw-text-gray-900 tw-font-medium">{{ $cuenta->_id ?? $cuenta->id }}</div>
                    </div>
                    <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                        <div class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">ID
                            Empleado</div>
                        <div class="tw-text-sm tw-text-gray-900 tw-font-medium">{{ $cuenta->empleado_id ?? 'No asignado' }}
                        </div>
                    </div>
                    <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                        <div class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                            Clave Centro</div>
                        <div class="tw-text-sm tw-text-gray-900 tw-font-medium">{{ $cuenta->centro_key ?? 'No asignada' }}
                        </div>
                    </div>
                    <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                        <div class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                            Fecha de Creación</div>
                        <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                            @if ($cuenta->created_at)
                                {{ \Carbon\Carbon::parse($cuenta->created_at)->format('d/m/Y H:i:s') }}
                            @else
                                No disponible
                            @endif
                        </div>
                    </div>
                    <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                        <div class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                            Último Acceso</div>
                        <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                            @if ($cuenta->ultimo_acceso)
                                {{ \Carbon\Carbon::parse($cuenta->ultimo_acceso)->diffForHumans() }}
                            @else
                                Nunca
                            @endif
                        </div>
                    </div>
                    <div class="tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border-l-4 tw-border-primary-500">
                        <div class="tw-text-xs tw-font-semibold tw-text-gray-500 tw-uppercase tw-tracking-wide tw-mb-1">
                            Empresa</div>
                        <div class="tw-text-sm tw-text-gray-900 tw-font-medium">
                            @if ($cuenta->empresas && is_array($cuenta->empresas) && count($cuenta->empresas) > 0)
                                {{ implode(', ', $cuenta->empresas) }}
                            @else
                                Sin empresa asignada
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="gir-card">
            <div class="gir-card-header">
                <h5 class="gir-card-section-title">
                    <i class="fas fa-tools tw-mr-2"></i>Acciones
                </h5>
            </div>
            <div class="gir-card-body">
                <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3">
                    <a href="{{ route('usuarios.cuentas.edit', $cuenta->_id ?? $cuenta->id) }}"
                        class="gir-btn gir-btn-warning tw-no-underline tw-text-center">
                        <i class="fas fa-edit tw-mr-2"></i>Editar Cuenta
                    </a>
                    <a href="{{ route('usuarios.cuentas.index') }}"
                        class="gir-btn gir-btn-secondary tw-no-underline tw-text-center">
                        <i class="fas fa-arrow-left tw-mr-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
    </section>
    </div>
@endsection
