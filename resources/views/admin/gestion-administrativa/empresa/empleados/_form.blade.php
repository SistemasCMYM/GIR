@php
    $prefix = $fieldIdPrefix ?? '';
    $empleadoData = $empleado ?? [];
    $primerNombre = old('primer_nombre', $empleadoData['primer_nombre'] ?? ($empleadoData['primerNombre'] ?? ''));
    $segundoNombre = old('segundo_nombre', $empleadoData['segundo_nombre'] ?? ($empleadoData['segundoNombre'] ?? ''));
    $primerApellido = old(
        'primer_apellido',
        $empleadoData['primer_apellido'] ?? ($empleadoData['primerApellido'] ?? ''),
    );
    $segundoApellido = old(
        'segundo_apellido',
        $empleadoData['segundo_apellido'] ?? ($empleadoData['segundoApellido'] ?? ''),
    );
    $numeroDocumento = old('numero_documento', $empleadoData['numero_documento'] ?? ($empleadoData['dni'] ?? ''));
    $tipoDocumento = old('tipo_documento', $empleadoData['tipo_documento'] ?? 'CC');
    $genero = old('genero', $empleadoData['genero'] ?? '');
    $email = old('email', $empleadoData['email'] ?? '');
    $telefono = old('telefono', $empleadoData['telefono'] ?? '');
    $cargo = old('cargo', $empleadoData['cargo'] ?? '');
    $tipoCargo = old('tipo_cargo', $empleadoData['tipo_cargo'] ?? '');

    // Extraer los _key de las estructuras anidadas si existen
    $areaId = old('area_id', $empleadoData['area_id'] ?? ($empleadoData['area_key'] ?? ''));
    $procesoId = old('proceso_id', $empleadoData['proceso_id'] ?? ($empleadoData['proceso_key'] ?? ''));
    $centroId = old('centro_id', $empleadoData['centro_id'] ?? ($empleadoData['centro_key'] ?? ''));

    $ciudad = old('ciudad', $empleadoData['ciudad'] ?? '');
    $psicosocialTipo = old('psicosocial_tipo', $empleadoData['psicosocial_tipo'] ?? '');
    $direccion = old('direccion', $empleadoData['direccion'] ?? '');
@endphp

<div class="row g-2">
    {{-- Nombres --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user text-primary me-1" style="font-size: 11px;"></i>
            Primer Nombre <span class="text-danger">*</span>
        </label>
        <input type="text" id="{{ $prefix }}primer_nombre" name="primer_nombre"
            class="form-control form-control-sm shadow-sm @error('primer_nombre') is-invalid @enderror"
            value="{{ $primerNombre }}" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        @error('primer_nombre')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user text-secondary me-1" style="font-size: 11px;"></i>
            Segundo Nombre
        </label>
        <input type="text" id="{{ $prefix }}segundo_nombre" name="segundo_nombre"
            class="form-control form-control-sm shadow-sm" value="{{ $segundoNombre }}"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>

    {{-- Apellidos --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user-tag text-primary me-1" style="font-size: 11px;"></i>
            Primer Apellido <span class="text-danger">*</span>
        </label>
        <input type="text" id="{{ $prefix }}primer_apellido" name="primer_apellido"
            class="form-control form-control-sm shadow-sm @error('primer_apellido') is-invalid @enderror"
            value="{{ $primerApellido }}" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        @error('primer_apellido')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user-tag text-secondary me-1" style="font-size: 11px;"></i>
            Segundo Apellido
        </label>
        <input type="text" id="{{ $prefix }}segundo_apellido" name="segundo_apellido"
            class="form-control form-control-sm shadow-sm" value="{{ $segundoApellido }}"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>

    {{-- Documento --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-id-card text-info me-1" style="font-size: 11px;"></i>
            DNI <span class="text-danger">*</span>
        </label>
        <input type="text" id="{{ $prefix }}numero_documento" name="numero_documento"
            class="form-control form-control-sm shadow-sm @error('numero_documento') is-invalid @enderror"
            value="{{ $numeroDocumento }}" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        @error('numero_documento')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-address-card text-secondary me-1" style="font-size: 11px;"></i>
            Tipo Documento
        </label>
        <select id="{{ $prefix }}tipo_documento" name="tipo_documento"
            class="form-select form-select-sm shadow-sm"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="CC" @selected($tipoDocumento === 'CC')>CC</option>
            <option value="CE" @selected($tipoDocumento === 'CE')>CE</option>
            <option value="TI" @selected($tipoDocumento === 'TI')>TI</option>
            <option value="PA" @selected($tipoDocumento === 'PA')>Pasaporte</option>
        </select>
    </div>

    {{-- Género y Email --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-venus-mars text-warning me-1" style="font-size: 11px;"></i>
            Género <span class="text-danger">*</span>
        </label>
        <select id="{{ $prefix }}genero" name="genero"
            class="form-select form-select-sm shadow-sm @error('genero') is-invalid @enderror" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <option value="masculino" @selected($genero === 'masculino')>Masculino</option>
            <option value="femenino" @selected($genero === 'femenino')>Femenino</option>
            <option value="otro" @selected($genero === 'otro')>Otro</option>
        </select>
        @error('genero')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-envelope text-danger me-1" style="font-size: 11px;"></i>
            Email <span class="text-danger">*</span>
        </label>
        <input type="email" id="{{ $prefix }}email" name="email"
            class="form-control form-control-sm shadow-sm @error('email') is-invalid @enderror"
            value="{{ $email }}" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        @error('email')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Cargo y Tipo --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-briefcase text-success me-1" style="font-size: 11px;"></i>
            Cargo <span class="text-danger">*</span>
        </label>
        <input type="text" id="{{ $prefix }}cargo" name="cargo"
            class="form-control form-control-sm shadow-sm @error('cargo') is-invalid @enderror"
            value="{{ $cargo }}" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        @error('cargo')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user-tie text-primary me-1" style="font-size: 11px;"></i>
            Tipo Cargo <span class="text-danger">*</span>
        </label>
        <select id="{{ $prefix }}tipo_cargo" name="tipo_cargo"
            class="form-select form-select-sm shadow-sm @error('tipo_cargo') is-invalid @enderror" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <option value="gerencial" @selected($tipoCargo === 'gerencial')>Gerencial</option>
            <option value="profesional" @selected($tipoCargo === 'profesional')>Profesional</option>
            <option value="tecnico" @selected($tipoCargo === 'tecnico')>Técnico</option>
            <option value="auxiliar" @selected($tipoCargo === 'auxiliar')>Auxiliar</option>
            <option value="operativo" @selected($tipoCargo === 'operativo')>Operativo</option>
        </select>
        @error('tipo_cargo')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Área y Proceso --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-sitemap text-info me-1" style="font-size: 11px;"></i>
            Área <span class="text-danger">*</span>
        </label>
        <select id="{{ $prefix }}area_id" name="area_id"
            class="form-select form-select-sm shadow-sm @error('area_id') is-invalid @enderror" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            @foreach ($areas as $area)
                @php
                    if (is_array($area)) {
                        $areaIdValue = isset($area['_id']) ? (string) $area['_id'] : '';
                        $areaNombre = $area['nombre'] ?? '';
                    } else {
                        $areaIdValue = isset($area->_id) ? (string) $area->_id : '';
                        $areaNombre = $area->nombre ?? '';
                    }
                @endphp
                <option value="{{ $areaIdValue }}" @selected($areaId === $areaIdValue)>{{ $areaNombre }}</option>
            @endforeach
        </select>
        @error('area_id')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-project-diagram text-secondary me-1" style="font-size: 11px;"></i>
            Proceso
        </label>
        <select id="{{ $prefix }}proceso_id" name="proceso_id" class="form-select form-select-sm shadow-sm"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            @foreach ($procesos as $proceso)
                @php
                    if (is_array($proceso)) {
                        $procesoIdValue = isset($proceso['_id']) ? (string) $proceso['_id'] : '';
                        $procesoNombre = $proceso['nombre'] ?? '';
                    } else {
                        $procesoIdValue = isset($proceso->_id) ? (string) $proceso->_id : '';
                        $procesoNombre = $proceso->nombre ?? '';
                    }
                @endphp
                <option value="{{ $procesoIdValue }}" @selected($procesoId === $procesoIdValue)>{{ $procesoNombre }}</option>
            @endforeach
        </select>
    </div>

    {{-- Sede y Ciudad --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-building text-warning me-1" style="font-size: 11px;"></i>
            Sede <span class="text-danger">*</span>
        </label>
        <select id="{{ $prefix }}centro_id" name="centro_id"
            class="form-select form-select-sm shadow-sm @error('centro_id') is-invalid @enderror" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            @foreach ($centros as $centro)
                @php
                    if (is_array($centro)) {
                        $centroIdValue = isset($centro['_id']) ? (string) $centro['_id'] : '';
                        $centroNombre = $centro['nombre'] ?? '';
                    } else {
                        $centroIdValue = isset($centro->_id) ? (string) $centro->_id : '';
                        $centroNombre = $centro->nombre ?? '';
                    }
                @endphp
                <option value="{{ $centroIdValue }}" @selected($centroId === $centroIdValue)>{{ $centroNombre }}</option>
            @endforeach
        </select>
        @error('centro_id')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-city text-success me-1" style="font-size: 11px;"></i>
            Ciudad <span class="text-danger">*</span>
        </label>
        <input type="text" id="{{ $prefix }}ciudad" name="ciudad"
            class="form-control form-control-sm shadow-sm @error('ciudad') is-invalid @enderror"
            value="{{ $ciudad }}" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        @error('ciudad')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Tipo Prueba y Teléfono --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-clipboard-list text-primary me-1" style="font-size: 11px;"></i>
            Tipo Prueba <span class="text-danger">*</span>
        </label>
        <select id="{{ $prefix }}psicosocial_tipo" name="psicosocial_tipo"
            class="form-select form-select-sm shadow-sm @error('psicosocial_tipo') is-invalid @enderror" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <option value="A" @selected($psicosocialTipo === 'A')>Tipo A</option>
            <option value="B" @selected($psicosocialTipo === 'B')>Tipo B</option>
        </select>
        @error('psicosocial_tipo')
            <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-phone text-info me-1" style="font-size: 11px;"></i>
            Teléfono
        </label>
        <input type="text" id="{{ $prefix }}telefono" name="telefono"
            class="form-control form-control-sm shadow-sm" value="{{ $telefono }}"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>

    {{-- Dirección --}}
    <div class="col-12">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 11px;"></i>
            Dirección
        </label>
        <input type="text" id="{{ $prefix }}direccion" name="direccion"
            class="form-control form-control-sm shadow-sm" value="{{ $direccion }}"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>
</div>
