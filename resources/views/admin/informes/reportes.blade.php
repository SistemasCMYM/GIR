@extends('layouts.dashboard')

@section('title', 'Reportes del Sistema')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-chart-bar"></i> Reportes del Sistema
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('informes.index') }}">Informes</a></li>
                        <li class="breadcrumb-item active">Reportes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Estadísticas de Reportes -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total_reportes_generados'] ?? 0 }}</h3>
                        <p>Reportes Generados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $estadisticas['reportes_mes_actual'] ?? 0 }}</h3>
                        <p>Este Mes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-month"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ count($estadisticas['tipos_reportes_disponibles'] ?? []) }}</h3>
                        <p>Tipos Disponibles</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>24<small class="fs-5">/7</small></h3>
                        <p>Disponibilidad</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Generación de Reportes -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle"></i> Generar Nuevo Reporte
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="reportForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_reporte">Tipo de Reporte</label>
                                        <select class="form-control" id="tipo_reporte" name="tipo_reporte" required>
                                            <option value="">Seleccionar tipo...</option>
                                            @if (isset($estadisticas['tipos_reportes_disponibles']))
                                                @foreach ($estadisticas['tipos_reportes_disponibles'] as $key => $nombre)
                                                    <option value="{{ $key }}">{{ $nombre }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="formato">Formato</label>
                                        <select class="form-control" id="formato" name="formato">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                            <option value="csv">CSV</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_inicio">Fecha Inicio</label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                            value="{{ now()->subMonth()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_fin">Fecha Fin</label>
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción (Opcional)</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                    placeholder="Descripción adicional para el reporte..."></textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Generar Reporte
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5>Tipos de Reportes Disponibles:</h5>
                        <ul class="list-unstyled">
                            @if (isset($estadisticas['tipos_reportes_disponibles']))
                                @foreach ($estadisticas['tipos_reportes_disponibles'] as $key => $nombre)
                                    <li><i class="fas fa-check text-success"></i> {{ $nombre }}</li>
                                @endforeach
                            @endif
                        </ul>

                        <hr>

                        <h5>Formatos Soportados:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-file-pdf text-danger"></i> PDF</li>
                            <li><i class="fas fa-file-excel text-success"></i> Excel</li>
                            <li><i class="fas fa-file-csv text-info"></i> CSV</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i> Últimos Reportes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="time-label">
                                <span class="bg-info">{{ now()->format('d M Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-file bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ now()->subHours(2)->format('H:i') }}</span>
                                    <h3 class="timeline-header">Reporte de Empresas</h3>
                                    <div class="timeline-body">
                                        Reporte mensual de estado de empresas
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-chart-line bg-green"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ now()->subHours(5)->format('H:i') }}</span>
                                    <h3 class="timeline-header">Reporte de Hallazgos</h3>
                                    <div class="timeline-body">
                                        Análisis de hallazgos críticos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Reportes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i> Historial de Reportes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Formato</th>
                                        <th>Estado</th>
                                        <th>Tamaño</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ now()->subHours(2)->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge bg-primary">Empresas</span></td>
                                        <td>PDF</td>
                                        <td><span class="badge bg-success">Completado</span></td>
                                        <td>2.3 MB</td>
                                        <td>
                                            <button class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ now()->subHours(5)->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge bg-warning">Hallazgos</span></td>
                                        <td>Excel</td>
                                        <td><span class="badge bg-success">Completado</span></td>
                                        <td>1.8 MB</td>
                                        <td>
                                            <button class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ now()->subDay()->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge bg-info">Psicosocial</span></td>
                                        <td>CSV</td>
                                        <td><span class="badge bg-success">Completado</span></td>
                                        <td>856 KB</td>
                                        <td>
                                            <button class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Manejo del formulario de reportes
            $('#reportForm').on('submit', function(e) {
                e.preventDefault();

                const tipoReporte = $('#tipo_reporte').val();
                if (!tipoReporte) {
                    toastr.error('Por favor seleccione un tipo de reporte');
                    return;
                }

                // Mostrar loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generando...').prop('disabled',
                    true);

                // Simular generación de reporte
                setTimeout(() => {
                    toastr.success('Reporte generado exitosamente');
                    submitBtn.html(originalText).prop('disabled', false);

                    // Resetear formulario
                    $('#reportForm')[0].reset();
                    $('#fecha_inicio').val('{{ now()->subMonth()->format('Y-m-d') }}');
                    $('#fecha_fin').val('{{ now()->format('Y-m-d') }}');
                }, 2000);
            });

            // Tooltip para botones de acciones
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
