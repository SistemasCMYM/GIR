<!-- Configuración Regional -->
<form action="{{ route('configuracion.empresa.update') }}" method="POST">
    @csrf
    <input type="hidden" name="seccion" value="configuracion_regional">
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="zona_horaria" class="form-label">Zona Horaria</label>
                <select class="form-control" id="zona_horaria" name="configuraciones[zona_horaria]">
                    @if(isset($zonas_horarias))
                        @foreach($zonas_horarias as $value => $label)
                            <option value="{{ $value }}" 
                                    {{ ($empresa->zona_horaria ?? 'America/Bogota') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @else
                        <option value="America/Bogota" selected>Bogotá (GMT-5)</option>
                    @endif
                </select>
                <small class="text-muted">Zona horaria para fechas y horas del sistema</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="idioma" class="form-label">Idioma</label>
                <select class="form-control" id="idioma" name="configuraciones[idioma]">
                    @if(isset($idiomas))
                        @foreach($idiomas as $value => $label)
                            <option value="{{ $value }}" 
                                    {{ ($empresa->idioma ?? 'es-419') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @else
                        <option value="es-419" selected>Español (Latinoamérica)</option>
                    @endif
                </select>
                <small class="text-muted">Idioma principal del sistema</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="formato_fecha" class="form-label">Formato de Fecha</label>
                <select class="form-control" id="formato_fecha" name="configuraciones[formato_fecha]">
                    @if(isset($formatos['fecha']))
                        @foreach($formatos['fecha'] as $value => $example)
                            <option value="{{ $value }}" 
                                    {{ ($empresa->formato_fecha ?? 'DD/MM/YYYY') == $value ? 'selected' : '' }}>
                                {{ $value }} ({{ $example }})
                            </option>
                        @endforeach
                    @else
                        <option value="DD/MM/YYYY" selected>DD/MM/YYYY (31/12/2023)</option>
                    @endif
                </select>
                <small class="text-muted">Formato para mostrar fechas</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="formato_hora" class="form-label">Formato de Hora</label>
                <select class="form-control" id="formato_hora" name="configuraciones[formato_hora]">
                    @if(isset($formatos['hora']))
                        @foreach($formatos['hora'] as $value => $example)
                            <option value="{{ $value }}" 
                                    {{ ($empresa->formato_hora ?? '24') == $value ? 'selected' : '' }}>
                                {{ $value }} horas ({{ $example }})
                            </option>
                        @endforeach
                    @else
                        <option value="24" selected>24 horas (23:59)</option>
                    @endif
                </select>
                <small class="text-muted">Formato para mostrar horas</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="moneda_principal" class="form-label">Moneda Principal</label>
                <select class="form-control" id="moneda_principal" name="configuraciones[moneda_principal]">
                    @if(isset($monedas))
                        @foreach($monedas as $value => $label)
                            <option value="{{ $value }}" 
                                    {{ ($empresa->moneda_principal ?? 'COP') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @else
                        <option value="COP" selected>Peso Colombiano (COP)</option>
                    @endif
                </select>
                <small class="text-muted">Moneda para valores monetarios</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="separador_decimal" class="form-label">Separador Decimal</label>
                <select class="form-control" id="separador_decimal" name="configuraciones[separador_decimal]">
                    <option value="." {{ ($configuraciones['configuracion_regional']['separador_decimal']['valor'] ?? '.') == '.' ? 'selected' : '' }}>
                        Punto (.) - 1234.56
                    </option>
                    <option value="," {{ ($configuraciones['configuracion_regional']['separador_decimal']['valor'] ?? '.') == ',' ? 'selected' : '' }}>
                        Coma (,) - 1234,56
                    </option>
                </select>
                <small class="text-muted">Separador para decimales</small>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Guardar Configuración Regional
        </button>
    </div>
</form>
