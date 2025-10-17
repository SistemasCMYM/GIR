

<?php $__env->startSection('title', 'Editar Encuesta'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('dashboard')); ?>">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('gestion-instrumentos.index')); ?>">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>">
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
                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Edición -->
        <div class="row">
            <div class="col-lg-8">
                <form id="encuestaForm" action="<?php echo e(route('gestion-instrumentos.encuestas.update', $encuesta->id)); ?>"
                    method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

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
                                    value="<?php echo e(old('nombre', $encuesta->titulo ?? $encuesta->nombre)); ?>"
                                    placeholder="Ej: Evaluación de Satisfacción Q4 2024">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"
                                    placeholder="Describe el objetivo y alcance de la encuesta..."><?php echo e(old('descripcion', $encuesta->descripcion)); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo</label>
                                        <select class="form-select" name="tipo">
                                            <option value="satisfaccion"
                                                <?php echo e(($encuesta->tipo ?? '') == 'satisfaccion' ? 'selected' : ''); ?>>
                                                Satisfacción</option>
                                            <option value="clima_laboral"
                                                <?php echo e(($encuesta->tipo ?? '') == 'clima_laboral' ? 'selected' : ''); ?>>Clima
                                                Laboral</option>
                                            <option value="evaluacion_desempeño"
                                                <?php echo e(($encuesta->tipo ?? '') == 'evaluacion_desempeño' ? 'selected' : ''); ?>>
                                                Evaluación de Desempeño</option>
                                            <option value="feedback_360"
                                                <?php echo e(($encuesta->tipo ?? '') == 'feedback_360' ? 'selected' : ''); ?>>Feedback
                                                360°</option>
                                            <option value="cultura_organizacional"
                                                <?php echo e(($encuesta->tipo ?? '') == 'cultura_organizacional' ? 'selected' : ''); ?>>
                                                Cultura Organizacional</option>
                                            <option value="personalizada"
                                                <?php echo e(($encuesta->tipo ?? 'personalizada') == 'personalizada' ? 'selected' : ''); ?>>
                                                Personalizada</option>
                                            <option value="general"
                                                <?php echo e(($encuesta->tipo ?? '') == 'general' ? 'selected' : ''); ?>>General
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Categoría</label>
                                        <select class="form-select" name="categoria">
                                            <option value="rrhh"
                                                <?php echo e(($encuesta->categoria ?? '') == 'rrhh' ? 'selected' : ''); ?>>Recursos
                                                Humanos</option>
                                            <option value="psicosocial"
                                                <?php echo e(($encuesta->categoria ?? '') == 'psicosocial' ? 'selected' : ''); ?>>
                                                Psicosocial</option>
                                            <option value="seguridad"
                                                <?php echo e(($encuesta->categoria ?? '') == 'seguridad' ? 'selected' : ''); ?>>
                                                Seguridad
                                                y Salud</option>
                                            <option value="calidad"
                                                <?php echo e(($encuesta->categoria ?? '') == 'calidad' ? 'selected' : ''); ?>>Calidad
                                            </option>
                                            <option value="satisfaccion"
                                                <?php echo e(($encuesta->categoria ?? '') == 'satisfaccion' ? 'selected' : ''); ?>>
                                                Satisfacción</option>
                                            <option value="general"
                                                <?php echo e(($encuesta->categoria ?? 'general') == 'general' ? 'selected' : ''); ?>>
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
                                            value="<?php echo e(old('tiempo_estimado', $encuesta->tiempo_estimado ?? 10)); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Estado</label>
                                        <select class="form-select" name="estado">
                                            <option value="1" <?php echo e($encuesta->estado ?? false ? 'selected' : ''); ?>>
                                                Activa
                                            </option>
                                            <option value="0" <?php echo e(!($encuesta->estado ?? false) ? 'selected' : ''); ?>>
                                                Inactiva</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="plantilla" value="1"
                                    id="plantillaCheck" <?php echo e($encuesta->plantilla ?? false ? 'checked' : ''); ?>>
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
                                (<?php echo e(count($encuesta->preguntas ?? [])); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="preguntasContainer">
                                <?php if(isset($encuesta->preguntas) && count($encuesta->preguntas) > 0): ?>
                                    <?php $__currentLoopData = $encuesta->preguntas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pregunta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="card mb-3 pregunta-item">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Pregunta <?php echo e($index + 1); ?></span>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="eliminarPregunta(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold">Texto de la pregunta</label>
                                                    <input type="text" class="form-control"
                                                        name="preguntas[<?php echo e($index); ?>][texto]"
                                                        value="<?php echo e($pregunta['texto'] ?? ''); ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Tipo</label>
                                                        <select class="form-select"
                                                            name="preguntas[<?php echo e($index); ?>][tipo]">
                                                            <option value="escala_likert"
                                                                <?php echo e(($pregunta['tipo'] ?? '') == 'escala_likert' ? 'selected' : ''); ?>>
                                                                Escala Likert</option>
                                                            <option value="escala_numerica"
                                                                <?php echo e(($pregunta['tipo'] ?? '') == 'escala_numerica' ? 'selected' : ''); ?>>
                                                                Escala Numérica</option>
                                                            <option value="opcion_multiple"
                                                                <?php echo e(($pregunta['tipo'] ?? '') == 'opcion_multiple' ? 'selected' : ''); ?>>
                                                                Opción Múltiple</option>
                                                            <option value="seleccion_multiple"
                                                                <?php echo e(($pregunta['tipo'] ?? '') == 'seleccion_multiple' ? 'selected' : ''); ?>>
                                                                Selección Múltiple</option>
                                                            <option value="si_no"
                                                                <?php echo e(($pregunta['tipo'] ?? '') == 'si_no' ? 'selected' : ''); ?>>
                                                                Sí
                                                                / No</option>
                                                            <option value="respuesta_abierta"
                                                                <?php echo e(($pregunta['tipo'] ?? '') == 'respuesta_abierta' ? 'selected' : ''); ?>>
                                                                Respuesta Abierta</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Obligatoria</label>
                                                        <select class="form-select"
                                                            name="preguntas[<?php echo e($index); ?>][obligatoria]">
                                                            <option value="1"
                                                                <?php echo e($pregunta['obligatoria'] ?? false ? 'selected' : ''); ?>>
                                                                Sí
                                                            </option>
                                                            <option value="0"
                                                                <?php echo e(!($pregunta['obligatoria'] ?? false) ? 'selected' : ''); ?>>
                                                                No
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay preguntas configuradas. Haga clic en "Agregar Pregunta" para comenzar.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="btn btn-outline-success" onclick="agregarPregunta()">
                                <i class="fas fa-plus me-2"></i>Agregar Pregunta
                            </button>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between mb-4">
                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>" class="btn btn-secondary">
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
                        <p class="mb-2"><strong>ID:</strong> <?php echo e($encuesta->id); ?></p>
                        <p class="mb-2"><strong>Creada:</strong>
                            <?php echo e($encuesta->created_at ? $encuesta->created_at->format('d/m/Y H:i') : 'N/A'); ?></p>
                        <p class="mb-2"><strong>Modificada:</strong>
                            <?php echo e($encuesta->updated_at ? $encuesta->updated_at->format('d/m/Y H:i') : 'N/A'); ?></p>
                        <hr>
                        <p class="mb-2"><strong>Estado:</strong>
                            <span class="badge <?php echo e($encuesta->estado ?? false ? 'bg-success' : 'bg-secondary'); ?>">
                                <?php echo e($encuesta->estado ?? false ? 'Activa' : 'Inactiva'); ?>

                            </span>
                        </p>
                        <p class="mb-0"><strong>Publicada:</strong>
                            <span class="badge <?php echo e($encuesta->publicada ?? false ? 'bg-success' : 'bg-warning'); ?>">
                                <?php echo e($encuesta->publicada ?? false ? 'Sí' : 'No'); ?>

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
        let preguntaIndex = <?php echo e(count($encuesta->preguntas ?? [])); ?>;

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/encuestas/edit.blade.php ENDPATH**/ ?>