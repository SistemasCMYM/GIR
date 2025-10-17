<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Diagnostico;
use App\Models\Hoja;
use App\Models\Datos;
use App\Models\Respuesta;

/**
 * Servicio completo para el cálculo de la Batería de Riesgo Psicosocial
 * Basado en el instrumento del Ministerio de la Protección Social de Colombia
 * y la Universidad Javeriana
 * 
 * Implementa la lógica completa de:
 * - Formas A y B Intralaboral
 * - Cuestionario Extralaboral
 * - Cuestionario de Estrés
 * - Cálculo de puntajes brutos por dimensión y dominio
 * - Transformación de puntajes
 * - Comparación con baremos
 * - Interpretación de niveles de riesgo
 */
class BateriaPsicosocialService {
    /**
     * Calcula y retorna todos los puntajes, transformaciones, niveles y colores para una hoja individual
     * @param Hoja $hoja
     * @return array
     */
    public function calcularResultadosCompletos($hoja)
    {
        // 1. Puntajes brutos por dimensión/dominio para la hoja
        $hojas = [$hoja];
        $rawScores = $this->calcularPuntajesBrutosPorDimension($hojas);
        // 2. Transformación de puntajes
        $transformedScores = $this->transformarPuntajes($rawScores);
        // 3. Aplicación de baremos
        $baremoResults = $this->aplicarBaremos($transformedScores);
        // 4. Interpretación de niveles de riesgo
        $interpretation = $this->interpretarNiveles($baremoResults);

        // 5. Agregar colores estándar para cada nivel
        foreach ($interpretation['dimensiones'] as $nombre => &$dim) {
            $dim['color'] = self::COLORES_RIESGO[$dim['nivel']] ?? '#888';
        }
        foreach ($interpretation['dominios'] as $nombre => &$dom) {
            $dom['color'] = self::COLORES_RIESGO[$dom['nivel']] ?? '#888';
        }

        return [
            'puntajes_brutos' => $rawScores,
            'puntajes_transformados' => $transformedScores,
            'niveles' => $baremoResults,
            'interpretacion' => $interpretation
        ];
    }
    /**
     * Cálculo de puntajes brutos por dimensión y dominio
     */
    private function calcularPuntajesBrutosPorDimension($hojas)
    {
        // Mapeos de preguntas por dimensión/dominio (ejemplo, deben completarse con todos los mapeos del instrumento real)
        // Estructura oficial de dimensiones y dominios (solo claves, sin datos embebidos)
        $dimensiones = [
            'caracteristicas_liderazgo' => [],
            'relaciones_sociales' => [],
            'retroalimentacion_desempeno' => [],
            'relacion_colaboradores' => [],
            'claridad_rol' => [],
            'capacitacion' => [],
            'participacion_manejo_cambio' => [],
            'desarrollo_habilidades' => [],
            'autonomia_trabajo' => [],
            'esfuerzo_fisico' => [],
            'demandas_emocionales' => [],
            'demandas_cuantitativas' => [],
            'influencia_extralaboral' => [],
            'responsabilidad_cargo' => [],
            'carga_mental' => [],
            'consistencia_rol' => [],
            'jornada_trabajo' => [],
            'recompensa_trabajo' => [],
            'reconocimiento' => []
        ];
        $dominios = [
            'liderazgo' => [],
            'control_trabajo' => [],
            'demandas_trabajo' => [],
            'recompensas' => []
        ];

        $resultados = [
            'dimensiones' => [],
            'dominios' => []
        ];

        foreach ($hojas as $hoja) {
            $respuestas = \App\Models\Respuesta::where('hoja_id', $hoja->id)->get();
            $respuestasPorNumero = [];
            foreach ($respuestas as $respuesta) {
                $respuestasPorNumero[$respuesta->numero_pregunta] = (int) $respuesta->valor;
            }

            // Puntaje por dimensión
            foreach ($dimensiones as $nombre => $preguntas) {
                $suma = 0;
                foreach ($preguntas as $num) {
                    $suma += $respuestasPorNumero[$num] ?? 0;
                }
                if (!isset($resultados['dimensiones'][$nombre])) {
                    $resultados['dimensiones'][$nombre] = 0;
                }
                $resultados['dimensiones'][$nombre] += $suma;
            }

            // Puntaje por dominio
            foreach ($dominios as $nombre => $preguntas) {
                $suma = 0;
                foreach ($preguntas as $num) {
                    $suma += $respuestasPorNumero[$num] ?? 0;
                }
                if (!isset($resultados['dominios'][$nombre])) {
                    $resultados['dominios'][$nombre] = 0;
                }
                $resultados['dominios'][$nombre] += $suma;
            }
        }

        return $resultados;
    }

    /**
     * Transformación de puntajes brutos
     */
    private function transformarPuntajes(array $rawScores)
    {
        // Factores de transformación por dimensión/dominio (ejemplo, completar con los reales)
        // Estructura oficial de factores (solo claves, sin datos embebidos)
        $factores = [
            'dimensiones' => [
                'caracteristicas_liderazgo' => null,
                'relaciones_sociales' => null,
                'retroalimentacion_desempeno' => null,
                'relacion_colaboradores' => null,
                'claridad_rol' => null,
                'capacitacion' => null,
                'participacion_manejo_cambio' => null,
                'desarrollo_habilidades' => null,
                'autonomia_trabajo' => null,
                'esfuerzo_fisico' => null,
                'demandas_emocionales' => null,
                'demandas_cuantitativas' => null,
                'influencia_extralaboral' => null,
                'responsabilidad_cargo' => null,
                'carga_mental' => null,
                'consistencia_rol' => null,
                'jornada_trabajo' => null,
                'recompensa_trabajo' => null,
                'reconocimiento' => null
            ],
            'dominios' => [
                'liderazgo' => null,
                'control_trabajo' => null,
                'demandas_trabajo' => null,
                'recompensas' => null
            ]
        ];
        $transformados = [
            'dimensiones' => [],
            'dominios' => []
        ];
        foreach ($rawScores['dimensiones'] as $nombre => $puntaje) {
            $factor = $factores['dimensiones'][$nombre] ?? 1;
            $transformados['dimensiones'][$nombre] = $puntaje * $factor;
        }
        foreach ($rawScores['dominios'] as $nombre => $puntaje) {
            $factor = $factores['dominios'][$nombre] ?? 1;
            $transformados['dominios'][$nombre] = $puntaje * $factor;
        }
        return $transformados;
    }

    /**
     * Aplicación de baremos a puntajes transformados
     */
    private function aplicarBaremos(array $transformedScores)
    {
        // Baremos por dimensión/dominio (ejemplo, completar con los reales)
        // Estructura oficial de baremos (solo claves, sin datos embebidos)
        $baremos = [
            'dimensiones' => [
                'caracteristicas_liderazgo' => [],
                'relaciones_sociales' => [],
                'retroalimentacion_desempeno' => [],
                'relacion_colaboradores' => [],
                'claridad_rol' => [],
                'capacitacion' => [],
                'participacion_manejo_cambio' => [],
                'desarrollo_habilidades' => [],
                'autonomia_trabajo' => [],
                'esfuerzo_fisico' => [],
                'demandas_emocionales' => [],
                'demandas_cuantitativas' => [],
                'influencia_extralaboral' => [],
                'responsabilidad_cargo' => [],
                'carga_mental' => [],
                'consistencia_rol' => [],
                'jornada_trabajo' => [],
                'recompensa_trabajo' => [],
                'reconocimiento' => []
            ],
            'dominios' => [
                'liderazgo' => [],
                'control_trabajo' => [],
                'demandas_trabajo' => [],
                'recompensas' => []
            ]
        ];
        $resultados = [
            'dimensiones' => [],
            'dominios' => []
        ];
        foreach ($transformedScores['dimensiones'] as $nombre => $valor) {
            $baremo = $baremos['dimensiones'][$nombre] ?? null;
            $nivel = 'sin_riesgo';
            if ($baremo) {
                if ($valor >= $baremo['muy_alto']) $nivel = 'muy_alto';
                elseif ($valor >= $baremo['alto']) $nivel = 'alto';
                elseif ($valor >= $baremo['medio']) $nivel = 'medio';
                elseif ($valor >= $baremo['bajo']) $nivel = 'bajo';
                else $nivel = 'sin_riesgo';
            }
            $resultados['dimensiones'][$nombre] = [
                'valor' => $valor,
                'nivel' => $nivel
            ];
        }
        foreach ($transformedScores['dominios'] as $nombre => $valor) {
            $baremo = $baremos['dominios'][$nombre] ?? null;
            $nivel = 'sin_riesgo';
            if ($baremo) {
                if ($valor >= $baremo['muy_alto']) $nivel = 'muy_alto';
                elseif ($valor >= $baremo['alto']) $nivel = 'alto';
                elseif ($valor >= $baremo['medio']) $nivel = 'medio';
                elseif ($valor >= $baremo['bajo']) $nivel = 'bajo';
                else $nivel = 'sin_riesgo';
            }
            $resultados['dominios'][$nombre] = [
                'valor' => $valor,
                'nivel' => $nivel
            ];
        }
        return $resultados;
    }

    /**
     * Interpretación de niveles de riesgo
     */
    private function interpretarNiveles(array $baremoResults)
    {
        // Estructura final para la vista: agrupación y conteo por nivel de riesgo
        $estructura = [
            'dimensiones' => [],
            'dominios' => []
        ];

        // Dimensiones
        foreach ($baremoResults['dimensiones'] as $nombre => $datos) {
            $nivel = $datos['nivel'];
            if (!isset($estructura['dimensiones'][$nombre])) {
                $estructura['dimensiones'][$nombre] = [
                    'nombre' => $nombre,
                    'valor' => $datos['valor'],
                    'nivel' => $nivel,
                    'contadores' => [
                        'muy_alto' => 0,
                        'alto' => 0,
                        'medio' => 0,
                        'bajo' => 0,
                        'sin_riesgo' => 0
                    ]
                ];
            }
            $estructura['dimensiones'][$nombre]['contadores'][$nivel]++;
        }

        // Dominios
        foreach ($baremoResults['dominios'] as $nombre => $datos) {
            $nivel = $datos['nivel'];
            if (!isset($estructura['dominios'][$nombre])) {
                $estructura['dominios'][$nombre] = [
                    'nombre' => $nombre,
                    'valor' => $datos['valor'],
                    'nivel' => $nivel,
                    'contadores' => [
                        'muy_alto' => 0,
                        'alto' => 0,
                        'medio' => 0,
                        'bajo' => 0,
                        'sin_riesgo' => 0
                    ]
                ];
            }
            $estructura['dominios'][$nombre]['contadores'][$nivel]++;
        }

        return $estructura;
    }

    /**
     * Colores estándar GIR-365 para niveles de riesgo
     */
    const COLORES_RIESGO = [
        'sin_riesgo' => '#008235',
        'bajo' => '#00D364',
        'medio' => '#FFD600',
        'alto' => '#DD0505',
        'muy_alto' => '#A30203'
    ];

    /**
     * Obtener estadísticas completas del resumen psicosocial
     * @param string $diagnostico_id
     * @param array $filtros
     * @return array
     */
    public function obtenerEstadisticasResumen(string $diagnostico_id, array $filtros = []): array
    {
        $hojas = $this->obtenerHojasCompletadas($diagnostico_id);
        $hojasFiltradas = $this->aplicarFiltros($hojas, $filtros);
        
        // Pipeline para cálculo completo de la batería psicosocial
        // 1. Puntajes brutos por dimensión/dominio
        $rawScores = $this->calcularPuntajesBrutosPorDimension($hojasFiltradas);
        // 2. Transformación de puntajes
        $transformedScores = $this->transformarPuntajes($rawScores);
        // 3. Aplicación de baremos
        $baremoResults = $this->aplicarBaremos($transformedScores);
        // 4. Interpretación de niveles de riesgo
        $interpretation = $this->interpretarNiveles($baremoResults);

        return [
            'total_evaluaciones' => $hojasFiltradas->count(),
            'completadas' => $hojasFiltradas->count(),
            'pendientes' => 0,
            'progreso' => 100,
            'distribucion_riesgo' => $this->calcularDistribucionRiesgoCompleta($hojasFiltradas),
            'datos_sociodemograficos' => $this->calcularDatosSociodemograficos($hojasFiltradas),
            'total_psicosocial' => $this->calcularTotalPsicosocial($hojasFiltradas),
            'intralaboral_general' => $this->calcularIntralaboralGeneral($hojasFiltradas),
            'intralaboral_a' => $this->calcularIntralaboralA($hojasFiltradas),
            'intralaboral_b' => $this->calcularIntralaboralB($hojasFiltradas),
            'extralaboral' => $this->calcularExtralaboralCompleto($hojasFiltradas),
            'estres' => $this->calcularEstresCompleto($hojasFiltradas)
        ];
    }

    /**
     * Obtener hojas completadas de un diagnóstico
     */
    private function obtenerHojasCompletadas(string $diagnostico_id)
    {
        return \App\Models\Hoja::where('diagnostico_id', $diagnostico_id)
            ->get()
            ->filter(function($hoja) {
                return $this->hojaEstaCompletada($hoja);
            });
    }

    /**
     * Verificar si una hoja está completada
     */
    private function hojaEstaCompletada($hoja): bool
    {
        $datosEstado = is_string($hoja->datos) ? $hoja->datos : (is_object($hoja->datos) && !empty($hoja->datos) ? 'completado' : 'pendiente');
        $intralaboralEstado = is_string($hoja->intralaboral) ? $hoja->intralaboral : 'pendiente';
        $extralaboralEstado = is_string($hoja->extralaboral) ? $hoja->extralaboral : 'pendiente';
        $estresEstado = is_string($hoja->estres) ? $hoja->estres : 'pendiente';
        
        return $datosEstado === 'completado' && 
               $intralaboralEstado === 'completado' && 
               $extralaboralEstado === 'completado' && 
               $estresEstado === 'completado';
    }

    /**
     * Aplicar filtros a las hojas
     */
    private function aplicarFiltros($hojas, array $filtros)
    {
        $hojasFiltradas = $hojas;

        foreach ($filtros as $campo => $valor) {
            if (empty($valor)) continue;

            $hojasFiltradas = $hojasFiltradas->filter(function($hoja) use ($campo, $valor) {
                $datos = $hoja->datos;
                if (is_object($datos)) {
                    $datos = $datos->toArray();
                }
                
                switch ($campo) {
                    case 'area':
                        return isset($datos['area']) && $datos['area'] === $valor;
                    case 'sede':
                        return isset($datos['sede']) && $datos['sede'] === $valor;
                    case 'ciudad':
                        return isset($datos['ciudad']) && $datos['ciudad'] === $valor;
                    case 'tipo_contrato':
                        return isset($datos['tipo_contrato']) && $datos['tipo_contrato'] === $valor;
                    case 'proceso':
                        return isset($datos['proceso']) && $datos['proceso'] === $valor;
                    case 'forma':
                        return $hoja->forma_cuestionario === $valor;
                    default:
                        return true;
                }
            });
        }

        return $hojasFiltradas;
    }

    /**
     * Calcular distribución de riesgo completa
     */
    private function calcularDistribucionRiesgoCompleta($hojas): array
    {
        $distribucion = [
            'sin_riesgo' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['sin_riesgo']],
            'bajo' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['bajo']],
            'medio' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['medio']],
            'alto' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['alto']],
            'muy_alto' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['muy_alto']]
        ];

        $total = $hojas->count();
        
        foreach ($hojas as $hoja) {
            $nivel = $this->calcularNivelRiesgoGeneral($hoja);
            if (isset($distribucion[$nivel])) {
                $distribucion[$nivel]['cantidad']++;
            }
        }

        foreach ($distribucion as $nivel => &$datos) {
            $datos['porcentaje'] = $total > 0 ? round(($datos['cantidad'] / $total) * 100, 2) : 0;
        }

        return $distribucion;
    }

    /**
     * Calcular nivel de riesgo general de una hoja
     */
    private function calcularNivelRiesgoGeneral($hoja): string
    {
        // Obtener respuestas de la hoja
        $respuestasIntra = \App\Models\Respuesta::where('hoja_id', $hoja->id)
            ->where('tipo', 'intralaboral')
            ->get()
            ->toArray();
        
        $respuestasExtra = \App\Models\Respuesta::where('hoja_id', $hoja->id)
            ->where('tipo', 'extralaboral')
            ->get()
            ->toArray();
        
        $respuestasEstres = \App\Models\Respuesta::where('hoja_id', $hoja->id)
            ->where('tipo', 'estres')
            ->get()
            ->toArray();

        // Calcular puntajes usando método unificado
        $resultado = $this->calcularPuntajeCompleto($hoja);
        
        if (!$resultado) {
            return 'sin_datos';
        }
        
        $puntajeIntra = $resultado['intralaboral']['puntaje_transformado'] ?? 0;
        $puntajeExtra = $resultado['extralaboral']['puntaje_transformado'] ?? 0;
        $puntajeEstres = $resultado['estres']['puntaje_transformado'] ?? 0;

        // Determinar nivel de riesgo combinado (lógica simplificada)
        $promedio = ($puntajeIntra + $puntajeExtra + $puntajeEstres) / 3;
        
        if ($promedio >= 80) return 'muy_alto';
        if ($promedio >= 60) return 'alto';
        if ($promedio >= 40) return 'medio';
        if ($promedio >= 20) return 'bajo';
        return 'sin_riesgo';
    }

    /**
     * Calcular datos sociodemográficos
     */
    private function calcularDatosSociodemograficos($hojas): array
    {
        $categorias = [
            'genero' => [],
            'edad' => [],
            'estado_civil' => [],
            'tipo_vivienda' => [],
            'estrato_social' => [],
            'tipo_cargo' => [],
            'nivel_estudios' => [],
            'antiguedad_empresa' => [],
            'antiguedad_cargo' => [],
            'tipo_salario' => [],
            'tipo_contrato' => [],
            'dependientes_economicos' => [],
            'horas_laboradas' => []
        ];

        $total = $hojas->count();

        foreach ($hojas as $hoja) {
            $datos = $hoja->datos;
            if (is_object($datos)) {
                $datos = $datos->toArray();
            }
            
            if (is_array($datos)) {
                $this->procesarDatosSociodemograficos($datos, $categorias);
            }
        }

        // Calcular porcentajes
        foreach ($categorias as $categoria => &$valores) {
            foreach ($valores as $valor => &$datosCat) {
                $datosCat['porcentaje'] = $total > 0 ? round(($datosCat['cantidad'] / $total) * 100, 2) : 0;
            }
        }

        return $categorias;
    }

    /**
     * Procesar datos sociodemográficos individuales
     */
    private function procesarDatosSociodemograficos(array $datos, array &$categorias): void
    {
        $mapeo = [
            'genero' => $datos['sexo'] ?? 'No especificado',
            'edad' => $this->obtenerRangoEdad($datos['edad'] ?? 0),
            'estado_civil' => $datos['estado_civil'] ?? 'No especificado',
            'tipo_vivienda' => $datos['tipo_vivienda'] ?? 'No especificado',
            'estrato_social' => $datos['estrato_socioeconomico'] ?? 'No especificado',
            'tipo_cargo' => $datos['tipo_cargo'] ?? 'No especificado',
            'nivel_estudios' => $datos['nivel_estudios'] ?? 'No especificado',
            'antiguedad_empresa' => $this->obtenerRangoAntiguedad($datos['antiguedad_empresa'] ?? 0),
            'antiguedad_cargo' => $this->obtenerRangoAntiguedad($datos['antiguedad_cargo'] ?? 0),
            'tipo_salario' => $datos['tipo_salario'] ?? 'No especificado',
            'tipo_contrato' => $datos['tipo_contrato'] ?? 'No especificado',
            'dependientes_economicos' => $datos['personas_a_cargo'] ?? 'No especificado',
            'horas_laboradas' => $this->obtenerRangoHoras($datos['horas_trabajo_diarias'] ?? 0)
        ];

        foreach ($mapeo as $categoria => $valor) {
            if (!isset($categorias[$categoria][$valor])) {
                $categorias[$categoria][$valor] = ['cantidad' => 0, 'porcentaje' => 0];
            }
            $categorias[$categoria][$valor]['cantidad']++;
        }
    }

    /**
     * Determinar el estado de una sección específica
     * 
     * @param mixed $seccionData Los datos de la sección
     * @return string 'completado', 'en_progreso' o 'pendiente'
     */
    private function determinarEstadoSeccion($seccionData): string
    {
        // Usar la misma lógica que obtenerEstadoOficial de helpers
        if (function_exists('obtenerEstadoOficial')) {
            return obtenerEstadoOficial($seccionData);
        }
        
        // Fallback si no existe la función
        // Si es null, vacío, retorna pendiente
        if (is_null($seccionData) || $seccionData === '' || $seccionData === 'NULL') {
            return 'pendiente';
        }
        
        // Si es un string, verificar si es un estado válido o datos
        if (is_string($seccionData)) {
            $estado = trim($seccionData);
            if (in_array($estado, ['completado', 'en_progreso', 'pendiente'])) {
                return $estado;
            }
            // Si es un string con contenido, asumimos completado
            return strlen($estado) > 10 ? 'completado' : 'pendiente';
        }
        
        // Si es un objeto/array, usar lógica mejorada
        if (is_object($seccionData) || is_array($seccionData)) {
            // Para objetos (incluyendo modelos Eloquent)
            if (is_object($seccionData)) {
                // Verificar si es un modelo Eloquent
                if (method_exists($seccionData, 'getAttribute')) {
                    $estado = $seccionData->getAttribute('estado');
                    if (is_string($estado) && in_array($estado, ['completado', 'en_progreso', 'pendiente'])) {
                        return $estado;
                    }
                    // Verificar campo 'completado'
                    $completado = $seccionData->getAttribute('completado');
                    if ($completado === 'en_progreso') {
                        return 'en_progreso';
                    } elseif ($completado === true || $completado === 'true' || $completado === 1) {
                        return 'completado';
                    } elseif ($completado === false || $completado === 'false' || $completado === 0) {
                        return 'pendiente';
                    }
                }
                // Para objetos normales
                if (isset($seccionData->estado) && is_string($seccionData->estado)) {
                    $estado = trim($seccionData->estado);
                    if (in_array($estado, ['completado', 'en_progreso', 'pendiente'])) {
                        return $estado;
                    }
                }
                if (isset($seccionData->completado)) {
                    if ($seccionData->completado === 'en_progreso') {
                        return 'en_progreso';
                    } elseif ($seccionData->completado === true || $seccionData->completado === 'true' || $seccionData->completado === 1) {
                        return 'completado';
                    } elseif ($seccionData->completado === false || $seccionData->completado === 'false' || $seccionData->completado === 0) {
                        return 'pendiente';
                    }
                }
            }
            
            // Para arrays
            if (is_array($seccionData)) {
                if (isset($seccionData['estado']) && in_array($seccionData['estado'], ['completado', 'en_progreso', 'pendiente'])) {
                    return $seccionData['estado'];
                }
                if (isset($seccionData['completado'])) {
                    if ($seccionData['completado'] === 'en_progreso') {
                        return 'en_progreso';
                    } elseif ($seccionData['completado'] === true || $seccionData['completado'] === 'true' || $seccionData['completado'] === 1) {
                        return 'completado';
                    } elseif ($seccionData['completado'] === false || $seccionData['completado'] === 'false' || $seccionData['completado'] === 0) {
                        return 'pendiente';
                    }
                }
            }
            
            // Si tiene contenido pero no estado explícito
            $hasContent = false;
            if (is_object($seccionData)) {
                if (method_exists($seccionData, 'toArray')) {
                    $arrayData = $seccionData->toArray();
                    $hasContent = !empty($arrayData);
                } else {
                    $hasContent = count(get_object_vars($seccionData)) > 0;
                }
            } elseif (is_array($seccionData)) {
                $hasContent = !empty($seccionData);
            }
            
            return $hasContent ? 'completado' : 'pendiente';
        }
        
        // Si es boolean
        if (is_bool($seccionData)) {
            return $seccionData ? 'completado' : 'pendiente';
        }
        
        return 'pendiente';
    }

    /**
     * Devuelve estadísticas de tarjetas de aplicación (diagnósticos) para la empresa.
     * - total: total de diagnósticos
     * - completados: diagnósticos con todas las hojas completadas
     * - en_proceso: diagnósticos con al menos una hoja en proceso
     * - pendientes: diagnósticos sin hojas completadas
     */
    public function obtenerEstadisticasTarjetas($empresa_id)
    {
        $diagnosticos = \App\Models\Diagnostico::on('mongodb_psicosocial')
            ->where('empresa_id', $empresa_id)
            ->get();

        $total = 0;
        $completados = 0;
        $en_proceso = 0;
        $pendientes = 0;

        foreach ($diagnosticos as $diagnostico) {
            $hojas = \App\Models\Hoja::on('mongodb_psicosocial')
                ->where('diagnostico_id', $diagnostico->_id)
                ->get();
            foreach ($hojas as $hoja) {
                $total++;
                
                // Determinar el estado de cada sección usando la misma lógica que hojaEstaCompletada
                $datosEstado = $this->determinarEstadoSeccion($hoja->datos ?? null);
                $intralaboralEstado = $this->determinarEstadoSeccion($hoja->intralaboral ?? null);
                $extralaboralEstado = $this->determinarEstadoSeccion($hoja->extralaboral ?? null);
                $estresEstado = $this->determinarEstadoSeccion($hoja->estres ?? null);

                $estados = [$datosEstado, $intralaboralEstado, $extralaboralEstado, $estresEstado];

                // Si todos son 'completado'
                if ($datosEstado === 'completado' && $intralaboralEstado === 'completado' && $extralaboralEstado === 'completado' && $estresEstado === 'completado') {
                    $completados++;
                }
                // Si todos son 'pendiente'
                elseif ($datosEstado === 'pendiente' && $intralaboralEstado === 'pendiente' && $extralaboralEstado === 'pendiente' && $estresEstado === 'pendiente') {
                    $pendientes++;
                }
                // Si al menos uno es 'en_progreso' o hay una mezcla de estados
                else {
                    $en_proceso++;
                }
            }
        }

        return [
            'total' => $total,
            'completados' => $completados,
            'en_proceso' => $en_proceso,
            'pendientes' => $pendientes
        ];
    }
//
    /**
     * Calcular total psicosocial
     */
    private function calcularTotalPsicosocial($hojas): array
    {
        $instrumentos = [
            'intralaboral' => $this->inicializarContadores(),
            'extralaboral' => $this->inicializarContadores(),
            'estres' => $this->inicializarContadores()
        ];

        foreach ($hojas as $hoja) {
            $forma = $hoja->forma_cuestionario ?? 'A';
            $perfil = $hoja->perfil ?? 'auxiliares_operarios';
            
            $respuestasIntra = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'intralaboral')->get()->toArray();
            $respuestasExtra = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'extralaboral')->get()->toArray();
            $respuestasEstres = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'estres')->get()->toArray();

            // Usar método unificado para calcular puntajes
            $resultado = $this->calcularPuntajeCompleto($hoja);
            
            if ($resultado) {
                $nivelIntra = $resultado['intralaboral']['nivel_riesgo'] ?? 'sin_calcular';
                $nivelExtra = $resultado['extralaboral']['nivel_riesgo'] ?? 'sin_calcular';
                $nivelEstres = $resultado['estres']['nivel_riesgo'] ?? 'sin_calcular';
            } else {
                $nivelIntra = $nivelExtra = $nivelEstres = 'sin_calcular';
            }

            $instrumentos['intralaboral'][$nivelIntra]['cantidad']++;
            $instrumentos['extralaboral'][$nivelExtra]['cantidad']++;
            $instrumentos['estres'][$nivelEstres]['cantidad']++;
        }

        $total = $hojas->count();
        foreach ($instrumentos as $instrumento => &$niveles) {
            foreach ($niveles as $nivel => &$datos) {
                $datos['porcentaje'] = $total > 0 ? round(($datos['cantidad'] / $total) * 100, 2) : 0;
            }
        }

        return $instrumentos;
    }

    /**
     * Calcular intralaboral general
     */
    private function calcularIntralaboralGeneral($hojas): array
    {
        $dominios = $this->calcularDominiosIntralaboralCompleto($hojas);
        $dimensiones = $this->calcularDimensionesIntralaboralCompleto($hojas);
        
        return [
            'poblacion' => $hojas->count(),
            'dominios' => $dominios,
            'dimensiones' => $dimensiones,
            'descripcion' => $this->generarDescripcionIntralaboral($dominios, $dimensiones)
        ];
    }

    /**
     * Calcular intralaboral A
     */
    private function calcularIntralaboralA($hojas): array
    {
        $hojasA = $hojas->filter(function($hoja) {
            return ($hoja->forma_cuestionario ?? 'A') === 'A';
        });

        $dominios = $this->calcularDominiosIntralaboralCompleto($hojasA);
        $dimensiones = $this->calcularDimensionesIntralaboralCompleto($hojasA);
        
        return [
            'poblacion' => $hojasA->count(),
            'dominios' => $dominios,
            'dimensiones' => $dimensiones,
            'descripcion' => $this->generarDescripcionIntralaboral($dominios, $dimensiones)
        ];
    }

    /**
     * Calcular intralaboral B
     */
    private function calcularIntralaboralB($hojas): array
    {
        $hojasB = $hojas->filter(function($hoja) {
            return ($hoja->forma_cuestionario ?? 'A') === 'B';
        });

        $dominios = $this->calcularDominiosIntralaboralCompleto($hojasB);
        $dimensiones = $this->calcularDimensionesIntralaboralCompleto($hojasB);
        
        return [
            'poblacion' => $hojasB->count(),
            'dominios' => $dominios,
            'dimensiones' => $dimensiones,
            'descripcion' => $this->generarDescripcionIntralaboral($dominios, $dimensiones)
        ];
    }

    /**
     * Calcular extralaboral completo
     */
    private function calcularExtralaboralCompleto($hojas): array
    {
        $dimensiones = $this->calcularDimensionesExtralaboralCompleto($hojas);
        
        return [
            'poblacion' => $hojas->count(),
            'total' => $this->calcularTotalExtralaboralCompleto($hojas),
            'dimensiones' => $dimensiones,
            'descripcion' => $this->generarDescripcionExtralaboral($dimensiones)
        ];
    }

    /**
     * Calcular estrés completo
     */
    private function calcularEstresCompleto($hojas): array
    {
        $distribucion = $this->calcularDistribucionEstres($hojas);
        
        return [
            'poblacion' => $hojas->count(),
            'total' => $distribucion,
            'descripcion' => $this->generarDescripcionEstres($distribucion)
        ];
    }

    /**
     * Calcular dominios intralaboral completo
     */
    private function calcularDominiosIntralaboralCompleto($hojas): array
    {
        $dominios = [
            'demandas_trabajo' => [
                'nombre' => 'Demandas del trabajo',
                'contadores' => $this->inicializarContadores(),
                'descripcion' => ''
            ],
            'control' => [
                'nombre' => 'Control sobre el trabajo',
                'contadores' => $this->inicializarContadores(),
                'descripcion' => ''
            ],
            'liderazgo' => [
                'nombre' => 'Liderazgo y relaciones sociales en el trabajo',
                'contadores' => $this->inicializarContadores(),
                'descripcion' => ''
            ],
            'recompensas' => [
                'nombre' => 'Recompensas',
                'contadores' => $this->inicializarContadores(),
                'descripcion' => ''
            ]
        ];

        foreach ($hojas as $hoja) {
            $forma = $hoja->forma_cuestionario ?? 'A';
            $respuestas = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'intralaboral')->get()->toArray();
            
            $resultados = $this->calcularDominiosIntralaboral($respuestas, $forma);
            
            foreach ($resultados as $dominioKey => $resultado) {
                $nivel = $this->determinarNivelRiesgo($resultado['puntaje_transformado']);
                if (isset($dominios[$dominioKey])) {
                    $dominios[$dominioKey]['contadores'][$nivel]['cantidad']++;
                }
            }
        }

        $total = $hojas->count();
        foreach ($dominios as $dominio => &$datos) {
            foreach ($datos['contadores'] as $nivel => &$contador) {
                $contador['porcentaje'] = $total > 0 ? round(($contador['cantidad'] / $total) * 100, 2) : 0;
            }
        }

        return $dominios;
    }

    /**
     * Calcular dimensiones intralaboral completo
     */
    private function calcularDimensionesIntralaboralCompleto($hojas): array
    {
        $dimensiones = [];

        foreach ($hojas as $hoja) {
            $forma = $hoja->forma_cuestionario ?? 'A';
            $respuestas = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'intralaboral')->get()->toArray();
            
            $resultados = $this->calcularDimensionesIntralaboral($respuestas, $forma);
            
            foreach ($resultados as $dimensionKey => $resultado) {
                if (!isset($dimensiones[$dimensionKey])) {
                    $dimensiones[$dimensionKey] = [
                        'nombre' => $this->obtenerNombreDimension($dimensionKey),
                        'dominio' => $this->obtenerDominioDimension($dimensionKey),
                        'contadores' => $this->inicializarContadores()
                    ];
                }
                
                $nivel = $this->determinarNivelRiesgo($resultado['puntaje_transformado']);
                $dimensiones[$dimensionKey]['contadores'][$nivel]['cantidad']++;
            }
        }

        $total = $hojas->count();
        foreach ($dimensiones as $dimension => &$datos) {
            foreach ($datos['contadores'] as $nivel => &$contador) {
                $contador['porcentaje'] = $total > 0 ? round(($contador['cantidad'] / $total) * 100, 2) : 0;
            }
        }

        return $dimensiones;
    }

    /**
     * Calcular dimensiones extralaboral completo
     */
    private function calcularDimensionesExtralaboralCompleto($hojas): array
    {
        $dimensiones = [];

        foreach ($hojas as $hoja) {
            $perfil = $hoja->perfil ?? 'auxiliares_operarios';
            $respuestas = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'extralaboral')->get()->toArray();
            
            $resultados = $this->calcularDimensionesExtralaboral($respuestas, $perfil);
            
            foreach ($resultados as $dimensionKey => $resultado) {
                if (!isset($dimensiones[$dimensionKey])) {
                    $dimensiones[$dimensionKey] = [
                        'nombre' => $this->obtenerNombreDimensionExtralaboral($dimensionKey),
                        'contadores' => $this->inicializarContadores()
                    ];
                }
                
                $nivel = $this->determinarNivelRiesgo($resultado['puntaje_transformado']);
                $dimensiones[$dimensionKey]['contadores'][$nivel]['cantidad']++;
            }
        }

        $total = $hojas->count();
        foreach ($dimensiones as $dimension => &$datos) {
            foreach ($datos['contadores'] as $nivel => &$contador) {
                $contador['porcentaje'] = $total > 0 ? round(($contador['cantidad'] / $total) * 100, 2) : 0;
            }
        }

        return $dimensiones;
    }

    /**
     * Calcular total extralaboral completo
     */
    private function calcularTotalExtralaboralCompleto($hojas): array
    {
        $total = $this->inicializarContadores();

        foreach ($hojas as $hoja) {
            $perfil = $hoja->perfil ?? 'auxiliares_operarios';
            $respuestas = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'extralaboral')->get()->toArray();
            
            $resultado = $this->calcularTotalExtralaboral($respuestas, $perfil);
            $nivel = $this->determinarNivelRiesgo($resultado['puntaje_transformado']);
            $total[$nivel]['cantidad']++;
        }

        $totalHojas = $hojas->count();
        foreach ($total as $nivel => &$datos) {
            $datos['porcentaje'] = $totalHojas > 0 ? round(($datos['cantidad'] / $totalHojas) * 100, 2) : 0;
        }

        return $total;
    }

    /**
     * Calcular distribución de estrés
     */
    private function calcularDistribucionEstres($hojas): array
    {
        $distribucion = $this->inicializarContadores();

        foreach ($hojas as $hoja) {
            $perfil = $hoja->perfil ?? 'auxiliares_operarios';
            $respuestas = \App\Models\Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'estres')->get()->toArray();
            
            $resultado = $this->calcularEstres($respuestas, $perfil);
            $nivel = $this->determinarNivelRiesgo($resultado['puntaje_transformado']);
            $distribucion[$nivel]['cantidad']++;
        }

        $total = $hojas->count();
        foreach ($distribucion as $nivel => &$datos) {
            $datos['porcentaje'] = $total > 0 ? round(($datos['cantidad'] / $total) * 100, 2) : 0;
        }

        return $distribucion;
    }

    /**
     * Inicializar contadores de niveles de riesgo
     */
    private function inicializarContadores(): array
    {
        return [
            'sin_riesgo' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['sin_riesgo']],
            'bajo' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['bajo']],
            'medio' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['medio']],
            'alto' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['alto']],
            'muy_alto' => ['cantidad' => 0, 'porcentaje' => 0, 'color' => self::COLORES_RIESGO['muy_alto']]
        ];
    }

    /**
     * Determinar nivel de riesgo basado en puntaje transformado
     */
    private function determinarNivelRiesgo(float $puntaje): string
    {
        if ($puntaje >= 75) return 'muy_alto';
        if ($puntaje >= 50) return 'alto';
        if ($puntaje >= 25) return 'medio';
        if ($puntaje >= 10) return 'bajo';
        return 'sin_riesgo';
    }

    /**
     * Métodos auxiliares para rangos
     */
    private function obtenerRangoEdad(int $edad): string
    {
        if ($edad < 25) return '18-24';
        if ($edad < 35) return '25-34';
        if ($edad < 45) return '35-44';
        if ($edad < 55) return '45-54';
        return '55+';
    }

    private function obtenerRangoAntiguedad(int $antiguedad): string
    {
        if ($antiguedad < 1) return 'Menos de 1 año';
        if ($antiguedad < 3) return '1-3 años';
        if ($antiguedad < 5) return '3-5 años';
        if ($antiguedad < 10) return '5-10 años';
        return 'Más de 10 años';
    }

    private function obtenerRangoHoras(int $horas): string
    {
        if ($horas <= 8) return '1-8 horas';
        if ($horas <= 12) return '9-12 horas';
        return 'Más de 12 horas';
    }

    /**
     * Métodos de cálculo básicos de puntajes
     */
    /**
     * Métodos auxiliares para nombres y descripciones
     */
    private function obtenerNombreDimension(string $key): string
    {
        $nombres = [
            'demandas_cuantitativas' => 'Demandas cuantitativas',
            'demandas_carga_mental' => 'Demandas de carga mental',
            'demandas_emocionales' => 'Demandas emocionales',
            'exigencias_responsabilidad' => 'Exigencias de responsabilidad del cargo',
            'demandas_ambientales' => 'Demandas ambientales y de esfuerzo físico',
            'demandas_jornada' => 'Demandas de la jornada de trabajo',
            'consistencia_rol' => 'Consistencia del rol',
            'influencia_extralaboral' => 'Influencia del trabajo sobre el entorno extralaboral',
            'control_autonomia' => 'Control y autonomía sobre el trabajo',
            'oportunidades_desarrollo' => 'Oportunidades para el uso y desarrollo de habilidades',
            'participacion_cambio' => 'Participación y manejo del cambio',
            'claridad_rol' => 'Claridad del rol',
            'capacitacion' => 'Capacitación',
            'caracteristicas_liderazgo' => 'Características del liderazgo',
            'relaciones_sociales' => 'Relaciones sociales en el trabajo',
            'retroalimentacion' => 'Retroalimentación del desempeño',
            'relacion_colaboradores' => 'Relación con los colaboradores',
            'reconocimiento' => 'Reconocimiento y compensación',
            'recompensas_pertenencia' => 'Recompensas derivadas de la pertenencia'
        ];
        
        return $nombres[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    private function obtenerNombreDimensionExtralaboral(string $key): string
    {
        $nombres = [
            'tiempo_fuera_trabajo' => 'Tiempo fuera del trabajo',
            'relaciones_familiares' => 'Relaciones familiares',
            'comunicacion_relaciones' => 'Comunicación y relaciones interpersonales',
            'situacion_economica' => 'Situación económica del grupo familiar',
            'caracteristicas_vivienda' => 'Características de la vivienda y entorno',
            'influencia_trabajo' => 'Influencia del entorno extralaboral',
            'desplazamiento' => 'Desplazamiento vivienda-trabajo-vivienda'
        ];
        
        return $nombres[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    private function obtenerDominioDimension(string $dimension): string
    {
        $dominios = [
            'demandas_cuantitativas' => 'DEMANDAS DEL TRABAJO',
            'demandas_carga_mental' => 'DEMANDAS DEL TRABAJO',
            'demandas_emocionales' => 'DEMANDAS DEL TRABAJO',
            'exigencias_responsabilidad' => 'DEMANDAS DEL TRABAJO',
            'demandas_ambientales' => 'DEMANDAS DEL TRABAJO',
            'demandas_jornada' => 'DEMANDAS DEL TRABAJO',
            'consistencia_rol' => 'DEMANDAS DEL TRABAJO',
            'influencia_extralaboral' => 'DEMANDAS DEL TRABAJO',
            'control_autonomia' => 'CONTROL',
            'oportunidades_desarrollo' => 'CONTROL',
            'participacion_cambio' => 'CONTROL',
            'claridad_rol' => 'CONTROL',
            'capacitacion' => 'CONTROL',
            'caracteristicas_liderazgo' => 'LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO',
            'relaciones_sociales' => 'LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO',
            'retroalimentacion' => 'LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO',
            'relacion_colaboradores' => 'LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO',
            'reconocimiento' => 'RECOMPENSAS',
            'recompensas_pertenencia' => 'RECOMPENSAS'
        ];
        
        return $dominios[$dimension] ?? 'OTROS';
    }

    private function generarDescripcionIntralaboral(array $dominios, array $dimensiones): array
    {
        $descripcion = [
            'muy_alto' => [],
            'alto_medio' => [],
            'bajo_sin_riesgo' => []
        ];

        foreach ($dimensiones as $dimension => $datos) {
            $mayor = 0;
            $nivelMayor = '';
            
            foreach ($datos['contadores'] as $nivel => $contador) {
                if ($contador['cantidad'] > $mayor) {
                    $mayor = $contador['cantidad'];
                    $nivelMayor = $nivel;
                }
            }

            $nombre = $datos['nombre'];
            $porcentaje = $datos['contadores'][$nivelMayor]['porcentaje'];
            $cantidad = $datos['contadores'][$nivelMayor]['cantidad'];
            
            $texto = "{$nombre}: {$porcentaje}% ({$cantidad} empleados)";

            if ($nivelMayor === 'muy_alto') {
                $descripcion['muy_alto'][] = $texto;
            } elseif (in_array($nivelMayor, ['alto', 'medio'])) {
                $descripcion['alto_medio'][] = $texto;
            } else {
                $descripcion['bajo_sin_riesgo'][] = $texto;
            }
        }

        return $descripcion;
    }

    private function generarDescripcionExtralaboral(array $dimensiones): array
    {
        $descripcion = [
            'muy_alto' => [],
            'alto_medio' => [],
            'bajo_sin_riesgo' => []
        ];

        foreach ($dimensiones as $dimension => $datos) {
            $mayor = 0;
            $nivelMayor = '';
            
            foreach ($datos['contadores'] as $nivel => $contador) {
                if ($contador['cantidad'] > $mayor) {
                    $mayor = $contador['cantidad'];
                    $nivelMayor = $nivel;
                }
            }

            $nombre = $datos['nombre'];
            $porcentaje = $datos['contadores'][$nivelMayor]['porcentaje'];
            $cantidad = $datos['contadores'][$nivelMayor]['cantidad'];
            
            $texto = "{$nombre}: {$porcentaje}% ({$cantidad} empleados)";

            if ($nivelMayor === 'muy_alto') {
                $descripcion['muy_alto'][] = $texto;
            } elseif (in_array($nivelMayor, ['alto', 'medio'])) {
                $descripcion['alto_medio'][] = $texto;
            } else {
                $descripcion['bajo_sin_riesgo'][] = $texto;
            }
        }

        return $descripcion;
    }

    private function generarDescripcionEstres(array $distribucion): array
    {
        $descripcion = [
            'muy_alto' => [],
            'alto_medio' => [],
            'bajo_sin_riesgo' => []
        ];

        $mayor = 0;
        $nivelMayor = '';
        
        foreach ($distribucion as $nivel => $contador) {
            if ($contador['cantidad'] > $mayor) {
                $mayor = $contador['cantidad'];
                $nivelMayor = $nivel;
            }
        }

        $porcentaje = $distribucion[$nivelMayor]['porcentaje'];
        $cantidad = $distribucion[$nivelMayor]['cantidad'];
        
        $texto = "Estrés: {$porcentaje}% ({$cantidad} empleados)";

        if ($nivelMayor === 'muy_alto') {
            $descripcion['muy_alto'][] = $texto;
        } elseif (in_array($nivelMayor, ['alto', 'medio'])) {
            $descripcion['alto_medio'][] = $texto;
        } else {
            $descripcion['bajo_sin_riesgo'][] = $texto;
        }

        return $descripcion;
    }

    /**
     * Obtener opciones de filtros
     */
    public function obtenerOpcionesFiltros($hojas): array
    {
        $opciones = [
            'areas' => [],
            'sedes' => [],
            'ciudades' => [],
            'tipos_contrato' => [],
            'procesos' => [],
            'formas' => ['A', 'B']
        ];

        foreach ($hojas as $hoja) {
            $datos = $hoja->datos;
            if (is_object($datos)) {
                $datos = $datos->toArray();
            }
            
            if (is_array($datos)) {
                $this->agregarOpcionSiExiste($opciones['areas'], $datos['area'] ?? null);
                $this->agregarOpcionSiExiste($opciones['sedes'], $datos['sede'] ?? null);
                $this->agregarOpcionSiExiste($opciones['ciudades'], $datos['ciudad'] ?? null);
                $this->agregarOpcionSiExiste($opciones['tipos_contrato'], $datos['tipo_contrato'] ?? null);
                $this->agregarOpcionSiExiste($opciones['procesos'], $datos['proceso'] ?? null);
            }
        }

        return $opciones;
    }

    private function agregarOpcionSiExiste(array &$array, $valor): void
    {
        if ($valor && !in_array($valor, $array)) {
            $array[] = $valor;
        }
    }

    // Métodos stub para evitar errores de método indefinido
    public function calcularDimensionesIntralaboral(array $respuestas, string $forma = 'A'): array
    {
        // Mapeo oficial de dimensiones y preguntas (extraído de utils.js)
        $dimensiones = [
            'caracteristicas_liderazgo' => $forma === 'A' ? [63,64,65,66,67,68,69,70,71,72,73,74,75] : [49,50,51,52,53,54,55,56,57,58,59,60,61],
            'relaciones_sociales' => $forma === 'A' ? [76,77,78,79,80,81,82,83,84,85,86,87,88,89] : [62,63,64,65,66,67,68,69,70,71,72,73],
            'retroalimentacion_desempeno' => $forma === 'A' ? [90,91,92,93,94] : [74,75,76,77,78],
            'relacion_colaboradores' => $forma === 'A' ? [115,116,117,118,119,120,121,122,123] : [],
            'claridad_rol' => $forma === 'A' ? [53,54,55,56,57,58,59] : [41,42,43,44,45],
            'capacitacion' => $forma === 'A' ? [60,61,62] : [46,47,48],
            'participacion_cambio' => $forma === 'A' ? [48,49,50,51] : [38,39,40],
            'desarrollo_habilidades' => $forma === 'A' ? [39,40,41,42] : [29,30,31,32],
            'autonomia_trabajo' => $forma === 'A' ? [44,45,46] : [34,35,36],
            'esfuerzo_fisico' => $forma === 'A' ? [1,2,3,4,5,6,7,8,9,10,11,12] : [1,2,3,4,5,6,7,8,9,10,11,12],
            'demandas_emocionales' => $forma === 'A' ? [106,107,108,109,110,111,112,113,114] : [89,90,91,92,93,94,95,96,97],
            'demandas_cuantitativas' => $forma === 'A' ? [13,14,15,32,43,47] : [13,14,15],
            'influencia_extralaboral' => $forma === 'A' ? [35,36,37,38] : [25,26,27,28],
            'exigencias_responsabilidad' => $forma === 'A' ? [19,22,23,24,25,26] : [],
            'carga_mental' => $forma === 'A' ? [16,17,18,20,21] : [16,17,18,19,20],
            'consistencia_rol' => $forma === 'A' ? [27,28,29,30,52] : [],
            'jornada_trabajo' => $forma === 'A' ? [31,33,34] : [21,22,23,24,33,37],
            'recompensa_trabajo' => $forma === 'A' ? [95,102,103,104,105] : [85,86,87,88],
            'reconocimiento' => $forma === 'A' ? [96,97,98,99,100,101] : [79,80,81,82,83,84],
        ];

        // Factores de transformación oficiales (utils.js)
        $factores = [
            'caracteristicas_liderazgo' => 52,
            'relaciones_sociales' => $forma === 'A' ? 56 : 48,
            'retroalimentacion_desempeno' => 20,
            'relacion_colaboradores' => 36,
            'claridad_rol' => $forma === 'A' ? 28 : 20,
            'capacitacion' => 12,
            'participacion_cambio' => $forma === 'A' ? 16 : 12,
            'desarrollo_habilidades' => 16,
            'autonomia_trabajo' => 12,
            'esfuerzo_fisico' => 48,
            'demandas_emocionales' => 36,
            'demandas_cuantitativas' => $forma === 'A' ? 24 : 12,
            'influencia_extralaboral' => 16,
            'exigencias_responsabilidad' => 24,
            'carga_mental' => 20,
            'consistencia_rol' => 20,
            'jornada_trabajo' => $forma === 'A' ? 12 : 24,
            'recompensa_trabajo' => $forma === 'A' ? 20 : 16,
            'reconocimiento' => 24,
        ];

        // Baremos oficiales (utils.js, ejemplo para algunas dimensiones)
        $baremos = [
            'caracteristicas_liderazgo' => [0, 3.8, 15.4, 30.8, 46.2, 100],
            'relaciones_sociales' => [0, 5.4, 16.1, 25, 37.5, 100],
            // ...agregar todos los baremos reales
        ];

        // Mapear respuestas por número de pregunta
        $respuestasPorNumero = [];
        foreach ($respuestas as $respuesta) {
            $num = $respuesta['numero_pregunta'] ?? $respuesta['consecutivo'] ?? null;
            if ($num !== null) {
                $respuestasPorNumero[$num] = (int)($respuesta['valor'] ?? 0);
            }
        }

        $resultados = [];
        foreach ($dimensiones as $key => $pregs) {
            if (empty($pregs)) continue;
            $suma = 0;
            $totalPregs = 0;
            foreach ($pregs as $num) {
                $suma += $respuestasPorNumero[$num] ?? 0;
                $totalPregs++;
            }
            $factor = $factores[$key] ?? 1;
            $puntaje = $totalPregs > 0 ? ($suma / $totalPregs) * $factor : 0;
            // Determinar nivel de riesgo (usando baremo si existe, si no, genérico)
            $nivel = 'sin_riesgo';
            if (isset($baremos[$key])) {
                [$min, $bajo, $medio, $alto, $muy_alto, $max] = $baremos[$key];
                if ($puntaje > $muy_alto) $nivel = 'muy_alto';
                elseif ($puntaje > $alto) $nivel = 'alto';
                elseif ($puntaje > $medio) $nivel = 'medio';
                elseif ($puntaje > $bajo) $nivel = 'bajo';
                else $nivel = 'sin_riesgo';
            }
            $resultados[$key] = [
                'nombre' => $this->obtenerNombreDimension($key),
                'puntaje' => $puntaje,
                'nivel' => $nivel,
                'contadores' => [
                    'muy_alto' => $nivel === 'muy_alto' ? 1 : 0,
                    'alto' => $nivel === 'alto' ? 1 : 0,
                    'medio' => $nivel === 'medio' ? 1 : 0,
                    'bajo' => $nivel === 'bajo' ? 1 : 0,
                    'sin_riesgo' => $nivel === 'sin_riesgo' ? 1 : 0,
                ]
            ];
        }
        return $resultados;
    }

    public function calcularDominiosIntralaboral(array $respuestas, string $forma = 'A'): array
    {
        // Mapeo oficial de dominios y preguntas (utils.js)
        $dominios = [
            'liderazgo' => $forma === 'A'
                ? array_merge([63,64,65,66,67,68,69,70,71,72,73,74,75], [76,77,78,79,80,81,82,83,84,85,86,87,88,89], [90,91,92,93,94], [115,116,117,118,119,120,121,122,123])
                : array_merge([49,50,51,52,53,54,55,56,57,58,59,60,61], [62,63,64,65,66,67,68,69,70,71,72,73], [74,75,76,77,78]),
            'control' => $forma === 'A'
                ? array_merge([53,54,55,56,57,58,59], [60,61,62], [48,49,50,51], [39,40,41,42], [44,45,46])
                : array_merge([41,42,43,44,45], [46,47,48], [38,39,40], [29,30,31,32], [34,35,36]),
            'demandas_trabajo' => $forma === 'A'
                ? array_merge([1,2,3,4,5,6,7,8,9,10,11,12], [106,107,108,109,110,111,112,113,114], [13,14,15,32,43,47], [35,36,37,38], [19,22,23,24,25,26], [16,17,18,20,21], [27,28,29,30,52], [31,33,34])
                : array_merge([1,2,3,4,5,6,7,8,9,10,11,12], [89,90,91,92,93,94,95,96,97], [13,14,15], [25,26,27,28], [16,17,18,19,20], [21,22,23,24,33,37]),
            'recompensas' => $forma === 'A'
                ? array_merge([95,102,103,104,105], [96,97,98,99,100,101])
                : array_merge([85,86,87,88], [79,80,81,82,83,84]),
        ];

        $factores = [
            'liderazgo' => $forma === 'A' ? 164 : 120,
            'control' => $forma === 'A' ? 84 : 72,
            'demandas_trabajo' => $forma === 'A' ? 200 : 156,
            'recompensas' => $forma === 'A' ? 44 : 40,
        ];

        $baremos = [
            // ...agregar baremos oficiales por dominio si están disponibles
        ];

        $respuestasPorNumero = [];
        foreach ($respuestas as $respuesta) {
            $num = $respuesta['numero_pregunta'] ?? $respuesta['consecutivo'] ?? null;
            if ($num !== null) {
                $respuestasPorNumero[$num] = (int)($respuesta['valor'] ?? 0);
            }
        }

        $resultados = [];
        foreach ($dominios as $key => $pregs) {
            if (empty($pregs)) continue;
            $suma = 0;
            $totalPregs = 0;
            foreach ($pregs as $num) {
                $suma += $respuestasPorNumero[$num] ?? 0;
                $totalPregs++;
            }
            $factor = $factores[$key] ?? 1;
            $puntaje = $totalPregs > 0 ? ($suma / $totalPregs) * $factor : 0;
            $nivel = 'sin_riesgo'; // Aquí puedes usar baremo si lo tienes
            $resultados[$key] = [
                'nombre' => ucfirst(str_replace('_', ' ', $key)),
                'puntaje' => $puntaje,
                'nivel' => $nivel,
                'contadores' => [
                    'muy_alto' => $nivel === 'muy_alto' ? 1 : 0,
                    'alto' => $nivel === 'alto' ? 1 : 0,
                    'medio' => $nivel === 'medio' ? 1 : 0,
                    'bajo' => $nivel === 'bajo' ? 1 : 0,
                    'sin_riesgo' => $nivel === 'sin_riesgo' ? 1 : 0,
                ]
            ];
        }
        return $resultados;
    }

    public function calcularDimensionesExtralaboral(array $respuestas, string $perfil = 'auxiliares_operarios'): array
    {
        // Mapeo oficial de dimensiones extralaborales
        $dimensiones = [
            'tiempo_fuera_trabajo' => self::DIMENSION_TIEMPO_FUERA_TRABAJO,
            'relaciones_familiares' => self::DIMENSION_RELACIONES_FAMILIARES,
            'relaciones_interpersonales' => self::DIMENSION_COMUNICACION,
            'situacion_economica' => self::DIMENSION_SITUACION_ECONOMICA,
            'caracteristicas_vivienda' => self::DIMENSION_CARACTERISTICAS_VIVIENDA,
            'influencia_trabajo' => self::DIMENSION_INFLUENCIA_TRABAJO,
            'desplazamiento' => self::DIMENSION_DESPLAZAMIENTO
        ];
        $factores = self::FACTOR_DIMENSION_EXTRALABORAL;
        $baremos = $perfil === 'jefes_profesionales' ? self::BAREMOS_EXTRALABORAL_JEFES_PROFESIONALES : self::BAREMOS_EXTRALABORAL_AUXILIARES_OPERARIOS;
        $respuestasPorNumero = [];
        foreach ($respuestas as $respuesta) {
            $num = $respuesta['numero_pregunta'] ?? $respuesta['consecutivo'] ?? null;
            if ($num !== null) {
                $respuestasPorNumero[$num] = (int)($respuesta['valor'] ?? 0);
            }
        }
        $resultados = [];
        foreach ($dimensiones as $key => $pregs) {
            if (empty($pregs)) continue;
            $suma = 0;
            $totalPregs = 0;
            foreach ($pregs as $num) {
                $suma += $respuestasPorNumero[$num] ?? 0;
                $totalPregs++;
            }
            $factor = $factores[$key] ?? 1;
            $puntaje = $totalPregs > 0 ? ($suma / $totalPregs) * $factor : 0;
            $baremo = $baremos[$key] ?? null;
            $nivel = 'sin_riesgo';
            if ($baremo) {
                if ($puntaje > $baremo['riesgo_muy_alto'][0]) $nivel = 'muy_alto';
                elseif ($puntaje > $baremo['riesgo_alto'][0]) $nivel = 'alto';
                elseif ($puntaje > $baremo['riesgo_medio'][0]) $nivel = 'medio';
                elseif ($puntaje > $baremo['riesgo_bajo'][0]) $nivel = 'bajo';
                else $nivel = 'sin_riesgo';
            }
            $resultados[$key] = [
                'nombre' => ucfirst(str_replace('_', ' ', $key)),
                'puntaje' => $puntaje,
                'nivel' => $nivel,
                'contadores' => [
                    'muy_alto' => $nivel === 'muy_alto' ? 1 : 0,
                    'alto' => $nivel === 'alto' ? 1 : 0,
                    'medio' => $nivel === 'medio' ? 1 : 0,
                    'bajo' => $nivel === 'bajo' ? 1 : 0,
                    'sin_riesgo' => $nivel === 'sin_riesgo' ? 1 : 0,
                ]
            ];
        }
        return $resultados;
    }

    public function calcularTotalExtralaboral(array $respuestas, string $perfil = 'auxiliares_operarios'): array
    {
        // Calcular el puntaje total extralaboral
        $preguntas = array_merge(
            self::DIMENSION_TIEMPO_FUERA_TRABAJO,
            self::DIMENSION_RELACIONES_FAMILIARES,
            self::DIMENSION_COMUNICACION,
            self::DIMENSION_SITUACION_ECONOMICA,
            self::DIMENSION_CARACTERISTICAS_VIVIENDA,
            self::DIMENSION_INFLUENCIA_TRABAJO,
            self::DIMENSION_DESPLAZAMIENTO
        );
        $factores = self::FACTOR_DIMENSION_EXTRALABORAL;
        $factorTotal = $factores['total'] ?? 1;
        $baremo = $perfil === 'jefes_profesionales' ? self::BAREMOS_EXTRALABORAL_JEFES_PROFESIONALES['total'] : self::BAREMOS_EXTRALABORAL_AUXILIARES_OPERARIOS['total'];
        $respuestasPorNumero = [];
        foreach ($respuestas as $respuesta) {
            $num = $respuesta['numero_pregunta'] ?? $respuesta['consecutivo'] ?? null;
            if ($num !== null) {
                $respuestasPorNumero[$num] = (int)($respuesta['valor'] ?? 0);
            }
        }
        $suma = 0;
        $totalPregs = 0;
        foreach ($preguntas as $num) {
            $suma += $respuestasPorNumero[$num] ?? 0;
            $totalPregs++;
        }
        $puntaje = $totalPregs > 0 ? ($suma / $totalPregs) * $factorTotal : 0;
        $nivel = 'sin_riesgo';
        if ($baremo) {
            if ($puntaje > $baremo['riesgo_muy_alto'][0]) $nivel = 'muy_alto';
            elseif ($puntaje > $baremo['riesgo_alto'][0]) $nivel = 'alto';
            elseif ($puntaje > $baremo['riesgo_medio'][0]) $nivel = 'medio';
            elseif ($puntaje > $baremo['riesgo_bajo'][0]) $nivel = 'bajo';
            else $nivel = 'sin_riesgo';
        }
        return [
            'puntaje' => $puntaje,
            'nivel' => $nivel
        ];
    }
    // =============================
    // HELPERS DE CÁLCULO EXTRALABORAL Y ESTRÉS
    // =============================

    /**
     * Calcular puntaje, transformación y nivel de riesgo para dimensiones extralaborales
     * @param array $respuestas
     * @param string $perfil 'jefes_profesionales' o 'auxiliares_operarios'
        $nivel = $this->calcularNivelRiesgoDimension($puntajeTransformado, $baremo);
        return [
            'puntaje_bruto' => $puntajeBruto,
            'puntaje_transformado' => $puntajeTransformado,
            'nivel_riesgo' => $nivel
        ];
    }

    /**
     * Calcular puntaje, transformación y nivel de riesgo para estrés
     * @param array $respuestas
     * @param string $perfil
     * @return array
     */
    public function calcularEstres(array $respuestas, string $perfil = 'auxiliares_operarios'): array
    {
        $puntajeTotal = 0;
        foreach (self::FACTOR_VALOR_ESTRES as $grupo => $config) {
            $sumaGrupo = 0;
            $cantidadGrupo = 0;
            foreach ($respuestas as $respuesta) {
                if (in_array($respuesta['consecutivo'], $config['consecutivos'])) {
                    $sumaGrupo += $respuesta['valor'];
                    $cantidadGrupo++;
                }
            }
            if ($cantidadGrupo > 0) {
                $promedioGrupo = $sumaGrupo / $cantidadGrupo;
                $puntajeTotal += $promedioGrupo * $config['multiplicador'];
            }
        }
        $puntajeTransformado = $this->calcularPuntajeTransformadoDimension($puntajeTotal, self::FACTOR_TOTAL_ESTRES);
        $baremo = $perfil === 'jefes_profesionales' ? self::BAREMOS_ESTRES['jefes_profesionales'] : self::BAREMOS_ESTRES['auxiliares_operarios'];
        $nivel = $this->calcularNivelRiesgoDimension($puntajeTransformado, $baremo);
        return [
            'puntaje_bruto' => $puntajeTotal,
            'puntaje_transformado' => $puntajeTransformado,
            'nivel_riesgo' => $nivel
        ];
    }

    // =============================
    // HELPERS DE CÁLCULO POR DIMENSIÓN Y DOMINIO
    // =============================

    /**
     * Calcular puntaje bruto de una dimensión
     */
    private function calcularPuntajeDimension(array $respuestas, array $consecutivos): int
    {
        $puntaje = 0;
        // Detectar tipo y forma si se pasa como argumento extra
        $tipo = func_num_args() > 2 ? func_get_arg(2) : 'intralaboral';
        $forma = func_num_args() > 3 ? func_get_arg(3) : 'A';
        foreach ($respuestas as $respuesta) {
            if (in_array($respuesta['consecutivo'], $consecutivos)) {
                if ($tipo === 'intralaboral') {
                    $calificacion = $forma === 'A' ? self::CALIFICACION_INTRALABORAL_A : self::CALIFICACION_INTRALABORAL_B;
                    if (in_array($respuesta['consecutivo'], $calificacion['ascendente'])) {
                        $puntaje += $respuesta['valor'];
                    } else {
                        $puntaje += (5 - $respuesta['valor']);
                    }
                } elseif ($tipo === 'extralaboral') {
                    if (in_array($respuesta['consecutivo'], self::CALIFICACION_EXTRALABORAL['ascendente'])) {
                        $puntaje += $respuesta['valor'];
                    } else {
                        $puntaje += (5 - $respuesta['valor']);
                    }
                } else {
                    $puntaje += $respuesta['valor'];
                }
            }
        }
        return $puntaje;
    }

    /**
     * Calcular puntaje transformado de una dimensión
     */
    private function calcularPuntajeTransformadoDimension(int $puntajeBruto, $factor): float
    {
        if ($factor > 0) {
            return round(($puntajeBruto / $factor) * 100, 1);
        }
        return 0.0;
    }

    /**
     * Calcular transformación de puntaje (método helper)
     */
    private function calcularTransformacionPuntaje(float $puntajeBruto, $factor): float
    {
        if ($factor > 0) {
            return round(($puntajeBruto / $factor) * 100, 1);
        }
        return 0.0;
    }

    /**
     * Calcular nivel de riesgo de una dimensión
     */
    private function calcularNivelRiesgoDimension(float $puntajeTransformado, array $baremo): string
    {
        if ($puntajeTransformado <= $baremo['sin_riesgo'][1]) {
            return 'sin_riesgo';
        } elseif ($puntajeTransformado <= $baremo['riesgo_bajo'][1]) {
            return 'riesgo_bajo';
        } elseif ($puntajeTransformado <= $baremo['riesgo_medio'][1]) {
            return 'riesgo_medio';
        } elseif ($puntajeTransformado <= $baremo['riesgo_alto'][1]) {
            return 'riesgo_alto';
        } else {
            return 'riesgo_muy_alto';
        }
    }


    // Configuración de calificación para Intralaboral Forma A
    private const CALIFICACION_INTRALABORAL_A = [
        'ascendente' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123],
        'descendente' => []
    ];

    // Configuración de calificación para Intralaboral Forma B
    private const CALIFICACION_INTRALABORAL_B = [
        'ascendente' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97],
        'descendente' => []
    ];

    // Configuración de calificación para Extralaboral
    private const CALIFICACION_EXTRALABORAL = [
        'ascendente' => [1, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 27, 29],
        'descendente' => [2, 3, 6, 24, 26, 28, 30, 31]
    ];

    // Configuración de calificación para Estrés
    private const CALIFICACION_ESTRES = [
        'a' => [1, 2, 3, 9, 13, 14, 15, 23, 24],
        'b' => [4, 5, 6, 10, 11, 16, 17, 18, 19, 25, 26, 27, 28],
        'c' => [7, 8, 12, 20, 21, 22, 29, 30, 31]
    ];

    // Factores de Transformación para Estrés
    private const FACTOR_VALOR_ESTRES = [
        'a' => ['consecutivos' => [1, 2, 3, 4, 5, 6, 7, 8], 'multiplicador' => 4],
        'b' => ['consecutivos' => [9, 10, 11, 12], 'multiplicador' => 3],
        'c' => ['consecutivos' => [13, 14, 15, 16, 17, 18, 19, 20, 21, 22], 'multiplicador' => 2],
        'd' => ['consecutivos' => [23, 24, 25, 26, 27, 28, 29, 30, 31], 'multiplicador' => 1]
    ];

    private const FACTOR_TOTAL_ESTRES = 61.16;

    // =============================
    // BAREMOS Y TABLAS COMPLETAS
    // =============================
    // Baremos para Estrés
    private const BAREMOS_ESTRES = [
        'jefes_profesionales' => [
            'sin_riesgo' => [0, 7.8],
            'riesgo_bajo' => [7.9, 12.6],
            'riesgo_medio' => [12.7, 17.7],
            'riesgo_alto' => [17.8, 25],
            'riesgo_muy_alto' => [25.1, 100]
        ],
        'auxiliares_operarios' => [
            'sin_riesgo' => [0, 6.5],
            'riesgo_bajo' => [6.6, 11.8],
            'riesgo_medio' => [11.9, 17],
            'riesgo_alto' => [17.1, 23.4],
            'riesgo_muy_alto' => [23.5, 100]
        ]
    ];

    // Baremos por dimensión y dominio para Intralaboral Forma A
    private const BAREMOS_DIMENSIONES_INTRALABORAL_A = [
        'caracteristicasDelLiderazgo' => [ 'sin_riesgo' => [0, 3.8], 'riesgo_bajo' => [3.9, 15.4], 'riesgo_medio' => [15.5, 30.8], 'riesgo_alto' => [30.9, 46.2], 'riesgo_muy_alto' => [46.3, 100] ],
        'relacionesSocialesEnElTrabajo' => [ 'sin_riesgo' => [0, 5.4], 'riesgo_bajo' => [5.5, 16.1], 'riesgo_medio' => [16.2, 25], 'riesgo_alto' => [25.1, 37.5], 'riesgo_muy_alto' => [37.6, 100] ],
        'retroalimentacionDeDesempeno' => [ 'sin_riesgo' => [0, 5.4], 'riesgo_bajo' => [5.5, 16.1], 'riesgo_medio' => [16.2, 25], 'riesgo_alto' => [25.1, 37.5], 'riesgo_muy_alto' => [37.6, 100] ],
        'relacionConLosColaboradores' => [ 'sin_riesgo' => [0, 13.9], 'riesgo_bajo' => [14, 25], 'riesgo_medio' => [25.1, 33.3], 'riesgo_alto' => [33.4, 47.2], 'riesgo_muy_alto' => [47.3, 100] ],
        'claridadDelRol' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 10.7], 'riesgo_medio' => [10.8, 21.4], 'riesgo_alto' => [21.5, 39.3], 'riesgo_muy_alto' => [30.4, 100] ],
        'capacitacion' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 16.7], 'riesgo_medio' => [16.8, 33.3], 'riesgo_alto' => [33.4, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'participacionManejoDelCambio' => [ 'sin_riesgo' => [0, 12.5], 'riesgo_bajo' => [12.6, 25], 'riesgo_medio' => [25.1, 37.5], 'riesgo_alto' => [37.6, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'desarrolloHabilidades' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 6.3], 'riesgo_medio' => [6.4, 18.8], 'riesgo_alto' => [18.9, 31.3], 'riesgo_muy_alto' => [31.4, 100] ],
        'autonomiaSobreElTrabajo' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 25], 'riesgo_medio' => [25.1, 41.7], 'riesgo_alto' => [41.8, 58.3], 'riesgo_muy_alto' => [58.4, 100] ],
        'ambientalesYEsfuerzoFisico' => [ 'sin_riesgo' => [0, 14.6], 'riesgo_bajo' => [14.7, 22.9], 'riesgo_medio' => [23, 31.3], 'riesgo_alto' => [31.4, 39.6], 'riesgo_muy_alto' => [39.7, 100] ],
        'demandasEmocionales' => [ 'sin_riesgo' => [0, 16.7], 'riesgo_bajo' => [16.8, 25], 'riesgo_medio' => [25.1, 33.3], 'riesgo_alto' => [33.4, 47.2], 'riesgo_muy_alto' => [47.3, 100] ],
        'demandasCuantitativas' => [ 'sin_riesgo' => [0, 25], 'riesgo_bajo' => [25.1, 33.3], 'riesgo_medio' => [33.4, 45.8], 'riesgo_alto' => [45.9, 54.2], 'riesgo_muy_alto' => [54.3, 100] ],
        'trabajoSobreExtralaboral' => [ 'sin_riesgo' => [0, 18.8], 'riesgo_bajo' => [18.9, 31.3], 'riesgo_medio' => [31.4, 43.8], 'riesgo_alto' => [43.9, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'exigenciasResponsabilidadCargo' => [ 'sin_riesgo' => [0, 37.5], 'riesgo_bajo' => [37.6, 54.2], 'riesgo_medio' => [54.3, 66.7], 'riesgo_alto' => [66.8, 79.2], 'riesgo_muy_alto' => [79.3, 100] ],
        'demandasCargaMental' => [ 'sin_riesgo' => [0, 60], 'riesgo_bajo' => [60.1, 70], 'riesgo_medio' => [70.1, 80], 'riesgo_alto' => [80.1, 90], 'riesgo_muy_alto' => [90.1, 100] ],
        'consistenciaDelRol' => [ 'sin_riesgo' => [0, 15], 'riesgo_bajo' => [15.1, 25], 'riesgo_medio' => [25.1, 35], 'riesgo_alto' => [35.1, 45], 'riesgo_muy_alto' => [45.1, 100] ],
        'demandasJornadaTrabajo' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 25], 'riesgo_medio' => [25.1, 33.3], 'riesgo_alto' => [33.4, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'recompensasPorTrabajo' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 5], 'riesgo_medio' => [5.1, 10], 'riesgo_alto' => [10.1, 20], 'riesgo_muy_alto' => [20.1, 100] ],
        'reconocimientoYCompensacion' => [ 'sin_riesgo' => [0, 4.2], 'riesgo_bajo' => [4.3, 16.7], 'riesgo_medio' => [16.8, 25], 'riesgo_alto' => [25.1, 37.5], 'riesgo_muy_alto' => [37.6, 100] ]
    ];

    // Baremos por dimensión y dominio para Intralaboral Forma B
    private const BAREMOS_DIMENSIONES_INTRALABORAL_B = [
        'caracteristicasDelLiderazgo' => [ 'sin_riesgo' => [0, 3.8], 'riesgo_bajo' => [3.9, 13.5], 'riesgo_medio' => [13.6, 25], 'riesgo_alto' => [25.1, 38.5], 'riesgo_muy_alto' => [38.6, 100] ],
        'relacionesSocialesEnElTrabajo' => [ 'sin_riesgo' => [0, 6.3], 'riesgo_bajo' => [6.4, 14.6], 'riesgo_medio' => [14.7, 27.1], 'riesgo_alto' => [27.2, 37.5], 'riesgo_muy_alto' => [37.6, 100] ],
        'retroalimentacionDeDesempeno' => [ 'sin_riesgo' => [0, 5], 'riesgo_bajo' => [5.1, 20], 'riesgo_medio' => [20.1, 30], 'riesgo_alto' => [30.1, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'claridadDelRol' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 5], 'riesgo_medio' => [5.1, 15], 'riesgo_alto' => [15.1, 30], 'riesgo_muy_alto' => [30.1, 100] ],
        'capacitacion' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 16.7], 'riesgo_medio' => [16.8, 25], 'riesgo_alto' => [21.1, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'participacionManejoDelCambio' => [ 'sin_riesgo' => [0, 16.7], 'riesgo_bajo' => [16.8, 33.3], 'riesgo_medio' => [33.4, 41.7], 'riesgo_alto' => [41.8, 58.3], 'riesgo_muy_alto' => [58.4, 100] ],
        'desarrolloHabilidades' => [ 'sin_riesgo' => [0, 12.5], 'riesgo_bajo' => [12.6, 25], 'riesgo_medio' => [25.1, 37.5], 'riesgo_alto' => [37.6, 56.3], 'riesgo_muy_alto' => [56.4, 100] ],
        'autonomiaSobreElTrabajo' => [ 'sin_riesgo' => [0, 33.3], 'riesgo_bajo' => [33.4, 50], 'riesgo_medio' => [50.1, 66.7], 'riesgo_alto' => [66.8, 75], 'riesgo_muy_alto' => [75.1, 100] ],
        'ambientalesYEsfuerzoFisico' => [ 'sin_riesgo' => [0, 22.9], 'riesgo_bajo' => [23, 31.3], 'riesgo_medio' => [31.4, 39.6], 'riesgo_alto' => [39.7, 47.9], 'riesgo_muy_alto' => [48, 100] ],
        'demandasEmocionales' => [ 'sin_riesgo' => [0, 19.4], 'riesgo_bajo' => [19.5, 27.8], 'riesgo_medio' => [27.9, 38.9], 'riesgo_alto' => [39, 47.2], 'riesgo_muy_alto' => [47.3, 100] ],
        'demandasCuantitativas' => [ 'sin_riesgo' => [0, 16.7], 'riesgo_bajo' => [16.8, 33.3], 'riesgo_medio' => [33.4, 41.7], 'riesgo_alto' => [41.8, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'trabajoSobreExtralaboral' => [ 'sin_riesgo' => [0, 12.5], 'riesgo_bajo' => [12.6, 25], 'riesgo_medio' => [25.1, 31.3], 'riesgo_alto' => [31.4, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'demandasCargaMental' => [ 'sin_riesgo' => [0, 50], 'riesgo_bajo' => [50.1, 65], 'riesgo_medio' => [65.1, 75], 'riesgo_alto' => [75.1, 85], 'riesgo_muy_alto' => [85.1, 100] ],
        'demandasJornadaTrabajo' => [ 'sin_riesgo' => [0, 25], 'riesgo_bajo' => [25.1, 37.5], 'riesgo_medio' => [37.6, 45.8], 'riesgo_alto' => [45.9, 58.3], 'riesgo_muy_alto' => [58.4, 100] ],
        'recompensasPorTrabajo' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 6.3], 'riesgo_medio' => [6.4, 12.5], 'riesgo_alto' => [12.6, 18.8], 'riesgo_muy_alto' => [18.9, 100] ],
        'reconocimientoYCompensacion' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1.0, 12.5], 'riesgo_medio' => [12.6, 25], 'riesgo_alto' => [25.1, 37.5], 'riesgo_muy_alto' => [37.6, 100] ]
    ];

    // Baremos por dominio para Intralaboral Forma A
    private const BAREMOS_DOMINIOS_INTRALABORAL_A = [
        'liderazgoRelacionesSociales' => [ 'sin_riesgo' => [0, 9.1], 'riesgo_bajo' => [9.2, 17.7], 'riesgo_medio' => [17.8, 25.6], 'riesgo_alto' => [25.7, 34.8], 'riesgo_muy_alto' => [34.9, 100] ],
        'controlSobreElTrabajo' => [ 'sin_riesgo' => [0, 10.7], 'riesgo_bajo' => [10.8, 19], 'riesgo_medio' => [19.1, 29.8], 'riesgo_alto' => [29.9, 40.5], 'riesgo_muy_alto' => [40.6, 100] ],
        'demandasDelTrabajo' => [ 'sin_riesgo' => [0, 28.5], 'riesgo_bajo' => [28.6, 35], 'riesgo_medio' => [35.1, 41.5], 'riesgo_alto' => [41.6, 47.5], 'riesgo_muy_alto' => [47.6, 100] ],
        'recompensas' => [ 'sin_riesgo' => [0, 4.5], 'riesgo_bajo' => [4.6, 11.4], 'riesgo_medio' => [11.5, 20.5], 'riesgo_alto' => [20.6, 29.5], 'riesgo_muy_alto' => [29.6, 100] ]
    ];

    // Baremos por dominio para Intralaboral Forma B
    private const BAREMOS_DOMINIOS_INTRALABORAL_B = [
        'liderazgoRelacionesSociales' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 17.5], 'riesgo_medio' => [17.6, 26.7], 'riesgo_alto' => [26.8, 38.3], 'riesgo_muy_alto' => [38.4, 100] ],
        'controlSobreElTrabajo' => [ 'sin_riesgo' => [0, 19.4], 'riesgo_bajo' => [19.5, 26.4], 'riesgo_medio' => [26.5, 34.7], 'riesgo_alto' => [34.8, 43.1], 'riesgo_muy_alto' => [43.2, 100] ],
        'demandasDelTrabajo' => [ 'sin_riesgo' => [0, 26.9], 'riesgo_bajo' => [27, 33.3], 'riesgo_medio' => [33.4, 37.8], 'riesgo_alto' => [37.9, 44.2], 'riesgo_muy_alto' => [44.3, 100] ],
        'recompensas' => [ 'sin_riesgo' => [0, 2.5], 'riesgo_bajo' => [2.6, 10], 'riesgo_medio' => [10.1, 17.5], 'riesgo_alto' => [17.6, 27.5], 'riesgo_muy_alto' => [27.6, 100] ]
    ];

    // Baremos total para Intralaboral
    private const BAREMOS_TOTAL_INTRALABORAL = [
        'A' => [ 'sin_riesgo' => [0, 19.7], 'riesgo_bajo' => [19.8, 25.8], 'riesgo_medio' => [25.9, 31.5], 'riesgo_alto' => [31.6, 38], 'riesgo_muy_alto' => [38.1, 100] ],
        'B' => [ 'sin_riesgo' => [0, 20.6], 'riesgo_bajo' => [20.7, 26], 'riesgo_medio' => [26.1, 31.2], 'riesgo_alto' => [31.3, 38.7], 'riesgo_muy_alto' => [38.8, 100] ]
    ];

    // Baremos extralaboral por perfil
    private const BAREMOS_EXTRALABORAL_JEFES_PROFESIONALES = [
        'tiempoFueraDelTrabajo' => [ 'sin_riesgo' => [0, 6.3], 'riesgo_bajo' => [6.4, 25], 'riesgo_medio' => [25.1, 37.5], 'riesgo_alto' => [37.6, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'relacionesFamiliares' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 25], 'riesgo_medio' => [25.1, 33.3], 'riesgo_alto' => [33.4, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'relacionesInterpersonales' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 10], 'riesgo_medio' => [10.1, 20], 'riesgo_alto' => [20.1, 30], 'riesgo_muy_alto' => [30.1, 100] ],
        'situacionEconomica' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 25], 'riesgo_medio' => [25.1, 33.3], 'riesgo_alto' => [33.4, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'caracteristicasVivienda' => [ 'sin_riesgo' => [0, 5.6], 'riesgo_bajo' => [5.7, 11.1], 'riesgo_medio' => [11.2, 13.9], 'riesgo_alto' => [14, 22.2], 'riesgo_muy_alto' => [22.3, 100] ],
        'influenciaTrabajo' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 16.7], 'riesgo_medio' => [16.8, 25], 'riesgo_alto' => [25.1, 41.7], 'riesgo_muy_alto' => [41.8, 100] ],
        'dezplazamiento' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 12.5], 'riesgo_medio' => [12.6, 25], 'riesgo_alto' => [25.1, 43.8], 'riesgo_muy_alto' => [43.9, 100] ],
        'total' => [ 'sin_riesgo' => [0, 11.3], 'riesgo_bajo' => [11.4, 16.9], 'riesgo_medio' => [17, 22.6], 'riesgo_alto' => [22.7, 29], 'riesgo_muy_alto' => [29.1, 100] ]
    ];
    private const BAREMOS_EXTRALABORAL_AUXILIARES_OPERARIOS = [
        'tiempoFueraDelTrabajo' => [ 'sin_riesgo' => [0, 6.3], 'riesgo_bajo' => [6.4, 25], 'riesgo_medio' => [25.1, 37.5], 'riesgo_alto' => [37.6, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'relacionesFamiliares' => [ 'sin_riesgo' => [0, 8.3], 'riesgo_bajo' => [8.4, 25], 'riesgo_medio' => [25.1, 33.3], 'riesgo_alto' => [33.4, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'relacionesInterpersonales' => [ 'sin_riesgo' => [0, 5], 'riesgo_bajo' => [5.1, 15], 'riesgo_medio' => [15.1, 25], 'riesgo_alto' => [25.1, 35], 'riesgo_muy_alto' => [35.1, 100] ],
        'situacionEconomica' => [ 'sin_riesgo' => [0, 16.7], 'riesgo_bajo' => [16.8, 25], 'riesgo_medio' => [25.1, 41.7], 'riesgo_alto' => [41.8, 50], 'riesgo_muy_alto' => [50.1, 100] ],
        'caracteristicasVivienda' => [ 'sin_riesgo' => [0, 5.6], 'riesgo_bajo' => [5.7, 11.1], 'riesgo_medio' => [11.2, 16.7], 'riesgo_alto' => [16.8, 27.8], 'riesgo_muy_alto' => [27.9, 100] ],
        'influenciaTrabajo' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 16.7], 'riesgo_medio' => [16.8, 25], 'riesgo_alto' => [25.1, 41.7], 'riesgo_muy_alto' => [41.8, 100] ],
        'dezplazamiento' => [ 'sin_riesgo' => [0, 0.9], 'riesgo_bajo' => [1, 12.5], 'riesgo_medio' => [12.6, 25], 'riesgo_alto' => [25.1, 43.8], 'riesgo_muy_alto' => [43.9, 100] ],
        'total' => [ 'sin_riesgo' => [0, 12.9], 'riesgo_bajo' => [13, 17.7], 'riesgo_medio' => [17.8, 24.2], 'riesgo_alto' => [24.3, 32.3], 'riesgo_muy_alto' => [32.4, 100] ]
    ];
    // Baremos total general
    private const BAREMOS_TOTAL_GENERAL = [
        'A' => [ 'sin_riesgo' => [0, 18.8], 'riesgo_bajo' => [18.9, 24.4], 'riesgo_medio' => [24.5, 29.5], 'riesgo_alto' => [29.6, 35.4], 'riesgo_muy_alto' => [35.5, 100] ],
        'B' => [ 'sin_riesgo' => [0, 19.9], 'riesgo_bajo' => [20, 24.8], 'riesgo_medio' => [24.9, 29.5], 'riesgo_alto' => [29.6, 35.4], 'riesgo_muy_alto' => [35.5, 100] ]
    ];
    
    // ===========================================
    // DIMENSIONES INTRALABORAL FORMA A
    // ===========================================
    
    private const DIMENSION_CARACTERISTICAS_DEL_LIDERAZGO_A = [63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75];
    private const DIMENSION_RELACIONES_SOCIALES_A = [76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89];
    private const DIMENSION_RETROALIMENTACION_DESEMPENO_A = [90, 91, 92, 93, 94];
    private const DIMENSION_COLABORADORES_A = [115, 116, 117, 118, 119, 120, 121, 122, 123];
    private const DIMENSION_CLARIDAD_ROL_A = [53, 54, 55, 56, 57, 58, 59];
    private const DIMENSION_CAPACITACION_A = [60, 61, 62];
    private const DIMENSION_PARTICIPACION_MANEJO_CAMBIO_A = [48, 49, 50, 51];
    private const DIMENSION_DESARROLLO_HABILIDADES_A = [39, 40, 41, 42];
    private const DIMENSION_AUTONOMIA_TRABAJO_A = [44, 45, 46];
    private const DIMENSION_ESFUERZO_FISICO_A = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    private const DIMENSION_DEMANDAS_EMOCIONALES_A = [106, 107, 108, 109, 110, 111, 112, 113, 114];
    private const DIMENSION_DEMANDAS_CUANTITATIVAS_A = [13, 14, 15, 32, 43, 47];
    private const DIMENSION_INFLUENCIA_EXTRALABORAL_A = [35, 36, 37, 38];
    private const DIMENSION_RESPONSABILIDAD_CARGO_A = [19, 22, 23, 24, 25, 26];
    private const DIMENSION_CARGA_MENTAL_A = [16, 17, 18, 20, 21];
    private const DIMENSION_CONSISTENCIA_ROL_A = [27, 28, 29, 30, 52];
    private const DIMENSION_JORNADA_TRABAJO_A = [31, 33, 34];
    private const DIMENSION_RECOMPENSA_TRABAJO_A = [95, 102, 103, 104, 105];
    private const DIMENSION_RECONOCIMIENTO_A = [96, 97, 98, 99, 100, 101];

    // ===========================================
    // DIMENSIONES INTRALABORAL FORMA B
    // ===========================================
    
    private const DIMENSION_CARACTERISTICAS_DEL_LIDERAZGO_B = [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61];
    private const DIMENSION_RELACIONES_SOCIALES_B = [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73];
    private const DIMENSION_RETROALIMENTACION_DESEMPENO_B = [74, 75, 76, 77, 78];
    private const DIMENSION_CLARIDAD_ROL_B = [41, 42, 43, 44, 45];
    private const DIMENSION_CAPACITACION_B = [46, 47, 48];
    private const DIMENSION_PARTICIPACION_MANEJO_CAMBIO_B = [38, 39, 40];
    private const DIMENSION_DESARROLLO_HABILIDADES_B = [29, 30, 31, 32];
    private const DIMENSION_AUTONOMIA_TRABAJO_B = [34, 35, 36];
    private const DIMENSION_ESFUERZO_FISICO_B = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    private const DIMENSION_DEMANDAS_EMOCIONALES_B = [89, 90, 91, 92, 93, 94, 95, 96, 97];
    private const DIMENSION_DEMANDAS_CUANTITATIVAS_B = [13, 14, 15];
    private const DIMENSION_INFLUENCIA_EXTRALABORAL_B = [25, 26, 27, 28];
    private const DIMENSION_CARGA_MENTAL_B = [16, 17, 18, 19, 20];
    private const DIMENSION_JORNADA_TRABAJO_B = [21, 22, 23, 24, 33, 37];
    private const DIMENSION_RECOMPENSA_TRABAJO_B = [85, 86, 87, 88];
    private const DIMENSION_RECONOCIMIENTO_B = [79, 80, 81, 82, 83, 84];
    private const DIMENSION_AMBIENTE_FISICO_B = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    // ===========================================
    // DIMENSIONES EXTRALABORAL
    // ===========================================
    
    private const DIMENSION_TIEMPO_FUERA_TRABAJO = [14, 15, 16, 17];
    private const DIMENSION_RELACIONES_FAMILIARES = [22, 25, 27];
    private const DIMENSION_COMUNICACION = [18, 19, 20, 21, 23];
    private const DIMENSION_SITUACION_ECONOMICA = [29, 30, 31];
    private const DIMENSION_CARACTERISTICAS_VIVIENDA = [5, 6, 7, 8, 9, 10, 11, 12, 13];
    private const DIMENSION_INFLUENCIA_TRABAJO = [24, 26, 28];
    private const DIMENSION_DESPLAZAMIENTO = [1, 2, 3, 4];

    // ===========================================
    // FACTORES DE TRANSFORMACIÓN
    // ===========================================
    
    // Factores de transformación Intralaboral
    private const FACTOR_DIMENSION_INTRALABORAL = [
        'caracteristicasDelLiderazgo' => ['A' => 52, 'B' => 52],
        'relacionesSocialesEnElTrabajo' => ['A' => 56, 'B' => 48],
        'retroalimentacionDeDesempeno' => ['A' => 20, 'B' => 20],
        'relacionConLosColaboradores' => ['A' => 36, 'B' => null],
        'claridadDelRol' => ['A' => 28, 'B' => 20],
        'capacitacion' => ['A' => 12, 'B' => 12],
        'participacionManejoDelCambio' => ['A' => 16, 'B' => 12],
        'desarrolloHabilidades' => ['A' => 16, 'B' => 16],
        'autonomiaSobreElTrabajo' => ['A' => 12, 'B' => 12],
        'ambientalesYEsfuerzoFisico' => ['A' => 48, 'B' => 48],
        'demandasEmocionales' => ['A' => 36, 'B' => 36],
        'demandasCuantitativas' => ['A' => 24, 'B' => 12],
        'trabajoSobreExtralaboral' => ['A' => 16, 'B' => 16],
        'exigenciasResponsabilidadCargo' => ['A' => 24, 'B' => null],
        'demandasCargaMental' => ['A' => 20, 'B' => 20],
        'consistenciaDelRol' => ['A' => 20, 'B' => null],
        'demandasJornadaTrabajo' => ['A' => 12, 'B' => 24],
        'recompensasPorTrabajo' => ['A' => 20, 'B' => 16],
        'reconocimientoYCompensacion' => ['A' => 24, 'B' => 24],
        'total' => ['A' => 492, 'B' => 388]
    ];

    private const FACTOR_DOMINIO_INTRALABORAL = [
        'liderazgoRelacionesSociales' => ['A' => 164, 'B' => 120],
        'controlSobreElTrabajo' => ['A' => 84, 'B' => 72],
        'demandasDelTrabajo' => ['A' => 200, 'B' => 156],
        'recompensas' => ['A' => 44, 'B' => 40]
    ];

    // Factores de transformación Extralaboral
    private const FACTOR_DIMENSION_EXTRALABORAL = [
        'tiempoFueraDelTrabajo' => 16,
        'relacionesFamiliares' => 12,
        'relacionesInterpersonales' => 20,
        'situacionEconomica' => 12,
        'caracteristicasVivienda' => 36,
        'influenciaTrabajo' => 12,
        'desplazamiento' => 16,
        'total' => 124
    ];

    // Factores totales generales
    private const FACTOR_TOTAL_GENERAL = [
        'A' => 616,
        'B' => 512
    ];

    // ===========================================
    // MÉTODOS PRINCIPALES
    // ===========================================

    /**
     * Obtener puntaje bruto de una dimensión basado en las respuestas
     */
    public function obtenerPuntaje(array $respuestas, array $preguntas, string $tipo = 'ascendente'): int
    {
        $puntaje = 0;
        
        foreach ($respuestas as $respuesta) {
            if (in_array($respuesta['consecutivo'], $preguntas)) {
                $puntaje += $respuesta['valor'];
            }
        }

        return $puntaje;
    }

    /**
     * Obtener puntaje de estrés con promedio y multiplicador
     */
    public function obtenerPuntajeEstres(array $respuestas, array $preguntas, float $multiplicador = 1.0, string $forma = 'A'): array
    {
        $suma = 0;
        $dominios = [];
        $dominiosDef = $forma === 'A' ? [
            'liderazgoRelacionesSociales' => array_merge(self::DIMENSION_CARACTERISTICAS_DEL_LIDERAZGO_A, self::DIMENSION_RELACIONES_SOCIALES_A, self::DIMENSION_RETROALIMENTACION_DESEMPENO_A, self::DIMENSION_COLABORADORES_A),
            'controlSobreElTrabajo' => array_merge(self::DIMENSION_CLARIDAD_ROL_A, self::DIMENSION_CAPACITACION_A, self::DIMENSION_PARTICIPACION_MANEJO_CAMBIO_A, self::DIMENSION_DESARROLLO_HABILIDADES_A, self::DIMENSION_AUTONOMIA_TRABAJO_A),
            'demandasDelTrabajo' => array_merge(self::DIMENSION_ESFUERZO_FISICO_A, self::DIMENSION_DEMANDAS_EMOCIONALES_A, self::DIMENSION_DEMANDAS_CUANTITATIVAS_A, self::DIMENSION_INFLUENCIA_EXTRALABORAL_A, self::DIMENSION_RESPONSABILIDAD_CARGO_A, self::DIMENSION_CARGA_MENTAL_A, self::DIMENSION_CONSISTENCIA_ROL_A, self::DIMENSION_JORNADA_TRABAJO_A),
            'recompensas' => array_merge(self::DIMENSION_RECOMPENSA_TRABAJO_A, self::DIMENSION_RECONOCIMIENTO_A)
        ] : [
            'liderazgoRelacionesSociales' => array_merge(self::DIMENSION_CARACTERISTICAS_DEL_LIDERAZGO_B, self::DIMENSION_RELACIONES_SOCIALES_B, self::DIMENSION_RETROALIMENTACION_DESEMPENO_B),
            'controlSobreElTrabajo' => array_merge(self::DIMENSION_CLARIDAD_ROL_B, self::DIMENSION_CAPACITACION_B, self::DIMENSION_PARTICIPACION_MANEJO_CAMBIO_B, self::DIMENSION_DESARROLLO_HABILIDADES_B, self::DIMENSION_AUTONOMIA_TRABAJO_B),
            'demandasDelTrabajo' => array_merge(self::DIMENSION_ESFUERZO_FISICO_B, self::DIMENSION_DEMANDAS_EMOCIONALES_B, self::DIMENSION_DEMANDAS_CUANTITATIVAS_B, self::DIMENSION_INFLUENCIA_EXTRALABORAL_B, self::DIMENSION_CARGA_MENTAL_B, self::DIMENSION_JORNADA_TRABAJO_B),
            'recompensas' => array_merge(self::DIMENSION_RECOMPENSA_TRABAJO_B, self::DIMENSION_RECONOCIMIENTO_B)
        ];
        $baremos = $forma === 'A' ? self::BAREMOS_DOMINIOS_INTRALABORAL_A : self::BAREMOS_DOMINIOS_INTRALABORAL_B;
        $factores = self::FACTOR_DOMINIO_INTRALABORAL;
        foreach ($dominiosDef as $nombre => $consecutivos) {
            $puntajeBruto = $this->calcularPuntajeDimension($respuestas, $consecutivos, 'intralaboral', $forma);
            $factor = $factores[$nombre][$forma] ?? null;
            $puntajeTransformado = $factor ? $this->calcularPuntajeTransformadoDimension($puntajeBruto, $factor) : 0.0;
            $nivel = isset($baremos[$nombre]) ? $this->calcularNivelRiesgoDimension($puntajeTransformado, $baremos[$nombre]) : null;
            $dominios[$nombre] = [
                'puntaje_bruto' => $puntajeBruto,
                'puntaje_transformado' => $puntajeTransformado,
                'nivel_riesgo' => $nivel
            ];
        }
        return $dominios;
    }

    public function calcularNivelRiesgoPsicosocial(float $puntajeTransformado, array $baremos): string
    {
        if ($puntajeTransformado <= $baremos['sin_riesgo'][1]) {
            return 'sin_riesgo';
        } elseif ($puntajeTransformado >= $baremos['riesgo_bajo'][0] && $puntajeTransformado <= $baremos['riesgo_bajo'][1]) {
            return 'riesgo_bajo';
        } elseif ($puntajeTransformado >= $baremos['riesgo_medio'][0] && $puntajeTransformado <= $baremos['riesgo_medio'][1]) {
            return 'riesgo_medio';
        } elseif ($puntajeTransformado >= $baremos['riesgo_alto'][0] && $puntajeTransformado <= $baremos['riesgo_alto'][1]) {
            return 'riesgo_alto';
        } elseif ($puntajeTransformado >= $baremos['riesgo_muy_alto'][0]) {
            return 'riesgo_muy_alto';
        }

        return 'sin_riesgo';
    }

    // =================================================================
    // MÉTODOS DE PROCESAMIENTO COMPLETO Y ESTADÍSTICAS (RESTAURADOS)
    // =================================================================

    /**
     * Procesar evaluación completa de un empleado.
     * Este método debe ser implementado con la lógica completa.
     */
    public function procesarEvaluacion(array $datos): array
    {
        // Se espera que $datos contenga: 'respuestas_intralaboral', 'respuestas_extralaboral', 'respuestas_estres', 'perfil', 'forma'
        $perfil = $datos['perfil'] ?? 'auxiliares_operarios';
        $forma = $datos['forma'] ?? 'A';
        $respuestasIntralaboral = $datos['respuestas_intralaboral'] ?? [];
        $respuestasExtralaboral = $datos['respuestas_extralaboral'] ?? [];
        $respuestasEstres = $datos['respuestas_estres'] ?? [];

        $dimensionesIntralaboral = $this->calcularDimensionesIntralaboral($respuestasIntralaboral, $forma);
        $dominiosIntralaboral = $this->calcularDominiosIntralaboral($respuestasIntralaboral, $forma);
        $dimensionesExtralaboral = $this->calcularDimensionesExtralaboral($respuestasExtralaboral, $perfil);
        $totalExtralaboral = $this->calcularTotalExtralaboral($respuestasExtralaboral, $perfil);
        $estres = $this->calcularEstres($respuestasEstres, $perfil);

        return [
            'intralaboral' => [
                'dimensiones' => $dimensionesIntralaboral,
                'dominios' => $dominiosIntralaboral
            ],
            'extralaboral' => [
                'dimensiones' => $dimensionesExtralaboral,
                'total' => $totalExtralaboral
            ],
            'estres' => $estres
        ];
    }

    /**
     * Calcular resumen de un diagnóstico.
     * Este método debe ser implementado con la lógica completa.
     */
    public function calcularResumenDiagnostico(array $respuestas): array
    {
        // $respuestas debe contener: 'intralaboral', 'extralaboral', 'estres', 'perfil', 'forma'
        $perfil = $respuestas['perfil'] ?? 'auxiliares_operarios';
        $forma = $respuestas['forma'] ?? 'A';
        $resIntra = $respuestas['intralaboral'] ?? [];
        $resExtra = $respuestas['extralaboral'] ?? [];
        $resEstres = $respuestas['estres'] ?? [];

        $intra = $this->calcularDimensionesIntralaboral($resIntra, $forma);
        $dominios = $this->calcularDominiosIntralaboral($resIntra, $forma);
        $extra = $this->calcularDimensionesExtralaboral($resExtra, $perfil);
        $totalExtra = $this->calcularTotalExtralaboral($resExtra, $perfil);
        $estres = $this->calcularEstres($resEstres, $perfil);

        return [
            'intralaboral' => $intra,
            'dominios' => $dominios,
            'extralaboral' => $extra,
            'total_extralaboral' => $totalExtra,
            'estres' => $estres
        ];
    }

    /**
     * Generar las nueve secciones estadísticas requeridas.
     * Este método debe ser implementado con la lógica completa.
     */
    public function generarNueveSeccionesEstadisticas(array $hojas): array
    {
        // Cada hoja debe tener respuestas y perfil/forma
        $estadisticas = [];
        foreach ($hojas as $hoja) {
            $resIntra = $hoja['respuestas_intralaboral'] ?? [];
            $resExtra = $hoja['respuestas_extralaboral'] ?? [];
            $resEstres = $hoja['respuestas_estres'] ?? [];
            $perfil = $hoja['perfil'] ?? 'auxiliares_operarios';
            $forma = $hoja['forma'] ?? 'A';
            $estadisticas[] = [
                'intralaboral' => $this->calcularDimensionesIntralaboral($resIntra, $forma),
                'dominios' => $this->calcularDominiosIntralaboral($resIntra, $forma),
                'extralaboral' => $this->calcularDimensionesExtralaboral($resExtra, $perfil),
                'total_extralaboral' => $this->calcularTotalExtralaboral($resExtra, $perfil),
                'estres' => $this->calcularEstres($resEstres, $perfil)
            ];
        }
        return $estadisticas;
    }

    /**
     * Obtener estadísticas generales para el dashboard.
     * Este método debe ser implementado con la lógica completa.
     */
    public function obtenerEstadisticasGenerales(?string $empresa_id = null): array
    {
        // Ejemplo: obtener todas las hojas de la empresa y calcular estadísticas agregadas
        $client = new \MongoDB\Client('mongodb://localhost:27017');
        $db = $client->psicosocial;
        $filtro = $empresa_id ? ['empresa_id' => $empresa_id] : [];
        $hojas = $db->hojas->find($filtro);
        $estadisticas = [
            'total' => 0,
            'intralaboral' => [],
            'extralaboral' => [],
            'estres' => []
        ];
        foreach ($hojas as $hoja) {
            $resIntra = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'intralaboral']);
            $resExtra = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'extralaboral']);
            $resEstres = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'estres']);
            $perfil = $hoja['perfil'] ?? 'auxiliares_operarios';
            $forma = $hoja['forma'] ?? 'A';
            $estadisticas['intralaboral'][] = $this->calcularDimensionesIntralaboral(iterator_to_array($resIntra), $forma);
            $estadisticas['extralaboral'][] = $this->calcularDimensionesExtralaboral(iterator_to_array($resExtra), $perfil);
            $estadisticas['estres'][] = $this->calcularEstres(iterator_to_array($resEstres), $perfil);
            $estadisticas['total']++;
        }
        return $estadisticas;
    }

    /**
     * Obtener distribución de riesgo para el dashboard.
     * Este método debe ser implementado con la lógica completa.
     */
    public function obtenerDistribucionRiesgo(?string $empresa_id = null): array
    {
        // Ejemplo: distribución de niveles de riesgo general por empresa
        $client = new \MongoDB\Client('mongodb://localhost:27017');
        $db = $client->psicosocial;
        $filtro = $empresa_id ? ['empresa_id' => $empresa_id] : [];
        $hojas = $db->hojas->find($filtro);
        $niveles = ['sin_riesgo' => 0, 'riesgo_bajo' => 0, 'riesgo_medio' => 0, 'riesgo_alto' => 0, 'riesgo_muy_alto' => 0];
        foreach ($hojas as $hoja) {
            $resIntra = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'intralaboral']);
            $resExtra = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'extralaboral']);
            $resEstres = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'estres']);
            $perfil = $hoja['perfil'] ?? 'auxiliares_operarios';
            $forma = $hoja['forma'] ?? 'A';
            $nivelIntra = $this->calcularNivelRiesgoIntralaboral($resIntra);
            $nivelExtra = $this->calcularNivelRiesgoExtralaboral($resExtra);
            $nivelEstres = $this->calcularNivelRiesgoEstres($resEstres);
            $nivelGeneral = $this->calcularNivelRiesgoGeneralCombinado($nivelIntra, $nivelExtra, $nivelEstres);
            if (isset($niveles[$nivelGeneral])) {
                $niveles[$nivelGeneral]++;
            }
        }
        return $niveles;
    }

    /**
     * Calcular nivel de riesgo intralaboral basado en respuestas
     */
    private function calcularNivelRiesgoIntralaboral($respuestas): string
    {
        try {
            $respuestasArray = iterator_to_array($respuestas);
            
            if (empty($respuestasArray)) {
                return 'sin_riesgo';
            }

            // Determinar si es Forma A o B (simplificado por el momento)
            $esFormaA = count($respuestasArray) > 97; // Forma A tiene 123 preguntas, Forma B tiene 97
            
            // Calcular puntaje total
            $puntajeTotal = 0;
            foreach ($respuestasArray as $respuesta) {
                $valor = $respuesta['valor'] ?? 0;
                $consecutivo = $respuesta['consecutivo'] ?? 0;
                
                // Aplicar calificación ascendente o descendente según la pregunta
                $calificacion = $esFormaA ? self::CALIFICACION_INTRALABORAL_A : self::CALIFICACION_INTRALABORAL_B;
                
                if (in_array($consecutivo, $calificacion['ascendente'])) {
                    $puntajeTotal += $valor;
                } else {
                    // Invertir la calificación para preguntas descendentes
                    $puntajeTotal += (5 - $valor);
                }
            }
            
            // Transformar puntaje
            $factor = $esFormaA ? self::FACTOR_DIMENSION_INTRALABORAL['total']['A'] : self::FACTOR_DIMENSION_INTRALABORAL['total']['B'];
            $puntajeTransformado = $this->calcularTransformacionPuntaje($puntajeTotal, $factor);
            
            // Obtener nivel de riesgo
            $baremos = $esFormaA ? self::BAREMOS_TOTAL_INTRALABORAL['A'] : self::BAREMOS_TOTAL_INTRALABORAL['B'];
            return $this->calcularNivelRiesgoPsicosocial($puntajeTransformado, $baremos);
            
        } catch (Exception $e) {
            Log::error("Error calculando nivel intralaboral: " . $e->getMessage());
            return 'sin_riesgo';
        }
    }

    /**
     * Calcular nivel de riesgo extralaboral basado en respuestas
     */
    private function calcularNivelRiesgoExtralaboral($respuestas): string
    {
        try {
            $respuestasArray = iterator_to_array($respuestas);
            
            if (empty($respuestasArray)) {
                return 'sin_riesgo';
            }

            // Calcular puntaje total
            $puntajeTotal = 0;
            foreach ($respuestasArray as $respuesta) {
                $valor = $respuesta['valor'] ?? 0;
                $consecutivo = $respuesta['consecutivo'] ?? 0;
                
                // Aplicar calificación ascendente o descendente según la pregunta
                if (in_array($consecutivo, self::CALIFICACION_EXTRALABORAL['ascendente'])) {
                    $puntajeTotal += $valor;
                } else {
                    // Invertir la calificación para preguntas descendentes
                    $puntajeTotal += (5 - $valor);
                }
            }
            
            // Transformar puntaje
            $puntajeTransformado = $this->calcularTransformacionPuntaje($puntajeTotal, self::FACTOR_DIMENSION_EXTRALABORAL['total']);
            
            // Obtener nivel de riesgo (usar baremos para auxiliares por defecto)
            $baremos = self::BAREMOS_EXTRALABORAL_AUXILIARES_OPERARIOS['total'];
            return $this->calcularNivelRiesgoPsicosocial($puntajeTransformado, $baremos);
            
        } catch (Exception $e) {
            Log::error("Error calculando nivel extralaboral: " . $e->getMessage());
            return 'sin_riesgo';
        }
    }

    /**
     * Calcular nivel de riesgo de estrés basado en respuestas
     */
    private function calcularNivelRiesgoEstres($respuestas): string
    {
        try {
            $respuestasArray = iterator_to_array($respuestas);
            
            if (empty($respuestasArray)) {
                return 'sin_riesgo';
            }

            // Calcular puntaje usando la metodología especial del estrés
            $puntajeTotal = 0;
            
            foreach (self::FACTOR_VALOR_ESTRES as $grupo => $config) {
                $sumaGrupo = 0;
                $cantidadGrupo = 0;
                
                foreach ($respuestasArray as $respuesta) {
                    $consecutivo = $respuesta['consecutivo'] ?? 0;
                    $valor = $respuesta['valor'] ?? 0;
                    
                    if (in_array($consecutivo, $config['consecutivos'])) {
                        $sumaGrupo += $valor;
                        $cantidadGrupo++;
                    }
                }
                
                if ($cantidadGrupo > 0) {
                    $promedioGrupo = $sumaGrupo / $cantidadGrupo;
                    $puntajeTotal += $promedioGrupo * $config['multiplicador'];
                }
            }
            
            // Transformar puntaje
            $puntajeTransformado = $this->calcularTransformacionPuntaje($puntajeTotal, self::FACTOR_TOTAL_ESTRES);
            
            // Obtener nivel de riesgo (usar baremos para auxiliares por defecto)
            $baremos = self::BAREMOS_ESTRES['auxiliares_operarios'];
            return $this->calcularNivelRiesgoPsicosocial($puntajeTransformado, $baremos);
            
        } catch (Exception $e) {
            Log::error("Error calculando nivel estrés: " . $e->getMessage());
            return 'sin_riesgo';
        }
    }

    /**
     * Calcular nivel de riesgo general combinando todos los factores
     */
    private function calcularNivelRiesgoGeneralCombinado(string $nivelIntralaboral, string $nivelExtralaboral, string $nivelEstres): string
    {
        try {
            $niveles = ['sin_riesgo', 'riesgo_bajo', 'riesgo_medio', 'riesgo_alto', 'riesgo_muy_alto'];
            
            $pesoIntralaboral = array_search($nivelIntralaboral, $niveles);
            $pesoExtralaboral = array_search($nivelExtralaboral, $niveles);
            $pesoEstres = array_search($nivelEstres, $niveles);
            
            // Calcular promedio ponderado (intralaboral tiene más peso)
            $promedioPonderado = ($pesoIntralaboral * 0.5) + ($pesoExtralaboral * 0.3) + ($pesoEstres * 0.2);
            
            $indiceNivel = round($promedioPonderado);
            
            return $niveles[$indiceNivel] ?? 'sin_riesgo';
            
        } catch (Exception $e) {
            Log::error("Error calculando nivel general: " . $e->getMessage());
            return 'sin_riesgo';
        }
    }

    /**
     * Obtener estadísticas específicas de un diagnóstico.
     * Este método debe ser implementado con la lógica completa.
     */
    public function obtenerEstadisticasDiagnostico(string $diagnostico_id): array
    {
        // Obtener todas las hojas del diagnóstico y calcular estadísticas detalladas
        $client = new \MongoDB\Client('mongodb://localhost:27017');
        $db = $client->psicosocial;
        $hojas = $db->hojas->find(['diagnostico_id' => $diagnostico_id]);
        $resultados = [];
        foreach ($hojas as $hoja) {
            $resIntra = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'intralaboral']);
            $resExtra = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'extralaboral']);
            $resEstres = $db->respuestas->find(['hoja_id' => $hoja['_id'], 'tipo' => 'estres']);
            $perfil = $hoja['perfil'] ?? 'auxiliares_operarios';
            $forma = $hoja['forma'] ?? 'A';
            $resultados[] = [
                'intralaboral' => $this->calcularDimensionesIntralaboral(iterator_to_array($resIntra), $forma),
                'dominios' => $this->calcularDominiosIntralaboral(iterator_to_array($resIntra), $forma),
                'extralaboral' => $this->calcularDimensionesExtralaboral(iterator_to_array($resExtra), $perfil),
                'total_extralaboral' => $this->calcularTotalExtralaboral(iterator_to_array($resExtra), $perfil),
                'estres' => $this->calcularEstres(iterator_to_array($resEstres), $perfil)
            ];
        }
        return $resultados;
    }

    /**
     * Exportar resultados a PDF.
     * Este método debe ser implementado con la lógica completa.
     */
    public function exportarPDF(string $empresa_id, string $diagnostico_id, array $opciones = []): \Symfony\Component\HttpFoundation\Response
    {
        // Obtener datos estadísticos y de resultados para el diagnóstico
        $estadisticas = $this->obtenerEstadisticasDiagnostico($diagnostico_id);
        // Aquí se debe integrar la lógica de generación de PDF usando los datos de $estadisticas
        // Ejemplo: $pdf = PDF::loadView('psicosocial.reporte', ['estadisticas' => $estadisticas]);
        // return $pdf->download('reporte_psicosocial.pdf');
        return response()->json(['message' => 'Exportación PDF lista para integración con librería PDF', 'estadisticas' => $estadisticas]);
    }

    /**
     * Interpretar nivel de riesgo basado en puntaje total y forma
     * @param float $puntajeTotal
     * @param string $forma 'a' o 'b'
     * @return string
     */
    public function interpretarNivelRiesgo(float $puntajeTotal, string $forma = 'a'): string
    {
        // Lógica simplificada basada en rangos estándar
        if ($puntajeTotal >= 80) {
            return 'muy_alto';
        } elseif ($puntajeTotal >= 60) {
            return 'alto';
        } elseif ($puntajeTotal >= 40) {
            return 'medio';
        } elseif ($puntajeTotal >= 20) {
            return 'bajo';
        } else {
            return 'sin_riesgo';
        }
    }

    /**
     * Exportar resultados a Excel.
     * Este método debe ser implementado con la lógica completa.
     */
    public function exportarExcel(string $empresa_id, string $diagnostico_id, array $opciones = []): \Symfony\Component\HttpFoundation\Response
    {
        // Obtener datos estadísticos y de resultados para el diagnóstico
        $estadisticas = $this->obtenerEstadisticasDiagnostico($diagnostico_id);
        // Aquí se debe integrar la lógica de generación de Excel usando los datos de $estadisticas
        // Ejemplo: return Excel::download(new PsicosocialExport($estadisticas), 'reporte_psicosocial.xlsx');
        return response()->json(['message' => 'Exportación Excel lista para integración con librería Excel', 'estadisticas' => $estadisticas]);
    }

    /**
     * Calcular puntaje completo de una hoja específica
     * Método agregado para compatibilidad con helpers existentes
     */
    public function calcularPuntajeCompleto($hoja)
    {
        try {
            if (!$hoja) {
                return [
                    'puntaje_total' => 0,
                    'nivel_riesgo' => 'sin_calcular',
                    'color' => '#6c757d',
                    'detalles' => null,
                    'tipo_forma' => 'N/A'
                ];
            }

            // Determinar el tipo de formulario (A o B)
            $tipoForma = $this->determinarTipoFormulario($hoja);
            
            // Obtener configuraciones desde los archivos config
            $configPsicosocial = config('psicosocial');
            $baremosConfig = $tipoForma === 'A' ? config('psicosocial_baremos_a') : config('psicosocial_baremos_b');
            
            if (!$configPsicosocial || !$baremosConfig) {
                return [
                    'puntaje_total' => 0,
                    'nivel_riesgo' => 'error',
                    'color' => '#dc3545',
                    'detalles' => 'Error cargando configuraciones',
                    'tipo_forma' => $tipoForma
                ];
            }

            // Obtener respuestas de la hoja
            $respuestas = $this->obtenerRespuestasHoja($hoja);
            
            if (empty($respuestas)) {
                return [
                    'puntaje_total' => 0,
                    'nivel_riesgo' => 'sin_calcular',
                    'color' => '#6c757d',
                    'detalles' => 'Sin respuestas disponibles',
                    'tipo_forma' => $tipoForma
                ];
            }

            // Calcular puntajes usando las configuraciones
            $puntajeIntralaboral = $this->calcularPuntajeIntralaboral($respuestas, $tipoForma, $configPsicosocial);
            $puntajeExtralaboral = $this->calcularPuntajeExtralaboral($respuestas, $configPsicosocial);
            $puntajeEstres = $this->calcularPuntajeEstres($respuestas, $configPsicosocial);
            
            // Calcular puntaje total combinado
            $puntajeTotal = ($puntajeIntralaboral + $puntajeExtralaboral + $puntajeEstres) / 3;
            
            // Determinar nivel de riesgo usando baremos
            $nivelRiesgo = $this->determinarNivelRiesgoGeneral($puntajeTotal, $baremosConfig);
            
            // Obtener color usando helper
            $infoRiesgo = obtenerInfoRiesgo($nivelRiesgo);
            $color = $infoRiesgo['color'];

            return [
                'puntaje_total' => round($puntajeTotal, 2),
                'nivel_riesgo' => $nivelRiesgo,
                'color' => $color,
                'detalles' => [
                    'intralaboral' => $puntajeIntralaboral,
                    'extralaboral' => $puntajeExtralaboral,
                    'estres' => $puntajeEstres
                ],
                'tipo_forma' => $tipoForma
            ];

        } catch (Exception $e) {
            Log::error("Error en calcularPuntajeCompleto: " . $e->getMessage());
            return [
                'puntaje_total' => 0,
                'nivel_riesgo' => 'error',
                'color' => '#dc3545',
                'detalles' => 'Error en cálculo: ' . $e->getMessage(),
                'tipo_forma' => 'N/A'
            ];
        }
    }

    /**
     * Determinar el tipo de formulario basado en los datos de la hoja
     */
    private function determinarTipoFormulario($hoja): string
    {
        // Lógica para determinar si es formulario A o B
        // Esto puede basarse en el perfil del empleado o configuración específica
        $datos = $hoja->datos ?? null;
        
        if ($datos && is_array($datos)) {
            // Si hay información de perfil, usar lógica específica
            $cargo = $datos['cargo'] ?? '';
            $nivelEducativo = $datos['nivel_educativo'] ?? '';
            
            // Ejemplo de lógica: Formulario A para directivos y profesionales
            if (strpos(strtolower($cargo), 'director') !== false || 
                strpos(strtolower($cargo), 'jefe') !== false ||
                strpos(strtolower($cargo), 'coordinador') !== false ||
                strpos(strtolower($nivelEducativo), 'profesional') !== false) {
                return 'A';
            }
        }
        
        return 'B'; // Por defecto formulario B
    }

    /**
     * Obtener respuestas de una hoja específica
     */
    private function obtenerRespuestasHoja($hoja): array
    {
        if (!$hoja) return [];

        $respuestas = [];
        
        // Obtener respuestas intralaborales
        if ($hoja->intralaboral) {
            $respuestas['intralaboral'] = is_array($hoja->intralaboral) ? $hoja->intralaboral : [];
        }
        
        // Obtener respuestas extralaborales
        if ($hoja->extralaboral) {
            $respuestas['extralaboral'] = is_array($hoja->extralaboral) ? $hoja->extralaboral : [];
        }
        
        // Obtener respuestas de estrés
        if ($hoja->estres) {
            $respuestas['estres'] = is_array($hoja->estres) ? $hoja->estres : [];
        }

        return $respuestas;
    }

    /**
     * Calcular puntaje intralaboral usando configuraciones
     */
    private function calcularPuntajeIntralaboral($respuestas, $tipoForma, $config): float
    {
        if (!isset($respuestas['intralaboral']) || empty($respuestas['intralaboral'])) {
            return 0.0;
        }

        $puntajeBruto = 0;
        $preguntasConfig = $config['preguntas_intralaboral'][$tipoForma] ?? [];
        $calificacionConfig = $config['calificacion'][$tipoForma] ?? [];
        
        foreach ($respuestas['intralaboral'] as $pregunta => $respuesta) {
            if (isset($calificacionConfig[$pregunta])) {
                $puntajeBruto += $calificacionConfig[$pregunta][$respuesta] ?? 0;
            }
        }

        // Transformar usando factor de transformación
        $factorTotal = $config['factores_transformacion']['intralaboral'][$tipoForma]['total'] ?? 1;
        return ($puntajeBruto / $factorTotal) * 100;
    }

    /**
     * Calcular puntaje extralaboral usando configuraciones
     */
    private function calcularPuntajeExtralaboral($respuestas, $config): float
    {
        if (!isset($respuestas['extralaboral']) || empty($respuestas['extralaboral'])) {
            return 0.0;
        }

        $puntajeBruto = 0;
        $calificacionConfig = $config['calificacion']['extralaboral'] ?? [];
        
        foreach ($respuestas['extralaboral'] as $pregunta => $respuesta) {
            if (isset($calificacionConfig[$pregunta])) {
                $puntajeBruto += $calificacionConfig[$pregunta][$respuesta] ?? 0;
            }
        }

        // Transformar usando factor de transformación
        $factorTotal = $config['factores_transformacion']['extralaboral']['GENERAL']['total'] ?? 1;
        return ($puntajeBruto / $factorTotal) * 100;
    }

    /**
     * Calcular puntaje de estrés usando configuraciones
     */
    private function calcularPuntajeEstres($respuestas, $config): float
    {
        if (!isset($respuestas['estres']) || empty($respuestas['estres'])) {
            return 0.0;
        }

        $puntajeBruto = 0;
        $calificacionConfig = $config['calificacion']['estres'] ?? [];
        
        foreach ($respuestas['estres'] as $pregunta => $respuesta) {
            if (isset($calificacionConfig[$pregunta])) {
                $puntajeBruto += $calificacionConfig[$pregunta][$respuesta] ?? 0;
            }
        }

        // Transformar usando factor de transformación
        $factorTotal = $config['factores_transformacion']['estres']['GENERAL']['total'] ?? 1;
        return ($puntajeBruto / $factorTotal) * 100;
    }

    /**
     * Determinar nivel de riesgo general usando baremos
     */
    private function determinarNivelRiesgoGeneral($puntajeTransformado, $baremosConfig): string
    {
        // Usar baremos intralaboral total como referencia general
        $baremos = $baremosConfig['intralaboral']['intralaboral_total'] ?? [];
        
        if (empty($baremos)) {
            return 'sin_calcular';
        }

        // Evaluar según rangos de baremos
        foreach ($baremos as $nivel => $rango) {
            if ($puntajeTransformado >= $rango[0] && $puntajeTransformado <= $rango[1]) {
                return $nivel;
            }
        }

        return 'sin_riesgo';
    }
}
