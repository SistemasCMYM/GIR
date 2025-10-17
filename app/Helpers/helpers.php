<?php

if (!function_exists('generateBase64UrlId')) {
    /**
     * Genera un ID único usando base64url (compatible con URLs)
     * Compatible con el esquema de Node.js/MongoDB especificado
     */
    function generateBase64UrlId($length = 16) {
        $bytes = random_bytes($length); // 16 bytes = 22 caracteres base64 sin padding
        $base64 = base64_encode($bytes);
        $safe = strtr($base64, '+/', '-_');
        return rtrim($safe, '='); // Elimina el "=" al final, si lo hubiera
    }
}

if (!function_exists('generateDocumentId')) {
    /**
     * Genera un ID único para documentos MongoDB
     * Compatible con el sistema de IDs del proyecto
     */
    function generateDocumentId($prefix = '', $length = 16) {
        $id = generateBase64UrlId($length);
        return $prefix ? $prefix . $id : $id;
    }
}

if (!function_exists('hashPassword')) {
    /**
     * Hashea una contraseña usando bcrypt con factor de costo 11
     * Compatible con el esquema de Node.js especificado
     */
    function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }
}

if (!function_exists('verifyPassword')) {
    /**
     * Verifica una contraseña contra su hash bcrypt
     */
    function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

if (!function_exists('obtenerEstadoOficial')) {
    /**
     * Determina el estado oficial de unos datos específicos
     * Optimizado para datos de MongoDB bien formateados
     */
    function obtenerEstadoOficial($datosRaw) {
        // Si es null, vacío, o string "NULL", retorna pendiente
        if (is_null($datosRaw) || $datosRaw === '' || $datosRaw === 'NULL') {
            return 'pendiente';
        }
        
        // Si es string, validar directamente (caso más común en MongoDB)
        if (is_string($datosRaw)) {
            $estado = trim($datosRaw);
            if (in_array($estado, ['completado', 'en_progreso', 'pendiente'])) {
                return $estado;
            }
            // Si es un string pero contiene datos (no es un estado), asumimos completado
            if (strlen($estado) > 10) { // String con contenido real
                return 'completado';
            }
        }
        
        // Si es objeto/array con datos, consideramos completado
        if (is_object($datosRaw) || is_array($datosRaw)) {
            // CASO 1: Buscar primero propiedades de estado explícitas
            if (is_object($datosRaw)) {
                // Para modelos Eloquent, verificar atributos directamente
                if (method_exists($datosRaw, 'getAttribute')) {
                    $estado = $datosRaw->getAttribute('estado');
                    if (is_string($estado) && in_array($estado, ['completado', 'en_progreso', 'pendiente'])) {
                        return $estado;
                    }
                    // También verificar el campo 'completado' común en modelos Datos
                    $completado = $datosRaw->getAttribute('completado');
                    if ($completado === 'en_progreso') {
                        return 'en_progreso';
                    } elseif ($completado === true || $completado === 'true' || $completado === 1) {
                        return 'completado';
                    } elseif ($completado === false || $completado === 'false' || $completado === 0) {
                        return 'pendiente';
                    }
                }
                // Para objetos normales
                if (isset($datosRaw->estado) && is_string($datosRaw->estado)) {
                    $estado = trim($datosRaw->estado);
                    if (in_array($estado, ['completado', 'en_progreso', 'pendiente'])) {
                        return $estado;
                    }
                }
                // Verificar campo completado en objetos normales
                if (isset($datosRaw->completado)) {
                    if ($datosRaw->completado === 'en_progreso') {
                        return 'en_progreso';
                    } elseif ($datosRaw->completado === true || $datosRaw->completado === 'true' || $datosRaw->completado === 1) {
                        return 'completado';
                    } elseif ($datosRaw->completado === false || $datosRaw->completado === 'false' || $datosRaw->completado === 0) {
                        return 'pendiente';
                    }
                }
            }
            
            // CASO 2: Para arrays
            if (is_array($datosRaw)) {
                if (isset($datosRaw['estado']) && in_array($datosRaw['estado'], ['completado', 'en_progreso', 'pendiente'])) {
                    return $datosRaw['estado'];
                }
                if (isset($datosRaw['completado'])) {
                    if ($datosRaw['completado'] === 'en_progreso') {
                        return 'en_progreso';
                    } elseif ($datosRaw['completado'] === true || $datosRaw['completado'] === 'true' || $datosRaw['completado'] === 1) {
                        return 'completado';
                    } elseif ($datosRaw['completado'] === false || $datosRaw['completado'] === 'false' || $datosRaw['completado'] === 0) {
                        return 'pendiente';
                    }
                }
            }
            
            // CASO 3: Si hay contenido pero no estado explícito, verificar si realmente tiene datos
            $hasContent = false;
            if (is_object($datosRaw)) {
                if (method_exists($datosRaw, 'toArray')) {
                    // Es un modelo Eloquent, convertir a array para verificar contenido
                    $arrayData = $datosRaw->toArray();
                    $hasContent = !empty($arrayData);
                } else {
                    $hasContent = count(get_object_vars($datosRaw)) > 0;
                }
            } elseif (is_array($datosRaw)) {
                $hasContent = !empty($datosRaw);
            }
            
            return $hasContent ? 'completado' : 'pendiente';
        }
        
        // Si es boolean
        if (is_bool($datosRaw)) {
            return $datosRaw ? 'completado' : 'pendiente';
        }
        
        return 'pendiente'; // fallback seguro
    }
}

if (!function_exists('obtenerEstadoGeneralPsicosocial')) {
    /**
     * Obtener el estado general de la evaluación psicosocial considerando todas las secciones
     * Esta función analiza el campo datos y también las secciones individuales (intralaboral, extralaboral, estres)
     */
    function obtenerEstadoGeneralPsicosocial($hoja) {
        if (!$hoja || !$hoja->datos) {
            return 'pendiente';
        }
        
        $datos = $hoja->datos;
        
        // Primero verificar si hay un estado general definido
        $estadoGeneral = obtenerEstadoOficial($datos);
        if ($estadoGeneral !== 'pendiente') {
            return $estadoGeneral;
        }
        
        // Si el estado general es pendiente, analizar las secciones individuales
        $secciones = ['intralaboral', 'extralaboral', 'estres'];
        $estadosSecciones = [];
        
        foreach ($secciones as $seccion) {
            if (isset($datos->$seccion)) {
                $estadoSeccion = obtenerEstadoOficial($datos->$seccion);
                $estadosSecciones[] = $estadoSeccion;
            } else {
                $estadosSecciones[] = 'pendiente';
            }
        }
        
        // Determinar estado general basado en las secciones
        $completadas = array_count_values($estadosSecciones);
        
        // Si todas están completadas
        if (($completadas['completado'] ?? 0) === 3) {
            return 'completado';
        }
        
        // Si alguna está en progreso
        if (($completadas['en_progreso'] ?? 0) > 0) {
            return 'en_progreso';
        }
        
        // Si algunas están completadas pero no todas
        if (($completadas['completado'] ?? 0) > 0) {
            return 'en_progreso';
        }
        
        // Si todas están pendientes
        return 'pendiente';
    }
}

if (!function_exists('getRoleConfiguration')) {
    /**
     * Obtener configuración de rol desde los roles del sistema
     * 
     * @param string $roleName Nombre del rol
     * @return array|null Configuración del rol
     */
    function getRoleConfiguration($roleName) {
        // Configuración estática para evitar problemas con modelo
        $rolesConfig = [
            'SuperAdmin' => [
                'nombre' => 'Super Administrador',
                'nivel' => 0,
                'modulos' => ['all'],
                'acciones' => ['all'],
                'permisos' => ['all']
            ],
            'administrador' => [
                'nombre' => 'Administrador',
                'nivel' => 1,
                'modulos' => ['usuarios', 'empresas', 'informes', 'configuracion'],
                'acciones' => ['read', 'create', 'update', 'delete', 'admin'],
                'permisos' => ['administrar_usuarios', 'configurar_sistema']
            ],
            'profesional' => [
                'nombre' => 'Profesional',
                'nivel' => 2,
                'modulos' => ['psicosocial', 'informes', 'empleados'],
                'acciones' => ['read', 'create', 'update', 'export'],
                'permisos' => ['evaluar_psicosocial', 'generar_informes']
            ],
            'tecnico' => [
                'nombre' => 'Técnico',
                'nivel' => 3,
                'modulos' => ['psicosocial', 'empleados'],
                'acciones' => ['read', 'create', 'update'],
                'permisos' => ['aplicar_instrumentos']
            ],
            'supervisor' => [
                'nombre' => 'Supervisor',
                'nivel' => 4,
                'modulos' => ['psicosocial', 'empleados', 'informes'],
                'acciones' => ['read', 'update', 'export'],
                'permisos' => ['supervisar_evaluaciones']
            ],
            'usuario' => [
                'nombre' => 'Usuario',
                'nivel' => 5,
                'modulos' => ['psicosocial'],
                'acciones' => ['read'],
                'permisos' => ['ver_propios_resultados']
            ]
        ];
        
        return $rolesConfig[$roleName] ?? null;
    }
}

if (!function_exists('tieneAccesoEmpresa')) {
    /**
     * Verificar si una cuenta tiene acceso a una empresa específica
     */
    function tieneAccesoEmpresa($cuenta, $empresaId) {
        if (!$cuenta || !$empresaId) {
            return false;
        }
        
        // SuperAdmin tiene acceso a todas las empresas
        if (esSuperAdmin($cuenta)) {
            return true;
        }
        
        // Verificar en los permisos de la cuenta
        $perfil = $cuenta->perfil;
        if (!$perfil || !$perfil->empresas_ids) {
            return false;
        }
        
    // empresas_ids puede venir como string JSON o como array; usar safe_json_decode
    $empresasIds = is_string($perfil->empresas_ids) || $perfil->empresas_ids instanceof \Traversable ?
              safe_json_decode($perfil->empresas_ids, true) :
              ($perfil->empresas_ids ?? []);
        
        return in_array($empresaId, $empresasIds ?? []);
    }
}

if (!function_exists('validarPermisosModulo')) {
    /**
     * Validar permisos de un módulo para una cuenta
     */
    function validarPermisosModulo($cuenta, $modulo, $accion = 'read') {
        if (!$cuenta) {
            return false;
        }
        
        // SuperAdmin tiene acceso completo
        if (esSuperAdmin($cuenta)) {
            return true;
        }
        
        $configuracionRol = getRoleConfiguration($cuenta->rol);
        if (!$configuracionRol) {
            return false;
        }
        
        // Verificar módulo
        if (!in_array($modulo, $configuracionRol['modulos'] ?? [])) {
            return false;
        }
        
        // Verificar acción
        $accionesPermitidas = $configuracionRol['acciones'] ?? [];
        return in_array($accion, $accionesPermitidas) || in_array('all', $accionesPermitidas);
    }
}

if (!function_exists('obtenerBadgeEstado')) {
    function obtenerBadgeEstado($datosRaw) {
        $estado = obtenerEstadoOficial($datosRaw);
        switch ($estado) {
            case 'completado':
                return '<span class="badge bg-success text-white">Completado</span>';
            case 'en_progreso':
                return '<span class="badge bg-warning text-dark">En Progreso</span>';
            case 'pendiente':
            default:
                return '<span class="badge bg-secondary text-white">Pendiente</span>';
        }
    }
}

if (!function_exists('obtenerBadgeEstadoGeneral')) {
    /**
     * Obtener badge para el estado general de la evaluación psicosocial
     */
    function obtenerBadgeEstadoGeneral($hoja) {
        $estado = obtenerEstadoGeneralPsicosocial($hoja);
        switch ($estado) {
            case 'completado':
                return '<span class="badge bg-success text-white">Completado</span>';
            case 'en_progreso':
                return '<span class="badge bg-warning text-dark">En Progreso</span>';
            case 'pendiente':
            default:
                return '<span class="badge bg-secondary text-white">Pendiente</span>';
        }
    }
}

if (!function_exists('calcularProgresoEvaluacion')) {
    /**
     * Calcula el progreso de una evaluación psicosocial basado en sus componentes
     * CORREGIDA: Basada en estados reales de BD psicosocial
     * 
     * Estados reales: "completado", "en_progreso", "pendiente"
     */
    function calcularProgresoEvaluacion($datos) {
        if (!$datos) {
            return ['porcentaje' => 0, 'completadas' => 0, 'total' => 3];
        }
        
        $secciones = ['intralaboral', 'extralaboral', 'estres'];
        $completadas = 0;
        
        foreach ($secciones as $seccion) {
            if (isset($datos->$seccion)) {
                $estado = obtenerEstadoOficial($datos->$seccion);
                if ($estado === 'completado') {
                    $completadas++;
                }
            }
        }
        
        $porcentaje = ($completadas / 3) * 100;
        
        return [
            'porcentaje' => round($porcentaje, 1),
            'completadas' => $completadas,
            'total' => 3
        ];
    }
}

if (!function_exists('obtenerColorNivelRiesgo')) {
    /**
     * Obtiene el color CSS para un nivel de riesgo
     */
    function obtenerColorNivelRiesgo($nivel) {
        $colores = [
            'Sin riesgo' => '#28a745',    // Verde
            'Riesgo bajo' => '#20c997',   // Verde azulado
            'Riesgo medio' => '#ffc107',  // Amarillo
            'Riesgo alto' => '#fd7e14',   // Naranja
            'Riesgo muy alto' => '#dc3545' // Rojo
        ];
        
        return $colores[$nivel] ?? '#6c757d'; // Gris por defecto
    }
}

if (!function_exists('formatearFecha')) {
    /**
     * Formatea una fecha en español
     */
    function formatearFecha($fecha, $formato = 'd/m/Y') {
        if (!$fecha) return 'N/A';
        
        try {
            if (is_string($fecha)) {
                $fecha = new DateTime($fecha);
            }
            return $fecha->format($formato);
        } catch (Exception $e) {
            return 'N/A';
        }
    }
}

if (!function_exists('safe_json_decode')) {
    /**
     * Decodifica JSON de forma segura: sólo intenta json_decode si la entrada es string.
     * Previene TypeError en PHP 8+ cuando se pasa un array/objeto a json_decode.
     *
     * @param mixed $json
     * @param bool $assoc
     * @return mixed
     */
    function safe_json_decode($json, $assoc = true)
    {
        try {
            if (is_string($json)) {
                $decoded = json_decode($json, $assoc);
                // Si json_decode falla devuelve null; convertir a array vacía cuando $assoc=true
                return $decoded === null ? ($assoc ? [] : null) : $decoded;
            }
            // Convertir BSONArray/Traversable a array si es posible
            if ($json instanceof \Traversable) {
                return iterator_to_array($json);
            }
            // Si ya es array o nulo, devolver tal cual
            if (is_array($json) || is_null($json)) {
                return $json;
            }
            // Para otros tipos (int, object), devolver valor por defecto
            return $assoc ? [] : null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('safe_json_decode: error decodificando JSON: ' . $e->getMessage());
            return $assoc ? [] : null;
        }
    }
}

if (!function_exists('obtenerNombreCompleto')) {
    /**
     * Obtiene el nombre completo de un empleado
     */
    function obtenerNombreCompleto($empleado) {
        if (!$empleado) return 'N/A';
        
        $nombre = trim(($empleado->primer_nombre ?? '') . ' ' . ($empleado->segundo_nombre ?? ''));
        $apellido = trim(($empleado->primer_apellido ?? '') . ' ' . ($empleado->segundo_apellido ?? ''));
        
        return trim($nombre . ' ' . $apellido) ?: 'N/A';
    }
}

if (!function_exists('getModulosPorRol')) {
    /**
     * Obtener módulos disponibles según rol
     * 
     * @param string $rol Nombre del rol
     * @return array Módulos disponibles
     */
    function getModulosPorRol($rol) {
        $config = getRoleConfiguration($rol);
        return $config['modulos'] ?? ['dashboard'];
    }
}

if (!function_exists('getPermisosPorRol')) {
    /**
     * Obtener permisos disponibles según rol
     * 
     * @param string $rol Nombre del rol
     * @return array Permisos disponibles
     */
    function getPermisosPorRol($rol) {
        $config = getRoleConfiguration($rol);
        return $config['permisos'] ?? ['read'];
    }
}

if (!function_exists('getAccionesPorRol')) {
    /**
     * Obtener acciones disponibles según rol
     * 
     * @param string $rol Nombre del rol
     * @return array Acciones disponibles
     */
    function getAccionesPorRol($rol) {
        $config = getRoleConfiguration($rol);
        return $config['acciones'] ?? ['leer'];
    }
}

if (!function_exists('validarEstructuraUsuario')) {
    /**
     * Validar estructura de datos de usuario según schema de Node.js
     * 
     * @param array $datos Datos del usuario
     * @return array Errores de validación
     */
    function validarEstructuraUsuario($datos) {
        $errores = [];
        
        // Validar rol
        if (!in_array($datos['rol'] ?? '', \App\Models\Cuenta::$roles)) {
            $errores[] = 'Rol inválido';
        }
        
        // Validar tipo
        if (!in_array($datos['tipo'] ?? '', \App\Models\Cuenta::$tipos)) {
            $errores[] = 'Tipo inválido';
        }
        
        // Validar estado
        if (!in_array($datos['estado'] ?? '', \App\Models\Cuenta::$estados)) {
            $errores[] = 'Estado inválido';
        }
        
        // Validar email
        if (!filter_var($datos['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email inválido';
        }
        
        return $errores;
    }
}

if (!function_exists('esSuperAdmin')) {
    /**
     * Verificar si una cuenta es SuperAdmin
     * 
     * @param \App\Models\Cuenta|array $cuenta Cuenta o datos de cuenta
     * @return bool True si es SuperAdmin
     */
    function esSuperAdmin($cuenta) {
        if (is_array($cuenta)) {
            return ($cuenta['rol'] ?? '') === 'SuperAdmin' && ($cuenta['tipo'] ?? '') === 'interna';
        }
        
        return $cuenta->rol === 'SuperAdmin' && $cuenta->tipo === 'interna';
    }
}

if (!function_exists('tieneAccesoEmpresa')) {
    /**
     * Verificar si una cuenta tiene acceso a una empresa específica
     * 
     * @param \App\Models\Cuenta|array $cuenta Cuenta o datos de cuenta
     * @param string $empresaId ID de la empresa
     * @return bool True si tiene acceso
     */
    function tieneAccesoEmpresa($cuenta, $empresaId) {
        // SuperAdmin tiene acceso a todo
        if (esSuperAdmin($cuenta)) {
            return true;
        }
        
        $empresas = is_array($cuenta) ? ($cuenta['empresas'] ?? []) : ($cuenta->empresas ?? []);
        return in_array($empresaId, $empresas);
    }
}

if (!function_exists('limpiarDatosUsuario')) {
    /**
     * Limpiar y preparar datos de usuario según schema de Node.js
     * 
     * @param array $datos Datos crudos del usuario
     * @return array Datos limpiados
     */
    function limpiarDatosUsuario($datos) {
        return [
            'id' => $datos['id'] ?? generateBase64UrlId(),
            'empleado_id' => $datos['empleado_id'] ?? null,
            'nick' => $datos['nick'] ?? null,
            'email' => $datos['email'] ?? null,
            'contrasena' => isset($datos['contrasena']) ? hashPassword($datos['contrasena']) : '',
            'dni' => $datos['dni'] ?? null,
            'rol' => in_array($datos['rol'] ?? '', \App\Models\Cuenta::$roles) ? $datos['rol'] : 'usuario',
            'estado' => in_array($datos['estado'] ?? '', \App\Models\Cuenta::$estados) ? $datos['estado'] : 'inactiva',
            'tipo' => in_array($datos['tipo'] ?? '', \App\Models\Cuenta::$tipos) ? $datos['tipo'] : 'cliente',
            'empresas' => is_array($datos['empresas'] ?? null) ? $datos['empresas'] : [],
            'canales' => is_array($datos['canales'] ?? null) ? $datos['canales'] : [],
            'centro_key' => $datos['centro_key'] ?? null
        ];
    }
}

if (!function_exists('crearVirtualLink')) {
    /**
     * Crear link virtual según el schema de Node.js
     * 
     * @param string $recurso Nombre del recurso
     * @param string $id ID del documento
     * @return string Link virtual
     */
    function crearVirtualLink($recurso, $id) {
        return "/v2.0/{$recurso}/{$id}";
    }
}

if (!function_exists('obtenerEstadisticasUsuarios')) {
    /**
     * Obtener estadísticas de usuarios del sistema
     * 
     * @param string|null $empresaId ID de empresa (opcional)
     * @return array Estadísticas
     */
    function obtenerEstadisticasUsuarios($empresaId = null) {
        $estadisticas = [
            'total_usuarios' => 0,
            'usuarios_activos' => 0,
            'usuarios_suspendidos' => 0,
            'usuarios_inactivos' => 0,
            'por_rol' => [],
            'por_tipo' => []
        ];
        
        try {
            $query = \App\Models\Cuenta::query();
            
            if ($empresaId && !esSuperAdmin(session('user_data', []))) {
                $query->where('empresas', $empresaId);
            }
            
            $cuentas = $query->get();
            
            $estadisticas['total_usuarios'] = $cuentas->count();
            $estadisticas['usuarios_activos'] = $cuentas->where('estado', 'activa')->count();
            $estadisticas['usuarios_suspendidos'] = $cuentas->where('estado', 'suspendida')->count();
            $estadisticas['usuarios_inactivos'] = $cuentas->where('estado', 'inactiva')->count();
            
            // Por rol
            foreach (\App\Models\Cuenta::$roles as $rol) {
                $estadisticas['por_rol'][$rol] = $cuentas->where('rol', $rol)->count();
            }
            
            // Por tipo
            foreach (\App\Models\Cuenta::$tipos as $tipo) {
                $estadisticas['por_tipo'][$tipo] = $cuentas->where('tipo', $tipo)->count();
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error obteniendo estadísticas de usuarios: ' . $e->getMessage());
        }
        
        return $estadisticas;
    }
}

if (!function_exists('obtenerEdad')) {
    /**
     * Calcula la edad basada en la fecha de nacimiento
     */
    function obtenerEdad($fechaNacimiento) {
        if (!$fechaNacimiento) return null;
        
        try {
            if (is_string($fechaNacimiento)) {
                $fechaNacimiento = new DateTime($fechaNacimiento);
            }
            $hoy = new DateTime();
            return $hoy->diff($fechaNacimiento)->y;
        } catch (Exception $e) {
            return null;
        }
    }
}

// ======================== FUNCIONES DE VISTA ========================

if (!function_exists('safeNumeric')) {
    /**
     * Función auxiliar para manejar valores numéricos de forma segura
     */
    function safeNumeric($value, $default = 0)
    {
        if (is_numeric($value)) {
            return $value;
        }
        if (is_array($value) && !empty($value)) {
            $firstValue = reset($value);
            return is_numeric($firstValue) ? $firstValue : $default;
        }
        return $default;
    }
}

if (!function_exists('safeString')) {
    /**
     * Función auxiliar para manejar strings de forma segura
     */
    function safeString($value, $default = '')
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value) && !empty($value)) {
            $firstValue = reset($value);
            return is_string($firstValue) ? $firstValue : $default;
        }
        return $default;
    }
}

if (!function_exists('safeArray')) {
    /**
     * Función auxiliar para manejar arrays de forma segura
     */
    function safeArray($value, $default = [])
    {
        if (is_array($value)) {
            return $value;
        }
        return $default;
    }
}

if (!function_exists('formatPercentage')) {
    /**
     * Función auxiliar para formatear porcentajes
     */
    function formatPercentage($value, $decimals = 2)
    {
        return number_format(safeNumeric($value), $decimals);
    }
}

if (!function_exists('getDimensionValue')) {
    /**
     * Función auxiliar para obtener el valor de una dimensión de forma segura
     */
    function getDimensionValue($dimension, $key, $default = null)
    {
        if (!is_array($dimension) || !isset($dimension[$key])) {
            return $default;
        }
        return $dimension[$key];
    }
}

if (!function_exists('isValidDimension')) {
    /**
     * Función auxiliar para verificar si una dimensión es válida
     */
    function isValidDimension($dimension)
    {
        return is_array($dimension) && isset($dimension['nombre']);
    }
}

if (!function_exists('isValidDominio')) {
    /**
     * Función auxiliar para verificar si un dominio es válido
     */
    function isValidDominio($dominio)
    {
        return is_array($dominio) && isset($dominio['nombre']);
    }
}
