@extends('layouts.dashboard')

@section('title', 'Cuestionario de Estrés')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="card-title">CUESTIONARIO PARA LA EVALUACIÓN DEL ESTRÉS</h3>
                        <p class="text-muted mb-0">31 preguntas sobre síntomas de estrés</p>
                    </div>

                    <div class="card-body">
                        <form id="formulario-estres" method="POST"
                            action="{{ route('psicosocial.instrumentos.guardar-respuestas') }}">
                            @csrf
                            <input type="hidden" name="tipo_instrumento" value="estres">
                            <input type="hidden" name="hoja_id" value="{{ $hojaId ?? '' }}">

                            <div class="mb-4">
                                <p>Las siguientes preguntas están relacionadas con las molestias y síntomas que usted puede
                                    haber presentado en los últimos tres meses.</p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-warning">
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 55%">Pregunta</th>
                                                <th style="width: 8%">Siempre</th>
                                                <th style="width: 8%">Casi siempre</th>
                                                <th style="width: 8%">Algunas veces</th>
                                                <th style="width: 8%">Casi nunca</th>
                                                <th style="width: 8%">Nunca</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($preguntas) && $preguntas->count() > 0)
                                                @foreach ($preguntas as $pregunta)
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
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">
                                                        No se encontraron preguntas de estrés en la base de datos
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-save"></i> Guardar Respuestas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
