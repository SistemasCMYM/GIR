<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregunta;
use Illuminate\Support\Facades\DB;

class IntralaboralFormaBSeeder extends Seeder
{
    /**
     * Seed the application's database with 97 questions for Intralaboral Forma B
     * According to the official manual requirements for auxiliares y operarios
     */
    public function run()
    {
        // Limpiar preguntas existentes de Forma B para evitar duplicados
        Pregunta::where('tipo', 'intralaboral_b')->delete();

        $preguntas = $this->getPreguntasFormaB();

        foreach ($preguntas as $pregunta) {
            Pregunta::create([
                'tipo' => 'intralaboral_b',
                'consecutivo' => $pregunta['consecutivo'],
                'enunciado' => $pregunta['enunciado'],
                'dimension' => $pregunta['dimension'],
                'factor' => $pregunta['factor'],
                'dominio' => $pregunta['dominio'],
                'opciones_respuesta' => [
                    ['valor' => 4, 'texto' => 'Siempre'],
                    ['valor' => 3, 'texto' => 'Casi siempre'],
                    ['valor' => 2, 'texto' => 'Algunas veces'],
                    ['valor' => 1, 'texto' => 'Casi nunca'],
                    ['valor' => 0, 'texto' => 'Nunca']
                ],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder completado: 97 preguntas Intralaboral Forma B cargadas');
    }

    /**
     * Get all 97 questions for Intralaboral Forma B
     * Organized by segments according to the official manual
     */
    private function getPreguntasFormaB()
    {
        return [
            // SEGMENTO 1: Condiciones ambientales del lugar de trabajo (Preguntas 1-12)
            [
                'consecutivo' => 1,
                'enunciado' => 'El ruido en el lugar donde trabajo es molesto',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 2,
                'enunciado' => 'En el lugar donde trabajo hace mucho frío',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 3,
                'enunciado' => 'En el lugar donde trabajo hace mucho calor',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 4,
                'enunciado' => 'El aire en el lugar donde trabajo es fresco y agradable',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 5,
                'enunciado' => 'La luz del sitio donde trabajo es agradable',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 6,
                'enunciado' => 'El espacio donde trabajo es cómodo',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 7,
                'enunciado' => 'En mi trabajo me preocupa estar expuesto a sustancias químicas que afecten mi salud',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 8,
                'enunciado' => 'Mi trabajo me exige hacer mucho esfuerzo físico',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 9,
                'enunciado' => 'Los equipos o herramientas con los que trabajo son cómodos',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 10,
                'enunciado' => 'En mi trabajo me preocupa estar expuesto a microbios, animales o plantas que afecten mi salud',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 11,
                'enunciado' => 'Me preocupa accidentarme en mi trabajo',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 12,
                'enunciado' => 'El lugar donde trabajo es limpio y ordenado',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Condiciones ambientales',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 2: Cantidad de trabajo (Preguntas 13-15)
            [
                'consecutivo' => 13,
                'enunciado' => 'Por la cantidad de trabajo que tengo debo quedarme tiempo adicional a mi horario',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Carga de trabajo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 14,
                'enunciado' => 'Me alcanza el tiempo de trabajo para tener al día mis deberes',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Carga de trabajo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 15,
                'enunciado' => 'Por la cantidad de trabajo que tengo debo trabajar sin parar',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Carga de trabajo',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 3: Esfuerzo mental (Preguntas 16-20)
            [
                'consecutivo' => 16,
                'enunciado' => 'Mi trabajo me exige hacer mucho esfuerzo mental',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas psicológicas',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 17,
                'enunciado' => 'Mi trabajo me exige estar muy concentrado',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas psicológicas',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 18,
                'enunciado' => 'Mi trabajo me exige memorizar mucha información',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas psicológicas',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 19,
                'enunciado' => 'En mi trabajo tengo que hacer cálculos matemáticos',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas psicológicas',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 20,
                'enunciado' => 'Mi trabajo requiere que me fije en pequeños detalles',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas psicológicas',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 4: Jornada de trabajo (Preguntas 21-28)
            [
                'consecutivo' => 21,
                'enunciado' => 'Trabajo en horario de noche',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 22,
                'enunciado' => 'En mi trabajo es posible tomar pausas para descansar',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 23,
                'enunciado' => 'Mi trabajo me exige laborar en días de descanso, festivos o fines de semana',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 24,
                'enunciado' => 'En mi trabajo puedo tomar fines de semana o días de descanso al mes',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 25,
                'enunciado' => 'Cuando estoy en casa sigo pensando en el trabajo',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 26,
                'enunciado' => 'Discuto con mi familia o amigos por causa de mi trabajo',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 27,
                'enunciado' => 'Debo atender cuestiones de trabajo cuando estoy en casa',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 28,
                'enunciado' => 'Por mi trabajo el tiempo que paso con mi familia y amigos es muy poco',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 5: Decisiones y control (Preguntas 29-37)
            [
                'consecutivo' => 29,
                'enunciado' => 'En mi trabajo puedo hacer cosas creativas',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 30,
                'enunciado' => 'En mi trabajo puedo desarrollar mis habilidades',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 31,
                'enunciado' => 'Mi trabajo me permite aplicar mis conocimientos',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 32,
                'enunciado' => 'Mi trabajo me permite aprender nuevas cosas',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 33,
                'enunciado' => 'Puedo decidir cuánto trabajo hago en el día',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 34,
                'enunciado' => 'Puedo decidir la velocidad a la que trabajo',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 35,
                'enunciado' => 'Puedo cambiar el orden de las actividades en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 36,
                'enunciado' => 'Puedo parar un momento para descansar cuando lo necesito',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 37,
                'enunciado' => 'Puedo decidir cuándo tomar un descanso',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],

            // SEGMENTO 6: Cambios en el trabajo (Preguntas 38-40)
            [
                'consecutivo' => 38,
                'enunciado' => 'Me explican claramente los cambios que ocurren en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 39,
                'enunciado' => 'Puedo dar sugerencias sobre los cambios que ocurren en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 40,
                'enunciado' => 'Cuando hay cambios en mi trabajo se tienen en cuenta mis ideas y sugerencias',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],

            // SEGMENTO 7: Información sobre el trabajo (Preguntas 41-45)
            [
                'consecutivo' => 41,
                'enunciado' => 'Me informan con claridad cuáles son mis funciones',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 42,
                'enunciado' => 'Me informan cuáles son las decisiones que puedo tomar en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 43,
                'enunciado' => 'Me explican claramente el resultado que debo lograr en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 44,
                'enunciado' => 'Me explican claramente el efecto de mi trabajo en la empresa',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 45,
                'enunciado' => 'Me explican claramente los objetivos de mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],

            // SEGMENTO 8: Formación y capacitación (Preguntas 46-48)
            [
                'consecutivo' => 46,
                'enunciado' => 'La empresa me permite asistir a capacitaciones relacionadas con mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Capacitación',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 47,
                'enunciado' => 'Recibo capacitación útil para hacer mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Capacitación',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 48,
                'enunciado' => 'Recibo capacitación que me ayuda a hacer mejor mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Capacitación',
                'dominio' => 'Control'
            ],

            // SEGMENTO 9: Relación con jefes (Preguntas 49-61)
            [
                'consecutivo' => 49,
                'enunciado' => 'Mi jefe ayuda a organizar mejor el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 50,
                'enunciado' => 'Mi jefe tiene en cuenta mis puntos de vista y opiniones',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 51,
                'enunciado' => 'Mi jefe me anima para hacer mejor mi trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 52,
                'enunciado' => 'Mi jefe distribuye las tareas de forma que me facilita el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 53,
                'enunciado' => 'Mi jefe me comunica a tiempo la información relacionada con el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 54,
                'enunciado' => 'La orientación que me da mi jefe me ayuda a hacer mejor el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 55,
                'enunciado' => 'Mi jefe me ayuda a progresar en el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 56,
                'enunciado' => 'Mi jefe me ayuda a sentirme bien en el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 57,
                'enunciado' => 'Mi jefe ayuda a solucionar los problemas que se presentan en el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 58,
                'enunciado' => 'Mi jefe me trata con respeto',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 59,
                'enunciado' => 'Siento que puedo confiar en mi jefe',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 60,
                'enunciado' => 'Mi jefe me escucha cuando tengo problemas de trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 61,
                'enunciado' => 'Mi jefe me brinda su apoyo cuando lo necesito',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],

            // SEGMENTO 10: Relaciones interpersonales (Preguntas 62-73)
            [
                'consecutivo' => 62,
                'enunciado' => 'Me agrada el ambiente de mi grupo de trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 63,
                'enunciado' => 'En mi grupo de trabajo me tratan de forma respetuosa',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 64,
                'enunciado' => 'Siento que puedo confiar en mis compañeros de trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 65,
                'enunciado' => 'Mis compañeros de trabajo me ayudan cuando tengo dificultades',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 66,
                'enunciado' => 'En mi trabajo las personas nos apoyamos unos a otros',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 67,
                'enunciado' => 'En mi grupo de trabajo algunas personas me maltratan',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 68,
                'enunciado' => 'Entre compañeros solucionamos los problemas de forma respetuosa',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 69,
                'enunciado' => 'Mi grupo de trabajo es muy unido',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 70,
                'enunciado' => 'En mi trabajo me hacen sentir parte del grupo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 71,
                'enunciado' => 'Cuando tenemos que realizar trabajo de grupo los compañeros colaboran',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 72,
                'enunciado' => 'Es fácil poner de acuerdo al grupo para hacer el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 73,
                'enunciado' => 'Mis compañeros de trabajo me ayudan cuando tengo problemas',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],

            // SEGMENTO 11: Retroalimentación del rendimiento (Preguntas 74-78)
            [
                'consecutivo' => 74,
                'enunciado' => 'Me informan sobre lo que hago bien en mi trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 75,
                'enunciado' => 'Me informan sobre lo que debo mejorar en mi trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 76,
                'enunciado' => 'La información que recibo sobre mi rendimiento en el trabajo es clara',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 77,
                'enunciado' => 'La forma como evalúan mi trabajo en la empresa es clara',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 78,
                'enunciado' => 'Me informan a tiempo sobre lo que debo mejorar en el trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],

            // SEGMENTO 12: Satisfacción y reconocimiento (Preguntas 79-88)
            [
                'consecutivo' => 79,
                'enunciado' => 'En mi trabajo tengo posibilidades de progresar',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 80,
                'enunciado' => 'Las personas que hacen bien el trabajo pueden progresar en la empresa',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 81,
                'enunciado' => 'La empresa se preocupa por el bienestar de los trabajadores',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 82,
                'enunciado' => 'Mi trabajo en la empresa es estable',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 83,
                'enunciado' => 'El salario que recibo es bastante bueno',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 84,
                'enunciado' => 'El salario que tengo se demora',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 85,
                'enunciado' => 'De acuerdo con mis estudios y experiencia el salario que recibo es bueno',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 86,
                'enunciado' => 'Tengo un trabajo por el cual me pagan bien',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 87,
                'enunciado' => 'Siento orgullo de trabajar en esta empresa',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 88,
                'enunciado' => 'Me siento satisfecho con mi trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],

            // SEGMENTO 13: Atención a clientes y usuarios (Preguntas 89-97) - CONDICIONAL
            [
                'consecutivo' => 89,
                'enunciado' => 'Atiendo clientes o usuarios muy enojados',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 90,
                'enunciado' => 'Atiendo clientes o usuarios muy preocupados',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 91,
                'enunciado' => 'Atiendo clientes o usuarios muy tristes',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 92,
                'enunciado' => 'Mi trabajo me exige atender personas muy enfermas',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 93,
                'enunciado' => 'Mi trabajo me exige atender personas muy necesitadas de ayuda',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 94,
                'enunciado' => 'Atiendo clientes o usuarios que me maltratan',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 95,
                'enunciado' => 'Mi trabajo me exige no demostrar lo que siento',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 96,
                'enunciado' => 'Mi trabajo me exige atender situaciones de violencia',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 97,
                'enunciado' => 'Mi trabajo me exige atender situaciones muy tristes o dolorosas',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ]
        ];
    }
}
