

<?php $__env->startSection('title', 'Ver Consentimiento'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .consentimiento-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.3);
        }

        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contenido-consentimiento {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            line-height: 1.8;
        }

        .items-list {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .item-badge {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            display: inline-block;
            font-size: 0.9rem;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .status-activo {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-inactivo {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        @media print {

            .btn,
            .card-header,
            .navbar,
            .sidebar {
                display: none !important;
            }

            .content-display {
                font-size: 12px;
                line-height: 1.4;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .card-body {
                padding: 0;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('empleados.index')); ?>">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('gestion-instrumentos.index')); ?>">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('gestion-instrumentos.consentimientos.index')); ?>">Consentimientos</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-briefcase"></i> Ver Consentimiento
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('gestion-instrumentos.consentimientos.index')); ?>"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <a href="<?php echo e(route('gestion-instrumentos.index')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Volver al módulo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header del Consentimiento -->
        <div class="consentimiento-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-file-signature me-3"></i>
                        <?php echo e($consentimiento->titulo); ?>

                    </h2>
                    <?php if($consentimiento->descripcion): ?>
                        <p class="mb-2 opacity-75"><?php echo e($consentimiento->descripcion); ?></p>
                    <?php endif; ?>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge <?php echo e($consentimiento->estado ? 'bg-success' : 'bg-danger'); ?> fs-6">
                            <?php echo e($consentimiento->estado_texto); ?>

                        </span>
                        <span class="badge bg-light text-dark fs-6">
                            <?php echo e($consentimiento->tipo_formateado); ?>

                        </span>
                        <?php if($consentimiento->items_total > 0): ?>
                            <span class="badge bg-info fs-6">
                                <?php echo e($consentimiento->items_total); ?> ítems
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex flex-column gap-2">
                        <small class="opacity-75">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Creado:
                            <?php echo e($consentimiento->fecha_creacion ? $consentimiento->fecha_creacion->format('d/m/Y') : 'N/A'); ?>

                        </small>
                        <?php if($consentimiento->fecha_modificacion && $consentimiento->fecha_modificacion != $consentimiento->fecha_creacion): ?>
                            <small class="opacity-75">
                                <i class="fas fa-calendar-edit me-2"></i>
                                Modificado: <?php echo e($consentimiento->fecha_modificacion->format('d/m/Y')); ?>

                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-primary"><?php echo e($estadisticas['total_respuestas']); ?></div>
                    <div class="stat-label">Total Respuestas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-success"><?php echo e($estadisticas['aceptaron']); ?></div>
                    <div class="stat-label">Aceptaron</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-danger"><?php echo e($estadisticas['rechazaron']); ?></div>
                    <div class="stat-label">Rechazaron</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-info"><?php echo e($estadisticas['firmados']); ?></div>
                    <div class="stat-label">Firmados</div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-tools me-2"></i>
                            Acciones
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo e(route('gestion-instrumentos.consentimientos.edit', $consentimiento)); ?>"
                                class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>

                            <a href="<?php echo e(route('gestion-instrumentos.consentimientos.diligenciar', $consentimiento)); ?>"
                                class="btn btn-primary <?php echo e(!$consentimiento->estado ? 'disabled' : ''); ?>">
                                <i class="fas fa-pen-alt me-2"></i>Diligenciar
                            </a>

                            <a href="<?php echo e(route('gestion-instrumentos.consentimientos.informes', $consentimiento)); ?>"
                                class="btn btn-info">
                                <i class="fas fa-chart-bar me-2"></i>Ver Informes
                            </a>

                            <form
                                action="<?php echo e(route('gestion-instrumentos.consentimientos.toggle-estado', $consentimiento)); ?>"
                                method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                    class="btn <?php echo e($consentimiento->estado ? 'btn-outline-danger' : 'btn-outline-success'); ?>">
                                    <i class="fas fa-<?php echo e($consentimiento->estado ? 'times' : 'check'); ?> me-2"></i>
                                    <?php echo e($consentimiento->estado ? 'Desactivar' : 'Activar'); ?>

                                </button>
                            </form>

                            <button class="btn btn-outline-secondary" onclick="imprimirConsentimiento()">
                                <i class="fas fa-print me-2"></i>Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contenido del Consentimiento -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Contenido del Consentimiento
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="contenido-consentimiento" id="contenido-imprimir">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold"><?php echo e($consentimiento->titulo); ?></h4>
                            </div>

                            <div class="mb-4">
                                <?php echo nl2br(e($consentimiento->contenido)); ?>

                            </div>

                            <?php if(
                                $consentimiento->configuracion &&
                                    isset($consentimiento->configuracion['items']) &&
                                    count($consentimiento->configuracion['items']) > 0): ?>
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Puntos específicos del consentimiento:</h6>
                                    <ul class="list-unstyled">
                                        <?php $__currentLoopData = $consentimiento->configuracion['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" disabled>
                                                    <label class="form-check-label"><?php echo e($item); ?></label>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="border-top pt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="acepta_muestra"
                                                disabled>
                                            <label class="form-check-label fw-bold text-success">
                                                <i class="fas fa-check me-2"></i>ACEPTO los términos del consentimiento
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="acepta_muestra"
                                                disabled>
                                            <label class="form-check-label fw-bold text-danger">
                                                <i class="fas fa-times me-2"></i>NO ACEPTO los términos del consentimiento
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <?php if($consentimiento->configuracion && ($consentimiento->configuracion['requiere_firma'] ?? true)): ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Firma Digital:</label>
                                        <div class="border rounded p-3 bg-light text-center">
                                            <i class="fas fa-signature fa-2x text-muted mb-2"></i>
                                            <br>
                                            <small class="text-muted">Área reservada para firma digital</small>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($consentimiento->configuracion && ($consentimiento->configuracion['requiere_fecha'] ?? true)): ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha de diligenciamiento:</label>
                                        <div class="border rounded p-2 bg-light">
                                            <small class="text-muted">Se registrará automáticamente al momento del
                                                envío</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="col-md-4">
                <!-- Detalles Técnicos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Detalles Técnicos
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td><?php echo e($consentimiento->_id); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Versión:</strong></td>
                                <td><?php echo e($consentimiento->version ?? '1.0'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    <span
                                        class="status-badge <?php echo e($consentimiento->estado ? 'status-activo' : 'status-inactivo'); ?>">
                                        <?php echo e($consentimiento->estado_texto); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tipo:</strong></td>
                                <td><?php echo e($consentimiento->tipo_formateado); ?></td>
                            </tr>
                            <?php if($consentimiento->items_total > 0): ?>
                                <tr>
                                    <td><strong>Total Ítems:</strong></td>
                                    <td><?php echo e($consentimiento->items_total); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($consentimiento->usuario_creador): ?>
                                <tr>
                                    <td><strong>Creado por:</strong></td>
                                    <td>ID: <?php echo e($consentimiento->usuario_creador); ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Configuración -->
                <?php if($consentimiento->configuracion): ?>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-cog me-2"></i>
                                Configuración
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        <?php echo e($consentimiento->configuracion['requiere_firma'] ?? true ? 'checked' : ''); ?>

                                        disabled>
                                    <label class="form-check-label">Requiere firma digital</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        <?php echo e($consentimiento->configuracion['requiere_fecha'] ?? true ? 'checked' : ''); ?>

                                        disabled>
                                    <label class="form-check-label">Registra fecha automáticamente</label>
                                </div>
                            </div>

                            <?php if(isset($consentimiento->configuracion['items']) && count($consentimiento->configuracion['items']) > 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2">Ítems configurados:</small>
                                    <div>
                                        <?php $__currentLoopData = $consentimiento->configuracion['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="item-badge"><?php echo e(Str::limit($item, 30)); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function imprimirConsentimiento() {
            const printWindow = window.open('', '_blank');
            const contenido = document.getElementById('contenido-imprimir').innerHTML;

            printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title><?php echo e($consentimiento->titulo); ?></title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
                .text-center { text-align: center; }
                .fw-bold { font-weight: bold; }
                .mb-4 { margin-bottom: 1.5rem; }
                .mb-3 { margin-bottom: 1rem; }
                .mb-2 { margin-bottom: 0.5rem; }
                .border-top { border-top: 1px solid #dee2e6; }
                .pt-4 { padding-top: 1.5rem; }
                .form-check { margin-bottom: 0.5rem; }
                .form-check-label { margin-left: 1.5rem; }
                .border { border: 1px solid #dee2e6; }
                .rounded { border-radius: 0.375rem; }
                .p-3 { padding: 1rem; }
                .bg-light { background-color: #f8f9fa; }
                .p-2 { padding: 0.5rem; }
                ul { list-style: none; padding-left: 0; }
                li { margin-bottom: 0.5rem; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            ${contenido}
        </body>
        </html>
    `);

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/consentimientos/show.blade.php ENDPATH**/ ?>