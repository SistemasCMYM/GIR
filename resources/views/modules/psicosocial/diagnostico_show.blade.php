@extends('layouts.dashboard')

@section('title', 'Detalles del Diagnóstico Psicosocial')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-brain text-info"></i>
                        Diagnóstico Psicosocial - Ver Detalles
                    </h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- BOTONES DE NAVEGACIÓN -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('psicosocial.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Inicio
                        </a>
                        <a href="{{ route('psicosocial.resumen', $diagnostico->id ?? $diagnostico->_id) }}"
                            class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> Ver Resumen
                        </a>
                        <button type="button" class="btn btn-success" onclick="mostrarPlanIntervencion()">
                            <i class="fas fa-tasks"></i> Plan de Intervención
                        </button>
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN DEL DIAGNÓSTICO -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Información del Diagnóstico
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Identificador:</dt>
                                        <dd class="col-sm-8">{{ $diagnostico->id ?? $diagnostico->_id }}</dd>

                                        <dt class="col-sm-4">Descripción:</dt>
                                        <dd class="col-sm-8">{{ $diagnostico->descripcion ?? 'No especificado' }}</dd>

                                        <dt class="col-sm-4">Clave:</dt>
                                        <dd class="col-sm-8">{{ $diagnostico->clave ?? 'No especificado' }}</dd>

                                        <dt class="col-sm-4">Empresa:</dt>
                                        <dd class="col-sm-8">{{ $diagnostico->empresa_id ?? 'No especificado' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Fecha Creación:</dt>
                                        <dd class="col-sm-8">
                                            {{ $diagnostico->created_at ? $diagnostico->created_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                        </dd>

                                        <dt class="col-sm-4">Total Hojas:</dt>
                                        <dd class="col-sm-8">{{ $estadisticas['total'] ?? 0 }}</dd>

                                        <dt class="col-sm-4">Completadas:</dt>
                                        <dd class="col-sm-8">{{ $estadisticas['completadas'] ?? 0 }}</dd>

                                        <dt class="col-sm-4">Progreso:</dt>
                                        <dd class="col-sm-8">{{ $estadisticas['progreso'] ?? 0 }}%</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESUMEN DE NIVELES DE RIESGO -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line"></i> Resumen de Evaluaciones
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted">Total</h5>
                                            <h2 class="text-primary">{{ $estadisticas['total'] ?? 0 }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted">Completadas</h5>
                                            <h2 class="text-success">{{ $estadisticas['completadas'] ?? 0 }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted">En Progreso</h5>
                                            <h2 class="text-warning">{{ $estadisticas['en_progreso'] ?? 0 }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted">Pendientes</h5>
                                            <h2 class="text-secondary">{{ $estadisticas['pendientes'] ?? 0 }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie"></i> Niveles de Riesgo
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="riesgoChart" width="400" height="200"></canvas>
                            <div class="mt-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge text-white" style="background-color: #008235;">Sin Riesgo</span>
                                    <span>{{ $nivelesRiesgo['sin_riesgo'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge text-white" style="background-color: #00D364;">Riesgo Bajo</span>
                                    <span>{{ $nivelesRiesgo['bajo'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge text-dark" style="background-color: #FFD600;">Riesgo Medio</span>
                                    <span>{{ $nivelesRiesgo['medio'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge text-white" style="background-color: #DD0505;">Riesgo Alto</span>
                                    <span>{{ $nivelesRiesgo['alto'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge text-white" style="background-color: #A30203;">Riesgo Muy
                                        Alto</span>
                                    <span>{{ $nivelesRiesgo['muy_alto'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EVALUACIONES ASIGNADAS A EMPLEADOS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">
                                <i class="fas fa-users"></i> Evaluaciones Asignadas a Empleados
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (isset($hojas) && $hojas->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Empleado</th>
                                                <th>Documento</th>
                                                <th>E-mail</th>
                                                <th>Nivel de riesgo</th>
                                                <th>Puntaje</th>
                                                <th>Datos</th>
                                                <th>Intralaboral</th>
                                                <th>Extralaboral</th>
                                                <th>Estrés</th>
                                                <th>Progreso</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $contador = 0;
                                                $limite = 50; // Límite estricto para evitar timeout
                                                $tiempo_inicio = microtime(true);
                                            @endphp
                                            @if (isset($hojas) && is_iterable($hojas))
                                                @foreach ($hojas as $hoja)
                                                    @php
                                                        // Control de tiempo y límite
                                                        $contador++;
                                                        if ($contador > $limite) {
                                                            break;
                                                        } // Salir si excede el límite

                                                        $tiempo_actual = microtime(true);
                                                        if ($tiempo_actual - $tiempo_inicio > 20) {
                                                            break;
                                                        } // Salir si toma más de 20 segundos

                                                        // Verificar que $hoja existe
                                                        if (!$hoja) {
                                                            continue;
                                                        }

                                                        $empleado = $hoja->empleado ?? null;

                                                        // Usar helpers para obtener información
                                                        $infoEmpleado = obtenerInfoEmpleado($empleado);

                                                        // CALCULAR PUNTAJE Y NIVEL DE RIESGO DIRECTAMENTE DESDE EL MODELO
                                                        try {
                                                            $puntajeTotal = $hoja->getPuntajeTotal();
                                                            $nivelRiesgo = $hoja->getNivelRiesgo();
                                                            $colorRiesgo = $hoja->getColorRiesgo();
                                                            $etiquetaRiesgo = $hoja->getEtiquetaNivelRiesgo();
                                                            $tipoForma = $hoja->intralaboral_tipo ?? 'A';
                                                            $tieneDetalles =
                                                                $hoja->intralaboral === 'completado' ||
                                                                $hoja->extralaboral === 'completado' ||
                                                                $hoja->estres === 'completado';
                                                        } catch (Exception $e) {
                                                            $puntajeTotal = 0;
                                                            $nivelRiesgo = 'sin_calcular';
                                                            $colorRiesgo = '#6c757d';
                                                            $etiquetaRiesgo = 'Sin Calcular';
                                                            $tipoForma = 'A';
                                                            $tieneDetalles = false;
                                                        }

                                                        // VALIDACIÓN ULTRA-ROBUSTA PARA ESTADOS
                                                        // Obtener datos directamente desde la colección hojas
                                                        $datosRaw = $hoja->datos ?? null;
                                                        $intralaboralRaw = $hoja->intralaboral ?? null;
                                                        $extralaboralRaw = $hoja->extralaboral ?? null;
                                                        $estresRaw = $hoja->estres ?? null;

                                                        // Para progreso, usar estados normalizados
                                                        $estadoDatos = obtenerEstadoOficial($datosRaw);
                                                        $estadoIntralaboral = obtenerEstadoOficial($intralaboralRaw);
                                                        $estadoExtralaboral = obtenerEstadoOficial($extralaboralRaw);
                                                        $estadoEstres = obtenerEstadoOficial($estresRaw);

                                                        // Calcular progreso usando función helper
                                                        $progreso_info = calcularProgresoEvaluacion($hoja);
                                                        $progreso = $progreso_info['porcentaje'];
                                                    @endphp
                                                    <tr>
                                                        <td>{!! $infoEmpleado['nombre'] !!}</td>
                                                        <td>{!! $infoEmpleado['documento'] !!}</td>
                                                        <td>{!! $infoEmpleado['email'] !!}</td>
                                                        <td>
                                                            <!-- Nivel de Riesgo con Badge -->
                                                            @if ($nivelRiesgo && $nivelRiesgo !== 'sin_calcular')
                                                                <span class="badge text-white"
                                                                    style="background-color: {{ $colorRiesgo }}; font-size: 11px; padding: 3px 8px;">
                                                                    {{ $etiquetaRiesgo }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-secondary"
                                                                    style="font-size: 11px; padding: 3px 8px;">
                                                                    Sin evaluar [{{ $nivelRiesgo }}]
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <!-- Puntaje Transformado -->
                                                            @if ($puntajeTotal > 0)
                                                                <strong
                                                                    style="color: {{ $colorRiesgo }}; font-size: 14px;">
                                                                    {{ number_format($puntajeTotal, 1) }}
                                                                </strong>
                                                                @if ($tieneDetalles)
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        Forma {{ $tipoForma }}
                                                                    </small>
                                                                @endif
                                                            @else
                                                                <span class="text-muted" style="font-size: 12px;">
                                                                    [{{ $puntajeTotal }}]
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                // VALIDACIÓN CORRECTA DEL ESTADO DE DATOS
                                                                // Ahora que eliminamos la relación conflictiva datos(), podemos acceder directamente al campo
                                                                $estadoDatos = $hoja->datos ?? 'pendiente';

                                                                // Normalizar el estado si es boolean (por compatibilidad)
                                                                if (is_bool($estadoDatos)) {
                                                                    $estadoDatos = $estadoDatos
                                                                        ? 'completado'
                                                                        : 'pendiente';
                                                                }

                                                                // Generar badge según el estado (tres estados para datos)
                                                                $badgeDatos = match ($estadoDatos) {
                                                                    'completado'
                                                                        => '<span class="badge bg-success text-white">Completado</span>',
                                                                    'en_progreso'
                                                                        => '<span class="badge bg-warning text-dark">En Progreso</span>',
                                                                    default
                                                                        => '<span class="badge bg-secondary text-white">Pendiente</span>',
                                                                };
                                                            @endphp
                                                            {!! $badgeDatos !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                // ESTADO INTRALABORAL desde el campo directo de la hoja
                                                                $estadoIntralaboral =
                                                                    $hoja->intralaboral ?? 'pendiente';

                                                                // Normalizar el estado si es boolean
                                                                if (is_bool($estadoIntralaboral)) {
                                                                    $estadoIntralaboral = $estadoIntralaboral
                                                                        ? 'completado'
                                                                        : 'pendiente';
                                                                }

                                                                $badgeIntralaboral = match ($estadoIntralaboral) {
                                                                    'completado'
                                                                        => '<span class="badge bg-success text-white">Completado</span>',
                                                                    'en_progreso'
                                                                        => '<span class="badge bg-warning text-dark">En Progreso</span>',
                                                                    default
                                                                        => '<span class="badge bg-secondary text-white">Pendiente</span>',
                                                                };
                                                            @endphp
                                                            {!! $badgeIntralaboral !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                // ESTADO EXTRALABORAL desde el campo directo de la hoja
                                                                $estadoExtralaboral =
                                                                    $hoja->extralaboral ?? 'pendiente';

                                                                // Normalizar el estado si es boolean
                                                                if (is_bool($estadoExtralaboral)) {
                                                                    $estadoExtralaboral = $estadoExtralaboral
                                                                        ? 'completado'
                                                                        : 'pendiente';
                                                                }

                                                                $badgeExtralaboral = match ($estadoExtralaboral) {
                                                                    'completado'
                                                                        => '<span class="badge bg-success text-white">Completado</span>',
                                                                    'en_progreso'
                                                                        => '<span class="badge bg-warning text-dark">En Progreso</span>',
                                                                    default
                                                                        => '<span class="badge bg-secondary text-white">Pendiente</span>',
                                                                };
                                                            @endphp
                                                            {!! $badgeExtralaboral !!}
                                                        </td>
                                                        <td>
                                                            @php
                                                                // ESTADO ESTRÉS desde el campo directo de la hoja
                                                                $estadoEstres = $hoja->estres ?? 'pendiente';

                                                                // Normalizar el estado si es boolean
                                                                if (is_bool($estadoEstres)) {
                                                                    $estadoEstres = $estadoEstres
                                                                        ? 'completado'
                                                                        : 'pendiente';
                                                                }

                                                                $badgeEstres = match ($estadoEstres) {
                                                                    'completado'
                                                                        => '<span class="badge bg-success text-white">Completado</span>',
                                                                    'en_progreso'
                                                                        => '<span class="badge bg-warning text-dark">En Progreso</span>',
                                                                    default
                                                                        => '<span class="badge bg-secondary text-white">Pendiente</span>',
                                                                };
                                                            @endphp
                                                            {!! $badgeEstres !!}
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width: {{ $progreso }}%;"
                                                                    aria-valuenow="{{ $progreso }}" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                    {{ number_format($progreso, 0) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    onclick="verTest('{{ $hoja->id ?? $hoja->_id }}')"
                                                                    title="Ver Test">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-success btn-sm"
                                                                    onclick="resumenIndividual('{{ $hoja->id ?? $hoja->_id }}')"
                                                                    title="Resumen Individual">
                                                                    <i class="fas fa-chart-bar"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-warning btn-sm"
                                                                    onclick="copiarLink('{{ $hoja->link ?? '' }}')"
                                                                    title="Copiar Link">
                                                                    <i class="fas fa-link"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paginación -->
                                @if (method_exists($hojas, 'links'))
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $hojas->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">No hay evaluaciones asignadas</h4>
                                    <p class="text-muted">No se han registrado evaluaciones para este diagnóstico aún.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Plan de Intervención -->
    <div class="modal fade" id="planIntervencionModal" tabindex="-1" aria-labelledby="planIntervencionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="planIntervencionModalLabel">
                        <i class="fas fa-tasks"></i> Plan de Intervención - Planes de Acción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user"></i> Plan Individual
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="verPlanesIndividuales()">
                                            <i class="fas fa-user-check"></i> Ver Planes Individuales
                                        </button>
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="crearPlanIndividual()">
                                            <i class="fas fa-plus"></i> Crear Plan Individual
                                        </button>
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="reportePlanesIndividuales()">
                                            <i class="fas fa-file-pdf"></i> Generar Reporte Individual
                                        </button>
                                    </div>
                                    <hr>
                                    <div class="alert alert-info small">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Planes Individuales:</strong> Dirigidos a empleados específicos según su
                                        nivel de riesgo psicosocial.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-users"></i> Plan Grupal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-success"
                                            onclick="verPlanesGrupales()">
                                            <i class="fas fa-users-cog"></i> Ver Planes Grupales
                                        </button>
                                        <button type="button" class="btn btn-outline-success"
                                            onclick="crearPlanGrupal()">
                                            <i class="fas fa-plus"></i> Crear Plan Grupal
                                        </button>
                                        <button type="button" class="btn btn-outline-success"
                                            onclick="reportePlanesGrupales()">
                                            <i class="fas fa-file-pdf"></i> Generar Reporte Grupal
                                        </button>
                                    </div>
                                    <hr>
                                    <div class="alert alert-success small">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Planes Grupales:</strong> Dirigidos a grupos de empleados o departamentos
                                        completos.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Planes Existentes -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list"></i> Resumen de Planes de Intervención
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">0</h5>
                                                    <p class="card-text small">Planes Individuales</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">0</h5>
                                                    <p class="card-text small">Planes Grupales</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">0</h5>
                                                    <p class="card-text small">En Progreso</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">0</h5>
                                                    <p class="card-text small">Completados</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Chart.js y plugins para gráficas 3D -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar gráfica de niveles de riesgo
            const ctx = document.getElementById('riesgoChart').getContext('2d');
            const riesgoChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sin Riesgo', 'Riesgo Bajo', 'Riesgo Medio', 'Riesgo Alto', 'Riesgo Muy Alto'],
                    datasets: [{
                        data: [
                            {{ $nivelesRiesgo['sin_riesgo'] ?? 0 }},
                            {{ $nivelesRiesgo['bajo'] ?? 0 }},
                            {{ $nivelesRiesgo['medio'] ?? 0 }},
                            {{ $nivelesRiesgo['alto'] ?? 0 }},
                            {{ $nivelesRiesgo['muy_alto'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#008235',
                            '#00D364',
                            '#FFD600',
                            '#DD0505',
                            '#A30203'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });

        function mostrarPlanIntervencion() {
            // Mostrar modal del Plan de Intervención
            const modal = new bootstrap.Modal(document.getElementById('planIntervencionModal'));
            modal.show();
        }

        function verTest(hojaId) {
            // Redirigir a la vista de test
            window.location.href = '/psicosocial/evaluacion/' + hojaId;
        }

        function resumenIndividual(hojaId) {
            // Redirigir al resumen individual
            window.location.href = '/psicosocial/resumen/' + hojaId;
        }

        function copiarLink(link) {
            if (link) {
                navigator.clipboard.writeText(link).then(function() {
                    alert('Link copiado al portapapeles');
                }, function() {
                    alert('Error al copiar el link');
                });
            } else {
                alert('No hay link disponible para este empleado');
            }
        }

        // Funciones para Plan de Intervención Individual
        function verPlanesIndividuales() {
            // Redirigir a la vista de planes individuales
            window.location.href = '/psicosocial/{{ $diagnostico->id ?? $diagnostico->_id }}/planes-individuales';
        }

        function crearPlanIndividual() {
            // Redirigir a crear plan individual
            window.location.href = '/psicosocial/{{ $diagnostico->id ?? $diagnostico->_id }}/crear-plan-individual';
        }

        function reportePlanesIndividuales() {
            // Generar reporte de planes individuales
            window.open('/psicosocial/{{ $diagnostico->id ?? $diagnostico->_id }}/reporte-planes-individuales', '_blank');
        }

        // Funciones para Plan de Intervención Grupal
        function verPlanesGrupales() {
            // Redirigir a la vista de planes grupales
            window.location.href = '/psicosocial/{{ $diagnostico->id ?? $diagnostico->_id }}/planes-grupales';
        }

        function crearPlanGrupal() {
            // Redirigir a crear plan grupal
            window.location.href = '/psicosocial/{{ $diagnostico->id ?? $diagnostico->_id }}/crear-plan-grupal';
        }

        function reportePlanesGrupales() {
            // Generar reporte de planes grupales
            window.open('/psicosocial/{{ $diagnostico->id ?? $diagnostico->_id }}/reporte-planes-grupales', '_blank');
        }
    </script>
@endsection
