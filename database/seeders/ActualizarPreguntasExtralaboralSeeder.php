<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregunta;
use Illuminate\Support\Facades\DB;

class ActualizarPreguntasExtralaboralSeeder extends Seeder
{
    /**
     * Actualizar las preguntas extralaborales existentes con las opciones de respuesta
     * según Tabla 11, página 149 del manual oficial
     */
    public function run()
    {
        $this->command->info('🔄 Actualizando preguntas extralaborales con opciones de respuesta...');

        // Opciones de respuesta según el manual (Tabla 11, pág. 149)
        $opcionesRespuesta = [
            ['valor' => 4, 'texto' => 'Siempre'],
            ['valor' => 3, 'texto' => 'Casi siempre'],
            ['valor' => 2, 'texto' => 'Algunas veces'],
            ['valor' => 1, 'texto' => 'Casi nunca'],
            ['valor' => 0, 'texto' => 'Nunca']
        ];

        // Obtener todas las preguntas extralaborales
        $preguntas = Pregunta::where('tipo', 'extralaboral')->get();

        $contador = 0;
        foreach ($preguntas as $pregunta) {
            // Actualizar con opciones de respuesta si no las tiene
            if (!isset($pregunta->opciones_respuesta) || empty($pregunta->opciones_respuesta)) {
                $pregunta->opciones_respuesta = $opcionesRespuesta;
                
                // Agregar información de dimensiones y factores si no existen
                if (!isset($pregunta->dimension) || empty($pregunta->dimension)) {
                    $dimension = $this->getDimensionPorConsecutivo($pregunta->consecutivo);
                    $pregunta->dimension = $dimension['dimension'];
                    $pregunta->factor = $dimension['factor'];
                    $pregunta->dominio = 'Condiciones extralaborales';
                }
                
                $pregunta->save();
                $contador++;
            }
        }

        $this->command->info("✅ Actualizadas {$contador} preguntas extralaborales con opciones de respuesta");
    }

    /**
     * Obtener la dimensión y factor según el consecutivo de la pregunta
     */
    private function getDimensionPorConsecutivo($consecutivo)
    {
        if ($consecutivo >= 1 && $consecutivo <= 13) {
            return [
                'dimension' => 'Condiciones del lugar de vivienda y de su entorno',
                'factor' => 'Tiempo fuera del trabajo'
            ];
        } elseif ($consecutivo >= 14 && $consecutivo <= 31) {
            return [
                'dimension' => 'Relaciones familiares',
                'factor' => 'Relaciones interpersonales'
            ];
        }
        
        return [
            'dimension' => 'Condiciones extralaborales',
            'factor' => 'Factor general'
        ];
    }
}
