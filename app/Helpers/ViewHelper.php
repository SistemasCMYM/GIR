<?php

namespace App\Helpers;

class ViewHelper
{
    /**
     * Función auxiliar para manejar valores numéricos de forma segura
     */
    public static function safeNumeric($value, $default = 0)
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

    /**
     * Función auxiliar para manejar strings de forma segura
     */
    public static function safeString($value, $default = '')
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

    /**
     * Función auxiliar para manejar arrays de forma segura
     */
    public static function safeArray($value, $default = [])
    {
        if (is_array($value)) {
            return $value;
        }
        return $default;
    }

    /**
     * Función auxiliar para formatear porcentajes
     */
    public static function formatPercentage($value, $decimals = 2)
    {
        return number_format(self::safeNumeric($value), $decimals);
    }

    /**
     * Función auxiliar para obtener el valor de una dimensión de forma segura
     */
    public static function getDimensionValue($dimension, $key, $default = null)
    {
        if (!is_array($dimension) || !isset($dimension[$key])) {
            return $default;
        }
        return $dimension[$key];
    }

    /**
     * Función auxiliar para verificar si una dimensión es válida
     */
    public static function isValidDimension($dimension)
    {
        return is_array($dimension) && isset($dimension['nombre']);
    }

    /**
     * Función auxiliar para verificar si un dominio es válido
     */
    public static function isValidDominio($dominio)
    {
        return is_array($dominio) && isset($dominio['nombre']);
    }
}
