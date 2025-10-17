@extends('layouts.print')

@section('title', 'Impresión - ' . $diagnostico->descripcion)

@section('content')
<div class="print-header">
    <h1>{{ $diagnostico->descripcion }}</h1>
    <p><strong>Fecha:</strong> {{ $diagnostico->fecha_evaluacion ?? $diagnostico->created_at->format('d/m/Y') }}</p>
    <p><strong>Evaluador:</strong> {{ $diagnostico->evaluador_id }}</p>
    @if($diagnostico->observaciones)
        <p><strong>Observaciones:</strong> {{ $diagnostico->observaciones }}</p>
    @endif
    <hr>
</div>

<div class="print-content">
    <!-- Estadísticas Generales -->
    @if(isset($resumen['estadisticas_generales']))
    <div class="section">
        <h2>Estadísticas Generales</h2>
        <table class="table table-bordered">
            <tr>
                <td>Total Evaluaciones</td>
                <td>{{ $resumen['estadisticas_generales']['total_evaluaciones'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Completadas</td>
                <td>{{ $resumen['estadisticas_generales']['completadas'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Pendientes</td>
                <td>{{ $resumen['estadisticas_generales']['pendientes'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>% Completado</td>
                <td>{{ $resumen['estadisticas_generales']['porcentaje_completado'] ?? 0 }}%</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Distribución de Riesgo -->
    @if(isset($resumen['distribucion_riesgo']))
    <div class="section">
        <h2>Distribución de Niveles de Riesgo</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nivel de Riesgo</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen['distribucion_riesgo']['contadores'] ?? [] as $nivel => $cantidad)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $nivel)) }}</td>
                    <td>{{ $cantidad }}</td>
                    <td>{{ $resumen['distribucion_riesgo']['porcentajes'][$nivel] ?? 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Lista de Empleados -->
    @if($hojas->count() > 0)
    <div class="section">
        <h2>Empleados Evaluados</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Área</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hojas as $hoja)
                <tr>
                    <td>{{ $hoja->nombre }}</td>
                    <td>{{ $hoja->dni }}</td>
                    <td>{{ $hoja->area_label }}</td>
                    <td>
                        @if($hoja->completado || $hoja->datos === 'completado')
                            Completado
                        @elseif($hoja->datos === 'en_progreso')
                            En Progreso
                        @else
                            Pendiente
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('styles')
<style>
    @media print {
        .print-header {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .table {
            font-size: 12px;
        }
        
        .table th {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endpush
@endsection
