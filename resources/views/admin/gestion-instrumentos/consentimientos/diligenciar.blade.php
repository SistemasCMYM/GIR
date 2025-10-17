@extends('layouts.dashboard')

@section('title', 'Diligenciar Consentimiento')

@push('styles')
    <style>
        .consentimiento-form {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .header-consentimiento {
            background: linear-gradient(135deg, #059669 0%, #065f46 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .decision-buttons {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .decision-option {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .decision-option:hover {
            border-color: #6366f1;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
        }

        .decision-option.selected {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .decision-option.selected.rechaza {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .decision-option input[type="radio"] {
            margin-right: 1rem;
            transform: scale(1.2);
        }

        .signature-pad {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signature-pad:hover {
            border-color: #6366f1;
            background: #f0f9ff;
        }

        .signature-canvas {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            cursor: crosshair;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .items-checklist {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .item-check {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .item-check:hover {
            border-color: #6366f1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .item-check input[type="checkbox"] {
            margin-right: 1rem;
            transform: scale(1.2);
        }

        .submit-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            margin-top: 2rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('gestion-instrumentos.index') }}">Gestión de Instrumentos</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('gestion-instrumentos.consentimientos.index') }}">Consentimientos</a>
                                </li>
                                <li class="breadcrumb-item active">Diligenciar</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.consentimientos.show', $consentimiento) }}"
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

        <!-- Header del Consentimiento -->
        <div class="header-consentimiento">
            <h2 class="mb-2">
                <i class="fas fa-file-signature me-3"></i>
                {{ $consentimiento->titulo }}
            </h2>
            <p class="mb-0 opacity-75">Lea cuidadosamente y complete el formulario de consentimiento</p>
        </div>

        <form action="{{ route('gestion-instrumentos.consentimientos.procesar', $consentimiento) }}" method="POST"
            enctype="multipart/form-data" id="consentimientoForm">
            @csrf

            <!-- Contenido del Consentimiento -->
            <div class="consentimiento-form">
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-file-alt me-2"></i>
                        Contenido del Consentimiento
                    </h5>

                    <div class="content-display" style="line-height: 1.8; font-size: 1rem;">
                        {!! nl2br(e($consentimiento->contenido)) !!}
                    </div>
                </div>

                <!-- Ítems específicos si existen -->
                @if (
                    $consentimiento->configuracion &&
                        isset($consentimiento->configuracion['items']) &&
                        count($consentimiento->configuracion['items']) > 0)
                    <div class="form-section">
                        <h5 class="mb-3">
                            <i class="fas fa-list-check me-2"></i>
                            Puntos Específicos del Consentimiento
                        </h5>

                        <div class="items-checklist">
                            <p class="mb-3"><strong>Por favor, confirme que comprende cada uno de los siguientes
                                    puntos:</strong></p>

                            @foreach ($consentimiento->configuracion['items'] as $index => $item)
                                <div class="item-check">
                                    <input type="checkbox" id="item_{{ $index }}" name="items_confirmados[]"
                                        value="{{ $index }}" required>
                                    <label for="item_{{ $index }}" class="mb-0">{{ $item }}</label>
                                </div>
                            @endforeach

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Debe confirmar todos los puntos para continuar</small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Decisión -->
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-gavel me-2"></i>
                        Su Decisión
                    </h5>

                    <div class="decision-buttons">
                        <p class="mb-3"><strong>Después de leer el contenido anterior, por favor indique su
                                decisión:</strong></p>

                        <div class="decision-option" onclick="selectDecision(this, true)">
                            <input type="radio" name="acepta" value="1" id="acepta_si" required>
                            <label for="acepta_si" class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>ACEPTO</strong> los términos y condiciones del consentimiento informado
                            </label>
                        </div>

                        <div class="decision-option" onclick="selectDecision(this, false)">
                            <input type="radio" name="acepta" value="0" id="acepta_no" required>
                            <label for="acepta_no" class="mb-0">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <strong>NO ACEPTO</strong> los términos y condiciones del consentimiento informado
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Firma Digital -->
                @if ($consentimiento->configuracion && ($consentimiento->configuracion['requiere_firma'] ?? true))
                    <div class="form-section">
                        <h5 class="mb-3">
                            <i class="fas fa-signature me-2"></i>
                            Firma Digital
                        </h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Dibuje su firma en el área a continuación:</label>
                                    <div class="signature-pad" id="signature-pad">
                                        <canvas id="signature-canvas" width="600" height="180"
                                            class="signature-canvas"></canvas>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                            onclick="clearSignature()">
                                            <i class="fas fa-eraser me-2"></i>Limpiar Firma
                                        </button>
                                    </div>
                                    <input type="hidden" name="firma_digital" id="firma_digital">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">O suba una imagen de su firma:</label>
                                    <input type="file" class="form-control" name="firma_imagen" accept="image/*"
                                        onchange="previewSignature(this)">
                                    <small class="text-muted">Formatos: JPG, PNG. Máximo 2MB</small>
                                    <div id="signature-preview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Observaciones -->
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-comment me-2"></i>
                        Observaciones (Opcional)
                    </h5>

                    <textarea class="form-control" name="observaciones" rows="4"
                        placeholder="Si desea, puede agregar comentarios adicionales..."></textarea>
                </div>

                <!-- Información Automática -->
                @if ($consentimiento->configuracion && ($consentimiento->configuracion['requiere_fecha'] ?? true))
                    <div class="form-section">
                        <h5 class="mb-3">
                            <i class="fas fa-clock me-2"></i>
                            Información de Registro
                        </h5>

                        <div class="alert alert-info">
                            <p class="mb-2"><strong>Se registrará automáticamente:</strong></p>
                            <ul class="mb-0">
                                <li>Fecha y hora de diligenciamiento</li>
                                <li>Dirección IP desde donde se envía</li>
                                <li>Información del navegador utilizado</li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Botones de Envío -->
            <div class="submit-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.consentimientos.show', $consentimiento) }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-warning" onclick="validarFormulario()">
                            <i class="fas fa-check me-2"></i>Validar Datos
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Enviar Consentimiento
                        </button>
                    </div>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Al enviar este formulario, confirma que ha leído y comprendido el contenido del consentimiento.
                    </small>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let canvas, ctx;
        let isDrawing = false;
        let hasSignature = false;

        document.addEventListener('DOMContentLoaded', function() {
            initializeSignaturePad();
            updateSubmitButton();
        });

        function initializeSignaturePad() {
            canvas = document.getElementById('signature-canvas');
            ctx = canvas.getContext('2d');

            // Configurar canvas
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';

            // Eventos del mouse
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // Eventos táctiles para dispositivos móviles
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDrawing);
        }

        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        function draw(e) {
            if (!isDrawing) return;

            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
            hasSignature = true;
            updateSubmitButton();
        }

        function stopDrawing() {
            if (isDrawing) {
                isDrawing = false;
                // Guardar firma como base64
                document.getElementById('firma_digital').value = canvas.toDataURL();
            }
        }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' :
                e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
            canvas.dispatchEvent(mouseEvent);
        }

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('firma_digital').value = '';
            hasSignature = false;
            updateSubmitButton();
        }

        function selectDecision(element, acepta) {
            // Limpiar selecciones anteriores
            document.querySelectorAll('.decision-option').forEach(opt => {
                opt.classList.remove('selected', 'rechaza');
            });

            // Seleccionar nueva opción
            element.classList.add('selected');
            if (!acepta) {
                element.classList.add('rechaza');
            }

            // Marcar radio button
            const radio = element.querySelector('input[type="radio"]');
            radio.checked = true;

            updateSubmitButton();
        }

        function previewSignature(input) {
            const preview = document.getElementById('signature-preview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '100px';
                    img.style.border = '1px solid #ddd';
                    img.style.borderRadius = '4px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }

            updateSubmitButton();
        }

        function validarFormulario() {
            const errores = [];

            // Validar decisión
            const decision = document.querySelector('input[name="acepta"]:checked');
            if (!decision) {
                errores.push('Debe seleccionar si acepta o no el consentimiento');
            }

            // Validar ítems si existen
            const itemsRequeridos = document.querySelectorAll('input[name="items_confirmados[]"]');
            if (itemsRequeridos.length > 0) {
                const itemsConfirmados = document.querySelectorAll('input[name="items_confirmados[]"]:checked');
                if (itemsConfirmados.length !== itemsRequeridos.length) {
                    errores.push('Debe confirmar todos los puntos del consentimiento');
                }
            }

            // Validar firma si es requerida
            @if ($consentimiento->configuracion && ($consentimiento->configuracion['requiere_firma'] ?? true))
                const firmaDigital = document.getElementById('firma_digital').value;
                const firmaArchivo = document.querySelector('input[name="firma_imagen"]').files.length;

                if (!hasSignature && !firmaDigital && firmaArchivo === 0) {
                    errores.push('Debe proporcionar una firma digital o subir una imagen de su firma');
                }
            @endif

            if (errores.length > 0) {
                alert('Errores encontrados:\n\n' + errores.join('\n'));
                return false;
            }

            alert('✓ Todos los datos están completos. Puede enviar el formulario.');
            return true;
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submit-btn');
            const decision = document.querySelector('input[name="acepta"]:checked');

            // Verificar ítems si existen
            const itemsRequeridos = document.querySelectorAll('input[name="items_confirmados[]"]');
            let itemsCompletos = true;
            if (itemsRequeridos.length > 0) {
                const itemsConfirmados = document.querySelectorAll('input[name="items_confirmados[]"]:checked');
                itemsCompletos = itemsConfirmados.length === itemsRequeridos.length;
            }

            // Verificar firma si es requerida
            let firmaCompleta = true;
            @if ($consentimiento->configuracion && ($consentimiento->configuracion['requiere_firma'] ?? true))
                const firmaArchivo = document.querySelector('input[name="firma_imagen"]').files.length;
                firmaCompleta = hasSignature || firmaArchivo > 0;
            @endif

            if (decision && itemsCompletos && firmaCompleta) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-primary');
            }
        }

        // Event listeners para actualizar el botón
        document.addEventListener('change', updateSubmitButton);

        // Validación final antes del envío
        document.getElementById('consentimientoForm').addEventListener('submit', function(e) {
            if (!validarFormulario()) {
                e.preventDefault();
                return;
            }

            if (!confirm('¿Está seguro de enviar el consentimiento? Esta acción no se puede deshacer.')) {
                e.preventDefault();
                return;
            }

            // Mostrar mensaje de procesamiento
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            submitBtn.disabled = true;
        });
    </script>
@endpush
