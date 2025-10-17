

<?php $__env->startSection('title', 'Gestión de Instrumentos'); ?>
<?php $__env->startSection('page-title', 'Gestión de Instrumentos'); ?>

<?php $__env->startSection('content'); ?>
    <div class="tw-container-fluid tw-py-6">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4">
            <div class="container-fluid">

                <nav aria-label="breadcrumb" class="tw-mb-6">
                    <ol class="breadcrumb tw-bg-transparent tw-mb-0 tw-p-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Gestión de Instrumentos
                        </li>
                    </ol>
                </nav>

                <!-- Page Title -->
                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h4 class="page-title">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Gestión de Instrumentos
                            </h4>
                            <p class="text-muted mb-0">Administración de consentimientos, cuestionarios y encuestas</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards Silva Dashboard -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="fas fa-file-signature fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0"><?php echo e($stats['total_consentimientos'] ?? 0); ?></h4>
                                        <p class="text-muted mb-0">Consentimientos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm rounded-circle bg-success">
                                            <span class="avatar-title rounded-circle bg-success">
                                                <i class="fas fa-clipboard-list fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0"><?php echo e($stats['total_cuestionarios'] ?? 0); ?></h4>
                                        <p class="text-muted mb-0">Cuestionarios</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm rounded-circle bg-info">
                                            <span class="avatar-title rounded-circle bg-info">
                                                <i class="fas fa-poll fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0"><?php echo e($stats['total_encuestas'] ?? 0); ?></h4>
                                        <p class="text-muted mb-0">Encuestas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm rounded-circle bg-warning">
                                            <span class="avatar-title rounded-circle bg-warning">
                                                <i class="fas fa-chart-line fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0"><?php echo e($stats['aplicaciones_activas'] ?? 0); ?></h4>
                                        <p class="text-muted mb-0">Aplicaciones Activas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Modules Silva Dashboard -->
                <div class="row">
                    <!-- Consentimientos -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-file-signature me-2"></i>
                                    Consentimientos
                                </h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Gestión de documentos de consentimiento informado para evaluaciones
                                    psicosociales y tratamiento de datos.</p>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Funcionalidades:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Creación y edición
                                        </li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Control de
                                            versiones
                                        </li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Plantillas
                                            predefinidas
                                        </li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Firmas digitales
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('gestion-instrumentos.consentimientos.index')); ?>"
                                        class="btn btn-primary">
                                        <i class="fas fa-list me-2"></i>Ver Consentimientos
                                    </a>
                                    <a href="<?php echo e(route('gestion-instrumentos.consentimientos.create')); ?>"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>Crear Nuevo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cuestionarios -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    Cuestionarios
                                </h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Cuestionarios oficiales de la batería de riesgo psicosocial según
                                    resolución
                                    2646 de 2008.</p>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Instrumentos disponibles:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><i class="fas fa-file-alt text-primary me-2"></i>Intralaboral
                                            Forma A
                                        </li>
                                        <li class="mb-1"><i class="fas fa-file-alt text-primary me-2"></i>Intralaboral
                                            Forma
                                            B
                                        </li>
                                        <li class="mb-1"><i class="fas fa-file-alt text-primary me-2"></i>Extralaboral
                                        </li>
                                        <li class="mb-1"><i class="fas fa-file-alt text-primary me-2"></i>Cuestionario
                                            de
                                            Estrés
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.index')); ?>"
                                        class="btn btn-success">
                                        <i class="fas fa-list me-2"></i>Ver Cuestionarios
                                    </a>
                                    <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.datos-generales')); ?>"
                                        class="btn btn-outline-success">
                                        <i class="fas fa-play me-2"></i>Aplicar Batería
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Encuestas -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-poll me-2"></i>
                                    Encuestas
                                </h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Creación y gestión de encuestas personalizadas para satisfacción,
                                    clima
                                    laboral
                                    y evaluaciones.</p>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Tipos de encuestas:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><i class="fas fa-smile text-info me-2"></i>Satisfacción laboral
                                        </li>
                                        <li class="mb-1"><i class="fas fa-users text-info me-2"></i>Clima organizacional
                                        </li>
                                        <li class="mb-1"><i class="fas fa-chart-bar text-info me-2"></i>Evaluación de
                                            desempeño
                                        </li>
                                        <li class="mb-1"><i class="fas fa-edit text-info me-2"></i>Encuestas
                                            personalizadas
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>" class="btn btn-info">
                                        <i class="fas fa-list me-2"></i>Ver Encuestas
                                    </a>
                                    <a href="<?php echo e(route('gestion-instrumentos.encuestas.create')); ?>"
                                        class="btn btn-outline-info">
                                        <i class="fas fa-plus me-2"></i>Crear Nueva
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>
                                    Acciones Rápidas
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.datos-generales')); ?>"
                                            class="btn btn-primary w-100 py-3">
                                            <i class="fas fa-user-plus mb-2 d-block" style="font-size: 2rem;"></i>
                                            Nueva Evaluación
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?php echo e(route('gestion-instrumentos.consentimientos.create')); ?>"
                                            class="btn btn-info w-100 py-3">
                                            <i class="fas fa-file-contract mb-2 d-block" style="font-size: 2rem;"></i>
                                            Nuevo Consentimiento
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.create')); ?>"
                                            class="btn btn-success w-100 py-3">
                                            <i class="fas fa-poll-h mb-2 d-block" style="font-size: 2rem;"></i>
                                            Nueva Encuesta
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-warning w-100 py-3"
                                            onclick="alert('Próximamente: Reportes y estadísticas avanzadas')">
                                            <i class="fas fa-chart-bar mb-2 d-block" style="font-size: 2rem;"></i>
                                            Reportes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/index.blade.php ENDPATH**/ ?>