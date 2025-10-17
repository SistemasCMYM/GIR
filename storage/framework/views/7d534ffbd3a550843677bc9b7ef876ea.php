
<?php $__env->startSection('title', 'Administración de Usuarios'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* UNIFORM METRICS CARDS - SAME HEIGHT & ALIGNMENT */
        .gir-metric-card {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            animation: none !important;
            transition: all 0.3s ease !important;
            transform: none !important;
            position: relative !important;
            background: white !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            padding: 16px !important;
            margin-bottom: 0 !important;
            /* EXACT SAME HEIGHT FOR ALL CARDS */
            height: 120px !important;
            min-height: 120px !important;
            max-height: 120px !important;
            width: 100% !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .gir-metric-card:hover {
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px) !important;
        }

        /* CONSISTENT GRID LAYOUT */
        .row.g-3.mb-4 {
            display: flex !important;
            flex-wrap: wrap !important;
            margin: 0 !important;
            align-items: stretch !important;
        }

        .row.g-3.mb-4 .col-xl-3,
        .row.g-3.mb-4 .col-lg-3,
        .row.g-3.mb-4 .col-md-6,
        .row.g-3.mb-4 .col-sm-6 {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex: 0 0 25% !important;
            max-width: 25% !important;
            padding: 0 8px !important;
            margin-bottom: 16px !important;
        }

        /* CONTENT INSIDE CARDS - UNIFORM SIZING */
        .gir-metric-card .flex-grow-1 {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        .gir-metric-card .gir-metric-number {
            font-size: 1.75rem !important;
            line-height: 1.2 !important;
            margin-bottom: 4px !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
        }

        .gir-metric-card .gir-metric-label {
            font-size: 0.75rem !important;
            line-height: 1.1 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #6b7280 !important;
            margin: 0 !important;
        }

        /* ICONS - UNIFORM SIZE */
        .gir-metric-card .gir-metric-icon {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .gir-metric-card .gir-metric-icon i {
            font-size: 20px !important;
            color: white !important;
        }

        /* RESPONSIVE BREAKPOINTS - MAINTAIN ALIGNMENT */
        @media (max-width: 1199.98px) {

            .row.g-3.mb-4 .col-xl-3,
            .row.g-3.mb-4 .col-lg-3 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
        }

        @media (max-width: 767.98px) {

            .row.g-3.mb-4 .col-md-6,
            .row.g-3.mb-4 .col-sm-6 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            .gir-metric-card {
                height: 100px !important;
                min-height: 100px !important;
                max-height: 100px !important;
                padding: 12px !important;
            }

            .gir-metric-card .gir-metric-number {
                font-size: 1.5rem !important;
            }

            .gir-metric-card .gir-metric-icon {
                width: 40px !important;
                height: 40px !important;
            }

            .gir-metric-card .gir-metric-icon i {
                font-size: 18px !important;
            }
        }

        /* DISABLE PROBLEMATIC ANIMATIONS */
        .gir-slide-up,
        .gir-fade-in {
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* FORCE REMOVE ANY H-100 CONFLICTS */
        .h-100 {
            height: 120px !important;
        }
    </style>

    <div class="container-fluid py-4 gir-override">
        <div class="row">
            <div class="col-14">
                <!-- Breadcrumb Corporativo -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>"
                                class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                                <i class="fas fa-home tw-mr-1"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('admin.gestion-administrativa.index')); ?>"
                                class="tw-text-gir-primary-600 hover:tw-text-gir-primary-800 tw-no-underline tw-transition-colors">
                                <i class="fas fa-users-cog tw-mr-1"></i> Administración de Usuarios
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-users me-1"></i> Cuentas
                        </li>
                    </ol>
                </nav>

                <!-- Header Corporativo -->
                <div class="gir-modern-card mb-4 gir-fade-in">
                    <div class="gir-header-gradient">
                        <h1 class="mb-2" style="font-size: 32px; font-weight: 800;">
                            <i class="fas fa-users me-3"></i>Administración de Cuentas de Usuarios
                        </h1>
                        <p class="mb-0 opacity-90">Gestione las cuentas de usuario del sistema con control total de acceso y
                            permisos</p>
                    </div>
                </div>

                <!-- Métricas Corporativas - Alineadas uniformemente -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        <?php echo e($totalUsuarios ?? 3); ?>

                                    </div>
                                    <div class="gir-metric-label small text-muted">TOTAL USUARIOS</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #D1A554 0%, #B8943C 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        <?php echo e($usuariosActivos ?? 0); ?>

                                    </div>
                                    <div class="gir-metric-label small text-muted">USUARIOS ACTIVOS</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-check text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        <?php echo e($usuariosInactivos ?? 0); ?>

                                    </div>
                                    <div class="gir-metric-label small text-muted">USUARIOS INACTIVOS</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-times text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        <?php echo e($usuariosBloqueados ?? 0); ?>

                                    </div>
                                    <div class="gir-metric-label small text-muted">USUARIOS BLOQUEADOS</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-slash text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controles Corporativos - Compactos -->
                <div
                    class="gir-modern-card mb-3 gir-fade-in tw-max-h-[120px] sm:tw-max-h-[150px] lg:tw-max-h-[180px] xl:tw-max-h-[200px] 2xl:tw-max-h-[300px] 4xl:tw-max-h-[3840px] tw-overflow-hidden">
                    <div class="gir-table-header tw-p-2 sm:tw-p-3">
                        <div class="row align-items-center g-2">
                            <div class="col-md-4">
                                <div class="gir-search-box tw-h-8 sm:tw-h-10">
                                    <div class="gir-search-icon">
                                        <i class="fas fa-search tw-text-xs sm:tw-text-sm"></i>
                                    </div>
                                    <input type="text"
                                        class="gir-search-input tw-text-xs sm:tw-text-sm tw-py-1 sm:tw-py-2"
                                        placeholder="Buscar usuarios..." id="searchInput">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select tw-text-xs sm:tw-text-sm tw-py-1 sm:tw-py-2 tw-h-8 sm:tw-h-10"
                                    id="statusFilter" style="border-radius: 8px; border: 2px solid #e5e7eb;">
                                    <option value="">Estados</option>
                                    <option value="activa">Activos</option>
                                    <option value="inactiva">Inactivos</option>
                                    <option value="suspendida">Suspendidos</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select tw-text-xs sm:tw-text-sm tw-py-1 sm:tw-py-2 tw-h-8 sm:tw-h-10"
                                    id="roleFilter" style="border-radius: 8px; border: 2px solid #e5e7eb;">
                                    <option value="">Roles</option>
                                    <?php if(isset($roles)): ?>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rol); ?>"><?php echo e($rol); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-4 text-end">
                                <a href="<?php echo e(route('usuarios.cuentas.create')); ?>"
                                    class="gir-btn-modern tw-text-xs sm:tw-text-sm tw-py-1 sm:tw-py-2 tw-px-2 sm:tw-px-4"
                                    id="btnAbrirModalCrearUsuario">
                                    <i class="fas fa-plus tw-me-1"></i>Nueva Cuenta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla Corporativa - Compacta y Responsiva -->
                <div
                    class="gir-table-modern gir-fade-in tw-max-h-[400px] sm:tw-max-h-[500px] lg:tw-max-h-[600px] xl:tw-max-h-[700px] 2xl:tw-max-h-[900px] 4xl:tw-max-h-[3840px] tw-overflow-hidden">
                    <div class="gir-table-header tw-p-2 sm:tw-p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 tw-text-sm sm:tw-text-base lg:tw-text-lg tw-font-bold tw-text-gray-700">
                                <i class="fas fa-list me-2 tw-text-xs sm:tw-text-sm"
                                    style="color: var(--gir-primary);"></i>Lista de Usuarios
                            </h6>
                            <a href="#"
                                class="text-decoration-none d-flex align-items-center tw-text-xs sm:tw-text-sm"
                                onclick="location.reload()" style="color: var(--gir-primary); font-weight: 600;">
                                <i class="fas fa-refresh me-1 tw-text-xs"></i>Refrescar
                            </a>
                        </div>
                    </div>

                    <div
                        class="table-responsive tw-max-h-[320px] sm:tw-max-h-[400px] lg:tw-max-h-[480px] tw-overflow-y-auto">
                        <table class="table table-hover mb-0" id="usersTable" style="border: none;">
                            <thead
                                style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2"
                                        style="border: none; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Iniciales</th>
                                    <th class="tw-px-3 sm:tw-px-4 tw-py-3"
                                        style="border: none; font-weight: 900; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Usuario</th>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2 tw-hidden sm:tw-table-cell"
                                        style="border: none; font-weight: 500; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Email</th>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2"
                                        style="border: none; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Rol</th>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2"
                                        style="border: none; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Estado</th>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2 tw-hidden lg:tw-table-cell"
                                        style="border: none; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Último Acceso</th>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2 tw-hidden xl:tw-table-cell"
                                        style="border: none; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Empresa</th>
                                    <th class="tw-px-2 sm:tw-px-3 tw-py-2"
                                        style="border: none; font-weight: 700; color: #6b7280; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px;">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($cuentas) && count($cuentas) > 0): ?>
                                    <?php $__currentLoopData = $cuentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuenta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr style="border-bottom: 1px solid #f3f4f6; transition: all 0.2s;"
                                            class="tw-h-12 sm:tw-h-16">
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2" style="border: none;">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-2 tw-me-3">
                                                        <div class="rounded-full d-flex align-items-center justify-center text-white tw-w-6 tw-h-6 sm:tw-w-8 sm:tw-h-8 tw-text-xs"
                                                            style="background: var(--gir-gradient-primary); font-weight: 600;">
                                                            <?php echo e(strtoupper(substr($cuenta->nombre ?? 'N', 0, 1))); ?><?php echo e(strtoupper(substr($cuenta->apellido ?? 'A', 0, 1))); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2" style="border: none;">
                                                <div class="d-flex align-items-center">
                                                    <div class="tw-flex-1 tw-min-w-0">
                                                        <div
                                                            class="tw-truncate tw-text-xs sm:tw-text-sm tw-font-semibold tw-text-gray-900">
                                                            <?php echo e($cuenta->nombre ?? 'N/A'); ?>

                                                            <?php if($cuenta->apellido): ?>
                                                                <?php echo e($cuenta->apellido); ?>

                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2 tw-hidden sm:tw-table-cell"
                                                style="border: none;">
                                                <div class="tw-text-xs sm:tw-text-sm tw-text-gray-700 tw-truncate">
                                                    <?php echo e($cuenta->email ?? 'N/A'); ?>

                                                </div>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2" style="border: none;">
                                                <span
                                                    class="badge tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-rounded-full"
                                                    style="background: linear-gradient(135deg, var(--gir-gold-500) 0%, var(--gir-primary-light) 100%); color: white;"><?php echo e(Str::limit($cuenta->rol ?? 'Sin rol', 8)); ?></span>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2" style="border: none;">
                                                <?php
                                                    $estado = strtolower($cuenta->estado ?? 'inactiva');
                                                    $badgeClass =
                                                        $estado === 'activa'
                                                            ? 'gir-badge-active'
                                                            : ($estado === 'suspendida'
                                                                ? 'gir-badge-blocked'
                                                                : 'gir-badge-inactive');
                                                ?>
                                                <span
                                                    class="gir-badge-status <?php echo e($badgeClass); ?> tw-text-xs tw-px-2 tw-py-1">
                                                    <i class="fas fa-circle me-1 tw-text-xs"></i>
                                                    <?php echo e(ucfirst($cuenta->estado ?? 'Inactiva')); ?>

                                                </span>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2 tw-hidden lg:tw-table-cell"
                                                style="border: none;">
                                                <div class="tw-text-xs tw-text-gray-500">
                                                    <?php echo e(isset($cuenta->ultimoAcceso) && $cuenta->ultimoAcceso ? \Carbon\Carbon::parse($cuenta->ultimoAcceso)->format('d/m/Y') : 'Nunca'); ?>

                                                </div>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2 tw-hidden xl:tw-table-cell"
                                                style="border: none;">
                                                <div class="tw-text-xs tw-text-gray-500 tw-truncate">
                                                    <?php echo e($cuenta->empresa ?? 'Sin empresa'); ?>

                                                </div>
                                            </td>
                                            <td class="tw-px-2 sm:tw-px-3 tw-py-1 sm:tw-py-2" style="border: none;">
                                                <div class="d-flex tw-gap-1">
                                                    <a href="<?php echo e(route('usuarios.cuentas.show', $cuenta->id)); ?>"
                                                        class="btn btn-sm d-flex align-items-center justify-content-center tw-w-6 tw-h-6 sm:tw-w-8 sm:tw-h-8"
                                                        style="border: 1px solid #3b82f6; color: #0060fc; background: transparent; border-radius: 6px; transition: all 0.2s;"
                                                        title="Ver detalles" onmouseover="this.style.background='#eff6ff'"
                                                        onmouseout="this.style.background='transparent'">
                                                        <i class="fas fa-eye tw-text-xs"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('usuarios.cuentas.edit', $cuenta->id)); ?>"
                                                        class="btn btn-sm d-flex align-items-center justify-content-center tw-w-6 tw-h-6 sm:tw-w-8 sm:tw-h-8"
                                                        style="border: 1px solid var(--gir-primary); color: var(--gir-primary); background: transparent; border-radius: 6px; transition: all 0.2s;"
                                                        title="Editar" onmouseover="this.style.background='#fef7e7'"
                                                        onmouseout="this.style.background='transparent'">
                                                        <i class="fas fa-edit tw-text-xs"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm d-flex align-items-center justify-content-center tw-w-6 tw-h-6 sm:tw-w-8 sm:tw-h-8"
                                                        style="border: 1px solid #f59e0b; color: #f59e0b; background: transparent; border-radius: 6px; transition: all 0.2s;"
                                                        title="Pausar/Reactivar"
                                                        onmouseover="this.style.background='#fef3c7'"
                                                        onmouseout="this.style.background='transparent'"
                                                        onclick="toggleUserStatus('<?php echo e($cuenta->id); ?>', '<?php echo e($cuenta->estado); ?>')">
                                                        <i class="fas fa-pause tw-text-xs"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm d-flex align-items-center justify-content-center tw-w-6 tw-h-6 sm:tw-w-8 sm:tw-h-8"
                                                        style="border: 1px solid #ef4444; color: #ef4444; background: transparent; border-radius: 6px; transition: all 0.2s;"
                                                        title="Eliminar" onmouseover="this.style.background='#fef2f2'"
                                                        onmouseout="this.style.background='transparent'"
                                                        onclick="confirmDelete('<?php echo e($cuenta->id); ?>')">
                                                        <i class="fas fa-trash tw-text-xs"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="tw-px-4 tw-py-8 tw-text-center" style="border: none;">
                                            <div class="tw-text-gray-400">
                                                <i
                                                    class="fas fa-users tw-mb-3 tw-text-3xl sm:tw-text-4xl tw-text-gray-300"></i>
                                                <br>
                                                <span class="tw-text-sm sm:tw-text-base tw-font-semibold">No hay usuarios
                                                    registrados</span>
                                                <br>
                                                <span class="tw-text-xs sm:tw-text-sm tw-text-gray-500">Agregue el primer
                                                    usuario haciendo clic en "Nueva Cuenta"</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Corporativo: Crear Usuario -->
        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content"
                    style="border-radius: 16px; border: none; overflow: hidden; box-shadow: var(--gir-shadow-lg);">
                    <div class="modal-header"
                        style="background: var(--gir-gradient-header); color: white; border: none; padding: 24px;">
                        <h5 class="modal-title" id="modalCrearUsuarioLabel"
                            style="font-size: 20px; font-weight: 700; margin: 0;">
                            <i class="fas fa-user-plus me-2"></i>Nueva Cuenta
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="modalCrearUsuarioBody" style="padding: 24px;">
                        <!-- Aquí se cargará el formulario -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Animations disabled for stability

            // Inicializar DataTable si está disponible
            if ($.fn.DataTable) {
                $('#usersTable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [6] // Columna de acciones
                    }],
                    dom: 'rtip' // Ocultar el buscador por defecto
                });
            }

            // Búsqueda personalizada
            $('#searchInput').on('keyup', function() {
                if ($.fn.DataTable) {
                    $('#usersTable').DataTable().search(this.value).draw();
                }
            });

            // Filtros
            $('#statusFilter, #roleFilter').on('change', function() {
                if ($.fn.DataTable) {
                    let table = $('#usersTable').DataTable();

                    // Limpiar filtros anteriores
                    $.fn.dataTable.ext.search.pop();

                    // Aplicar nuevos filtros
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        let statusFilter = $('#statusFilter').val();
                        let roleFilter = $('#roleFilter').val();

                        let statusMatch = !statusFilter || data[3].toLowerCase().includes(
                            statusFilter.toLowerCase());
                        let roleMatch = !roleFilter || data[2].toLowerCase().includes(roleFilter
                            .toLowerCase());

                        return statusMatch && roleMatch;
                    });

                    table.draw();
                }
            });
        });

        function toggleUserStatus(userId, currentStatus) {
            let newStatus = currentStatus === 'activa' ? 'inactiva' : 'activa';
            let actionText = newStatus === 'activa' ? 'reactivar' : 'pausar';

            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea ${actionText} esta cuenta de usuario?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Sí, ${actionText}`,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: newStatus === 'activa' ? '#10b981' : '#f59e0b'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría la llamada AJAX para cambiar el estado
                    $.ajax({
                        url: `/admin/usuarios/cuentas/${userId}/toggle-status`,
                        method: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            estado: newStatus
                        },
                        success: function(response) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: `Usuario ${actionText}do correctamente`,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo cambiar el estado del usuario',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function confirmDelete(userId) {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría la llamada AJAX para eliminar
                    $.ajax({
                        url: `/admin/usuarios/cuentas/${userId}`,
                        method: 'DELETE',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: 'Usuario eliminado correctamente',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo eliminar el usuario',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        // Configuración mejorada del modal
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnAbrirModalCrearUsuario');
            const modalBody = document.getElementById('modalCrearUsuarioBody');
            const modal = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Mostrar mensaje de carga corporativo
                modalBody.innerHTML = `
                        <div class="text-center p-5">
                            <div class="spinner-border" style="color: var(--gir-primary);" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-3 mb-0" style="color: #6b7280;">Cargando formulario...</p>
                        </div>
                    `;

                // Cargar el formulario modal vía AJAX
                $.ajax({
                    url: '<?php echo e(route('usuarios.cuentas.create')); ?>',
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(html) {
                        modalBody.innerHTML = html;
                        modal.show();

                        // Inicializar componentes después de cargar el formulario
                        setTimeout(function() {
                            initializeModalFormComponents();
                        }, 100);
                    },
                    error: function(xhr) {
                        modalBody.innerHTML = `
                                <div class="alert alert-danger" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No se pudo cargar el formulario. Intente nuevamente.
                                </div>
                            `;
                    }
                });
            });

            function initializeModalFormComponents() {
                // Inicializar select2 para empresas si está disponible
                if ($.fn.select2 && $('#empresas').length) {
                    $('#empresas').select2({
                        placeholder: 'Seleccionar empresas...',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#modalCrearUsuario')
                    });
                }

                // Inicializar funcionalidades específicas del modal
                if (typeof updateProgressModal === 'function') {
                    updateProgressModal();
                }
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-administrativa/cuentas/index.blade.php ENDPATH**/ ?>