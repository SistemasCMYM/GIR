<?php

if (!function_exists('generateBase64UrlId')) {
    /**
     * Generar ID único usando Base64 URL seguro (igual que Node.js)
     * 
     * @param int $length Longitud en bytes (16 bytes = 22 caracteres)
     * @return string ID único seguro para URL
     */
    function generateBase64UrlId($length = 16) {
        $bytes = random_bytes($length); // 16 bytes = 22 caracteres base64 sin padding
        $base64 = base64_encode($bytes);
        $safe = strtr($base64, '+/', '-_');
        return rtrim($safe, '='); // Elimina el "=" al final, si lo hubiera
    }
}

if (!function_exists('hashPassword')) {
    /**
     * Generar hash para contraseña usando bcrypt con factor 11 (igual que Node.js)
     * 
     * @param string $password Contraseña a encriptar
     * @return string Hash de la contraseña
     */
    function hashPassword($password) {
        return bcrypt($password, ['rounds' => 11]);
    }
}

if (!function_exists('verifyPassword')) {
    /**
     * Verificar contraseña contra hash - Compatible con legacy y Bcrypt
     * 
     * @param string $password Contraseña a verificar
     * @param string $hash Hash almacenado
     * @return bool True si coincide
     */
    function verifyPassword($password, $hash) {
        // Si el hash es vacío, retornar false
        if (empty($hash)) {
            return false;
        }
        
        // Detectar si es formato Bcrypt ($2a$, $2y$, $2x$)
        if (substr($hash, 0, 3) === '$2a' || substr($hash, 0, 3) === '$2y' || substr($hash, 0, 3) === '$2x') {
            return \Illuminate\Support\Facades\Hash::check($password, $hash);
        }
        
        // Compatibilidad con algoritmos legacy
        // Verificar MD5 (32 caracteres hexadecimales)
        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            return md5($password) === $hash;
        }
        
        // Verificar SHA1 (40 caracteres hexadecimales)
        if (strlen($hash) === 40 && ctype_xdigit($hash)) {
            return sha1($password) === $hash;
        }
        
        // Verificar SHA256 (64 caracteres hexadecimales)
        if (strlen($hash) === 64 && ctype_xdigit($hash)) {
            return hash('sha256', $password) === $hash;
        }
        
        // Intentar verificar como bcrypt por si tiene formato diferente
        try {
            return \Illuminate\Support\Facades\Hash::check($password, $hash);
        } catch (\Exception $e) {
            // Si todo falla, comparar texto plano (solo para desarrollo - INSEGURO)
            return $password === $hash;
        }
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
        return \App\Models\Rol::ROLES_SISTEMA[$roleName] ?? null;
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
