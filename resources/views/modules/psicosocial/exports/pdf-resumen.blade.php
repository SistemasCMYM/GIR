<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Psicosocial - PDF</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .stats-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .riesgo-muy-alto { background-color: #dc3545; color: white; }
        .riesgo-alto { background-color: #fd7e14; color: white; }
        .riesgo-medio { background-color: #ffc107; color: #000; }
        .riesgo-bajo { background-color: #20c997; color: white; }
        .riesgo-sin-riesgo { background-color: #28a745; color: white; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">GIR-365</div>
        <h1>Resumen Diagnóstico Psicosocial</h1>
        <p>Fecha de generación: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="section">
        <h2 class="section-title">1. Estadísticas Generales</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Total Empleados</div>
                <div class="stats-cell stats-header">Evaluaciones Completas</div>
                <div class="stats-cell stats-header">% Completado</div>
            </div>
            <div class="stats-row">
                <div class="stats-cell">{{ $resumen['estadisticas_generales']['total_empleados'] ?? 0 }}</div>
                <div class="stats-cell">{{ $resumen['estadisticas_generales']['evaluaciones_completas'] ?? 0 }}</div>
                <div class="stats-cell">{{ $resumen['estadisticas_generales']['porcentaje_completado'] ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <!-- Distribución de Riesgo Intralaboral Forma A -->
    @if(isset($resumen['distribucion_riesgo']['intralaboral_a']))
    <div class="section">
        <h2 class="section-title">2. Distribución Riesgo Intralaboral (Forma A)</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Nivel de Riesgo</div>
                <div class="stats-cell stats-header">Cantidad</div>
                <div class="stats-cell stats-header">Porcentaje</div>
            </div>
            @foreach($resumen['distribucion_riesgo']['intralaboral_a'] as $nivel => $datos)
            <div class="stats-row">
                <div class="stats-cell riesgo-{{ strtolower(str_replace(' ', '-', $nivel)) }}">{{ ucfirst($nivel) }}</div>
                <div class="stats-cell">{{ $datos['cantidad'] }}</div>
                <div class="stats-cell">{{ $datos['porcentaje'] }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Distribución de Riesgo Intralaboral Forma B -->
    @if(isset($resumen['distribucion_riesgo']['intralaboral_b']))
    <div class="section">
        <h2 class="section-title">3. Distribución Riesgo Intralaboral (Forma B)</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Nivel de Riesgo</div>
                <div class="stats-cell stats-header">Cantidad</div>
                <div class="stats-cell stats-header">Porcentaje</div>
            </div>
            @foreach($resumen['distribucion_riesgo']['intralaboral_b'] as $nivel => $datos)
            <div class="stats-row">
                <div class="stats-cell riesgo-{{ strtolower(str_replace(' ', '-', $nivel)) }}">{{ ucfirst($nivel) }}</div>
                <div class="stats-cell">{{ $datos['cantidad'] }}</div>
                <div class="stats-cell">{{ $datos['porcentaje'] }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Distribución de Riesgo Extralaboral -->
    @if(isset($resumen['distribucion_riesgo']['extralaboral']))
    <div class="section">
        <h2 class="section-title">4. Distribución Riesgo Extralaboral</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Nivel de Riesgo</div>
                <div class="stats-cell stats-header">Cantidad</div>
                <div class="stats-cell stats-header">Porcentaje</div>
            </div>
            @foreach($resumen['distribucion_riesgo']['extralaboral'] as $nivel => $datos)
            <div class="stats-row">
                <div class="stats-cell riesgo-{{ strtolower(str_replace(' ', '-', $nivel)) }}">{{ ucfirst($nivel) }}</div>
                <div class="stats-cell">{{ $datos['cantidad'] }}</div>
                <div class="stats-cell">{{ $datos['porcentaje'] }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Distribución de Estrés -->
    @if(isset($resumen['distribucion_riesgo']['estres']))
    <div class="section">
        <h2 class="section-title">5. Distribución Nivel de Estrés</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Nivel de Estrés</div>
                <div class="stats-cell stats-header">Cantidad</div>
                <div class="stats-cell stats-header">Porcentaje</div>
            </div>
            @foreach($resumen['distribucion_riesgo']['estres'] as $nivel => $datos)
            <div class="stats-row">
                <div class="stats-cell riesgo-{{ strtolower(str_replace(' ', '-', $nivel)) }}">{{ ucfirst($nivel) }}</div>
                <div class="stats-cell">{{ $datos['cantidad'] }}</div>
                <div class="stats-cell">{{ $datos['porcentaje'] }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Generado por GIR-365 - Sistema de Gestión Integral de Riesgos</p>
        <p>Este reporte contiene información confidencial de la organización</p>
    </div>
</body>
</html>
