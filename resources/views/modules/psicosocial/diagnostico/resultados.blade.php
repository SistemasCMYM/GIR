@extends('layouts.dashboard-modern')

@section('title', 'Resultados Grupales - Diagnóstico Psicosocial')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-chart-bar text-success"></i>
                        Resultados Grupales
                    </h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="{{ route('psicosocial.resumen', $diagnostico->id) }}" class="btn btn-success">
                        <i class="fas fa-chart-pie"></i> Resumen Completo Psicosocial
                    </a>
                    <a href="{{ route('psicosocial.diagnostico.show', $diagnostico->id) }}"
                        class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Diagnóstico
                    </a>
                    <a href="{{ route('psicosocial.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- INFORMACIÓN DEL DIAGNÓSTICO -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Información del Diagnóstico
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Descripción:</dt>
                                        <dd class="col-sm-8">{{ $diagnostico->descripcion ?? 'No especificado' }}</dd>

                                        <dt class="col-sm-4">Empresa:</dt>
                                        <dd class="col-sm-8">{{ $diagnostico->empresa_id ?? 'No especificado' }}</dd>

                                        <dt class="col-sm-4">Fecha Creación:</dt>
                                        <dd class="col-sm-8">
                                            {{ $diagnostico->created_at ? $diagnostico->created_at->format('d/m/Y') : 'Sin fecha' }}
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Total Evaluaciones:</dt>
                                        <dd class="col-sm-8">{{ $hojas->count() }}</dd>

                                        <dt class="col-sm-4">Evaluaciones Completadas:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-success">{{ $hojasConResultados }}</span>
                                            @if ($hojasConResultados > 0)
                                                <small class="text-muted">(Datos procesados con servicio
                                                    actualizado)</small>
                                            @endif
                                        </dd>

                                        <dt class="col-sm-4">Porcentaje Completado:</dt>
                                        <dd class="col-sm-8">
                                            {{ $hojas->count() > 0 ? round(($hojasConResultados / $hojas->count()) * 100) : 0 }}%
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <a href="#" class="btn btn-primary"
                                        onclick="exportarResultados('{{ $diagnostico->id }}')">
                                        <i class="fas fa-file-export"></i> Exportar Resultados
                                    </a>
                                    <a href="#" class="btn btn-info"
                                        onclick="imprimirResultados('{{ $diagnostico->id }}')">
                                        <i class="fas fa-print"></i> Imprimir Resultados
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($hojasConResultados > 0)
                <!-- DISTRIBUCIÓN DE NIVELES DE RIESGO -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie"></i> Distribución de Niveles de Riesgo
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <canvas id="distribucionRiesgoChart" height="300"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Nivel de Riesgo</th>
                                                        <th>Cantidad</th>
                                                        <th>Porcentaje</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><span class="badge text-white"
                                                                style="background-color: #008235;">Sin Riesgo</span></td>
                                                        <td>{{ $distribucionNiveles['sin_riesgo'] }}</td>
                                                        <td>{{ $hojasConResultados > 0 ? round(($distribucionNiveles['sin_riesgo'] / $hojasConResultados) * 100) : 0 }}%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge text-white"
                                                                style="background-color: #00D364;">Bajo</span></td>
                                                        <td>{{ $distribucionNiveles['bajo'] }}</td>
                                                        <td>{{ $hojasConResultados > 0 ? round(($distribucionNiveles['bajo'] / $hojasConResultados) * 100) : 0 }}%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge text-dark"
                                                                style="background-color: #FFD600;">Medio</span></td>
                                                        <td>{{ $distribucionNiveles['medio'] }}</td>
                                                        <td>{{ $hojasConResultados > 0 ? round(($distribucionNiveles['medio'] / $hojasConResultados) * 100) : 0 }}%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge text-white"
                                                                style="background-color: #DA3D3D;">Alto</span></td>
                                                        <td>{{ $distribucionNiveles['alto'] }}</td>
                                                        <td>{{ $hojasConResultados > 0 ? round(($distribucionNiveles['alto'] / $hojasConResultados) * 100) : 0 }}%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge text-white"
                                                                style="background-color: #D10000;">Muy Alto</span></td>
                                                        <td>{{ $distribucionNiveles['muy_alto'] }}</td>
                                                        <td>{{ $hojasConResultados > 0 ? round(($distribucionNiveles['muy_alto'] / $hojasConResultados) * 100) : 0 }}%
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RESULTADOS PROMEDIO POR DOMINIO -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar"></i> Resultados Promedio por Dominio
                                </h3>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="dominiosTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="intralaboral-tab" data-toggle="tab"
                                            href="#intralaboral" role="tab" aria-controls="intralaboral"
                                            aria-selected="true">Factores Intralaborales</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="extralaboral-tab" data-toggle="tab" href="#extralaboral"
                                            role="tab" aria-controls="extralaboral" aria-selected="false">Factores
                                            Extralaborales</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="estres-tab" data-toggle="tab" href="#estres"
                                            role="tab" aria-controls="estres" aria-selected="false">Estrés</a>
                                    </li>
                                </ul>

                                <div class="tab-content p-3" id="dominiosTabsContent">
                                    <!-- INTRALABORAL -->
                                    <div class="tab-pane fade show active" id="intralaboral" role="tabpanel"
                                        aria-labelledby="intralaboral-tab">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <canvas id="intralaboralChart" height="300"></canvas>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th>Dominio</th>
                                                                <th>Puntaje</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Liderazgo y Relaciones Sociales</td>
                                                                <td>{{ $promedioIntralaboral['liderazgo_relaciones_sociales'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Control sobre el Trabajo</td>
                                                                <td>{{ $promedioIntralaboral['control_sobre_trabajo'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Demandas del Trabajo</td>
                                                                <td>{{ $promedioIntralaboral['demandas_trabajo'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Recompensas</td>
                                                                <td>{{ $promedioIntralaboral['recompensas'] }}</td>
                                                            </tr>
                                                            <tr class="table-primary">
                                                                <td><strong>Total Intralaboral</strong></td>
                                                                <td><strong>{{ $promedioIntralaboral['total'] }}</strong>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EXTRALABORAL -->
                                    <div class="tab-pane fade" id="extralaboral" role="tabpanel"
                                        aria-labelledby="extralaboral-tab">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <canvas id="extralaboralChart" height="300"></canvas>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead class="table-success">
                                                            <tr>
                                                                <th>Dimensión</th>
                                                                <th>Puntaje</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Tiempo Fuera del Trabajo</td>
                                                                <td>{{ $promedioExtralaboral['tiempo_fuera_trabajo'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Relaciones Familiares</td>
                                                                <td>{{ $promedioExtralaboral['relaciones_familiares'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Comunicación y Relaciones</td>
                                                                <td>{{ $promedioExtralaboral['comunicacion_relaciones_interpersonales'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Situación Económica</td>
                                                                <td>{{ $promedioExtralaboral['situacion_economica'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Características de Vivienda</td>
                                                                <td>{{ $promedioExtralaboral['caracteristicas_vivienda'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Influencia Entorno Extralaboral</td>
                                                                <td>{{ $promedioExtralaboral['influencia_entorno_extralaboral'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Desplazamiento</td>
                                                                <td>{{ $promedioExtralaboral['desplazamiento_vivienda_trabajo'] }}
                                                                </td>
                                                            </tr>
                                                            <tr class="table-success">
                                                                <td><strong>Total Extralaboral</strong></td>
                                                                <td><strong>{{ $promedioExtralaboral['total'] }}</strong>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ESTRÉS -->
                                    <div class="tab-pane fade" id="estres" role="tabpanel"
                                        aria-labelledby="estres-tab">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <canvas id="estresChart" height="300"></canvas>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead class="table-warning">
                                                            <tr>
                                                                <th>Categoría</th>
                                                                <th>Puntaje</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Síntomas Fisiológicos</td>
                                                                <td>{{ $promedioEstres['sintomas_fisiologicos'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Síntomas de Comportamiento Social</td>
                                                                <td>{{ $promedioEstres['sintomas_comportamiento_social'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Síntomas Intelectuales y Laborales</td>
                                                                <td>{{ $promedioEstres['sintomas_intelectuales_laborales'] }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Síntomas Psicoemocionales</td>
                                                                <td>{{ $promedioEstres['sintomas_psicoemocionales'] }}</td>
                                                            </tr>
                                                            <tr class="table-warning">
                                                                <td><strong>Total Estrés</strong></td>
                                                                <td><strong>{{ $promedioEstres['total'] }}</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECOMENDACIONES -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-lightbulb"></i> Recomendaciones Generales
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Las siguientes recomendaciones se basan en los
                                    resultados grupales obtenidos.
                                </div>

                                <div class="accordion" id="recomendacionesAccordion">
                                    <!-- RECOMENDACIONES INTRALABORALES -->
                                    <div class="card">
                                        <div class="card-header" id="headingIntralaboral">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left" type="button"
                                                    data-toggle="collapse" data-target="#collapseIntralaboral"
                                                    aria-expanded="true" aria-controls="collapseIntralaboral">
                                                    Recomendaciones para Factores Intralaborales
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseIntralaboral" class="collapse show"
                                            aria-labelledby="headingIntralaboral" data-parent="#recomendacionesAccordion">
                                            <div class="card-body">
                                                <ul>
                                                    @if (isset($interpretaciones['intralaboral']['recomendaciones']))
                                                        @foreach ($interpretaciones['intralaboral']['recomendaciones'] as $recomendacion)
                                                            <li>{{ $recomendacion }}</li>
                                                        @endforeach
                                                    @else
                                                        <li>Fortalecer el liderazgo y las relaciones sociales en el trabajo
                                                            mediante capacitaciones y actividades de integración.</li>
                                                        <li>Mejorar la claridad de rol y las oportunidades para el uso y
                                                            desarrollo de habilidades y conocimientos.</li>
                                                        <li>Implementar estrategias para el manejo de las demandas del
                                                            trabajo, especialmente en cuanto a carga mental y emocional.
                                                        </li>
                                                        <li>Revisar y fortalecer el sistema de recompensas y reconocimiento
                                                            al desempeño.</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RECOMENDACIONES EXTRALABORALES -->
                                    <div class="card">
                                        <div class="card-header" id="headingExtralaboral">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left collapsed" type="button"
                                                    data-toggle="collapse" data-target="#collapseExtralaboral"
                                                    aria-expanded="false" aria-controls="collapseExtralaboral">
                                                    Recomendaciones para Factores Extralaborales
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseExtralaboral" class="collapse"
                                            aria-labelledby="headingExtralaboral" data-parent="#recomendacionesAccordion">
                                            <div class="card-body">
                                                <ul>
                                                    @if (isset($interpretaciones['extralaboral']['recomendaciones']))
                                                        @foreach ($interpretaciones['extralaboral']['recomendaciones'] as $recomendacion)
                                                            <li>{{ $recomendacion }}</li>
                                                        @endforeach
                                                    @else
                                                        <li>Promover actividades de integración familiar y uso adecuado del
                                                            tiempo libre.</li>
                                                        <li>Brindar asesoramiento en finanzas personales y manejo de
                                                            economía familiar.</li>
                                                        <li>Considerar opciones de horarios flexibles o teletrabajo para
                                                            reducir el impacto de los desplazamientos.</li>
                                                        <li>Fomentar el equilibrio entre la vida laboral y personal.</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RECOMENDACIONES ESTRÉS -->
                                    <div class="card">
                                        <div class="card-header" id="headingEstres">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left collapsed" type="button"
                                                    data-toggle="collapse" data-target="#collapseEstres"
                                                    aria-expanded="false" aria-controls="collapseEstres">
                                                    Recomendaciones para Manejo del Estrés
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseEstres" class="collapse" aria-labelledby="headingEstres"
                                            data-parent="#recomendacionesAccordion">
                                            <div class="card-body">
                                                <ul>
                                                    @if (isset($interpretaciones['estres']['recomendaciones']))
                                                        @foreach ($interpretaciones['estres']['recomendaciones'] as $recomendacion)
                                                            <li>{{ $recomendacion }}</li>
                                                        @endforeach
                                                    @else
                                                        <li>Implementar programas de gestión del estrés y técnicas de
                                                            relajación.</li>
                                                        <li>Promover hábitos saludables como alimentación balanceada,
                                                            actividad física y descanso adecuado.</li>
                                                        <li>Realizar pausas activas durante la jornada laboral.</li>
                                                        <li>Considerar la implementación de un programa de apoyo psicológico
                                                            para casos que lo requieran.</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                    <h4>No hay evaluaciones completadas</h4>
                                    <p class="text-muted mb-4">
                                        Para ver los resultados grupales, es necesario que al menos una evaluación esté
                                        completamente finalizada
                                        (datos personales, factores intralaborales, extralaborales y estrés).
                                    </p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6>Total Evaluaciones</h6>
                                                    <h3 class="text-primary">{{ $hojas->count() }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6>En Progreso</h6>
                                                    <h3 class="text-warning">
                                                        {{ $hojas->filter(function ($h) {
                                                                return $h->intralaboral === 'en_progreso' ||
                                                                    $h->extralaboral === 'en_progreso' ||
                                                                    $h->estres === 'en_progreso' ||
                                                                    $h->datos === 'en_progreso';
                                                            })->count() }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6>Pendientes</h6>
                                                    <h3 class="text-secondary">
                                                        {{ $hojas->filter(function ($h) {
                                                                return $h->intralaboral === 'pendiente' &&
                                                                    $h->extralaboral === 'pendiente' &&
                                                                    $h->estres === 'pendiente' &&
                                                                    $h->datos === 'pendiente';
                                                            })->count() }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('psicosocial.resumen', $diagnostico->id) }}"
                                            class="btn btn-success btn-lg">
                                            <i class="fas fa-chart-pie"></i> Ver Resumen Completo
                                        </a>
                                        <a href="{{ route('psicosocial.diagnostico.show', $diagnostico->id) }}"
                                            class="btn btn-primary btn-lg">
                                            <i class="fas fa-arrow-left"></i> Volver al Diagnóstico
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    @if ($hojasConResultados > 0)
        <!-- Chart.js y plugins para gráficas 3D -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js-plugin-3d@1.9.0/dist/chart.3d.umd.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Configuración para efecto 3D
                const threeDConfig = {
                    enabled: true,
                    alpha: 20, // rotación en el eje X
                    beta: 30, // rotación en el eje Y
                    depth: 30, // profundidad del efecto 3D
                    shadowDepth: 2
                };

                // Gráfico de distribución de niveles de riesgo
                var ctxDistribucion = document.getElementById('distribucionRiesgoChart').getContext('2d');
                var distribucionChart = new Chart(ctxDistribucion, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sin Riesgo', 'Bajo', 'Medio', 'Alto', 'Muy Alto'],
                        datasets: [{
                            data: [
                                {{ $distribucionNiveles['sin_riesgo'] }},
                                {{ $distribucionNiveles['bajo'] }},
                                {{ $distribucionNiveles['medio'] }},
                                {{ $distribucionNiveles['alto'] }},
                                {{ $distribucionNiveles['muy_alto'] }}
                            ],
                            backgroundColor: [
                                '#077D3E', // Sin Riesgo - Verde oscuro
                                '#00D364', // Riesgo Bajo - Verde claro
                                '#FFD600', // Riesgo Medio - Amarillo
                                '#FF0000', // Riesgo Alto - Rojo
                                '#8B0000' // Riesgo Muy Alto - Rojo oscuro
                            ],
                            borderWidth: 2,
                            borderColor: [
                                '#077D3E',
                                '#00D364',
                                '#FFD600',
                                '#FF0000',
                                '#8B0000'
                            ],
                            hoverOffset: 10
                        }]
                    },
                    plugins: [ChartJs3D],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '50%',
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.raw || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return percentage + '%';
                                },
                                display: function(context) {
                                    return context.dataset.data[context.dataIndex] > 0;
                                }
                            },
                            '3d': threeDConfig
                        }
                    }
                });

                // Gráfico de Factores Intralaborales
                var ctxIntralaboral = document.getElementById('intralaboralChart').getContext('2d');
                var intralaboralChart = new Chart(ctxIntralaboral, {
                    type: 'bar',
                    data: {
                        labels: ['Liderazgo y Relaciones', 'Control sobre el Trabajo', 'Demandas del Trabajo',
                            'Recompensas', 'Total'
                        ],
                        datasets: [{
                            label: 'Puntaje Promedio',
                            data: [
                                {{ $promedioIntralaboral['liderazgo_relaciones_sociales'] }},
                                {{ $promedioIntralaboral['control_sobre_trabajo'] }},
                                {{ $promedioIntralaboral['demandas_trabajo'] }},
                                {{ $promedioIntralaboral['recompensas'] }},
                                {{ $promedioIntralaboral['total'] }}
                            ],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Puntaje: ' + context.raw.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });

                // Gráfico de Factores Extralaborales
                var ctxExtralaboral = document.getElementById('extralaboralChart').getContext('2d');
                var extralaboralChart = new Chart(ctxExtralaboral, {
                    type: 'bar',
                    data: {
                        labels: ['Tiempo Fuera', 'Rel. Familiares', 'Comunicación', 'Sit. Económica',
                            'Vivienda', 'Entorno', 'Desplazamiento', 'Total'
                        ],
                        datasets: [{
                            label: 'Puntaje Promedio',
                            data: [
                                {{ $promedioExtralaboral['tiempo_fuera_trabajo'] }},
                                {{ $promedioExtralaboral['relaciones_familiares'] }},
                                {{ $promedioExtralaboral['comunicacion_relaciones_interpersonales'] }},
                                {{ $promedioExtralaboral['situacion_economica'] }},
                                {{ $promedioExtralaboral['caracteristicas_vivienda'] }},
                                {{ $promedioExtralaboral['influencia_entorno_extralaboral'] }},
                                {{ $promedioExtralaboral['desplazamiento_vivienda_trabajo'] }},
                                {{ $promedioExtralaboral['total'] }}
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 159, 64, 0.8)',
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(255, 205, 86, 0.8)',
                                'rgba(201, 203, 207, 0.8)',
                                'rgba(75, 192, 192, 0.8)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(201, 203, 207, 1)',
                                'rgba(75, 192, 192, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Puntaje: ' + context.raw.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });

                // Gráfico de Estrés
                var ctxEstres = document.getElementById('estresChart').getContext('2d');
                var estresChart = new Chart(ctxEstres, {
                    type: 'bar',
                    data: {
                        labels: ['Fisiológicos', 'Comportamiento', 'Intelectuales', 'Psicoemocionales',
                            'Total'
                        ],
                        datasets: [{
                            label: 'Puntaje Promedio',
                            data: [
                                {{ $promedioEstres['sintomas_fisiologicos'] }},
                                {{ $promedioEstres['sintomas_comportamiento_social'] }},
                                {{ $promedioEstres['sintomas_intelectuales_laborales'] }},
                                {{ $promedioEstres['sintomas_psicoemocionales'] }},
                                {{ $promedioEstres['total'] }}
                            ],
                            backgroundColor: [
                                'rgba(112, 81, 88, 0.8)',
                                'rgba(255, 159, 64, 0.8)',
                                'rgba(255, 205, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(54, 162, 235, 0.8)'
                            ],
                            borderColor: [
                                'rgb(112, 81, 88)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Puntaje: ' + context.raw.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
            });

            // Función para exportar resultados
            function exportarResultados(diagnosticoId) {
                window.location.href = `/psicosocial/diagnostico/${diagnosticoId}/exportar`;
            }

            // Función para imprimir resultados
            function imprimirResultados(diagnosticoId) {
                window.location.href = `/psicosocial/diagnostico/${diagnosticoId}/imprimir`;
            }
        </script>
    @endif
@endsection
