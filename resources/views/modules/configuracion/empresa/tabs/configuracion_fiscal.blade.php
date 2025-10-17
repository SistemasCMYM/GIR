<!-- Configuración Fiscal -->
<form action="{{ route('configuracion.empresa.update') }}" method="POST">
    @csrf
    <input type="hidden" name="seccion" value="configuracion_fiscal">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="regimen_tributario" class="form-label">Régimen Tributario</label>
                <select class="form-control" id="regimen_tributario" name="configuraciones[regimen_tributario]">
                    <option value="gran_contribuyente" 
                            {{ ($configuraciones['configuracion_fiscal']['regimen_tributario']['valor'] ?? '') == 'gran_contribuyente' ? 'selected' : '' }}>
                        Gran Contribuyente
                    </option>
                    <option value="regimen_comun" 
                            {{ ($configuraciones['configuracion_fiscal']['regimen_tributario']['valor'] ?? '') == 'regimen_comun' ? 'selected' : '' }}>
                        Régimen Común
                    </option>
                    <option value="regimen_simple" 
                            {{ ($configuraciones['configuracion_fiscal']['regimen_tributario']['valor'] ?? '') == 'regimen_simple' ? 'selected' : '' }}>
                        Régimen Simple de Tributación
                    </option>
                    <option value="no_responsable" 
                            {{ ($configuraciones['configuracion_fiscal']['regimen_tributario']['valor'] ?? '') == 'no_responsable' ? 'selected' : '' }}>
                        No Responsable de IVA
                    </option>
                </select>
                <small class="text-muted">Clasificación tributaria de la empresa</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="actividad_economica" class="form-label">Actividad Económica (CIIU)</label>
                <input type="text" class="form-control" id="actividad_economica" name="configuraciones[actividad_economica]" 
                       value="{{ $configuraciones['configuracion_fiscal']['actividad_economica']['valor'] ?? '' }}">
                <small class="text-muted">Código CIIU de la actividad principal</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="responsable_iva" class="form-label">Responsable de IVA</label>
                <select class="form-control" id="responsable_iva" name="configuraciones[responsable_iva]">
                    <option value="si" 
                            {{ ($configuraciones['configuracion_fiscal']['responsable_iva']['valor'] ?? 'si') == 'si' ? 'selected' : '' }}>
                        Sí
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['configuracion_fiscal']['responsable_iva']['valor'] ?? 'si') == 'no' ? 'selected' : '' }}>
                        No
                    </option>
                </select>
                <small class="text-muted">¿La empresa es responsable de IVA?</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="tarifa_iva" class="form-label">Tarifa de IVA (%)</label>
                <input type="number" class="form-control" id="tarifa_iva" name="configuraciones[tarifa_iva]" 
                       value="{{ $configuraciones['configuracion_fiscal']['tarifa_iva']['valor'] ?? '19' }}" 
                       min="0" max="100" step="0.01">
                <small class="text-muted">Tarifa de IVA aplicable</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="autorretenciones" class="form-label">Autorretenciones</label>
                <select class="form-control" id="autorretenciones" name="configuraciones[autorretenciones]">
                    <option value="si" 
                            {{ ($configuraciones['configuracion_fiscal']['autorretenciones']['valor'] ?? 'no') == 'si' ? 'selected' : '' }}>
                        Sí
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['configuracion_fiscal']['autorretenciones']['valor'] ?? 'no') == 'no' ? 'selected' : '' }}>
                        No
                    </option>
                </select>
                <small class="text-muted">¿La empresa practica autorretenciones?</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="retencion_fuente" class="form-label">Retención en la Fuente (%)</label>
                <input type="number" class="form-control" id="retencion_fuente" name="configuraciones[retencion_fuente]" 
                       value="{{ $configuraciones['configuracion_fiscal']['retencion_fuente']['valor'] ?? '2.5' }}" 
                       min="0" max="100" step="0.1">
                <small class="text-muted">Porcentaje de retención en la fuente</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="representante_legal" class="form-label">Representante Legal</label>
        <input type="text" class="form-control" id="representante_legal" name="configuraciones[representante_legal]" 
               value="{{ $configuraciones['configuracion_fiscal']['representante_legal']['valor'] ?? '' }}">
        <small class="text-muted">Nombre completo del representante legal</small>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="documento_representante" class="form-label">Documento del Representante</label>
                <input type="text" class="form-control" id="documento_representante" name="configuraciones[documento_representante]" 
                       value="{{ $configuraciones['configuracion_fiscal']['documento_representante']['valor'] ?? '' }}">
                <small class="text-muted">Número de documento de identidad</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="contador_publico" class="form-label">Contador Público</label>
                <input type="text" class="form-control" id="contador_publico" name="configuraciones[contador_publico]" 
                       value="{{ $configuraciones['configuracion_fiscal']['contador_publico']['valor'] ?? '' }}">
                <small class="text-muted">Nombre del contador público</small>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Guardar Configuración Fiscal
        </button>
    </div>
</form>
