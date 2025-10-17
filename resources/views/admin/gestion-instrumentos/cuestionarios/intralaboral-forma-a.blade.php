@extends('layouts.dashboard')

@section('title', 'Cuestionario Intralaboral Forma A')

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
                    <i class="fas fa-briefcase"></i> Intralaboral Forma A
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-briefcase text-primary"></i>
                            Cuestionario Intralaboral Forma A
                        </h1>
                        <p class="text-muted mb-0">
                            Factores de riesgo psicosocial intralaboral para profesionales (123 ítems)
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
                            <span class="text-muted" id="progreso-texto">Segmento 1 de 15</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 6.67%"
                                id="barra-progreso"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($preguntas->isNotEmpty())
            <!-- Formulario del cuestionario -->
            <form id="cuestionarioForm">
                @csrf
                <input type="hidden" name="tipo_cuestionario" value="intralaboral_a">

                @php
                    $segmentos = [
                        [
                            'inicio' => 1,
                            'fin' => 12,
                            'titulo' => 'Condiciones ambientales del lugar de trabajo',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con las condiciones ambientales del(los) sitio(s) o lugar(es) donde habitualmente realiza su trabajo.',
                        ],
                        [
                            'inicio' => 13,
                            'fin' => 15,
                            'titulo' => 'Cantidad de trabajo',
                            'descripcion' =>
                                'Para responder a las siguientes preguntas piense en la cantidad de trabajo que usted tiene a cargo.',
                        ],
                        [
                            'inicio' => 16,
                            'fin' => 21,
                            'titulo' => 'Esfuerzo mental',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con el esfuerzo mental que le exige su trabajo.',
                        ],
                        [
                            'inicio' => 22,
                            'fin' => 30,
                            'titulo' => 'Responsabilidades y actividades',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con las responsabilidades y actividades que usted debe hacer en su trabajo.',
                        ],
                        [
                            'inicio' => 31,
                            'fin' => 38,
                            'titulo' => 'Jornada de trabajo',
                            'descripcion' => 'Las siguientes preguntas están relacionadas con la jornada de trabajo.',
                        ],
                        [
                            'inicio' => 39,
                            'fin' => 47,
                            'titulo' => 'Decisiones y control',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con las decisiones y el control que le permite su trabajo.',
                        ],
                        [
                            'inicio' => 48,
                            'fin' => 52,
                            'titulo' => 'Cambios en el trabajo',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con cualquier tipo de cambio que ocurra en su trabajo.',
                        ],
                        [
                            'inicio' => 53,
                            'fin' => 59,
                            'titulo' => 'Información sobre el trabajo',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con la información que la empresa le ha dado sobre su trabajo.',
                        ],
                        [
                            'inicio' => 60,
                            'fin' => 62,
                            'titulo' => 'Formación y capacitación',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con la formación y capacitación que la empresa le facilita para hacer su trabajo.',
                        ],
                        [
                            'inicio' => 63,
                            'fin' => 75,
                            'titulo' => 'Relación con jefes',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con el o los jefes con quien tenga más contacto.',
                        ],
                        [
                            'inicio' => 76,
                            'fin' => 89,
                            'titulo' => 'Relaciones interpersonales',
                            'descripcion' =>
                                'Las siguientes preguntas indagan sobre las relaciones con otras personas y el apoyo entre las personas de su trabajo.',
                        ],
                        [
                            'inicio' => 90,
                            'fin' => 94,
                            'titulo' => 'Retroalimentación del rendimiento',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con la información que usted recibe sobre su rendimiento en el trabajo.',
                        ],
                        [
                            'inicio' => 95,
                            'fin' => 105,
                            'titulo' => 'Satisfacción y reconocimiento',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con la satisfacción, reconocimiento y la seguridad que le ofrece su trabajo.',
                        ],
                        [
                            'inicio' => 106,
                            'fin' => 114,
                            'titulo' => 'Atención a clientes y usuarios',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con la atención a clientes y usuarios.',
                            'condicional' => true,
                            'pregunta_filtro' => 'En mi trabajo debo brindar servicio a clientes o usuarios',
                        ],
                        [
                            'inicio' => 115,
                            'fin' => 123,
                            'titulo' => 'Supervisión de personal',
                            'descripcion' =>
                                'Las siguientes preguntas están relacionadas con las personas que usted supervisa o dirige.',
                            'condicional' => true,
                            'pregunta_filtro' => 'Soy jefe de otras personas en mi trabajo',
                        ],
                    ];
                @endphp

                @foreach ($segmentos as $segmentoIndex => $segmento)
                    <div class="segmento-preguntas" id="segmento-{{ $segmentoIndex }}"
                        style="{{ $segmentoIndex == 0 ? '' : 'display: none;' }}">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard-check text-primary"></i>
                                    Preguntas de la {{ $segmento['inicio'] }} a la {{ $segmento['fin'] }}:
                                    {{ $segmento['titulo'] }}
                                </h5>
                                <p class="text-muted mb-0 mt-2">
                                    {{ $segmento['descripcion'] }}
                                </p>
                                @if (isset($segmento['condicional']) && $segmento['condicional'])
                                    <div class="alert alert-info mt-3 mb-0">
                                        <strong>{{ $segmento['pregunta_filtro'] }}:</strong>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input pregunta-filtro" type="radio"
                                                    name="filtro_{{ $segmentoIndex }}" value="si"
                                                    data-segmento="{{ $segmentoIndex }}">
                                                <label class="form-check-label">SÍ</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input pregunta-filtro" type="radio"
                                                    name="filtro_{{ $segmentoIndex }}" value="no"
                                                    data-segmento="{{ $segmentoIndex }}">
                                                <label class="form-check-label">NO</label>
                                            </div>
                                        </div>
                                        <div id="mensaje-filtro-{{ $segmentoIndex }}" class="mt-2"
                                            style="display: none;">
                                            <small class="text-muted">Si su respuesta fue NO, pase al siguiente
                                                segmento.</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body" id="preguntas-segmento-{{ $segmentoIndex }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-primary">
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
                                            @foreach ($preguntas as $index => $pregunta)
                                                @if ($pregunta->consecutivo >= $segmento['inicio'] && $pregunta->consecutivo <= $segmento['fin'])
                                                    <tr>
                                                        <td class="text-center align-middle">
                                                            <strong>{{ $pregunta->consecutivo }}</strong>
                                                        </td>
                                                        <td class="align-middle">{{ $pregunta->enunciado }}</td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}"
                                                                value="4" style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}"
                                                                value="3" style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}"
                                                                value="2" style="transform: scale(1.2);" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <input type="radio" class="form-check-input"
                                                                name="pregunta_{{ $pregunta->consecutivo }}"
                                                                value="1" style="transform: scale(1.2);" required>
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
                            <button type="button" class="btn btn-primary" id="btn-siguiente"
                                onclick="segmentoSiguiente()">
                                Siguiente <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> No hay preguntas disponibles</h6>
                        <p>Actualmente no hay preguntas cargadas para el cuestionario Intralaboral Forma A.</p>
                        <hr>
                        <p class="mb-0">
                            <small class="text-muted">
                                Contacta al administrador del sistema para cargar las preguntas del cuestionario.
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información del Cuestionario Intralaboral Forma A</h6>
                    <ul class="mb-0">
                        <li>Este cuestionario contiene <strong>123 preguntas</strong> dirigidas a profesionales, analistas,
                            especialistas y supervisores.</li>
                        <li>Las preguntas están organizadas en <strong>15 segmentos temáticos</strong> según el manual
                            oficial.</li>
                        <li>Algunos segmentos son condicionales (atención a clientes y supervisión de personal).</li>
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
                // Scroll al top
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
                // Scroll al top
                window.scrollTo(0, 0);
            }
        }

        function enviarCuestionario() {
            // Implementar envío del cuestionario
            alert('Cuestionario completado. Pendiente implementar lógica de guardado.');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar preguntas filtro
            document.querySelectorAll('.pregunta-filtro').forEach(input => {
                input.addEventListener('change', function() {
                    const segmento = this.getAttribute('data-segmento');
                    const valor = this.value;
                    const mensajeFiltro = document.getElementById(`mensaje-filtro-${segmento}`);
                    const preguntasSegmento = document.getElementById(
                        `preguntas-segmento-${segmento}`);

                    if (valor === 'no') {
                        mensajeFiltro.style.display = 'block';
                        preguntasSegmento.style.display = 'none';
                    } else {
                        mensajeFiltro.style.display = 'none';
                        preguntasSegmento.style.display = 'block';
                    }
                });
            });

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

        .table-primary th {
            background-color: #cfe2ff;
            border-color: #b6d7ff;
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

        .badge {
            font-size: 0.75rem;
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
