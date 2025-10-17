<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasCustomId;

class Consentimiento extends BaseMongoModel
{
    use HasCustomId;

    protected $connection = 'mongodb_empresas';
    protected $table = 'consentimientos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'contenido',
        'version',
        'tipo',
        'estado',
        'plantilla',
        'empresa_id',
        'campo_firma',
        'fecha_creacion',
        'fecha_modificacion',
        'usuario_creador',
        'usuario_modificador',
        'items_total',
        'configuracion'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
        'estado' => 'boolean',
        'plantilla' => 'boolean',
        'items_total' => 'integer',
        'configuracion' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->fecha_creacion = now();
            $model->fecha_modificacion = now();
            
            if (!$model->estado) {
                $model->estado = true;
            }
            
            if (!$model->version) {
                $model->version = '1.0';
            }
        });

        static::updating(function ($model) {
            $model->fecha_modificacion = now();
        });
    }

    /**
     * Scope para consentimientos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para plantillas
     */
    public function scopePlantillas($query)
    {
        return $query->where('plantilla', true);
    }

    /**
     * Scope por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Obtener el estado como texto
     */
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtener el tipo de consentimiento formateado
     */
    public function getTipoFormateadoAttribute()
    {
        $tipos = [
            'general' => 'General',
            'datos_personales' => 'Datos Personales',
            'evaluacion_psicosocial' => 'Evaluación Psicosocial',
            'tratamiento_datos' => 'Tratamiento de Datos',
            'investigacion' => 'Investigación',
            'personalizado' => 'Personalizado'
        ];

        return $tipos[$this->tipo] ?? ucfirst($this->tipo);
    }

    /**
     * Generar versión siguiente
     */
    public function generarSiguienteVersion()
    {
        $version_actual = $this->version;
        $partes = explode('.', $version_actual);
        $partes[1] = (int)$partes[1] + 1;
        
        return implode('.', $partes);
    }

    /**
     * Marcar como plantilla
     */
    public function marcarComoPlantilla()
    {
        $this->update(['plantilla' => true]);
        return $this;
    }

    /**
     * Clonar consentimiento
     */
    public function clonar($nuevo_titulo = null)
    {
        $nuevo_consentimiento = $this->replicate();
        $nuevo_consentimiento->titulo = $nuevo_titulo ?? $this->titulo . ' (Copia)';
        $nuevo_consentimiento->version = '1.0';
        $nuevo_consentimiento->plantilla = false;
        $nuevo_consentimiento->save();

        return $nuevo_consentimiento;
    }
}
