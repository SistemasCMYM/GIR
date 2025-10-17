<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de la Batería de Riesgo Psicosocial
    |--------------------------------------------------------------------------
    |
    | Configuración basada en el documento oficial del Ministerio de Protección Social
    | y la implementación original de Padduk Solutions / CMYM
    |
    */

    // Configuración de preguntas por dimensión intralaboral
    'preguntas_intralaboral' => [
        'A' => [
            'caracteristicasDelLiderazgo' => [63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75],
            'relacionesSocialesEnElTrabajo' => [76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            'retroalimentacionDeDesempeno' => [90, 91, 92, 93, 94],
            'relacionConLosColaboradores' => [115, 116, 117, 118, 119, 120, 121, 122, 123],
            'claridadDelRol' => [53, 54, 55, 56, 57, 58, 59],
            'capacitacion' => [60, 61, 62],
            'participacionManejoDelCambio' => [48, 49, 50, 51],
            'desarrolloHabilidades' => [39, 40, 41, 42],
            'autonomiaSobreElTrabajo' => [44, 45, 46],
            'ambientalesYEsfuerzoFisico' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'demandasEmocionales' => [106, 107, 108, 109, 110, 111, 112, 113, 114],
            'demandasCuantitativas' => [13, 14, 15, 32, 43, 47],
            'trabajoSobreExtralaboral' => [35, 36, 37, 38],
            'exigenciasResponsabilidadCargo' => [19, 22, 23, 24, 25, 26],
            'demandasCargaMental' => [16, 17, 18, 20, 21],
            'consistenciaDelRol' => [27, 28, 29, 30, 52],
            'demandasJornadaTrabajo' => [31, 33, 34],
            'recompensasPorTrabajo' => [95, 102, 103, 104, 105],
            'reconocimientoYCompensacion' => [96, 97, 98, 99, 100, 101]
        ],
        'B' => [
            'caracteristicasDelLiderazgo' => [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61],
            'relacionesSocialesEnElTrabajo' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73],
            'retroalimentacionDeDesempeno' => [74, 75, 76, 77, 78],
            'claridadDelRol' => [41, 42, 43, 44, 45],
            'capacitacion' => [46, 47, 48],
            'participacionManejoDelCambio' => [38, 39, 40],
            'desarrolloHabilidades' => [29, 30, 31, 32],
            'autonomiaSobreElTrabajo' => [34, 35, 36],
            'ambientalesYEsfuerzoFisico' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'demandasEmocionales' => [89, 90, 91, 92, 93, 94, 95, 96, 97],
            'demandasCuantitativas' => [13, 14, 15],
            'trabajoSobreExtralaboral' => [25, 26, 27, 28],
            'demandasCargaMental' => [16, 17, 18, 19, 20],
            'demandasJornadaTrabajo' => [21, 22, 23, 24, 33, 37],
            'recompensasPorTrabajo' => [85, 86, 87, 88],
            'reconocimientoYCompensacion' => [79, 80, 81, 82, 83, 84]
        ]
    ],

    // Configuración de dominios intralaboral
    'dominios_intralaboral' => [
        'A' => [
            'liderazgo' => [63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 115, 116, 117, 118, 119, 120, 121, 122, 123],
            'control_trabajo' => [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 48, 49, 50, 51, 39, 40, 41, 42, 44, 45, 46],
            'demandas_trabajo' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 106, 107, 108, 109, 110, 111, 112, 113, 114, 13, 14, 15, 32, 43, 47, 35, 36, 37, 38, 19, 22, 23, 24, 25, 26, 16, 17, 18, 20, 21, 27, 28, 29, 30, 52, 31, 33, 34],
            'recompensas' => [95, 102, 103, 104, 105, 96, 97, 98, 99, 100, 101]
        ],
        'B' => [
            'liderazgo' => [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78],
            'control_trabajo' => [41, 42, 43, 44, 45, 46, 47, 48, 38, 39, 40, 29, 30, 31, 32, 34, 35, 36],
            'demandas_trabajo' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 89, 90, 91, 92, 93, 94, 95, 96, 97, 13, 14, 15, 25, 26, 27, 28, 16, 17, 18, 19, 20, 21, 22, 23, 24, 33, 37],
            'recompensas' => [85, 86, 87, 88, 79, 80, 81, 82, 83, 84]
        ]
    ],

    // Configuración de preguntas extralaboral
    'preguntas_extralaboral' => [
        'tiempoFueraTrabajo' => [14, 15, 16, 17],
        'relacionesFamiliares' => [22, 25, 27],
        'comunicacion' => [18, 19, 20, 21, 23],
        'situacionEconomica' => [29, 30, 31],
        'caracteristicasVivienda' => [5, 6, 7, 8, 9, 10, 11, 12, 13],
        'influenciaDelTrabajo' => [24, 26, 28],
        'desplazamiento' => [1, 2, 3, 4]
    ],

    // Configuración de preguntas estrés
    'preguntas_estres' => [
        'escalaA' => [1, 2, 3, 9, 13, 14, 15, 23, 24],
        'escalaB' => [4, 5, 6, 10, 11, 16, 17, 18, 19, 25, 26, 27, 28],
        'escalaC' => [7, 8, 12, 20, 21, 22, 29, 30, 31]
    ],

    // Configuración de calificación ascendente/descendente
    'calificacion' => [
        'A' => [
            'ascendente' => [4, 5, 6, 9, 12, 14, 32, 34, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105],
            'descendente' => [1, 2, 3, 7, 8, 10, 11, 13, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 33, 35, 36, 37, 38, 52, 80, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123]
        ],
        'B' => [
            'ascendente' => [4, 5, 6, 9, 12, 14, 22, 24, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 97],
            'descendente' => [1, 2, 3, 7, 8, 10, 11, 13, 15, 16, 17, 18, 19, 20, 21, 23, 25, 26, 27, 28, 66, 89, 90, 91, 92, 93, 94, 95, 96]
        ],
        'EXTRALABORAL' => [
            'ascendente' => [1, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 27, 29],
            'descendente' => [2, 3, 6, 24, 26, 28, 30, 31]
        ]
    ],

    // Factores de transformación
    'factores_transformacion' => [
        'intralaboral' => [
            'A' => [
                'caracteristicasDelLiderazgo' => 52,
                'relacionesSocialesEnElTrabajo' => 56,
                'retroalimentacionDeDesempeno' => 20,
                'relacionConLosColaboradores' => 36,
                'claridadDelRol' => 28,
                'capacitacion' => 12,
                'participacionManejoDelCambio' => 16,
                'desarrolloHabilidades' => 16,
                'autonomiaSobreElTrabajo' => 12,
                'ambientalesYEsfuerzoFisico' => 48,
                'demandasEmocionales' => 36,
                'demandasCuantitativas' => 24,
                'trabajoSobreExtralaboral' => 16,
                'exigenciasResponsabilidadCargo' => 24,
                'demandasCargaMental' => 20,
                'consistenciaDelRol' => 20,
                'demandasJornadaTrabajo' => 12,
                'recompensasPorTrabajo' => 20,
                'reconocimientoYCompensacion' => 24,
                'total' => 492
            ],
            'B' => [
                'caracteristicasDelLiderazgo' => 52,
                'relacionesSocialesEnElTrabajo' => 48,
                'retroalimentacionDeDesempeno' => 20,
                'claridadDelRol' => 20,
                'capacitacion' => 12,
                'participacionManejoDelCambio' => 12,
                'desarrolloHabilidades' => 16,
                'autonomiaSobreElTrabajo' => 12,
                'ambientalesYEsfuerzoFisico' => 48,
                'demandasEmocionales' => 36,
                'demandasCuantitativas' => 12,
                'trabajoSobreExtralaboral' => 16,
                'demandasCargaMental' => 20,
                'demandasJornadaTrabajo' => 24,
                'recompensasPorTrabajo' => 16,
                'reconocimientoYCompensacion' => 24,
                'total' => 388
            ]
        ],
        'extralaboral' => [
            'GENERAL' => [
                'tiempoFueraTrabajo' => 16,
                'relacionesFamiliares' => 12,
                'comunicacion' => 20,
                'situacionEconomica' => 12,
                'caracteristicasVivienda' => 36,
                'influenciaDelTrabajo' => 12,
                'desplazamiento' => 16,
                'total' => 124
            ]
        ],
        'estres' => [
            'GENERAL' => [
                'total' => 124
            ]
        ]
    ]
];
