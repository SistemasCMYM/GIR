@extends('layouts.dashboard')

@section('title', 'Evaluación Psicosocial - ' . $diagnostico->descripcion)

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empleados.index') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('psicosocial.index') }}">
                        <i class="fas fa-brain"></i> Psicosocial
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-clipboard-list"></i> Evaluación
                </li>
            </ol>
        </nav>
        <!-- Encabezado del diagnóstico -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-2">{{ $diagnostico->descripcion }}</h4>
                                <p class="text-muted mb-0">
                                    <i
                                        class="fas fa-building me-2"></i>{{ $diagnostico->empresa_nombre ?? 'Empresa no definida' }}
                                    <span class="mx-3">|</span>
                                    <i class="fas fa-calendar me-2"></i>Creado:
                                    {{ $diagnostico->fecha_formateada ?? 'Sin fecha' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('psicosocial.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                                <a href="{{ route('psicosocial.resumen', $diagnostico->id) }}" class="btn btn-info">
                                    <i class="fas fa-chart-bar me-2"></i>Ver Resumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Diagnóstico -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Información del Diagnóstico 202525
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $diagnostico->filtro ? 'info' : 'secondary' }}">
                                {{ $diagnostico->filtro ? 'Con Filtro' : 'Sin Filtro' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <td>
                                        {{ $diagnostico->datos ?? '' }}
                                    </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descripción:</strong></td>
                                        <td>{{ $diagnostico->descripcion ?? 'Sin descripción' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Clave:</strong></td>
                                        <td><code>{{ $diagnostico->clave ?? 'N/A' }}</code></td>
                                    </tr>
                                    @if ($profesional)
                                        <tr>
                                            <td><strong>Profesional a cargo:</strong></td>
                                            <td>{{ $profesional->nombre }} {{ $profesional->apellidos }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    @if ($empresa)
                                        <tr>
                                            <td><strong>Empresa:</strong></td>
                                            <td>{{ $empresa->nombre }}</td>
                                        </tr>
                                    @endif
                                    @if ($diagnostico->filtro && $diagnostico->filtro_key)
                                        <tr>
                                            <td><strong>Filtro por contrato:</strong></td>
                                            <td><span class="badge badge-info">{{ $diagnostico->filtro_key }}</span></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Estado:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $diagnostico->cierre ? 'success' : 'warning' }}">
                                                {{ $diagnostico->cierre ? 'Cerrado' : 'Activo' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha de creación:</strong></td>
                                        <td>{{ $diagnostico->created_at ? $diagnostico->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if ($diagnostico->observaciones)
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <strong>Observaciones:</strong>
                                    <p class="text-muted">{{ $diagnostico->observaciones }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas generales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['total'] }}</h3>
                        <p class="mb-0">Total Empleados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['completados'] }}</h3>
                        <p class="mb-0">Completados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $estadisticas['pendientes'] }}</h3>
                        <p class="mb-0">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ number_format($estadisticas['porcentaje_general'], 1) }}%</h3>
                        <p class="mb-0">Progreso</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta principal: Evaluaciones asignadas a empleados -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Evaluaciones Asignadas a Empleados
                        </h5>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="exportarExcel()">
                                <i class="fas fa-file-excel me-1"></i>Excel
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($empleados->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" id="empleadosTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Empleado</th>
                                            <th>Identificación</th>
                                            <th>Email</th>
                                            <th>Cargo</th>
                                            <th>Nivel Riesgo</th>
                                            <th>Puntaje</th>
                                            <th>Estados</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($empleados as $empleado)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-gradient-info rounded-circle me-3">
                                                            <span class="text-white font-weight-bold">
                                                                {{ strtoupper(substr($empleado['nombre'], 0, 2)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $empleado['nombre'] }}</h6>
                                                            <small
                                                                class="text-muted">{{ $empleado['empleado_id'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $empleado['dni'] }}</td>
                                                <td>
                                                    <span class="text-sm">{{ $empleado['email'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-sm">{{ $empleado['cargo'] }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $riesgoConfig = match ($empleado['nivel_riesgo']) {
                                                            'Sin Riesgo' => [
                                                                'class' => 'text-white',
                                                                'style' => 'background-color: #008235;',
                                                            ],
                                                            'Bajo' => [
                                                                'class' => 'text-white',
                                                                'style' => 'background-color: #00D364;',
                                                            ],
                                                            'Medio' => [
                                                                'class' => 'text-dark',
                                                                'style' => 'background-color: #FFD600;',
                                                            ],
                                                            'Alto' => [
                                                                'class' => 'text-white',
                                                                'style' => 'background-color: #DA3D3D;',
                                                            ],
                                                            'Muy Alto' => [
                                                                'class' => 'text-white',
                                                                'style' => 'background-color: #D10000;',
                                                            ],
                                                            default => [
                                                                'class' => 'text-white',
                                                                'style' => 'background-color: #6c757d;',
                                                            ],
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $riesgoConfig['class'] }}"
                                                        style="{{ $riesgoConfig['style'] }}">{{ $empleado['nivel_riesgo'] }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-sm font-weight-bold">{{ $empleado['puntaje'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ $empleado['porcentaje'] }}%"
                                                            aria-valuenow="{{ $empleado['porcentaje'] }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $empleado['porcentaje'] }}%
                                                        ({{ count(array_filter([$empleado['datos'], $empleado['intralaboral'], $empleado['extralaboral'], $empleado['estres']], fn($estado) => $estado === 'completado')) }}/4)
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="verDetalleEmpleado('{{ $empleado['id'] }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if (!$empleado['completado'])
                                                            <a href="{{ route('psicosocial.evaluacion', [$diagnostico->id, $empleado['id']]) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay empleados asignados</h5>
                                <p class="text-muted">Aún no se han asignado empleados a esta evaluación.</p>
                            </div>
                        @endif
                    </div>
                    <!-- Lista de Hojas de Evaluación -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-users"></i>
                                        Hojas de Evaluación ({{ $hojas->count() }})
                                    </h3>
                                    <div class="card-tools">
                                        <div class="progress" style="width: 200px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $estadisticas['porcentaje_completado'] }}%">
                                                {{ $estadisticas['porcentaje_completado'] }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    @if ($hojas->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Empleado</th>
                                                        <th>Documento</th>
                                                        <th>Cargo</th>
                                                        <th>Contrato</th>
                                                        <th>Datos</th>
                                                        <th>Intralaboral</th>
                                                        <th>Extralaboral</th>
                                                        <th>Estrés</th>
                                                        <th>Progreso</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($hojas as $hoja)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $hoja->nombre ?: 'Sin nombre' }}</strong>
                                                            </td>
                                                            <td>{{ $hoja->numero_documento ?: 'N/A' }}</td>
                                                            <td>{{ $hoja->cargo ?: 'N/A' }}</td>
                                                            <td>
                                                                @if ($hoja->contrato_key)
                                                                    <span
                                                                        class="badge badge-info">{{ $hoja->contrato_key }}</span>
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php $estadoDatos = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->datos ?? null) : ($hoja->datos ?? null); @endphp
                                                                {!! function_exists('obtenerBadgeEstado')
                                                                    ? obtenerBadgeEstado($hoja->datos ?? null)
                                                                    : ($estadoDatos === 'completado'
                                                                        ? '<span class="badge bg-success text-white">Completado</span>'
                                                                        : ($estadoDatos === 'en_progreso'
                                                                            ? '<span class="badge bg-warning text-dark">En Progreso</span>'
                                                                            : ($estadoDatos === 'pendiente'
                                                                                ? '<span class="badge bg-secondary text-white">Pendiente</span>'
                                                                                : '<span class="badge bg-info text-white">' . e($estadoDatos) . '</span>'))) !!}
                                                                <br>

                                                            </td>
                                                            <td>
                                                                @php $estadoIntralaboral = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->intralaboral ?? null) : ($hoja->intralaboral ?? null); @endphp
                                                                {!! function_exists('obtenerBadgeEstado')
                                                                    ? obtenerBadgeEstado($hoja->intralaboral ?? null)
                                                                    : ($estadoIntralaboral === 'completado'
                                                                        ? '<span class="badge badge-success">Completado</span>'
                                                                        : ($estadoIntralaboral === 'en_proceso' || $estadoIntralaboral === 'en_progreso'
                                                                            ? '<span class="badge badge-warning">En Progreso</span>'
                                                                            : ($estadoIntralaboral === 'pendiente'
                                                                                ? '<span class="badge badge-secondary">Pendiente</span>'
                                                                                : '<span class="badge badge-info">' . e($estadoIntralaboral) . '</span>'))) !!}
                                                            </td>
                                                            <td>
                                                                @php $estadoExtralaboral = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->extralaboral ?? null) : ($hoja->extralaboral ?? null); @endphp
                                                                {!! function_exists('obtenerBadgeEstado')
                                                                    ? obtenerBadgeEstado($hoja->extralaboral ?? null)
                                                                    : ($estadoExtralaboral === 'completado'
                                                                        ? '<span class="badge badge-success">Completado</span>'
                                                                        : ($estadoExtralaboral === 'en_proceso' || $estadoExtralaboral === 'en_progreso'
                                                                            ? '<span class="badge badge-warning">En Progreso</span>'
                                                                            : ($estadoExtralaboral === 'pendiente'
                                                                                ? '<span class="badge badge-secondary">Pendiente</span>'
                                                                                : '<span class="badge badge-info">' . e($estadoExtralaboral) . '</span>'))) !!}
                                                            </td>
                                                            <td>
                                                                @php $estadoEstres = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->estres ?? null) : ($hoja->estres ?? null); @endphp
                                                                {!! function_exists('obtenerBadgeEstado')
                                                                    ? obtenerBadgeEstado($hoja->estres ?? null)
                                                                    : ($estadoEstres === 'completado'
                                                                        ? '<span class="badge badge-success">Completado</span>'
                                                                        : ($estadoEstres === 'en_proceso' || $estadoEstres === 'en_progreso'
                                                                            ? '<span class="badge badge-warning">En Progreso</span>'
                                                                            : ($estadoEstres === 'pendiente'
                                                                                ? '<span class="badge badge-secondary">Pendiente</span>'
                                                                                : '<span class="badge badge-info">' . e($estadoEstres) . '</span>'))) !!}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $completados = 0;
                                                                    $estadoDatos = function_exists(
                                                                        'obtenerEstadoOficial',
                                                                    )
                                                                        ? obtenerEstadoOficial($hoja->datos ?? null)
                                                                        : $hoja->datos ?? null;
                                                                    $estadoIntralaboral = function_exists(
                                                                        'obtenerEstadoOficial',
                                                                    )
                                                                        ? obtenerEstadoOficial(
                                                                            $hoja->intralaboral ?? null,
                                                                        )
                                                                        : $hoja->intralaboral ?? null;
                                                                    $estadoExtralaboral = function_exists(
                                                                        'obtenerEstadoOficial',
                                                                    )
                                                                        ? obtenerEstadoOficial(
                                                                            $hoja->extralaboral ?? null,
                                                                        )
                                                                        : $hoja->extralaboral ?? null;
                                                                    $estadoEstres = function_exists(
                                                                        'obtenerEstadoOficial',
                                                                    )
                                                                        ? obtenerEstadoOficial($hoja->estres ?? null)
                                                                        : $hoja->estres ?? null;
                                                                    if ($estadoDatos === 'completado') {
                                                                        $completados++;
                                                                    }
                                                                    if ($estadoIntralaboral === 'completado') {
                                                                        $completados++;
                                                                    }
                                                                    if ($estadoExtralaboral === 'completado') {
                                                                        $completados++;
                                                                    }
                                                                    if ($estadoEstres === 'completado') {
                                                                        $completados++;
                                                                    }
                                                                    $porcentaje = round(($completados / 4) * 100);
                                                                @endphp
                                                                <div class="progress progress-sm">
                                                                    <div class="progress-bar bg-{{ $porcentaje == 100 ? 'success' : ($porcentaje > 0 ? 'warning' : 'secondary') }}"
                                                                        style="width: {{ $porcentaje }}%">
                                                                    </div>
                                                                </div>
                                                                <small>{{ $porcentaje }}%</small>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button" class="btn btn-info btn-sm"
                                                                        title="Ver detalles">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    @if ($hoja->completado)
                                                                        <button type="button"
                                                                            class="btn btn-success btn-sm"
                                                                            title="Ver resultados">
                                                                            <i class="fas fa-chart-bar"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay hojas de evaluación asignadas</h5>
                                            <p class="text-muted">
                                                @if ($diagnostico->filtro && $diagnostico->filtro_key)
                                                    No se encontraron empleados activos con el contrato
                                                    "{{ $diagnostico->filtro_key }}"
                                                @else
                                                    Este diagnóstico no tiene empleados asignados
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-footer">
                                    <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Volver al Listado
                                    </a>
                                    @if ($isSuperAdmin)
                                        <div class="float-right">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                title="Editar diagnóstico">
                                                <i class="fas fa-edit"></i>
                                                Editar
                                            </button>
                                            @if (!$diagnostico->cierre)
                                                <button type="button" class="btn btn-success btn-sm"
                                                    title="Cerrar diagnóstico">
                                                    <i class="fas fa-lock"></i>
                                                    Cerrar
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalle del empleado -->
    <div class="modal fade" id="detalleEmpleadoModal" tabindex="-1" aria-labelledby="detalleEmpleadoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleEmpleadoModalLabel">Detalle del Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleEmpleadoContent">
                    <!-- El contenido se carga dinámicamente -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function verDetalleEmpleado(hojaId) {
            // Mostrar loading
            $('#detalleEmpleadoContent').html(
                '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Cargando...</p></div>'
            );
            $('#detalleEmpleadoModal').modal('show');

            // Hacer petición AJAX
            fetch(`{{ route('psicosocial.show', $diagnostico->id) }}/empleado/${hojaId}`)
                .then(response => response.json())
                .then(data => {
                    const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información Personal</h6>
                            <ul class="list-unstyled">
                                <li><strong>Nombre:</strong> ${data.nombre}</li>
                                <li><strong>DNI:</strong> ${data.dni}</li>
                                <li><strong>Email:</strong> ${data.email}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Estado de Evaluaciones</h6>
                            <ul class="list-unstyled">
                                <li><strong>Datos:</strong> <span class="badge bg-${data.estados.datos === 'completado' ? 'success' : 'warning'}">${data.estados.datos}</span></li>
                                <li><strong>Intralaboral:</strong> <span class="badge bg-${data.estados.intralaboral === 'completado' ? 'success' : 'warning'}">${data.estados.intralaboral}</span></li>
                                <li><strong>Extralaboral:</strong> <span class="badge bg-${data.estados.extralaboral === 'completado' ? 'success' : 'warning'}">${data.estados.extralaboral}</span></li>
                                <li><strong>Estrés:</strong> <span class="badge bg-${data.estados.estres === 'completado' ? 'success' : 'warning'}">${data.estados.estres}</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Resultados</h6>
                            <p><strong>Nivel de Riesgo:</strong> ${data.nivel_riesgo}</p>
                            <p><strong>Puntaje Total:</strong> ${data.puntaje_total}</p>
                            <p><strong>Progreso:</strong> ${data.porcentaje_completado}%</p>
                        </div>
                    </div>
                `;
                    $('#detalleEmpleadoContent').html(content);
                })
                .catch(error => {
                    $('#detalleEmpleadoContent').html(
                        '<div class="alert alert-danger">Error al cargar los datos del empleado.</div>');
                });
        }

        function exportarExcel() {
            window.location.href = `{{ route('psicosocial.exportar.excel', $diagnostico->id) }}`;
        }

        function exportarPDF() {
            window.location.href = `{{ route('psicosocial.exportar.pdf', $diagnostico->id) }}`;
        }

        // Inicializar DataTable si está disponible
        $(document).ready(function() {
            if (typeof $.fn.dataTable !== 'undefined') {
                $('#empleadosTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 25,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [6, 7]
                    }]
                });
            }
        });
    </script>
@endsection
