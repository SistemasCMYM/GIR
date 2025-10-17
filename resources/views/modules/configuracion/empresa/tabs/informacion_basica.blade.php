<!-- Información Básica de la Empresa -->
<form action="{{ route('configuracion.empresa.update') }}" method="POST">
    @csrf
    <input type="hidden" name="seccion" value="informacion_basica">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre de la Empresa</label>
                <input type="text" class="form-control" id="nombre" name="configuraciones[nombre]" 
                       value="{{ $empresa->nombre ?? '' }}" required>
                <small class="text-muted">Nombre comercial de la empresa</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="razon_social" class="form-label">Razón Social</label>
                <input type="text" class="form-control" id="razon_social" name="configuraciones[razon_social]" 
                       value="{{ $empresa->razon_social ?? '' }}" required>
                <small class="text-muted">Razón social registrada</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nit" class="form-label">NIT</label>
                <input type="text" class="form-control" id="nit" name="configuraciones[nit]" 
                       value="{{ $empresa->nit ?? '' }}" required>
                <small class="text-muted">Número de Identificación Tributaria</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email" class="form-label">Email Corporativo</label>
                <input type="email" class="form-control" id="email" name="configuraciones[email]" 
                       value="{{ $empresa->email ?? '' }}">
                <small class="text-muted">Email principal de contacto</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="configuraciones[telefono]" 
                       value="{{ $empresa->telefono ?? '' }}">
                <small class="text-muted">Número de teléfono principal</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sitio_web" class="form-label">Sitio Web</label>
                <input type="url" class="form-control" id="sitio_web" name="configuraciones[sitio_web]" 
                       value="{{ $empresa->sitio_web ?? '' }}">
                <small class="text-muted">URL del sitio web corporativo</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="direccion" class="form-label">Dirección</label>
        <textarea class="form-control" id="direccion" name="configuraciones[direccion]" rows="3">{{ $empresa->direccion ?? '' }}</textarea>
        <small class="text-muted">Dirección principal de la empresa</small>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Guardar Información Básica
        </button>
    </div>
</form>
