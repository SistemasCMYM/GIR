<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregunta;

class PreguntasPsicosocialSeeder extends Seeder
{
    /**
     * Seed de preguntas para la Batería de Riesgo Psicosocial
     * Basado en el documento oficial: bateria-instrumento-evaluacion-factores-riesgo-psicosocial.pdf
     */
    public function run()
    {
        $this->command->info('🌱 Iniciando seeding de preguntas psicosociales...');

        // Truncar tabla si ya existen datos
        try {
            Pregunta::truncate();
            $this->command->info('   📋 Tabla de preguntas limpiada');
        } catch (\Exception $e) {
            $this->command->info('   ⚠️  No se pudo limpiar la tabla: ' . $e->getMessage());
        }

        // Crear preguntas de ejemplo para cada tipo
        $this->crearPreguntasIntralaboralA();
        $this->crearPreguntasIntralaboralB();
        $this->crearPreguntasExtralaboral();
        $this->crearPreguntasEstres();

        $total = Pregunta::count();
        $this->command->info("✅ Preguntas de la Batería Psicosocial creadas exitosamente");
        $this->command->info("📊 Total creadas: {$total} preguntas");
    }

    private function crearPreguntasIntralaboralA()
    {
        $this->command->info('   📝 Creando preguntas Intralaboral Forma A...');
        
        // Preguntas Intralaboral Forma A (ejemplo de las primeras 20)
        $preguntasIntralaboralA = [
            1 => 'El jefe inmediato tiene en cuenta mis puntos de vista y opiniones',
            2 => 'Estoy informado sobre los objetivos, metas y políticas de la empresa',
            3 => 'El jefe inmediato me comunica oportunamente los cambios en el trabajo',
            4 => 'Puedo confiar en el jefe inmediato',
            5 => 'El jefe inmediato me escucha cuando le expreso mis ideas para mejorar las actividades del trabajo',
            6 => 'El jefe inmediato me ayuda a realizar mejor el trabajo',
            7 => 'El jefe inmediato me trata con respeto',
            8 => 'Siento que puedo confiar en mis compañeros de trabajo',
            9 => 'Entre compañeros solucionamos los problemas de forma respetuosa',
            10 => 'En mi grupo de trabajo me tratan de forma respetuosa',
            11 => 'Cuando tenemos reuniones de trabajo todos participamos en las decisiones',
            12 => 'Mi grupo de trabajo se mantiene unido',
            13 => 'En mi grupo de trabajo se presentan conflictos',
            14 => 'Las personas en el trabajo me hacen sentir parte del grupo',
            15 => 'Cuando trabajo en grupo los resultados son mejores',
            16 => 'Me siento a gusto con mis compañeros de trabajo',
            17 => 'En mi trabajo me tratan de forma respetuosa',
            18 => 'Recibo buen trato de parte de mis compañeros de trabajo',
            19 => 'En mi trabajo tengo buenas relaciones con mis compañeros',
            20 => 'En mi trabajo se valoran mis ideas y opiniones'
        ];

        foreach ($preguntasIntralaboralA as $consecutivo => $enunciado) {
            try {
                Pregunta::create([
                    'tipo' => 'intralaboral_a',
                    'enunciado' => $enunciado,
                    'consecutivo' => $consecutivo
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error creando pregunta {$consecutivo}: " . $e->getMessage());
            }
        }
        
        $this->command->info('   ✅ Preguntas Intralaboral A creadas: ' . count($preguntasIntralaboralA));
    }

    private function crearPreguntasIntralaboralB()
    {
        $this->command->info('   📝 Creando preguntas Intralaboral Forma B...');
        
        // Preguntas Intralaboral Forma B (ejemplo de las primeras 20)
        $preguntasIntralaboralB = [
            1 => 'Me informan con claridad cuáles son mis funciones',
            2 => 'Me informan cuáles son las decisiones que puedo tomar en mi trabajo',
            3 => 'Me explican los resultados que debo lograr en mi trabajo',
            4 => 'Me explican los objetivos del trabajo',
            5 => 'Me informan quien es mi jefe inmediato',
            6 => 'Me informan con quien puedo resolver los problemas de trabajo',
            7 => 'Me permiten asistir a capacitaciones relacionadas con mi trabajo',
            8 => 'Recibo capacitación útil para hacer mi trabajo',
            9 => 'Recibo capacitación que me ayuda a hacer mejor el trabajo',
            10 => 'Me permiten participar en las decisiones que afectan mi trabajo',
            11 => 'En mi trabajo puedo hacer sugerencias para mejorar las actividades',
            12 => 'Mi opinión es tenida en cuenta para los cambios en el trabajo',
            13 => 'Me informan sobre lo que hago bien en mi trabajo',
            14 => 'Me informan sobre lo que debo mejorar en mi trabajo',
            15 => 'La información que recibo sobre mi rendimiento en el trabajo es clara',
            16 => 'Me dan a conocer los resultados de mi trabajo',
            17 => 'Me felicitan por los resultados de mi trabajo',
            18 => 'Cuando hago bien mi trabajo recibo el reconocimiento de mis jefes',
            19 => 'El pago que recibo es el que me ofrecieron',
            20 => 'El pago que recibo es el que merezco por el trabajo que realizo'
        ];

        foreach ($preguntasIntralaboralB as $consecutivo => $enunciado) {
            try {
                Pregunta::create([
                    'tipo' => 'intralaboral_b',
                    'enunciado' => $enunciado,
                    'consecutivo' => $consecutivo
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error creando pregunta {$consecutivo}: " . $e->getMessage());
            }
        }
        
        $this->command->info('   ✅ Preguntas Intralaboral B creadas: ' . count($preguntasIntralaboralB));
    }

    private function crearPreguntasExtralaboral()
    {
        $this->command->info('   📝 Creando preguntas Extralaboral...');
        
        // Preguntas Extralaboral (todas las 31)
        $preguntasExtralaboral = [
            1 => 'Es fácil transportarme entre mi casa y el trabajo',
            2 => 'Tengo que tomar varios medios de transporte para llegar a mi lugar de trabajo',
            3 => 'Paso mucho tiempo viajando de ida y regreso al trabajo',
            4 => 'Me transporto cómodamente entre mi casa y el trabajo',
            5 => 'La zona donde vivo es segura',
            6 => 'En la zona donde vivo se presentan hurtos y mucha delincuencia',
            7 => 'Desde donde vivo me es fácil llegar al centro médico donde me atienden',
            8 => 'Cerca al lugar donde vivo las vías están en buenas condiciones',
            9 => 'Cerca al lugar donde vivo encuentro fácilmente transporte',
            10 => 'Las condiciones de mi vivienda son buenas',
            11 => 'En mi vivienda hay condiciones físicas que me molestan (ruido, olores, iluminación, ventilación, espacio)',
            12 => 'Tengo privacidad en mi vivienda',
            13 => 'Me queda fácil llegar a la vivienda',
            14 => 'Me siento a gusto con las personas con las que vivo',
            15 => 'En mi vivienda se presentan conflictos',
            16 => 'Las relaciones en mi hogar son cordiales',
            17 => 'Puedo descansar cuando llego a la casa',
            18 => 'En mi casa tengo espacios suficientes para descansar',
            19 => 'Las actividades que hago en mi tiempo libre son de mi agrado',
            20 => 'Tengo tiempo suficiente para atender mis asuntos personales y del hogar',
            21 => 'Tengo tiempo suficiente para atender a mi familia',
            22 => 'Tengo tiempo suficiente para realizar actividades de descanso',
            23 => 'Mis horarios de trabajo me permiten atender mis asuntos personales',
            24 => 'Mis horarios de trabajo me permiten disponer de tiempo para descansar',
            25 => 'Mis horarios de trabajo me permiten compartir con mi familia o amigos',
            26 => 'Tengo buenas relaciones con mis vecinos',
            27 => 'Las personas cercanas me brindan el apoyo que necesito',
            28 => 'Es fácil contactarme con las personas cercanas',
            29 => 'Las personas cercanas me hacen sentir querido',
            30 => 'Las personas cercanas están dispuestas a escucharme',
            31 => 'Cuento con el apoyo de mi familia cuando tengo dificultades'
        ];

        foreach ($preguntasExtralaboral as $consecutivo => $enunciado) {
            try {
                Pregunta::create([
                    'tipo' => 'extralaboral',
                    'enunciado' => $enunciado,
                    'consecutivo' => $consecutivo
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error creando pregunta {$consecutivo}: " . $e->getMessage());
            }
        }
        
        $this->command->info('   ✅ Preguntas Extralaboral creadas: ' . count($preguntasExtralaboral));
    }

    private function crearPreguntasEstres()
    {
        $this->command->info('   📝 Creando preguntas de Estrés...');
        
        // Preguntas de Estrés (todas las 31)
        $preguntasEstres = [
            1 => 'Dolores en el cuello y espalda o tensión muscular',
            2 => 'Problemas gastrointestinales, úlcera péptica, acidez, problemas digestivos o del colon',
            3 => 'Problemas respiratorios',
            4 => 'Dolor de cabeza',
            5 => 'Trastornos del sueño como somnolencia durante el día o desvelo en la noche',
            6 => 'Palpitaciones en el pecho o problemas cardíacos',
            7 => 'Cambios fuertes del apetito',
            8 => 'Problemas relacionados con la función de los órganos genitales (impotencia, frigidez)',
            9 => 'Dificultad en las relaciones familiares',
            10 => 'Dificultad para permanecer quieto o dificultad para iniciar actividades',
            11 => 'Aislamiento de los demás',
            12 => 'Conflictos con otras personas',
            13 => 'Dificultad para expresar las opiniones',
            14 => 'Ausentismo del trabajo',
            15 => 'Dificultad para concentrarse',
            16 => 'Aumento en el número de accidentes de trabajo',
            17 => 'Sentimiento de sobrecarga de trabajo',
            18 => 'Dificultad para tomar decisiones',
            19 => 'Deseo de no asistir al trabajo',
            20 => 'Disminución del rendimiento en el trabajo o poca motivación para trabajar',
            21 => 'Sentimiento de frustración, de no haber hecho lo que se quería en la vida',
            22 => 'Sentimiento de cansancio, tedio o desgano',
            23 => 'Disminución del gusto por actividades que generalmente me agradan',
            24 => 'Sentimiento de ansiedad',
            25 => 'Sentimiento de depresión (tristeza, ganas de llorar, melancolía)',
            26 => 'Sentimiento de angustia, preocupación o miedo',
            27 => 'Sentimiento de irritabilidad, mal genio o agresividad',
            28 => 'Sentimiento de ira, cólera o rabia',
            29 => 'Sentimiento de soledad',
            30 => 'Sentimiento de nerviosismo',
            31 => 'Sentimiento de alegría y bienestar'
        ];

        foreach ($preguntasEstres as $consecutivo => $enunciado) {
            try {
                Pregunta::create([
                    'tipo' => 'estres',
                    'enunciado' => $enunciado,
                    'consecutivo' => $consecutivo
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error creando pregunta {$consecutivo}: " . $e->getMessage());
            }
        }
        
        $this->command->info('   ✅ Preguntas de Estrés creadas: ' . count($preguntasEstres));
    }
}
