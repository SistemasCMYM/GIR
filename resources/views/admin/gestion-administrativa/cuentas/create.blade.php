@extends('layouts.dashboard')
@section('title', 'Crear Cuentas de Usuarios')

@section('content')
    <div class="container-fluid py-4 gir-override">
        <!-- Breadcrumb Corporativo -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent mb-0 p-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: var(--gir-primary);">
                        <i class="fas fa-home me-1"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('usuarios.index') }}" class="text-decoration-none" style="color: var(--gir-primary);">
                        <i class="fas fa-users-cog me-1"></i> Gestión Administrativa
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('usuarios.cuentas.index') }}" class="text-decoration-none"
                        style="color: var(--gir-primary);">
                        <i class="fas fa-users me-1"></i> Cuentas
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-user-plus me-1"></i> Crear Nueva Cuenta
                </li>
            </ol>
        </nav>
        {{-- Formulario de Creación de Cuentas con tres secciones --}}
        <!-- Header Corporativo -->
        <div class="gir-modern-card mb-4 gir-fade-in">
            <div class="gir-header-gradient">
                <h1 class="mb-2" style="font-size: 28px; font-weight: 800;">
                    <i class="fas fa-user-plus me-3"></i>Crear Nueva Cuenta
                </h1>
                <p class="mb-0 opacity-90">Complete el formulario con la información de las tres secciones para crear
                    una
                    nueva cuenta</p>
            </div>
        </div>

        <!-- Formulario Principal -->
        <form action="{{ route('usuarios.cuentas.store') }}" method="POST" id="createAccountForm" class="needs-validation"
            novalidate>
            @csrf

            <div class="row g-4">
                <!-- Columna Principal - Formulario -->
                <div class="col-lg-8">

                    {{-- SECCIÓN 1: DATOS DE LA CUENTA --}}
                    <div class="gir-modern-card mb-4 gir-fade-in">
                        <div class="gir-table-header">
                            <h5 class="mb-0" style="font-size: 20px; font-weight: 700; color: #374151;">
                                <i class="fas fa-user-cog me-2" style="color: var(--gir-primary);"></i>
                                Sección 1: Datos de la Cuenta
                            </h5>
                            <small class="text-muted">Información que se almacenará en la colección
                                <strong>cuentas</strong></small>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <!-- Nick/Nombre -->
                                <div class="col-md-6">
                                    <label for="nick" class="form-label fw-semibold" style="color: #374151;">
                                        Nick/Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control gir-form-control @error('nick') is-invalid @enderror"
                                        id="nick" name="nick" value="{{ old('nick') }}"
                                        placeholder="Nombre de usuario único" required>
                                    @error('nick')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- DNI -->
                                <div class="col-md-6">
                                    <label for="dni" class="form-label fw-semibold" style="color: #374151;">
                                        DNI <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control gir-form-control @error('dni') is-invalid @enderror"
                                        id="dni" name="dni" value="{{ old('dni') }}"
                                        placeholder="Documento de identidad" required>
                                    @error('dni')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- E-Mail -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold" style="color: #374151;">
                                        E-Mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        class="form-control gir-form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="correo@ejemplo.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contraseña -->
                                <div class="col-md-6">
                                    <label for="contrasena" class="form-label fw-semibold" style="color: #374151;">
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control gir-form-control @error('contrasena') is-invalid @enderror"
                                            id="contrasena" name="contrasena" placeholder="Mínimo 8 caracteres" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('contrasena')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div class="col-md-6">
                                    <label for="contrasena_confirmation" class="form-label fw-semibold"
                                        style="color: #374151;">
                                        Confirmar Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control gir-form-control"
                                        id="contrasena_confirmation" name="contrasena_confirmation"
                                        placeholder="Repita la contraseña" required>
                                </div>

                                <!-- Tipo de Cuenta -->
                                <div class="col-md-6">
                                    <label for="tipo" class="form-label fw-semibold" style="color: #374151;">
                                        Tipo de Cuenta <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select gir-form-control @error('tipo') is-invalid @enderror"
                                        id="tipo" name="tipo" required>
                                        <option value="">Seleccionar tipo...</option>
                                        @if (isset($tiposCuenta) && count($tiposCuenta) > 0)
                                            @foreach ($tiposCuenta as $tipo)
                                                <option value="{{ $tipo }}"
                                                    {{ old('tipo') === $tipo ? 'selected' : '' }}>
                                                    {{ ucfirst($tipo) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option disabled>No hay tipos de cuenta disponibles</option>
                                        @endif
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Asignación del Rol -->
                                <div class="col-md-6">
                                    <label for="rol_id" class="form-label fw-semibold" style="color: #374151;">
                                        Asignación del Rol <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select gir-form-control @error('rol_id') is-invalid @enderror"
                                        id="rol_id" name="rol_id" required>
                                        <option value="">Seleccionar rol...</option>
                                        @if (isset($roles) && count($roles) > 0)
                                            @foreach ($roles as $rol)
                                                <option value="{{ $rol->id }}" data-tipo="{{ $rol->tipo ?? '' }}"
                                                    data-modulos="{{ json_encode($rol->modulos ?? []) }}"
                                                    data-permisos="{{ json_encode($rol->permisos ?? []) }}"
                                                    {{ old('rol_id') === $rol->id ? 'selected' : '' }}>
                                                    {{ $rol->nombre ?? 'Rol sin nombre' }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option disabled>No hay roles disponibles</option>
                                        @endif
                                    </select>
                                    @error('rol_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado de la Cuenta -->
                                <div class="col-md-6">
                                    <label for="estado" class="form-label fw-semibold" style="color: #374151;">
                                        Estado de la Cuenta <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select gir-form-control @error('estado') is-invalid @enderror"
                                        id="estado" name="estado" required>
                                        <option value="activa"
                                            {{ old('estado', 'activa') === 'activa' ? 'selected' : '' }}>Activa
                                        </option>
                                        <option value="inactiva" {{ old('estado') === 'inactiva' ? 'selected' : '' }}>
                                            Inactiva</option>
                                        <option value="suspendida" {{ old('estado') === 'suspendida' ? 'selected' : '' }}>
                                            Suspendida</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Empresas asignadas -->
                                <div class="col-12">
                                    <label for="empresas" class="form-label fw-semibold" style="color: #374151;">
                                        Empresas Asignadas <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select gir-form-control @error('empresas') is-invalid @enderror"
                                        id="empresas" name="empresas[]" multiple required>
                                        @if (isset($empresas) && count($empresas) > 0)
                                            @foreach ($empresas as $empresa)
                                                @php
                                                    $empresaId = isset($empresa->_id)
                                                        ? $empresa->_id
                                                        : (isset($empresa['_id'])
                                                            ? $empresa['_id']
                                                            : $empresa->id ?? '');
                                                    $empresaNombre = isset($empresa->nombre_comercial)
                                                        ? $empresa->nombre_comercial
                                                        : (isset($empresa['nombre_comercial'])
                                                            ? $empresa['nombre_comercial']
                                                            : $empresa->razon_social ?? 'Empresa sin nombre');
                                                    $empresaNit = isset($empresa->nit)
                                                        ? $empresa->nit
                                                        : (isset($empresa['nit'])
                                                            ? $empresa['nit']
                                                            : '');
                                                @endphp
                                                <option value="{{ $empresaId }}"
                                                    {{ is_array(old('empresas')) && in_array($empresaId, old('empresas')) ? 'selected' : '' }}>
                                                    {{ $empresaNombre }}{{ $empresaNit ? ' - ' . $empresaNit : '' }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option disabled>No hay empresas disponibles - Contacte al administrador
                                            </option>
                                        @endif
                                    </select>
                                    <small class="text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar
                                        múltiples empresas</small>
                                    @error('empresas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: DATOS DEL PERFIL --}}
                    <div class="gir-modern-card mb-4 gir-fade-in" style="animation-delay: 0.1s;">
                        <div class="gir-table-header">
                            <h5 class="mb-0" style="font-size: 20px; font-weight: 700; color: #374151;">
                                <i class="fas fa-id-card me-2" style="color: var(--gir-primary);"></i>
                                Sección 2: Datos del Perfil
                            </h5>
                            <small class="text-muted">Información que se almacenará en la colección
                                <strong>perfiles</strong></small>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <!-- Nombres -->
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label fw-semibold" style="color: #374151;">
                                        Nombres <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control gir-form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre') }}"
                                        placeholder="Nombres completos" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Apellidos -->
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label fw-semibold" style="color: #374151;">
                                        Apellidos <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control gir-form-control @error('apellido') is-invalid @enderror"
                                        id="apellido" name="apellido" value="{{ old('apellido') }}"
                                        placeholder="Apellidos completos" required>
                                    @error('apellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Género -->
                                <div class="col-md-6">
                                    <label for="genero" class="form-label fw-semibold" style="color: #374151;">
                                        Género <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select gir-form-control @error('genero') is-invalid @enderror"
                                        id="genero" name="genero" required>
                                        <option value="">Seleccionar género...</option>
                                        @if (isset($generos) && count($generos) > 0)
                                            @foreach ($generos as $genero)
                                                <option value="{{ $genero }}"
                                                    {{ old('genero') === $genero ? 'selected' : '' }}>
                                                    {{ ucfirst($genero) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="masculino"
                                                {{ old('genero') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="femenino" {{ old('genero') === 'femenino' ? 'selected' : '' }}>
                                                Femenino</option>
                                            <option value="otro" {{ old('genero') === 'otro' ? 'selected' : '' }}>Otro
                                            </option>
                                        @endif
                                    </select>
                                    @error('genero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ocupación -->
                                <div class="col-md-6">
                                    <label for="ocupacion" class="form-label fw-semibold" style="color: #374151;">
                                        Ocupación
                                    </label>
                                    <input type="text"
                                        class="form-control gir-form-control @error('ocupacion') is-invalid @enderror"
                                        id="ocupacion" name="ocupacion" value="{{ old('ocupacion') }}"
                                        placeholder="Cargo o profesión">
                                    @error('ocupacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Firma -->
                                <div class="col-md-6">
                                    <label for="firma" class="form-label fw-semibold" style="color: #374151;">
                                        Firma
                                    </label>
                                    <textarea class="form-control gir-form-control @error('firma') is-invalid @enderror" id="firma" name="firma"
                                        rows="3" placeholder="Firma digital o texto de firma">{{ old('firma') }}</textarea>
                                    @error('firma')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Pie de la Firma -->
                                <div class="col-md-6">
                                    <label for="piefirma" class="form-label fw-semibold" style="color: #374151;">
                                        Pie de la Firma
                                    </label>
                                    <textarea class="form-control gir-form-control @error('piefirma') is-invalid @enderror" id="piefirma"
                                        name="piefirma" rows="3" placeholder="Información adicional para la firma">{{ old('piefirma') }}</textarea>
                                    @error('piefirma')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Licencia Profesional -->
                                <div class="col-12">
                                    <label for="licencia" class="form-label fw-semibold" style="color: #374151;">
                                        Licencia Profesional
                                    </label>
                                    <input type="text"
                                        class="form-control gir-form-control @error('licencia') is-invalid @enderror"
                                        id="licencia" name="licencia" value="{{ old('licencia') }}"
                                        placeholder="Número de licencia profesional o certificación">
                                    @error('licencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 3: VALIDACIÓN DE PERMISOS Y ACCESOS --}}
                    <div class="gir-modern-card mb-4 gir-fade-in" style="animation-delay: 0.2s;">
                        <div class="gir-table-header">
                            <h5 class="mb-0" style="font-size: 20px; font-weight: 700; color: #374151;">
                                <i class="fas fa-shield-alt me-2" style="color: var(--gir-primary);"></i>
                                Sección 3: Validación de Permisos y Accesos
                            </h5>
                            <small class="text-muted">Información que se almacenará en la colección
                                <strong>permisos</strong> (se genera automáticamente según el rol)</small>
                        </div>
                        <div class="p-4">
                            <div class="alert alert-info"
                                style="border-radius: 12px; border: none; background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Información Automática:</strong> Los permisos y accesos se configurarán
                                automáticamente según el rol seleccionado.
                            </div>

                            <div class="row g-3">
                                <!-- Vista previa del tipo de cuenta (del rol) -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Tipo de
                                        Cuenta</label>
                                    <div class="p-3 bg-light rounded" style="border-radius: 8px;">
                                        <span id="preview-tipo" class="text-muted">Se determinará según el rol</span>
                                    </div>
                                    <small class="text-muted">Proveniente del rol seleccionado</small>
                                </div>

                                <!-- Vista previa de módulos asignados -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Módulos
                                        Asignados</label>
                                    <div class="p-3 bg-light rounded" style="border-radius: 8px;">
                                        <span id="preview-modulos" class="text-muted">Se asignarán según el rol</span>
                                    </div>
                                    <small class="text-muted">Proveniente del rol seleccionado</small>
                                </div>

                                <!-- Vista previa de acciones de la cuenta -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Acciones de la
                                        Cuenta</label>
                                    <div class="p-3 bg-light rounded" style="border-radius: 8px;">
                                        <span id="preview-acciones" class="text-muted">Se asignarán según el
                                            rol</span>
                                    </div>
                                    <small class="text-muted">Proveniente del rol seleccionado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Lateral - Resumen y Acciones -->
                <div class="col-lg-4">
                    <div class="row g-4">
                        <!-- Resumen -->
                        <div class="col-12">
                            <div class="gir-modern-card gir-fade-in" style="animation-delay: 0.3s;">
                                <div class="gir-table-header">
                                    <h6 class="mb-0" style="font-size: 18px; font-weight: 700; color: #374151;">
                                        <i class="fas fa-clipboard-list me-2"
                                            style="color: var(--gir-primary);"></i>Resumen
                                    </h6>
                                </div>
                                <div class="p-4">
                                    <div class="mb-3">
                                        <small class="text-muted">Progreso del formulario</small>
                                        <div class="progress mt-1" style="height: 8px; border-radius: 4px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;"
                                                id="progress-bar"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Secciones completadas:</small>
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark me-1" id="section1-status">
                                                <i class="fas fa-circle text-secondary me-1"></i>Datos de Cuenta
                                            </span>
                                            <span class="badge bg-light text-dark me-1" id="section2-status">
                                                <i class="fas fa-circle text-secondary me-1"></i>Perfil
                                            </span>
                                            <span class="badge bg-light text-dark" id="section3-status">
                                                <i class="fas fa-circle text-secondary me-1"></i>Permisos
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="col-12">
                            <div class="gir-modern-card gir-fade-in" style="animation-delay: 0.4s;">
                                <div class="gir-table-header">
                                    <h6 class="mb-0" style="font-size: 18px; font-weight: 700; color: #374151;">
                                        <i class="fas fa-tools me-2" style="color: var(--gir-primary);"></i>Acciones
                                    </h6>
                                </div>
                                <div class="p-4">
                                    <div class="d-grid gap-3">
                                        <button type="submit" class="gir-btn-modern" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Crear Cuenta
                                        </button>

                                        <button type="button" class="btn btn-outline-secondary"
                                            style="border-radius: 12px; padding: 12px 24px; font-weight: 600;"
                                            onclick="history.back()">
                                            <i class="fas fa-arrow-left me-2"></i>Volver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Verificar que jQuery y Select2 estén disponibles
                console.log('Inicializando formulario de creación de cuentas...');

                // Inicializar select múltiple para empresas
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#empresas').select2({
                        placeholder: 'Seleccionar empresas...',
                        allowClear: true,
                        width: '100%'
                    });
                    console.log('Select2 inicializado para empresas');
                } else {
                    console.warn('Select2 no está disponible');
                }

                // Mostrar/ocultar contraseña
                $('#togglePassword').on('click', function() {
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

                // Actualizar vista previa de permisos cuando se selecciona un rol
                $('#rol_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const tipo = selectedOption.data('tipo') || 'No definido';
                    const modulos = selectedOption.data('modulos');
                    const permisos = selectedOption.data('permisos');

                    $('#preview-tipo').text(tipo);

                    let modulosText = 'No definido';
                    if (Array.isArray(modulos) && modulos.length > 0) {
                        modulosText = modulos.join(', ');
                    } else if (modulos) {
                        modulosText = modulos;
                    }
                    $('#preview-modulos').text(modulosText);

                    let permisosText = 'No definido';
                    if (Array.isArray(permisos) && permisos.length > 0) {
                        permisosText = permisos.join(', ');
                    } else if (permisos) {
                        permisosText = permisos;
                    }
                    $('#preview-acciones').text(permisosText);

                    updateProgress();
                });

                // Validación en tiempo real y actualización de progreso
                function updateProgress() {
                    let section1 = 0,
                        section2 = 0,
                        section3 = 0;

                    // Sección 1: Datos de la cuenta
                    const section1Fields = ['nick', 'dni', 'email', 'contrasena', 'tipo', 'rol_id', 'estado'];
                    let section1Completed = 0;
                    section1Fields.forEach(function(field) {
                        const fieldElement = $(`#${field}`);
                        if (fieldElement.length > 0 && fieldElement.val() && fieldElement.val().trim() !== '') {
                            section1Completed++;
                        }
                    });

                    // Verificar empresas seleccionadas
                    const empresasSelected = $('#empresas').val();
                    if (empresasSelected && empresasSelected.length > 0) {
                        section1Completed++;
                        section1Fields.push('empresas'); // Agregar empresas a la cuenta
                    }

                    section1 = (section1Completed / (section1Fields.length + 1)) * 100; // +1 por empresas

                    // Sección 2: Datos del perfil  
                    const section2Fields = ['nombre', 'apellido', 'genero', 'ocupacion'];
                    let section2Completed = 0;
                    section2Fields.forEach(function(field) {
                        const fieldElement = $(`#${field}`);
                        if (fieldElement.length > 0 && fieldElement.val() && fieldElement.val().trim() !== '') {
                            section2Completed++;
                        }
                    });
                    section2 = (section2Completed / section2Fields.length) * 100;

                    // Sección 3: Se completa automáticamente con el rol
                    section3 = $('#rol_id').val() ? 100 : 0;

                    const totalProgress = (section1 + section2 + section3) / 3;
                    $('#progress-bar').css('width', totalProgress + '%');

                    // Actualizar badges de estado
                    updateSectionBadge('section1-status', section1);
                    updateSectionBadge('section2-status', section2);
                    updateSectionBadge('section3-status', section3);
                }

                function updateSectionBadge(badgeId, progress) {
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

                // Actualizar progreso cuando cambien los campos
                $('input, select, textarea').on('input change keyup', function() {
                    updateProgress();
                });

                // Específicamente para select múltiple de empresas
                $('#empresas').on('change', function() {
                    updateProgress();
                });

                // Validación específica para campos requeridos
                $('input[required], select[required], textarea[required]').on('blur', function() {
                    const field = $(this);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('is-invalid');
                        if (!field.siblings('.invalid-feedback').length) {
                            field.after('<div class="invalid-feedback">Este campo es requerido</div>');
                        }
                    } else {
                        field.removeClass('is-invalid');
                        field.siblings('.invalid-feedback').remove();
                    }
                });

                // Envío del formulario con validaciones
                $('#createAccountForm').on('submit', function(e) {
                    // Validar confirmación de contraseña
                    if ($('#contrasena').val() !== $('#contrasena_confirmation').val()) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden');
                        return false;
                    }

                    const submitBtn = $('#submitBtn');
                    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Creando cuenta...').prop(
                        'disabled', true);

                    // Permitir envío normal del formulario
                    return true;
                });

                // Limpiar errores al escribir
                $('input, select, textarea').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                });

                // Inicializar progreso
                updateProgress();
            });
        </script>
    @endpush
@endsection
