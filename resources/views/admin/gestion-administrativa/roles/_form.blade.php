@php
    $modulosSeleccionados = collect(old('modulos', $rolData['modulos'] ?? []))
        ->flatten()
        ->filter()
        ->values()
        ->all();
    $permisosSeleccionados = collect(old('permisos', $rolData['permisos'] ?? []))
        ->flatten()
        ->filter()
        ->values()
        ->all();
    $configuraciones = $opciones['configuraciones'] ?? [];
@endphp

<form method="POST" action="{{ $formAction }}" class="needs-validation" novalidate>
    @csrf
    @if (strtoupper($formMethod) !== 'POST')
        @method($formMethod)
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-3">
                        <i class="fas fa-sliders-h me-2 text-primary"></i>Detalles del rol
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-semibold">Nombre del rol <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="nombre" name="nombre" required>
                                <option value="" disabled
                                    {{ empty(old('nombre', $rolData['nombre'] ?? null)) ? 'selected' : '' }}>Selecciona
                                    un rol</option>
                                @foreach ($opciones['roles'] ?? [] as $rolNombre)
                                    <option value="{{ $rolNombre }}"
                                        {{ old('nombre', $rolData['nombre'] ?? null) === $rolNombre ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $rolNombre)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nombre')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tipo" class="form-label fw-semibold">Tipo de rol <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="" disabled
                                    {{ empty(old('tipo', $rolData['tipo'] ?? null)) ? 'selected' : '' }}>Selecciona un
                                    tipo</option>
                                @foreach ($opciones['tipos'] ?? [] as $tipo)
                                    <option value="{{ $tipo }}"
                                        {{ old('tipo', $rolData['tipo'] ?? null) === $tipo ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $tipo)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                placeholder="Describe brevemente las responsabilidades y alcance del rol">{{ old('descripcion', $rolData['descripcion'] ?? null) }}</textarea>
                            @error('descripcion')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="empresa_id" class="form-label fw-semibold">Empresa asociada</label>
                            <input type="text" class="form-control" id="empresa_id" name="empresa_id"
                                value="{{ old('empresa_id', $rolData['empresa_id'] ?? null) }}" placeholder="Opcional">
                            @error('empresa_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cuenta_id" class="form-label fw-semibold">Cuenta asociada</label>
                            <input type="text" class="form-control" id="cuenta_id" name="cuenta_id"
                                value="{{ old('cuenta_id', $rolData['cuenta_id'] ?? null) }}" placeholder="Opcional">
                            @error('cuenta_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="config_template" class="form-label fw-semibold">Plantillas rápidas</label>
                            <select class="form-select" id="config_template">
                                <option value="">Selecciona una plantilla</option>
                                @foreach ($configuraciones as $clave => $config)
                                    <option value="{{ $clave }}">
                                        {{ $config['nombre'] ?? \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $clave)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Puedes usar una plantilla para autocompletar módulos y
                                permisos.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <div class="form-check form-switch pt-2">
                                <input type="hidden" name="activo" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="activo"
                                    name="activo" value="1"
                                    {{ old('activo', $rolData['activo'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Rol activo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Resumen de la plantilla
                    </h5>
                    <div id="config-summary" class="small text-muted">
                        <p class="mb-2">Selecciona un rol o plantilla para ver los detalles rápidos.</p>
                        <ul class="list-unstyled mb-0" id="config-summary-list"></ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-3"><i class="fas fa-layer-group me-2 text-primary"></i>Módulos
                        habilitados</h5>
                    <div class="row g-3">
                        @foreach ($opciones['modulos'] ?? [] as $modulo)
                            <div class="col-sm-6 col-lg-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="modulo_{{ $loop->index }}"
                                        name="modulos[]" value="{{ $modulo }}"
                                        {{ in_array($modulo, $modulosSeleccionados, true) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="modulo_{{ $loop->index }}">{{ \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $modulo)) }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('modulos')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-3"><i
                            class="fas fa-user-shield me-2 text-primary"></i>Permisos otorgados</h5>
                    <div class="row g-3">
                        @foreach ($opciones['permisos'] ?? [] as $permiso)
                            <div class="col-sm-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="permiso_{{ $loop->index }}"
                                        name="permisos[]" value="{{ $permiso }}"
                                        {{ in_array($permiso, $permisosSeleccionados, true) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="permiso_{{ $loop->index }}">{{ \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $permiso)) }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('permisos')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('usuarios.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>{{ $submitLabel }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const configuraciones = @json($configuraciones);
            const selectPlantilla = document.getElementById('config_template');
            const selectNombre = document.getElementById('nombre');
            const selectTipo = document.getElementById('tipo');
            const descripcion = document.getElementById('descripcion');
            const modulosInputs = Array.from(document.querySelectorAll('input[name="modulos[]"]'));
            const permisosInputs = Array.from(document.querySelectorAll('input[name="permisos[]"]'));
            const summaryList = document.getElementById('config-summary-list');

            const renderSummary = (config) => {
                if (!summaryList) {
                    return;
                }
                summaryList.innerHTML = '';
                if (!config) {
                    summaryList.innerHTML = '<li class="text-muted">Sin plantilla seleccionada.</li>';
                    return;
                }
                summaryList.innerHTML = `
                    <li class="mb-2"><strong>Descripción:</strong> ${config.descripcion || '—'}</li>
                    <li class="mb-2"><strong>Módulos:</strong> ${(config.modulos || []).map((m) => `<span class="badge bg-primary-subtle text-primary me-1">${m}</span>`).join(' ') || '—'}</li>
                    <li><strong>Permisos:</strong> ${(config.permisos || []).map((p) => `<span class="badge bg-success-subtle text-success me-1">${p}</span>`).join(' ') || '—'}</li>
                `;
            };

            const toggleCheckboxes = (inputs, values) => {
                inputs.forEach((input) => {
                    input.checked = values.includes(input.value);
                });
            };

            const hydrateFromConfig = (key) => {
                const config = configuraciones[key];
                if (!config) {
                    renderSummary(null);
                    return;
                }
                selectTipo.value = config.tipo || '';
                descripcion.value = config.descripcion || '';
                toggleCheckboxes(modulosInputs, config.modulos || []);
                toggleCheckboxes(permisosInputs, config.permisos || []);
                renderSummary(config);
            };

            if (selectPlantilla) {
                selectPlantilla.addEventListener('change', (event) => {
                    const key = event.target.value;
                    if (key) {
                        selectNombre.value = key;
                        hydrateFromConfig(key);
                    } else {
                        renderSummary(null);
                    }
                });
            }

            if (selectNombre) {
                selectNombre.addEventListener('change', (event) => {
                    const key = event.target.value;
                    if (configuraciones[key]) {
                        selectPlantilla.value = key;
                        hydrateFromConfig(key);
                    } else {
                        selectPlantilla.value = '';
                        renderSummary(null);
                    }
                });
            }

            // Render initial summary when editing o con datos precargados
            const initialKey = selectNombre ? selectNombre.value : null;
            if (initialKey && configuraciones[initialKey]) {
                renderSummary(configuraciones[initialKey]);
            }
        });
    </script>
@endpush
