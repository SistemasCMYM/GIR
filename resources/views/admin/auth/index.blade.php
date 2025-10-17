@extends('layouts.dashboard')

@section('title', 'Gestión de Autenticación - Super Admin')

@push('styles')
    <style>
        .super-admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .module-nav-tabs .nav-link {
            border-radius: 10px 10px 0 0;
            margin-right: 5px;
            font-weight: 500;
        }

        .module-nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
        }

        .admin-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .action-btn {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header del Super Admin -->
        <div class="super-admin-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2><i class="fas fa-crown mr-2"></i>Panel de Super Administrador</h2>
                    <p class="mb-0">Gestión de Autenticación y Usuarios del Sistema</p>
                </div>
                <div class="col-auto">
                    <span class="badge badge-light badge-lg">
                        <i class="fas fa-user-shield mr-1"></i>
                        {{ Auth::user()->nick ?? Auth::user()->email }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navegación por pestañas -->
        <ul class="nav nav-tabs module-nav-tabs" id="authTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                    type="button" role="tab">
                    <i class="fas fa-chart-line mr-1"></i>Resumen
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button"
                    role="tab">
                    <i class="fas fa-users mr-1"></i>Usuarios
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="empresas-tab" data-bs-toggle="tab" data-bs-target="#empresas" type="button"
                    role="tab">
                    <i class="fas fa-building mr-1"></i>Empresas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button"
                    role="tab">
                    <i class="fas fa-user-tag mr-1"></i>Roles y Permisos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button"
                    role="tab">
                    <i class="fas fa-history mr-1"></i>Auditoría
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="authTabContent">
            <!-- Pestaña Resumen -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $statsData['total_usuarios'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Usuarios</p>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $statsData['total_empresas'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Empresas</p>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-building fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $statsData['usuarios_activos'] ?? 0 }}</h3>
                                    <p class="mb-0">Usuarios Activos</p>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $statsData['empresas_activas'] ?? 0 }}</h3>
                                    <p class="mb-0">Empresas Activas</p>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="admin-card card">
                            <div class="card-body text-center">
                                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                <h5>Crear Usuario</h5>
                                <p class="text-muted">Agregar nuevo usuario al sistema</p>
                                <a href="{{ route('usuarios.cuentas.create') }}" class="btn btn-primary action-btn">
                                    <i class="fas fa-plus mr-1"></i>Crear Usuario
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-card card">
                            <div class="card-body text-center">
                                <i class="fas fa-building fa-3x text-success mb-3"></i>
                                <h5>Agregar Empresa</h5>
                                <p class="text-muted">Registrar nueva empresa en el sistema</p>
                                <a href="{{ route('empresa.empresas.create') }}" class="btn btn-success action-btn">
                                    <i class="fas fa-plus mr-1"></i>Crear Empresa
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-card card">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fa-3x text-warning mb-3"></i>
                                <h5>Gestionar Permisos</h5>
                                <p class="text-muted">Configurar roles y permisos</p>
                                <a href="{{ route('usuarios.permisos.index') }}" class="btn btn-warning action-btn">
                                    <i class="fas fa-cog mr-1"></i>Configurar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña Usuarios -->
            <div class="tab-pane fade" id="usuarios" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="admin-card card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fas fa-users mr-2"></i>Gestión de Usuarios
                                    <a href="{{ route('usuarios.cuentas.create') }}"
                                        class="btn btn-primary btn-sm float-right">
                                        <i class="fas fa-plus mr-1"></i>Nuevo Usuario
                                    </a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="usuariosTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Email</th>
                                                <th>Nick</th>
                                                <th>Empresa</th>
                                                <th>Rol</th>
                                                <th>Estado</th>
                                                <th>Último Acceso</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Los datos se cargarán via AJAX -->
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando usuarios...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña Empresas -->
            <div class="tab-pane fade" id="empresas" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="admin-card card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fas fa-building mr-2"></i>Gestión de Empresas
                                    <a href="{{ route('empresa.empresas.create') }}"
                                        class="btn btn-success btn-sm float-right">
                                        <i class="fas fa-plus mr-1"></i>Nueva Empresa
                                    </a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="empresasTable">
                                        <thead>
                                            <tr>
                                                <th>NIT</th>
                                                <th>Razón Social</th>
                                                <th>Nombre Comercial</th>
                                                <th>Usuarios</th>
                                                <th>Estado</th>
                                                <th>Fecha Registro</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Los datos se cargarán via AJAX -->
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando empresas...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña Roles y Permisos -->
            <div class="tab-pane fade" id="roles" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="admin-card card">
                            <div class="card-header">
                                <h5><i class="fas fa-user-tag mr-2"></i>Roles del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Super Administrador</h6>
                                            <small>Acceso completo al sistema</small>
                                        </div>
                                        <span class="badge badge-danger badge-pill">1</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Administrador</h6>
                                            <small>Gestión de empresa y usuarios</small>
                                        </div>
                                        <span
                                            class="badge badge-primary badge-pill">{{ $statsData['admins_empresas'] ?? 0 }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Usuario</h6>
                                            <small>Acceso a módulos asignados</small>
                                        </div>
                                        <span
                                            class="badge badge-success badge-pill">{{ $statsData['usuarios_normales'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="admin-card card">
                            <div class="card-header">
                                <h5><i class="fas fa-shield-alt mr-2"></i>Permisos por Módulo</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <h6 class="mb-2">Hallazgos</h6>
                                        <span class="badge badge-info mr-1">Ver</span>
                                        <span class="badge badge-warning mr-1">Crear</span>
                                        <span class="badge badge-success mr-1">Editar</span>
                                        <span class="badge badge-danger">Eliminar</span>
                                    </div>
                                    <div class="list-group-item">
                                        <h6 class="mb-2">Psicosocial</h6>
                                        <span class="badge badge-info mr-1">Ver</span>
                                        <span class="badge badge-warning mr-1">Evaluar</span>
                                        <span class="badge badge-success">Reportes</span>
                                    </div>
                                    <div class="list-group-item">
                                        <h6 class="mb-2">Planes</h6>
                                        <span class="badge badge-info mr-1">Ver</span>
                                        <span class="badge badge-warning mr-1">Crear</span>
                                        <span class="badge badge-success">Gestionar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña Auditoría -->
            <div class="tab-pane fade" id="auditoria" role="tabpanel">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="admin-card card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fas fa-history mr-2"></i>Registro de Auditoría
                                </h4>
                            </div>
                            <div class="card-body">
                                <!-- Filtros -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select class="form-control" id="filtroAccion">
                                            <option value="">Todas las acciones</option>
                                            <option value="login">Inicio de sesión</option>
                                            <option value="logout">Cierre de sesión</option>
                                            <option value="create">Crear</option>
                                            <option value="update">Actualizar</option>
                                            <option value="delete">Eliminar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" id="filtroFecha" placeholder="Fecha">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="filtroUsuario"
                                            placeholder="Usuario">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary" onclick="filtrarAuditoria()">
                                            <i class="fas fa-search mr-1"></i>Filtrar
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="auditoriaTable">
                                        <thead>
                                            <tr>
                                                <th>Fecha/Hora</th>
                                                <th>Usuario</th>
                                                <th>Acción</th>
                                                <th>Módulo</th>
                                                <th>IP</th>
                                                <th>Detalles</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Los datos se cargarán via AJAX -->
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando registros de
                                                    auditoría...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>

    <!-- Modales para gestión de usuarios -->
    <div class="modal fade" id="userEditModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="userEditForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contraseña (dejar vacío para no cambiar)</label>
                                    <input type="password" class="form-control" id="edit_password" name="password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirmar Contraseña</label>
                                    <input type="password" class="form-control" id="edit_password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rol</label>
                                    <select class="form-control" id="edit_rol" name="rol" required>
                                        <option value="super_admin">Super Administrador</option>
                                        <option value="admin">Administrador</option>
                                        <option value="coordinador">Coordinador</option>
                                        <option value="tecnico">Técnico</option>
                                        <option value="observador">Observador</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select class="form-control" id="edit_empresa_id" name="empresa_id" required>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit_activo" name="activo"
                                    value="1" checked>
                                <label class="custom-control-label" for="edit_activo">Usuario Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para crear usuario -->
    <div class="modal fade" id="userCreateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="userCreateForm" action="{{ route('usuarios.cuentas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirmar Contraseña</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rol</label>
                                    <select class="form-control" name="rol" required>
                                        <option value="">Seleccionar Rol</option>
                                        <option value="admin">Administrador</option>
                                        <option value="coordinador">Coordinador</option>
                                        <option value="tecnico">Técnico</option>
                                        <option value="observador">Observador</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select class="form-control" name="empresa_id" required>
                                        <option value="">Seleccionar Empresa</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Configurar toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Manejar formulario de crear usuario
            $('#userCreateForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#userCreateModal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.message || 'Error al crear usuario');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.errors) {
                            Object.values(response.errors).forEach(function(errors) {
                                errors.forEach(function(error) {
                                    toastr.error(error);
                                });
                            });
                        } else {
                            toastr.error('Error al crear usuario');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Manejar formulario de editar usuario
            $('#userEditForm').on('submit', function(e) {
                e.preventDefault();

                const userId = $('#edit_user_id').val();
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Guardando...');

                $.ajax({
                    url: `/admin/auth/users/${userId}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#userEditModal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.message || 'Error al actualizar usuario');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.errors) {
                            Object.values(response.errors).forEach(function(errors) {
                                errors.forEach(function(error) {
                                    toastr.error(error);
                                });
                            });
                        } else {
                            toastr.error('Error al actualizar usuario');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        // Funciones para cargar datos via AJAX (placeholder para futuras implementaciones)
        function cargarUsuarios() {
            // TODO: Implementar carga de usuarios via AJAX
            console.log('Cargando usuarios...');
        }

        function cargarEmpresas() {
            // TODO: Implementar carga de empresas via AJAX
            console.log('Cargando empresas...');
        }

        function filtrarAuditoria() {
            // TODO: Implementar filtrado de auditoría
            console.log('Filtrando auditoría...');
        }

        // Cargar datos al cambiar de pestaña
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute('data-bs-target');
                    if (target === '#usuarios') {
                        cargarUsuarios();
                    } else if (target === '#empresas') {
                        cargarEmpresas();
                    }
                });
            });

            // Cargar estadísticas iniciales
            cargarUsuarios();
        });
    </script>
@endpush
