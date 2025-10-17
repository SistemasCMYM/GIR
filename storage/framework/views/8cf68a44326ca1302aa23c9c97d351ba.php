
<?php $__env->startSection('title', 'Crear Nueva Encuesta'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Wizard Container */
        .wizard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Wizard Steps */
        .wizard-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }

        .wizard-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }

        .wizard-step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 600;
            color: #9ca3af;
            transition: all 0.3s ease;
        }

        .wizard-step.active .step-circle {
            border-color: #d1a854;
            background: #d1a854;
            color: white;
            transform: scale(1.1);
        }

        .wizard-step.completed .step-circle {
            border-color: #10b981;
            background: #10b981;
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }

        .wizard-step.active .step-label {
            color: #d1a854;
            font-weight: 600;
        }

        /* Template Cards */
        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .template-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            text-align: center;
        }

        .template-card:hover {
            border-color: #d1a854;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .template-card.selected {
            border-color: #d1a854;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .template-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
        }

        .template-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .template-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .template-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }

        /* Form Sections */
        .step-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            min-height: 400px;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f3f4f6;
        }

        /* Question Builder */
        .question-builder {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #f9fafb;
        }

        .question-options {
            display: none;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #e5e7eb;
        }

        .question-options.show {
            display: block;
        }

        /* Navigation Buttons */
        .wizard-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f3f4f6;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Preview Cards */
        .preview-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            color: white;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('gestion-instrumentos.index')); ?>">
                        <i class="fas fa-clipboard-list"></i> Gestión de Instrumentos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>">
                        <i class="fas fa-poll"></i> Encuestas
                    </a>
                </li>
                <li class="breadcrumb-item active">Crear Nueva Encuesta</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    Crear Nueva Encuesta
                </h1>
                <p class="text-muted mb-0">Diseñe encuestas personalizadas para evaluación organizacional</p>
            </div>
        </div>

        <div class="wizard-container">
            <!-- Wizard Steps Indicator -->
            <div class="wizard-steps">
                <div class="wizard-step active" id="wizardStep1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Seleccione una plantilla</div>
                </div>
                <div class="wizard-step" id="wizardStep2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Información básica</div>
                </div>
                <div class="wizard-step" id="wizardStep3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Diseñar preguntas</div>
                </div>
                <div class="wizard-step" id="wizardStep4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Revisión final</div>
                </div>
            </div>

            <form id="encuestaForm" method="POST" action="<?php echo e(route('gestion-instrumentos.encuestas.store')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Paso 1: Seleccionar Plantilla -->
                <div id="stepContent1" class="step-content">
                    <h2 class="form-section-title">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Paso 1: Seleccione una plantilla
                    </h2>
                    <p class="text-muted mb-4">Elija una plantilla base para su encuesta o comience desde cero</p>

                    <input type="hidden" name="template_tipo" id="templateTipo">

                    <div class="template-grid">
                        <!-- Satisfacción Laboral -->
                        <div class="template-card" onclick="selectTemplate('satisfaccion_laboral')">
                            <div class="template-icon"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                <i class="fas fa-smile text-white"></i>
                            </div>
                            <div class="template-title">Satisfacción Laboral</div>
                            <div class="template-description">
                                Evalúa el nivel de satisfacción de los empleados con su trabajo, ambiente laboral y
                                condiciones.
                            </div>
                            <div class="template-meta">
                                <i class="fas fa-question-circle"></i>
                                <span>15 preguntas base</span>
                            </div>
                        </div>

                        <!-- Clima Organizacional -->
                        <div class="template-card" onclick="selectTemplate('clima_organizacional')">
                            <div class="template-icon"
                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div class="template-title">Clima Organizacional</div>
                            <div class="template-description">
                                Mide la percepción del ambiente de trabajo, comunicación y cultura organizacional.
                            </div>
                            <div class="template-meta">
                                <i class="fas fa-question-circle"></i>
                                <span>20 preguntas base</span>
                            </div>
                        </div>

                        <!-- Compromiso Laboral -->
                        <div class="template-card" onclick="selectTemplate('compromiso_laboral')">
                            <div class="template-icon"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                <i class="fas fa-heart text-white"></i>
                            </div>
                            <div class="template-title">Compromiso Laboral</div>
                            <div class="template-description">
                                Evalúa el nivel de compromiso y engagement de los colaboradores con la organización.
                            </div>
                            <div class="template-meta">
                                <i class="fas fa-question-circle"></i>
                                <span>12 preguntas base</span>
                            </div>
                        </div>

                        <!-- Personalizada -->
                        <div class="template-card" onclick="selectTemplate('personalizada')">
                            <div class="template-icon"
                                style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            <div class="template-title">Encuesta Personalizada</div>
                            <div class="template-description">
                                Cree su propia encuesta desde cero con preguntas completamente personalizadas.
                            </div>
                            <div class="template-meta">
                                <i class="fas fa-infinity"></i>
                                <span>Flexible</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Información Básica -->
                <div id="stepContent2" class="step-content" style="display: none;">
                    <h2 class="form-section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Paso 2: Información básica
                    </h2>
                    <p class="text-muted mb-4">Complete los datos generales de la encuesta</p>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-semibold">
                                    <i class="fas fa-heading me-1 text-primary"></i>
                                    Nombre de la encuesta <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="nombre" name="nombre"
                                    placeholder="Ej: Encuesta de Clima Laboral 2025" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label fw-semibold">
                                    <i class="fas fa-align-left me-1 text-primary"></i>
                                    Descripción
                                </label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                    placeholder="Describa el objetivo y alcance de esta encuesta..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_inicio" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt me-1 text-success"></i>
                                            Fecha de inicio
                                        </label>
                                        <input type="date" class="form-control" id="fecha_inicio"
                                            name="fecha_inicio">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_cierre" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-times me-1 text-danger"></i>
                                            Fecha de cierre
                                        </label>
                                        <input type="date" class="form-control" id="fecha_cierre"
                                            name="fecha_cierre">
                                    </div>
                                </div>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="anonima" name="anonima"
                                    value="1">
                                <label class="form-check-label fw-semibold" for="anonima">
                                    <i class="fas fa-user-secret me-1"></i>
                                    Encuesta anónima
                                </label>
                                <small class="d-block text-muted">Los participantes no serán identificados</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="preview-card">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-eye me-2 text-primary"></i>
                                    Vista previa
                                </h6>
                                <h5 id="previewNombre" class="text-primary mb-2">Nombre de la encuesta</h5>
                                <p id="previewDescripcion" class="text-muted small">La descripción aparecerá aquí</p>
                                <hr>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="fas fa-calendar me-1"></i> Periodo:</span>
                                        <strong id="previewFechas">Fechas no definidas</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Diseñar Preguntas -->
                <div id="stepContent3" class="step-content" style="display: none;">
                    <h2 class="form-section-title">
                        <i class="fas fa-question-circle me-2"></i>
                        Paso 3: Diseñar preguntas
                    </h2>
                    <p class="text-muted mb-4">Agregue y configure las preguntas de su encuesta</p>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="fas fa-list-ol me-2"></i>Preguntas de la encuesta</h6>
                        <button type="button" class="btn btn-primary" onclick="agregarPregunta()">
                            <i class="fas fa-plus me-2"></i>Agregar Pregunta
                        </button>
                    </div>

                    <div id="preguntasContainer">
                        <div id="noPreguntasMsg" class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <p>No hay preguntas agregadas aún</p>
                            <button type="button" class="btn btn-outline-primary" onclick="agregarPregunta()">
                                <i class="fas fa-plus me-2"></i>Agregar Primera Pregunta
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Paso 4: Revisión Final -->
                <div id="stepContent4" class="step-content" style="display: none;">
                    <h2 class="form-section-title">
                        <i class="fas fa-check-circle me-2"></i>
                        Paso 4: Revisión final
                    </h2>
                    <p class="text-muted mb-4">Revise y confirme los datos de su encuesta antes de guardar</p>

                    <div class="row">
                        <div class="col-md-8">
                            <div id="resumenEncuesta" class="preview-card">
                                <h5 class="fw-bold">Resumen de la Encuesta</h5>
                                <p class="text-muted">Los datos aparecerán aquí</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card mb-3"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="stat-value" id="totalPreguntas">0</div>
                                <div class="stat-label">Total de Preguntas</div>
                            </div>
                            <div class="stat-card mb-3"
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="stat-value" id="tiempoEstimado">0 min</div>
                                <div class="stat-label">Tiempo Estimado</div>
                            </div>
                            <div class="preview-card">
                                <h6 class="fw-bold mb-3">Configuración</h6>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tipo:</span>
                                        <strong id="tipoEncuesta">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Estado:</span>
                                        <strong id="estadoEncuesta">Borrador</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="wizard-navigation">
                    <button type="button" class="btn btn-outline-secondary btn-lg" id="prevBtn"
                        onclick="previousStep()" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Anterior
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="nextBtn" onclick="nextStep()">
                        Siguiente<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentStep = 1;
            let totalSteps = 4;
            let preguntaCount = 0;

            // Seleccionar plantilla
            function selectTemplate(tipo) {
                document.querySelectorAll('.template-card').forEach(card => {
                    card.classList.remove('selected');
                });
                event.target.closest('.template-card').classList.add('selected');
                document.getElementById('templateTipo').value = tipo;
            }

            // Navegación entre pasos
            function nextStep() {
                if (validateCurrentStep()) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        showStep(currentStep);
                        if (currentStep === 4) {
                            generarResumen();
                        }
                    }
                }
            }

            function previousStep() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            }

            function showStep(step) {
                // Ocultar todos los pasos
                for (let i = 1; i <= totalSteps; i++) {
                    document.getElementById('stepContent' + i).style.display = 'none';
                    document.getElementById('wizardStep' + i).classList.remove('active', 'completed');
                    if (i < step) {
                        document.getElementById('wizardStep' + i).classList.add('completed');
                    }
                }

                // Mostrar paso actual
                document.getElementById('stepContent' + step).style.display = 'block';
                document.getElementById('wizardStep' + step).classList.add('active');

                // Actualizar botones
                document.getElementById('prevBtn').style.display = step > 1 ? 'inline-block' : 'none';

                if (step === totalSteps) {
                    document.getElementById('nextBtn').innerHTML = '<i class="fas fa-check me-2"></i>Finalizar';
                    document.getElementById('nextBtn').onclick = guardarEncuesta;
                } else {
                    document.getElementById('nextBtn').innerHTML = 'Siguiente<i class="fas fa-arrow-right ms-2"></i>';
                    document.getElementById('nextBtn').onclick = nextStep;
                }
            }

            function validateCurrentStep() {
                switch (currentStep) {
                    case 1:
                        if (!document.getElementById('templateTipo').value) {
                            Swal.fire('Atención', 'Por favor seleccione una plantilla', 'warning');
                            return false;
                        }
                        break;
                    case 2:
                        if (!document.getElementById('nombre').value) {
                            Swal.fire('Atención', 'Por favor ingrese el nombre de la encuesta', 'warning');
                            return false;
                        }
                        break;
                    case 3:
                        if (preguntaCount === 0) {
                            Swal.fire('Atención', 'Debe agregar al menos una pregunta', 'warning');
                            return false;
                        }
                        break;
                }
                return true;
            }

            // Agregar pregunta
            function agregarPregunta() {
                preguntaCount++;
                document.getElementById('noPreguntasMsg').style.display = 'none';

                const preguntaHtml = `
                    <div class="question-builder" id="pregunta${preguntaCount}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-question me-2"></i>Pregunta ${preguntaCount}</h6>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarPregunta(${preguntaCount})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Texto de la pregunta</label>
                            <textarea class="form-control" name="preguntas[${preguntaCount}][texto]" rows="2" required
                                      placeholder="Escriba aquí el texto de la pregunta..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de pregunta</label>
                                    <select class="form-select" name="preguntas[${preguntaCount}][tipo]" 
                                            onchange="toggleQuestionOptions(${preguntaCount}, this.value)" required>
                                        <option value="">Seleccione...</option>
                                        <option value="opcion_multiple">Opción Múltiple</option>
                                        <option value="seleccion_multiple">Selección Múltiple</option>
                                        <option value="escala_likert">Escala Likert</option>
                                        <option value="respuesta_abierta">Respuesta Abierta</option>
                                        <option value="si_no">Sí/No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">¿Requerida?</label>
                                    <select class="form-select" name="preguntas[${preguntaCount}][requerida]">
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="question-options" id="options${preguntaCount}">
                            <label class="form-label">Opciones de respuesta</label>
                            <div id="optionsContainer${preguntaCount}"></div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="agregarOpcion(${preguntaCount})">
                                <i class="fas fa-plus me-1"></i>Agregar opción
                            </button>
                        </div>
                    </div>
                `;

                document.getElementById('preguntasContainer').insertAdjacentHTML('beforeend', preguntaHtml);
            }

            function eliminarPregunta(id) {
                document.getElementById('pregunta' + id).remove();
                preguntaCount--;
                if (preguntaCount === 0) {
                    document.getElementById('noPreguntasMsg').style.display = 'block';
                }
            }

            function toggleQuestionOptions(preguntaId, tipo) {
                const optionsDiv = document.getElementById('options' + preguntaId);
                const optionsContainer = document.getElementById('optionsContainer' + preguntaId);

                optionsContainer.innerHTML = '';

                if (tipo === 'opcion_multiple' || tipo === 'seleccion_multiple') {
                    optionsDiv.classList.add('show');
                    for (let i = 1; i <= 3; i++) {
                        optionsContainer.insertAdjacentHTML('beforeend', `
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="preguntas[${preguntaId}][opciones][]" placeholder="Opción ${i}">
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `);
                    }
                } else {
                    optionsDiv.classList.remove('show');
                }
            }

            function agregarOpcion(preguntaId) {
                const container = document.getElementById('optionsContainer' + preguntaId);
                const optionCount = container.children.length + 1;
                container.insertAdjacentHTML('beforeend', `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="preguntas[${preguntaId}][opciones][]" placeholder="Opción ${optionCount}">
                        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
            }

            // Generar resumen
            function generarResumen() {
                const form = document.getElementById('encuestaForm');
                const formData = new FormData(form);

                let resumenHtml = '<div class="mb-3">';
                resumenHtml += `<h5>${formData.get('nombre') || 'Sin nombre'}</h5>`;
                resumenHtml += `<p class="text-muted">${formData.get('descripcion') || 'Sin descripción'}</p>`;
                resumenHtml += '</div>';

                resumenHtml += '<h6>Configuración:</h6><ul>';
                resumenHtml += `<li><strong>Plantilla:</strong> ${formData.get('template_tipo') || 'No seleccionada'}</li>`;
                resumenHtml += `<li><strong>Anónima:</strong> ${formData.get('anonima') === '1' ? 'Sí' : 'No'}</li>`;
                if (formData.get('fecha_inicio')) {
                    resumenHtml += `<li><strong>Fecha inicio:</strong> ${formData.get('fecha_inicio')}</li>`;
                }
                if (formData.get('fecha_cierre')) {
                    resumenHtml += `<li><strong>Fecha cierre:</strong> ${formData.get('fecha_cierre')}</li>`;
                }
                resumenHtml += '</ul>';

                document.getElementById('resumenEncuesta').innerHTML = resumenHtml;
                document.getElementById('totalPreguntas').textContent = preguntaCount;
                document.getElementById('tiempoEstimado').textContent = Math.ceil(preguntaCount * 1.5) + ' min';
                document.getElementById('tipoEncuesta').textContent = formData.get('template_tipo') || '-';
            }

            // Guardar encuesta
            function guardarEncuesta() {
                const form = document.getElementById('encuestaForm');
                const formData = new FormData(form);

                Swal.fire({
                    title: 'Guardando encuesta...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('<?php echo e(route('gestion-instrumentos.encuestas.store')); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Encuesta creada!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'Ver encuestas'
                            }).then(() => {
                                window.location.href = "<?php echo e(route('gestion-instrumentos.encuestas.index')); ?>";
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'No se pudo crear la encuesta',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al guardar la encuesta',
                            icon: 'error'
                        });
                    });
            }

            // Event listeners
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('nombre').addEventListener('input', function() {
                    document.getElementById('previewNombre').textContent = this.value ||
                    'Nombre de la encuesta';
                });

                document.getElementById('descripcion').addEventListener('input', function() {
                    document.getElementById('previewDescripcion').textContent = this.value ||
                        'La descripción aparecerá aquí';
                });

                document.getElementById('fecha_inicio').addEventListener('change', updateFechas);
                document.getElementById('fecha_cierre').addEventListener('change', updateFechas);
            });

            function updateFechas() {
                const inicio = document.getElementById('fecha_inicio').value;
                const cierre = document.getElementById('fecha_cierre').value;

                let fechasText = 'Fechas no definidas';
                if (inicio && cierre) {
                    fechasText = `${inicio} al ${cierre}`;
                } else if (inicio) {
                    fechasText = `Desde ${inicio}`;
                } else if (cierre) {
                    fechasText = `Hasta ${cierre}`;
                }

                document.getElementById('previewFechas').textContent = fechasText;
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-instrumentos/encuestas/create.blade.php ENDPATH**/ ?>