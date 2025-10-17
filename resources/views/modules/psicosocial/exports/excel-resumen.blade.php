<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Psicosocial - Excel</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RESUMEN DIAGNÓSTICO PSICOSOCIAL</h1>
        <p>Fecha: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="section-title">ESTADÍSTICAS GENERALES</div>
    <table>
        <thead>
            <tr>
                <th>Total Empleados</th>
                <th>Evaluaciones Completas</th>
                <th>Evaluaciones Pendientes</th>
                <th>% Completado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $resumen['estadisticas_generales']['total_empleados'] ?? 0 }}</td>
                <td>{{ $resumen['estadisticas_generales']['evaluaciones_completas'] ?? 0 }}</td>
                <td>{{ $resumen['estadisticas_generales']['evaluaciones_pendientes'] ?? 0 }}</td>
                <td>{{ $resumen['estadisticas_generales']['porcentaje_completado'] ?? 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Distribución Riesgo Intralaboral Forma A -->
    @if(isset($resumen['distribucion_riesgo']['intralaboral_a']))
    <div class="section-title">DISTRIBUCIÓN RIESGO INTRALABORAL (FORMA A)</div>
    <table>
        <thead>
            <tr>
                <th>Nivel de Riesgo</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumen['distribucion_riesgo']['intralaboral_a'] as $nivel => $datos)
            <tr>
                <td>{{ ucfirst($nivel) }}</td>
                <td>{{ $datos['cantidad'] }}</td>
                <td>{{ $datos['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Distribución Riesgo Intralaboral Forma B -->
    @if(isset($resumen['distribucion_riesgo']['intralaboral_b']))
    <div class="section-title">DISTRIBUCIÓN RIESGO INTRALABORAL (FORMA B)</div>
    <table>
        <thead>
            <tr>
                <th>Nivel de Riesgo</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumen['distribucion_riesgo']['intralaboral_b'] as $nivel => $datos)
            <tr>
                <td>{{ ucfirst($nivel) }}</td>
                <td>{{ $datos['cantidad'] }}</td>
                <td>{{ $datos['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Distribución Riesgo Extralaboral -->
    @if(isset($resumen['distribucion_riesgo']['extralaboral']))
    <div class="section-title">DISTRIBUCIÓN RIESGO EXTRALABORAL</div>
    <table>
        <thead>
            <tr>
                <th>Nivel de Riesgo</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumen['distribucion_riesgo']['extralaboral'] as $nivel => $datos)
            <tr>
                <td>{{ ucfirst($nivel) }}</td>
                <td>{{ $datos['cantidad'] }}</td>
                <td>{{ $datos['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Distribución Estrés -->
    @if(isset($resumen['distribucion_riesgo']['estres']))
    <div class="section-title">DISTRIBUCIÓN NIVEL DE ESTRÉS</div>
    <table>
        <thead>
            <tr>
                <th>Nivel de Estrés</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumen['distribucion_riesgo']['estres'] as $nivel => $datos)
            <tr>
                <td>{{ ucfirst($nivel) }}</td>
                <td>{{ $datos['cantidad'] }}</td>
                <td>{{ $datos['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Datos Sociodemográficos -->
    @if(isset($resumen['sociodemograficos']))
    <div class="section-title">DATOS SOCIODEMOGRÁFICOS</div>
    
    @if(isset($resumen['sociodemograficos']['sexo']))
    <table>
        <thead>
            <tr><th colspan="3">DISTRIBUCIÓN POR SEXO</th></tr>
            <tr>
                <th>Sexo</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumen['sociodemograficos']['sexo'] as $sexo => $datos)
            <tr>
                <td>{{ ucfirst($sexo) }}</td>
                <td>{{ $datos['cantidad'] }}</td>
                <td>{{ $datos['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($resumen['sociodemograficos']['edad']))
    <table>
        <thead>
            <tr><th colspan="3">DISTRIBUCIÓN POR EDAD</th></tr>
            <tr>
                <th>Rango de Edad</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumen['sociodemograficos']['edad'] as $rango => $datos)
            <tr>
                <td>{{ $rango }}</td>
                <td>{{ $datos['cantidad'] }}</td>
                <td>{{ $datos['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif

</body>
</html>
