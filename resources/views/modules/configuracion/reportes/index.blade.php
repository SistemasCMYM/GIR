@extends('layouts.dashboard')

@section('title', 'Configuración de Reportes')

@push('styles')
    <style>
        .reports-header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(23, 162, 184, 0.3);
        }

        .report-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
        }

        .section-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: white;
            font-size: 14px;
        }

        .general-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .templates-icon {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .distribution-icon {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .schedule-icon {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(23, 162, 184, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #212529;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(255, 193, 7, 0.4);
            color: #212529;
        }

        .stats-card {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(19, 132, 150, 0.1));
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #138496;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .table thead th {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            border: none;
            font-weight: 500;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.4);
        }

        .status-inactive {
            background: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.4);
        }

        .status-scheduled {
            background: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(30, 126, 52, 0.1));
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.1));
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .template-preview {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .template-preview:hover {
            border-color: #17a2b8;
            background: rgba(23, 162, 184, 0.05);
        }

        .frequency-selector {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .frequency-option {
            padding: 0.5rem 1rem;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            min-width: 120px;
        }

        .frequency-option:hover {
            border-color: #17a2b8;
            background: rgba(23, 162, 184, 0.05);
        }

        .frequency-option.active {
            border-color: #17a2b8;
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, #17a2b8, #138496);
            transition: width 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="reports-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-chart-bar me-3"></i>Configuración de Reportes
                    </h1>
                    <p class="mb-0 opacity-90">Gestiona la configuración de generación y distribución de reportes del sistema
                    </p>
                </div>
                <a href="{{ route('configuracion.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Pestañas de navegación -->
        <div class="report-card">
            <ul class="nav nav-tabs" id="reportTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates"
                        type="button" role="tab">
                        <i class="fas fa-file-alt me-2"></i>Plantillas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="distribution-tab" data-bs-toggle="tab" data-bs-target="#distribution"
                        type="button" role="tab">
                        <i class="fas fa-share-alt me-2"></i>Distribución
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule"
                        type="button" role="tab">
                        <i class="fas fa-calendar-alt me-2"></i>Programación
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4" id="reportTabContent">
                <!-- Tab General -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row">
                        <!-- Configuración General -->
                        <div class="col-md-6">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon general-icon">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    Configuración General
                                </h5>

                                <form action="{{ route('configuracion.reportes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="general">

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="reportes_automaticos"
                                            id="reportes_automaticos"
                                            {{ $configuracion['reportes_automaticos'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="reportes_automaticos">
                                            Habilitar generación automática de reportes
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="formato_predeterminado" class="form-label">Formato
                                            predeterminado</label>
                                        <select class="form-select" name="formato_predeterminado"
                                            id="formato_predeterminado">
                                            <option value="pdf"
                                                {{ ($configuracion['formato_predeterminado'] ?? 'pdf') == 'pdf' ? 'selected' : '' }}>
                                                PDF</option>
                                            <option value="excel"
                                                {{ ($configuracion['formato_predeterminado'] ?? 'pdf') == 'excel' ? 'selected' : '' }}>
                                                Excel</option>
                                            <option value="word"
                                                {{ ($configuracion['formato_predeterminado'] ?? 'pdf') == 'word' ? 'selected' : '' }}>
                                                Word</option>
                                            <option value="csv"
                                                {{ ($configuracion['formato_predeterminado'] ?? 'pdf') == 'csv' ? 'selected' : '' }}>
                                                CSV</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="calidad_imagenes" class="form-label">Calidad de imágenes</label>
                                        <select class="form-select" name="calidad_imagenes" id="calidad_imagenes">
                                            <option value="alta"
                                                {{ ($configuracion['calidad_imagenes'] ?? 'media') == 'alta' ? 'selected' : '' }}>
                                                Alta (300 DPI)</option>
                                            <option value="media"
                                                {{ ($configuracion['calidad_imagenes'] ?? 'media') == 'media' ? 'selected' : '' }}>
                                                Media (150 DPI)</option>
                                            <option value="baja"
                                                {{ ($configuracion['calidad_imagenes'] ?? 'media') == 'baja' ? 'selected' : '' }}>
                                                Baja (72 DPI)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="max_size_mb" class="form-label">Tamaño máximo (MB)</label>
                                        <input type="number" class="form-control" name="max_size_mb" id="max_size_mb"
                                            value="{{ is_numeric($configuracion['max_size_mb'] ?? 50) ? $configuracion['max_size_mb'] ?? 50 : 50 }}"
                                            min="1" max="500">
                                        <small class="text-muted">Tamaño máximo permitido para reportes generados</small>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="incluir_graficos"
                                            id="incluir_graficos"
                                            {{ $configuracion['incluir_graficos'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="incluir_graficos">
                                            Incluir gráficos automáticamente
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="col-md-6">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon general-icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </span>
                                    Estadísticas de Reportes
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['total_reportes'] ?? 0) ? $configuracion['total_reportes'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Total Reportes</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['reportes_mes'] ?? 0) ? $configuracion['reportes_mes'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Este Mes</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['plantillas_activas'] ?? 0) ? $configuracion['plantillas_activas'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Plantillas Activas</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number">
                                                {{ is_numeric($configuracion['reportes_programados'] ?? 0) ? $configuracion['reportes_programados'] ?? 0 : 0 }}
                                            </div>
                                            <div class="stats-label">Programados</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Espacio de Almacenamiento</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ is_numeric($configuracion['espacio_usado'] ?? 35) ? $configuracion['espacio_usado'] ?? 35 : 35 }}%">
                                        </div>
                                    </div>
                                    <small
                                        class="text-muted">{{ is_numeric($configuracion['espacio_usado'] ?? 35) ? $configuracion['espacio_usado'] ?? 35 : 35 }}%
                                        del espacio utilizado</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="generarReporteManual()">
                                        <i class="fas fa-file-download me-2"></i>Generar Reporte Manual
                                    </button>
                                    <button class="btn btn-warning" onclick="limpiarReportesAntiguos()">
                                        <i class="fas fa-trash-alt me-2"></i>Limpiar Reportes Antiguos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Plantillas -->
                <div class="tab-pane fade" id="templates" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon templates-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    Plantillas de Reportes
                                </h5>

                                @if (!empty($configuracion['plantillas']) && is_array($configuracion['plantillas']))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Tipo</th>
                                                    <th>Formato</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['plantillas'] as $plantilla)
                                                    <tr>
                                                        <td class="fw-bold">
                                                            {{ is_string($plantilla['nombre'] ?? '') ? $plantilla['nombre'] ?? 'Sin nombre' : 'Sin nombre' }}
                                                        </td>
                                                        <td>{{ is_string($plantilla['tipo'] ?? '') ? $plantilla['tipo'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-info">{{ is_string($plantilla['formato'] ?? '') ? strtoupper($plantilla['formato'] ?? 'PDF') : 'PDF' }}</span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="status-indicator {{ $plantilla['activa'] ?? false ? 'status-active' : 'status-inactive' }}"></span>
                                                            <span
                                                                class="badge {{ $plantilla['activa'] ?? false ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $plantilla['activa'] ?? false ? 'Activa' : 'Inactiva' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-primary btn-sm me-1"
                                                                onclick="editarPlantilla('{{ is_string($plantilla['id'] ?? '') ? $plantilla['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-success btn-sm me-1"
                                                                onclick="previsualizarPlantilla('{{ is_string($plantilla['id'] ?? '') ? $plantilla['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="eliminarPlantilla('{{ is_string($plantilla['id'] ?? '') ? $plantilla['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay plantillas configuradas</p>
                                        <button class="btn btn-success" onclick="crearPlantilla()">
                                            <i class="fas fa-plus me-2"></i>Crear Primera Plantilla
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon templates-icon">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    Nueva Plantilla
                                </h5>

                                <form action="{{ route('configuracion.reportes.plantilla.guardar') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nombre_plantilla" class="form-label">Nombre de la plantilla</label>
                                        <input type="text" class="form-control" name="nombre_plantilla"
                                            id="nombre_plantilla" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tipo_reporte" class="form-label">Tipo de reporte</label>
                                        <select class="form-select" name="tipo_reporte" id="tipo_reporte" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="psicosocial">Psicosocial</option>
                                            <option value="hallazgos">Hallazgos</option>
                                            <option value="gestion">Gestión</option>
                                            <option value="estadisticas">Estadísticas</option>
                                            <option value="auditoria">Auditoría</option>
                                            <option value="personalizado">Personalizado</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="formato_plantilla" class="form-label">Formato</label>
                                        <select class="form-select" name="formato_plantilla" id="formato_plantilla"
                                            required>
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                            <option value="word">Word</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Secciones a incluir</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="secciones[]"
                                                value="resumen" id="seccion_resumen" checked>
                                            <label class="form-check-label" for="seccion_resumen">Resumen
                                                ejecutivo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="secciones[]"
                                                value="graficos" id="seccion_graficos" checked>
                                            <label class="form-check-label" for="seccion_graficos">Gráficos y
                                                tablas</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="secciones[]"
                                                value="detalles" id="seccion_detalles">
                                            <label class="form-check-label" for="seccion_detalles">Datos
                                                detallados</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="secciones[]"
                                                value="recomendaciones" id="seccion_recomendaciones">
                                            <label class="form-check-label"
                                                for="seccion_recomendaciones">Recomendaciones</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save me-2"></i>Crear Plantilla
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Distribución -->
                <div class="tab-pane fade" id="distribution" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon distribution-icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    Distribución por Email
                                </h5>

                                <form action="{{ route('configuracion.reportes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="distribucion">

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="distribucion_automatica"
                                            id="distribucion_automatica"
                                            {{ $configuracion['distribucion_automatica'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="distribucion_automatica">
                                            Habilitar distribución automática
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="emails_destinatarios" class="form-label">Emails destinatarios</label>
                                        <textarea class="form-control" name="emails_destinatarios" id="emails_destinatarios" rows="4"
                                            placeholder="ejemplo@empresa.com&#10;otro@empresa.com">{{ is_string($configuracion['emails_destinatarios'] ?? '') ? $configuracion['emails_destinatarios'] ?? '' : '' }}</textarea>
                                        <small class="text-muted">Un email por línea</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="asunto_email" class="form-label">Asunto del email</label>
                                        <input type="text" class="form-control" name="asunto_email" id="asunto_email"
                                            value="{{ is_string($configuracion['asunto_email'] ?? '') ? $configuracion['asunto_email'] ?? 'Reporte Automático - {fecha}' : 'Reporte Automático - {fecha}' }}">
                                        <small class="text-muted">Variables disponibles: {fecha}, {empresa}, {tipo}</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="mensaje_email" class="form-label">Mensaje del email</label>
                                        <textarea class="form-control" name="mensaje_email" id="mensaje_email" rows="3">{{ is_string($configuracion['mensaje_email'] ?? '') ? $configuracion['mensaje_email'] ?? 'Se adjunta el reporte solicitado.' : 'Se adjunta el reporte solicitado.' }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Distribución
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon distribution-icon">
                                        <i class="fas fa-cloud"></i>
                                    </span>
                                    Almacenamiento en la Nube
                                </h5>

                                <form action="{{ route('configuracion.reportes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="nube">

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="backup_nube"
                                            id="backup_nube"
                                            {{ $configuracion['backup_nube'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="backup_nube">
                                            Respaldar reportes en la nube
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="proveedor_nube" class="form-label">Proveedor de nube</label>
                                        <select class="form-select" name="proveedor_nube" id="proveedor_nube">
                                            <option value="">Seleccionar...</option>
                                            <option value="google_drive"
                                                {{ ($configuracion['proveedor_nube'] ?? '') == 'google_drive' ? 'selected' : '' }}>
                                                Google Drive</option>
                                            <option value="dropbox"
                                                {{ ($configuracion['proveedor_nube'] ?? '') == 'dropbox' ? 'selected' : '' }}>
                                                Dropbox</option>
                                            <option value="onedrive"
                                                {{ ($configuracion['proveedor_nube'] ?? '') == 'onedrive' ? 'selected' : '' }}>
                                                OneDrive</option>
                                            <option value="aws_s3"
                                                {{ ($configuracion['proveedor_nube'] ?? '') == 'aws_s3' ? 'selected' : '' }}>
                                                AWS S3</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="carpeta_nube" class="form-label">Carpeta de destino</label>
                                        <input type="text" class="form-control" name="carpeta_nube" id="carpeta_nube"
                                            value="{{ is_string($configuracion['carpeta_nube'] ?? '') ? $configuracion['carpeta_nube'] ?? '/GIR365/Reportes' : '/GIR365/Reportes' }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="retencion_dias" class="form-label">Días de retención</label>
                                        <input type="number" class="form-control" name="retencion_dias"
                                            id="retencion_dias"
                                            value="{{ is_numeric($configuracion['retencion_dias'] ?? 90) ? $configuracion['retencion_dias'] ?? 90 : 90 }}"
                                            min="1" max="365">
                                        <small class="text-muted">Días que se mantendrán los reportes en la nube</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración de Nube
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Programación -->
                <div class="tab-pane fade" id="schedule" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon schedule-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    Reportes Programados
                                </h5>

                                @if (!empty($configuracion['reportes_programados']) && is_array($configuracion['reportes_programados']))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Reporte</th>
                                                    <th>Frecuencia</th>
                                                    <th>Próxima Ejecución</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['reportes_programados'] as $reporte)
                                                    <tr>
                                                        <td class="fw-bold">
                                                            {{ is_string($reporte['nombre'] ?? '') ? $reporte['nombre'] ?? 'Sin nombre' : 'Sin nombre' }}
                                                        </td>
                                                        <td>{{ is_string($reporte['frecuencia'] ?? '') ? $reporte['frecuencia'] ?? 'N/A' : 'N/A' }}
                                                        </td>
                                                        <td>{{ is_string($reporte['proxima_ejecucion'] ?? '') ? $reporte['proxima_ejecucion'] ?? 'No programada' : 'No programada' }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="status-indicator {{ $reporte['activo'] ?? false ? 'status-active' : 'status-inactive' }}"></span>
                                                            <span
                                                                class="badge {{ $reporte['activo'] ?? false ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $reporte['activo'] ?? false ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-success btn-sm me-1"
                                                                onclick="ejecutarAhora('{{ is_string($reporte['id'] ?? '') ? $reporte['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                            <button class="btn btn-primary btn-sm me-1"
                                                                onclick="editarProgramacion('{{ is_string($reporte['id'] ?? '') ? $reporte['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="eliminarProgramacion('{{ is_string($reporte['id'] ?? '') ? $reporte['id'] ?? '' : '' }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay reportes programados</p>
                                        <button class="btn btn-success" onclick="crearProgramacion()">
                                            <i class="fas fa-plus me-2"></i>Crear Primera Programación
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="section-card">
                                <h5 class="section-title">
                                    <span class="section-icon schedule-icon">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    Nueva Programación
                                </h5>

                                <form action="{{ route('configuracion.reportes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="programacion">

                                    <div class="mb-3">
                                        <label for="nombre_programacion" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="nombre_programacion"
                                            id="nombre_programacion" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="plantilla_id" class="form-label">Plantilla</label>
                                        <select class="form-select" name="plantilla_id" id="plantilla_id" required>
                                            <option value="">Seleccionar plantilla...</option>
                                            @if (!empty($configuracion['plantillas']) && is_array($configuracion['plantillas']))
                                                @foreach ($configuracion['plantillas'] as $plantilla)
                                                    <option value="{{ $plantilla['id'] ?? '' }}">
                                                        {{ is_string($plantilla['nombre'] ?? '') ? $plantilla['nombre'] ?? 'Sin nombre' : 'Sin nombre' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Frecuencia</label>
                                        <div class="frequency-selector">
                                            <div class="frequency-option active" data-value="diario">
                                                <i class="fas fa-calendar-day d-block"></i>
                                                <small>Diario</small>
                                            </div>
                                            <div class="frequency-option" data-value="semanal">
                                                <i class="fas fa-calendar-week d-block"></i>
                                                <small>Semanal</small>
                                            </div>
                                            <div class="frequency-option" data-value="mensual">
                                                <i class="fas fa-calendar-alt d-block"></i>
                                                <small>Mensual</small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="frecuencia_programacion" id="frecuencia_programacion"
                                            value="diario">
                                    </div>

                                    <div class="mb-3">
                                        <label for="hora_ejecucion" class="form-label">Hora de ejecución</label>
                                        <input type="time" class="form-control" name="hora_ejecucion"
                                            id="hora_ejecucion" value="09:00" required>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="notificar_ejecucion"
                                            id="notificar_ejecucion" checked>
                                        <label class="form-check-label" for="notificar_ejecucion">
                                            Notificar cuando se ejecute
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-calendar-plus me-2"></i>Crear Programación
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Selector de frecuencia
        document.querySelectorAll('.frequency-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.frequency-option').forEach(opt => opt.classList.remove(
                    'active'));
                this.classList.add('active');
                document.getElementById('frecuencia_programacion').value = this.dataset.value;
            });
        });

        // Funciones para plantillas
        function crearPlantilla() {
            alert('Funcionalidad de creación de plantillas - Usar el formulario de la derecha');
        }

        function editarPlantilla(plantillaId) {
            if (plantillaId) {
                alert('Editar plantilla ID: ' + plantillaId + ' - Por implementar');
            }
        }

        function previsualizarPlantilla(plantillaId) {
            if (plantillaId) {
                window.open('{{ route('configuracion.reportes.plantilla.previsualizar') }}?id=' + plantillaId, '_blank');
            }
        }

        function eliminarPlantilla(plantillaId) {
            if (confirm('¿Estás seguro de que deseas eliminar esta plantilla?')) {
                fetch('{{ route('configuracion.reportes.plantilla.eliminar') }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            plantilla_id: plantillaId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Plantilla eliminada exitosamente');
                            location.reload();
                        } else {
                            alert('Error al eliminar la plantilla: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }

        // Funciones para programación
        function crearProgramacion() {
            alert('Funcionalidad de creación de programación - Usar el formulario de la derecha');
        }

        function ejecutarAhora(reporteId) {
            if (confirm('¿Estás seguro de que deseas ejecutar este reporte ahora?')) {
                fetch('{{ route('configuracion.reportes.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            reporte_id: reporteId,
                            seccion: 'ejecutar_ahora',
                            accion: 'ejecutar'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Reporte ejecutado exitosamente');
                            location.reload();
                        } else {
                            alert('Error al ejecutar el reporte: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }

        function editarProgramacion(reporteId) {
            if (reporteId) {
                alert('Editar programación ID: ' + reporteId + ' - Por implementar');
            }
        }

        function eliminarProgramacion(reporteId) {
            if (confirm('¿Estás seguro de que deseas eliminar esta programación?')) {
                fetch('{{ route('configuracion.reportes.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            reporte_id: reporteId,
                            seccion: 'eliminar_programacion',
                            accion: 'eliminar'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Programación eliminada exitosamente');
                            location.reload();
                        } else {
                            alert('Error al eliminar la programación: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }

        // Funciones generales
        function generarReporteManual() {
            alert('Generar reporte manual - Por implementar');
        }

        function limpiarReportesAntiguos() {
            if (confirm('¿Estás seguro de que deseas limpiar los reportes antiguos?')) {
                fetch('{{ route('configuracion.reportes.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            seccion: 'limpiar_antiguos',
                            accion: 'limpiar'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Reportes antiguos eliminados exitosamente');
                            location.reload();
                        } else {
                            alert('Error al limpiar reportes antiguos: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: ' + error.message);
                    });
            }
        }
    </script>
@endpush
