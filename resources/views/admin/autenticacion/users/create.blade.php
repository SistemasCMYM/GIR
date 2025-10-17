@extends('layouts.dashboard')

@section('title', 'Crear Usuario - GIR-365')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-user-plus mr-2"></i>Crear Usuario</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/Inicio">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="/modules/auth">Autenticación</a></li>
                            <li class="breadcrumb-item"><a href="/modules/auth/users">Usuarios</a></li>
                            <li class="breadcrumb-item active">Crear</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- Alerts -->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error en la validación!</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('auth.users.store') }}" method="POST" id="create-user-form">
                    @csrf

                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user mr-1"></i>
                                        Información Personal
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('nombre') is-invalid @enderror"
                                                    id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                                @error('nombre')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="apellido">Apellido <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('apellido') is-invalid @enderror"
                                                    id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                                @error('apellido')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" value="{{ old('email') }}" required>
                                                @error('email')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="tel"
                                                    class="form-control @error('telefono') is-invalid @enderror"
                                                    id="telefono" name="telefono" value="{{ old('telefono') }}">
                                                @error('telefono')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Contraseña <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            id="toggle-password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    @error('password')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">Mínimo 8 caracteres</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirmar Contraseña <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control"
                                                        id="password_confirmation" name="password_confirmation" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            id="toggle-password-confirm">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Access Configuration -->
                        <div class="col-md-4">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-cog mr-1"></i>
                                        Configuración de Acceso
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="perfil_id">Perfil <span class="text-danger">*</span></label>
                                        <select class="form-control @error('perfil_id') is-invalid @enderror"
                                            id="perfil_id" name="perfil_id" required>
                                            <option value="">Seleccionar perfil</option>
                                            @foreach ($perfiles as $perfil)
                                                <option value="{{ $perfil->_id }}"
                                                    {{ old('perfil_id') == $perfil->_id ? 'selected' : '' }}>
                                                    {{ $perfil->nombre }} (Nivel {{ $perfil->nivel }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('perfil_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="empresa_id">Empresa <span class="text-danger">*</span></label>
                                        <select class="form-control @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id" required>
                                            <option value="">Seleccionar empresa</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->_id }}"
                                                    {{ old('empresa_id') == $empresa->_id ? 'selected' : '' }}>
                                                    {{ $empresa->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('empresa_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="empleado_id">Empleado (Opcional)</label>
                                        <select class="form-control @error('empleado_id') is-invalid @enderror"
                                            id="empleado_id" name="empleado_id">
                                            <option value="">Sin empleado asignado</option>
                                            @foreach ($empleados as $empleado)
                                                <option value="{{ $empleado->_id }}"
                                                    {{ old('empleado_id') == $empleado->_id ? 'selected' : '' }}>
                                                    {{ $empleado->nombres }} {{ $empleado->apellidos }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('empleado_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="activo"
                                                name="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="activo">Usuario Activo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Info -->
                            <div class="card card-secondary" id="profile-info" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Información del Perfil
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div id="profile-details"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>
                                        Crear Usuario
                                    </button>
                                    <a href="{{ route('auth.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#toggle-password').click(function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#toggle-password-confirm').click(function() {
                const passwordField = $('#password_confirmation');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Handle profile selection
            $('#perfil_id').change(function() {
                const perfilId = $(this).val();
                if (perfilId) {
                    $.ajax({
                        url: '/modules/auth/profiles/' + perfilId + '/info',
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                $('#profile-details').html(
                                    '<p><strong>Descripción:</strong><br>' + response.perfil
                                    .descripcion + '</p>' +
                                    '<p><strong>Nivel:</strong> ' + response.perfil.nivel +
                                    '</p>' +
                                    '<p><strong>Módulos:</strong> ' + response.perfil
                                    .modulos.length + '</p>' +
                                    '<p><strong>Permisos:</strong> ' + response.perfil
                                    .permisos.length + '</p>'
                                );
                                $('#profile-info').show();
                            }
                        },
                        error: function() {
                            $('#profile-info').hide();
                        }
                    });
                } else {
                    $('#profile-info').hide();
                }
            });

            // Filter employees by company
            $('#empresa_id').change(function() {
                const empresaId = $(this).val();
                const empleadoSelect = $('#empleado_id');

                // Clear current options
                empleadoSelect.empty().append('<option value="">Cargando empleados...</option>');

                if (empresaId) {
                    $.ajax({
                        url: '/api/empresas/' + empresaId + '/empleados',
                        type: 'GET',
                        success: function(response) {
                            empleadoSelect.empty().append(
                                '<option value="">Sin empleado asignado</option>');
                            if (response.success && response.empleados.length > 0) {
                                response.empleados.forEach(function(empleado) {
                                    empleadoSelect.append(
                                        '<option value="' + empleado.id + '">' +
                                        empleado.nombres + ' ' + empleado
                                        .apellidos +
                                        '</option>'
                                    );
                                });
                            }
                        },
                        error: function() {
                            empleadoSelect.empty().append(
                                '<option value="">Sin empleado asignado</option>');
                        }
                    });
                } else {
                    empleadoSelect.empty().append('<option value="">Sin empleado asignado</option>');
                }
            });

            // Form validation
            $('#create-user-form').submit(function(e) {
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();

                if (password !== passwordConfirm) {
                    e.preventDefault();
                    toastr.error('Las contraseñas no coinciden');
                    return false;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    toastr.error('La contraseña debe tener al menos 8 caracteres');
                    return false;
                }
            });

            // Load profile info if already selected
            if ($('#perfil_id').val()) {
                $('#perfil_id').trigger('change');
            }

            // Load employees if company already selected
            if ($('#empresa_id').val()) {
                $('#empresa_id').trigger('change');
            }
        });
    </script>
@endsection
