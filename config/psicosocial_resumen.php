<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Colores GIR-365 para Niveles de Riesgo
    |--------------------------------------------------------------------------
    |
    | Colores estándar utilizados en el sistema GIR-365 para representar
    | los diferentes niveles de riesgo psicosocial.
    |
    */
    'colores_niveles' => [
        'sin_riesgo' => '#008235',
        'bajo' => '#00D364',
        'medio' => '#FFD600',
        'alto' => '#DA3D3D',
        'muy_alto' => '#D10000'
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas para Niveles de Riesgo
    |--------------------------------------------------------------------------
    |
    | Etiquetas descriptivas para cada nivel de riesgo.
    |
    */
    'etiquetas_niveles' => [
        'sin_riesgo' => 'Sin Riesgo',
        'bajo' => 'Riesgo Bajo',
        'medio' => 'Riesgo Medio',
        'alto' => 'Riesgo Alto',
        'muy_alto' => 'Riesgo Muy Alto'
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Gráficos
    |--------------------------------------------------------------------------
    |
    | Configuración para los gráficos 3D y visualizaciones.
    |
    */
    'graficos' => [
        'altura_default' => 250,
        'altura_mobile' => 300,
        'ancho_default' => 400,
        'tipo_default' => 'bar',
        'responsivo' => true,
        'mantener_aspecto' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Filtros
    |--------------------------------------------------------------------------
    |
    | Configuración para los filtros del resumen.
    |
    */
    'filtros' => [
        'campos_disponibles' => [
            'area',
            'sede',
            'cargo',
            'ciudad',
            'tipo_contrato',
            'proceso',
            'forma',
            'dominio',
            'dimension'
        ],
        'campos_obligatorios' => [
            'area',
            'sede',
            'ciudad',
            'tipo_contrato',
            'forma'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Secciones del Resumen
    |--------------------------------------------------------------------------
    |
    | Configuración de las 9 secciones principales del resumen.
    |
    */
    'secciones' => [
        'filtros' => [
            'titulo' => 'Cuadro para aplicar Filtros al Informe Resumen Psicosocial',
            'icono' => 'fas fa-filter',
            'orden' => 1
        ],
        'estadisticas_generales' => [
            'titulo' => 'Estadísticas Generales',
            'icono' => 'fas fa-chart-line',
            'orden' => 2
        ],
        'distribucion_riesgo' => [
            'titulo' => 'Distribución de Niveles de Riesgo',
            'icono' => 'fas fa-chart-pie',
            'orden' => 3
        ],
        'datos_sociodemograficos' => [
            'titulo' => 'Datos Sociodemográficos',
            'icono' => 'fas fa-users',
            'orden' => 4
        ],
        'riesgo_total' => [
            'titulo' => 'Riesgo Psicosocial Total',
            'icono' => 'fas fa-chart-bar',
            'orden' => 5
        ],
        'intralaboral_general' => [
            'titulo' => 'Riesgo Intralaboral General',
            'icono' => 'fas fa-building',
            'orden' => 6
        ],
        'intralaboral_a' => [
            'titulo' => 'Riesgo Intralaboral Forma A',
            'icono' => 'fas fa-clipboard-list',
            'orden' => 7
        ],
        'intralaboral_b' => [
            'titulo' => 'Riesgo Intralaboral Forma B',
            'icono' => 'fas fa-clipboard-check',
            'orden' => 8
        ],
        'extralaboral' => [
            'titulo' => 'Riesgo Extralaboral',
            'icono' => 'fas fa-home',
            'orden' => 9
        ],
        'estres' => [
            'titulo' => 'Riesgo por Estrés',
            'icono' => 'fas fa-brain',
            'orden' => 10
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Estadísticas
    |--------------------------------------------------------------------------
    |
    | Configuración para cálculos estadísticos.
    |
    */
    'estadisticas' => [
        'decimales' => 2,
        'mostrar_porcentajes' => true,
        'incluir_totales' => true,
        'calcular_desviacion' => true,
        'calcular_mediana' => true
    ]
];
