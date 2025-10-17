<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\SafeJson;
use App\Traits\HasEmpresaScope;

class Actividad extends BaseMongoModel
{
    use HasEmpresaScope;
    use SafeJson;
    
    protected $connection = 'mongodb_psicosocial';
    protected $collection = 'actividades';
    
    protected $fillable = [
        'id', 'empresa_id', 'profesional_id', 'diagnostico_id', 'categoria', 'tipo', 'actividad',
        'recomendacion', 'objetivo', 'horas', 'variables', 'horas_profesional', 'requerimientos',
        'created_at', 'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Normalizar requerimientos: tolerate arrays or JSON strings.
     */
    public function getRequerimientosAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }
    
    /**
     * Obtiene el diagnóstico asociado a esta actividad
     */
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id', 'id');
    }
    
    /**
     * Obtiene el enlace de la actividad
     */
    public function getLinkAttribute()
    {
        return "/m/psicosocial/intervenciones/actividades/{$this->id}";
    }
}
