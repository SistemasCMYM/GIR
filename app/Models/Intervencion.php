<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasEmpresaScope;

class Intervencion extends BaseMongoModel
{
    use HasEmpresaScope;
    
    protected $connection = 'mongodb_hallazgos';
    protected $collection = 'intervenciones';
    
    protected $fillable = [
        'id', 'empresa_id', 'diagnostico_id', 'profesional_id', 'categoria', 'tipo', 'actividad',
        'recomendacion', 'objetivo', 'horas', 'variables', 'horas_profesional', 'requerimientos',
        'nivel_riesgo', 'dimension', 'dominio', 'fecha_inicio', 'fecha_fin', 'estado',
        'created_at', 'updated_at'
    ];
    
    protected $casts = [
        'requerimientos' => 'array',
        'horas_profesional' => 'integer',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtiene el diagnóstico asociado a esta intervención
     */
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id', 'id');
    }
    
    /**
     * Obtiene las actividades asociadas a esta intervención
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'intervencion_id', 'id');
    }
}
