@extends('layouts.dashboard')

@section('title', 'Responder Encuesta')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header de la Encuesta -->
                <div class="card mb-4 border-0 shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-poll fa-3x text-primary mb-3"></i>
                            <h1 class="h3 fw-bold">{{ $encuesta->titulo ?? 'Encuesta sin título' }}</h1>
                            @if ($encuesta->descripcion)
                                <p class="text-muted lead">{{ $encuesta->descripcion }}</p>
                            @endif
                        </div>

                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border-end">
                                    <i class="fas fa-clock text-info mb-2"></i>
                                    <p class="mb-0"><strong>Tiempo estimado</strong></p>
                                    <p class="text-muted small">{{ $encuesta->tiempo_estimado ?? 10 }} minutos</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border-end">
                                    <i class="fas fa-question-circle text-warning mb-2"></i>
                                    <p class="mb-0"><strong>Preguntas</strong></p>
                                    <p class="text-muted small">{{ count($encuesta->preguntas ?? []) }} preguntas</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-shield-alt text-success mb-2"></i>
                                <p class="mb-0"><strong>Privacidad</strong></p>
                                <p class="text-muted small">{{ $encuesta->anonima ? 'Anónima' : 'Identificada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Respuestas -->
                <form id="encuestaForm"
                    action="{{ route('gestion-instrumentos.encuestas.respuesta.store', $encuesta->_id) }}" method="POST">
                    @csrf

                    <!-- Barra de Progreso -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Progreso</span>
                                <span class="text-muted small" id="progress-text">0%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-gradient-primary" id="progress-bar" role="progressbar"
                                    style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Preguntas -->
                    @if (isset($encuesta->preguntas) && count($encuesta->preguntas) > 0)
                        @foreach ($encuesta->preguntas as $index => $pregunta)
                            <div class="card mb-4 border-0 shadow-sm pregunta-card" data-pregunta="{{ $index + 1 }}">
                                <div class="card-header bg-light border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            {{ $pregunta['texto'] ?? 'Pregunta sin texto' }}
                                        </h5>
                                        @if ($pregunta['obligatoria'] ?? false)
                                            <span class="badge bg-danger">Obligatoria</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    @switch($pregunta['tipo'] ?? 'escala_likert')
                                        @case('escala_likert')
                                            <div class="likert-scale">
                                                @php
                                                    $opciones = [
                                                        1 => 'Totalmente en desacuerdo',
                                                        2 => 'En desacuerdo',
                                                        3 => 'Neutral',
                                                        4 => 'De acuerdo',
                                                        5 => 'Totalmente de acuerdo',
                                                    ];
                                                @endphp
                                                <div class="row g-2">
                                                    @foreach ($opciones as $valor => $etiqueta)
                                                        <div class="col">
                                                            <input type="radio" class="btn-check"
                                                                name="respuestas[{{ $index }}]"
                                                                id="p{{ $index }}_{{ $valor }}"
                                                                value="{{ $valor }}" onchange="actualizarProgreso()">
                                                            <label class="btn btn-outline-primary w-100 py-3"
                                                                for="p{{ $index }}_{{ $valor }}">
                                                                <div class="fw-bold">{{ $valor }}</div>
                                                                <div class="small">{{ $etiqueta }}</div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @break

                                        @case('escala_numerica')
                                            @php
                                                $min = $pregunta['configuracion']['minimo'] ?? 1;
                                                $max = $pregunta['configuracion']['maximo'] ?? 10;
                                            @endphp
                                            <div class="numeric-scale">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <span
                                                        class="text-muted small">{{ $pregunta['configuracion']['etiqueta_min'] ?? 'Mínimo' }}</span>
                                                    <span
                                                        class="text-muted small">{{ $pregunta['configuracion']['etiqueta_max'] ?? 'Máximo' }}</span>
                                                </div>
                                                <div class="row g-2">
                                                    @for ($i = $min; $i <= $max; $i++)
                                                        <div class="col">
                                                            <input type="radio" class="btn-check"
                                                                name="respuestas[{{ $index }}]"
                                                                id="p{{ $index }}_{{ $i }}"
                                                                value="{{ $i }}" onchange="actualizarProgreso()">
                                                            <label class="btn btn-outline-secondary w-100 py-3"
                                                                for="p{{ $index }}_{{ $i }}">
                                                                <div class="fw-bold">{{ $i }}</div>
                                                            </label>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        @break

                                        @case('opcion_multiple')
                                            <div class="multiple-choice">
                                                @foreach ($pregunta['opciones'] ?? ['Opción 1', 'Opción 2'] as $opcionIndex => $opcion)
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="radio"
                                                            name="respuestas[{{ $index }}]"
                                                            id="p{{ $index }}_op{{ $opcionIndex }}"
                                                            value="{{ $opcion }}" onchange="actualizarProgreso()">
                                                        <label class="form-check-label"
                                                            for="p{{ $index }}_op{{ $opcionIndex }}">
                                                            {{ $opcion }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @break

                                        @case('seleccion_multiple')
                                            <div class="multiple-selection">
                                                @foreach ($pregunta['opciones'] ?? ['Opción 1', 'Opción 2'] as $opcionIndex => $opcion)
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="respuestas[{{ $index }}][]"
                                                            id="p{{ $index }}_op{{ $opcionIndex }}"
                                                            value="{{ $opcion }}" onchange="actualizarProgreso()">
                                                        <label class="form-check-label"
                                                            for="p{{ $index }}_op{{ $opcionIndex }}">
                                                            {{ $opcion }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @break

                                        @case('si_no')
                                            <div class="yes-no">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <input type="radio" class="btn-check"
                                                            name="respuestas[{{ $index }}]" id="p{{ $index }}_si"
                                                            value="Si" onchange="actualizarProgreso()">
                                                        <label class="btn btn-outline-success w-100 py-4"
                                                            for="p{{ $index }}_si">
                                                            <i class="fas fa-check fa-2x mb-2"></i>
                                                            <div class="fw-bold">Sí</div>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="radio" class="btn-check"
                                                            name="respuestas[{{ $index }}]" id="p{{ $index }}_no"
                                                            value="No" onchange="actualizarProgreso()">
                                                        <label class="btn btn-outline-danger w-100 py-4"
                                                            for="p{{ $index }}_no">
                                                            <i class="fas fa-times fa-2x mb-2"></i>
                                                            <div class="fw-bold">No</div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @break

                                        @case('matriz_calificacion')
                                            <div class="matrix-rating">
                                                <div class="table-responsive">
                                                    <table class="table table-borderless">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                @foreach ($pregunta['columnas'] ?? ['Malo', 'Regular', 'Bueno'] as $columna)
                                                                    <th class="text-center">{{ $columna }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($pregunta['filas'] ?? ['Aspecto 1', 'Aspecto 2'] as $filaIndex => $fila)
                                                                <tr>
                                                                    <td class="fw-bold">{{ $fila }}</td>
                                                                    @foreach ($pregunta['columnas'] ?? ['Malo', 'Regular', 'Bueno'] as $colIndex => $columna)
                                                                        <td class="text-center">
                                                                            <input type="radio" class="form-check-input"
                                                                                name="respuestas[{{ $index }}][{{ $filaIndex }}]"
                                                                                value="{{ $columna }}"
                                                                                onchange="actualizarProgreso()">
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @break

                                        @case('respuesta_abierta')
                                            <div class="open-text">
                                                <textarea class="form-control" name="respuestas[{{ $index }}]" rows="4"
                                                    placeholder="Escriba su respuesta aquí..." onchange="actualizarProgreso()"></textarea>
                                                <div class="form-text">
                                                    Máximo {{ $pregunta['configuracion']['max_caracteres'] ?? 500 }} caracteres
                                                </div>
                                            </div>
                                        @break

                                        @default
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Tipo de pregunta no reconocido
                                            </div>
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4>No hay preguntas configuradas</h4>
                                <p class="text-muted">Esta encuesta aún no tiene preguntas.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Botones de Acción -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="guardarBorrador()">
                                    <i class="fas fa-save me-2"></i>Guardar Borrador
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Respuestas
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let totalPreguntas = {{ count($encuesta->preguntas ?? []) }};
        let preguntasRespondidas = 0;

        function actualizarProgreso() {
            let respondidas = 0;

            // Contar preguntas respondidas
            for (let i = 0; i < totalPreguntas; i++) {
                const preguntaInputs = document.querySelectorAll(`input[name^="respuestas[${i}]"]`);
                let tieneRespuesta = false;

                preguntaInputs.forEach(input => {
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        if (input.checked) tieneRespuesta = true;
                    } else if (input.type === 'textarea' || input.tagName === 'TEXTAREA') {
                        if (input.value.trim() !== '') tieneRespuesta = true;
                    }
                });

                if (tieneRespuesta) respondidas++;
            }

            preguntasRespondidas = respondidas;
            const progreso = totalPreguntas > 0 ? (respondidas / totalPreguntas) * 100 : 0;

            document.getElementById('progress-bar').style.width = progreso + '%';
            document.getElementById('progress-text').textContent = Math.round(progreso) + '%';
        }

        function guardarBorrador() {
            // Simular guardado de borrador
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'info',
                title: 'Borrador guardado correctamente'
            });
        }

        // Validación del formulario
        document.getElementById('encuestaForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (preguntasRespondidas === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Debe responder al menos una pregunta antes de enviar.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            Swal.fire({
                title: '¿Enviar respuestas?',
                text: 'Una vez enviadas las respuestas no podrá modificarlas.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simular envío exitoso
                    Swal.fire({
                        icon: 'success',
                        title: '¡Respuestas enviadas!',
                        text: 'Gracias por participar en la encuesta.',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        window.location.href =
                            '{{ route('gestion-instrumentos.encuestas.index') }}';
                    });
                }
            });
        });

        // Auto-guardado cada 30 segundos
        setInterval(function() {
            if (preguntasRespondidas > 0) {
                console.log('Auto-guardando borrador...');
            }
        }, 30000);
    </script>

    <style>
        .pregunta-card {
            transition: all 0.3s ease;
        }

        .pregunta-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .btn-check:checked+.btn {
            transform: scale(0.95);
        }

        .likert-scale .btn,
        .numeric-scale .btn,
        .yes-no .btn {
            transition: all 0.3s ease;
        }

        .table th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
            background: linear-gradient(90deg, #0d6efd, #6610f2);
        }

        .card {
            transition: all 0.3s ease;
        }

        textarea.form-control {
            border-radius: 8px;
            resize: vertical;
        }

        .badge {
            font-size: 0.85rem;
        }
    </style>
@endsection
