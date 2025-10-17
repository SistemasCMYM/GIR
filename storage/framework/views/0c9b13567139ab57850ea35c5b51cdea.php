

<?php $__env->startSection('title', 'Detalle de Encuesta'); ?>

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
                    Detalle
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0"><?php echo e($encuesta->titulo ?? ($encuesta->nombre ?? 'Sin título')); ?></h1>
                        <p class="text-muted"><?php echo e($encuesta->descripcion ?? 'Sin descripción'); ?></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.edit', $encuesta->id)); ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        <?php if($encuesta->publicada ?? false): ?>
                            <a href="<?php echo e(route('gestion-instrumentos.encuestas.responder', $encuesta->id)); ?>"
                                class="btn btn-success">
                                <i class="fas fa-play me-2"></i>Responder
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.informes', $encuesta->id)); ?>"
                            class="btn btn-info">
                            <i class="fas fa-chart-bar me-2"></i>Informes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <!-- Estadísticas Rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-question-circle fa-2x mb-2"></i>
                                <h3 class="mb-0"><?php echo e(count($encuesta->preguntas ?? [])); ?></h3>
                                <small>Preguntas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h3 class="mb-0">0</h3>
                                <small>Respuestas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h3 class="mb-0"><?php echo e($encuesta->tiempo_estimado ?? 10); ?></h3>
                                <small>Minutos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-percentage fa-2x mb-2"></i>
                                <h3 class="mb-0">0%</h3>
                                <small>Completitud</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preguntas -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Preguntas de la Encuesta
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($encuesta->preguntas) && count($encuesta->preguntas) > 0): ?>
                            <div class="accordion" id="preguntasAccordion">
                                <?php $__currentLoopData = $encuesta->preguntas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pregunta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header" id="heading<?php echo e($index); ?>">
                                            <button class="accordion-button <?php echo e($index > 0 ? 'collapsed' : ''); ?>"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?php echo e($index); ?>"
                                                aria-expanded="<?php echo e($index === 0 ? 'true' : 'false'); ?>"
                                                aria-controls="collapse<?php echo e($index); ?>">
                                                <span class="badge bg-primary me-2"><?php echo e($index + 1); ?></span>
                                                <strong><?php echo e($pregunta['texto'] ?? 'Pregunta sin texto'); ?></strong>
                                                <span class="ms-auto me-3">
                                                    <span
                                                        class="badge bg-secondary"><?php echo e($pregunta['tipo'] ?? 'Sin tipo'); ?></span>
                                                    <?php if($pregunta['obligatoria'] ?? false): ?>
                                                        <span class="badge bg-danger">Obligatoria</span>
                                                    <?php endif; ?>
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo e($index); ?>"
                                            class="accordion-collapse collapse <?php echo e($index === 0 ? 'show' : ''); ?>"
                                            aria-labelledby="heading<?php echo e($index); ?>"
                                            data-bs-parent="#preguntasAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Tipo:</strong>
                                                            <?php echo e(ucfirst(str_replace('_', ' ', $pregunta['tipo'] ?? 'Sin tipo'))); ?>

                                                        </p>
                                                        <p class="mb-1"><strong>Obligatoria:</strong>
                                                            <?php echo e($pregunta['obligatoria'] ?? false ? 'Sí' : 'No'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php if(isset($pregunta['configuracion'])): ?>
                                                            <p class="mb-1"><strong>Configuración:</strong></p>
                                                            <ul class="small">
                                                                <?php $__currentLoopData = $pregunta['configuracion']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <li><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>:
                                                                        <?php echo e($value); ?></li>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <?php if(isset($pregunta['opciones']) && count($pregunta['opciones']) > 0): ?>
                                                    <hr>
                                                    <p class="mb-2"><strong>Opciones:</strong></p>
                                                    <ul>
                                                        <?php $__currentLoopData = $pregunta['opciones']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li><?php echo e($opcion); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php endif; ?>

                                                <?php if(isset($pregunta['filas']) && count($pregunta['filas']) > 0): ?>
                                                    <hr>
                                                    <p class="mb-2"><strong>Filas (Matriz):</strong></p>
                                                    <ul>
                                                        <?php $__currentLoopData = $pregunta['filas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fila): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li><?php echo e($fila); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php endif; ?>

                                                <?php if(isset($pregunta['columnas']) && count($pregunta['columnas']) > 0): ?>
                                                    <p class="mb-2"><strong>Columnas (Matriz):</strong></p>
                                                    <ul>
                                                        <?php $__currentLoopData = $pregunta['columnas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li><?php echo e($columna); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Esta encuesta no tiene preguntas configuradas.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar de Información -->
            <div class="col-lg-4">
                <!-- Información General -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Información General
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>ID:</strong> <?php echo e($encuesta->id); ?></p>
                        <p class="mb-2"><strong>Tipo:</strong>
                            <?php echo e(ucfirst(str_replace('_', ' ', $encuesta->tipo ?? 'General'))); ?></p>
                        <p class="mb-2"><strong>Categoría:</strong>
                            <?php echo e(ucfirst(str_replace('_', ' ', $encuesta->categoria ?? 'General'))); ?></p>
                        <hr>
                        <p class="mb-2"><strong>Creada:</strong>
                            <?php echo e($encuesta->created_at ? $encuesta->created_at->format('d/m/Y H:i') : 'N/A'); ?></p>
                        <p class="mb-2"><strong>Modificada:</strong>
                            <?php echo e($encuesta->updated_at ? $encuesta->updated_at->format('d/m/Y H:i') : 'N/A'); ?></p>
                        <?php if($encuesta->fecha_publicacion ?? false): ?>
                            <p class="mb-0"><strong>Publicada:</strong>
                                <?php echo e(\Carbon\Carbon::parse($encuesta->fecha_publicacion)->format('d/m/Y H:i')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estado -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-toggle-on me-2"></i>Estado
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Estado:</span>
                            <span class="badge <?php echo e($encuesta->estado ?? false ? 'bg-success' : 'bg-secondary'); ?>">
                                <?php echo e($encuesta->estado ?? false ? 'Activa' : 'Inactiva'); ?>

                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Publicada:</span>
                            <span class="badge <?php echo e($encuesta->publicada ?? false ? 'bg-success' : 'bg-warning'); ?>">
                                <?php echo e($encuesta->publicada ?? false ? 'Sí' : 'No'); ?>

                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Plantilla:</span>
                            <span class="badge <?php echo e($encuesta->plantilla ?? false ? 'bg-info' : 'bg-secondary'); ?>">
                                <?php echo e($encuesta->plantilla ?? false ? 'Sí' : 'No'); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="<?php echo e(route('gestion-instrumentos.encuestas.toggle-estado', $encuesta->id)); ?>"
                                method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-toggle-<?php echo e($encuesta->estado ?? false ? 'off' : 'on'); ?> me-2"></i>
                                    <?php echo e($encuesta->estado ?? false ? 'Desactivar' : 'Activar'); ?>

                                </button>
                            </form>

                            <form action="<?php echo e(route('gestion-instrumentos.encuestas.toggle-publicacion', $encuesta->id)); ?>"
                                method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i
                                        class="fas fa-<?php echo e($encuesta->publicada ?? false ? 'eye-slash' : 'globe'); ?> me-2"></i>
                                    <?php echo e($encuesta->publicada ?? false ? 'Despublicar' : 'Publicar'); ?>

                                </button>
                            </form>

                            <a href="<?php echo e(route('gestion-instrumentos.encuestas.informes', $encuesta->id)); ?>"
                                class="btn btn-outline-info">
                                <i class="fas fa-chart-bar me-2"></i>Ver Informes
                            </a>

                            <form action="<?php echo e(route('gestion-instrumentos.encuestas.destroy', $encuesta->id)); ?>"
                                method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta encuesta?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #000;
        }

        .accordion-item {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/encuestas/show.blade.php ENDPATH**/ ?>