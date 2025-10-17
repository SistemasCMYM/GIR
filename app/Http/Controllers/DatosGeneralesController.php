<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DatosGenerales;
use App\Models\Hoja;

class DatosGeneralesController extends Controller
{
    /**
     * Guardar borrador de datos generales
     */
    public function guardarBorrador(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            
            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID del empleado es requerido'
                ], 400);
            }

            // Buscar o crear registro en datos
            $datosGenerales = DatosGenerales::findByEmployeeId($employeeId);
            
            if (!$datosGenerales) {
                $datosGenerales = new DatosGenerales();
                $datosGenerales->employee_id = $employeeId;
            }

            // Actualizar con los datos del formulario
            $this->mapearDatosFormulario($datosGenerales, $request);
            $datosGenerales->completado = false;
            $datosGenerales->save();

            Log::info('Borrador de Datos Generales guardado', [
                'employee_id' => $employeeId,
                'datos_id' => $datosGenerales->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Borrador guardado exitosamente',
                'data' => [
                    'id' => $datosGenerales->id,
                    'employee_id' => $employeeId,
                    'progreso' => $datosGenerales->getProgressData()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al guardar borrador de Datos Generales', [
                'error' => $e->getMessage(),
                'employee_id' => $request->input('employee_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Completar ficha de datos generales
     */
    public function completarFicha(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            
            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID del empleado es requerido'
                ], 400);
            }

            // Validar que todos los campos requeridos estén presentes
            $camposRequeridos = [
                'genero', 'ano_nacimiento', 'edad', 'estado_civil', 'nivel_estudios',
                'profesion', 'lugar_residencia', 'estrato_social', 'tipo_vivienda',
                'dependientes_economicos', 'lugar_trabajo', 'tiempo_laborado',
                'nombre_cargo', 'tipo_cargo', 'tiempo_en_cargo', 'departamento_cargo',
                'tipo_contrato', 'horas_laboradas_dia', 'tipo_salario'
            ];

            $camposFaltantes = [];
            foreach ($camposRequeridos as $campo) {
                if (!$request->has($campo) || $request->input($campo) === null || $request->input($campo) === '') {
                    $camposFaltantes[] = $campo;
                }
            }

            if (!empty($camposFaltantes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan campos requeridos: ' . implode(', ', $camposFaltantes)
                ], 400);
            }

            // Buscar o crear registro en datos
            $datosGenerales = DatosGenerales::findByEmployeeId($employeeId);
            
            if (!$datosGenerales) {
                $datosGenerales = new DatosGenerales();
                $datosGenerales->employee_id = $employeeId;
            }

            // Mapear todos los datos del formulario
            $this->mapearDatosFormulario($datosGenerales, $request);
            $datosGenerales->markAsCompleted();

            // Actualizar o crear hoja de seguimiento
            $hoja = Hoja::findOrCreateByEmployeeId($employeeId);
            $hoja->marcarDatosGeneralesCompletados();

            Log::info('Ficha de Datos Generales completada', [
                'employee_id' => $employeeId,
                'datos_id' => $datosGenerales->id,
                'hoja_id' => $hoja->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha de Datos Generales completada exitosamente',
                'data' => [
                    'id' => $datosGenerales->id,
                    'employee_id' => $employeeId,
                    'completado' => true,
                    'fecha_completado' => $datosGenerales->fecha_completado,
                    'progreso_cuestionario' => $hoja->getProgresoCuestionario(),
                    'siguiente_seccion' => $hoja->getSiguienteSeccionPendiente()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al completar ficha de Datos Generales', [
                'error' => $e->getMessage(),
                'employee_id' => $request->input('employee_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de un empleado
     */
    public function obtenerDatos($employeeId)
    {
        try {
            $datosGenerales = DatosGenerales::findByEmployeeId($employeeId);
            $hoja = Hoja::findByEmployeeId($employeeId);

            if (!$datosGenerales) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron datos para este empleado',
                    'data' => [
                        'employee_id' => $employeeId,
                        'existe' => false,
                        'completado' => false,
                        'datos' => null,
                        'progreso_cuestionario' => $hoja ? $hoja->getProgresoCuestionario() : null
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Datos obtenidos exitosamente',
                'data' => [
                    'employee_id' => $employeeId,
                    'existe' => true,
                    'completado' => $datosGenerales->isCompleted(),
                    'fecha_completado' => $datosGenerales->fecha_completado,
                    'progreso_ficha' => $datosGenerales->getProgressData(),
                    'progreso_cuestionario' => $hoja ? $hoja->getProgresoCuestionario() : null,
                    'datos' => $this->formatearDatosParaFormulario($datosGenerales)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener datos generales', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapear datos del formulario al modelo
     */
    private function mapearDatosFormulario($datosGenerales, $request)
    {
        // Pregunta 1: Sexo
        $datosGenerales->genero = (int) $request->input('genero');

        // Pregunta 2: Año de nacimiento
        $datosGenerales->ano_nacimiento = (int) $request->input('ano_nacimiento');

        // Pregunta 3: Edad
        $datosGenerales->edad = (int) $request->input('edad');

        // Pregunta 4: Estado civil
        $datosGenerales->estado_civil = (int) $request->input('estado_civil');

        // Pregunta 5: Nivel de estudios
        $datosGenerales->nivel_estudios = (int) $request->input('nivel_estudios');

        // Pregunta 6: Profesión
        $datosGenerales->profesion = $request->input('profesion');

        // Pregunta 7: Lugar de residencia
        $datosGenerales->lugar_residencia = [
            'departamento' => $request->input('residencia_departamento'),
            'ciudad' => $request->input('residencia_ciudad')
        ];

        // Pregunta 8: Estrato socioeconómico
        $datosGenerales->estrato_social = (int) $request->input('estrato_social');

        // Pregunta 9: Tipo de vivienda
        $datosGenerales->tipo_vivienda = (int) $request->input('tipo_vivienda');

        // Pregunta 10: Número de personas a cargo
        $datosGenerales->dependientes_economicos = (int) $request->input('dependientes_economicos');

        // Pregunta 11: Lugar de trabajo
        $datosGenerales->lugar_trabajo = [
            'departamento' => $request->input('trabajo_departamento'),
            'ciudad' => $request->input('trabajo_ciudad')
        ];

        // Pregunta 12: Tiempo de trabajo en la empresa
        $datosGenerales->tiempo_laborado = [
            'anos' => (int) $request->input('tiempo_laborado_anos', 0),
            'meses' => (int) $request->input('tiempo_laborado_meses', 0)
        ];

        // Pregunta 13: Nombre del cargo o puesto de trabajo
        $datosGenerales->nombre_cargo = $request->input('nombre_cargo');

        // Pregunta 14: Tipo de cargo
        $datosGenerales->tipo_cargo = (int) $request->input('tipo_cargo');

        // Pregunta 15: Tiempo de trabajo en el cargo actual
        $datosGenerales->tiempo_en_cargo = [
            'anos' => (int) $request->input('tiempo_cargo_anos', 0),
            'meses' => (int) $request->input('tiempo_cargo_meses', 0)
        ];

        // Pregunta 16: Departamento, área o sección de la empresa
        $datosGenerales->departamento_cargo = $request->input('departamento_cargo');

        // Pregunta 17: Tipo de contrato
        $datosGenerales->tipo_contrato = (int) $request->input('tipo_contrato');

        // Pregunta 18: Horas de trabajo por día
        $datosGenerales->horas_laboradas_dia = (int) $request->input('horas_laboradas_dia');

        // Pregunta 19: Tipo de salario
        $datosGenerales->tipo_salario = (int) $request->input('tipo_salario');
    }

    /**
     * Formatear datos para mostrar en el formulario
     */
    private function formatearDatosParaFormulario($datosGenerales)
    {
        $datos = $datosGenerales->toArray();
        
        // Descomponer arrays estructurados para el formulario
        if (isset($datos['lugar_residencia']) && is_array($datos['lugar_residencia'])) {
            $datos['residencia_departamento'] = $datos['lugar_residencia']['departamento'] ?? '';
            $datos['residencia_ciudad'] = $datos['lugar_residencia']['ciudad'] ?? '';
        }

        if (isset($datos['lugar_trabajo']) && is_array($datos['lugar_trabajo'])) {
            $datos['trabajo_departamento'] = $datos['lugar_trabajo']['departamento'] ?? '';
            $datos['trabajo_ciudad'] = $datos['lugar_trabajo']['ciudad'] ?? '';
        }

        if (isset($datos['tiempo_laborado']) && is_array($datos['tiempo_laborado'])) {
            $datos['tiempo_laborado_anos'] = $datos['tiempo_laborado']['anos'] ?? 0;
            $datos['tiempo_laborado_meses'] = $datos['tiempo_laborado']['meses'] ?? 0;
        }

        if (isset($datos['tiempo_en_cargo']) && is_array($datos['tiempo_en_cargo'])) {
            $datos['tiempo_cargo_anos'] = $datos['tiempo_en_cargo']['anos'] ?? 0;
            $datos['tiempo_cargo_meses'] = $datos['tiempo_en_cargo']['meses'] ?? 0;
        }

        return $datos;
    }
}
