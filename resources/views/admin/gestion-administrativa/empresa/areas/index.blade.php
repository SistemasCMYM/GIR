@extends('layouts.dashboard')

@section('title', 'Gestión de Áreas')
@section('page-title', 'Gestión de Áreas')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('empresa.index') }}"
            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
            <i class="fas fa-building tw-mr-1"></i> Administración de Empresa
        </a>
    </li>
    <li class="tw-text-gray-600 tw-font-medium">Gestión de Áreas</li>
@endsection

@section('content')
    <div class="tw-min-h-screen tw-bg-gradient-to-br tw-from-gray-50 tw-to-white">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4 tw-py-6">
            {{-- 
          INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA 
          @last_verified: 2025-08-14 
          @security_level: critical
        --}}

            <!-- Page Header -->
            <div
                class="tw-flex tw-flex-col lg:tw-flex-row tw-justify-between tw-items-start lg:tw-items-center tw-mb-8 tw-gap-4">
                <div>
                    <h1
                        class="tw-text-3xl tw-font-bold tw-bg-gradient-to-r tw-from-gir-primary-600 tw-to-gir-gold-600 tw-bg-clip-text tw-text-transparent tw-flex tw-items-center tw-gap-3">
                        <i class="fas fa-sitemap tw-text-gir-primary-500"></i>
                        Gestión de Áreas
                    </h1>
                    <p class="tw-text-gray-600 tw-mt-2">Administre las áreas organizacionales de su empresa</p>
                </div>
                <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-w-full lg:tw-w-auto">
                    <button
                        class="tw-bg-gradient-to-r tw-from-green-500 tw-to-green-600 hover:tw-from-green-600 hover:tw-to-green-700 tw-text-white tw-px-6 tw-py-3 tw-rounded-xl tw-font-semibold tw-shadow-lg hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-transform hover:tw-scale-105 tw-flex tw-items-center tw-justify-center tw-gap-2"
                        data-bs-toggle="modal" data-bs-target="#uploadAreasModal">
                        <i class="fas fa-upload"></i>
                        Cargar Áreas
                    </button>
                    <button
                        class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 hover:tw-from-gir-primary-600 hover:tw-to-gir-gold-600 tw-text-white tw-px-6 tw-py-3 tw-rounded-xl tw-font-semibold tw-shadow-lg hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-transform hover:tw-scale-105 tw-flex tw-items-center tw-justify-center tw-gap-2"
                        data-bs-toggle="modal" data-bs-target="#createAreaModal">
                        <i class="fas fa-plus"></i>
                        Nueva Área
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 xl:tw-grid-cols-4 tw-gap-6 tw-mb-8">
                <div
                    class="gir-card tw-group tw-cursor-pointer tw-transform tw-transition-all tw-duration-300 hover:tw-scale-105">
                    <div class="tw-flex tw-items-center tw-p-6">
                        <div class="tw-flex-1">
                            <div
                                class="tw-text-sm tw-font-semibold tw-text-gir-primary-600 tw-uppercase tw-tracking-wide tw-mb-2">
                                Total Áreas
                            </div>
                            <div
                                class="tw-text-3xl tw-font-bold tw-text-gray-800 tw-group-hover:tw-text-gir-primary-600 tw-transition-colors">
                                0</div>
                        </div>
                        <div
                            class="tw-bg-gradient-to-br tw-from-gir-primary-100 tw-to-gir-primary-200 tw-p-4 tw-rounded-2xl">
                            <i class="fas fa-sitemap tw-text-2xl tw-text-gir-primary-600"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="gir-card tw-group tw-cursor-pointer tw-transform tw-transition-all tw-duration-300 hover:tw-scale-105">
                    <div class="tw-flex tw-items-center tw-p-6">
                        <div class="tw-flex-1">
                            <div
                                class="tw-text-sm tw-font-semibold tw-text-green-600 tw-uppercase tw-tracking-wide tw-mb-2">
                                Áreas Activas
                            </div>
                            <div
                                class="tw-text-3xl tw-font-bold tw-text-gray-800 tw-group-hover:tw-text-green-600 tw-transition-colors">
                                0</div>
                        </div>
                        <div class="tw-bg-gradient-to-br tw-from-green-100 tw-to-green-200 tw-p-4 tw-rounded-2xl">
                            <i class="fas fa-check-circle tw-text-2xl tw-text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="gir-card tw-overflow-hidden">
                <div class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-px-6 tw-py-4">
                    <h6 class="tw-text-white tw-font-bold tw-text-lg tw-flex tw-items-center tw-gap-2 tw-m-0">
                        <i class="fas fa-list"></i>
                        Lista de Áreas
                    </h6>
                </div>
                <div class="tw-p-6">
                    <div class="tw-overflow-x-auto">
                        <table class="tw-w-full tw-bg-white tw-rounded-xl tw-overflow-hidden tw-shadow-sm" id="areasTable">
                            <thead>
                                <tr class="tw-bg-gradient-to-r tw-from-gray-50 tw-to-gray-100">
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Código</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Empresa</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Estado</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-center tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="tw-divide-y tw-divide-gray-200">
                                <!-- Los datos se cargarán dinámicamente via AJAX -->
                                <tr>
                                    <td colspan="6" class="tw-text-center tw-py-12">
                                        <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                            <i class="fas fa-info-circle tw-text-4xl tw-text-gray-400"></i>
                                            <p class="tw-text-gray-600 tw-font-medium">No hay áreas registradas</p>
                                            <p class="tw-text-gray-500 tw-text-sm">Comience creando una nueva área o
                                                cargando desde Excel</p>
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

    <!-- Modal para crear área -->
    <div class="modal fade" id="createAreaModal" tabindex="-1" aria-labelledby="createAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content tw-rounded-2xl tw-border-0 tw-shadow-2xl tw-overflow-hidden">
                <div class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-px-6 tw-py-4">
                    <h5 class="tw-text-white tw-font-bold tw-text-xl tw-flex tw-items-center tw-gap-2 tw-m-0"
                        id="createAreaModalLabel">
                        <i class="fas fa-sitemap"></i>
                        Nueva Área
                    </h5>
                    <button type="button" class="btn-close btn-close-white tw-float-right" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="createAreaForm">
                    <div class="tw-p-6">
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                            <div>
                                <label for="codigo" class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                    Código <span class="tw-text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                    id="codigo" name="codigo" placeholder="Ej: ADM, RRHH" required>
                            </div>
                            <div>
                                <label for="nombre" class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                    Nombre <span class="tw-text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                    id="nombre" name="nombre" placeholder="Nombre del área" required>
                            </div>
                        </div>
                        <div class="tw-mt-6">
                            <label for="descripcion" class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                Descripción
                            </label>
                            <textarea
                                class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200 tw-resize-none"
                                id="descripcion" name="descripcion" rows="3" placeholder="Descripción del área y sus funciones..."></textarea>
                        </div>
                    </div>
                    <div class="tw-bg-gray-50 tw-px-6 tw-py-4 tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-justify-end">
                        <button type="button"
                            class="tw-px-6 tw-py-3 tw-bg-gray-200 hover:tw-bg-gray-300 tw-text-gray-700 tw-font-semibold tw-rounded-lg tw-transition-all tw-duration-200"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="tw-px-6 tw-py-3 tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 hover:tw-from-gir-primary-600 hover:tw-to-gir-gold-600 tw-text-white tw-font-semibold tw-rounded-lg tw-shadow-lg tw-transition-all tw-duration-200 tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Área
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cargar áreas -->
    <div class="modal fade" id="uploadAreasModal" tabindex="-1" aria-labelledby="uploadAreasModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content tw-rounded-2xl tw-border-0 tw-shadow-2xl tw-overflow-hidden">
                <div class="tw-bg-gradient-to-r tw-from-green-500 tw-to-green-600 tw-px-6 tw-py-4">
                    <h5 class="tw-text-white tw-font-bold tw-text-xl tw-flex tw-items-center tw-gap-2 tw-m-0"
                        id="uploadAreasModalLabel">
                        <i class="fas fa-upload"></i>
                        Cargar Áreas desde Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white tw-float-right" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="uploadAreasForm" enctype="multipart/form-data">
                    <div class="tw-p-6">
                        <div class="tw-mb-6">
                            <label for="archivo_areas"
                                class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                Archivo Excel <span class="tw-text-red-500">*</span>
                            </label>
                            <input type="file"
                                class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-green-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                id="archivo_areas" name="archivo_areas" accept=".xlsx,.xls,.csv" required>
                            <div class="tw-text-sm tw-text-gray-500 tw-mt-2">
                                Formatos soportados: .xlsx, .xls, .csv
                            </div>
                        </div>
                        <div class="tw-bg-blue-50 tw-border tw-border-blue-200 tw-rounded-lg tw-p-4">
                            <div class="tw-flex tw-items-start tw-gap-3">
                                <i class="fas fa-info-circle tw-text-blue-500 tw-mt-1"></i>
                                <div>
                                    <h6 class="tw-font-semibold tw-text-blue-800 tw-mb-2">Columnas requeridas:</h6>
                                    <ul class="tw-text-blue-700 tw-text-sm tw-list-disc tw-list-inside tw-space-y-1">
                                        <li>Código</li>
                                        <li>Nombre</li>
                                        <li>Descripción</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tw-bg-gray-50 tw-px-6 tw-py-4 tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-justify-end">
                        <button type="button"
                            class="tw-px-6 tw-py-3 tw-bg-gray-200 hover:tw-bg-gray-300 tw-text-gray-700 tw-font-semibold tw-rounded-lg tw-transition-all tw-duration-200"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="tw-px-6 tw-py-3 tw-bg-gradient-to-r tw-from-green-500 tw-to-green-600 hover:tw-from-green-600 hover:tw-to-green-700 tw-text-white tw-font-semibold tw-rounded-lg tw-shadow-lg tw-transition-all tw-duration-200 tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <i class="fas fa-upload"></i>
                            Cargar Áreas
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
            $('#areasTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                responsive: true,
                processing: true
            });

            // Manejar envío del formulario de creación
            $('#createAreaForm').on('submit', function(e) {
                e.preventDefault();
                alert('Funcionalidad en desarrollo');
                $('#createAreaModal').modal('hide');
            });

            // Manejar envío del formulario de carga
            $('#uploadAreasForm').on('submit', function(e) {
                e.preventDefault();
                alert('Funcionalidad de carga en desarrollo');
                $('#uploadAreasModal').modal('hide');
            });
        });
    </script>
@endpush
