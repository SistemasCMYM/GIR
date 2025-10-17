<?php if(!isset($isModal) || !$isModal): ?>
    <?php $__env->startSection('title', 'Editar Empleado'); ?>

    <?php $__env->startSection('content'); ?>
    <?php endif; ?>

    <?php if(!isset($isModal) || !$isModal): ?>
        <div class="container-fluid py-4 gir-override">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Editar Empleado
                            </h5>
                        </div>
                        <div class="card-body">
    <?php endif; ?>

    <form id="editarEmpleadoForm<?php echo e(isset($isModal) && $isModal ? '_modal' : ''); ?>" method="POST"
        action="<?php echo e(route('empresa.empleados.update', $empleado['_id'])); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <?php echo $__env->make('admin.gestion-administrativa.empresa.empleados._form', [
            'empleado' => $empleado,
            'areas' => $areas,
            'centros' => $centros,
            'procesos' => $procesos,
            'fieldIdPrefix' => isset($isModal) && $isModal ? 'modal_edit_' : 'page_edit_',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(!isset($isModal) || !$isModal): ?>
            <div class="mt-4">
                <a href="<?php echo e(route('empresa.empleados.index')); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver
                </a>
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fas fa-save me-2"></i>
                    Actualizar Empleado
                </button>
            </div>
        <?php endif; ?>
    </form>

    <?php if(!isset($isModal) || !$isModal): ?>
        </div>
        </div>
        </div>
        </div>
        </div>
    <?php $__env->stopSection(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-administrativa/empresa/empleados/edit.blade.php ENDPATH**/ ?>