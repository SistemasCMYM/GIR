@extends('layouts.dashboard')

@section('title', 'Cuestionario Intralaboral - Forma A')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">CUESTIONARIO DE FACTORES DE RIESGO PSICOSOCIAL INTRALABORAL - FORMA A</h3>
                        <p class="text-muted">Jefes, profesionales, técnicos y tecnólogos</p>
                    </div>

                    <div class="card-body">
                        <form id="formulario-intralaboral-a" method="POST"
                            action="{{ route('psicosocial.instrumentos.guardar-respuestas') }}">
                            @csrf
                            <input type="hidden" name="tipo_instrumento" value="intralaboral_a">
                            <input type="hidden" name="hoja_id" value="{{ $hojaId ?? '' }}">

                            <!-- Pregunta inicial sobre atención a clientes -->
                            <div class="form-group mb-4">
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>En mi trabajo debo brindar servicio a clientes o usuarios:</strong></p>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="atiende_clientes"
                                                id="atiende_si" value="si" required>
                                            <label class="form-check-label" for="atiende_si">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="atiende_clientes"
                                                id="atiende_no" value="no" required>
                                            <label class="form-check-label" for="atiende_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preguntas condicionadas por atención a clientes -->
                            <div id="seccion-clientes" class="d-none">
                                <p><strong>Si su respuesta fue SÍ por favor responda las siguientes preguntas. Si su
                                        respuesta fue NO pase a las preguntas de la página siguiente.</strong></p>

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%"></th>
                                                <th style="width: 55%">Las siguientes preguntas están relacionadas con la
                                                    atención a clientes y usuarios.</th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntasClientes))
                                                @foreach ($preguntasClientes as $pregunta)
                                                    <tr>
                                                        <td><strong>{{ $pregunta->numero }}</strong></td>
                                                        <td>{{ $pregunta->texto }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="4" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="3" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="2" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="1" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="0" required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pregunta sobre supervisión -->
                            <div class="form-group mb-4">
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Soy jefe de otras personas en mi trabajo:</strong></p>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="es_jefe" id="jefe_si"
                                                value="si" required>
                                            <label class="form-check-label" for="jefe_si">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="es_jefe" id="jefe_no"
                                                value="no" required>
                                            <label class="form-check-label" for="jefe_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preguntas condicionadas por supervisión -->
                            <div id="seccion-jefe" class="d-none">
                                <p><strong>Si su respuesta fue SÍ por favor responda las siguientes preguntas. Si su
                                        respuesta fue NO pase a la pregunta de la página siguiente: FICHA DE DATOS
                                        GENERALES.</strong></p>

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%"></th>
                                                <th style="width: 55%">Las siguientes preguntas están relacionadas con las
                                                    personas que usted supervisa o dirige.</th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntasJefe))
                                                @foreach ($preguntasJefe as $pregunta)
                                                    <tr>
                                                        <td><strong>{{ $pregunta->numero }}</strong></td>
                                                        <td>{{ $pregunta->texto }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="4" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="3" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="2" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="1" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="0" required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Sección principal del cuestionario -->
                            <div class="mb-4">
                                <h5>FICHA DE DATOS GENERALES</h5>
                                <p>Las siguientes preguntas están relacionadas con las condiciones ambientales del(os)
                                    sitio(s) o lugar(es) donde habitualmente realiza su trabajo.</p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%"></th>
                                                <th style="width: 55%"></th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntasGenerales))
                                                @foreach ($preguntasGenerales as $pregunta)
                                                    <tr>
                                                        <td><strong>{{ $pregunta->numero }}</strong></td>
                                                        <td>{{ $pregunta->texto }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="4" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="3" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="2" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="1" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="0" required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Secciones adicionales según imágenes -->
                            <div class="mb-4">
                                <p><strong>Para responder a las siguientes preguntas piense en la cantidad de trabajo que
                                        usted tiene a cargo.</strong></p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%"></th>
                                                <th style="width: 55%"></th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntasCantidadTrabajo))
                                                @foreach ($preguntasCantidadTrabajo as $pregunta)
                                                    <tr>
                                                        <td><strong>{{ $pregunta->numero }}</strong></td>
                                                        <td>{{ $pregunta->texto }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="4" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="3" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="2" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="1" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="0" required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Sección esfuerzo mental -->
                            <div class="mb-4">
                                <p><strong>Las siguientes preguntas están relacionadas con el esfuerzo mental que le exige
                                        su trabajo.</strong></p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%"></th>
                                                <th style="width: 55%"></th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntasEsfuerzoMental))
                                                @foreach ($preguntasEsfuerzoMental as $pregunta)
                                                    <tr>
                                                        <td><strong>{{ $pregunta->numero }}</strong></td>
                                                        <td>{{ $pregunta->texto }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="4" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="3" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="2" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="1" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="0" required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Sección responsabilidades -->
                            <div class="mb-4">
                                <p><strong>Las siguientes preguntas están relacionadas con las responsabilidades y
                                        actividades que usted debe hacer en su trabajo.</strong></p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%"></th>
                                                <th style="width: 55%"></th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntasResponsabilidades))
                                                @foreach ($preguntasResponsabilidades as $pregunta)
                                                    <tr>
                                                        <td><strong>{{ $pregunta->numero }}</strong></td>
                                                        <td>{{ $pregunta->texto }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="4" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="3" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="2" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="1" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="radio" name="respuesta_{{ $pregunta->_id }}"
                                                                value="0" required>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Guardar Respuestas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Control de secciones condicionadas
            const atiendeCientesRadios = document.querySelectorAll('input[name="atiende_clientes"]');
            const seccionClientes = document.getElementById('seccion-clientes');

            const esJefeRadios = document.querySelectorAll('input[name="es_jefe"]');
            const seccionJefe = document.getElementById('seccion-jefe');

            atiendeCientesRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'si') {
                        seccionClientes.classList.remove('d-none');
                        // Marcar preguntas como requeridas
                        seccionClientes.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.required = true;
                        });
                    } else {
                        seccionClientes.classList.add('d-none');
                        // Quitar requisito y limpiar respuestas
                        seccionClientes.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.required = false;
                            input.checked = false;
                        });
                    }
                });
            });

            esJefeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'si') {
                        seccionJefe.classList.remove('d-none');
                        // Marcar preguntas como requeridas
                        seccionJefe.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.required = true;
                        });
                    } else {
                        seccionJefe.classList.add('d-none');
                        // Quitar requisito y limpiar respuestas
                        seccionJefe.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.required = false;
                            input.checked = false;
                        });
                    }
                });
            });
        });
    </script>
@endsection
