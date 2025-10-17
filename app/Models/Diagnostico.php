<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasEmpresaScope;

class Diagnostico extends BaseMongoModel
{
    use HasEmpresaScope;
    
    protected $connection = 'mongodb_psicosocial';
    protected $table = 'diagnosticos';
    
    protected $fillable = [
        'id', 'empresa_id', 'profesional_id', 'area_key', 'area_label', 'contrato_key', 'contrato_label',
        'centro_key', 'centro_label', 'ciudad_key', 'ciudad_label', 'proceso_key', 'proceso_label',
        'filtro', 'filtro_key', 'clave', 'descripcion', 'grupo', 'cierre', 'objetivo',
        'objetivos_especificos', 'metodologia', 'observaciones', 'recomendaciones', 'informe', 'created_at', 'updated_at'
    ];
    
    protected $casts = [
        'filtro' => 'boolean',
        'cierre' => 'boolean',
        // MongoDB handles arrays natively, no need to cast as array/JSON
        // 'objetivos_especificos' => 'array',
        // 'informe' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtiene las hojas asociadas a este diagnóstico
     */
    public function hojas()
    {
        return $this->hasMany(Hoja::class, 'diagnostico_id', 'id');
    }
    
    /**
     * Obtiene las actividades asociadas a este diagnóstico
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'diagnostico_id', 'id');
    }
    
    /**
     * Obtiene el enlace del diagnóstico
     */
    public function getLinkAttribute()
    {
        return "/v2.0/psicosocial/diagnosticos/{$this->id}";
    }
    
    
    /**
     * Accessor para created_at que mapea _fechaCreado
     */
    public function getCreatedAtAttribute()
    {
        if (isset($this->attributes['created_at'])) {
            return $this->asDateTime($this->attributes['created_at']);
        }
        
        if (isset($this->attributes['_fechaCreado'])) {
            return $this->asDateTime($this->attributes['_fechaCreado']);
        }
        
        return null;
    }
    
    /**
     * Accessor para updated_at que mapea _fechaModificado
     */
    public function getUpdatedAtAttribute()
    {
        if (isset($this->attributes['updated_at'])) {
            return $this->asDateTime($this->attributes['updated_at']);
        }
        
        if (isset($this->attributes['_fechaModificado'])) {
            return $this->asDateTime($this->attributes['_fechaModificado']);
        }
        
        return null;
    }
}
