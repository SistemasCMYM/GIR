<?php

// FUNCIONES HELPER SIMPLIFICADAS PARA DEBUG

if (!function_exists('generateBase64UrlId')) {
    /**
     * Generar ID Base64 URL-safe único según Node.js
     */
    function generateBase64UrlId($prefix = 'usr') {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
        $result = $prefix . '_';
        for ($i = 0; $i < 16; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }
}

if (!function_exists('hashPassword')) {
    /**
     * Hash de contraseña con bcrypt factor 11 (compatible con Node.js)
     */
    function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }
}

if (!function_exists('esSuperAdmin')) {
    /**
     * Verificar si una cuenta tiene rol SuperAdmin
     */
    function esSuperAdmin($cuenta) {
        if (!$cuenta || !isset($cuenta->rol)) {
            return false;
        }
        return $cuenta->rol === 'SuperAdmin';
    }
}

// Comentar temporalmente las otras funciones para debugging
/*
if (!function_exists('getRoleConfiguration')) {
    function getRoleConfiguration($rol) {
        // Configuración básica sin modelo
        $roles = [
            'SuperAdmin' => ['modulos' => ['all'], 'acciones' => ['all']],
            'administrador' => ['modulos' => ['usuarios', 'empresas'], 'acciones' => ['read', 'create', 'update', 'delete']],
        ];
        return $roles[$rol] ?? null;
    }
}
*/
