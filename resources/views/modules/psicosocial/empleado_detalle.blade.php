@extends('layouts.dashboard')

@section('title', 'Detalle del Empleado')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-user text-info"></i>
                            {{ $hoja->nombre ?: 'Empleado Sin Nombre' }}
                        </h1>
                        <p class="text-muted">
                            Diagnóstico: {{ $diagnostico->descripcion }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('psicosocial.show', $diagnostico->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Diagnóstico
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Empleado -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Información Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <td>
                                {{ $diagnostico->datos ?? '' }}
                            </td>
                            </tr>
                            <tr>
                                <td><strong>DNI:</strong></td>
                                <td>{{ $hoja->dni ?: 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Área:</strong></td>
                                <td>{{ $hoja->area_label ?: 'No especificada' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Centro:</strong></td>
                                <td>{{ $hoja->centro_label ?: 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sede:</strong></td>
                                <td>{{ $hoja->sede_label ?: 'No especificada' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Contrato:</strong></td>
                                <td>{{ $hoja->contrato_label ?: 'No especificado' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>Estado de Evaluaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center mb-3">
                                <div class="p-3">
                                    <h6>Datos Personales</h6>
                                    @php $estadoDatos = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->datos ?? null) : ($hoja->datos ?? null); @endphp
                                    {!! function_exists('obtenerBadgeEstado')
                                        ? obtenerBadgeEstado($hoja->datos ?? null)
                                        : ($estadoDatos === 'completado'
                                            ? '<span class="badge bg-success text-white badge-lg">Completado</span>'
                                            : ($estadoDatos === 'en_progreso'
                                                ? '<span class="badge bg-warning text-dark badge-lg">En Progreso</span>'
                                                : ($estadoDatos === 'pendiente'
                                                    ? '<span class="badge bg-secondary text-white badge-lg">Pendiente</span>'
                                                    : '<span class="badge bg-info text-white badge-lg">' . e($estadoDatos) . '</span>'))) !!}
                                </div>
                            </div>
                            <div class="col-6 text-center mb-3">
                                <div class="p-3">
                                    <h6>Intralaboral</h6>
                                    @php $estadoIntralaboral = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->intralaboral ?? null) : ($hoja->intralaboral ?? null); @endphp
                                    {!! function_exists('obtenerBadgeEstado')
                                        ? obtenerBadgeEstado($hoja->intralaboral ?? null)
                                        : ($estadoIntralaboral === 'completado'
                                            ? '<span class="badge bg-success badge-lg">Completado</span>'
                                            : ($estadoIntralaboral === 'en_proceso' || $estadoIntralaboral === 'en_progreso'
                                                ? '<span class="badge bg-info badge-lg">En Progreso</span>'
                                                : ($estadoIntralaboral === 'pendiente'
                                                    ? '<span class="badge bg-warning badge-lg">Pendiente</span>'
                                                    : '<span class="badge bg-info badge-lg">' . e($estadoIntralaboral) . '</span>'))) !!}
                                </div>
                            </div>
                            <div class="col-6 text-center mb-3">
                                <div class="p-3">
                                    <h6>Extralaboral</h6>
                                    @php $estadoExtralaboral = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->extralaboral ?? null) : ($hoja->extralaboral ?? null); @endphp
                                    {!! function_exists('obtenerBadgeEstado')
                                        ? obtenerBadgeEstado($hoja->extralaboral ?? null)
                                        : ($estadoExtralaboral === 'completado'
                                            ? '<span class="badge bg-success badge-lg">Completado</span>'
                                            : ($estadoExtralaboral === 'en_proceso' || $estadoExtralaboral === 'en_progreso'
                                                ? '<span class="badge bg-info badge-lg">En Progreso</span>'
                                                : ($estadoExtralaboral === 'pendiente'
                                                    ? '<span class="badge bg-warning badge-lg">Pendiente</span>'
                                                    : '<span class="badge bg-info badge-lg">' . e($estadoExtralaboral) . '</span>'))) !!}
                                </div>
                            </div>
                            <div class="col-6 text-center mb-3">
                                <div class="p-3">
                                    <h6>Estrés</h6>
                                    @php $estadoEstres = function_exists('obtenerEstadoOficial') ? obtenerEstadoOficial($hoja->estres ?? null) : ($hoja->estres ?? null); @endphp
                                    {!! function_exists('obtenerBadgeEstado')
                                        ? obtenerBadgeEstado($hoja->estres ?? null)
                                        : ($estadoEstres === 'completado'
                                            ? '<span class="badge bg-success badge-lg">Completado</span>'
                                            : ($estadoEstres === 'en_proceso' || $estadoEstres === 'en_progreso'
                                                ? '<span class="badge bg-info badge-lg">En Progreso</span>'
                                                : ($estadoEstres === 'pendiente'
                                                    ? '<span class="badge bg-warning badge-lg">Pendiente</span>'
                                                    : '<span class="badge bg-info badge-lg">' . e($estadoEstres) . '</span>'))) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos Adicionales -->
        @if ($datos)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Datos Demográficos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Género:</strong></td>
                                            <td>{{ ucfirst($datos->genero ?: 'No especificado') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Edad:</strong></td>
                                            <td>{{ $datos->edad ?: 'No especificada' }} años</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado Civil:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $datos->estado_civil ?: 'No especificado')) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nivel de Estudios:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $datos->nivel_estudios ?: 'No especificado')) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Profesión:</strong></td>
                                            <td>{{ $datos->profesion ?: 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estrato Social:</strong></td>
                                            <td>{{ $datos->estrato_social ?: 'No especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo de Vivienda:</strong></td>
                                            <td>{{ ucfirst($datos->tipo_vivienda ?: 'No especificada') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dependientes:</strong></td>
                                            <td>{{ $datos->dependientes_economicos ?: 0 }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tiempo Laborado:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $datos->tiempo_laborado ?: 'No especificado')) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo de Cargo:</strong></td>
                                            <td>{{ ucfirst($datos->tipo_cargo ?: 'No especificado') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo de Contrato:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $datos->tipo_contrato ?: 'No especificado')) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Horas/Día:</strong></td>
                                            <td>{{ $datos->horas_laboradas_dia ?: 'No especificado' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .badge-lg {
            font-size: 1em;
            padding: 0.5em 1em;
        }
    </style>
@endsection
