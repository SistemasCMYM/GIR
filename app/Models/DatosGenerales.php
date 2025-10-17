<?php

namespace App\Models;

/**
 * Modelo para la Ficha de Datos Generales
 * Base de datos: psicosocial
 * Colección: datos
 */
class DatosGenerales extends BaseMongoModel
{
    /**
     * Conexión de base de datos MongoDB
     */
    protected $connection = 'mongodb_psicosocial';

    /**
     * Nombre de la colección
     */
    protected $table = 'datos';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'employee_id',
        'genero',
        'ano_nacimiento',
        'edad',
        'estado_civil',
        'nivel_estudios',
        'profesion',
        'lugar_residencia',
        'estrato_social',
        'tipo_vivienda',
        'dependientes_economicos',
        'lugar_trabajo',
        'tiempo_laborado',
        'nombre_cargo',
        'tipo_cargo',
        'tiempo_en_cargo',
        'departamento_cargo',
        'tipo_contrato',
        'horas_laboradas_dia',
        'tipo_salario',
        'completado',
        'fecha_completado'
    ];

    /**
     * Campos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'employee_id' => 'string',
        'genero' => 'integer',
        'ano_nacimiento' => 'integer',
        'edad' => 'integer',
        'estado_civil' => 'integer',
        'nivel_estudios' => 'integer',
        'profesion' => 'string',
        'lugar_residencia' => 'array',
        'estrato_social' => 'integer',
        'tipo_vivienda' => 'integer',
        'dependientes_economicos' => 'integer',
        'lugar_trabajo' => 'array',
        'tiempo_laborado' => 'array',
        'nombre_cargo' => 'string',
        'tipo_cargo' => 'integer',
        'tiempo_en_cargo' => 'array',
        'departamento_cargo' => 'string',
        'tipo_contrato' => 'integer',
        'horas_laboradas_dia' => 'integer',
        'tipo_salario' => 'integer',
        'completado' => 'boolean',
        'fecha_completado' => 'datetime'
    ];

    /**
     * Buscar por employee_id
     */
    public static function findByEmployeeId($employeeId)
    {
        return static::where('employee_id', $employeeId)->first();
    }

    /**
     * Verificar si está completado
     */
    public function isCompleted()
    {
        return $this->completado === true;
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted()
    {
        $this->completado = true;
        $this->fecha_completado = now();
        return $this->save();
    }

    /**
     * Obtener datos de progreso
     */
    public function getProgressData()
    {
        $total_campos = 19; // 19 preguntas del cuestionario
        $campos_completados = 0;
        
        $campos_requeridos = [
            'genero', 'ano_nacimiento', 'edad', 'estado_civil', 'nivel_estudios',
            'profesion', 'lugar_residencia', 'estrato_social', 'tipo_vivienda',
            'dependientes_economicos', 'lugar_trabajo', 'tiempo_laborado',
            'nombre_cargo', 'tipo_cargo', 'tiempo_en_cargo', 'departamento_cargo',
            'tipo_contrato', 'horas_laboradas_dia', 'tipo_salario'
        ];

        foreach ($campos_requeridos as $campo) {
            if (!empty($this->$campo)) {
                $campos_completados++;
            }
        }

        return [
            'total' => $total_campos,
            'completados' => $campos_completados,
            'porcentaje' => round(($campos_completados / $total_campos) * 100, 2),
            'faltantes' => $total_campos - $campos_completados
        ];
    }

    /**
     * Scope para obtener solo datos completados
     */
    public function scopeCompletados($query)
    {
        return $query->where('completado', true);
    }

    /**
     * Scope para obtener solo borradores
     */
    public function scopeBorradores($query)
    {
        return $query->where('completado', '!=', true);
    }
}
