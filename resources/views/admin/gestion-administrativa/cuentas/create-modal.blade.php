{{-- Formulario Modal para Creación de Cuentas con tres secciones --}}
<form action="{{ route('usuarios.cuentas.store') }}" method="POST" id="createAccountModalForm" class="needs-validation" novalidate>
    @csrf
    
    <!-- Contenedor principal del modal -->
    <div class="row g-4">
        <!-- Formulario completo -->
        <div class="col-12">
            
            {{-- SECCIÓN 1: DATOS DE LA CUENTA --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, var(--gir-primary) 0%, var(--gir-primary-light) 100%); color: white;">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user-cog me-2"></i>
                        Sección 1: Datos de la Cuenta
                    </h6>
                    <small class="opacity-90">Información que se almacenará en la colección <strong>cuentas</strong></small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nick/Nombre -->
                        <div class="col-md-6">
                            <label for="nick" class="form-label fw-semibold">
                                Nick/Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nick') is-invalid @enderror"
                                   id="nick" 
                                   name="nick" 
                                   value="{{ old('nick') }}" 
                                   placeholder="Nombre de usuario único"
                                   required>
                            @error('nick')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- DNI -->
                        <div class="col-md-6">
                            <label for="dni" class="form-label fw-semibold">
                                DNI <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('dni') is-invalid @enderror"
                                   id="dni" 
                                   name="dni" 
                                   value="{{ old('dni') }}" 
                                   placeholder="Documento de identidad"
                                   required>
                            @error('dni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- E-Mail -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                E-Mail <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="correo@ejemplo.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <div class="col-md-6">
                            <label for="contrasena" class="form-label fw-semibold">
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('contrasena') is-invalid @enderror"
                                       id="contrasena" 
                                       name="contrasena" 
                                       placeholder="Mínimo 8 caracteres"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('contrasena')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="col-md-6">
                            <label for="contrasena_confirmation" class="form-label fw-semibold">
                                Confirmar Contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control"
                                   id="contrasena_confirmation" 
                                   name="contrasena_confirmation" 
                                   placeholder="Repita la contraseña"
                                   required>
                        </div>

                        <!-- Tipo de Cuenta -->
                        <div class="col-md-6">
                            <label for="tipo" class="form-label fw-semibold">
                                Tipo de Cuenta <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('tipo') is-invalid @enderror" 
                                    id="tipo" 
                                    name="tipo" 
                                    required>
                                <option value="">Seleccionar tipo...</option>
                                @foreach($tiposCuenta as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo') === $tipo ? 'selected' : '' }}>
                                        {{ ucfirst($tipo) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Asignación del Rol -->
                        <div class="col-md-6">
                            <label for="rol_id" class="form-label fw-semibold">
                                Asignación del Rol <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('rol_id') is-invalid @enderror" 
                                    id="rol_id" 
                                    name="rol_id" 
                                    required>
                                <option value="">Seleccionar rol...</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->_id }}" 
                                            data-tipo="{{ $rol->tipo }}" 
                                            data-modulos="{{ json_encode($rol->modulos) }}" 
                                            data-permisos="{{ json_encode($rol->permisos) }}"
                                            {{ old('rol_id') === $rol->_id ? 'selected' : '' }}>
                                        {{ $rol->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rol_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estado de la Cuenta -->
                        <div class="col-md-6">
                            <label for="estado" class="form-label fw-semibold">
                                Estado de la Cuenta <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    id="estado" 
                                    name="estado" 
                                    required>
                                <option value="activa" {{ old('estado', 'activa') === 'activa' ? 'selected' : '' }}>Activa</option>
                                <option value="inactiva" {{ old('estado') === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                <option value="suspendida" {{ old('estado') === 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Empresas asignadas -->
                        <div class="col-12">
                            <label for="empresas" class="form-label fw-semibold">
                                Empresas Asignadas <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('empresas') is-invalid @enderror" 
                                    id="empresas" 
                                    name="empresas[]" 
                                    multiple 
                                    required>
                                @foreach($empresas as $empresa)
                                    <option value="{{ isset($empresa->_id) ? $empresa->_id : $empresa['_id'] }}" 
                                            {{ in_array((isset($empresa->_id) ? $empresa->_id : $empresa['_id']), old('empresas', [])) ? 'selected' : '' }}>
                                        {{ isset($empresa->nombre_comercial) ? $empresa->nombre_comercial : $empresa['nombre_comercial'] }} - {{ isset($empresa->nit) ? $empresa->nit : $empresa['nit'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples empresas</small>
                            @error('empresas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: DATOS DEL PERFIL --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-id-card me-2"></i>
                        Sección 2: Datos del Perfil
                    </h6>
                    <small class="opacity-90">Información que se almacenará en la colección <strong>perfiles</strong></small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nombres -->
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-semibold">
                                Nombres <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre') }}" 
                                   placeholder="Nombres completos"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <label for="apellido" class="form-label fw-semibold">
                                Apellidos <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('apellido') is-invalid @enderror"
                                   id="apellido" 
                                   name="apellido" 
                                   value="{{ old('apellido') }}" 
                                   placeholder="Apellidos completos"
                                   required>
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Género -->
                        <div class="col-md-6">
                            <label for="genero" class="form-label fw-semibold">
                                Género <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('genero') is-invalid @enderror" 
                                    id="genero" 
                                    name="genero" 
                                    required>
                                <option value="">Seleccionar género...</option>
                                @foreach($generos as $genero)
                                    <option value="{{ $genero }}" {{ old('genero') === $genero ? 'selected' : '' }}>
                                        {{ ucfirst($genero) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ocupación -->
                        <div class="col-md-6">
                            <label for="ocupacion" class="form-label fw-semibold">
                                Ocupación
                            </label>
                            <input type="text" 
                                   class="form-control @error('ocupacion') is-invalid @enderror"
                                   id="ocupacion" 
                                   name="ocupacion" 
                                   value="{{ old('ocupacion') }}" 
                                   placeholder="Cargo o profesión">
                            @error('ocupacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Firma -->
                        <div class="col-md-6">
                            <label for="firma" class="form-label fw-semibold">
                                Firma
                            </label>
                            <textarea class="form-control @error('firma') is-invalid @enderror"
                                      id="firma" 
                                      name="firma" 
                                      rows="3"
                                      placeholder="Firma digital o texto de firma">{{ old('firma') }}</textarea>
                            @error('firma')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pie de la Firma -->
                        <div class="col-md-6">
                            <label for="piefirma" class="form-label fw-semibold">
                                Pie de la Firma
                            </label>
                            <textarea class="form-control @error('piefirma') is-invalid @enderror"
                                      id="piefirma" 
                                      name="piefirma" 
                                      rows="3"
                                      placeholder="Información adicional para la firma">{{ old('piefirma') }}</textarea>
                            @error('piefirma')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Licencia Profesional -->
                        <div class="col-12">
                            <label for="licencia" class="form-label fw-semibold">
                                Licencia Profesional
                            </label>
                            <input type="text" 
                                   class="form-control @error('licencia') is-invalid @enderror"
                                   id="licencia" 
                                   name="licencia" 
                                   value="{{ old('licencia') }}" 
                                   placeholder="Número de licencia profesional o certificación">
                            @error('licencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: VALIDACIÓN DE PERMISOS Y ACCESOS --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-shield-alt me-2"></i>
                        Sección 3: Validación de Permisos y Accesos
                    </h6>
                    <small class="opacity-90">Información que se almacenará en la colección <strong>permisos</strong> (se genera automáticamente según el rol)</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Información Automática:</strong> Los permisos y accesos se configurarán automáticamente según el rol seleccionado.
                    </div>
                    
                    <div class="row g-3">
                        <!-- Vista previa del tipo de cuenta (del rol) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo de Cuenta</label>
                            <div class="p-3 bg-light rounded">
                                <span id="preview-tipo-modal" class="text-muted">Se determinará según el rol</span>
                            </div>
                            <small class="text-muted">Proveniente del rol seleccionado</small>
                        </div>

                        <!-- Vista previa de módulos asignados -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Módulos Asignados</label>
                            <div class="p-3 bg-light rounded">
                                <span id="preview-modulos-modal" class="text-muted">Se asignarán según el rol</span>
                            </div>
                            <small class="text-muted">Proveniente del rol seleccionado</small>
                        </div>

                        <!-- Vista previa de acciones de la cuenta -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Acciones de la Cuenta</label>
                            <div class="p-3 bg-light rounded">
                                <span id="preview-acciones-modal" class="text-muted">Se asignarán según el rol</span>
                            </div>
                            <small class="text-muted">Proveniente del rol seleccionado</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progreso del formulario -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Progreso del formulario</label>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;" id="progress-bar-modal"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Secciones completadas:</small>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark me-1" id="section1-status-modal">
                                <i class="fas fa-circle text-secondary me-1"></i>Datos de Cuenta
                            </span>
                            <span class="badge bg-light text-dark me-1" id="section2-status-modal">
                                <i class="fas fa-circle text-secondary me-1"></i>Perfil
                            </span>
                            <span class="badge bg-light text-dark" id="section3-status-modal">
                                <i class="fas fa-circle text-secondary me-1"></i>Permisos
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones del modal (se incluirán en el footer del modal) -->
    <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary" id="submitBtnModal">
            <i class="fas fa-save me-2"></i>Crear Cuenta
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Inicializar select múltiple para empresas en modal
    if ($.fn.select2) {
        $('#empresas').select2({
            placeholder: 'Seleccionar empresas...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalCrearUsuario') // Importante para modales
        });
    }

    // Mostrar/ocultar contraseña en modal
    $('#togglePasswordModal').click(function() {
        const passwordInput = $('#contrasena');
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Actualizar vista previa de permisos cuando se selecciona un rol en modal
    $('#rol_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const tipo = selectedOption.data('tipo');
        const modulos = selectedOption.data('modulos');
        const permisos = selectedOption.data('permisos');

        $('#preview-tipo-modal').text(tipo || 'No definido');
        $('#preview-modulos-modal').text(Array.isArray(modulos) ? modulos.join(', ') : (modulos || 'No definido'));
        $('#preview-acciones-modal').text(Array.isArray(permisos) ? permisos.join(', ') : (permisos || 'No definido'));

        updateProgressModal();
    });

    // Validación en tiempo real y actualización de progreso en modal
    function updateProgressModal() {
        let section1 = 0, section2 = 0, section3 = 0;
        
        // Sección 1: Datos de la cuenta
        const section1Fields = ['nick', 'dni', 'email', 'contrasena', 'tipo', 'rol_id', 'estado'];
        const section1Completed = section1Fields.filter(field => $(`#${field}`).val().trim() !== '').length;
        section1 = (section1Completed / section1Fields.length) * 100;
        
    // Sección 2: Datos del perfil  
    const section2Fields = ['nombre', 'apellido', 'genero'];
        const section2Completed = section2Fields.filter(field => $(`#${field}`).val().trim() !== '').length;
        section2 = (section2Completed / section2Fields.length) * 100;
        
        // Sección 3: Se completa automáticamente con el rol
        section3 = $('#rol_id').val() ? 100 : 0;
        
        const totalProgress = (section1 + section2 + section3) / 3;
        $('#progress-bar-modal').css('width', totalProgress + '%');
        
        // Actualizar badges de estado
        updateSectionBadgeModal('section1-status-modal', section1);
        updateSectionBadgeModal('section2-status-modal', section2);
        updateSectionBadgeModal('section3-status-modal', section3);
    }

    function updateSectionBadgeModal(badgeId, progress) {
        const badge = $(`#${badgeId}`);
        const icon = badge.find('i');
        
        if (progress >= 100) {
            badge.removeClass('bg-light text-dark').addClass('bg-success text-white');
            icon.removeClass('text-secondary').addClass('text-white');
        } else if (progress > 0) {
            badge.removeClass('bg-light text-dark bg-success text-white').addClass('bg-warning text-dark');
            icon.removeClass('text-secondary text-white').addClass('text-dark');
        } else {
            badge.removeClass('bg-warning text-dark bg-success text-white').addClass('bg-light text-dark');
            icon.removeClass('text-dark text-white').addClass('text-secondary');
        }
    }

    // Actualizar progreso cuando cambien los campos en modal
    $('#createAccountModalForm input, #createAccountModalForm select, #createAccountModalForm textarea').on('input change', function() {
        updateProgressModal();
    });

    // Envío del formulario modal con validaciones
    $('#createAccountModalForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validar confirmación de contraseña
        if ($('#contrasena').val() !== $('#contrasena_confirmation').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Las contraseñas no coinciden',
                customClass: { popup: 'rounded-4' }
            });
            return;
        }

        const submitBtn = $('#submitBtnModal');
        const originalText = submitBtn.html();
        
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Creando cuenta...').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cuenta creada exitosamente!',
                        text: 'La cuenta y sus permisos han sido configurados correctamente',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'rounded-4' }
                    }).then(() => {
                        $('#modalCrearUsuario').modal('hide');
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            location.reload();
                        }
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al crear la cuenta';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    // Limpiar errores anteriores
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                    
                    // Mostrar errores específicos
                    Object.keys(errors).forEach(function(field) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                    
                    errorMessage = 'Por favor, corrija los errores en el formulario';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    customClass: { popup: 'rounded-4' }
                });
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Limpiar errores al escribir en modal
    $('#createAccountModalForm input, #createAccountModalForm select, #createAccountModalForm textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').remove();
    });

    // Inicializar progreso en modal
    updateProgressModal();
});
</script>
