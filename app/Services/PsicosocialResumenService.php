<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Hoja;
use App\Models\Respuesta;

/**
 * Servicio especializado para el resumen psicosocial completo
 */
class PsicosocialResumenService
{
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
     */
    public function obtenerEstadisticasResumen(string $diagnostico_id, array $filtros = []): array
    {
        $hojas = $this->obtenerHojasCompletadas($diagnostico_id);
        $hojasFiltradas = $this->aplicarFiltros($hojas, $filtros);
        
        return [
            'total_evaluaciones' => $hojasFiltradas->count(),
            'completadas' => $hojasFiltradas->count(),
            'pendientes' => 0,
            'progreso' => 100,
            'distribucion_riesgo' => $this->calcularDistribucionRiesgo($hojasFiltradas),
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

    /**
     * Obtener hojas completadas de un diagnóstico
     */
    private function obtenerHojasCompletadas(string $diagnostico_id)
    {
        return Hoja::where('diagnostico_id', $diagnostico_id)
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
     * Calcular distribución de riesgo
     */
    private function calcularDistribucionRiesgo($hojas): array
    {
        $distribucion = $this->inicializarContadores();
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
        $respuestasIntra = Respuesta::where('hoja_id', $hoja->id)
            ->where('tipo', 'intralaboral')
            ->get()
            ->toArray();
        
        $respuestasExtra = Respuesta::where('hoja_id', $hoja->id)
            ->where('tipo', 'extralaboral')
            ->get()
            ->toArray();
        
        $respuestasEstres = Respuesta::where('hoja_id', $hoja->id)
            ->where('tipo', 'estres')
            ->get()
            ->toArray();

        // Calcular puntajes simplificados
        $puntajeIntra = $this->calcularPuntajeSimplificado($respuestasIntra);
        $puntajeExtra = $this->calcularPuntajeSimplificado($respuestasExtra);
        $puntajeEstres = $this->calcularPuntajeSimplificado($respuestasEstres);

        // Determinar nivel de riesgo combinado
        $promedio = ($puntajeIntra + $puntajeExtra + $puntajeEstres) / 3;
        
        return $this->determinarNivelRiesgo($promedio);
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
            $respuestasIntra = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'intralaboral')->get()->toArray();
            $respuestasExtra = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'extralaboral')->get()->toArray();
            $respuestasEstres = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'estres')->get()->toArray();

            $nivelIntra = $this->determinarNivelRiesgo($this->calcularPuntajeSimplificado($respuestasIntra));
            $nivelExtra = $this->determinarNivelRiesgo($this->calcularPuntajeSimplificado($respuestasExtra));
            $nivelEstres = $this->determinarNivelRiesgo($this->calcularPuntajeSimplificado($respuestasEstres));

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
        $dominios = $this->calcularDominiosIntralaboral($hojas);
        $dimensiones = $this->calcularDimensionesIntralaboral($hojas);
        
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

        $dominios = $this->calcularDominiosIntralaboral($hojasA);
        $dimensiones = $this->calcularDimensionesIntralaboral($hojasA);
        
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

        $dominios = $this->calcularDominiosIntralaboral($hojasB);
        $dimensiones = $this->calcularDimensionesIntralaboral($hojasB);
        
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
        $dimensiones = $this->calcularDimensionesExtralaboral($hojas);
        
        return [
            'poblacion' => $hojas->count(),
            'total' => $this->calcularTotalExtralaboral($hojas),
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
     * Calcular dominios intralaboral
     */
    private function calcularDominiosIntralaboral($hojas): array
    {
        $dominios = [
            'demandas_trabajo' => [
                'nombre' => 'Demandas del trabajo',
                'contadores' => $this->inicializarContadores()
            ],
            'control' => [
                'nombre' => 'Control sobre el trabajo',
                'contadores' => $this->inicializarContadores()
            ],
            'liderazgo' => [
                'nombre' => 'Liderazgo y relaciones sociales en el trabajo',
                'contadores' => $this->inicializarContadores()
            ],
            'recompensas' => [
                'nombre' => 'Recompensas',
                'contadores' => $this->inicializarContadores()
            ]
        ];

        foreach ($hojas as $hoja) {
            $respuestas = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'intralaboral')->get()->toArray();
            
            $puntaje = $this->calcularPuntajeSimplificado($respuestas);
            $nivel = $this->determinarNivelRiesgo($puntaje);
            
            // Distribuir aleatoriamente entre dominios para demo
            $dominiosKeys = array_keys($dominios);
            $dominioAleatorio = $dominiosKeys[array_rand($dominiosKeys)];
            $dominios[$dominioAleatorio]['contadores'][$nivel]['cantidad']++;
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
     * Calcular dimensiones intralaboral
     */
    private function calcularDimensionesIntralaboral($hojas): array
    {
        $dimensiones = [
            'demandas_cuantitativas' => [
                'nombre' => 'Demandas cuantitativas',
                'dominio' => 'DEMANDAS DEL TRABAJO',
                'contadores' => $this->inicializarContadores()
            ],
            'demandas_carga_mental' => [
                'nombre' => 'Demandas de carga mental',
                'dominio' => 'DEMANDAS DEL TRABAJO',
                'contadores' => $this->inicializarContadores()
            ],
            'demandas_emocionales' => [
                'nombre' => 'Demandas emocionales',
                'dominio' => 'DEMANDAS DEL TRABAJO',
                'contadores' => $this->inicializarContadores()
            ],
            'control_autonomia' => [
                'nombre' => 'Control y autonomía sobre el trabajo',
                'dominio' => 'CONTROL',
                'contadores' => $this->inicializarContadores()
            ],
            'oportunidades_desarrollo' => [
                'nombre' => 'Oportunidades para el uso y desarrollo de habilidades',
                'dominio' => 'CONTROL',
                'contadores' => $this->inicializarContadores()
            ],
            'caracteristicas_liderazgo' => [
                'nombre' => 'Características del liderazgo',
                'dominio' => 'LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO',
                'contadores' => $this->inicializarContadores()
            ],
            'relaciones_sociales' => [
                'nombre' => 'Relaciones sociales en el trabajo',
                'dominio' => 'LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO',
                'contadores' => $this->inicializarContadores()
            ],
            'reconocimiento' => [
                'nombre' => 'Reconocimiento y compensación',
                'dominio' => 'RECOMPENSAS',
                'contadores' => $this->inicializarContadores()
            ]
        ];

        foreach ($hojas as $hoja) {
            $respuestas = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'intralaboral')->get()->toArray();
            
            $puntaje = $this->calcularPuntajeSimplificado($respuestas);
            $nivel = $this->determinarNivelRiesgo($puntaje);
            
            // Distribuir aleatoriamente entre dimensiones para demo
            $dimensionesKeys = array_keys($dimensiones);
            $dimensionAleatoria = $dimensionesKeys[array_rand($dimensionesKeys)];
            $dimensiones[$dimensionAleatoria]['contadores'][$nivel]['cantidad']++;
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
     * Calcular dimensiones extralaboral
     */
    private function calcularDimensionesExtralaboral($hojas): array
    {
        $dimensiones = [
            'tiempo_fuera_trabajo' => [
                'nombre' => 'Tiempo fuera del trabajo',
                'contadores' => $this->inicializarContadores()
            ],
            'relaciones_familiares' => [
                'nombre' => 'Relaciones familiares',
                'contadores' => $this->inicializarContadores()
            ],
            'situacion_economica' => [
                'nombre' => 'Situación económica del grupo familiar',
                'contadores' => $this->inicializarContadores()
            ],
            'caracteristicas_vivienda' => [
                'nombre' => 'Características de la vivienda y entorno',
                'contadores' => $this->inicializarContadores()
            ],
            'desplazamiento' => [
                'nombre' => 'Desplazamiento vivienda-trabajo-vivienda',
                'contadores' => $this->inicializarContadores()
            ]
        ];

        foreach ($hojas as $hoja) {
            $respuestas = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'extralaboral')->get()->toArray();
            
            $puntaje = $this->calcularPuntajeSimplificado($respuestas);
            $nivel = $this->determinarNivelRiesgo($puntaje);
            
            // Distribuir aleatoriamente entre dimensiones para demo
            $dimensionesKeys = array_keys($dimensiones);
            $dimensionAleatoria = $dimensionesKeys[array_rand($dimensionesKeys)];
            $dimensiones[$dimensionAleatoria]['contadores'][$nivel]['cantidad']++;
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
     * Calcular total extralaboral
     */
    private function calcularTotalExtralaboral($hojas): array
    {
        $total = $this->inicializarContadores();

        foreach ($hojas as $hoja) {
            $respuestas = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'extralaboral')->get()->toArray();
            
            $puntaje = $this->calcularPuntajeSimplificado($respuestas);
            $nivel = $this->determinarNivelRiesgo($puntaje);
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
            $respuestas = Respuesta::where('hoja_id', $hoja->id)
                ->where('tipo', 'estres')->get()->toArray();
            
            $puntaje = $this->calcularPuntajeSimplificado($respuestas);
            $nivel = $this->determinarNivelRiesgo($puntaje);
            $distribucion[$nivel]['cantidad']++;
        }

        $total = $hojas->count();
        foreach ($distribucion as $nivel => &$datos) {
            $datos['porcentaje'] = $total > 0 ? round(($datos['cantidad'] / $total) * 100, 2) : 0;
        }

        return $distribucion;
    }

    /**
     * Métodos auxiliares
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

    private function determinarNivelRiesgo(float $puntaje): string
    {
        if ($puntaje >= 80) return 'muy_alto';
        if ($puntaje >= 60) return 'alto';
        if ($puntaje >= 40) return 'medio';
        if ($puntaje >= 20) return 'bajo';
        return 'sin_riesgo';
    }

    private function calcularPuntajeSimplificado(array $respuestas): float
    {
        if (empty($respuestas)) return 0;
        
        $suma = array_sum(array_column($respuestas, 'valor'));
        return ($suma / count($respuestas)) * 20;
    }

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

    private function obtenerRangoEdad($edad): string
    {
        $edad = is_numeric($edad) ? (int)$edad : 0;
        if ($edad < 25) return '18-24';
        if ($edad < 35) return '25-34';
        if ($edad < 45) return '35-44';
        if ($edad < 55) return '45-54';
        return '55+';
    }

    private function obtenerRangoAntiguedad($antiguedad): string
    {
        $antiguedad = is_numeric($antiguedad) ? (int)$antiguedad : 0;
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

    private function agregarOpcionSiExiste(array &$array, $valor): void
    {
        if ($valor && !in_array($valor, $array)) {
            $array[] = $valor;
        }
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

            if ($mayor > 0) {
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

            if ($mayor > 0) {
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

        if ($mayor > 0) {
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
        }

        return $descripcion;
    }
}
