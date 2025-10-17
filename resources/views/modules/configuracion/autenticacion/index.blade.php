@extends('layouts.dashboard')

@section('title', 'Configuración de Autenticación')

@push('styles')
    <style>
        .auth-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 123, 255, 0.3);
        }

        .auth-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .auth-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
        }

        .section-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: white;
            font-size: 14px;
        }

        .general-icon {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .twofa-icon {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .access-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .users-icon {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 123, 255, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.4);
        }

        .stats-card {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 86, 179, 0.1));
            border: 1px solid rgba(0, 123, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .table thead th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            font-weight: 500;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.4);
        }

        .status-inactive {
            background: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.4);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 107, 107, 0.1));
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="auth-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-lock me-3"></i>Configuración de Autenticación
                    </h1>
                    <p class="mb-0 opacity-90">Gestiona las políticas de autenticación y control de acceso del sistema</p>
                </div>
                <a href="{{ route('configuracion.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Configuración General -->
            <div class="col-md-6">
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-icon general-icon">
                            <i class="fas fa-cog"></i>
                        </span>
                        Configuración General
                    </h5>

                    <form action="{{ route('configuracion.autenticacion.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="seccion" value="general">

                        <div class="mb-3">
                            <label for="timeout_sesion" class="form-label">Timeout de Sesión (minutos)</label>
                            <input type="number" class="form-control" name="timeout_sesion" id="timeout_sesion"
                                value="{{ $configuracion['timeout_sesion'] ?? 60 }}" min="5" max="1440">
                            <small class="text-muted">Tiempo de inactividad antes de cerrar sesión</small>
                        </div>

                        <div class="mb-3">
                            <label for="intentos_maximos" class="form-label">Intentos máximos de login</label>
                            <input type="number" class="form-control" name="intentos_maximos" id="intentos_maximos"
                                value="{{ $configuracion['intentos_maximos'] ?? 5 }}" min="3" max="10">
                        </div>

                        <div class="mb-3">
                            <label for="bloqueo_tiempo" class="form-label">Tiempo de bloqueo (minutos)</label>
                            <input type="number" class="form-control" name="bloqueo_tiempo" id="bloqueo_tiempo"
                                value="{{ $configuracion['bloqueo_tiempo'] ?? 15 }}" min="5" max="60">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="recordar_sesion" id="recordar_sesion"
                                {{ $configuracion['recordar_sesion'] ?? true ? 'checked' : '' }}>
                            <label class="form-check-label" for="recordar_sesion">
                                Permitir "Recordar sesión"
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Configuración
                        </button>
                    </form>
                </div>
            </div>

            <!-- Autenticación de Dos Factores -->
            <div class="col-md-6">
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-icon twofa-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </span>
                        Autenticación de Dos Factores
                    </h5>

                    <form action="{{ route('configuracion.autenticacion.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="seccion" value="2fa">

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="twofa_habilitado" id="twofa_habilitado"
                                {{ $configuracion['twofa_habilitado'] ?? false ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="twofa_habilitado">
                                Habilitar 2FA para todos los usuarios
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="twofa_metodo" class="form-label">Método predeterminado</label>
                            <select class="form-select" name="twofa_metodo" id="twofa_metodo">
                                <option value="totp"
                                    {{ ($configuracion['twofa_metodo'] ?? 'totp') == 'totp' ? 'selected' : '' }}>TOTP
                                    (Google Authenticator)</option>
                                <option value="sms"
                                    {{ ($configuracion['twofa_metodo'] ?? 'totp') == 'sms' ? 'selected' : '' }}>SMS
                                </option>
                                <option value="email"
                                    {{ ($configuracion['twofa_metodo'] ?? 'totp') == 'email' ? 'selected' : '' }}>Email
                                </option>
                            </select>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="twofa_obligatorio_admin"
                                id="twofa_obligatorio_admin"
                                {{ $configuracion['twofa_obligatorio_admin'] ?? true ? 'checked' : '' }}>
                            <label class="form-check-label" for="twofa_obligatorio_admin">
                                Obligatorio para administradores
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Config. 2FA
                        </button>
                    </form>
                </div>
            </div>

            <!-- Control de Acceso -->
            <div class="col-md-6">
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-icon access-icon">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        Control de Acceso
                    </h5>

                    <form action="{{ route('configuracion.autenticacion.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="seccion" value="acceso">

                        <div class="mb-3">
                            <label for="ips_permitidas" class="form-label">IPs Permitidas (opcional)</label>
                            <textarea class="form-control" name="ips_permitidas" id="ips_permitidas" rows="3"
                                placeholder="192.168.1.0/24&#10;10.0.0.1">{{ $configuracion['ips_permitidas'] ?? '' }}</textarea>
                            <small class="text-muted">Una IP o rango por línea. Vacío = todas las IPs</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="geobloqueo_habilitado"
                                id="geobloqueo_habilitado"
                                {{ $configuracion['geobloqueo_habilitado'] ?? false ? 'checked' : '' }}>
                            <label class="form-check-label" for="geobloqueo_habilitado">
                                Habilitar geobloqueo
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="paises_bloqueados" class="form-label">Países bloqueados</label>
                            <input type="text" class="form-control" name="paises_bloqueados" id="paises_bloqueados"
                                value="{{ $configuracion['paises_bloqueados'] ?? '' }}" placeholder="CN,RU,IR">
                            <small class="text-muted">Códigos de país separados por comas</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Control Acceso
                        </button>
                    </form>
                </div>
            </div>

            <!-- Gestión de Usuarios -->
            <div class="col-md-6">
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-icon users-icon">
                            <i class="fas fa-users"></i>
                        </span>
                        Gestión de Usuarios
                    </h5>

                    <!-- Estadísticas rápidas -->
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number">{{ $configuracion['total_usuarios'] ?? 0 }}</div>
                                <div class="stats-label">Total Usuarios</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number">{{ $configuracion['usuarios_activos'] ?? 0 }}</div>
                                <div class="stats-label">Activos</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number">{{ $configuracion['usuarios_bloqueados'] ?? 0 }}</div>
                                <div class="stats-label">Bloqueados</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('configuracion.autenticacion.usuarios') }}" class="btn btn-success">
                            <i class="fas fa-users-cog me-2"></i>Gestionar Usuarios
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="desbloquearTodos()">
                            <i class="fas fa-unlock me-2"></i>Desbloquear Todos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuarios Recientes -->
        <div class="auth-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Últimos Accesos
                </h5>
            </div>
            <div class="card-body">
                @if (!empty($configuracion['ultimos_accesos']))
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Último Acceso</th>
                                    <th>IP</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($configuracion['ultimos_accesos'] as $acceso)
                                    <tr>
                                        <td class="fw-bold">{{ $acceso['nombre'] ?? 'Sin nombre' }}</td>
                                        <td>{{ $acceso['email'] ?? 'Sin email' }}</td>
                                        <td>{{ $acceso['ultimo_acceso'] ?? 'Nunca' }}</td>
                                        <td><small class="text-muted">{{ $acceso['ip'] ?? 'N/A' }}</small></td>
                                        <td>
                                            <span
                                                class="status-indicator {{ $acceso['activo'] ? 'status-active' : 'status-inactive' }}"></span>
                                            <span class="badge {{ $acceso['activo'] ? 'bg-success' : 'bg-danger' }}">
                                                {{ $acceso['activo'] ? 'Activo' : 'Bloqueado' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($acceso['activo'])
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="bloquearUsuario('{{ $acceso['id'] ?? '' }}')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-success btn-sm"
                                                    onclick="desbloquearUsuario('{{ $acceso['id'] ?? '' }}')">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-primary btn-sm"
                                                onclick="restablecerPassword('{{ $acceso['id'] ?? '' }}')">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay registros de acceso recientes</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function bloquearUsuario(usuarioId) {
            if (confirm('¿Estás seguro de que deseas bloquear este usuario?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.autenticacion.usuarios.toggle-bloqueo') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const usuarioInput = document.createElement('input');
                usuarioInput.type = 'hidden';
                usuarioInput.name = 'usuario_id';
                usuarioInput.value = usuarioId;

                const accionInput = document.createElement('input');
                accionInput.type = 'hidden';
                accionInput.name = 'accion';
                accionInput.value = 'bloquear';

                form.appendChild(csrfToken);
                form.appendChild(usuarioInput);
                form.appendChild(accionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function desbloquearUsuario(usuarioId) {
            if (confirm('¿Estás seguro de que deseas desbloquear este usuario?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.autenticacion.usuarios.toggle-bloqueo') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const usuarioInput = document.createElement('input');
                usuarioInput.type = 'hidden';
                usuarioInput.name = 'usuario_id';
                usuarioInput.value = usuarioId;

                const accionInput = document.createElement('input');
                accionInput.type = 'hidden';
                accionInput.name = 'accion';
                accionInput.value = 'desbloquear';

                form.appendChild(csrfToken);
                form.appendChild(usuarioInput);
                form.appendChild(accionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function restablecerPassword(usuarioId) {
            if (confirm('¿Estás seguro de que deseas restablecer la contraseña de este usuario?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.autenticacion.usuarios.restablecer-contrasena') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const usuarioInput = document.createElement('input');
                usuarioInput.type = 'hidden';
                usuarioInput.name = 'usuario_id';
                usuarioInput.value = usuarioId;

                form.appendChild(csrfToken);
                form.appendChild(usuarioInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function desbloquearTodos() {
            if (confirm('¿Estás seguro de que deseas desbloquear TODOS los usuarios bloqueados?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.autenticacion.usuarios.toggle-bloqueo') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const accionInput = document.createElement('input');
                accionInput.type = 'hidden';
                accionInput.name = 'accion';
                accionInput.value = 'desbloquear_todos';

                form.appendChild(csrfToken);
                form.appendChild(accionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
