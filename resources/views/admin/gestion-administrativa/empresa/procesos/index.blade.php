@extends('layouts.dashboard')

@section('title', 'Gestión de Procesos')
@section('page-title', 'Gestión de Procesos')

@section('breadcrumb')
    <li class="tw-flex tw-items-center">
        <a href="{{ route('empresa.index') }}"
            class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-transition-colors tw-duration-200">
            Administración de Empresa
        </a>
        <i class="fas fa-chevron-right tw-mx-2 tw-text-gray-400 tw-text-sm"></i>
    </li>
    <li class="tw-text-gray-600 tw-font-medium">Gestión de Procesos</li>
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
                        <i class="fas fa-cogs tw-text-gir-primary-500"></i>
                        Gestión de Procesos
                    </h1>
                    <p class="tw-text-gray-600 tw-mt-2">Administre los procesos organizacionales y su mejora continua</p>
                </div>
                <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-w-full lg:tw-w-auto">
                    <button
                        class="tw-bg-gradient-to-r tw-from-green-500 tw-to-green-600 hover:tw-from-green-600 hover:tw-to-green-700 tw-text-white tw-px-6 tw-py-3 tw-rounded-xl tw-font-semibold tw-shadow-lg hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-transform hover:tw-scale-105 tw-flex tw-items-center tw-justify-center tw-gap-2"
                        data-bs-toggle="modal" data-bs-target="#uploadProcesosModal">
                        <i class="fas fa-upload"></i>
                        Cargar Procesos
                    </button>
                    <button
                        class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 hover:tw-from-gir-primary-600 hover:tw-to-gir-gold-600 tw-text-white tw-px-6 tw-py-3 tw-rounded-xl tw-font-semibold tw-shadow-lg hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-transform hover:tw-scale-105 tw-flex tw-items-center tw-justify-center tw-gap-2"
                        data-bs-toggle="modal" data-bs-target="#createProcesoModal">
                        <i class="fas fa-plus"></i>
                        Nuevo Proceso
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
                                Total Procesos
                            </div>
                            <div
                                class="tw-text-3xl tw-font-bold tw-text-gray-800 tw-group-hover:tw-text-gir-primary-600 tw-transition-colors">
                                0</div>
                        </div>
                        <div
                            class="tw-bg-gradient-to-br tw-from-gir-primary-100 tw-to-gir-primary-200 tw-p-4 tw-rounded-2xl">
                            <i class="fas fa-cogs tw-text-2xl tw-text-gir-primary-600"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="gir-card tw-group tw-cursor-pointer tw-transform tw-transition-all tw-duration-300 hover:tw-scale-105">
                    <div class="tw-flex tw-items-center tw-p-6">
                        <div class="tw-flex-1">
                            <div
                                class="tw-text-sm tw-font-semibold tw-text-green-600 tw-uppercase tw-tracking-wide tw-mb-2">
                                Procesos Activos
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

                <div
                    class="gir-card tw-group tw-cursor-pointer tw-transform tw-transition-all tw-duration-300 hover:tw-scale-105">
                    <div class="tw-flex tw-items-center tw-p-6">
                        <div class="tw-flex-1">
                            <div
                                class="tw-text-sm tw-font-semibold tw-text-yellow-600 tw-uppercase tw-tracking-wide tw-mb-2">
                                Procesos en Revisión
                            </div>
                            <div
                                class="tw-text-3xl tw-font-bold tw-text-gray-800 tw-group-hover:tw-text-yellow-600 tw-transition-colors">
                                0</div>
                        </div>
                        <div class="tw-bg-gradient-to-br tw-from-yellow-100 tw-to-yellow-200 tw-p-4 tw-rounded-2xl">
                            <i class="fas fa-hourglass-half tw-text-2xl tw-text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="gir-card tw-group tw-cursor-pointer tw-transform tw-transition-all tw-duration-300 hover:tw-scale-105">
                    <div class="tw-flex tw-items-center tw-p-6">
                        <div class="tw-flex-1">
                            <div class="tw-text-sm tw-font-semibold tw-text-red-600 tw-uppercase tw-tracking-wide tw-mb-2">
                                Procesos Inactivos
                            </div>
                            <div
                                class="tw-text-3xl tw-font-bold tw-text-gray-800 tw-group-hover:tw-text-red-600 tw-transition-colors">
                                0</div>
                        </div>
                        <div class="tw-bg-gradient-to-br tw-from-red-100 tw-to-red-200 tw-p-4 tw-rounded-2xl">
                            <i class="fas fa-times-circle tw-text-2xl tw-text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="gir-card tw-overflow-hidden">
                <div class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-px-6 tw-py-4">
                    <h6 class="tw-text-white tw-font-bold tw-text-lg tw-flex tw-items-center tw-gap-2 tw-m-0">
                        <i class="fas fa-list"></i>
                        Lista de Procesos
                    </h6>
                </div>
                <div class="tw-p-6">
                    <div class="tw-overflow-x-auto">
                        <table class="tw-w-full tw-bg-white tw-rounded-xl tw-overflow-hidden tw-shadow-sm"
                            id="procesosTable">
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
                                        Tipo</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Área</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Responsable</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Estado</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Última Act.</th>
                                    <th
                                        class="tw-px-6 tw-py-4 tw-text-center tw-text-xs tw-font-semibold tw-text-gray-700 tw-uppercase tw-tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="tw-divide-y tw-divide-gray-200">
                                <!-- Los datos se cargarán dinámicamente via AJAX -->
                                <tr>
                                    <td colspan="8" class="tw-text-center tw-py-12">
                                        <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                            <i class="fas fa-info-circle tw-text-4xl tw-text-gray-400"></i>
                                            <p class="tw-text-gray-600 tw-font-medium">No hay procesos registrados</p>
                                            <p class="tw-text-gray-500 tw-text-sm">Comience creando un nuevo proceso o
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

    <!-- Modal para crear proceso -->
    <div class="modal fade" id="createProcesoModal" tabindex="-1" aria-labelledby="createProcesoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content tw-rounded-2xl tw-border-0 tw-shadow-2xl tw-overflow-hidden">
                <div class="tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-px-6 tw-py-4">
                    <h5 class="tw-text-white tw-font-bold tw-text-xl tw-flex tw-items-center tw-gap-2 tw-m-0"
                        id="createProcesoModalLabel">
                        <i class="fas fa-cogs"></i>
                        Nuevo Proceso
                    </h5>
                    <button type="button" class="btn-close btn-close-white tw-float-right" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="createProcesoForm">
                    <div class="tw-p-6">
                        <!-- Información Básica -->
                        <div class="tw-mb-8">
                            <h6
                                class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4 tw-border-b tw-border-gray-200 tw-pb-2">
                                <i class="fas fa-info-circle tw-text-gir-primary-500 tw-mr-2"></i>
                                Información Básica
                            </h6>
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                                <div>
                                    <label for="codigo"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Código <span class="tw-text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                        id="codigo" name="codigo" placeholder="Ej: PROC-001" required>
                                </div>
                                <div>
                                    <label for="nombre"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Nombre <span class="tw-text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                        id="nombre" name="nombre" placeholder="Nombre del proceso" required>
                                </div>
                            </div>
                        </div>

                        <!-- Clasificación -->
                        <div class="tw-mb-8">
                            <h6
                                class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4 tw-border-b tw-border-gray-200 tw-pb-2">
                                <i class="fas fa-tags tw-text-gir-primary-500 tw-mr-2"></i>
                                Clasificación
                            </h6>
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                                <div>
                                    <label for="tipo"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Tipo de Proceso <span class="tw-text-red-500">*</span>
                                    </label>
                                    <select
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                        id="tipo" name="tipo" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="Estratégico">Estratégico</option>
                                        <option value="Misional">Misional</option>
                                        <option value="Operativo">Operativo</option>
                                        <option value="Apoyo">Apoyo</option>
                                        <option value="Control">Control</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="area_id"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Área <span class="tw-text-red-500">*</span>
                                    </label>
                                    <select
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                        id="area_id" name="area_id" required>
                                        <option value="">Seleccionar área</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="responsable"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Responsable <span class="tw-text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                        id="responsable" name="responsable" placeholder="Nombre del responsable"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="tw-mb-8">
                            <h6
                                class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4 tw-border-b tw-border-gray-200 tw-pb-2">
                                <i class="fas fa-file-alt tw-text-gir-primary-500 tw-mr-2"></i>
                                Descripción y Objetivos
                            </h6>
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                                <div>
                                    <label for="objetivo"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Objetivo
                                    </label>
                                    <textarea
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200 tw-resize-none"
                                        id="objetivo" name="objetivo" rows="3" placeholder="Objetivo del proceso..."></textarea>
                                </div>
                                <div>
                                    <label for="descripcion"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Descripción
                                    </label>
                                    <textarea
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200 tw-resize-none"
                                        id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada del proceso..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="tw-mb-6">
                            <h6
                                class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4 tw-border-b tw-border-gray-200 tw-pb-2">
                                <i class="fas fa-chart-line tw-text-gir-primary-500 tw-mr-2"></i>
                                Información Adicional
                            </h6>
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                                <div>
                                    <label for="indicadores"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Indicadores Clave
                                    </label>
                                    <textarea
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200 tw-resize-none"
                                        id="indicadores" name="indicadores" rows="2" placeholder="Ej: Tiempo, Calidad, Costo"></textarea>
                                </div>
                                <div>
                                    <label for="recursos"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Recursos Necesarios
                                    </label>
                                    <textarea
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200 tw-resize-none"
                                        id="recursos" name="recursos" rows="2" placeholder="Ej: Personal, Equipos, Software"></textarea>
                                </div>
                                <div>
                                    <label for="normativa"
                                        class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                        Marco Normativo
                                    </label>
                                    <textarea
                                        class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-gir-primary-500 focus:tw-border-transparent tw-transition-all tw-duration-200 tw-resize-none"
                                        id="normativa" name="normativa" rows="2" placeholder="Ej: ISO 9001, Ley específica"></textarea>
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
                            class="tw-px-6 tw-py-3 tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 hover:tw-from-gir-primary-600 hover:tw-to-gir-gold-600 tw-text-white tw-font-semibold tw-rounded-lg tw-shadow-lg tw-transition-all tw-duration-200 tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Proceso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cargar procesos -->
    <div class="modal fade" id="uploadProcesosModal" tabindex="-1" aria-labelledby="uploadProcesosModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content tw-rounded-2xl tw-border-0 tw-shadow-2xl tw-overflow-hidden">
                <div class="tw-bg-gradient-to-r tw-from-green-500 tw-to-green-600 tw-px-6 tw-py-4">
                    <h5 class="tw-text-white tw-font-bold tw-text-xl tw-flex tw-items-center tw-gap-2 tw-m-0"
                        id="uploadProcesosModalLabel">
                        <i class="fas fa-upload"></i>
                        Cargar Procesos desde Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white tw-float-right" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="uploadProcesosForm" enctype="multipart/form-data">
                    <div class="tw-p-6">
                        <div class="tw-mb-6">
                            <label for="archivo_procesos"
                                class="tw-block tw-text-sm tw-font-semibold tw-text-gray-700 tw-mb-2">
                                Archivo Excel <span class="tw-text-red-500">*</span>
                            </label>
                            <input type="file"
                                class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-green-500 focus:tw-border-transparent tw-transition-all tw-duration-200"
                                id="archivo_procesos" name="archivo_procesos" accept=".xlsx,.xls,.csv" required>
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
                                        <li>Tipo</li>
                                        <li>Área</li>
                                        <li>Responsable</li>
                                        <li>Objetivo</li>
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
                            Cargar Procesos
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
            $('#procesosTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                responsive: true,
                processing: true
            });

            // Manejar envío del formulario de creación
            $('#createProcesoForm').on('submit', function(e) {
                e.preventDefault();
                alert('Funcionalidad en desarrollo');
                $('#createProcesoModal').modal('hide');
            });

            // Manejar envío del formulario de carga
            $('#uploadProcesosForm').on('submit', function(e) {
                e.preventDefault();
                alert('Funcionalidad de carga en desarrollo');
                $('#uploadProcesosModal').modal('hide');
            });
        });
    </script>
@endpush
