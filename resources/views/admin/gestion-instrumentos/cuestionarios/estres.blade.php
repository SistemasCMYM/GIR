@extends('layouts.dashboard')

@section('title', 'Cuestionario de Estrés')

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
                    <i class="fas fa-heart-pulse"></i> Estrés
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-heart-pulse text-danger"></i>
                            Cuestionario para la Evaluación del Estrés
                        </h1>
                        <p class="text-muted mb-0">
                            Tercera versión - Evaluación de síntomas de estrés (31 ítems)
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
                            <span class="text-muted" id="progreso-texto">0 de 31 preguntas respondidas</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" id="barra-progreso">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario del cuestionario -->
        <form id="cuestionario-estres">
            @csrf
            <input type="hidden" name="tipo_cuestionario" value="estres">

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-heart-pulse text-danger"></i>
                        Síntomas de Estrés
                    </h5>
                    <p class="text-muted mb-0 mt-2">
                        A continuación encontrará un listado de síntomas que pueden estar relacionados con el estrés.
                        Marque con qué frecuencia los ha sentido o experimentado durante el último mes.
                    </p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th width="5%">#</th>
                                    <th width="45%">SÍNTOMA</th>
                                    <th width="12.5%">SIEMPRE</th>
                                    <th width="12.5%">CASI SIEMPRE</th>
                                    <th width="12.5%">A VECES</th>
                                    <th width="12.5%">NUNCA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($preguntas as $index => $pregunta)
                                    <tr>
                                        <td class="text-center align-middle"><strong>{{ $pregunta->consecutivo }}</strong>
                                        </td>
                                        <td class="align-middle">{{ $pregunta->enunciado }}</td>
                                        <td class="text-center align-middle">
                                            <input type="radio" class="form-check-input pregunta-radio"
                                                name="pregunta_{{ $pregunta->consecutivo }}" value="9"
                                                style="transform: scale(1.2);" data-pregunta="{{ $pregunta->consecutivo }}"
                                                required>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="radio" class="form-check-input pregunta-radio"
                                                name="pregunta_{{ $pregunta->consecutivo }}" value="6"
                                                style="transform: scale(1.2);" data-pregunta="{{ $pregunta->consecutivo }}"
                                                required>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="radio" class="form-check-input pregunta-radio"
                                                name="pregunta_{{ $pregunta->consecutivo }}" value="3"
                                                style="transform: scale(1.2);" data-pregunta="{{ $pregunta->consecutivo }}"
                                                required>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="radio" class="form-check-input pregunta-radio"
                                                name="pregunta_{{ $pregunta->consecutivo }}" value="0"
                                                style="transform: scale(1.2);" data-pregunta="{{ $pregunta->consecutivo }}"
                                                required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Navegación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Volver a Cuestionarios
                        </a>
                        <button type="submit" class="btn btn-danger" id="btn-guardar">
                            <i class="fas fa-save"></i> Guardar Respuestas
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información del Cuestionario de Estrés</h6>
                    <ul class="mb-0">
                        <li>Este cuestionario contiene <strong>31 preguntas</strong> sobre síntomas relacionados con el
                            estrés.</li>
                        <li>Evalúa la frecuencia de síntomas experimentados durante el <strong>último mes</strong>.</li>
                        <li>Escala de puntuación: Siempre (9), Casi siempre (6), A veces (3), Nunca (0).</li>
                        <li>Las respuestas se almacenan en la colección <code>respuestas</code> de la base de datos
                            <code>psicosocial</code>.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        let preguntasRespondidas = 0;
        const totalPreguntas = {{ count($preguntas) }};

        function actualizarProgreso() {
            const porcentaje = (preguntasRespondidas / totalPreguntas) * 100;
            document.getElementById('barra-progreso').style.width = porcentaje + '%';
            document.getElementById('progreso-texto').textContent =
                `${preguntasRespondidas} de ${totalPreguntas} preguntas respondidas`;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Escuchar cambios en las respuestas
            document.querySelectorAll('.pregunta-radio').forEach(input => {
                input.addEventListener('change', function() {
                    const preguntaNumero = this.getAttribute('data-pregunta');
                    const otrasRespuestas = document.querySelectorAll(
                        `input[data-pregunta="${preguntaNumero}"]`);

                    // Verificar si esta pregunta ya había sido respondida
                    let yaRespondida = false;
                    otrasRespuestas.forEach(radio => {
                        if (radio !== this && radio.checked) {
                            yaRespondida = true;
                        }
                    });

                    // Si no estaba respondida, incrementar contador
                    if (!yaRespondida) {
                        preguntasRespondidas++;
                        actualizarProgreso();
                    }
                });
            });

            // Validación del formulario
            document.getElementById('cuestionario-estres').addEventListener('submit', function(e) {
                e.preventDefault();

                if (preguntasRespondidas < totalPreguntas) {
                    alert(
                        `Por favor responde todas las preguntas. Faltan ${totalPreguntas - preguntasRespondidas} preguntas por responder.`
                        );
                    return;
                }

                // Aquí iría la lógica para enviar el formulario
                alert('Cuestionario de estrés completado. Implementar lógica de guardado en MongoDB.');
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

        .table-danger th {
            background-color: #f8d7da;
            border-color: #f1aeb5;
        }

        .form-check-input {
            margin: 0;
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
