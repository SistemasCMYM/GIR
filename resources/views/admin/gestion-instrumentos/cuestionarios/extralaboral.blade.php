@extends('layouts.dashboard')

@section('title', 'Cuestionario Extralaboral')

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
                    <a href="{{ route('gestion-instrumentos.index') }}">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}">
                        <i class="fas fa-file-alt"></i> Cuestionarios
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-home"></i> Extralaboral
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-home text-success"></i>
                            Cuestionario de Factores de Riesgo Psicosocial Extralaboral
                        </h1>
                        <p class="text-muted mb-0">
                            Evaluación de factores externos al trabajo (31 ítems)
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('gestion-instrumentos.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-clipboard-list"></i> Volver al Módulo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Progreso del cuestionario -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Progreso del cuestionario</span>
                            <span class="text-muted" id="progreso-texto">Segmento 1 de 2</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 50%" id="barra-progreso">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($preguntas->isNotEmpty())
            <!-- Formulario del cuestionario -->
            <form id="cuestionarioForm">
                @csrf
                <input type="hidden" name="tipo_cuestionario" value="extralaboral">

                @php
                    $segmentos = [
                        [
                            'inicio' => 1,
                            'fin' => 13,
                            'titulo' => 'Condiciones de la zona donde vive',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con varias condiciones de la zona donde usted vive.',
                        ],
                        [
                            'inicio' => 14,
                            'fin' => 31,
                            'titulo' => 'Vida fuera del trabajo',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con su vida fuera del trabajo.',
                        ],
                    ];
                @endphp

                @foreach ($segmentos as $segmentoIndex => $segmento)
                    <div class="segmento-preguntas" id="segmento-{{ $segmentoIndex }}"
                        style="{{ $segmentoIndex == 0 ? '' : 'display: none;' }}">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-home text-success"></i>
                                    Preguntas de la {{ $segmento['inicio'] }} a la {{ $segmento['fin'] }}:
                                    {{ $segmento['titulo'] }}
                                </h5>
                                <p class="text-muted mb-0 mt-2">
                                    {{ $segmento['descripcion'] }}
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-success">
                                            <tr class="text-center">
                                                <th width="5%">#</th>
                                                <th width="45%">PREGUNTA</th>
                                                <th width="10%">SIEMPRE</th>
                                                <th width="10%">CASI SIEMPRE</th>
                                                <th width="10%">ALGUNAS VECES</th>
                                                <th width="10%">CASI NUNCA</th>
                                                <th width="10%">NUNCA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($preguntas as $pregunta)
                                                @if ($pregunta->consecutivo >= $segmento['inicio'] && $pregunta->consecutivo <= $segmento['fin'])
                                                    <tr>
                                                        <td class="text-center align-middle">
                                                            <strong>{{ $pregunta->consecutivo }}</strong>
                                                        </td>
                                                        <td class="align-middle">{{ $pregunta->enunciado }}</td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}" value="4"
                                                                style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}" value="3"
                                                                style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}" value="2"
                                                                style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}" value="1"
                                                                style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}"
                                                                value="0" style="transform: scale(1.2);" required>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Navegación -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="btn-anterior"
                                onclick="segmentoAnterior()" disabled>
                                <i class="fas fa-arrow-left"></i> Anterior
                            </button>
                            <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}"
                                class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i> Volver a Cuestionarios
                            </a>
                            <button type="button" class="btn btn-success" id="btn-siguiente"
                                onclick="segmentoSiguiente()">
                                Siguiente <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <!-- Mensaje cuando no hay preguntas -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Sin preguntas disponibles</strong><br>
                        No se encontraron preguntas de tipo 'extralaboral' en la base de datos.
                        <br><br>
                        <small class="text-muted">
                            Contacte al administrador del sistema para cargar las preguntas del cuestionario extralaboral.
                        </small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información del Cuestionario Extralaboral</h6>
                    <ul class="mb-0">
                        <li>Este cuestionario contiene <strong>31 preguntas</strong> sobre factores externos al trabajo.
                        </li>
                        <li>Las preguntas están organizadas en <strong>2 segmentos temáticos</strong>:</li>
                        <ul>
                            <li><strong>Tiempo fuera del trabajo</strong> (preguntas 1-13)</li>
                            <li><strong>Condiciones del lugar de vivienda</strong> (preguntas 14-31)</li>
                        </ul>
                        <li>Las respuestas se almacenan en la colección <code>respuestas</code> de la base de datos
                            <code>psicosocial</code>.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        let segmentoActual = 0;
        const totalSegmentos = {{ count($segmentos) }};

        function actualizarProgreso() {
            const porcentaje = ((segmentoActual + 1) / totalSegmentos) * 100;
            document.getElementById('barra-progreso').style.width = porcentaje + '%';
            document.getElementById('progreso-texto').textContent = `Segmento ${segmentoActual + 1} de ${totalSegmentos}`;
        }

        function segmentoSiguiente() {
            if (segmentoActual < totalSegmentos - 1) {
                document.getElementById(`segmento-${segmentoActual}`).style.display = 'none';
                segmentoActual++;
                document.getElementById(`segmento-${segmentoActual}`).style.display = 'block';

                // Actualizar botones
                document.getElementById('btn-anterior').disabled = false;
                if (segmentoActual === totalSegmentos - 1) {
                    document.getElementById('btn-siguiente').innerHTML =
                        '<i class="fas fa-check"></i> Finalizar Cuestionario';
                    document.getElementById('btn-siguiente').onclick = function() {
                        enviarCuestionario();
                    };
                }

                actualizarProgreso();
                window.scrollTo(0, 0);
            }
        }

        function segmentoAnterior() {
            if (segmentoActual > 0) {
                document.getElementById(`segmento-${segmentoActual}`).style.display = 'none';
                segmentoActual--;
                document.getElementById(`segmento-${segmentoActual}`).style.display = 'block';

                // Actualizar botones
                if (segmentoActual === 0) {
                    document.getElementById('btn-anterior').disabled = true;
                }

                document.getElementById('btn-siguiente').innerHTML = 'Siguiente <i class="fas fa-arrow-right"></i>';
                document.getElementById('btn-siguiente').onclick = function() {
                    segmentoSiguiente();
                };

                actualizarProgreso();
                window.scrollTo(0, 0);
            }
        }

        function enviarCuestionario() {
            alert('Cuestionario extralaboral completado. Implementar lógica de guardado en MongoDB.');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            actualizarProgreso();
        });
    </script>

    <style>
        .table th {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            padding: 0.75rem 0.5rem;
        }

        .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }

        .table-success th {
            background-color: #d1e7dd;
            border-color: #a3cfbb;
        }

        .form-check-input {
            margin: 0;
        }

        .segmento-preguntas {
            transition: all 0.3s ease;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        .alert-info {
            background-color: #f8f9fa;
            border-left: 4px solid #0dcaf0;
            border-radius: 0.375rem;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        .table-bordered {
            border: 2px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }
    </style>
@endsection
