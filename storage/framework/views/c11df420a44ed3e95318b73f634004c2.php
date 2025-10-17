

<?php $__env->startSection('title', 'Configuración del Sistema'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .config-header {
            background: linear-gradient(135deg, #D1A854, #EDC979);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .config-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .config-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .section-icon {
            width: 30px;
            height: 30px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            margin: 0 auto 0.5rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-number {
            font-size: 1rem;
            font-weight: bold;
            color: #495057;
        }

        .stats-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background: #28a745;
        }

        .status-inactive {
            background: #dc3545;
        }

        .status-warning {
            background: #ffc107;
        }

        .module-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .module-status:last-child {
            border-bottom: none;
        }

        .badge-custom {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
        }
    </style>
<?php $__env->stopPush(); ?>

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
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-cog"></i> Configuración del Sistema
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="config-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-cogs me-3"></i>
                        Configuración del Sistema
                    </h2>
                    <p class="mb-0 opacity-75">
                        Configure los módulos del sistema, ajustes de empresa y preferencias
                    </p>
                    <?php if(isset($empresa) && $empresa): ?>
                        <small class="opacity-75 d-block mt-2">
                            <i class="fas fa-building me-2"></i>
                            <?php echo e($empresa['razon_social'] ?? 'Sin nombre'); ?> (<?php echo e($empresa['nit'] ?? 'Sin NIT'); ?>)
                        </small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex flex-column gap-2">
                        <small class="opacity-75">
                            <i class="fas fa-user me-2"></i>
                            Usuario: <?php echo e($usuario['nombre'] ?? 'N/A'); ?>

                        </small>
                        <small class="opacity-75">
                            <i class="fas fa-clock me-2"></i>
                            <?php echo e(now()->format('d/m/Y H:i')); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estadísticas del Sistema -->
        <?php if(isset($estadisticas)): ?>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-primary"><?php echo e($estadisticas['total_configuraciones'] ?? 0); ?></div>
                        <div class="stats-label">Total Configuraciones</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-success"><?php echo e($estadisticas['modulos_activos'] ?? 0); ?></div>
                        <div class="stats-label">Módulos Activos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-info"><?php echo e($estadisticas['configuraciones_empresa'] ?? 0); ?></div>
                        <div class="stats-label">Config. Empresa</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-warning"><?php echo e($estadisticas['configuraciones_psicosocial'] ?? 0); ?>

                        </div>
                        <div class="stats-label">Config. Psicosocial</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Módulos de Configuración -->
        <?php if(isset($modulos) && count($modulos) > 0): ?>
            <div class="row">
                <?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-4">
                        <div class="config-card text-center">
                            <div class="section-icon mx-auto"
                                style="background: 
                        <?php if($modulo['color'] == 'primary'): ?> linear-gradient(135deg, #007bff 0%, #0056b3 100%)
                        <?php elseif($modulo['color'] == 'success'): ?> linear-gradient(135deg, #28a745 0%, #1e7e34 100%)
                        <?php elseif($modulo['color'] == 'primary'): ?> linear-gradient(135deg, #ffc107 0%, #e0a800 100%)
                        <?php elseif($modulo['color'] == 'info'): ?> linear-gradient(135deg, #17a2b8 0%, #138496 100%)
                        <?php elseif($modulo['color'] == 'danger'): ?> linear-gradient(135deg, #dc3545 0%, #c82333 100%)
                        <?php elseif($modulo['color'] == 'secondary'): ?> linear-gradient(135deg, #6c757d 0%, #545b62 100%)
                        <?php elseif($modulo['color'] == 'auth'): ?> linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)
                        <?php elseif($modulo['color'] == 'process'): ?> linear-gradient(135deg, #28a745 0%, #218838 100%)
                        <?php else: ?> linear-gradient(135deg, #667eea 0%, #764ba2 100%) <?php endif; ?>;">
                                <i class="<?php echo e($modulo['icono']); ?>"></i>
                            </div>
                            <h5 class="mb-1"><?php echo e($modulo['nombre']); ?></h5>
                            <p class="text-muted mb-2" style="font-size: 0.85rem;">
                                <?php echo e($modulo['descripcion']); ?>

                            </p>
                            <?php if($modulo['habilitado']): ?>
                                <a href="<?php echo e(route($modulo['ruta'])); ?>" class="btn btn-<?php echo e($modulo['color']); ?>">
                                    <i class="fas fa-cog me-2"></i>Configurar
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-lock me-2"></i>No Disponible
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <!-- Estado del Sistema -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Estado del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="module-status">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-active"></span>
                                        <strong>Base de Datos</strong>
                                    </div>
                                    <span class="badge-custom badge bg-success">Conectado</span>
                                </div>
                                <div class="module-status">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-active"></span>
                                        <strong>Sesión</strong>
                                    </div>
                                    <span class="badge-custom badge bg-success">Activa</span>
                                </div>
                                <div class="module-status">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-active"></span>
                                        <strong>Autenticación</strong>
                                    </div>
                                    <span class="badge-custom badge bg-success">Funcionando</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="module-status">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-active"></span>
                                        <strong>Cache</strong>
                                    </div>
                                    <span class="badge-custom badge bg-success">Operativo</span>
                                </div>
                                <div class="module-status">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-active"></span>
                                        <strong>Almacenamiento</strong>
                                    </div>
                                    <span class="badge-custom badge bg-success">Disponible</span>
                                </div>
                                <div class="module-status">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-active"></span>
                                        <strong>Empresa</strong>
                                    </div>
                                    <span class="badge-custom badge bg-success">Configurada</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Última Actividad
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($estadisticas['ultima_configuracion']) && $estadisticas['ultima_configuracion'] !== 'N/A'): ?>
                            <p class="mb-2">
                                <strong>Última modificación:</strong><br>
                                <small class="text-muted"><?php echo e($estadisticas['ultima_configuracion']); ?></small>
                            </p>
                        <?php else: ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay modificaciones recientes
                            </p>
                        <?php endif; ?>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Auto-refresh system status every 30 seconds
        let statusRefreshInterval = setInterval(function() {
            // Aquí podrías agregar una llamada AJAX para verificar el estado del sistema
            console.log('Verificando estado del sistema...');
        }, 30000);

        // Cleanup interval on page unload
        window.addEventListener('beforeunload', function() {
            if (statusRefreshInterval) {
                clearInterval(statusRefreshInterval);
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/modules/configuracion/index.blade.php ENDPATH**/ ?>