<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasEmpresaScope;

class Respuesta extends BaseMongoModel
{
    use HasEmpresaScope;

    protected $connection = 'mongodb_psicosocial';
    protected $collection = 'respuestas';

    protected $fillable = [
        'id',
        'empresa_id',
        'diagnostico_id',
        'empleado_id',
        'hoja_id',
        'pregunta_id',
        'consecutivo',
        'tipo',
        'tipo_instrumento',
        'valor',
        'opcion',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'consecutivo' => 'integer',
        'valor' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['pregunta'];

    /**
     * Obtiene la hoja asociada a esta respuesta
     */
    public function hoja()
    {
        return $this->belongsTo(Hoja::class, 'hoja_id', 'id');
    }

    /**
     * Obtiene la pregunta asociada a esta respuesta
     */
    public function preguntaRelacion()
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id', 'id');
    }

    /**
     * Obtiene el diagnóstico asociado a esta respuesta
     */
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id', 'id');
    }

    /**
     * Obtiene el enlace de la respuesta
     */
    public function getLinkAttribute()
    {
        return "/{$this->empresa_id}/respuestas/{$this->id}";
    }

    /**
     * Obtiene la pregunta asociada a esta respuesta
     */
    public function getPreguntaAttribute()
    {
        return $this->preguntaRelacion;
    }
}
