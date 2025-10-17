<?php

namespace App\Models;

use App\Models\BaseMongoModel;

class Pregunta extends BaseMongoModel
{
    protected $connection = 'mongodb_psicosocial';
    protected $table = 'preguntas';
    
    protected $fillable = [
        'id', 'tipo', 'enunciado', 'consecutivo', 'dimension', 'factor', 'dominio', 
        'baremo', 'interpretacion', 'items_evalua', 'opciones_respuesta',
        'created_at', 'updated_at'
    ];
    
    protected $casts = [
        'consecutivo' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtiene las respuestas asociadas a esta pregunta
     */
    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'pregunta_id', 'id');
    }
    
    /**
     * Obtiene el enlace de la pregunta
     */
    public function getLinkAttribute()
    {
        return "/v2.0/empresa/preguntas/{$this->id}";
    }
}
