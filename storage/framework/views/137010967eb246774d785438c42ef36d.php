

<?php $__env->startSection('title', 'Cuestionarios'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #3498db 0%, #226897 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #0b582b 100%);
            --warning-gradient: linear-gradient(135deg, #137791 0%, #0a5a6e 100%);
            --danger-gradient: linear-gradient(135deg, #e74c3c 0%, #aa1100 100%);
            --info-gradient: linear-gradient(135deg, #f39c12 0%, #b97405 100%);
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --card-shadow-hover: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .hero-section {
            background: linear-gradient(135deg, #146c43, #198754);
            margin: -1.5rem -1.5rem 2rem -1.5rem;
            padding: 1.5rem 1rem;
            color: white;
            border-radius: 0 0 25px 25px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .modern-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .modern-card .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 1.5rem 2rem;
        }

        .modern-card .card-body {
            padding: 0;
        }

        .table-modern {
            margin: 0;
        }

        .table-modern th {
            background: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .table-modern td {
            padding: 1.5rem;
            border: none;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        }

        .cuestionario-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 1rem;
        }

        .cuestionario-datos .cuestionario-icon {
            background: var(--info-gradient);
        }

        .cuestionario-intralaboral .cuestionario-icon {
            background: var(--primary-gradient);
        }

        .cuestionario-extralaboral .cuestionario-icon {
            background: var(--success-gradient);
        }

        .cuestionario-estres .cuestionario-icon {
            background: var(--danger-gradient);
        }

        .badge-modern {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: none;
        }

        .badge-info {
            background: var(--info-gradient);
            color: white;
        }

        .badge-warning {
            background: var(--warning-gradient);
            color: white;
        }

        .badge-success {
            background: var(--success-gradient);
            color: white;
        }

        .badge-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .badge-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
            color: white;
        }

        .switch-modern {
            width: 60px;
            height: 30px;
            background: #e2e8f0;
            border-radius: 30px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .switch-modern.active {
            background: var(--success-gradient);
        }

        .switch-modern::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .switch-modern.active::before {
            transform: translateX(30px);
        }

        .btn-group-modern .btn {
            border-radius: 12px;
            margin: 0 3px;
            padding: 10px 14px;
            border: 2px solid;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-warning {
            border-color: #f093fb;
            color: #f093fb;
        }

        .btn-outline-warning:hover {
            background: var(--warning-gradient);
            border-color: #f093fb;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
        }

        .btn-outline-danger {
            border-color: #ff9a9e;
            color: #ff9a9e;
        }

        .btn-outline-danger:hover {
            background: var(--danger-gradient);
            border-color: #ff9a9e;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 154, 158, 0.3);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 15px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: white;
        }

        .alert-modern {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-radius: 20px 20px 0 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border: none;
            padding: 1rem 2rem 2rem;
        }

        @media (max-width: 768px) {
            .hero-section {
                margin: -1rem -1rem 1.5rem -1rem;
                padding: 2rem 1rem;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .table-modern td {
                padding: 1rem;
            }

            .cuestionario-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <div class="col-md-4 text-md-end">
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
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-briefcase"></i> Cuestionarios
                    </li>
                </ol>
            </nav>
        </div>

        <div class="hero-section">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>
                            <i class="fas fa-clipboard-list me-3"></i>
                            Gestión de Cuestionarios
                        </h1>
                        <p>Administra los cuestionarios psicosociales del sistema de manera eficiente</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <!-- Tabla de Cuestionarios -->
            <div class="modern-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Cuestionarios Disponibles
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                                <i class="fas fa-refresh me-1"></i>Refrescar
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createQuestionnaireModal">
                                <i class="fas fa-plus me-1"></i>Nuevo Cuestionario
                            </button>
                            <button class="btn btn-back btn-sm" data-bs-toggle="modal"
                                data-bs-target="#importQuestionnairesModal">
                                <a href="<?php echo e(route('gestion-instrumentos.index')); ?>" class="btn-back">
                                    <i class="fas fa-arrow-left me-2"></i>Volver a Gestión de Instrumentos
                                </a>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>CUESTIONARIO</th>
                                    <th>DETALLES</th>
                                    <th>ÍTEMS</th>
                                    <th>FECHAS</th>
                                    <th>TIPO</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Ficha de Datos Generales -->
                                <tr class="cuestionario-datos">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="cuestionario-icon">
                                                <i class="fas fa-id-card"></i>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.datos-generales')); ?>"
                                                    class="text-decoration-none fw-bold text-primary fs-6">
                                                    Ficha de Datos Generales
                                                </a>
                                                <br>
                                                <small class="text-muted">ID: datos-generales</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            Información demográfica y laboral básica del empleado
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info badge-modern">19 preguntas</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <strong>Creado:</strong> <?php echo e(date('d/m/Y')); ?><br>
                                            <strong>Modificado:</strong> <?php echo e(date('d/m/Y')); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary badge-modern">Datos</span>
                                    </td>
                                    <td>
                                        <div class="switch-modern active" data-cuestionario="datos-generales">
                                        </div>
                                        <small class="text-success d-block mt-1">Activo</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-modern" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.datos-generales')); ?>"
                                                class="btn btn-outline-primary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="editCuestionario('datos-generales', 'Ficha de Datos Generales')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCuestionario('datos-generales', 'Ficha de Datos Generales')"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Intralaboral Forma A -->
                                <tr class="cuestionario-intralaboral">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="cuestionario-icon">
                                                <i class="fas fa-briefcase"></i>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.intralaboral-forma-a')); ?>"
                                                    class="text-decoration-none fw-bold text-primary fs-6">
                                                    Intralaboral Forma A
                                                </a>
                                                <br>
                                                <small class="text-muted">ID: intralaboral-forma-a</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            Factores psicosociales intralaborales para profesionales
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning badge-modern">123 preguntas</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <strong>Creado:</strong> <?php echo e(date('d/m/Y')); ?><br>
                                            <strong>Modificado:</strong> <?php echo e(date('d/m/Y')); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary badge-modern">Intralaboral</span>
                                    </td>
                                    <td>
                                        <div class="switch-modern active" data-cuestionario="intralaboral-forma-a">
                                        </div>
                                        <small class="text-success d-block mt-1">Activo</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-modern" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.intralaboral-forma-a')); ?>"
                                                class="btn btn-outline-primary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="editCuestionario('intralaboral-forma-a', 'Intralaboral Forma A')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCuestionario('intralaboral-forma-a', 'Intralaboral Forma A')"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Intralaboral Forma B -->
                                <tr class="cuestionario-intralaboral">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="cuestionario-icon">
                                                <i class="fas fa-tools"></i>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.intralaboral-forma-b')); ?>"
                                                    class="text-decoration-none fw-bold text-primary fs-6">
                                                    Intralaboral Forma B
                                                </a>
                                                <br>
                                                <small class="text-muted">ID: intralaboral-forma-b</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            Factores psicosociales intralaborales para auxiliares y operarios
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning badge-modern">97 preguntas</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <strong>Creado:</strong> <?php echo e(date('d/m/Y')); ?><br>
                                            <strong>Modificado:</strong> <?php echo e(date('d/m/Y')); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary badge-modern">Intralaboral</span>
                                    </td>
                                    <td>
                                        <div class="switch-modern active" data-cuestionario="intralaboral-forma-b">
                                        </div>
                                        <small class="text-success d-block mt-1">Activo</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-modern" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.intralaboral-forma-b')); ?>"
                                                class="btn btn-outline-primary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="editCuestionario('intralaboral-forma-b', 'Intralaboral Forma B')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCuestionario('intralaboral-forma-b', 'Intralaboral Forma B')"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Extralaboral -->
                                <tr class="cuestionario-extralaboral">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="cuestionario-icon">
                                                <i class="fas fa-home"></i>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.extralaboral')); ?>"
                                                    class="text-decoration-none fw-bold text-primary fs-6">
                                                    Factores Extralaborales
                                                </a>
                                                <br>
                                                <small class="text-muted">ID: extralaboral</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            Condiciones del entorno extralaboral del empleado
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-success badge-modern">31 preguntas</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <strong>Creado:</strong> <?php echo e(date('d/m/Y')); ?><br>
                                            <strong>Modificado:</strong> <?php echo e(date('d/m/Y')); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-success badge-modern">Extralaboral</span>
                                    </td>
                                    <td>
                                        <div class="switch-modern active" data-cuestionario="extralaboral">
                                        </div>
                                        <small class="text-success d-block mt-1">Activo</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-modern" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.extralaboral')); ?>"
                                                class="btn btn-outline-primary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="editCuestionario('extralaboral', 'Factores Extralaborales')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCuestionario('extralaboral', 'Factores Extralaborales')"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Estrés -->
                                <tr class="cuestionario-estres">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="cuestionario-icon">
                                                <i class="fas fa-heartbeat"></i>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.estres')); ?>"
                                                    class="text-decoration-none fw-bold text-primary fs-6">
                                                    Cuestionario de Estrés
                                                </a>
                                                <br>
                                                <small class="text-muted">ID: estres</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            Evaluación de síntomas de estrés relacionados con el trabajo
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger badge-modern">31 preguntas</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <strong>Creado:</strong> <?php echo e(date('d/m/Y')); ?><br>
                                            <strong>Modificado:</strong> <?php echo e(date('d/m/Y')); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger badge-modern">Estrés</span>
                                    </td>
                                    <td>
                                        <div class="switch-modern active" data-cuestionario="estres">
                                        </div>
                                        <small class="text-success d-block mt-1">Activo</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-modern" role="group">
                                            <a href="<?php echo e(route('gestion-instrumentos.cuestionarios.estres')); ?>"
                                                class="btn btn-outline-primary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="editCuestionario('estres', 'Cuestionario de Estrés')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCuestionario('estres', 'Cuestionario de Estrés')"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="alert-modern">
                <h6><i class="fas fa-info-circle me-2"></i>Información Importante</h6>
                <ul class="mb-0">
                    <li>Los cuestionarios están basados en el manual oficial de la batería de instrumentos de evaluación
                        de factores de riesgo psicosocial.</li>
                    <li>Haz clic en el nombre de cualquier cuestionario para acceder a su vista completa.</li>
                    <li>Utiliza el botón "Editar" para modificar la configuración de cada cuestionario.</li>
                </ul>
            </div>
        </div>

        <!-- Modal para editar cuestionario -->
        <div class="modal fade" id="editQuestionnaireModal" tabindex="-1" aria-labelledby="editQuestionnaireModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editQuestionnaireModalLabel">
                            <i class="fas fa-edit text-warning me-2"></i>Editar Cuestionario
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editQuestionnaireForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="edit_nombre" class="form-label">Nombre del Cuestionario</label>
                                    <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                                </div>
                                <div class="col-12">
                                    <label for="edit_descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_tipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="edit_tipo" name="tipo">
                                        <option value="datos">Datos</option>
                                        <option value="intralaboral">Intralaboral</option>
                                        <option value="extralaboral">Extralaboral</option>
                                        <option value="estres">Estrés</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_estado" class="form-label">Estado</label>
                                    <select class="form-select" id="edit_estado" name="estado">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_num_preguntas" class="form-label">Número de Preguntas</label>
                                    <input type="number" class="form-control" id="edit_num_preguntas"
                                        name="num_preguntas" min="1">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_tiempo_estimado" class="form-label">Tiempo Estimado (minutos)</label>
                                    <input type="number" class="form-control" id="edit_tiempo_estimado"
                                        name="tiempo_estimado" min="1">
                                </div>
                            </div>
                            <input type="hidden" id="edit_cuestionario_id" name="cuestionario_id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-warning" onclick="saveQuestionnaireChanges()">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para crear nuevo cuestionario -->
        <div class="modal fade" id="createQuestionnaireModal" tabindex="-1"
            aria-labelledby="createQuestionnaireModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createQuestionnaireModalLabel">
                            <i class="fas fa-plus text-primary me-2"></i>Crear Nuevo Cuestionario
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createQuestionnaireForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="create_nombre" class="form-label">Nombre del Cuestionario</label>
                                    <input type="text" class="form-control" id="create_nombre" name="nombre"
                                        required>
                                </div>
                                <div class="col-12">
                                    <label for="create_descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="create_descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="create_tipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="create_tipo" name="tipo" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="datos">Datos</option>
                                        <option value="intralaboral">Intralaboral</option>
                                        <option value="extralaboral">Extralaboral</option>
                                        <option value="estres">Estrés</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="create_num_preguntas" class="form-label">Número de Preguntas</label>
                                    <input type="number" class="form-control" id="create_num_preguntas"
                                        name="num_preguntas" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="create_tiempo_estimado" class="form-label">Tiempo Estimado
                                        (minutos)</label>
                                    <input type="number" class="form-control" id="create_tiempo_estimado"
                                        name="tiempo_estimado" min="1">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="createQuestionnaire()">
                            <i class="fas fa-save me-1"></i>Crear Cuestionario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Inicialización de switches
            document.addEventListener('DOMContentLoaded', function() {
                // Agregar eventos a los switches
                document.querySelectorAll('.switch-modern').forEach(function(switchEl) {
                    switchEl.addEventListener('click', function() {
                        const cuestionarioId = this.dataset.cuestionario;
                        const isActive = this.classList.contains('active');
                        toggleQuestionnaireStatus(cuestionarioId, !isActive);
                    });
                });
            });

            // Función para alternar estado del cuestionario
            function toggleQuestionnaireStatus(cuestionarioId, newStatus) {
                const switchEl = document.querySelector(`[data-cuestionario="${cuestionarioId}"]`);
                const statusText = switchEl.parentElement.querySelector('small');

                if (newStatus) {
                    switchEl.classList.add('active');
                    statusText.textContent = 'Activo';
                    statusText.className = 'text-success d-block mt-1';
                } else {
                    switchEl.classList.remove('active');
                    statusText.textContent = 'Inactivo';
                    statusText.className = 'text-danger d-block mt-1';
                }

                // Aquí puedes agregar la llamada AJAX para guardar el estado en el servidor
                console.log(`Cuestionario ${cuestionarioId} ${newStatus ? 'activado' : 'desactivado'}`);

                // Mostrar notificación
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    icon: 'success',
                    title: `Cuestionario ${newStatus ? 'activado' : 'desactivado'} exitosamente`
                });
            }

            // Función para editar cuestionario
            function editCuestionario(id, nombre) {
                // Cargar datos del cuestionario (simulados)
                const cuestionarios = {
                    'datos-generales': {
                        nombre: 'Ficha de Datos Generales',
                        descripcion: 'Información demográfica y laboral básica del empleado',
                        tipo: 'datos',
                        estado: 'activo',
                        num_preguntas: 19,
                        tiempo_estimado: 10
                    },
                    'intralaboral-forma-a': {
                        nombre: 'Intralaboral Forma A',
                        descripcion: 'Factores psicosociales intralaborales para profesionales',
                        tipo: 'intralaboral',
                        estado: 'activo',
                        num_preguntas: 123,
                        tiempo_estimado: 30
                    },
                    'intralaboral-forma-b': {
                        nombre: 'Intralaboral Forma B',
                        descripcion: 'Factores psicosociales intralaborales para auxiliares y operarios',
                        tipo: 'intralaboral',
                        estado: 'activo',
                        num_preguntas: 97,
                        tiempo_estimado: 25
                    },
                    'extralaboral': {
                        nombre: 'Factores Extralaborales',
                        descripcion: 'Condiciones del entorno extralaboral del empleado',
                        tipo: 'extralaboral',
                        estado: 'activo',
                        num_preguntas: 31,
                        tiempo_estimado: 15
                    },
                    'estres': {
                        nombre: 'Cuestionario de Estrés',
                        descripcion: 'Evaluación de síntomas de estrés relacionados con el trabajo',
                        tipo: 'estres',
                        estado: 'activo',
                        num_preguntas: 31,
                        tiempo_estimado: 15
                    }
                };

                const data = cuestionarios[id];
                if (data) {
                    document.getElementById('edit_cuestionario_id').value = id;
                    document.getElementById('edit_nombre').value = data.nombre;
                    document.getElementById('edit_descripcion').value = data.descripcion;
                    document.getElementById('edit_tipo').value = data.tipo;
                    document.getElementById('edit_estado').value = data.estado;
                    document.getElementById('edit_num_preguntas').value = data.num_preguntas;
                    document.getElementById('edit_tiempo_estimado').value = data.tiempo_estimado;

                    const modal = new bootstrap.Modal(document.getElementById('editQuestionnaireModal'));
                    modal.show();
                }
            }

            // Función para guardar cambios del cuestionario
            function saveQuestionnaireChanges() {
                const form = document.getElementById('editQuestionnaireForm');
                const formData = new FormData(form);

                // Simular guardado
                Swal.fire({
                    title: '¿Guardar cambios?',
                    text: 'Se actualizará la configuración del cuestionario',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí iría la llamada AJAX real
                        console.log('Guardando cambios:', Object.fromEntries(formData));

                        Swal.fire({
                            title: 'Guardado',
                            text: 'Los cambios han sido guardados exitosamente',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'editQuestionnaireModal'));
                            modal.hide();
                            // Opcional: recargar la página o actualizar la tabla
                            // location.reload();
                        });
                    }
                });
            }

            // Función para eliminar cuestionario
            function deleteCuestionario(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar cuestionario?',
                    text: `¿Está seguro de eliminar "${nombre}"? Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí iría la llamada AJAX para eliminar
                        console.log('Eliminando cuestionario:', id);

                        Swal.fire({
                            title: 'Eliminado',
                            text: 'El cuestionario ha sido eliminado.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Opcional: remover la fila de la tabla o recargar
                            // location.reload();
                        });
                    }
                });
            }

            // Función para crear nuevo cuestionario
            function createQuestionnaire() {
                const form = document.getElementById('createQuestionnaireForm');
                const formData = new FormData(form);

                // Validar campos requeridos
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Simular creación
                console.log('Creando cuestionario:', Object.fromEntries(formData));

                Swal.fire({
                    title: 'Creado',
                    text: 'El cuestionario ha sido creado exitosamente',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createQuestionnaireModal'));
                    modal.hide();
                    form.reset();
                    // Opcional: recargar la página o actualizar la tabla
                    // location.reload();
                });
            }

            // Función para refrescar tabla
            function refreshTable() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    icon: 'info',
                    title: 'Actualizando tabla...'
                });

                // Aquí iría la lógica para recargar los datos
                setTimeout(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        icon: 'success',
                        title: 'Tabla actualizada'
                    });
                }, 1000);
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/cuestionarios/index.blade.php ENDPATH**/ ?>