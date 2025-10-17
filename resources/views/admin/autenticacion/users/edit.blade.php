@extends('layouts.dashboard')

@section('title', 'Editar Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auth.index') }}">Autenticación</a></li>
    <li class="breadcrumb-item"><a href="{{ route('auth.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Información del Usuario</h3>
                    </div>
                    <form id="editUserForm" action="{{ route('auth.users.update', $user->_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre *</label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                            id="nombre" name="nombre" value="{{ old('nombre', $user->nombre) }}"
                                            required>
                                        @error('nombre')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellido">Apellido *</label>
                                        <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                            id="apellido" name="apellido" value="{{ old('apellido', $user->apellido) }}"
                                            required>
                                        @error('apellido')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                            id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}">
                                        @error('telefono')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa_id">Empresa *</label>
                                        <select class="form-control select2 @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id" required>
                                            <option value="">Seleccionar empresa...</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->_id }}"
                                                    {{ old('empresa_id', $user->empresa_id) == $empresa->_id ? 'selected' : '' }}>
                                                    {{ $empresa->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('empresa_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empleado_id">Empleado</label>
                                        <select class="form-control select2" id="empleado_id" name="empleado_id">
                                            <option value="">Seleccionar empleado...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="perfil_id">Perfil *</label>
                                        <select class="form-control select2 @error('perfil_id') is-invalid @enderror"
                                            id="perfil_id" name="perfil_id" required>
                                            <option value="">Seleccionar perfil...</option>
                                            @foreach ($perfiles as $perfil)
                                                <option value="{{ $perfil->_id }}"
                                                    {{ old('perfil_id', $user->perfil_id) == $perfil->_id ? 'selected' : '' }}>
                                                    {{ $perfil->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('perfil_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado">Estado *</label>
                                        <select class="form-control @error('estado') is-invalid @enderror" id="estado"
                                            name="estado" required>
                                            <option value="activo"
                                                {{ old('estado', $user->estado) == 'activo' ? 'selected' : '' }}>
                                                Activo
                                            </option>
                                            <option value="inactivo"
                                                {{ old('estado', $user->estado) == 'inactivo' ? 'selected' : '' }}>
                                                Inactivo
                                            </option>
                                        </select>
                                        @error('estado')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="cambiar_password"
                                        name="cambiar_password">
                                    <label for="cambiar_password" class="custom-control-label">
                                        Cambiar contraseña
                                    </label>
                                </div>
                            </div>

                            <div id="password-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Nueva Contraseña</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password">
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Usuario
                            </button>
                            <a href="{{ route('auth.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Profile Information Card -->
                <div class="card card-widget widget-user">
                    <div class="widget-user-header bg-info">
                        <h3 class="widget-user-username">{{ $user->nombre }} {{ $user->apellido }}</h3>
                        <h5 class="widget-user-desc">{{ $user->perfil ? $user->perfil->nombre : 'Sin perfil' }}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle elevation-2" src="{{ asset('img/default-avatar.png') }}"
                            alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        {{ $user->empresa ? $user->empresa->nombre : 'Sin empresa' }}</h5>
                                    <span class="description-text">EMPRESA</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span class="badge badge-{{ $user->estado == 'activo' ? 'success' : 'danger' }}">
                                            {{ ucfirst($user->estado) }}
                                        </span>
                                    </h5>
                                    <span class="description-text">ESTADO</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Details Card -->
                <div class="card card-info" id="profile-info" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">Información del Perfil</h3>
                    </div>
                    <div class="card-body">
                        <div id="profile-details">
                            <!-- Profile information will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Load employees when company changes
            $('#empresa_id').change(function() {
                const empresaId = $(this).val();
                const empleadoSelect = $('#empleado_id');

                empleadoSelect.empty().append('<option value="">Seleccionar empleado...</option>');

                if (empresaId) {
                    $.get(`/api/empresas/${empresaId}/empleados`)
                        .done(function(empleados) {
                            empleados.forEach(function(empleado) {
                                const selected = '{{ $user->empleado_id }}' === empleado._id ?
                                    'selected' : '';
                                empleadoSelect.append(
                                    `<option value="${empleado._id}" ${selected}>${empleado.nombre} ${empleado.apellido}</option>`
                                );
                            });
                        })
                        .fail(function() {
                            toastr.error('Error al cargar empleados');
                        });
                }
            });

            // Load profile information when profile changes
            $('#perfil_id').change(function() {
                const perfilId = $(this).val();
                const profileInfo = $('#profile-info');
                const profileDetails = $('#profile-details');

                if (perfilId) {
                    $.get(`/api/perfiles/${perfilId}`)
                        .done(function(perfil) {
                            let html = `
                        <p><strong>Descripción:</strong> ${perfil.descripcion || 'Sin descripción'}</p>
                        <p><strong>Permisos:</strong></p>
                        <ul>
                    `;

                            if (perfil.permisos && perfil.permisos.length > 0) {
                                perfil.permisos.forEach(function(permiso) {
                                    html += `<li>${permiso}</li>`;
                                });
                            } else {
                                html += '<li>Sin permisos específicos</li>';
                            }

                            html += '</ul>';
                            profileDetails.html(html);
                            profileInfo.show();
                        })
                        .fail(function() {
                            profileInfo.hide();
                        });
                } else {
                    profileInfo.hide();
                }
            });

            // Toggle password fields
            $('#cambiar_password').change(function() {
                const passwordFields = $('#password-fields');
                const passwordInput = $('#password');
                const confirmPasswordInput = $('#password_confirmation');

                if ($(this).is(':checked')) {
                    passwordFields.show();
                    passwordInput.attr('required', true);
                } else {
                    passwordFields.hide();
                    passwordInput.attr('required', false);
                    passwordInput.val('');
                    confirmPasswordInput.val('');
                }
            });

            // Load initial data
            $('#empresa_id').trigger('change');
            $('#perfil_id').trigger('change');

            // Form submission
            $('#editUserForm').submit(function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Validate password confirmation if changing password
                if ($('#cambiar_password').is(':checked')) {
                    const password = $('#password').val();
                    const confirmPassword = $('#password_confirmation').val();

                    if (password !== confirmPassword) {
                        toastr.error('Las contraseñas no coinciden');
                        return;
                    }
                }

                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Actualizando...').prop('disabled',
                    true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message ||
                                'Usuario actualizado correctamente');
                            setTimeout(() => {
                                window.location.href =
                                    '{{ route('auth.users.index') }}';
                            }, 1500);
                        } else {
                            toastr.error(response.message || 'Error al actualizar usuario');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error al actualizar usuario';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            message = errors.join('<br>');
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
