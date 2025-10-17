<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregunta;
use Illuminate\Support\Facades\DB;

class IntralaboralFormaASeeder extends Seeder
{
    /**
     * Seed the application's database with 123 questions for Intralaboral Forma A
     * According to the official manual requirements
     */
    public function run()
    {
        // Limpiar preguntas existentes de Forma A para evitar duplicados
        Pregunta::where('tipo', 'intralaboral_a')->delete();

        $preguntas = $this->getPreguntasFormaA();

        foreach ($preguntas as $pregunta) {
            Pregunta::create([
                'tipo' => 'intralaboral_a',
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

        $this->command->info('✅ Seeder completado: 123 preguntas Intralaboral Forma A cargadas');
    }

    /**
     * Get all 123 questions for Intralaboral Forma A
     * Organized by segments according to the official manual
     */
    private function getPreguntasFormaA()
    {
        return [
            // SEGMENTO 1: Condiciones ambientales (Preguntas 1-12)
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

            // SEGMENTO 3: Esfuerzo mental (Preguntas 16-21)
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
            [
                'consecutivo' => 21,
                'enunciado' => 'Debo responder preguntas difíciles',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas psicológicas',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 4: Responsabilidades y actividades (Preguntas 22-30)
            [
                'consecutivo' => 22,
                'enunciado' => 'En mi trabajo respondo por cosas de mucho valor',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Responsabilidad del cargo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 23,
                'enunciado' => 'En mi trabajo respondo por dinero de la empresa',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Responsabilidad del cargo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 24,
                'enunciado' => 'Como parte de mis funciones debo responder por la seguridad de otros',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Responsabilidad del cargo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 25,
                'enunciado' => 'Respondo por la salud de otras personas',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Responsabilidad del cargo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 26,
                'enunciado' => 'En el trabajo me dan órdenes contradictorias',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas ambiguas del rol',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 27,
                'enunciado' => 'En mi trabajo me piden hacer cosas innecesarias',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas ambiguas del rol',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 28,
                'enunciado' => 'En mi trabajo me piden hacer cosas que yo no aprendí a hacer',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas ambiguas del rol',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 29,
                'enunciado' => 'En mi trabajo me piden hacer cosas que van contra mis principios o valores',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas ambiguas del rol',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 30,
                'enunciado' => 'En mi trabajo debo hacer cosas que me parecen innecesarias',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas ambiguas del rol',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 5: Jornada de trabajo (Preguntas 31-38)
            [
                'consecutivo' => 31,
                'enunciado' => 'Trabajo en horario de noche',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 32,
                'enunciado' => 'En mi trabajo es posible tomar pausas para descansar',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 33,
                'enunciado' => 'Mi trabajo me exige laborar en días de descanso, festivos o fines de semana',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 34,
                'enunciado' => 'En mi trabajo puedo tomar fines de semana o días de descanso al mes',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 35,
                'enunciado' => 'Cuando estoy en casa sigo pensando en el trabajo',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 36,
                'enunciado' => 'Discuto con mi familia o amigos por causa de mi trabajo',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 37,
                'enunciado' => 'Debo atender cuestiones de trabajo cuando estoy en casa',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 38,
                'enunciado' => 'Por mi trabajo el tiempo que paso con mi familia y amigos es muy poco',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas de tiempo',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 6: Decisiones y control (Preguntas 39-47)
            [
                'consecutivo' => 39,
                'enunciado' => 'En mi trabajo puedo hacer cosas creativas',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 40,
                'enunciado' => 'En mi trabajo puedo desarrollar mis habilidades',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 41,
                'enunciado' => 'Mi trabajo me permite aplicar mis conocimientos',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 42,
                'enunciado' => 'Mi trabajo me permite aprender nuevas cosas',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 43,
                'enunciado' => 'Puedo decidir cuánto trabajo hago en el día',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 44,
                'enunciado' => 'Puedo decidir la velocidad a la que trabajo',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 45,
                'enunciado' => 'Puedo cambiar el orden de las actividades en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 46,
                'enunciado' => 'Puedo parar un momento para descansar cuando lo necesito',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 47,
                'enunciado' => 'Puedo decidir cuándo tomar un descanso',
                'dimension' => 'Control',
                'factor' => 'Control y autonomía',
                'dominio' => 'Control'
            ],

            // SEGMENTO 7: Cambios en el trabajo (Preguntas 48-52)
            [
                'consecutivo' => 48,
                'enunciado' => 'Me explican claramente los cambios que ocurren en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 49,
                'enunciado' => 'Puedo dar sugerencias sobre los cambios que ocurren en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 50,
                'enunciado' => 'Cuando hay cambios en mi trabajo se tienen en cuenta mis ideas y sugerencias',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 51,
                'enunciado' => 'Los cambios que se presentan en mi trabajo dificultan mi labor',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 52,
                'enunciado' => 'Cuando se presentan cambios en el trabajo se me informa con suficiente tiempo',
                'dimension' => 'Control',
                'factor' => 'Participación y manejo del cambio',
                'dominio' => 'Control'
            ],

            // SEGMENTO 8: Información sobre el trabajo (Preguntas 53-59)
            [
                'consecutivo' => 53,
                'enunciado' => 'Me informan con claridad cuáles son mis funciones',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 54,
                'enunciado' => 'Me informan cuáles son las decisiones que puedo tomar en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 55,
                'enunciado' => 'Me explican claramente el resultado que debo lograr en mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 56,
                'enunciado' => 'Me explican claramente el efecto de mi trabajo en la empresa',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 57,
                'enunciado' => 'Me explican claramente los objetivos de mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 58,
                'enunciado' => 'Me informan claramente con quien puedo resolver los problemas de trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 59,
                'enunciado' => 'Me informan claramente con quien debo comunicar los problemas de trabajo',
                'dimension' => 'Control',
                'factor' => 'Claridad de rol',
                'dominio' => 'Control'
            ],

            // SEGMENTO 9: Formación y capacitación (Preguntas 60-62)
            [
                'consecutivo' => 60,
                'enunciado' => 'La empresa me permite asistir a capacitaciones relacionadas con mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Capacitación',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 61,
                'enunciado' => 'Recibo capacitación útil para hacer mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Capacitación',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 62,
                'enunciado' => 'Recibo capacitación que me ayuda a hacer mejor mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Capacitación',
                'dominio' => 'Control'
            ],

            // SEGMENTO 10: Relación con jefes (Preguntas 63-75)
            [
                'consecutivo' => 63,
                'enunciado' => 'Mi jefe ayuda a organizar mejor el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 64,
                'enunciado' => 'Mi jefe tiene en cuenta mis puntos de vista y opiniones',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 65,
                'enunciado' => 'Mi jefe me anima para hacer mejor mi trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 66,
                'enunciado' => 'Mi jefe distribuye las tareas de forma que me facilita el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 67,
                'enunciado' => 'Mi jefe me comunica a tiempo la información relacionada con el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 68,
                'enunciado' => 'La orientación que me da mi jefe me ayuda a hacer mejor el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 69,
                'enunciado' => 'Mi jefe me ayuda a progresar en el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 70,
                'enunciado' => 'Mi jefe me ayuda a sentirme bien en el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 71,
                'enunciado' => 'Mi jefe ayuda a solucionar los problemas que se presentan en el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 72,
                'enunciado' => 'Mi jefe me trata con respeto',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 73,
                'enunciado' => 'Siento que puedo confiar en mi jefe',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 74,
                'enunciado' => 'Mi jefe me escucha cuando tengo problemas de trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 75,
                'enunciado' => 'Mi jefe me brinda su apoyo cuando lo necesito',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de jefes',
                'dominio' => 'Apoyo social'
            ],

            // SEGMENTO 11: Relaciones interpersonales (Preguntas 76-89)
            [
                'consecutivo' => 76,
                'enunciado' => 'Me agrada el ambiente de mi grupo de trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 77,
                'enunciado' => 'En mi grupo de trabajo me tratan de forma respetuosa',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 78,
                'enunciado' => 'Siento que puedo confiar en mis compañeros de trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 79,
                'enunciado' => 'Mis compañeros de trabajo me ayudan cuando tengo dificultades',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 80,
                'enunciado' => 'En mi trabajo las personas nos apoyamos unos a otros',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 81,
                'enunciado' => 'En mi grupo de trabajo algunas personas me maltratan',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 82,
                'enunciado' => 'Entre compañeros solucionamos los problemas de forma respetuosa',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 83,
                'enunciado' => 'Mi grupo de trabajo es muy unido',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 84,
                'enunciado' => 'En mi trabajo me hacen sentir parte del grupo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 85,
                'enunciado' => 'Cuando tenemos que realizar trabajo de grupo los compañeros colaboran',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 86,
                'enunciado' => 'Es fácil poner de acuerdo al grupo para hacer el trabajo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 87,
                'enunciado' => 'Mis compañeros de trabajo me ayudan cuando tengo problemas',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 88,
                'enunciado' => 'Mis compañeros de trabajo me escuchan cuando les hablo',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],
            [
                'consecutivo' => 89,
                'enunciado' => 'Mis compañeros de trabajo me brindan su apoyo cuando lo necesito',
                'dimension' => 'Apoyo social',
                'factor' => 'Apoyo social de compañeros',
                'dominio' => 'Apoyo social'
            ],

            // SEGMENTO 12: Retroalimentación del rendimiento (Preguntas 90-94)
            [
                'consecutivo' => 90,
                'enunciado' => 'Me informan sobre lo que hago bien en mi trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 91,
                'enunciado' => 'Me informan sobre lo que debo mejorar en mi trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 92,
                'enunciado' => 'La información que recibo sobre mi rendimiento en el trabajo es clara',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 93,
                'enunciado' => 'La forma como evalúan mi trabajo en la empresa es clara',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 94,
                'enunciado' => 'Me informan a tiempo sobre lo que debo mejorar en el trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Retroalimentación del desempeño',
                'dominio' => 'Recompensas'
            ],

            // SEGMENTO 13: Satisfacción y reconocimiento (Preguntas 95-105)
            [
                'consecutivo' => 95,
                'enunciado' => 'En mi trabajo tengo posibilidades de progresar',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 96,
                'enunciado' => 'Las personas que hacen bien el trabajo pueden progresar en la empresa',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 97,
                'enunciado' => 'La empresa se preocupa por el bienestar de los trabajadores',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 98,
                'enunciado' => 'Mi trabajo en la empresa es estable',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 99,
                'enunciado' => 'El salario que recibo es bastante bueno',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 100,
                'enunciado' => 'El salario que tengo se demora',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 101,
                'enunciado' => 'De acuerdo con mis estudios y experiencia el salario que recibo es bueno',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 102,
                'enunciado' => 'Tengo un trabajo por el cual me pagan bien',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 103,
                'enunciado' => 'Siento orgullo de trabajar en esta empresa',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 104,
                'enunciado' => 'Hablo positivamente de la empresa con otras personas',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 105,
                'enunciado' => 'Me siento satisfecho con mi trabajo',
                'dimension' => 'Recompensas',
                'factor' => 'Reconocimiento y compensación',
                'dominio' => 'Recompensas'
            ],

            // SEGMENTO 14: Atención a clientes y usuarios (Preguntas 106-114) - CONDICIONAL
            [
                'consecutivo' => 106,
                'enunciado' => 'Atiendo clientes o usuarios muy enojados',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 107,
                'enunciado' => 'Atiendo clientes o usuarios muy preocupados',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 108,
                'enunciado' => 'Atiendo clientes o usuarios muy tristes',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 109,
                'enunciado' => 'Mi trabajo me exige atender personas muy enfermas',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 110,
                'enunciado' => 'Mi trabajo me exige atender personas muy necesitadas de ayuda',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 111,
                'enunciado' => 'Atiendo clientes o usuarios que me maltratan',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 112,
                'enunciado' => 'Mi trabajo me exige no demostrar lo que siento',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 113,
                'enunciado' => 'Mi trabajo me exige atender situaciones de violencia',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],
            [
                'consecutivo' => 114,
                'enunciado' => 'Mi trabajo me exige atender situaciones muy tristes o dolorosas',
                'dimension' => 'Condiciones del trabajo',
                'factor' => 'Demandas emocionales',
                'dominio' => 'Condiciones intralaborales'
            ],

            // SEGMENTO 15: Supervisión de personal (Preguntas 115-123) - CONDICIONAL
            [
                'consecutivo' => 115,
                'enunciado' => 'Puedo influir sobre la cantidad de trabajo que me asignan',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 116,
                'enunciado' => 'Puedo influir sobre la velocidad a la que debo hacer mi trabajo',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 117,
                'enunciado' => 'Puedo influir sobre el orden de mis actividades',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 118,
                'enunciado' => 'Puedo influir en la definición de los procedimientos de trabajo',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 119,
                'enunciado' => 'Puedo parar para descansar cuando lo necesito',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 120,
                'enunciado' => 'Puedo decidir cuánto tiempo me tomo para hacer una actividad',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 121,
                'enunciado' => 'En mi trabajo puedo aspirar a un mejor puesto',
                'dimension' => 'Recompensas',
                'factor' => 'Oportunidades de desarrollo',
                'dominio' => 'Recompensas'
            ],
            [
                'consecutivo' => 122,
                'enunciado' => 'Durante mi jornada de trabajo puedo tomar pausas cuando las necesito',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ],
            [
                'consecutivo' => 123,
                'enunciado' => 'Puedo decidir el ritmo de trabajo',
                'dimension' => 'Control',
                'factor' => 'Control sobre el trabajo',
                'dominio' => 'Control'
            ]
        ];
    }
}
