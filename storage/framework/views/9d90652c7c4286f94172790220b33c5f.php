

<?php $__env->startSection('title', 'Administración de Empresa - GIR-365'); ?>
<?php $__env->startSection('page-title', 'Administración de Empresa'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        // Obtener datos del usuario desde la sesión
        $userData = session('user_data') ?: session('usuario_data');
        $tipo = $userData['tipo'] ?? '';
        $rol = $userData['rol'] ?? '';

        // Determinar si es Super Administrador
        $isSuperAdmin =
            in_array($tipo, ['super_admin', 'superadmin', 'root']) ||
            in_array($rol, ['super_admin', 'superadmin', 'root']) ||
            (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);

        // Determinar si es Administrador de Empresa
        $isAdminEmpresa = ($tipo === 'cliente' || $tipo === 'interna') && !$isSuperAdmin;

        // Determinar si es Profesional/Psicólogo
        $isProfesional = $tipo === 'profesional';
    ?>
    <div class="tw-container-fluid tw-py-6">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-4">
            <div class="container-fluid">
                <nav aria-label="breadcrumb" class="tw-mb-6">
                    <ol class="breadcrumb tw-bg-transparent tw-mb-0 tw-p-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>"
                                class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                                <i class="fas fa-home tw-mr-1"></i>Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Administración de Empresa
                        </li>
                    </ol>
                </nav>
                <!-- Page Title -->
                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h4 class="page-title">
                                <i class="fas fa-building me-2"></i>
                                Administración de Empresa
                            </h4>
                            <p class="text-muted mb-0">Gestiona todos los aspectos organizacionales de tu empresa desde este panel centralizado</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards Silva Dashboard -->
                <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                    <div class="row mb-4">
                        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                            <div class="card tw-transition-transform tw-duration-300 hover:tw-scale-105">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    <i class="fas fa-users fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0"><?php echo e($totalEmpleados ?? 0); ?></h4>
                                            <p class="text-muted mb-0">Empleados</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                            <div class="card tw-transition-transform tw-duration-300 hover:tw-scale-105">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-success">
                                                <span class="avatar-title rounded-circle bg-success">
                                                    <i class="fas fa-sitemap fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0"><?php echo e($totalAreas ?? 0); ?></h4>
                                            <p class="text-muted mb-0">Áreas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                            <div class="card tw-transition-transform tw-duration-300 hover:tw-scale-105">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-info">
                                                <span class="avatar-title rounded-circle bg-info">
                                                    <i class="fas fa-map-marker-alt fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0"><?php echo e($totalCentros ?? 0); ?></h4>
                                            <p class="text-muted mb-0">Centros</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                            <div class="card tw-transition-transform tw-duration-300 hover:tw-scale-105">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-warning">
                                                <span class="avatar-title rounded-circle bg-warning">
                                                    <i class="fas fa-cogs fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="mb-0"><?php echo e($totalProcesos ?? 0); ?></h4>
                                            <p class="text-muted mb-0">Procesos</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Management Modules Silva Dashboard con Control de Acceso por Roles -->
                <div class="row">
                    <!-- Empleados - Acceso: SuperAdmin y Administrador Empresa -->
                    <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-upload me-2 tw-text-blue-600"></i>
                                        Cargue de Empleados
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted tw-text-sm">Importa y gestiona la información de los empleados de
                                        la empresa de forma masiva o individual.</p>
                                    <div class="mb-3">
                                        <span class="badge bg-primary"><?php echo e($totalEmpleados ?? 0); ?> empleados</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo e(route('empresa.empleados.index')); ?>"
                                        class="btn btn-primary w-100 tw-transition-colors tw-duration-200 hover:tw-bg-blue-700">
                                        <i class="fas fa-list me-2"></i>Gestionar Empleados
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Empresas - Acceso: SOLO SuperAdmin -->
                    <?php if($isSuperAdmin): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div
                                class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg tw-border-2 tw-border-green-500">
                                <div class="card-header bg-success text-white">
                                    <h4 class="card-title mb-0 text-white">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Creación de Empresas
                                    </h4>
                                    <span class="badge bg-light text-success tw-text-xs">Solo SuperAdmin</span>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted tw-text-sm">Registra nuevas empresas y configura su información
                                        básica en
                                        el sistema.</p>
                                    <div class="mb-3">
                                        <span class="badge bg-success">Gestión completa</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo e(route('empresa.empresas.index')); ?>"
                                        class="btn btn-success w-100 tw-transition-colors tw-duration-200 hover:tw-bg-green-700">
                                        <i class="fas fa-building me-2"></i>Gestionar Empresas
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Áreas - Acceso: SuperAdmin y Administrador Empresa -->
                    <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-sitemap me-2 tw-text-cyan-600"></i>
                                        Cargue de Áreas
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted tw-text-sm">Define y organiza las áreas funcionales y
                                        departamentos
                                        de la
                                        empresa.</p>
                                    <div class="mb-3">
                                        <span class="badge bg-info"><?php echo e($totalAreas ?? 0); ?> áreas</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo e(route('empresa.areas.index')); ?>"
                                        class="btn btn-info w-100 tw-transition-colors tw-duration-200 hover:tw-bg-cyan-700">
                                        <i class="fas fa-list me-2"></i>Gestionar Áreas
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Centros - Acceso: SuperAdmin y Administrador Empresa -->
                    <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-map-marker-alt me-2 tw-text-yellow-600"></i>
                                        Cargue de Centros
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted tw-text-sm">Administra las sedes, sucursales y centros de trabajo
                                        de la
                                        organización.</p>
                                    <div class="mb-3">
                                        <span class="badge bg-warning text-dark"><?php echo e($totalCentros ?? 0); ?> centros</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo e(route('empresa.centros.index')); ?>"
                                        class="btn btn-warning w-100 tw-transition-colors tw-duration-200 hover:tw-bg-yellow-600">
                                        <i class="fas fa-list me-2"></i>Gestionar Centros
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Ciudades - Acceso: SuperAdmin y Administrador Empresa -->
                    <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-city me-2 tw-text-gray-600"></i>
                                        Cargue de Ciudades
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted tw-text-sm">Gestiona las ubicaciones geográficas donde opera la
                                        empresa.
                                    </p>
                                    <div class="mb-3">
                                        <span class="badge bg-secondary">Ubicaciones</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo e(route('empresa.ciudades.index')); ?>"
                                        class="btn btn-secondary w-100 tw-transition-colors tw-duration-200 hover:tw-bg-gray-700">
                                        <i class="fas fa-list me-2"></i>Gestionar Ciudades
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Procesos - Acceso: SuperAdmin y Administrador Empresa -->
                    <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 tw-transition-all tw-duration-300 hover:tw-shadow-lg">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-cogs me-2 tw-text-gray-800"></i>
                                        Cargue de Procesos
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted tw-text-sm">Define los procesos organizacionales y procedimientos
                                        de
                                        trabajo.</p>
                                    <div class="mb-3">
                                        <span class="badge bg-dark text-white"><?php echo e($totalProcesos ?? 0); ?> procesos</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo e(route('empresa.procesos.index')); ?>"
                                        class="btn btn-dark w-100 tw-transition-colors tw-duration-200 hover:tw-bg-gray-900">
                                        <i class="fas fa-list me-2"></i>Gestionar Procesos
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mensaje de Acceso Restringido para Profesionales -->
                <?php if($isProfesional): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning tw-border-l-4 tw-border-yellow-500 tw-bg-yellow-50"
                                role="alert">
                                <h4 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Acceso Restringido
                                </h4>
                                <p class="mb-0">Como <strong>Profesional/Psicólogo</strong>, tu acceso a este módulo está
                                    limitado.
                                    Por favor, dirígete al <a href="<?php echo e(route('dashboard')); ?>" class="alert-link">Módulo
                                        Psicosocial</a>
                                    para realizar tus actividades.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Acciones Rápidas - Solo SuperAdmin y Administrador Empresa -->
                <?php if($isSuperAdmin || $isAdminEmpresa): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card tw-border-t-4 tw-border-indigo-500">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-bolt me-2 tw-text-purple-600"></i>
                                        Acciones Rápidas
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="<?php echo e(route('empresa.empleados.create')); ?>"
                                                class="btn btn-primary w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105 hover:tw-shadow-xl">
                                                <i class="fas fa-user-plus mb-2 d-block" style="font-size: 2rem;"></i>
                                                Nuevo Empleado
                                            </a>
                                        </div>

                                        <?php if($isSuperAdmin): ?>
                                            <div class="col-md-3 mb-3">
                                                <a href="<?php echo e(route('empresa.empresas.create')); ?>"
                                                    class="btn btn-success w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105 hover:tw-shadow-xl">
                                                    <i class="fas fa-building-flag mb-2 d-block"
                                                        style="font-size: 2rem;"></i>
                                                    Nueva Empresa
                                                    <span
                                                        class="badge bg-light text-success tw-text-xs d-block mt-1">SuperAdmin</span>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <div class="col-md-3 mb-3">
                                            <a href="<?php echo e(route('empresa.areas.create')); ?>"
                                                class="btn btn-info w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105 hover:tw-shadow-xl">
                                                <i class="fas fa-plus mb-2 d-block" style="font-size: 2rem;"></i>
                                                Nueva Área
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="<?php echo e(route('empresa.centros.create')); ?>"
                                                class="btn btn-warning w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105 hover:tw-shadow-xl">
                                                <i class="fas fa-building mb-2 d-block" style="font-size: 2rem;"></i>
                                                Nuevo Centro
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Segunda fila de acciones -->
                                    <div class="row mt-3">
                                        <div class="col-md-4 mb-3">
                                            <a href="#"
                                                class="btn btn-secondary w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105"
                                                onclick="alert('Función en desarrollo'); return false;">
                                                <i class="fas fa-file-export mb-2 d-block" style="font-size: 1.5rem;"></i>
                                                Exportar Datos
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="#"
                                                class="btn btn-dark w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105"
                                                onclick="alert('Función en desarrollo'); return false;">
                                                <i class="fas fa-file-import mb-2 d-block" style="font-size: 1.5rem;"></i>
                                                Importar Datos
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="#"
                                                class="btn btn-outline-primary w-100 py-3 tw-transition-all tw-duration-300 hover:tw-scale-105"
                                                onclick="alert('Función en desarrollo'); return false;">
                                                <i class="fas fa-chart-bar mb-2 d-block" style="font-size: 1.5rem;"></i>
                                                Ver Reportes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-administrativa/empresa/index.blade.php ENDPATH**/ ?>