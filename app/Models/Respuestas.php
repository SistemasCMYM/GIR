<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasEmpresaScope;

class Respuestas extends BaseMongoModel
{
    use HasEmpresaScope;
    
    protected $connection = 'mongodb_psicosocial';
    protected $collection = 'respuestas';
    
    protected $fillable = [
        'id', 'empresa_id', 'diagnostico_id', 'hoja_id', 'empleado_id', 
        'tipo', 'respuestas', 'completado', 'fecha_inicio', 'fecha_finalizacion',
        'created_at', 'updated_at'
    ];
    
    protected $casts = [
        'completado' => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtiene la hoja asociada a estas respuestas
     */
    public function hoja()
    {
        return $this->belongsTo(Hoja::class, 'hoja_id', 'id');
    }
    
    /**
     * Obtiene el diagnóstico asociado a estas respuestas
     */
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id', 'id');
    }
    
    /**
     * Scope para filtrar por tipo de respuesta
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    /**
     * Scope para respuestas completadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('completado', true);
    }
}
