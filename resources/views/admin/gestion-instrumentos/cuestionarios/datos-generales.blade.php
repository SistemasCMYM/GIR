@extends('layouts.dashboard')

@section('title', 'Ficha de Datos Generales')

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
                    <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}">
                        <i class="fas fa-file-alt"></i> Cuestionarios
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-id-card"></i> Ficha de Datos Generales
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-id-card text-primary"></i>
                            Ficha de Datos Generales
                        </h1>
                        <p class="text-muted mb-0">
                            Información sociodemográfica y laboral básica del empleado (19 ítems)
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('gestion-instrumentos.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-clipboard-list"></i> Volver al Módulo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instrucciones -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info border-0">
                    <h6><i class="fas fa-info-circle"></i> Instrucciones</h6>
                    <p class="mb-0">Las siguientes son algunas preguntas que se refieren a información general de usted o
                        su ocupación. Por favor seleccione una sola respuesta para cada pregunta y márquela o escríbala en
                        la casilla. Escriba con letra clara y legible.</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Datos Generales -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> Información del Empleado
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="datosGeneralesForm">
                            @csrf

                            <!-- Campo oculto para employee_id -->
                            <input type="hidden" id="employee_id" name="employee_id"
                                value="{{ session('user_data.employee_id') ?? 'EMP_001' }}">

                            <!-- Pregunta 1: Nombre completo -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="nombre_completo" class="form-label">
                                        <strong>1. Nombre completo:</strong>
                                    </label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                                        value="{{ old('nombre_completo', ($empleado->primerNombre ?? '') . ' ' . ($empleado->segundoNombre ?? '') . ' ' . ($empleado->primerApellido ?? '') . ' ' . ($empleado->segundoApellido ?? '')) }}"
                                        required>
                                    <div class="form-text">Los datos se cargan automáticamente desde su perfil. Puede
                                        modificarlos si es necesario.</div>
                                </div>
                            </div>

                            <!-- Pregunta 2: Sexo -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>2. Sexo:</strong></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sexo" id="sexo_femenino"
                                                value="femenino" required>
                                            <label class="form-check-label" for="sexo_femenino">Femenino</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sexo"
                                                id="sexo_masculino" value="masculino" required>
                                            <label class="form-check-label" for="sexo_masculino">Masculino</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 3: Año de nacimiento -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="ano_nacimiento" class="form-label">
                                        <strong>3. Año de nacimiento:</strong>
                                    </label>
                                    <input type="number" class="form-control" id="ano_nacimiento" name="ano_nacimiento"
                                        min="1940" max="{{ date('Y') - 16 }}" placeholder="Ej: 1985" required>
                                    <div class="form-text">Solo ingrese el año de nacimiento</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Edad actual:</label>
                                    <div class="alert alert-light mb-0" id="edad_calculada">
                                        <span id="edad_texto">La edad se calculará automáticamente</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 4: Estado civil -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="estado_civil" class="form-label">
                                        <strong>4. Estado civil:</strong>
                                    </label>
                                    <select class="form-select" id="estado_civil" name="estado_civil" required>
                                        <option value="">Seleccione...</option>
                                        <option value="soltero">Soltero(a)</option>
                                        <option value="casado">Casado(a)</option>
                                        <option value="union_libre">Unión libre</option>
                                        <option value="separado">Separado(a)</option>
                                        <option value="divorciado">Divorciado(a)</option>
                                        <option value="viudo">Viudo(a)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pregunta 5: Último nivel de estudios -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="nivel_estudios" class="form-label">
                                        <strong>5. Último nivel de estudios que alcanzó (marque una sola opción):</strong>
                                    </label>
                                    <select class="form-select" id="nivel_estudios" name="nivel_estudios" required>
                                        <option value="">Seleccione...</option>
                                        <option value="sin_estudios">Sin estudios</option>
                                        <option value="primaria_incompleta">Primaria incompleta</option>
                                        <option value="primaria_completa">Primaria completa</option>
                                        <option value="bachillerato_incompleto">Bachillerato incompleto</option>
                                        <option value="bachillerato_completo">Bachillerato completo</option>
                                        <option value="tecnico">Técnico</option>
                                        <option value="tecnologo">Tecnólogo</option>
                                        <option value="profesional">Profesional</option>
                                        <option value="especializacion">Especialización</option>
                                        <option value="maestria">Maestría</option>
                                        <option value="doctorado">Doctorado</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pregunta 6: Ocupación o profesión -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="ocupacion_profesion" class="form-label">
                                        <strong>6. ¿Cuál es su ocupación o profesión?</strong>
                                    </label>
                                    <input type="text" class="form-control" id="ocupacion_profesion"
                                        name="ocupacion_profesion" placeholder="Describa su ocupación o profesión"
                                        required>
                                </div>
                            </div>

                            <!-- Pregunta 7: Lugar de residencia actual -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">
                                        <strong>7. Lugar de residencia actual:</strong>
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="departamento_residencia" class="form-label">Departamento:</label>
                                            <select class="form-select" id="departamento_residencia"
                                                name="departamento_residencia" required>
                                                <option value="">Seleccione departamento...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="ciudad_residencia" class="form-label">Ciudad/Municipio:</label>
                                            <select class="form-select" id="ciudad_residencia" name="ciudad_residencia"
                                                required>
                                                <option value="">Seleccione ciudad...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 8: Estrato de servicios públicos -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="estrato_servicios" class="form-label">
                                        <strong>8. Seleccione y marque el estrato de los servicios públicos de su
                                            vivienda:</strong>
                                    </label>
                                    <select class="form-select" id="estrato_servicios" name="estrato_servicios" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1">Estrato 1</option>
                                        <option value="2">Estrato 2</option>
                                        <option value="3">Estrato 3</option>
                                        <option value="4">Estrato 4</option>
                                        <option value="5">Estrato 5</option>
                                        <option value="6">Estrato 6</option>
                                        <option value="finca">Finca</option>
                                        <option value="no_se">No sé</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pregunta 9: Tipo de vivienda -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tipo_vivienda" class="form-label">
                                        <strong>9. Tipo de vivienda:</strong>
                                    </label>
                                    <select class="form-select" id="tipo_vivienda" name="tipo_vivienda" required>
                                        <option value="">Seleccione...</option>
                                        <option value="propia">Propia</option>
                                        <option value="familiar">Familiar</option>
                                        <option value="arriendo">En arriendo</option>
                                        <option value="otra">Otra</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pregunta 10: Número de personas que dependen económicamente -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="personas_dependen" class="form-label">
                                        <strong>10. Número de personas que dependen económicamente de usted (aunque vivan en
                                            otro lugar):</strong>
                                    </label>
                                    <input type="number" class="form-control" id="personas_dependen"
                                        name="personas_dependen" min="0" max="20" placeholder="0" required>
                                </div>
                            </div>

                            <!-- Pregunta 11: Lugar donde trabaja actualmente -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">
                                        <strong>11. Lugar donde trabaja actualmente:</strong>
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="departamento_trabajo" class="form-label">Departamento:</label>
                                            <select class="form-select" id="departamento_trabajo"
                                                name="departamento_trabajo" required>
                                                <option value="">Seleccione departamento...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="ciudad_trabajo" class="form-label">Ciudad/Municipio:</label>
                                            <select class="form-select" id="ciudad_trabajo" name="ciudad_trabajo"
                                                required>
                                                <option value="">Seleccione ciudad...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 12: Años trabajando en esta empresa -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">
                                        <strong>12. ¿Hace cuántos años que trabaja en esta empresa?</strong>
                                    </label>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="anos_empresa_tipo"
                                                id="anos_empresa_menos_ano" value="menos_ano" required>
                                            <label class="form-check-label" for="anos_empresa_menos_ano">
                                                Llevo menos de un año
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="anos_empresa_tipo"
                                                id="anos_empresa_mas_ano" value="mas_ano" required>
                                            <label class="form-check-label" for="anos_empresa_mas_ano">
                                                Llevo más de un año
                                            </label>
                                        </div>
                                    </div>
                                    <div id="anos_empresa_input" style="display: none;">
                                        <label for="anos_empresa_cantidad" class="form-label">Anote cuántos años:</label>
                                        <input type="number" class="form-control" id="anos_empresa_cantidad"
                                            name="anos_empresa_cantidad" min="1" max="50" step="1"
                                            placeholder="Ej: 5">
                                        <div class="form-text">Escriba el número de años completos</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 13: Nombre del cargo -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="nombre_cargo" class="form-label">
                                        <strong>13. ¿Cuál es el nombre del cargo que ocupa en la empresa?</strong>
                                    </label>
                                    <input type="text" class="form-control" id="nombre_cargo" name="nombre_cargo"
                                        placeholder="Escriba el nombre exacto de su cargo" required>
                                </div>
                            </div>

                            <!-- Pregunta 14: Tipo de cargo -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="tipo_cargo" class="form-label">
                                        <strong>14. Seleccione el tipo de cargo que más se parece al que usted desempeña y
                                            señálelo en el cuadro correspondiente de la derecha. Si tiene dudas pida apoyo a
                                            la persona que le entregó este cuestionario:</strong>
                                    </label>
                                    <select class="form-select" id="tipo_cargo" name="tipo_cargo" required>
                                        <option value="">Seleccione...</option>
                                        <option value="jefatura">Jefatura - tiene personal a cargo</option>
                                        <option value="profesional">Profesional - analista - especialista - coordinador
                                        </option>
                                        <option value="auxiliar">Auxiliar - asistente - administrativo</option>
                                        <option value="operario">Operario - operador - técnico</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pregunta 15: Años en el cargo actual -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">
                                        <strong>15. ¿Hace cuántos años que desempeña el cargo u oficio actual en esta
                                            empresa?</strong>
                                    </label>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="anos_cargo_tipo"
                                                id="anos_cargo_menos_ano" value="menos_ano" required>
                                            <label class="form-check-label" for="anos_cargo_menos_ano">
                                                Llevo menos de un año
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="anos_cargo_tipo"
                                                id="anos_cargo_mas_ano" value="mas_ano" required>
                                            <label class="form-check-label" for="anos_cargo_mas_ano">
                                                Llevo más de un año
                                            </label>
                                        </div>
                                    </div>
                                    <div id="anos_cargo_input" style="display: none;">
                                        <label for="anos_cargo_cantidad" class="form-label">Anote cuántos años:</label>
                                        <input type="number" class="form-control" id="anos_cargo_cantidad"
                                            name="anos_cargo_cantidad" min="1" max="50" step="1"
                                            placeholder="Ej: 3">
                                        <div class="form-text">Escriba el número de años completos</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 16: Departamento/área donde trabaja -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="departamento_area" class="form-label">
                                        <strong>16. Escriba el nombre del departamento, área o sección de la empresa en el
                                            que trabaja:</strong>
                                    </label>
                                    <input type="text" class="form-control" id="departamento_area"
                                        name="departamento_area"
                                        placeholder="Ej: Recursos Humanos, Producción, Ventas, etc." required>
                                </div>
                            </div>

                            <!-- Pregunta 17: Tipo de contrato -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="tipo_contrato" class="form-label">
                                        <strong>17. Seleccione el tipo de contrato que tiene actualmente (marque una sola
                                            opción):</strong>
                                    </label>
                                    <select class="form-select" id="tipo_contrato" name="tipo_contrato" required>
                                        <option value="">Seleccione...</option>
                                        <option value="indefinido">Indefinido</option>
                                        <option value="fijo">A término fijo</option>
                                        <option value="cooperativa">Cooperativa</option>
                                        <option value="temporal">Temporal</option>
                                        <option value="prestacion_servicios">Prestación de servicios</option>
                                        <option value="no_se">No sé</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pregunta 18: Horas diarias de trabajo -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="horas_trabajo_dia" class="form-label">
                                        <strong>18. Indique cuántas horas diarias de trabajo están establecidas
                                            habitualmente por la empresa para su cargo:</strong>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="horas_trabajo_dia"
                                            name="horas_trabajo_dia" min="1" max="24" placeholder="12"
                                            required>
                                        <span class="input-group-text">horas de trabajo al día</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pregunta 19: Tipo de salario -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="tipo_salario" class="form-label">
                                        <strong>19. Seleccione y marque el tipo de salario que recibe (marque una sola
                                            opción):</strong>
                                    </label>
                                    <select class="form-select" id="tipo_salario" name="tipo_salario" required>
                                        <option value="">Seleccione...</option>
                                        <option value="fijo">Salario fijo</option>
                                        <option value="variable">Una parte fija y otra variable</option>
                                        <option value="comision">Todo variable (comisión o destajo)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('gestion-instrumentos.cuestionarios.index') }}"
                                            class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Volver a Cuestionarios
                                        </a>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary"
                                                onclick="guardarBorrador()">
                                                <i class="fas fa-save"></i> Guardar Borrador
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check"></i> Completar Ficha
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información sobre la Ficha de Datos Generales</h6>
                    <ul class="mb-0">
                        <li>Esta ficha contiene exactamente <strong>19 preguntas</strong> de información básica
                            sociodemográfica y laboral.</li>
                        <li>Los datos se almacenan en la base de datos <code>psicosocial</code>, colección
                            <code>datos</code>.
                        </li>
                        <li>Es requisito completar esta ficha antes de proceder con otros cuestionarios de evaluación.</li>
                        <li>La estructura sigue exactamente el manual oficial de la batería de instrumentos.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos geográficos de Colombia (todos los departamentos y municipios)
        const datosGeograficos = {
            'Amazonas': ['Leticia', 'Puerto Nariño', 'La Chorrera', 'Tarapacá', 'La Pedrera', 'Mirití-Paraná',
                'Puerto Arica', 'Puerto Santander', 'El Encanto'
            ],
            'Antioquia': ['Medellín', 'Bello', 'Itagüí', 'Envigado', 'Apartadó', 'Turbo', 'Rionegro', 'Caucasia',
                'Necoclí', 'Chigorodó', 'Carepa', 'Sabaneta', 'La Estrella', 'Copacabana', 'Girardota', 'Barbosa',
                'Caldas', 'Retiro', 'Guarne', 'Carmen de Viboral', 'Marinilla', 'Santuario', 'Concepción',
                'Alejandría', 'Santo Domingo', 'Cisneros', 'Yolombó', 'Remedios', 'Segovia', 'Amalfi', 'Vegachí',
                'Yalí', 'Maceo', 'Puerto Berrío', 'Puerto Nare', 'Puerto Triunfo', 'Cocorná', 'Granada',
                'San Carlos', 'San Rafael', 'San Francisco', 'San Luis', 'Sonsón', 'Nariño', 'Argelia', 'Abejorral',
                'La Ceja', 'La Unión', 'Fredonia', 'Venecia', 'Tarso', 'Jericó', 'Andes', 'Betania',
                'Ciudad Bolívar', 'Hispania', 'Salgar', 'Támesis', 'Valparaíso', 'Caramanta', 'Santa Bárbara',
                'Montebello', 'Angelópolis', 'Titiribí', 'Amagá', 'Concordia', 'Betulia', 'Urrao', 'Buriticá',
                'Peque', 'Sabanalarga', 'Liborina', 'Olaya', 'Sopetrán', 'Santa Fe de Antioquia', 'San Jerónimo',
                'Ebéjico', 'Heliconia', 'Anzá', 'Armenia', 'Cañasgordas', 'Abriaquí', 'Frontino', 'Dabeiba',
                'Uramita', 'Giraldo', 'Vigía del Fuerte', 'Murindó', 'Mutatá'
            ],
            'Arauca': ['Arauca', 'Arauquita', 'Cravo Norte', 'Fortul', 'Puerto Rondón', 'Saravena', 'Tame'],
            'Atlántico': ['Barranquilla', 'Soledad', 'Malambo', 'Sabanalarga', 'Puerto Colombia', 'Galapa', 'Polonuevo',
                'Ponedera', 'Tubará', 'Usiacurí', 'Baranoa', 'Sabanagrande', 'Santo Tomás', 'Palmar de Varela',
                'Candelaria', 'Manatí', 'Repelón', 'Luruaco', 'Piojó', 'Juan de Acosta', 'Suan', 'Campo de la Cruz',
                'Santa Lucía'
            ],
            'Bogotá D.C.': ['Bogotá D.C.'],
            'Bolívar': ['Cartagena', 'Magangué', 'Turbaco', 'El Carmen de Bolívar', 'San Pablo', 'Santa Rosa del Sur',
                'Simití', 'Arenal', 'Morales', 'Cantagallo', 'San Martín de Loba', 'Hatillo de Loba',
                'Barranco de Loba', 'Altos del Rosario', 'Cicuco', 'Talaigua Nuevo', 'Mompós', 'San Fernando',
                'Margarita', 'San Jacinto del Cauca', 'Tiquisio', 'Achí', 'Montecristo', 'Pinillos', 'Santa Rosa',
                'San Jacinto', 'Córdoba', 'Clemencia', 'Santa Catalina', 'Soplaviento', 'Luruaco', 'Mahates',
                'San Cristóbal', 'San Estanislao', 'Villanueva', 'Arjona', 'Calamar', 'El Guamo',
                'San Juan Nepomuceno', 'Zambrano', 'María la Baja', 'San Onofre', 'Tolú Viejo'
            ],
            'Boyacá': ['Tunja', 'Duitama', 'Sogamoso', 'Chiquinquirá', 'Puerto Boyacá', 'Villa de Leyva', 'Paipa',
                'Nobsa', 'Tibasosa', 'Firavitoba', 'Iza', 'Pesca', 'Tópaga', 'Mongua', 'Gámeza', 'Tasco',
                'Betéitiva', 'Busbanzá', 'Corrales', 'Floresta', 'Tutazá', 'Belén', 'Cerinza', 'Encino',
                'Paz de Río', 'Socotá', 'Socha', 'Jericó', 'Boavita', 'La Uvita', 'San Mateo', 'Sativanorte',
                'Sativasur', 'Susacón', 'Tipacoque', 'Covarachía', 'Güicán', 'El Cocuy', 'Chiscas', 'El Espino',
                'Guacamayas', 'Panqueba', 'Chita', 'Soatá', 'Capitanejo', 'Maripí', 'Otanche',
                'San Pablo de Borbur', 'Caldas', 'Briceño', 'Boyacá', 'Cómbita', 'Cucaita', 'Chíquiza', 'Chivatá',
                'Motavita', 'Oicatá', 'Samacá', 'Siachoque', 'Sora', 'Soracá', 'Sotaquirá', 'Toca', 'Tuta',
                'Ventaquemada', 'Nuevo Colón', 'Tibaná', 'Jenesano', 'Rondón', 'Ciénega', 'Úmbita', 'Ráquira',
                'Sáchica', 'Sutamarchán', 'Tinjacá', 'Gachantivá', 'Moniquirá', 'Barbosa', 'Santana',
                'San José de Pare', 'Togüí', 'Guateque', 'Sutatenza', 'Tenza', 'La Capilla', 'Pachavita', 'Garagoa',
                'Macanal', 'San Luis de Gaceno', 'Santa María', 'Chivor', 'Almeida', 'Somondoco', 'Guayatá',
                'Miraflores', 'Zetaquira', 'Páez', 'Campohermoso', 'Pisba', 'Paya', 'Labranzagrande', 'Aquitania',
                'Cuítiva', 'Tota', 'Monguí'
            ],
            'Caldas': ['Manizales', 'Chinchiná', 'Villamaría', 'Palestina', 'La Dorada', 'Anserma', 'Riosucio', 'Supía',
                'Marmato', 'Aguadas', 'Pácora', 'Salamina', 'Aranzazu', 'Filadelfia', 'La Merced', 'Risaralda',
                'San José', 'Belalcázar', 'Viterbo', 'Pensilvania', 'Marquetalia', 'Manzanares', 'Marulanda',
                'Victoria', 'Samaná', 'Norcasia', 'Neira'
            ],
            'Caquetá': ['Florencia', 'San Vicente del Caguán', 'Puerto Rico', 'El Doncello', 'Paujil', 'La Montañita',
                'Morelia', 'Belén de los Andaquíes', 'Albania', 'Curillo', 'El Paujil', 'Milán', 'Solano', 'Solita',
                'Valparaíso', 'Cartagena del Chairá'
            ],
            'Casanare': ['Yopal', 'Aguazul', 'Villanueva', 'Tauramena', 'Monterrey', 'Sabanalarga', 'Recetor',
                'Chameza', 'Sacama', 'La Salina', 'Orocué', 'San Luis de Palenque', 'Trinidad', 'Hato Corozal',
                'Paz de Ariporo', 'Pore', 'Támara', 'Nunchía', 'Maní'
            ],
            'Cauca': ['Popayán', 'Santander de Quilichao', 'Puerto Tejada', 'Guapi', 'Timbiquí', 'López de Micay',
                'Patía', 'Balboa', 'Argelia', 'El Tambo', 'La Sierra', 'La Vega', 'Rosas', 'Sucre', 'Mercaderes',
                'Florencia', 'Bolívar', 'Almaguer', 'San Sebastián', 'Santa Rosa', 'Sotará', 'Timbío', 'Cajibío',
                'Piendamó', 'Morales', 'Caldono', 'Jambaló', 'Toribío', 'Caloto', 'Corinto', 'Miranda', 'Padilla',
                'Villa Rica', 'Páez', 'Inzá', 'Belalcázar', 'Silvia', 'Totoró', 'Puracé'
            ],
            'Cesar': ['Valledupar', 'Aguachica', 'Codazzi', 'La Paz', 'San Diego', 'La Jagua de Ibirico', 'Chiriguaná',
                'Curumaní', 'El Paso', 'Astrea', 'Bosconia', 'El Copey', 'González', 'La Gloria', 'Manaure',
                'Pailitas', 'Pelaya', 'Pueblo Bello', 'Río de Oro', 'San Alberto', 'San Martín', 'Tamalameque'
            ],
            'Chocó': ['Quibdó', 'Istmina', 'Condoto', 'Tadó', 'Riosucio', 'Acandí', 'Unguía', 'Turbo', 'Necoclí',
                'San Juan de Urabá', 'Bojayá', 'Vigía del Fuerte', 'Bagadó', 'Bahía Solano', 'Juradó', 'Nuquí',
                'El Valle', 'Lloró', 'Atrato', 'Certegui', 'Unión Panamericana', 'Río Quito',
                'El Cantón del San Pablo', 'San José del Palmar', 'Sipí', 'Nóvita', 'Medio Baudó', 'Alto Baudó',
                'Bajo Baudó', 'Litoral del San Juan'
            ],
            'Córdoba': ['Montería', 'Cereté', 'Sahagún', 'Lorica', 'Planeta Rica', 'Montelíbano', 'Tierralta',
                'Valencia', 'Ciénaga de Oro', 'San Carlos', 'Chinú', 'San Andrés Sotavento', 'Tuchín', 'Momil',
                'Purísima', 'Cotorra', 'San Pelayo', 'Ayapel', 'Buenavista', 'Pueblo Nuevo', 'Canalete',
                'Los Córdobas', 'Puerto Escondido', 'Moñitos', 'San Bernardo del Viento', 'San Antero', 'Chimá'
            ],
            'Cundinamarca': ['Soacha', 'Girardot', 'Zipaquirá', 'Facatativá', 'Chía', 'Mosquera', 'Fusagasugá',
                'Madrid', 'Funza', 'Cajicá', 'Sibaté', 'Tocancipá', 'Cota', 'La Calera', 'Sopó', 'Tabio', 'Tenjo',
                'Subachoque', 'El Rosal', 'Bojacá', 'Zipacón', 'Cachipay', 'La Mesa', 'Anapoima', 'Anolaima',
                'Villeta', 'Sasaima', 'Supatá', 'La Peña', 'La Palma', 'Yacopí', 'Caparrapí', 'Guaduas',
                'Puerto Salgar', 'Albán', 'Vianí', 'Vergara', 'Nocaima', 'Nimaima', 'Quebradanegra', 'Beltrán',
                'Bituima', 'Chaguaní', 'Guayabal de Síquima', 'Pulí', 'San Juan de Río Seco', 'Viotá', 'Ricaurte',
                'Agua de Dios', 'Nilo', 'Tocaima', 'Jerusalén', 'Nariño', 'Venecia', 'Guataquí', 'Tibacuy',
                'Silvania', 'Granada', 'San Bernardo', 'Pandi', 'Cabrera', 'Gutierrez', 'Arbeláez',
                'San Antonio del Tequendama', 'Tena', 'Apulo', 'Viota', 'El Colegio', 'Quipile', 'Pasca', 'Ubaque',
                'Chipaque', 'Une', 'Fosca', 'Cáqueza', 'Quetame', 'Guayabetal', 'Junín', 'Gachalá', 'Gachetá',
                'Ubalá', 'Gama', 'Sesquilé', 'Suesca', 'Cucunubá', 'Lenguazaque', 'Guachetá', 'Carmen de Carupa',
                'Tausa', 'Cogua', 'Nemocón', 'Gachancipá', 'Guasca', 'Guatavita', 'Machetá', 'Manta', 'Tibirita',
                'Villapinzón', 'Chocontá'
            ],
            'Guainía': ['Inírida', 'Barranco Minas', 'Mapiripana', 'San Felipe', 'Puerto Colombia', 'La Guadalupe',
                'Cacahual', 'Pana Pana', 'Morichal'
            ],
            'Guaviare': ['San José del Guaviare', 'Calamar', 'El Retorno', 'Miraflores'],
            'Huila': ['Neiva', 'Pitalito', 'Garzón', 'La Plata', 'Campoalegre', 'Gigante', 'Guadalupe', 'Hobo',
                'Iquira', 'Nátaga', 'Oporapa', 'Paicol', 'Palermo', 'Rivera', 'Saladoblanco', 'San Agustín',
                'Santa María', 'Tarqui', 'Tesalia', 'Tello', 'Teruel', 'Timaná', 'Villavieja', 'Yaguará', 'Baraya',
                'Colombia', 'Elías', 'Altamira', 'Íquira', 'Algeciras', 'Acevedo', 'Aipe', 'Suaza', 'Agrado',
                'Palestina', 'Pital'
            ],
            'La Guajira': ['Riohacha', 'Maicao', 'Uribia', 'Manaure', 'Albania', 'Barrancas', 'Dibulla', 'Distracción',
                'El Molino', 'Fonseca', 'Hatonuevo', 'La Jagua del Pilar', 'San Juan del Cesar', 'Urumita',
                'Villanueva'
            ],
            'Magdalena': ['Santa Marta', 'Ciénaga', 'Fundación', 'Aracataca', 'El Banco', 'Plato', 'Tenerife',
                'Zapayán', 'Nueva Granada', 'Pedraza', 'Sabanas de San Ángel', 'San Sebastián de Buenavista',
                'San Zenón', 'Santa Bárbara de Pinto', 'Sitionuevo', 'Remolino', 'Puebloviejo', 'Pivijay',
                'Pijiño del Carmen', 'Palmira', 'Concordia', 'Cerro San Antonio', 'Algarrobo', 'Ariguaní',
                'Chivolo', 'Zona Bananera', 'El Retén', 'Salamina'
            ],
            'Meta': ['Villavicencio', 'Acacías', 'Granada', 'San Martín', 'Puerto López', 'Cumaral', 'Restrepo',
                'El Calvario', 'San Carlos de Guaroa', 'Castilla la Nueva', 'Cabuyaro', 'Puerto Gaitán',
                'San Juan de Arama', 'Lejanías', 'Mesetas', 'Uribe', 'La Macarena', 'Puerto Concordia',
                'Puerto Lleras', 'Puerto Rico', 'Mapiripán', 'Barranca de Upía', 'Fuente de Oro', 'El Dorado',
                'Guamal', 'El Castillo', 'Vistahermosa'
            ],
            'Nariño': ['Pasto', 'Tumaco', 'Ipiales', 'Túquerres', 'Samaniego', 'La Unión', 'Sandona', 'Consacá',
                'Nariño', 'Yacuanquer', 'Tangua', 'Funes', 'Pupiales', 'Gualmatán', 'Contadero', 'Cuaspud',
                'Aldana', 'Córdoba', 'Potosí', 'Puerres', 'Cumbal', 'Ricaurte', 'Mallama', 'Providencia', 'Sapuyes',
                'Guachucal', 'Iles', 'Carlosama', 'Colón', 'San Bernardo', 'Belén', 'San Pablo', 'Arboleda',
                'Buesaco', 'Chachagüí', 'El Peñol', 'El Rosario', 'El Tablón de Gómez', 'La Cruz', 'Leiva',
                'Policarpa', 'Los Andes', 'Magüí', 'Ospina', 'Francisco Pizarro', 'Roberto Payán', 'Barbacoas',
                'Mosquera', 'El Charco', 'La Tola', 'Olaya Herrera', 'Santa Bárbara', 'Cumbitara', 'Linares',
                'Albán', 'San Lorenzo', 'San Pedro de Cartago', 'Taminango', 'Ancuyá', 'Sandoná', 'Imués',
                'Guaitarilla'
            ],
            'Norte de Santander': ['Cúcuta', 'Ocaña', 'Pamplona', 'Villa del Rosario', 'Los Patios', 'Tibú', 'El Zulia',
                'San Cayetano', 'Puerto Santander', 'Villa Caro', 'Convención', 'El Tarra', 'Teorama', 'Hacarí',
                'La Playa', 'Abrego', 'La Esperanza', 'Sardinata', 'Gramalote', 'Arboledas', 'Lourdes', 'Salazar',
                'Santiago', 'Villacaro', 'Ábrego', 'Cachirá', 'Mutiscua', 'Chitagá', 'Silos', 'Cácota', 'Cucutilla',
                'Herrán', 'Ragonvalia', 'Durania', 'Bochalema', 'Chinácota', 'Labateca', 'Toledo', 'Bucarasica'
            ],
            'Putumayo': ['Mocoa', 'Puerto Asís', 'Orito', 'Valle del Guamuéz', 'Puerto Caicedo', 'Puerto Guzmán',
                'Leguízamo', 'Sibundoy', 'San Francisco', 'Santiago', 'Colón', 'San Miguel', 'Villagarzón'
            ],
            'Quindío': ['Armenia', 'Calarcá', 'Montenegro', 'La Tebaida', 'Quimbaya', 'Circasia', 'Filandia', 'Salento',
                'Pijao', 'Córdoba', 'Buenavista', 'Génova'
            ],
            'Risaralda': ['Pereira', 'Dosquebradas', 'Santa Rosa de Cabal', 'La Virginia', 'Marsella',
                'Belén de Umbría', 'Apía', 'Santuario', 'Balboa', 'La Celia', 'Guática', 'Quinchía', 'Mistrató',
                'Pueblo Rico'
            ],
            'San Andrés y Providencia': ['San Andrés', 'Providencia y Santa Catalina'],
            'Santander': ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta', 'Barrancabermeja', 'San Gil',
                'Socorro', 'Málaga', 'Barbosa', 'Vélez', 'Lebrija', 'Rionegro', 'Sabana de Torres',
                'Puerto Wilches', 'Cimitarra', 'El Carmen de Chucurí', 'Simacota', 'Betulia', 'Bolívar',
                'Landázuri', 'Sucre', 'La Belleza', 'Florián', 'Guadalupe', 'Guapotá', 'Puerto Parra', 'Hato',
                'El Peñón', 'La Paz', 'San Vicente de Chucurí', 'Albania', 'Jesús María', 'Puente Nacional',
                'Togüí', 'Gámbita', 'Valle de San José', 'Encino', 'Charalá', 'Coromoro', 'Curití', 'Mogotes',
                'Onzaga', 'San Joaquín', 'Villanueva', 'Cabrera', 'Confines', 'Contratación', 'Galán', 'Gambita',
                'Guapotá', 'Oiba', 'Palmar', 'Palmas del Socorro', 'Páramo', 'Pinchote', 'Santa Helena del Opón',
                'Suaita', 'San Benito', 'Aratoca', 'Capitanejo', 'Carcasí', 'Cerrito', 'Concepción', 'Enciso',
                'Guaca', 'Macaravita', 'Molagavita', 'San Andrés', 'San José de Miranda', 'San Miguel',
                'Santa Bárbara', 'Chipatá', 'El Guacamayo'
            ],
            'Sucre': ['Sincelejo', 'Corozal', 'Sampués', 'San Marcos', 'Majagual', 'Sucre', 'Guaranda', 'Buenavista',
                'Caimito', 'Coloso', 'Chalán', 'El Roble', 'Galeras', 'Los Palmitos', 'Morroa', 'Ovejas', 'Palmito',
                'San Benito Abad', 'San Juan de Betulia', 'San Pedro', 'Santiago de Tolú', 'Tolú Viejo'
            ],
            'Tolima': ['Ibagué', 'Espinal', 'Melgar', 'Honda', 'Líbano', 'Mariquita', 'Purificación', 'Saldaña',
                'Chaparral', 'Ataco', 'Planadas', 'Rioblanco', 'Roncesvalles', 'Rovira', 'San Antonio', 'Alvarado',
                'Ambalema', 'Armero', 'Cajamarca', 'Carmen de Apicalá', 'Casabianca', 'Coello', 'Coyaima', 'Cunday',
                'Dolores', 'Falan', 'Flandes', 'Fresno', 'Guamo', 'Herveo', 'Icononzo', 'Lérida', 'Natagaima',
                'Ortega', 'Palocabildo', 'Piedras', 'Prado', 'Suárez', 'Valle de San Juan', 'Venadillo',
                'Villahermosa', 'Villarrica'
            ],
            'Valle del Cauca': ['Cali', 'Palmira', 'Buenaventura', 'Tuluá', 'Cartago', 'Buga', 'Jamundí', 'Yumbo',
                'Candelaria', 'Florida', 'Pradera', 'Roldanillo', 'La Unión', 'Toro', 'Versalles', 'Andalucía',
                'Ansermanuevo', 'Argelia', 'Bolívar', 'Caicedonia', 'Calima', 'Dagua', 'El Águila', 'El Cairo',
                'El Cerrito', 'El Dovio', 'Ginebra', 'Guacarí', 'La Cumbre', 'La Victoria', 'Obando', 'Restrepo',
                'Río Frío', 'San Pedro', 'Sevilla', 'Ulloa', 'Vijes', 'Yotoco', 'Zarzal'
            ],
            'Vaupés': ['Mitú', 'Carurú', 'Pacoa', 'Taraira', 'Papunahua', 'Yavaraté'],
            'Vichada': ['Puerto Carreño', 'La Primavera', 'Santa Rosalía', 'Cumaribo']
        };

        // Cargar departamentos
        function cargarDepartamentos() {
            const departamentos = Object.keys(datosGeograficos).sort();

            // Cargar departamentos de residencia
            const selectDeptResidencia = document.getElementById('departamento_residencia');
            departamentos.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept;
                option.textContent = dept;
                selectDeptResidencia.appendChild(option);
            });

            // Cargar departamentos de trabajo
            const selectDeptTrabajo = document.getElementById('departamento_trabajo');
            departamentos.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept;
                option.textContent = dept;
                selectDeptTrabajo.appendChild(option);
            });
        }

        // Cargar ciudades según departamento seleccionado
        function cargarCiudades(departamento, selectCiudadId) {
            const selectCiudad = document.getElementById(selectCiudadId);
            selectCiudad.innerHTML = '<option value="">Seleccione ciudad...</option>';

            if (departamento && datosGeograficos[departamento]) {
                const ciudades = datosGeograficos[departamento].sort();
                ciudades.forEach(ciudad => {
                    const option = document.createElement('option');
                    option.value = ciudad;
                    option.textContent = ciudad;
                    selectCiudad.appendChild(option);
                });
            }
        }

        // Calcular edad automáticamente
        function calcularEdad() {
            const anoNacimiento = document.getElementById('ano_nacimiento').value;
            const edadTexto = document.getElementById('edad_texto');

            if (anoNacimiento) {
                const anoActual = new Date().getFullYear();
                const edad = anoActual - parseInt(anoNacimiento);

                if (edad >= 0 && edad <= 100) {
                    edadTexto.textContent = `${edad} años`;
                    edadTexto.parentElement.className = 'alert alert-success mb-0';
                } else {
                    edadTexto.textContent = 'Año de nacimiento inválido';
                    edadTexto.parentElement.className = 'alert alert-danger mb-0';
                }
            } else {
                edadTexto.textContent = 'La edad se calculará automáticamente';
                edadTexto.parentElement.className = 'alert alert-light mb-0';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar datos geográficos
            cargarDepartamentos();

            // Event listeners para departamentos
            document.getElementById('departamento_residencia').addEventListener('change', function() {
                cargarCiudades(this.value, 'ciudad_residencia');
            });

            document.getElementById('departamento_trabajo').addEventListener('change', function() {
                cargarCiudades(this.value, 'ciudad_trabajo');
            });

            // Event listener para cálculo de edad
            document.getElementById('ano_nacimiento').addEventListener('input', calcularEdad);

            // Event listeners para ítems 12 y 15 (años de trabajo)
            setupAnosEmpresaListeners();
            setupAnosCargoListeners();
        });

        // Funciones para manejar los campos condicionales del ítem 12
        function setupAnosEmpresaListeners() {
            const radioButtons = document.querySelectorAll('input[name="anos_empresa_tipo"]');
            const inputDiv = document.getElementById('anos_empresa_input');
            const inputField = document.getElementById('anos_empresa_cantidad');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'mas_ano') {
                        inputDiv.style.display = 'block';
                        inputField.required = true;
                    } else {
                        inputDiv.style.display = 'none';
                        inputField.required = false;
                        inputField.value = '';
                    }
                });
            });
        }

        // Funciones para manejar los campos condicionales del ítem 15
        function setupAnosCargoListeners() {
            const radioButtons = document.querySelectorAll('input[name="anos_cargo_tipo"]');
            const inputDiv = document.getElementById('anos_cargo_input');
            const inputField = document.getElementById('anos_cargo_cantidad');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'mas_ano') {
                        inputDiv.style.display = 'block';
                        inputField.required = true;
                    } else {
                        inputDiv.style.display = 'none';
                        inputField.required = false;
                        inputField.value = '';
                    }
                });
            });
        }

        function guardarBorrador() {
            // Recopilar datos del formulario
            const formData = new FormData(document.getElementById('datosGeneralesForm'));
            const datosFormulario = Object.fromEntries(formData);

            // Estructurar datos como borrador según especificaciones de BD
            const borradorParaBD = {
                // Datos básicos del empleado
                employee_id: datosFormulario.employee_id || null,
                nombre_completo: datosFormulario.nombre_completo || '',

                // Mapeo de campos según especificaciones
                genero: datosFormulario.sexo || '',
                ano_nacimiento: parseInt(datosFormulario.ano_nacimiento) || null,
                edad: null, // Se calculará abajo
                estado_civil: datosFormulario.estado_civil || '',
                nivel_estudios: datosFormulario.escolaridad || '',
                profesion: datosFormulario.ocupacion_profesion || '',
                lugar_residencia: {
                    departamento: datosFormulario.departamento_residencia || '',
                    ciudad: datosFormulario.ciudad_residencia || ''
                },
                estrato_social: datosFormulario.estrato_servicios || '',
                tipo_vivienda: datosFormulario.tipo_vivienda || '',
                dependientes_economicos: parseInt(datosFormulario.personas_dependen) || 0,
                lugar_trabajo: {
                    departamento: datosFormulario.departamento_trabajo || '',
                    ciudad: datosFormulario.ciudad_trabajo || ''
                },
                tiempo_laborado: null,
                nombre_cargo: datosFormulario.nombre_cargo || '',
                tipo_cargo: datosFormulario.tipo_cargo || '',
                tiempo_en_cargo: null,
                departamento_cargo: datosFormulario.departamento_area || '',
                tipo_contrato: datosFormulario.tipo_contrato || '',
                horas_laboradas_dia: parseInt(datosFormulario.horas_diarias) || 0,
                tipo_salario: datosFormulario.tipo_salario || '',

                // Metadatos del borrador
                completado: false, // Borrador no completado
                es_borrador: true,
                fecha_borrador: new Date().toISOString()
            };

            // Calcular edad actual
            if (borradorParaBD.ano_nacimiento) {
                const anoActual = new Date().getFullYear();
                borradorParaBD.edad = anoActual - borradorParaBD.ano_nacimiento;
            }

            // Procesar tiempo laborado en empresa (ítem 12)
            if (datosFormulario.anos_empresa_tipo) {
                if (datosFormulario.anos_empresa_tipo === 'menos_ano') {
                    borradorParaBD.tiempo_laborado = 0;
                } else if (datosFormulario.anos_empresa_tipo === 'mas_ano' && datosFormulario.anos_empresa_cantidad) {
                    borradorParaBD.tiempo_laborado = parseInt(datosFormulario.anos_empresa_cantidad);
                }
            }

            // Procesar tiempo en cargo actual (ítem 15)
            if (datosFormulario.anos_cargo_tipo) {
                if (datosFormulario.anos_cargo_tipo === 'menos_ano') {
                    borradorParaBD.tiempo_en_cargo = 0;
                } else if (datosFormulario.anos_cargo_tipo === 'mas_ano' && datosFormulario.anos_cargo_cantidad) {
                    borradorParaBD.tiempo_en_cargo = parseInt(datosFormulario.anos_cargo_cantidad);
                }
            }

            // Log para verificación
            console.log('=== BORRADOR PARA BASE DE DATOS ===');
            console.log('Colección psicosocial.datos (borrador):', borradorParaBD);

            // Enviar borrador a la API
            fetch('{{ route('gestion-instrumentos.api.datos-generales.borrador') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(borradorParaBD)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de confirmación
                        Swal.fire({
                            icon: 'info',
                            title: 'Borrador guardado',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error guardando borrador:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo guardar el borrador: ' + error.message,
                        confirmButtonText: 'Entendido'
                    });
                });
        }

        document.getElementById('datosGeneralesForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validar campos requeridos
            const camposRequeridos = this.querySelectorAll('[required]');
            let todosCompletos = true;

            camposRequeridos.forEach(campo => {
                if (!campo.value.trim()) {
                    campo.classList.add('is-invalid');
                    todosCompletos = false;
                } else {
                    campo.classList.remove('is-invalid');
                }
            });

            if (!todosCompletos) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Por favor complete todos los campos requeridos.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Recopilar datos del formulario
            const formData = new FormData(this);
            const datosFormulario = Object.fromEntries(formData);

            // Estructurar datos según especificaciones de base de datos psicosocial.datos
            const datosParaBD = {
                // Datos básicos del empleado
                employee_id: datosFormulario.employee_id || null, // ID del empleado
                nombre_completo: datosFormulario.nombre_completo || '',

                // Pregunta 2: Sexo/Género
                genero: datosFormulario.sexo || '',

                // Pregunta 3: Año de nacimiento y edad calculada
                ano_nacimiento: parseInt(datosFormulario.ano_nacimiento) || null,
                edad: null, // Se calculará abajo

                // Pregunta 4: Estado civil
                estado_civil: datosFormulario.estado_civil || '',

                // Pregunta 5: Nivel de estudios
                nivel_estudios: datosFormulario.escolaridad || '',

                // Pregunta 6: Profesión/ocupación
                profesion: datosFormulario.ocupacion_profesion || '',

                // Pregunta 7: Lugar de residencia (departamento + ciudad)
                lugar_residencia: {
                    departamento: datosFormulario.departamento_residencia || '',
                    ciudad: datosFormulario.ciudad_residencia || ''
                },

                // Pregunta 8: Estrato social
                estrato_social: datosFormulario.estrato_servicios || '',

                // Pregunta 9: Tipo de vivienda
                tipo_vivienda: datosFormulario.tipo_vivienda || '',

                // Pregunta 10: Dependientes económicos
                dependientes_economicos: parseInt(datosFormulario.personas_dependen) || 0,

                // Pregunta 11: Lugar de trabajo (departamento + ciudad)
                lugar_trabajo: {
                    departamento: datosFormulario.departamento_trabajo || '',
                    ciudad: datosFormulario.ciudad_trabajo || ''
                },

                // Pregunta 12: Tiempo laborado en la empresa
                tiempo_laborado: null, // Se procesará abajo

                // Pregunta 13: Nombre del cargo
                nombre_cargo: datosFormulario.nombre_cargo || '',

                // Pregunta 14: Tipo de cargo
                tipo_cargo: datosFormulario.tipo_cargo || '',

                // Pregunta 15: Tiempo en el cargo actual
                tiempo_en_cargo: null, // Se procesará abajo

                // Pregunta 16: Departamento/área del cargo
                departamento_cargo: datosFormulario.departamento_area || '',

                // Pregunta 17: Tipo de contrato
                tipo_contrato: datosFormulario.tipo_contrato || '',

                // Pregunta 18: Horas laboradas por día
                horas_laboradas_dia: parseInt(datosFormulario.horas_diarias) || 0,

                // Pregunta 19: Tipo de salario
                tipo_salario: datosFormulario.tipo_salario || '',

                // Metadatos del documento
                completado: true, // Se marca como completado al enviar
                fecha_completado: new Date().toISOString(),
                fecha_creacion: new Date().toISOString()
            };

            // Calcular edad actual basada en año de nacimiento
            if (datosParaBD.ano_nacimiento) {
                const anoActual = new Date().getFullYear();
                datosParaBD.edad = anoActual - datosParaBD.ano_nacimiento;
            }

            // Procesar tiempo laborado en empresa (ítem 12)
            if (datosFormulario.anos_empresa_tipo) {
                if (datosFormulario.anos_empresa_tipo === 'menos_ano') {
                    datosParaBD.tiempo_laborado = 0; // Menos de un año
                } else if (datosFormulario.anos_empresa_tipo === 'mas_ano' && datosFormulario
                    .anos_empresa_cantidad) {
                    datosParaBD.tiempo_laborado = parseInt(datosFormulario.anos_empresa_cantidad);
                }
            }

            // Procesar tiempo en cargo actual (ítem 15)
            if (datosFormulario.anos_cargo_tipo) {
                if (datosFormulario.anos_cargo_tipo === 'menos_ano') {
                    datosParaBD.tiempo_en_cargo = 0; // Menos de un año
                } else if (datosFormulario.anos_cargo_tipo === 'mas_ano' && datosFormulario.anos_cargo_cantidad) {
                    datosParaBD.tiempo_en_cargo = parseInt(datosFormulario.anos_cargo_cantidad);
                }
            }

            // Datos para actualización de estado en colección 'hoja'
            const actualizacionHoja = {
                employee_id: datosParaBD.employee_id,
                datos_generales: 'completado',
                fecha_actualizacion: new Date().toISOString()
            };

            // Log de datos estructurados para verificación
            console.log('=== DATOS PARA BASE DE DATOS ===');
            console.log('Colección psicosocial.datos:', datosParaBD);
            console.log('Actualización colección hoja:', actualizacionHoja);

            // Enviar datos completos a la API
            fetch('{{ route('gestion-instrumentos.api.datos-generales.completar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(datosParaBD)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Ficha completada!',
                            text: data.message,
                            confirmButtonText: 'Continuar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirigir a la siguiente etapa o volver al índice
                                window.location.href =
                                    '{{ route('gestion-instrumentos.cuestionarios.index') }}';
                            }
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error completando ficha:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo completar la ficha: ' + error.message,
                        confirmButtonText: 'Entendido'
                    });
                });
        });

        // Validaciones en tiempo real
        document.querySelectorAll('input, select').forEach(field => {
            field.addEventListener('change', function() {
                // Remover clases de error si existen
                this.classList.remove('is-invalid');

                // Validaciones específicas
                if (this.type === 'number' && this.value) {
                    const min = parseFloat(this.min);
                    const max = parseFloat(this.max);
                    const value = parseFloat(this.value);

                    if (value < min || value > max) {
                        this.classList.add('is-invalid');
                    }
                }
            });
        });

        // Función para cargar datos existentes del empleado
        async function cargarDatosExistentes() {
            try {
                const employeeId = document.getElementById('employee_id').value;
                if (!employeeId) {
                    console.log('No hay employee_id para cargar datos');
                    return;
                }

                const response = await fetch(
                    `{{ route('gestion-instrumentos.api.datos-generales.obtener', ':employeeId') }}`.replace(
                        ':employeeId', employeeId), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                const data = await response.json();

                if (data.success && data.data.existe) {
                    console.log('Datos existentes cargados:', data.data);
                    llenarFormularioConDatos(data.data.datos);

                    if (data.data.completado) {
                        // Mostrar mensaje de que la ficha ya está completada
                        Swal.fire({
                            icon: 'info',
                            title: 'Ficha completada',
                            text: 'Esta ficha ya fue completada anteriormente.',
                            showConfirmButton: true
                        });
                    }
                }
            } catch (error) {
                console.error('Error al cargar datos existentes:', error);
            }
        }

        // Función para llenar el formulario con datos existentes
        function llenarFormularioConDatos(datos) {
            // Llenar campos simples
            const camposSimples = [
                'genero', 'ano_nacimiento', 'edad', 'estado_civil', 'nivel_estudios',
                'profesion', 'estrato_social', 'tipo_vivienda', 'dependientes_economicos',
                'nombre_cargo', 'tipo_cargo', 'departamento_cargo', 'tipo_contrato',
                'horas_laboradas_dia', 'tipo_salario'
            ];

            camposSimples.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (elemento && datos[campo] !== undefined) {
                    elemento.value = datos[campo];
                }
            });

            // Llenar campos de ubicación de residencia
            if (datos.residencia_departamento) {
                document.getElementById('residencia_departamento').value = datos.residencia_departamento;
                cargarCiudades(datos.residencia_departamento, 'residencia_ciudad');
                setTimeout(() => {
                    if (datos.residencia_ciudad) {
                        document.getElementById('residencia_ciudad').value = datos.residencia_ciudad;
                    }
                }, 500);
            }

            // Llenar campos de ubicación de trabajo
            if (datos.trabajo_departamento) {
                document.getElementById('trabajo_departamento').value = datos.trabajo_departamento;
                cargarCiudades(datos.trabajo_departamento, 'trabajo_ciudad');
                setTimeout(() => {
                    if (datos.trabajo_ciudad) {
                        document.getElementById('trabajo_ciudad').value = datos.trabajo_ciudad;
                    }
                }, 500);
            }

            // Llenar tiempo laborado
            if (datos.tiempo_laborado_anos !== undefined) {
                document.getElementById('tiempo_laborado_anos').value = datos.tiempo_laborado_anos;
            }
            if (datos.tiempo_laborado_meses !== undefined) {
                document.getElementById('tiempo_laborado_meses').value = datos.tiempo_laborado_meses;
            }

            // Llenar tiempo en cargo
            if (datos.tiempo_cargo_anos !== undefined) {
                document.getElementById('tiempo_cargo_anos').value = datos.tiempo_cargo_anos;
            }
            if (datos.tiempo_cargo_meses !== undefined) {
                document.getElementById('tiempo_cargo_meses').value = datos.tiempo_cargo_meses;
            }
        }

        // Cargar datos al inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarDepartamentos();
            cargarDatosExistentes();
        });
    </script>

    <style>
        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .border-bottom {
            border-bottom: 2px solid #e9ecef !important;
        }

        .alert {
            border: none;
            border-left: 4px solid #0dcaf0;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-group .btn {
            margin-right: 0.5rem;
        }

        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

        strong {
            color: #495057;
        }
    </style>
@endsection
