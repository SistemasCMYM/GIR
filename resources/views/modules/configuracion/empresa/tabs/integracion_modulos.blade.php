<!-- Integración de Módulos -->
<form action="{{ route('configuracion.empresa.update') }}" method="POST">
    @csrf
    <input type="hidden" name="seccion" value="integracion_modulos">
    
    <div class="row">
        <div class="col-md-12">
            <h6 class="mb-3">
                <i class="fas fa-search me-2"></i>
                Configuración del Módulo de Hallazgos
            </h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="hallazgos_auto_numeracion" class="form-label">Auto-numeración de Hallazgos</label>
                <select class="form-control" id="hallazgos_auto_numeracion" name="configuraciones[hallazgos_auto_numeracion]">
                    <option value="si" 
                            {{ ($configuraciones['integracion_modulos']['hallazgos_auto_numeracion']['valor'] ?? 'si') == 'si' ? 'selected' : '' }}>
                        Habilitada
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['integracion_modulos']['hallazgos_auto_numeracion']['valor'] ?? 'si') == 'no' ? 'selected' : '' }}>
                        Deshabilitada
                    </option>
                </select>
                <small class="text-muted">Numeración automática para nuevos hallazgos</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="hallazgos_prefijo" class="form-label">Prefijo para Hallazgos</label>
                <input type="text" class="form-control" id="hallazgos_prefijo" name="configuraciones[hallazgos_prefijo]" 
                       value="{{ $configuraciones['integracion_modulos']['hallazgos_prefijo']['valor'] ?? 'HAL-' }}" 
                       maxlength="10">
                <small class="text-muted">Prefijo para la numeración de hallazgos</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="hallazgos_categorias" class="form-label">Categorías de Hallazgos</label>
                <textarea class="form-control" id="hallazgos_categorias" name="configuraciones[hallazgos_categorias]" rows="3">{{ $configuraciones['integracion_modulos']['hallazgos_categorias']['valor'] ?? 'Riesgo Alto, Riesgo Medio, Riesgo Bajo, Observación' }}</textarea>
                <small class="text-muted">Categorías separadas por comas</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="hallazgos_estados" class="form-label">Estados de Hallazgos</label>
                <textarea class="form-control" id="hallazgos_estados" name="configuraciones[hallazgos_estados]" rows="3">{{ $configuraciones['integracion_modulos']['hallazgos_estados']['valor'] ?? 'Nuevo, En Proceso, Resuelto, Cerrado' }}</textarea>
                <small class="text-muted">Estados separados por comas</small>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-12">
            <h6 class="mb-3">
                <i class="fas fa-brain me-2"></i>
                Configuración del Módulo Psicosocial
            </h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="psicosocial_instrumentos_activos" class="form-label">Instrumentos Activos</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="instrumento_intralaboral_a" 
                           name="configuraciones[psicosocial_instrumentos][]" value="intralaboral_forma_a"
                           {{ in_array('intralaboral_forma_a', explode(',', $configuraciones['integracion_modulos']['psicosocial_instrumentos']['valor'] ?? 'intralaboral_forma_a,intralaboral_forma_b,extralaboral,estres')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="instrumento_intralaboral_a">
                        Intralaboral Forma A
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="instrumento_intralaboral_b" 
                           name="configuraciones[psicosocial_instrumentos][]" value="intralaboral_forma_b"
                           {{ in_array('intralaboral_forma_b', explode(',', $configuraciones['integracion_modulos']['psicosocial_instrumentos']['valor'] ?? 'intralaboral_forma_a,intralaboral_forma_b,extralaboral,estres')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="instrumento_intralaboral_b">
                        Intralaboral Forma B
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="instrumento_extralaboral" 
                           name="configuraciones[psicosocial_instrumentos][]" value="extralaboral"
                           {{ in_array('extralaboral', explode(',', $configuraciones['integracion_modulos']['psicosocial_instrumentos']['valor'] ?? 'intralaboral_forma_a,intralaboral_forma_b,extralaboral,estres')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="instrumento_extralaboral">
                        Extralaboral
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="instrumento_estres" 
                           name="configuraciones[psicosocial_instrumentos][]" value="estres"
                           {{ in_array('estres', explode(',', $configuraciones['integracion_modulos']['psicosocial_instrumentos']['valor'] ?? 'intralaboral_forma_a,intralaboral_forma_b,extralaboral,estres')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="instrumento_estres">
                        Estrés
                    </label>
                </div>
                <small class="text-muted">Instrumentos disponibles para evaluación</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="psicosocial_auto_diagnostico" class="form-label">Diagnóstico Automático</label>
                <select class="form-control" id="psicosocial_auto_diagnostico" name="configuraciones[psicosocial_auto_diagnostico]">
                    <option value="si" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_auto_diagnostico']['valor'] ?? 'si') == 'si' ? 'selected' : '' }}>
                        Habilitado
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_auto_diagnostico']['valor'] ?? 'si') == 'no' ? 'selected' : '' }}>
                        Deshabilitado
                    </option>
                </select>
                <small class="text-muted">Cálculo automático de niveles de riesgo</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="psicosocial_alertas_email" class="form-label">Alertas por Email</label>
                <select class="form-control" id="psicosocial_alertas_email" name="configuraciones[psicosocial_alertas_email]">
                    <option value="si" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_alertas_email']['valor'] ?? 'si') == 'si' ? 'selected' : '' }}>
                        Habilitadas
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_alertas_email']['valor'] ?? 'si') == 'no' ? 'selected' : '' }}>
                        Deshabilitadas
                    </option>
                </select>
                <small class="text-muted">Notificaciones automáticas por email</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="psicosocial_periodo_reevaluacion" class="form-label">Período de Re-evaluación (meses)</label>
                <select class="form-control" id="psicosocial_periodo_reevaluacion" name="configuraciones[psicosocial_periodo_reevaluacion]">
                    <option value="6" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_periodo_reevaluacion']['valor'] ?? '12') == '6' ? 'selected' : '' }}>
                        6 meses
                    </option>
                    <option value="12" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_periodo_reevaluacion']['valor'] ?? '12') == '12' ? 'selected' : '' }}>
                        12 meses
                    </option>
                    <option value="24" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_periodo_reevaluacion']['valor'] ?? '12') == '24' ? 'selected' : '' }}>
                        24 meses
                    </option>
                    <option value="36" 
                            {{ ($configuraciones['integracion_modulos']['psicosocial_periodo_reevaluacion']['valor'] ?? '12') == '36' ? 'selected' : '' }}>
                        36 meses
                    </option>
                </select>
                <small class="text-muted">Frecuencia de re-evaluación recomendada</small>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-12">
            <h6 class="mb-3">
                <i class="fas fa-cogs me-2"></i>
                Configuración de Integración
            </h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="sincronizacion_automatica" class="form-label">Sincronización Automática</label>
                <select class="form-control" id="sincronizacion_automatica" name="configuraciones[sincronizacion_automatica]">
                    <option value="si" 
                            {{ ($configuraciones['integracion_modulos']['sincronizacion_automatica']['valor'] ?? 'si') == 'si' ? 'selected' : '' }}>
                        Habilitada
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['integracion_modulos']['sincronizacion_automatica']['valor'] ?? 'si') == 'no' ? 'selected' : '' }}>
                        Deshabilitada
                    </option>
                </select>
                <small class="text-muted">Sincronización automática entre módulos</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="reportes_integrados" class="form-label">Reportes Integrados</label>
                <select class="form-control" id="reportes_integrados" name="configuraciones[reportes_integrados]">
                    <option value="si" 
                            {{ ($configuraciones['integracion_modulos']['reportes_integrados']['valor'] ?? 'si') == 'si' ? 'selected' : '' }}>
                        Habilitados
                    </option>
                    <option value="no" 
                            {{ ($configuraciones['integracion_modulos']['reportes_integrados']['valor'] ?? 'si') == 'no' ? 'selected' : '' }}>
                        Deshabilitados
                    </option>
                </select>
                <small class="text-muted">Reportes que combinan datos de ambos módulos</small>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Guardar Configuración de Módulos
        </button>
    </div>
</form>
