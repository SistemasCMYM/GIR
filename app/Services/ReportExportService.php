<?php

namespace App\Services;

use App\Http\Requests\Admin\ReportRequest;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\EvaluacionPsicosocial;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportExportService
{
    protected $request;
    protected $filters;

    public function __construct(ReportRequest $request)
    {
        $this->request = $request;
        $this->filters = $request->validated();
    }

    public function export()
    {
        $reportType = $this->filters['tipo_reporte'];
        $format = $this->filters['formato'];

        // Obtener datos según el tipo de reporte
        $data = $this->getReportData($reportType);

        // Exportar según el formato
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data, $reportType);
            case 'excel':
                return $this->exportToExcel($data, $reportType);
            case 'csv':
                return $this->exportToCsv($data, $reportType);
            default:
                throw new \Exception('Formato de exportación no válido');
        }
    }

    protected function getReportData(string $reportType): array
    {
        switch ($reportType) {
            case 'empresas':
                return $this->getEmpresasData();
            case 'usuarios':
                return $this->getUsuariosData();
            case 'hallazgos':
                return $this->getHallazgosData();
            case 'psicosocial':
                return $this->getPsicosocialData();
            case 'actividad':
                return $this->getActividadData();
            case 'completo':
                return $this->getCompletoData();
            default:
                throw new \Exception('Tipo de reporte no válido');
        }
    }

    protected function getEmpresasData(): array
    {
        $query = Empresa::query();

        // Aplicar filtros de fecha si están presentes
        if (!empty($this->filters['fecha_inicio'])) {
            $query->where('created_at', '>=', $this->filters['fecha_inicio']);
        }
        if (!empty($this->filters['fecha_fin'])) {
            $query->where('created_at', '<=', $this->filters['fecha_fin']);
        }

        $empresas = $query->get();

        return [
            'title' => 'Reporte de Empresas',
            'headers' => ['ID', 'Nombre', 'Tipo', 'Estado', 'Usuarios', 'Fecha Creación'],
            'data' => $empresas->map(function ($empresa) {
                return [
                    $empresa->id,
                    $empresa->nombre,
                    $empresa->tipo ?? 'N/A',
                    $empresa->activa ? 'Activa' : 'Inactiva',
                    $empresa->usuarios()->count(),
                    $empresa->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray(),
            'statistics' => [
                'total_empresas' => $empresas->count(),
                'empresas_activas' => $empresas->where('activa', true)->count(),
                'empresas_inactivas' => $empresas->where('activa', false)->count(),
            ]
        ];
    }

    protected function getUsuariosData(): array
    {
        $query = Usuario::with('empresa');

        // Aplicar filtros
        if (!empty($this->filters['empresa_id'])) {
            $query->where('empresa_id', $this->filters['empresa_id']);
        }
        if (!empty($this->filters['estado'])) {
            $activo = $this->filters['estado'] === 'activo';
            $query->where('activo', $activo);
        }
        if (!empty($this->filters['fecha_inicio'])) {
            $query->where('created_at', '>=', $this->filters['fecha_inicio']);
        }
        if (!empty($this->filters['fecha_fin'])) {
            $query->where('created_at', '<=', $this->filters['fecha_fin']);
        }

        $usuarios = $query->get();

        return [
            'title' => 'Reporte de Usuarios',
            'headers' => ['ID', 'Nombre', 'Email', 'Empresa', 'Rol', 'Estado', 'Último Acceso', 'Fecha Creación'],
            'data' => $usuarios->map(function ($usuario) {
                return [
                    $usuario->id,
                    $usuario->nombre,
                    $usuario->email,
                    $usuario->empresa->nombre ?? 'N/A',
                    $usuario->rol,
                    $usuario->activo ? 'Activo' : 'Inactivo',
                    $usuario->fecha_ultimo_acceso ? $usuario->fecha_ultimo_acceso->format('Y-m-d H:i:s') : 'Nunca',
                    $usuario->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray(),
            'statistics' => [
                'total_usuarios' => $usuarios->count(),
                'usuarios_activos' => $usuarios->where('activo', true)->count(),
                'usuarios_inactivos' => $usuarios->where('activo', false)->count(),
                'por_rol' => $usuarios->groupBy('rol')->map->count()->toArray()
            ]
        ];
    }

    protected function getHallazgosData(): array
    {
        $query = Hallazgo::with('empresa');

        // Aplicar filtros
        if (!empty($this->filters['empresa_id'])) {
            $query->where('empresa_id', $this->filters['empresa_id']);
        }
        if (!empty($this->filters['fecha_inicio'])) {
            $query->where('created_at', '>=', $this->filters['fecha_inicio']);
        }
        if (!empty($this->filters['fecha_fin'])) {
            $query->where('created_at', '<=', $this->filters['fecha_fin']);
        }

        $hallazgos = $query->get();

        return [
            'title' => 'Reporte de Hallazgos',
            'headers' => ['ID', 'Título', 'Empresa', 'Categoría', 'Prioridad', 'Estado', 'Fecha Creación'],
            'data' => $hallazgos->map(function ($hallazgo) {
                return [
                    $hallazgo->id,
                    $hallazgo->titulo,
                    $hallazgo->empresa->nombre ?? 'N/A',
                    $hallazgo->categoria ?? 'N/A',
                    $hallazgo->prioridad ?? 'N/A',
                    $hallazgo->estado ?? 'N/A',
                    $hallazgo->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray(),
            'statistics' => [
                'total_hallazgos' => $hallazgos->count(),
                'por_prioridad' => $hallazgos->groupBy('prioridad')->map->count()->toArray(),
                'por_estado' => $hallazgos->groupBy('estado')->map->count()->toArray(),
            ]
        ];
    }

    protected function getPsicosocialData(): array
    {
        $query = EvaluacionPsicosocial::with('empresa');

        // Aplicar filtros
        if (!empty($this->filters['empresa_id'])) {
            $query->where('empresa_id', $this->filters['empresa_id']);
        }
        if (!empty($this->filters['fecha_inicio'])) {
            $query->where('created_at', '>=', $this->filters['fecha_inicio']);
        }
        if (!empty($this->filters['fecha_fin'])) {
            $query->where('created_at', '<=', $this->filters['fecha_fin']);
        }

        $evaluaciones = $query->get();

        return [
            'title' => 'Reporte de Evaluaciones Psicosociales',
            'headers' => ['ID', 'Empresa', 'Nivel Riesgo', 'Puntaje', 'Estado', 'Fecha Evaluación', 'Fecha Creación'],
            'data' => $evaluaciones->map(function ($evaluacion) {
                return [
                    $evaluacion->id,
                    $evaluacion->empresa->nombre ?? 'N/A',
                    $evaluacion->nivel_riesgo ?? 'N/A',
                    $evaluacion->puntaje_total ?? 'N/A',
                    $evaluacion->estado ?? 'N/A',
                    $evaluacion->fecha_evaluacion ? $evaluacion->fecha_evaluacion->format('Y-m-d') : 'N/A',
                    $evaluacion->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray(),
            'statistics' => [
                'total_evaluaciones' => $evaluaciones->count(),
                'por_nivel_riesgo' => $evaluaciones->groupBy('nivel_riesgo')->map->count()->toArray(),
                'promedio_puntaje' => $evaluaciones->avg('puntaje_total') ?? 0,
            ]
        ];
    }

    protected function getActividadData(): array
    {
        // Combinar datos de todas las actividades del sistema
        $fechaInicio = $this->filters['fecha_inicio'] ?? now()->subMonth()->format('Y-m-d');
        $fechaFin = $this->filters['fecha_fin'] ?? now()->format('Y-m-d');

        $usuarios = Usuario::whereBetween('created_at', [$fechaInicio, $fechaFin])->count();
        $hallazgos = Hallazgo::whereBetween('created_at', [$fechaInicio, $fechaFin])->count();
        $evaluaciones = EvaluacionPsicosocial::whereBetween('created_at', [$fechaInicio, $fechaFin])->count();

        return [
            'title' => 'Reporte de Actividad del Sistema',
            'headers' => ['Tipo de Actividad', 'Cantidad', 'Porcentaje'],
            'data' => [
                ['Usuarios Registrados', $usuarios, ''],
                ['Hallazgos Creados', $hallazgos, ''],
                ['Evaluaciones Psicosociales', $evaluaciones, ''],
            ],
            'statistics' => [
                'periodo' => "$fechaInicio - $fechaFin",
                'total_actividades' => $usuarios + $hallazgos + $evaluaciones,
            ]
        ];
    }

    protected function getCompletoData(): array
    {
        return [
            'title' => 'Reporte Completo del Sistema',
            'empresas' => $this->getEmpresasData(),
            'usuarios' => $this->getUsuariosData(),
            'hallazgos' => $this->getHallazgosData(),
            'psicosocial' => $this->getPsicosocialData(),
        ];
    }

    protected function exportToPdf(array $data, string $reportType): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generatePdfHtml($data, $reportType);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "reporte_{$reportType}_" . date('Y-m-d_H-i-s') . '.pdf';
        $output = $dompdf->output();
        
        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    protected function exportToExcel(array $data, string $reportType): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Título del reporte
        $sheet->setCellValue('A1', $data['title'] ?? "Reporte $reportType");
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($data['headers'])) . '1');
        
        // Headers
        $col = 1;
        foreach ($data['headers'] as $header) {
            $sheet->setCellValueByColumnAndRow($col, 2, $header);
            $col++;
        }
        
        // Datos
        $row = 3;
        foreach ($data['data'] as $rowData) {
            $col = 1;
            foreach ($rowData as $cellData) {
                $sheet->setCellValueByColumnAndRow($col, $row, $cellData);
                $col++;
            }
            $row++;
        }
        
        // Estadísticas (si existen)
        if (isset($data['statistics'])) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'ESTADÍSTICAS:');
            $row++;
            foreach ($data['statistics'] as $key => $value) {
                $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $key)) . ':');
                $sheet->setCellValue('B' . $row, is_array($value) ? json_encode($value) : $value);
                $row++;
            }
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = "reporte_{$reportType}_" . date('Y-m-d_H-i-s') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);
        
        return response()->download($temp_file, $filename)->deleteFileAfterSend();
    }

    protected function exportToCsv(array $data, string $reportType): string
    {
        $filename = "reporte_{$reportType}_" . date('Y-m-d_H-i-s') . '.csv';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        
        $handle = fopen($temp_file, 'w');
        
        // Headers
        fputcsv($handle, $data['headers']);
        
        // Datos
        foreach ($data['data'] as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        
        return response()->download($temp_file, $filename)->deleteFileAfterSend();
    }

    protected function generatePdfHtml(array $data, string $reportType): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . ($data['title'] ?? "Reporte $reportType") . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .title { font-size: 18px; font-weight: bold; }
                .date { font-size: 10px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .statistics { margin-top: 20px; }
                .stat-title { font-weight: bold; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">' . ($data['title'] ?? "Reporte $reportType") . '</div>
                <div class="date">Generado el: ' . date('Y-m-d H:i:s') . '</div>
            </div>
            
            <table>
                <thead>
                    <tr>';
        
        foreach ($data['headers'] as $header) {
            $html .= "<th>$header</th>";
        }
        
        $html .= '
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($data['data'] as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td>$cell</td>";
            }
            $html .= '</tr>';
        }
        
        $html .= '
                </tbody>
            </table>';
        
        if (isset($data['statistics'])) {
            $html .= '
            <div class="statistics">
                <div class="stat-title">ESTADÍSTICAS:</div>
                <table>
                    <tbody>';
            
            foreach ($data['statistics'] as $key => $value) {
                $displayValue = is_array($value) ? implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($value), $value)) : $value;
                $html .= '<tr><td><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong></td><td>' . $displayValue . '</td></tr>';
            }
            
            $html .= '
                    </tbody>
                </table>
            </div>';
        }
        
        $html .= '
        </body>
        </html>';
        
        return $html;
    }

    protected function getColumnLetter(int $columnNumber): string
    {
        $letter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr($columnNumber % 26 + 65) . $letter;
            $columnNumber = intval($columnNumber / 26);
        }
        return $letter;
    }
}
