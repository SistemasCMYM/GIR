<!-- Identidad Corporativa (Branding) -->
<form action="{{ route('configuracion.empresa.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="seccion" value="branding">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="colorPrimario" class="form-label">Color Primario</label>
                <div class="color-picker-container">
                    <input type="color" class="form-control" id="colorPrimario" name="configuraciones[colorPrimario]" 
                           value="{{ $empresa->colorPrimario ?? '#007bff' }}">
                    <div class="color-preview" style="background-color: {{ $empresa->colorPrimario ?? '#007bff' }}"></div>
                </div>
                <small class="text-muted">Color principal del tema corporativo</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="colorSecundario" class="form-label">Color Secundario</label>
                <div class="color-picker-container">
                    <input type="color" class="form-control" id="colorSecundario" name="configuraciones[colorSecundario]" 
                           value="{{ $empresa->colorSecundario ?? '#6c757d' }}">
                    <div class="color-preview" style="background-color: {{ $empresa->colorSecundario ?? '#6c757d' }}"></div>
                </div>
                <small class="text-muted">Color secundario del tema corporativo</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="logo" class="form-label">Logo de la Empresa</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                <small class="text-muted">Formatos: JPG, PNG, SVG. Tamaño máximo: 2MB</small>
                @if(isset($empresa->logo) && $empresa->logo)
                    <div class="mt-2">
                        <img src="{{ asset('storage/logos/' . $empresa->logo) }}" 
                             alt="Logo actual" class="empresa-logo">
                        <p class="small text-muted mt-1">Logo actual</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="favicon" class="form-label">Favicon</label>
                <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                <small class="text-muted">Formatos: ICO, PNG. Tamaño máximo: 512KB</small>
                @if(isset($empresa->favicon) && $empresa->favicon)
                    <div class="mt-2">
                        <img src="{{ asset('storage/favicons/' . $empresa->favicon) }}" 
                             alt="Favicon actual" style="width: 32px; height: 32px;">
                        <p class="small text-muted mt-1">Favicon actual</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="tema_interfaz" class="form-label">Tema de Interfaz</label>
                <select class="form-control" id="tema_interfaz" name="configuraciones[tema_interfaz]">
                    <option value="light" 
                            {{ ($configuraciones['branding']['tema_interfaz']['valor'] ?? 'light') == 'light' ? 'selected' : '' }}>
                        Claro
                    </option>
                    <option value="dark" 
                            {{ ($configuraciones['branding']['tema_interfaz']['valor'] ?? 'light') == 'dark' ? 'selected' : '' }}>
                        Oscuro
                    </option>
                    <option value="auto" 
                            {{ ($configuraciones['branding']['tema_interfaz']['valor'] ?? 'light') == 'auto' ? 'selected' : '' }}>
                        Automático
                    </option>
                </select>
                <small class="text-muted">Tema visual del sistema</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="fuente_principal" class="form-label">Fuente Principal</label>
                <select class="form-control" id="fuente_principal" name="configuraciones[fuente_principal]">
                    <option value="system" 
                            {{ ($configuraciones['branding']['fuente_principal']['valor'] ?? 'system') == 'system' ? 'selected' : '' }}>
                        Fuente del Sistema
                    </option>
                    <option value="roboto" 
                            {{ ($configuraciones['branding']['fuente_principal']['valor'] ?? 'system') == 'roboto' ? 'selected' : '' }}>
                        Roboto
                    </option>
                    <option value="opensans" 
                            {{ ($configuraciones['branding']['fuente_principal']['valor'] ?? 'system') == 'opensans' ? 'selected' : '' }}>
                        Open Sans
                    </option>
                    <option value="montserrat" 
                            {{ ($configuraciones['branding']['fuente_principal']['valor'] ?? 'system') == 'montserrat' ? 'selected' : '' }}>
                        Montserrat
                    </option>
                </select>
                <small class="text-muted">Tipografía principal del sistema</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="estilo_bordes" class="form-label">Estilo de Bordes</label>
                <select class="form-control" id="estilo_bordes" name="configuraciones[estilo_bordes]">
                    <option value="rounded" 
                            {{ ($configuraciones['branding']['estilo_bordes']['valor'] ?? 'rounded') == 'rounded' ? 'selected' : '' }}>
                        Redondeados
                    </option>
                    <option value="square" 
                            {{ ($configuraciones['branding']['estilo_bordes']['valor'] ?? 'rounded') == 'square' ? 'selected' : '' }}>
                        Cuadrados
                    </option>
                    <option value="soft" 
                            {{ ($configuraciones['branding']['estilo_bordes']['valor'] ?? 'rounded') == 'soft' ? 'selected' : '' }}>
                        Suaves
                    </option>
                </select>
                <small class="text-muted">Estilo de los bordes de elementos</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="animaciones" class="form-label">Animaciones</label>
                <select class="form-control" id="animaciones" name="configuraciones[animaciones]">
                    <option value="enabled" 
                            {{ ($configuraciones['branding']['animaciones']['valor'] ?? 'enabled') == 'enabled' ? 'selected' : '' }}>
                        Habilitadas
                    </option>
                    <option value="reduced" 
                            {{ ($configuraciones['branding']['animaciones']['valor'] ?? 'enabled') == 'reduced' ? 'selected' : '' }}>
                        Reducidas
                    </option>
                    <option value="disabled" 
                            {{ ($configuraciones['branding']['animaciones']['valor'] ?? 'enabled') == 'disabled' ? 'selected' : '' }}>
                        Deshabilitadas
                    </option>
                </select>
                <small class="text-muted">Nivel de animaciones en la interfaz</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="mensaje_bienvenida" class="form-label">Mensaje de Bienvenida</label>
        <textarea class="form-control" id="mensaje_bienvenida" name="configuraciones[mensaje_bienvenida]" rows="3">{{ $configuraciones['branding']['mensaje_bienvenida']['valor'] ?? 'Bienvenido al Sistema de Gestión Psicosocial' }}</textarea>
        <small class="text-muted">Mensaje que aparece en la página de inicio</small>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Guardar Identidad Corporativa
        </button>
    </div>
</form>
