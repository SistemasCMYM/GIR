@extends('layouts.dashboard')

@section('title', 'Editar Encuesta')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.index') }}">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.encuestas.index') }}">
                        <i class="fas fa-poll"></i> Encuestas
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Editar Encuesta
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Editar Encuesta</h1>
                        <p class="text-muted">Modifique los datos de la encuesta</p>
                    </div>
                    <div>
                        <a href="{{ route('gestion-instrumentos.encuestas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Edición -->
        <div class="row">
            <div class="col-lg-8">
                <form id="encuestaForm" action="{{ route('gestion-instrumentos.encuestas.update', $encuesta->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Información Básica -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de la encuesta <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" required
                                    value="{{ old('nombre', $encuesta->titulo ?? $encuesta->nombre) }}"
                                    placeholder="Ej: Evaluación de Satisfacción Q4 2024">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"
                                    placeholder="Describe el objetivo y alcance de la encuesta...">{{ old('descripcion', $encuesta->descripcion) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo</label>
                                        <select class="form-select" name="tipo">
                                            <option value="satisfaccion"
                                                {{ ($encuesta->tipo ?? '') == 'satisfaccion' ? 'selected' : '' }}>
                                                Satisfacción</option>
                                            <option value="clima_laboral"
                                                {{ ($encuesta->tipo ?? '') == 'clima_laboral' ? 'selected' : '' }}>Clima
                                                Laboral</option>
                                            <option value="evaluacion_desempeño"
                                                {{ ($encuesta->tipo ?? '') == 'evaluacion_desempeño' ? 'selected' : '' }}>
                                                Evaluación de Desempeño</option>
                                            <option value="feedback_360"
                                                {{ ($encuesta->tipo ?? '') == 'feedback_360' ? 'selected' : '' }}>Feedback
                                                360°</option>
                                            <option value="cultura_organizacional"
                                                {{ ($encuesta->tipo ?? '') == 'cultura_organizacional' ? 'selected' : '' }}>
                                                Cultura Organizacional</option>
                                            <option value="personalizada"
                                                {{ ($encuesta->tipo ?? 'personalizada') == 'personalizada' ? 'selected' : '' }}>
                                                Personalizada</option>
                                            <option value="general"
                                                {{ ($encuesta->tipo ?? '') == 'general' ? 'selected' : '' }}>General
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Categoría</label>
                                        <select class="form-select" name="categoria">
                                            <option value="rrhh"
                                                {{ ($encuesta->categoria ?? '') == 'rrhh' ? 'selected' : '' }}>Recursos
                                                Humanos</option>
                                            <option value="psicosocial"
                                                {{ ($encuesta->categoria ?? '') == 'psicosocial' ? 'selected' : '' }}>
                                                Psicosocial</option>
                                            <option value="seguridad"
                                                {{ ($encuesta->categoria ?? '') == 'seguridad' ? 'selected' : '' }}>
                                                Seguridad
                                                y Salud</option>
                                            <option value="calidad"
                                                {{ ($encuesta->categoria ?? '') == 'calidad' ? 'selected' : '' }}>Calidad
                                            </option>
                                            <option value="satisfaccion"
                                                {{ ($encuesta->categoria ?? '') == 'satisfaccion' ? 'selected' : '' }}>
                                                Satisfacción</option>
                                            <option value="general"
                                                {{ ($encuesta->categoria ?? 'general') == 'general' ? 'selected' : '' }}>
                                                General</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tiempo estimado (minutos)</label>
                                        <input type="number" class="form-control" name="tiempo_estimado" min="1"
                                            max="120"
                                            value="{{ old('tiempo_estimado', $encuesta->tiempo_estimado ?? 10) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Estado</label>
                                        <select class="form-select" name="estado">
                                            <option value="1" {{ $encuesta->estado ?? false ? 'selected' : '' }}>
                                                Activa
                                            </option>
                                            <option value="0" {{ !($encuesta->estado ?? false) ? 'selected' : '' }}>
                                                Inactiva</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="plantilla" value="1"
                                    id="plantillaCheck" {{ $encuesta->plantilla ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="plantillaCheck">
                                    Guardar como plantilla
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Preguntas -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i>Preguntas
                                ({{ count($encuesta->preguntas ?? []) }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="preguntasContainer">
                                @if (isset($encuesta->preguntas) && count($encuesta->preguntas) > 0)
                                    @foreach ($encuesta->preguntas as $index => $pregunta)
                                        <div class="card mb-3 pregunta-item">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Pregunta {{ $index + 1 }}</span>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="eliminarPregunta(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold">Texto de la pregunta</label>
                                                    <input type="text" class="form-control"
                                                        name="preguntas[{{ $index }}][texto]"
                                                        value="{{ $pregunta['texto'] ?? '' }}" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Tipo</label>
                                                        <select class="form-select"
                                                            name="preguntas[{{ $index }}][tipo]">
                                                            <option value="escala_likert"
                                                                {{ ($pregunta['tipo'] ?? '') == 'escala_likert' ? 'selected' : '' }}>
                                                                Escala Likert</option>
                                                            <option value="escala_numerica"
                                                                {{ ($pregunta['tipo'] ?? '') == 'escala_numerica' ? 'selected' : '' }}>
                                                                Escala Numérica</option>
                                                            <option value="opcion_multiple"
                                                                {{ ($pregunta['tipo'] ?? '') == 'opcion_multiple' ? 'selected' : '' }}>
                                                                Opción Múltiple</option>
                                                            <option value="seleccion_multiple"
                                                                {{ ($pregunta['tipo'] ?? '') == 'seleccion_multiple' ? 'selected' : '' }}>
                                                                Selección Múltiple</option>
                                                            <option value="si_no"
                                                                {{ ($pregunta['tipo'] ?? '') == 'si_no' ? 'selected' : '' }}>
                                                                Sí
                                                                / No</option>
                                                            <option value="respuesta_abierta"
                                                                {{ ($pregunta['tipo'] ?? '') == 'respuesta_abierta' ? 'selected' : '' }}>
                                                                Respuesta Abierta</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Obligatoria</label>
                                                        <select class="form-select"
                                                            name="preguntas[{{ $index }}][obligatoria]">
                                                            <option value="1"
                                                                {{ $pregunta['obligatoria'] ?? false ? 'selected' : '' }}>
                                                                Sí
                                                            </option>
                                                            <option value="0"
                                                                {{ !($pregunta['obligatoria'] ?? false) ? 'selected' : '' }}>
                                                                No
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay preguntas configuradas. Haga clic en "Agregar Pregunta" para comenzar.
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-outline-success" onclick="agregarPregunta()">
                                <i class="fas fa-plus me-2"></i>Agregar Pregunta
                            </button>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('gestion-instrumentos.encuestas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar de Información -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Información
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>ID:</strong> {{ $encuesta->id }}</p>
                        <p class="mb-2"><strong>Creada:</strong>
                            {{ $encuesta->created_at ? $encuesta->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p class="mb-2"><strong>Modificada:</strong>
                            {{ $encuesta->updated_at ? $encuesta->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        <hr>
                        <p class="mb-2"><strong>Estado:</strong>
                            <span class="badge {{ $encuesta->estado ?? false ? 'bg-success' : 'bg-secondary' }}">
                                {{ $encuesta->estado ?? false ? 'Activa' : 'Inactiva' }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Publicada:</strong>
                            <span class="badge {{ $encuesta->publicada ?? false ? 'bg-success' : 'bg-warning' }}">
                                {{ $encuesta->publicada ?? false ? 'Sí' : 'No' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Precaución
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-0">
                            Si esta encuesta ya tiene respuestas, modificar las preguntas puede afectar el análisis de
                            datos existentes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let preguntaIndex = {{ count($encuesta->preguntas ?? []) }};

        function agregarPregunta() {
            const container = document.getElementById('preguntasContainer');
            const nuevaPregunta = `
                <div class="card mb-3 pregunta-item">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Pregunta ${preguntaIndex + 1}</span>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarPregunta(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label fw-bold">Texto de la pregunta</label>
                            <input type="text" class="form-control" name="preguntas[${preguntaIndex}][texto]" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" name="preguntas[${preguntaIndex}][tipo]">
                                    <option value="escala_likert">Escala Likert</option>
                                    <option value="escala_numerica">Escala Numérica</option>
                                    <option value="opcion_multiple">Opción Múltiple</option>
                                    <option value="seleccion_multiple">Selección Múltiple</option>
                                    <option value="si_no">Sí / No</option>
                                    <option value="respuesta_abierta">Respuesta Abierta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Obligatoria</label>
                                <select class="form-select" name="preguntas[${preguntaIndex}][obligatoria]">
                                    <option value="1">Sí</option>
                                    <option value="0" selected>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', nuevaPregunta);
            preguntaIndex++;
        }

        function eliminarPregunta(button) {
            if (confirm('¿Está seguro de eliminar esta pregunta?')) {
                button.closest('.pregunta-item').remove();
            }
        }

        // Validación del formulario
        document.getElementById('encuestaForm').addEventListener('submit', function(e) {
            const preguntas = document.querySelectorAll('.pregunta-item');
            if (preguntas.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'La encuesta debe tener al menos una pregunta.',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
        });
    </script>

    <style>
        .pregunta-item {
            transition: all 0.3s ease;
        }

        .pregunta-item:hover {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
@endsection
