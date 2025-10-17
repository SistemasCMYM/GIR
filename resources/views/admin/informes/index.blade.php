@extends('layouts.dashboard')

@section('title', 'Informes y Reportes - Super Admin')

@push('styles')
    <style>
        .super-admin-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .report-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .report-header {
            background: linear-gradient(135deg, var(--report-color-1), var(--report-color-2));
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }

        .report-stats {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .chart-container {
            height: 300px;
            padding: 20px;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .export-buttons .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }

        /* Colores para diferentes tipos de reportes */
        .global-report {
            --report-color-1: #667eea;
            --report-color-2: #764ba2;
        }

        .empresa-report {
            --report-color-1: #4facfe;
            --report-color-2: #00f2fe;
        }

        .hallazgos-report {
            --report-color-1: #ff9a56;
            --report-color-2: #ff6b6b;
        }

        .psicosocial-report {
            --report-color-1: #4ecdc4;
            --report-color-2: #44a08d;
        }

        .actividad-report {
            --report-color-1: #43e97b;
            --report-color-2: #38f9d7;
        }

        .rendimiento-report {
            --report-color-1: #fa709a;
            --report-color-2: #fee140;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empleados.index') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-chart-bar"></i> Informes y Reportes
                </li>
            </ol>
        </nav>

        <!-- Header del Super Admin -->
        <div class="super-admin-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2><i class="fas fa-chart-bar mr-2"></i>Centro de Informes y Reportes</h2>
                    <p class="mb-0">Generación y análisis de reportes globales del sistema</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-light" data-toggle="modal" data-target="#programarReporteModal">
                        <i class="fas fa-clock mr-1"></i>Programar Reporte
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros Globales -->
        <div class="filter-section">
            <h5><i class="fas fa-filter mr-2"></i>Filtros Globales</h5>
            <form id="filtrosGlobales">
                <div class="row">
                    <div class="col-md-3">
                        <label for="fechaInicio">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio">
                    </div>
                    <div class="col-md-3">
                        <label for="fechaFin">Fecha Fin</label>
                        <input type="date" class="form-control" id="fechaFin" name="fecha_fin">
                    </div>
                    <div class="col-md-3">
                        <label for="empresaFiltro">Empresa</label>
                        <select class="form-control" id="empresaFiltro" name="empresa_id">
                            <option value="">Todas las empresas</option>
                            <!-- Opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="moduloFiltro">Módulo</label>
                        <select class="form-control" id="moduloFiltro" name="modulo">
                            <option value="">Todos los módulos</option>
                            <option value="hallazgos">Hallazgos</option>
                            <option value="psicosocial">Psicosocial</option>
                            <option value="planes">Planes</option>
                            <option value="indicadores">Indicadores</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                            <i class="fas fa-search mr-1"></i>Aplicar Filtros
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-times mr-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reportes Disponibles -->
        <div class="row">
            <!-- Reporte Global -->
            <div class="col-md-6 mb-4">
                <div class="report-card global-report">
                    <div class="report-header">
                        <h4><i class="fas fa-globe mr-2"></i>Reporte Global del Sistema</h4>
                        <p class="mb-0">Vista general de todas las empresas y módulos</p>
                    </div>
                    <div class="card-body">
                        <div class="report-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-primary">{{ $reportData['total_empresas'] ?? 0 }}</h3>
                                    <small>Empresas</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-success">{{ $reportData['total_usuarios'] ?? 0 }}</h3>
                                    <small>Usuarios</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-warning">{{ $reportData['total_registros'] ?? 0 }}</h3>
                                    <small>Registros</small>
                                </div>
                            </div>
                        </div>
                        <div class="export-buttons">
                            <a href="{{ route('informes.global') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Reporte
                            </a>
                            <button class="btn btn-success btn-sm" onclick="exportarReporte('global', 'excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportarReporte('global', 'pdf')">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Empresas -->
            <div class="col-md-6 mb-4">
                <div class="report-card empresa-report">
                    <div class="report-header">
                        <h4><i class="fas fa-building mr-2"></i>Reporte de Empresas</h4>
                        <p class="mb-0">Análisis detallado por empresa</p>
                    </div>
                    <div class="card-body">
                        <div class="report-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-success">{{ $reportData['empresas_activas'] ?? 0 }}</h3>
                                    <small>Activas</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-info">{{ $reportData['promedio_usuarios'] ?? 0 }}</h3>
                                    <small>Promedio Usuarios</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-primary">{{ $reportData['empresas_nuevas'] ?? 0 }}</h3>
                                    <small>Nuevas este mes</small>
                                </div>
                            </div>
                        </div>
                        <div class="export-buttons">
                            <a href="{{ route('informes.empresas') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Reporte
                            </a>
                            <button class="btn btn-success btn-sm" onclick="exportarReporte('empresas', 'excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportarReporte('empresas', 'pdf')">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Hallazgos -->
            <div class="col-md-6 mb-4">
                <div class="report-card hallazgos-report">
                    <div class="report-header">
                        <h4><i class="fas fa-exclamation-triangle mr-2"></i>Reporte de Hallazgos</h4>
                        <p class="mb-0">Análisis de hallazgos por empresa y estado</p>
                    </div>
                    <div class="card-body">
                        <div class="report-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-warning">{{ $reportData['total_hallazgos'] ?? 0 }}</h3>
                                    <small>Total</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-danger">{{ $reportData['hallazgos_pendientes'] ?? 0 }}</h3>
                                    <small>Pendientes</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-success">{{ $reportData['hallazgos_resueltos'] ?? 0 }}</h3>
                                    <small>Resueltos</small>
                                </div>
                            </div>
                        </div>
                        <div class="export-buttons">
                            <a href="{{ route('informes.hallazgos') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Reporte
                            </a>
                            <button class="btn btn-success btn-sm" onclick="exportarReporte('hallazgos', 'excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportarReporte('hallazgos', 'pdf')">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte Psicosocial -->
            <div class="col-md-6 mb-4">
                <div class="report-card psicosocial-report">
                    <div class="report-header">
                        <h4><i class="fas fa-brain mr-2"></i>Reporte Psicosocial</h4>
                        <p class="mb-0">Análisis de evaluaciones psicosociales</p>
                    </div>
                    <div class="card-body">
                        <div class="report-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-info">{{ $reportData['total_evaluaciones'] ?? 0 }}</h3>
                                    <small>Total</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-success">{{ $reportData['evaluaciones_completadas'] ?? 0 }}</h3>
                                    <small>Completadas</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-primary">{{ $reportData['promedio_riesgo'] ?? 0 }}%</h3>
                                    <small>Riesgo Promedio</small>
                                </div>
                            </div>
                        </div>
                        <div class="export-buttons">
                            <a href="{{ route('informes.psicosocial') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Reporte
                            </a>
                            <button class="btn btn-success btn-sm" onclick="exportarReporte('psicosocial', 'excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportarReporte('psicosocial', 'pdf')">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Actividad -->
            <div class="col-md-6 mb-4">
                <div class="report-card actividad-report">
                    <div class="report-header">
                        <h4><i class="fas fa-chart-line mr-2"></i>Reporte de Actividad</h4>
                        <p class="mb-0">Análisis de actividad y uso del sistema</p>
                    </div>
                    <div class="card-body">
                        <div class="report-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-success">{{ $reportData['usuarios_activos'] ?? 0 }}</h3>
                                    <small>Usuarios Activos</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-info">{{ $reportData['sesiones_mes'] ?? 0 }}</h3>
                                    <small>Sesiones/Mes</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-warning">{{ $reportData['tiempo_promedio'] ?? 0 }}h</h3>
                                    <small>Tiempo Promedio</small>
                                </div>
                            </div>
                        </div>
                        <div class="export-buttons">
                            <a href="{{ route('informes.actividad') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Reporte
                            </a>
                            <button class="btn btn-success btn-sm" onclick="exportarReporte('actividad', 'excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportarReporte('actividad', 'pdf')">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Rendimiento -->
            <div class="col-md-6 mb-4">
                <div class="report-card rendimiento-report">
                    <div class="report-header">
                        <h4><i class="fas fa-tachometer-alt mr-2"></i>Reporte de Rendimiento</h4>
                        <p class="mb-0">Métricas de rendimiento del sistema</p>
                    </div>
                    <div class="card-body">
                        <div class="report-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="text-success">{{ $reportData['tiempo_respuesta'] ?? 0 }}ms</h3>
                                    <small>Tiempo Respuesta</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-info">{{ $reportData['disponibilidad'] ?? 0 }}%</h3>
                                    <small>Disponibilidad</small>
                                </div>
                                <div class="col-4">
                                    <h3 class="text-primary">{{ $reportData['carga_sistema'] ?? 0 }}%</h3>
                                    <small>Carga Sistema</small>
                                </div>
                            </div>
                        </div>
                        <div class="export-buttons">
                            <a href="{{ route('informes.rendimiento') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Reporte
                            </a>
                            <button class="btn btn-success btn-sm" onclick="exportarReporte('rendimiento', 'excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportarReporte('rendimiento', 'pdf')">
                                <i class="fas fa-file-pdf mr-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes Programados -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-clock mr-2"></i>Reportes Programados</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Frecuencia</th>
                                        <th>Próxima Ejecución</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="reportesProgramados">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando reportes programados...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Programar Reportes -->
    <div class="modal fade" id="programarReporteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-clock mr-2"></i>Programar Nuevo Reporte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formProgramarReporte">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombreReporte">Nombre del Reporte</label>
                                    <input type="text" class="form-control" id="nombreReporte" name="nombre"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipoReporte">Tipo de Reporte</label>
                                    <select class="form-control" id="tipoReporte" name="tipo" required>
                                        <option value="global">Global</option>
                                        <option value="empresas">Empresas</option>
                                        <option value="hallazgos">Hallazgos</option>
                                        <option value="psicosocial">Psicosocial</option>
                                        <option value="actividad">Actividad</option>
                                        <option value="rendimiento">Rendimiento</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frecuencia">Frecuencia</label>
                                    <select class="form-control" id="frecuencia" name="frecuencia" required>
                                        <option value="diario">Diario</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="mensual">Mensual</option>
                                        <option value="trimestral">Trimestral</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="formatoExportacion">Formato de Exportación</label>
                                    <select class="form-control" id="formatoExportacion" name="formato" required>
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                        <option value="csv">CSV</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emailDestino">Email de Destino</label>
                            <input type="email" class="form-control" id="emailDestino" name="email" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarReporteProgramado()">
                        <i class="fas fa-save mr-1"></i>Programar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function aplicarFiltros() {
            const filtros = {
                fecha_inicio: document.getElementById('fechaInicio').value,
                fecha_fin: document.getElementById('fechaFin').value,
                empresa_id: document.getElementById('empresaFiltro').value,
                modulo: document.getElementById('moduloFiltro').value
            };

            console.log('Aplicando filtros:', filtros);
            // TODO: Implementar aplicación de filtros
        }

        function limpiarFiltros() {
            document.getElementById('filtrosGlobales').reset();
            aplicarFiltros();
        }

        function exportarReporte(tipo, formato) {
            const filtros = {
                fecha_inicio: document.getElementById('fechaInicio').value,
                fecha_fin: document.getElementById('fechaFin').value,
                empresa_id: document.getElementById('empresaFiltro').value,
                modulo: document.getElementById('moduloFiltro').value
            };

            const params = new URLSearchParams(filtros);
            const url = `{{ route('informes.exportar', '') }}/${tipo}?formato=${formato}&${params.toString()}`;
            window.open(url, '_blank');
        }

        function guardarReporteProgramado() {
            const formData = new FormData(document.getElementById('formProgramarReporte'));

            // TODO: Implementar guardado de reporte programado
            console.log('Guardando reporte programado...');
            $('#programarReporteModal').modal('hide');
        }

        function cargarEmpresas() {
            // TODO: Cargar empresas para el filtro
            console.log('Cargando empresas para filtro...');
        }

        function cargarReportesProgramados() {
            // TODO: Cargar reportes programados
            console.log('Cargando reportes programados...');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Establecer fechas por defecto (último mes)
            const hoy = new Date();
            const mesAnterior = new Date();
            mesAnterior.setMonth(hoy.getMonth() - 1);

            document.getElementById('fechaInicio').value = mesAnterior.toISOString().split('T')[0];
            document.getElementById('fechaFin').value = hoy.toISOString().split('T')[0];

            // Cargar datos iniciales
            cargarEmpresas();
            cargarReportesProgramados();
        });
    </script>
@endpush
