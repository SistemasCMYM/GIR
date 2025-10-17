<?php

namespace App\Helpers;

class IdGenerator
{
    /**
     * Genera un ID único base64url-safe de la longitud especificada
     * 
     * @param int $length La longitud deseada del ID (default: 16)
     * @return string ID único base64url-safe
     */
    public static function generateBase64UrlId($length = 16)
    {
        // Generar bytes aleatorios
        $bytes = random_bytes($length);
        
        // Convertir a base64 y hacer url-safe
        $base64 = base64_encode($bytes);
        
        // Hacer URL-safe: reemplazar +/= con -_
        $urlSafe = strtr($base64, '+/', '-_');
        
        // Remover padding
        $urlSafe = rtrim($urlSafe, '=');
        
        // Truncar a la longitud deseada
        return substr($urlSafe, 0, $length);
    }

    /**
     * Genera un token de sesión único
     * 
     * @return string Token de sesión de 64 caracteres
     */
    public static function generateSessionToken()
    {
        return self::generateBase64UrlId(64);
    }

    /**
     * Genera un ID para cuentas
     * 
     * @return string ID de cuenta de 16 caracteres
     */
    public static function generateAccountId()
    {
        return self::generateBase64UrlId(16);
    }

    /**
     * Verifica si un ID tiene el formato correcto base64url
     * 
     * @param string $id El ID a verificar
     * @return bool True si es válido, false si no
     */
    public static function isValidBase64UrlId($id)
    {
        // Verificar que solo contenga caracteres válidos base64url
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $id)) {
            return false;
        }
        
        // Verificar longitud mínima
        if (strlen($id) < 8) {
            return false;
        }
        
        return true;
    }
}
