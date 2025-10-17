@extends('layouts.dashboard')

@section('title', 'Crear Consentimiento')

@push('styles')
    <style>
        .plantilla-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .item-input {
            margin-bottom: 0.5rem;
        }

        .btn-add-item {
            border: 2px dashed #dee2e6;
            background: // Validación del formulario

            document.getElementById('consentimientoForm').addEventListener('submit', function(e) {
                    const titulo=document.getElementById('titulo').value.trim();
                    const contenido=document.getElementById('contenido').value.trim();

                    console.log('Validando formulario...');
                    console.log('Título:', titulo);
                    console.log('Contenido:', contenido);

                    if ( !titulo || !contenido) {
                        e.preventDefault();
                        alert('Por favor complete los campos obligatorios: Título y Contenido del consentimiento.');
                        return;
                    }

                    // Obtener ítems válidos
                    const items=Array.from(document.querySelectorAll('input[name="items[]"]')) .map(input=> input.value.trim()) .filter(item=> item !== '');

                    console.log('Items válidos:', items);

                    // Confirmar creación
                    if (confirm('¿Crear el nuevo consentimiento con estos datos?')) {
                        console.log('Enviando formulario...');
                        return true;
                    }

                    else {
                        e.preventDefault();
                    }

                    color: #6c757d;
                    transition: all 0.3s ease;
                });

            .btn-add-item:hover {
                border-color: #007bff;
                color: #007bff;
                background: rgba(0, 123, 255, 0.05);
            }

            .item-container {
                position: relative;
                margin-bottom: 0.75rem;
            }

            .btn-remove-item {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                z-index: 10;
            }

            .form-section {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .section-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid #e5e7eb;
            }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empleados.index') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.index') }}">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-instrumentos.consentimientos.index') }}">Consentimientos</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-briefcase"></i> Crear Consentimiento
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Crear Nuevo Consentimiento</h1>
                        <p class="text-muted">Complete la información para crear un nuevo consentimiento informado</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.consentimientos.index') }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <a href="{{ route('gestion-instrumentos.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Volver al módulo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('gestion-instrumentos.consentimientos.store') }}" method="POST" id="consentimientoForm">
            @csrf

            <!-- Información Básica -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Información Básica
                </h5>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título del Consentimiento <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo"
                                name="titulo" value="{{ old('titulo') }}"
                                placeholder="Ej: Consentimiento Informado para Evaluación Psicosocial" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Consentimiento <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo"
                                required>
                                <option value="">Seleccione un tipo</option>
                                @foreach ($tipos as $valor => $etiqueta)
                                    <option value="{{ $valor }}" {{ old('tipo') === $valor ? 'selected' : '' }}>
                                        {{ $etiqueta }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción Breve</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                        rows="2" placeholder="Descripción opcional del propósito del consentimiento">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Contenido del Consentimiento -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-file-alt me-2"></i>
                    Contenido del Consentimiento
                </h5>

                <div class="mb-3">
                    <label for="contenido" class="form-label">Texto del Consentimiento <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido" rows="8"
                        required placeholder="Escriba aquí el contenido completo del consentimiento informado...">{{ old('contenido') }}</textarea>
                    @error('contenido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Puede usar la plantilla de ejemplo como base y modificarla según sus necesidades.
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info" onclick="cargarPlantillaEjemplo()">
                        <i class="fas fa-file-import me-2"></i>Usar Plantilla de Ejemplo
                    </button>
                </div>
            </div>

            <!-- Ítems del Consentimiento -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-list-check me-2"></i>
                    Ítems del Consentimiento
                </h5>

                <p class="text-muted mb-3">
                    Agregue los puntos específicos que el usuario debe aceptar. Estos aparecerán como casillas de
                    verificación.
                </p>

                <div id="items-container">
                    @if (old('items'))
                        @foreach (old('items') as $index => $item)
                            <div class="item-container">
                                <div class="input-group">
                                    <span class="input-group-text">{{ $index + 1 }}</span>
                                    <input type="text" class="form-control" name="items[]" value="{{ $item }}"
                                        placeholder="Escriba el ítem del consentimiento">
                                    <button type="button" class="btn btn-outline-danger btn-remove-item"
                                        onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <button type="button" class="btn btn-add-item w-100 py-2" onclick="addItem()">
                    <i class="fas fa-plus me-2"></i>Agregar Ítem
                </button>
            </div>

            <!-- Configuración -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-cog me-2"></i>
                    Configuración
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="requiere_firma" name="requiere_firma"
                                value="1" checked>
                            <label class="form-check-label" for="requiere_firma">
                                <strong>Requiere Firma Digital</strong>
                                <small class="d-block text-muted">El usuario debe firmar digitalmente el
                                    consentimiento</small>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="requiere_fecha" name="requiere_fecha"
                                value="1" checked>
                            <label class="form-check-label" for="requiere_fecha">
                                <strong>Registrar Fecha de Diligenciamiento</strong>
                                <small class="d-block text-muted">Se guardará automáticamente la fecha y hora</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.consentimientos.index') }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-warning" onclick="previsualizarConsentimiento()">
                            <i class="fas fa-eye me-2"></i>Previsualizar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Consentimiento
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de Previsualización -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Previsualización del Consentimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="preview-content" class="plantilla-preview">
                        <!-- El contenido se cargará aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let itemCounter = {{ old('items') ? count(old('items')) : 0 }};

        // Plantilla de ejemplo
        const plantillaEjemplo = @json($plantilla_ejemplo);

        function addItem() {
            itemCounter++;
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className = 'item-container';
            itemDiv.innerHTML = `
        <div class="input-group">
            <span class="input-group-text">${itemCounter}</span>
            <input type="text" class="form-control" name="items[]" 
                   placeholder="Escriba el ítem del consentimiento">
            <button type="button" class="btn btn-outline-danger btn-remove-item" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
            container.appendChild(itemDiv);
            updateItemNumbers();
        }

        function removeItem(button) {
            const itemContainer = button.closest('.item-container');
            itemContainer.remove();
            updateItemNumbers();
        }

        function updateItemNumbers() {
            const items = document.querySelectorAll('#items-container .item-container');
            items.forEach((item, index) => {
                const numberSpan = item.querySelector('.input-group-text');
                numberSpan.textContent = index + 1;
            });
            itemCounter = items.length;
        }

        function cargarPlantillaEjemplo() {
            if (confirm('¿Desea cargar la plantilla de ejemplo? Esto sobrescribirá el contenido actual.')) {
                // Cargar título
                document.getElementById('titulo').value = plantillaEjemplo.titulo;

                // Cargar contenido
                const contenido =
                    `${plantillaEjemplo.introduccion}\n\n${plantillaEjemplo.declaracion}\n\n${plantillaEjemplo.footer}`;
                document.getElementById('contenido').value = contenido;

                // Limpiar ítems existentes
                document.getElementById('items-container').innerHTML = '';
                itemCounter = 0;

                // Cargar ítems de la plantilla
                plantillaEjemplo.items.forEach(item => {
                    addItemWithText(item);
                });

                alert('Plantilla cargada exitosamente. Puede modificar el contenido según sus necesidades.');
            }
        }

        function addItemWithText(text) {
            itemCounter++;
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className = 'item-container';
            itemDiv.innerHTML = `
        <div class="input-group">
            <span class="input-group-text">${itemCounter}</span>
            <input type="text" class="form-control" name="items[]" value="${text}">
            <button type="button" class="btn btn-outline-danger btn-remove-item" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
            container.appendChild(itemDiv);
        }

        function previsualizarConsentimiento() {
            const titulo = document.getElementById('titulo').value || 'CONSENTIMIENTO INFORMADO';
            const contenido = document.getElementById('contenido').value || 'No se ha especificado contenido.';
            const items = Array.from(document.querySelectorAll('input[name="items[]"]')).map(input => input.value).filter(
                item => item.trim() !== '');

            let previewHtml = `
        <div class="text-center mb-4">
            <h4 class="fw-bold">${titulo}</h4>
        </div>
        
        <div class="mb-4">
            <p>${contenido.replace(/\n/g, '</p><p>')}</p>
        </div>
    `;

            if (items.length > 0) {
                previewHtml += `
            <div class="mb-4">
                <h6 class="fw-bold">Puntos a considerar:</h6>
                <ul class="list-unstyled">
        `;

                items.forEach((item, index) => {
                    previewHtml += `
                <li class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled>
                        <label class="form-check-label">${item}</label>
                    </div>
                </li>
            `;
                });

                previewHtml += `
                </ul>
            </div>
        `;
            }

            previewHtml += `
        <div class="border-top pt-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="acepta_preview" disabled>
                        <label class="form-check-label fw-bold text-success">
                            ✓ ACEPTO los términos del consentimiento
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="acepta_preview" disabled>
                        <label class="form-check-label fw-bold text-danger">
                            ✗ NO ACEPTO los términos del consentimiento
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Firma Digital:</label>
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Área para firma digital</small>
                </div>
            </div>
        </div>
    `;

            document.getElementById('preview-content').innerHTML = previewHtml;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        // Validación del formulario
        document.getElementById('consentimientoForm').addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const contenido = document.getElementById('contenido').value.trim();

            if (!titulo || !contenido) {
                e.preventDefault();
                alert('Por favor complete los campos obligatorios: Título y Contenido del consentimiento.');
                return;
            }

            // Confirmar creación
            if (!confirm('¿Crear el consentimiento con la información proporcionada?')) {
                e.preventDefault();
            }
        });

        // Inicializar si hay items del old()
        document.addEventListener('DOMContentLoaded', function() {
            updateItemNumbers();
        });
    </script>
@endpush
