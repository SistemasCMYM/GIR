<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregunta;
use Illuminate\Support\Facades\DB;

class EstresPreguntasSeeder extends Seeder
{
    /**
     * Actualizar las preguntas de estrés con opciones de respuesta
     * según Tabla 4, página 384 del manual oficial
     */
    public function run()
    {
        $this->command->info('🔄 Actualizando preguntas de estrés con opciones de respuesta...');

        // Opciones de respuesta según el manual (Tabla 4, pág. 384)
        // Nota: Los valores específicos pueden variar según el manual
        $opcionesRespuesta = [
            ['valor' => 9, 'texto' => 'Siempre'],
            ['valor' => 6, 'texto' => 'Casi siempre'],
            ['valor' => 3, 'texto' => 'A veces'],
            ['valor' => 0, 'texto' => 'Nunca']
        ];

        // Obtener todas las preguntas de estrés
        $preguntas = Pregunta::where('tipo', 'estres')->get();

        if ($preguntas->count() === 0) {
            $this->command->warn('⚠️ No se encontraron preguntas de estrés. Creando las 31 preguntas...');
            $this->crearPreguntasEstres();
            $preguntas = Pregunta::where('tipo', 'estres')->get();
        }

        $contador = 0;
        foreach ($preguntas as $pregunta) {
            // Actualizar con opciones de respuesta
            $pregunta->opciones_respuesta = $opcionesRespuesta;
            
            // Agregar información de dimensiones si no existen
            if (!isset($pregunta->dimension) || empty($pregunta->dimension)) {
                $pregunta->dimension = 'Síntomas de estrés';
                $pregunta->factor = 'Indicadores fisiológicos y comportamentales';
                $pregunta->dominio = 'Estrés ocupacional';
            }
            
            $pregunta->save();
            $contador++;
        }

        $this->command->info("✅ Actualizadas {$contador} preguntas de estrés con opciones de respuesta");
    }

    /**
     * Crear las 31 preguntas de estrés según el manual oficial
     */
    private function crearPreguntasEstres()
    {
        $preguntasEstres = [
            'Dolores en el cuello y espalda o tensión muscular.',
            'Problemas gastrointestinales, úlcera péptica, acidez, problemas digestivos o del colon.',
            'Problemas respiratorios.',
            'Dolor de cabeza.',
            'Trastornos del sueño como somnolencia durante el día o desvelo en la noche.',
            'Palpitaciones en el pecho o problemas cardíacos.',
            'Cambios en el apetito.',
            'Problemas relacionados con la función de los órganos genitales (impotencia, frigidez).',
            'Dificultad en las relaciones familiares.',
            'Dificultad para permanecer quieto o dificultad para iniciar actividades.',
            'Dificultad en las relaciones con otras personas.',
            'Sensación de aislamiento y desinterés.',
            'Sentimiento de sobrecarga de trabajo.',
            'Dificultad para concentrarse, olvidos frecuentes.',
            'Aumento en el número de accidentes de trabajo.',
            'Sentimiento de frustración, de no haber hecho lo que se quería en la vida.',
            'Cansancio, tener la sensación de no haber descansado después de dormir.',
            'Disminución del rendimiento en el trabajo o poca motivación para trabajar.',
            'Consumo de drogas para aliviar la tensión o los nervios.',
            'Sentimientos de que "no vale nada", o " no sirve para nada".',
            'Consumo de bebidas alcohólicas o café o cigarrillo.',
            'Sentimiento de que está perdiendo la razón.',
            'Comportamientos rígidos, obstinación o terquedad.',
            'Sensación de no poder manejar los problemas de la vida.',
            'Dificultad para tomar decisiones.',
            'Ganas de no levantarse en la mañana.',
            'Temor a la muerte o a accidentes.',
            'Poco interés en realizar actividades grupales.',
            'Úlceras gastrointestinales.',
            'Problemas relacionados con la piel.',
            'Problemas de peso (reducción o incremento).'
        ];

        foreach ($preguntasEstres as $index => $enunciado) {
            Pregunta::create([
                'tipo' => 'estres',
                'consecutivo' => $index + 1,
                'enunciado' => $enunciado,
                'dimension' => 'Síntomas de estrés',
                'factor' => 'Indicadores fisiológicos y comportamentales',
                'dominio' => 'Estrés ocupacional',
                'opciones_respuesta' => [
                    ['valor' => 9, 'texto' => 'Siempre'],
                    ['valor' => 6, 'texto' => 'Casi siempre'],
                    ['valor' => 3, 'texto' => 'A veces'],
                    ['valor' => 0, 'texto' => 'Nunca']
                ],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Creadas 31 preguntas de estrés');
    }
}
