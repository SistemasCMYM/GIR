@extends('layouts.dashboard')

@section('title', 'Auditoría de Usuarios')

@section('content')
    <div class="tw-container-fluid tw-py-6">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4">
            <!-- Breadcrumb con Tailwind -->
            <nav aria-label="breadcrumb" class="tw-mb-6">
                <ol class="breadcrumb tw-bg-transparent tw-mb-0 tw-p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('empleados.index') }}"
                            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                            <i class="fas fa-home tw-mr-1"></i> Inicio
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.gestion-administrativa.index') }}"
                            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                            <i class="fas fa-users-cog tw-mr-1"></i> Gestión Administrativa
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-clipboard-list tw-mr-1"></i> Auditoría
                    </li>
                </ol>
            </nav>

            <!-- Header con Tailwind -->
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-gir tw-overflow-hidden tw-mb-6">
                <div class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-p-6 tw-text-white">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <div>
                            <h4 class="tw-text-2xl tw-font-bold tw-mb-2 tw-text-white tw-drop-shadow-md">
                                <i class="fas fa-clipboard-list tw-mr-3"></i>
                                Auditoría de Usuarios
                            </h4>
                            <p class="tw-text-base tw-opacity-90 tw-mb-0">Registro de actividades y cambios en el sistema
                            </p>
                        </div>
                        <div class="tw-flex tw-gap-2">
                            <button
                                class="tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-border tw-border-white tw-border-opacity-30 tw-text-white tw-bg-transparent tw-rounded-lg tw-text-sm tw-font-medium hover:tw-bg-white hover:tw-bg-opacity-20 tw-transition-all">
                                <i class="fas fa-filter tw-mr-2"></i>Filtros
                            </button>
                            <button
                                class="tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-bg-white tw-text-gir-primary-700 tw-rounded-lg tw-text-sm tw-font-medium hover:tw-bg-gir-warm-gray-100 tw-transition-all tw-shadow-md">
                                <i class="fas fa-download tw-mr-2"></i>Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros con Tailwind -->
                <div class="tw-p-6">
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                        <div>
                            <label class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Fecha
                                Inicio</label>
                            <input type="date"
                                class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-text-sm tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all">
                        </div>
                        <div>
                            <label class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Fecha
                                Fin</label>
                            <input type="date"
                                class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-text-sm tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all">
                        </div>
                        <div>
                            <label
                                class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Usuario</label>
                            <select
                                class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-text-sm tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all">
                                <option>Todos los usuarios</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Acción</label>
                            <select
                                class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-text-sm tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all">
                                <option>Todas las acciones</option>
                                <option>Login</option>
                                <option>Logout</option>
                                <option>Crear Usuario</option>
                                <option>Modificar Usuario</option>
                                <option>Eliminar Usuario</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tabla con Tailwind -->
                    <div class="tw-overflow-x-auto">
                        <table class="tw-w-full tw-text-sm">
                            <thead class="tw-bg-gir-warm-gray-50">
                                <tr>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Fecha/Hora</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Usuario</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Acción</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Detalle</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        IP</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Estado</th>
                                </tr>
                            </thead>
                            <tbody class="tw-bg-white tw-divide-y tw-divide-gir-warm-gray-200">
                                <tr class="hover:tw-bg-gir-warm-gray-50 tw-transition-colors">
                                    <td class="tw-px-6 tw-py-4 tw-whitespace-nowrap">
                                        <span
                                            class="tw-text-sm tw-text-gir-warm-gray-500">{{ now()->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="tw-px-6 tw-py-4 tw-whitespace-nowrap">
                                        <div class="tw-flex tw-items-center">
                                            <div class="tw-flex-shrink-0 tw-w-8 tw-h-8">
                                                <div
                                                    class="tw-w-8 tw-h-8 tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-gold-500 tw-rounded-full tw-flex tw-items-center tw-justify-center">
                                                    <i class="fas fa-user tw-text-white tw-text-xs"></i>
                                                </div>
                                            </div>
                                            <div class="tw-ml-3">
                                                <div class="tw-text-sm tw-font-medium tw-text-gir-warm-gray-900">
                                                    admin@test.com</div>
                                                <div class="tw-text-xs tw-text-gir-warm-gray-500">Administrador</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="tw-px-6 tw-py-4 tw-whitespace-nowrap">
                                        <span
                                            class="tw-inline-flex tw-items-center tw-px-3 tw-py-1 tw-rounded-full tw-text-xs tw-font-medium tw-bg-green-100 tw-text-green-800 tw-border tw-border-green-200">
                                            Login exitoso
                                        </span>
                                    </td>
                                    <td class="tw-px-6 tw-py-4 tw-whitespace-nowrap">
                                        <span class="tw-text-sm tw-text-gir-warm-gray-500">Acceso al sistema</span>
                                    </td>
                                    <td class="tw-px-6 tw-py-4 tw-whitespace-nowrap">
                                        <span class="tw-text-sm tw-text-gir-warm-gray-500">127.0.0.1</span>
                                    </td>
                                    <td class="tw-px-6 tw-py-4 tw-whitespace-nowrap">
                                        <i class="fas fa-check-circle tw-text-green-500"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="tw-px-6 tw-py-12 tw-text-center tw-text-gir-warm-gray-500">
                                        <div class="tw-flex tw-flex-col tw-items-center">
                                            <i class="fas fa-info-circle tw-text-3xl tw-text-gir-warm-gray-300 tw-mb-4"></i>
                                            <span class="tw-text-lg tw-font-medium tw-mb-2">Módulo de auditoría en
                                                desarrollo</span>
                                            <span class="tw-text-sm">Los registros se mostrarán aquí una vez
                                                implementado.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
