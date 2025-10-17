<?php

namespace App\Traits;

trait SafeJson
{
    /**
     * Decode a value that may be an array or a JSON string. Always returns an array or the provided default.
     *
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    protected function safeJsonDecode($value, $default = [])
    {
        if (is_array($value)) return $value;
        if (is_null($value)) return $default;
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : $default;
        }
        return $default;
    }
}
