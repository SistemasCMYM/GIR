<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasEmpresaScope;

class Datos extends BaseMongoModel
{
    use HasEmpresaScope;
    
    protected $connection = 'mongodb_psicosocial';
    protected $collection = 'datos';
    
    protected $fillable = [
        'id', 'empresa_id', 'empleado_id', 'diagnostico_id', 'hoja_id', 'nombre', 'completado',
        'sede_key', 'sede_label', 'contrato_key', 'contrato_label', 'centro_key', 'centro_label',
        'genero', 'fecha_nacimiento', 'edad', 'estado_civil', 'nivel_estudios', 'profesion',
        'lugar_residencia', 'estrato_social', 'tipo_vivienda', 'dependientes_economicos',
        'lugar_trabajo', 'tiempo_laborado', 'nombre_cargo', 'tipo_cargo', 'tiempo_en_cargo',
        'departamento_cargo', 'tipo_contrato', 'horas_laboradas_dia', 'tipo_salario',
        'created_at', 'updated_at'
    ];
    
    protected $casts = [
        'completado' => 'boolean',
        'fecha_nacimiento' => 'timestamp',
        'dependientes_economicos' => 'integer',
        'horas_laboradas_dia' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Obtiene la hoja asociada a estos datos
     */
    public function hoja()
    {
        return $this->belongsTo(Hoja::class, 'hoja_id', 'id');
    }
    
    /**
     * Obtiene el diagnóstico asociado a estos datos
     */
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id', 'id');
    }
    
    /**
     * Obtiene el enlace de los datos
     */
    public function getLinkAttribute()
    {
        return "/v2.0/psicosocial/datos/{$this->id}";
    }
}
