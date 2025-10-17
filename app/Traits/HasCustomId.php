<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasCustomId
 * 
 * Proporciona funcionalidad para generar IDs personalizados en formato base64url
 * para modelos MongoDB en el proyecto GIR-365
 */
trait HasCustomId
{
    /**
     * Prefijo para el ID del modelo (puede ser sobrescrito en cada modelo)
     * 
     * @var string|null
     */
    protected $idPrefix = null;

    /**
     * Longitud del ID personalizado (en bytes)
     * 
     * @var int
     */
    protected $idLength = 16;

    /**
     * Boot del trait - se ejecuta cuando se carga el modelo
     */
    protected static function bootHasCustomId()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = $model->generateCustomId();
            }
        });
    }

    /**
     * Genera un ID personalizado para el modelo
     * 
     * @return string
     */
    public function generateCustomId()
    {
        $prefix = $this->getIdPrefix();
        $length = $this->getIdLength();
        
        do {
            $id = generateDocumentId($prefix, $length);
            // Verificar que el ID no exista en la base de datos
        } while ($this->where($this->getKeyName(), $id)->exists());
        
        return $id;
    }

    /**
     * Obtiene el prefijo del ID para este modelo
     * 
     * @return string|null
     */
    protected function getIdPrefix()
    {
        // Si el modelo define un prefijo personalizado, usarlo
        if (property_exists($this, 'idPrefix') && $this->idPrefix !== null) {
            return $this->idPrefix;
        }

        // Si no, generar uno basado en el nombre del modelo
        $className = class_basename($this);
        return $this->generatePrefixFromClassName($className);
    }

    /**
     * Genera un prefijo basado en el nombre de la clase
     * 
     * @param string $className
     * @return string
     */
    protected function generatePrefixFromClassName($className)
    {
        // Extraer las consonantes principales para crear un prefijo corto
        $prefixes = [
            'Usuario' => 'USR',
            'User' => 'USR',
            'Empresa' => 'EMP',
            'Empleado' => 'EMPL',
            'Area' => 'AREA',
            'Centro' => 'CNTR',
            'Perfil' => 'PERF',
            'Cuenta' => 'CTA',
            'Sesion' => 'SES',
            'Permiso' => 'PERM',
            'Notificacion' => 'NOTIF',
            'Plan' => 'PLAN',
            'Hallazgo' => 'HALL',
            'Evaluacion' => 'EVAL',
            'EvaluacionPsicosocial' => 'EVAL',
            'Sector' => 'SECT',
            'Ciudad' => 'CIU',
            'Departamento' => 'DEPT',
        ];

        return $prefixes[$className] ?? strtoupper(substr($className, 0, 3));
    }

    /**
     * Obtiene la longitud del ID para este modelo
     * 
     * @return int
     */
    protected function getIdLength()
    {
        return property_exists($this, 'idLength') ? $this->idLength : 16;
    }

    /**
     * Configurar el tipo de clave primaria como string
     * 
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Deshabilitar auto-incremento para IDs personalizados
     * 
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Validar formato del ID personalizado
     * 
     * @param string $id
     * @return bool
     */
    public static function isValidCustomId($id)
    {
        return isValidBase64UrlId($id);
    }

    /**
     * Scope para buscar por ID personalizado
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $customId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCustomId($query, $customId)
    {
        return $query->where($this->getKeyName(), $customId);
    }
}
