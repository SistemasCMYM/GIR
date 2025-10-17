

<?php $__env->startSection('title', 'Encuestas'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">

        <?php if(session('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('warning')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                    <i class="fas fa-briefcase"></i> Encuestas
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Gestión de Encuestas</h1>
                        <p class="text-muted">Administración de encuestas personalizadas</p>
                    </div>
                    <div>
                        <a href="<?php echo e(route('gestion-instrumentos.index')); ?>" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Módulo
                        </a>
                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Encuesta
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Encuestas -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-poll me-2"></i>Lista de Encuestas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">ENCUESTA</th>
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
                            <?php $__empty_1 = true; $__currentLoopData = $encuestas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $encuesta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.show', $encuesta->id)); ?>"
                                            class="text-decoration-none fw-bold">
                                            <?php echo e($encuesta->titulo ?? ($encuesta->nombre ?? 'Sin nombre')); ?>

                                        </a>
                                        <?php if(!session('empresa_id')): ?>
                                            <br><small class="text-muted">Empresa ID:
                                                <?php echo e($encuesta->empresa_id ?? 'N/A'); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3"><?php echo e(Str::limit($encuesta->descripcion ?? 'Sin descripción', 50)); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-info">
                                            <?php echo e(count($encuesta->preguntas ?? [])); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e($encuesta->created_at ? $encuesta->created_at->format('d/m/Y') : 'N/A'); ?></td>
                                    <td class="px-4 py-3">
                                        <?php echo e($encuesta->updated_at ? $encuesta->updated_at->format('d/m/Y') : 'N/A'); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-secondary">
                                            <?php echo e(ucfirst($encuesta->tipo ?? 'General')); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form
                                            action="<?php echo e(route('gestion-instrumentos.encuestas.toggle-estado', $encuesta->id)); ?>"
                                            method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                class="btn btn-sm <?php echo e($encuesta->estado ?? false ? 'btn-success' : 'btn-outline-danger'); ?>">
                                                <?php echo e($encuesta->estado ?? false ? 'Activo' : 'Inactivo'); ?>

                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.encuestas.show', $encuesta->id)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($encuesta->publicada ?? false): ?>
                                                <a href="<?php echo e(route('gestion-instrumentos.encuestas.responder', $encuesta->id)); ?>"
                                                    class="btn btn-sm btn-outline-success" title="Responder">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('gestion-instrumentos.encuestas.edit', $encuesta->id)); ?>"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="<?php echo e(route('gestion-instrumentos.encuestas.toggle-publicacion', $encuesta->id)); ?>"
                                                method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                    class="btn btn-sm <?php echo e($encuesta->publicada ?? false ? 'btn-outline-success' : 'btn-outline-secondary'); ?>"
                                                    title="Publicar">
                                                    <i
                                                        class="fas fa-<?php echo e($encuesta->publicada ?? false ? 'globe' : 'eye-slash'); ?>"></i>
                                                </button>
                                            </form>
                                            <a href="<?php echo e(route('gestion-instrumentos.encuestas.informes', $encuesta->id)); ?>"
                                                class="btn btn-sm btn-outline-info" title="Informes">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <form
                                                action="<?php echo e(route('gestion-instrumentos.encuestas.destroy', $encuesta->id)); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta encuesta?')">
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
                                        <p class="mb-2">No hay encuestas registradas</p>
                                        <a href="<?php echo e(route('gestion-instrumentos.encuestas.create')); ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-2"></i>Crear la primera encuesta
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(isset($encuestas) &&
                        $encuestas instanceof \Illuminate\Contracts\Pagination\Paginator &&
                        method_exists($encuestas, 'links') &&
                        $encuestas->hasPages()): ?>
                    <div class="p-3">
                        <?php echo e($encuestas->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/encuestas/index.blade.php ENDPATH**/ ?>