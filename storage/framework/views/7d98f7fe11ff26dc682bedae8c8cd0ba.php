

<?php $__env->startSection('title', 'Consentimientos'); ?>

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
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-briefcase"></i> Consentimientos
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Gestión de Consentimientos</h1>
                        <p class="text-muted">Administración de consentimientos informados</p>
                    </div>
                    <div>
                        <a href="<?php echo e(route('gestion-instrumentos.index')); ?>" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Módulo
                        </a>
                        <a href="<?php echo e(route('gestion-instrumentos.consentimientos.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nuevo Consentimiento
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de estado -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabla de Consentimientos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-signature me-2"></i>Lista de Consentimientos
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">CONSENTIMIENTO</th>
                                <th class="px-4 py-3">DETALLES</th>
                                <th class="px-4 py-3">ÍTEMS</th>
                                <th class="px-4 py-3">CREADO</th>
                                <th class="px-4 py-3">MODIFICADO</th>
                                <th class="px-4 py-3">TIPO</th>
                                <th class="px-4 py-3">ESTADO</th>
                                <th class="px-4 py-3">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $consentimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consentimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="<?php echo e(route('gestion-instrumentos.consentimientos.show', $consentimiento->_id)); ?>"
                                            class="text-decoration-none fw-bold">
                                            <?php echo e($consentimiento->titulo ?? 'Sin título'); ?>

                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e(Str::limit($consentimiento->descripcion ?? 'Sin descripción', 50)); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-info">
                                            <?php echo e($consentimiento->items_total ?? 0); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            <?php echo e($consentimiento->fecha_creacion ? $consentimiento->fecha_creacion->format('d/m/Y H:i') : 'N/A'); ?>

                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            <?php echo e($consentimiento->fecha_modificacion ? $consentimiento->fecha_modificacion->format('d/m/Y H:i') : 'N/A'); ?>

                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge <?php echo e($consentimiento->tipo_color ?? 'bg-secondary'); ?>">
                                            <?php echo e($consentimiento->tipo_texto ?? ($consentimiento->tipo ?? 'General')); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form
                                            action="<?php echo e(route('gestion-instrumentos.consentimientos.toggle-estado', $consentimiento->_id)); ?>"
                                            method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                class="btn btn-sm <?php echo e($consentimiento->estado ? 'btn-success' : 'btn-outline-danger'); ?>">
                                                <?php echo e($consentimiento->estado ? 'Activo' : 'Inactivo'); ?>

                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.consentimientos.show', $consentimiento->_id)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('gestion-instrumentos.consentimientos.edit', $consentimiento->_id)); ?>"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo e(route('gestion-instrumentos.consentimientos.informes', $consentimiento->_id)); ?>"
                                                class="btn btn-sm btn-outline-info" title="Informes">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <form
                                                action="<?php echo e(route('gestion-instrumentos.consentimientos.destroy', $consentimiento->_id)); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de eliminar este consentimiento?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-2">No hay consentimientos registrados</p>
                                        <a href="<?php echo e(route('gestion-instrumentos.consentimientos.create')); ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-2"></i>Crear el primer consentimiento
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(isset($consentimientos) &&
                        $consentimientos instanceof \Illuminate\Contracts\Pagination\Paginator &&
                        method_exists($consentimientos, 'links') &&
                        $consentimientos->hasPages()): ?>
                    <div class="p-3">
                        <?php echo e($consentimientos->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/consentimientos/index.blade.php ENDPATH**/ ?>