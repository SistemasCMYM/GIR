@extends('layouts.dashboard')

@section('title', 'Gestión de Empresas')
@section('page-title', 'Gestión de Empresas')

@section('content')
    <div class="tw-container-fluid tw-py-6">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4">
            {{-- 
          INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA 
          @last_verified: 2025-08-14 
          @security_level: critical
        --}}

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
                            <i class="fas fa-building tw-mr-1"></i> Gestión Empresarial
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-building tw-mr-1"></i> Empresas
                    </li>
                </ol>
            </nav>

            <!-- Header con Tailwind -->
            <div class="tw-flex tw-justify-between tw-items-center tw-mb-6">
                <div
                    class="tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-primary-700 tw-rounded-2xl tw-p-6 tw-text-white tw-flex-1 tw-mr-4 tw-shadow-gir-lg">
                    <h1 class="tw-text-3xl tw-font-bold tw-mb-2 tw-drop-shadow-md">
                        <i class="fas fa-building tw-mr-3"></i>Gestión de Empresas
                    </h1>
                    <p class="tw-text-base tw-opacity-90 tw-mb-0">Administre las empresas del sistema</p>
                </div>
                <div>
                    <button
                        class="tw-inline-flex tw-items-center tw-px-6 tw-py-3 tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-text-white tw-font-medium tw-rounded-xl tw-shadow-gir hover:tw-shadow-gir-md tw-transition-all tw-duration-300 hover:tw-transform hover:tw--translate-y-1"
                        data-bs-toggle="modal" data-bs-target="#createEmpresaModal">
                        <i class="fas fa-plus tw-mr-2"></i>
                        Nueva Empresa
                    </button>
                </div>
            </div>

            <!-- Statistics Cards con Tailwind -->
            <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4 tw-mb-6">
                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-border-l-4 tw-border-gir-primary-500 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <div>
                            <div
                                class="tw-text-xs tw-font-bold tw-text-gir-primary-600 tw-uppercase tw-tracking-wider tw-mb-1">
                                Total Empresas</div>
                            <div class="tw-text-2xl tw-font-bold tw-text-gir-warm-gray-800">0</div>
                        </div>
                        <div
                            class="tw-w-12 tw-h-12 tw-bg-gir-primary-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                            <i class="fas fa-building tw-text-gir-primary-600 tw-text-xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-border-l-4 tw-border-green-500 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <div>
                            <div class="tw-text-xs tw-font-bold tw-text-green-600 tw-uppercase tw-tracking-wider tw-mb-1">
                                Empresas Activas</div>
                            <div class="tw-text-2xl tw-font-bold tw-text-gir-warm-gray-800">0</div>
                        </div>
                        <div
                            class="tw-w-12 tw-h-12 tw-bg-green-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                            <i class="fas fa-check-circle tw-text-green-600 tw-text-xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-border-l-4 tw-border-blue-500 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <div>
                            <div class="tw-text-xs tw-font-bold tw-text-blue-600 tw-uppercase tw-tracking-wider tw-mb-1">
                                Empleados Total</div>
                            <div class="tw-text-2xl tw-font-bold tw-text-gir-warm-gray-800">0</div>
                        </div>
                        <div class="tw-w-12 tw-h-12 tw-bg-blue-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                            <i class="fas fa-users tw-text-blue-600 tw-text-xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-p-6 tw-border-l-4 tw-border-gir-gold-500 tw-transition-all tw-duration-300 hover:tw-shadow-gir-lg">
                    <div class="tw-flex tw-items-center tw-justify-between">
                        <div>
                            <div
                                class="tw-text-xs tw-font-bold tw-text-gir-gold-600 tw-uppercase tw-tracking-wider tw-mb-1">
                                Centros de Trabajo</div>
                            <div class="tw-text-2xl tw-font-bold tw-text-gir-warm-gray-800">0</div>
                        </div>
                        <div
                            class="tw-w-12 tw-h-12 tw-bg-gir-gold-100 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                            <i class="fas fa-map-marker-alt tw-text-gir-gold-600 tw-text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card con Tailwind -->
            <div class="tw-bg-white tw-rounded-xl tw-shadow-gir tw-overflow-hidden">
                <div class="tw-flex tw-justify-between tw-items-center tw-p-6 tw-border-b tw-border-gir-warm-gray-200">
                    <h6 class="tw-text-lg tw-font-semibold tw-text-gir-warm-gray-900 tw-mb-0">
                        <i class="fas fa-list tw-mr-2 tw-text-gir-primary-600"></i>
                        Lista de Empresas
                    </h6>
                </div>
                <div class="tw-p-6">
                    <div class="tw-overflow-x-auto">
                        <table class="tw-w-full tw-text-sm" id="empresasTable">
                            <thead class="tw-bg-gir-warm-gray-50">
                                <tr>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        NIT</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Razón Social</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Nombre Comercial</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Estado</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Fecha Registro</th>
                                    <th
                                        class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gir-warm-gray-500 tw-uppercase tw-tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="tw-bg-white tw-divide-y tw-divide-gir-warm-gray-200">
                                <!-- Los datos se cargarán dinámicamente via AJAX -->
                                <tr>
                                    <td colspan="6" class="tw-px-6 tw-py-12 tw-text-center">
                                        <div class="tw-text-gir-warm-gray-500">
                                            <i class="fas fa-building fa-3x tw-mb-4 tw-text-gir-warm-gray-300"></i>
                                            <p class="tw-text-lg tw-font-medium tw-mb-0">No hay empresas registradas</p>
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

    <!-- Modal para crear empresa con Tailwind -->
    <div class="modal fade" id="createEmpresaModal" tabindex="-1" aria-labelledby="createEmpresaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-gir-xl tw-overflow-hidden tw-max-w-2xl tw-mx-auto">
                <div
                    class="tw-flex tw-justify-between tw-items-center tw-p-6 tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-text-white">
                    <h5 class="tw-text-xl tw-font-semibold tw-mb-0" id="createEmpresaModalLabel">
                        <i class="fas fa-building tw-mr-2"></i>
                        Nueva Empresa
                    </h5>
                    <button type="button" class="tw-text-white hover:tw-text-gir-warm-gray-200 tw-transition-colors"
                        data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times tw-text-xl"></i>
                    </button>
                </div>
                <form id="createEmpresaForm">
                    <div class="tw-p-6">
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <label for="nit"
                                    class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">NIT
                                    *</label>
                                <input type="text"
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all"
                                    id="nit" name="nit" required>
                            </div>
                            <div>
                                <label for="razon_social"
                                    class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Razón
                                    Social *</label>
                                <input type="text"
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all"
                                    id="razon_social" name="razon_social" required>
                            </div>
                        </div>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-4">
                            <div>
                                <label for="nombre_comercial"
                                    class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Nombre
                                    Comercial</label>
                                <input type="text"
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all"
                                    id="nombre_comercial" name="nombre_comercial">
                            </div>
                            <div>
                                <label for="estado"
                                    class="tw-block tw-text-sm tw-font-medium tw-text-gir-warm-gray-700 tw-mb-2">Estado</label>
                                <select
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-transition-all"
                                    id="estado" name="estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tw-flex tw-justify-end tw-gap-3 tw-p-6 tw-bg-gir-warm-gray-50 tw-border-t tw-border-gir-warm-gray-200">
                        <button type="button"
                            class="tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-border tw-border-gir-warm-gray-300 tw-text-gir-warm-gray-700 tw-bg-white tw-rounded-lg tw-text-sm tw-font-medium hover:tw-bg-gir-warm-gray-50 tw-transition-colors"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit"
                            class="tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-text-white tw-font-medium tw-rounded-lg tw-shadow-gir hover:tw-shadow-gir-md tw-transition-all tw-duration-300">
                            <i class="fas fa-save tw-mr-2"></i>
                            Guardar Empresa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#empresasTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                responsive: true,
                processing: true,
                // serverSide: true, // Activar cuando se implemente el backend
                // ajax: '{{ route('empresa.empresas.data') }}' // Ruta para obtener datos
            });

            // Manejar envío del formulario
            $('#createEmpresaForm').on('submit', function(e) {
                e.preventDefault();

                // Aquí iría la lógica para enviar los datos al servidor
                alert('Funcionalidad en desarrollo');
                $('#createEmpresaModal').modal('hide');
            });
        });
    </script>
@endpush
