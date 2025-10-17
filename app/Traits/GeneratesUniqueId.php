<?php

namespace App\Traits;

trait GeneratesUniqueId
{    /**
     * Genera un ID único base64url-safe
     * 
     * @param int $length Longitud en bytes (16 bytes = ~22 caracteres)
     * @return string ID único base64url-safe
     */
    public static function generateBase64UrlId($length = 16): string
    {
        $bytes = random_bytes($length); // 16 bytes = 22 caracteres base64 sin padding
        $base64 = base64_encode($bytes);
        $safe = strtr($base64, '+/', '-_');
        return rtrim($safe, '='); // Elimina el "=" al final, si lo hubiera
    }

    /**
     * Genera un ID único con prefijo personalizado
     * 
     * @param string $prefix Prefijo para el ID
     * @param int $length Longitud en bytes (16 bytes = ~22 caracteres)
     * @return string ID único con prefijo
     */
    public function generateIdWithPrefix($prefix = '', $length = 16): string
    {
        $uniqueId = static::generateBase64UrlId($length);
        return $prefix ? $prefix . $uniqueId : $uniqueId;
    }    /**
     * Hook para generar ID automáticamente al crear el modelo
     */
    protected static function bootGeneratesUniqueId()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                // Obtener prefijo del modelo si existe
                $prefix = $model->idPrefix ?? '';
                if ($prefix) {
                    $model->id = $model->generateIdWithPrefix($prefix);
                } else {
                    $model->id = static::generateBase64UrlId();
                }
            }
        });
    }

    /**
     * Indica que el modelo no usa incremento automático
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Tipo de clave primaria
     */
    public function getKeyType()
    {
        return 'string';
    }
}
