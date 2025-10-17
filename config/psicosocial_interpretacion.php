<?php
return [
    'niveles_riesgo' => [
        'sin_riesgo' => [
            'color' => 'success',
            'descripcion' => 'Ausencia de riesgo o riesgo tan bajo que no amerita desarrollar actividades de intervención. Las dimensiones y dominios que se encuentren en esta categoría serán objeto de acciones o programas de promoción.',
            'accion' => 'Mantener los factores psicosociales en los niveles de exposición actuales. Implementar programas de promoción y prevención de la salud mental y factores protectores.'
        ],
        'bajo' => [
            'color' => 'info',
            'descripcion' => 'No se espera que los factores psicosociales que obtengan puntuaciones de este nivel estén relacionados con síntomas o respuestas de estrés significativas. Las dimensiones y dominios que se encuentren bajo esta categoría serán objeto de acciones o programas de intervención, a fin de mantenerlos en los niveles de riesgo más bajos posibles.',
            'accion' => 'Realizar actividades de promoción orientadas a fortalecer los factores protectores y prevenir el aumento del nivel de riesgo. Monitorear estos factores periódicamente.'
        ],
        'medio' => [
            'color' => 'warning',
            'descripcion' => 'Nivel de riesgo en el que se esperaría una respuesta de estrés moderada. Las dimensiones y dominios que se encuentren bajo esta categoría ameritan observación y acciones sistemáticas de intervención para prevenir efectos perjudiciales en la salud.',
            'accion' => 'Implementar actividades de intervención focalizadas en estos factores de riesgo. Incluir a la población en programas de vigilancia epidemiológica. Realizar seguimiento periódico.'
        ],
        'alto' => [
            'color' => 'danger',
            'descripcion' => 'Nivel de riesgo que tiene una importante posibilidad de asociación con respuestas de estrés alto y por tanto, las dimensiones y dominios que se encuentren bajo esta categoría requieren intervención en el marco de un sistema de vigilancia epidemiológica.',
            'accion' => 'Desarrollar actividades de intervención inmediatas incluidas en el sistema de vigilancia epidemiológica. Considerar el factor como prioritario para la intervención. Evaluar frecuentemente los resultados de las intervenciones.'
        ],
        'muy_alto' => [
            'color' => 'dark',
            'descripcion' => 'Nivel de riesgo con amplia posibilidad de asociarse a respuestas muy altas de estrés. Por consiguiente las dimensiones y dominios que se encuentren bajo esta categoría requieren intervención inmediata en el marco de un sistema de vigilancia epidemiológica.',
            'accion' => 'Intervención inmediata. Incluir a los trabajadores en el programa de vigilancia epidemiológica de factores de riesgo psicosocial. Realizar actividades específicas de intervención para estos factores. Monitorear mensualmente los cambios en estos factores.'
        ]
    ],
    
    'dimensiones' => [
        'liderazgo_relaciones_sociales' => [
            'nombre' => 'Liderazgo y Relaciones Sociales',
            'descripcion' => 'Este dominio hace referencia a un tipo particular de relación social que se establece entre los superiores jerárquicos y sus colaboradores y cuyas características influyen en la forma de trabajar y en el ambiente de relaciones de un área. El concepto de relaciones sociales en el trabajo indica la interacción que se establece con otras personas en el contexto laboral y abarca aspectos como la posibilidad de contactos, las características de las interacciones, los aspectos funcionales de las interacciones como la retroalimentación del desempeño, el trabajo en equipo y el apoyo social, y los aspectos emocionales, como la cohesión.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener las actuales prácticas de liderazgo y fomentar el reconocimiento de los líderes positivos en la organización.',
                'bajo' => 'Fortalecer las competencias de liderazgo mediante talleres de habilidades directivas y comunicación asertiva.',
                'medio' => 'Implementar programas de desarrollo de habilidades sociales y trabajo en equipo. Establecer canales claros de comunicación.',
                'alto' => 'Intervenir con programas específicos de liderazgo transformacional. Evaluar y ajustar estilos de dirección.',
                'muy_alto' => 'Restructurar procesos de comunicación y dirección. Capacitación intensiva en liderazgo y resolución de conflictos. Considerar coaching ejecutivo para líderes.'
            ]
        ],
        'control_sobre_trabajo' => [
            'nombre' => 'Control sobre el Trabajo',
            'descripcion' => 'Este dominio se refiere a la posibilidad que el trabajo ofrece al individuo para influir y tomar decisiones sobre los diversos aspectos que intervienen en su realización. La iniciativa y autonomía, el uso y desarrollo de habilidades y conocimientos, la participación y manejo del cambio, la claridad de rol y la capacitación son aspectos que le dan al individuo la posibilidad de influir sobre su trabajo.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener los niveles actuales de autonomía y control. Continuar con programas de capacitación y desarrollo de competencias.',
                'bajo' => 'Reforzar la claridad de roles y responsabilidades. Mantener programas de capacitación actualizados.',
                'medio' => 'Revisar y ajustar los niveles de autonomía en la toma de decisiones. Implementar programas de gestión del cambio y participación.',
                'alto' => 'Rediseñar roles y funciones para aumentar el control sobre el trabajo. Mejorar sistemas de participación en toma de decisiones.',
                'muy_alto' => 'Intervención inmediata en la estructura de toma de decisiones. Rediseño de puestos de trabajo. Capacitación intensiva y desarrollo de competencias específicas.'
            ]
        ],
        'demandas_trabajo' => [
            'nombre' => 'Demandas del Trabajo',
            'descripcion' => 'Las demandas del trabajo se refieren a las exigencias que el trabajo impone al individuo. Pueden ser de diversa naturaleza, como cuantitativas, cognitivas o mentales, emocionales, de responsabilidad, del ambiente físico laboral y de la jornada de trabajo.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener equilibrio entre demandas y recursos. Implementar prácticas de trabajo saludable.',
                'bajo' => 'Monitorear periódicamente la carga de trabajo. Mantener estrategias de distribución equitativa de tareas.',
                'medio' => 'Revisar y ajustar cargas de trabajo. Implementar técnicas de manejo del tiempo y priorización.',
                'alto' => 'Redistribuir cargas laborales. Evaluar la necesidad de personal adicional. Implementar pausas activas regulares.',
                'muy_alto' => 'Rediseño inmediato de procesos de trabajo. Revisar dotación de personal y distribución de funciones. Implementar programas de manejo del estrés.'
            ]
        ],
        'recompensas' => [
            'nombre' => 'Recompensas',
            'descripcion' => 'Este término trata de la retribución que el trabajador obtiene a cambio de sus contribuciones o esfuerzos laborales. Este dominio comprende diversos tipos de retribución: la financiera (compensación económica por el trabajo), de estima (compensación psicológica, que comprende el reconocimiento del grupo social y el trato justo en el trabajo) y de posibilidades de promoción y seguridad en el trabajo. Otras formas de retribución que se consideran en este dominio comprenden las posibilidades de educación, la satisfacción y la identificación con el trabajo y con la organización.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener los sistemas actuales de reconocimiento y compensación. Fortalecer programas de bienestar.',
                'bajo' => 'Revisar periódicamente las políticas de compensación. Implementar sistemas de reconocimiento no monetario.',
                'medio' => 'Implementar programas de reconocimiento del desempeño. Revisar escala salarial y políticas de promoción.',
                'alto' => 'Rediseñar sistemas de compensación y beneficios. Implementar programas formales de reconocimiento y plan de carrera.',
                'muy_alto' => 'Reestructurar urgentemente los sistemas de compensación, reconocimiento y desarrollo profesional. Implementar planes de retención de talento.'
            ]
        ],
        'extralaboral' => [
            'nombre' => 'Factores Extralaborales',
            'descripcion' => 'Comprenden los aspectos del entorno familiar, social y económico del trabajador. A su vez, abarcan las condiciones del lugar de vivienda, que pueden influir en la salud y bienestar del individuo.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener los programas de equilibrio vida-trabajo. Promover actividades de integración familiar.',
                'bajo' => 'Implementar políticas de flexibilidad laboral. Fomentar actividades recreativas y familiares.',
                'medio' => 'Revisar horarios de trabajo y desplazamientos. Implementar programas de bienestar familiar.',
                'alto' => 'Implementar programas de apoyo para situaciones personales críticas. Revisar y ajustar políticas de conciliación.',
                'muy_alto' => 'Intervención inmediata en políticas de conciliación. Considerar teletrabajo o flexibilidad horaria. Proporcionar apoyo psicológico especializado.'
            ]
        ],
        'estres' => [
            'nombre' => 'Estrés',
            'descripcion' => 'El estrés es la respuesta de un trabajador tanto a nivel fisiológico, psicológico como conductual, en su intento de adaptarse a las demandas resultantes de la interacción de sus condiciones individuales, intralaborales y extralaborales.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener programas de promoción de la salud mental. Fomentar hábitos saludables.',
                'bajo' => 'Implementar programas preventivos de manejo del estrés. Fomentar técnicas de relajación.',
                'medio' => 'Implementar talleres de manejo del estrés. Revisar factores organizacionales contribuyentes.',
                'alto' => 'Intervención especializada en manejo del estrés. Incluir en vigilancia epidemiológica. Revisar condiciones laborales.',
                'muy_alto' => 'Intervención inmediata por especialistas en salud mental. Revisión profunda de condiciones de trabajo. Seguimiento individual.'
            ]
        ],
        'total' => [
            'nombre' => 'Riesgo Psicosocial Total',
            'descripcion' => 'El puntaje total representa la valoración global del riesgo psicosocial considerando todos los dominios evaluados.',
            'recomendaciones' => [
                'sin_riesgo' => 'Mantener las condiciones actuales y realizar seguimiento periódico. Fortalecer factores protectores.',
                'bajo' => 'Implementar acciones preventivas generales. Monitorear periódicamente los factores de riesgo identificados.',
                'medio' => 'Desarrollar un plan de intervención integral. Priorizar los dominios con mayor nivel de riesgo.',
                'alto' => 'Implementar un plan de intervención inmediato. Incluir a la población en vigilancia epidemiológica.',
                'muy_alto' => 'Intervención urgente y multidimensional. Rediseño de condiciones de trabajo críticas. Seguimiento continuo.'
            ]
        ]
    ]
];
