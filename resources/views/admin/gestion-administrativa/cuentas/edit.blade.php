@extends('layouts.dashboard')

@section('title', 'Editar Cuenta')

@section('content')
    <div class="tw-p-4 tw-min-h-screen tw-bg-gray-50">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="tw-mb-3">
            <ol class="tw-flex tw-items-center tw-space-x-2 tw-text-sm tw-text-gray-600">
                <li>
                    <a href="{{ route('empleados.index') }}"
                        class="tw-text-gray-500 hover:tw-text-primary-600 tw-transition-colors tw-no-underline">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="tw-text-gray-400">/</li>
                <li>
                    <a href="{{ route('usuarios.index') }}"
                        class="tw-text-gray-500 hover:tw-text-primary-600 tw-transition-colors tw-no-underline">
                        <i class="fas fa-users-cog"></i> Gestión Administrativa
                    </a>
                </li>
                <li class="tw-text-gray-400">/</li>
                <li>
                    <a href="{{ route('usuarios.cuentas.index') }}"
                        class="tw-text-gray-500 hover:tw-text-primary-600 tw-transition-colors tw-no-underline">
                        <i class="fas fa-users"></i> Cuentas
                    </a>
                </li>
                <li class="tw-text-gray-400">/</li>
                <li class="tw-text-gray-900 tw-font-medium" aria-current="page">
                    Editar: {{ $cuenta->nombre ?? 'N/A' }}
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="gir-card tw-mb-6">
            <div class="gir-card-header">
                <div class="gir-card-header-content">
                    <h1 class="gir-card-title">
                        <i class="fas fa-user-edit tw-mr-2"></i>Editar Cuenta
                    </h1>
                    <p class="gir-card-subtitle">Modifique los datos de la cuenta de usuario</p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form action="{{ route('usuarios.cuentas.update', $cuenta->id) }}" method="POST" id="editAccountForm">
            @csrf
            @method('PUT')

            <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-6">
                <div class="lg:tw-col-span-2">
                    <!-- Información Básica -->
                    <div class="gir-card tw-mb-6">
                        <div class="gir-card-header">
                            <h6 class="gir-card-section-title">
                                <i class="fas fa-user tw-mr-2"></i>Información Básica
                            </h6>
                        </div>
                        <div class="gir-card-body">
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                                <div>
                                    <label for="nombre"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">
                                        Nombre <span class="tw-text-red-500 tw-text-sm">*</span>
                                    </label>
                                    <input type="text"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('nombre') tw-border-red-500 @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre', $cuenta->nombre) }}" required>
                                    @error('nombre')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="apellido"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Apellido</label>
                                    <input type="text"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('apellido') tw-border-red-500 @enderror"
                                        id="apellido" name="apellido" value="{{ old('apellido', $cuenta->apellido) }}">
                                    @error('apellido')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">
                                        Email <span class="tw-text-red-500 tw-text-sm">*</span>
                                    </label>
                                    <input type="email"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('email') tw-border-red-500 @enderror"
                                        id="email" name="email" value="{{ old('email', $cuenta->email) }}" required>
                                    @error('email')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nick"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Nick/Usuario</label>
                                    <input type="text"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('nick') tw-border-red-500 @enderror"
                                        id="nick" name="nick" value="{{ old('nick', $cuenta->nick) }}">
                                    @error('nick')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cambio de Contraseña -->
                    <div class="gir-card tw-mb-6">
                        <div class="gir-card-header">
                            <h6 class="gir-card-section-title">
                                <i class="fas fa-lock tw-mr-2"></i>Cambio de Contraseña
                            </h6>
                        </div>
                        <div class="gir-card-body">
                            <small class="tw-text-gray-500 tw-mb-4 tw-block">Deje vacío si no desea cambiar la
                                contraseña</small>

                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                                <div>
                                    <label for="password"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Nueva
                                        Contraseña</label>
                                    <input type="password"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('password') tw-border-red-500 @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="tw-text-gray-500 tw-text-xs tw-mt-1">Mínimo 8 caracteres</small>
                                </div>

                                <div>
                                    <label for="password_confirmation"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Confirmar Nueva
                                        Contraseña</label>
                                    <input type="password"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500"
                                        id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="gir-card tw-mb-6">
                        <div class="gir-card-header">
                            <h6 class="gir-card-section-title">
                                <i class="fas fa-info-circle tw-mr-2"></i>Información Adicional
                            </h6>
                        </div>
                        <div class="gir-card-body">
                            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                                <div>
                                    <label for="genero"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Género</label>
                                    <select
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('genero') tw-border-red-500 @enderror"
                                        id="genero" name="genero">
                                        <option value="">Seleccionar...</option>
                                        <option value="masculino"
                                            {{ old('genero', $cuenta->genero) === 'masculino' ? 'selected' : '' }}>
                                            Masculino</option>
                                        <option value="femenino"
                                            {{ old('genero', $cuenta->genero) === 'femenino' ? 'selected' : '' }}>Femenino
                                        </option>
                                        <option value="otro"
                                            {{ old('genero', $cuenta->genero) === 'otro' ? 'selected' : '' }}>Otro</option>
                                        <option value="no-especifica"
                                            {{ old('genero', $cuenta->genero) === 'no-especifica' ? 'selected' : '' }}>No
                                            especifica</option>
                                    </select>
                                    @error('genero')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ocupacion"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Ocupación</label>
                                    <input type="text"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('ocupacion') tw-border-red-500 @enderror"
                                        id="ocupacion" name="ocupacion"
                                        value="{{ old('ocupacion', $cuenta->ocupacion) }}">
                                    @error('ocupacion')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="empleado_id"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">ID
                                        Empleado</label>
                                    <input type="text"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('empleado_id') tw-border-red-500 @enderror"
                                        id="empleado_id" name="empleado_id"
                                        value="{{ old('empleado_id', $cuenta->empleado_id) }}">
                                    @error('empleado_id')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="centro_key"
                                        class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Clave
                                        Centro</label>
                                    <input type="text"
                                        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('centro_key') tw-border-red-500 @enderror"
                                        id="centro_key" name="centro_key"
                                        value="{{ old('centro_key', $cuenta->centro_key) }}">
                                    @error('centro_key')
                                        <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel Lateral -->
                <div class="tw-space-y-6">
                    <!-- Estado Actual -->
                    <div class="gir-card">
                        <div class="gir-card-header">
                            <h6 class="gir-card-section-title">
                                <i class="fas fa-info-circle tw-mr-2"></i>Estado Actual
                            </h6>
                        </div>
                        <div class="gir-card-body tw-space-y-4">
                            <div>
                                <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Estado
                                    Actual</label>
                                <div>
                                    <span class="gir-badge gir-badge-{{ strtolower($cuenta->estado ?? 'inactive') }}">
                                        <i class="fas fa-circle tw-mr-1"></i>
                                        {{ ucfirst($cuenta->estado ?? 'Inactiva') }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Creada</label>
                                <div class="tw-text-gray-600 tw-text-sm">
                                    {{ $cuenta->created_at ? $cuenta->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Última
                                    actualización</label>
                                <div class="tw-text-gray-600 tw-text-sm">
                                    {{ $cuenta->updated_at ? $cuenta->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración -->
                    <div class="gir-card">
                        <div class="gir-card-header">
                            <h6 class="gir-card-section-title">
                                <i class="fas fa-cogs tw-mr-2"></i>Configuración
                            </h6>
                        </div>
                        <div class="gir-card-body tw-space-y-4">
                            <div>
                                <label for="rol" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">
                                    Rol <span class="tw-text-red-500 tw-text-sm">*</span>
                                </label>
                                <select
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('rol') tw-border-red-500 @enderror"
                                    id="rol" name="rol" required>
                                    <option value="">Seleccionar rol...</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol }}"
                                            {{ old('rol', $cuenta->rol) === $rol ? 'selected' : '' }}>
                                            {{ $rol }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rol')
                                    <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="tipo" class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">
                                    Tipo de Cuenta <span class="tw-text-red-500 tw-text-sm">*</span>
                                </label>
                                <select
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('tipo') tw-border-red-500 @enderror"
                                    id="tipo" name="tipo" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="interna"
                                        {{ old('tipo', $cuenta->tipo) === 'interna' ? 'selected' : '' }}>Interna</option>
                                    <option value="cliente"
                                        {{ old('tipo', $cuenta->tipo) === 'cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="profesional"
                                        {{ old('tipo', $cuenta->tipo) === 'profesional' ? 'selected' : '' }}>Profesional
                                    </option>
                                    <option value="crm-cliente"
                                        {{ old('tipo', $cuenta->tipo) === 'crm-cliente' ? 'selected' : '' }}>CRM Cliente
                                    </option>
                                    <option value="usuario"
                                        {{ old('tipo', $cuenta->tipo) === 'usuario' ? 'selected' : '' }}>Usuario</option>
                                </select>
                                @error('tipo')
                                    <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="estado"
                                    class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-1">Nuevo
                                    Estado</label>
                                <select
                                    class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 tw-rounded-md tw-text-sm focus:tw-ring-2 focus:tw-ring-primary-500 focus:tw-border-primary-500 @error('estado') tw-border-red-500 @enderror"
                                    id="estado" name="estado">
                                    <option value="activa"
                                        {{ old('estado', $cuenta->estado) === 'activa' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactiva"
                                        {{ old('estado', $cuenta->estado) === 'inactiva' ? 'selected' : '' }}>Inactiva
                                    </option>
                                    <option value="suspendida"
                                        {{ old('estado', $cuenta->estado) === 'suspendida' ? 'selected' : '' }}>Suspendida
                                    </option>
                                </select>
                                @error('estado')
                                    <div class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="gir-card">
                        <div class="gir-card-header">
                            <h6 class="gir-card-section-title">
                                <i class="fas fa-tools tw-mr-2"></i>Acciones
                            </h6>
                        </div>
                        <div class="gir-card-body">
                            <div class="tw-space-y-3">
                                <button type="submit" class="gir-btn gir-btn-primary tw-w-full">
                                    <i class="fas fa-save tw-mr-2"></i>Actualizar Cuenta
                                </button>

                                <a href="{{ route('usuarios.cuentas.show', $cuenta->id) }}"
                                    class="gir-btn gir-btn-info tw-w-full tw-text-center tw-no-underline">
                                    <i class="fas fa-eye tw-mr-2"></i>Ver Detalles
                                </a>

                                <a href="{{ route('usuarios.cuentas.index') }}"
                                    class="gir-btn gir-btn-secondary tw-w-full tw-text-center tw-no-underline">
                                    <i class="fas fa-times tw-mr-2"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#editAccountForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = form.find('button[type="submit"]');
                let originalText = submitBtn.html();

                submitBtn.html('<i class="fas fa-spinner fa-spin tw-mr-2"></i>Actualizando...').prop(
                    'disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Cuenta actualizada correctamente',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href =
                                '{{ route('usuarios.cuentas.show', $cuenta->id) }}';
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors || {};
                        let errorMessage = 'Error al actualizar la cuenta';

                        if (Object.keys(errors).length > 0) {
                            // Mostrar errores de validación
                            Object.keys(errors).forEach(function(field) {
                                let input = form.find(`[name="${field}"]`);
                                input.addClass('tw-border-red-500');
                                input.siblings('.tw-text-red-500').remove();
                                input.after(
                                    `<div class="tw-text-red-500 tw-text-xs tw-mt-1">${errors[field][0]}</div>`
                                );
                            });
                            errorMessage = 'Por favor, corrija los errores en el formulario';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Limpiar errores al escribir
            $('input, select').on('input change', function() {
                $(this).removeClass('tw-border-red-500');
                $(this).siblings('.tw-text-red-500').remove();
            });

            // Validar coincidencia de contraseñas
            $('#password_confirmation').on('input', function() {
                let password = $('#password').val();
                let confirmation = $(this).val();

                if (confirmation && password !== confirmation) {
                    $(this).addClass('tw-border-red-500');
                    $(this).siblings('.tw-text-red-500').remove();
                    $(this).after(
                        '<div class="tw-text-red-500 tw-text-xs tw-mt-1">Las contraseñas no coinciden</div>'
                    );
                } else {
                    $(this).removeClass('tw-border-red-500');
                    $(this).siblings('.tw-text-red-500').remove();
                }
            });
        });
    </script>
@endpush
