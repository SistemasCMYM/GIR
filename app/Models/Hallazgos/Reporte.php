<?php

namespace App\Models\Hallazgo;

use MongoDB\Laravel\Eloquent\Model;
use App\Models\Empresas\Empleado;

class Reporte extends Model
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_hallazgos';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'reportes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'titulo',
        'descripcion',
        'tipo',
        'categoria',
        'severidad',
        'fecha_hallazgo',
        'lugar',
        'area_id',
        'centro_id',
        'empleado_reporta_id',
        'empleado_involucrado_id',
        'estado',
        'acciones_tomadas',
        'fecha_cierre',
        'archivos_adjuntos',
        'variables_asociadas',
        'observaciones',
        'datos_adicionales'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_hallazgo' => 'datetime',
            'fecha_cierre' => 'datetime',
            'archivos_adjuntos' => 'array',
            'variables_asociadas' => 'array',
            'acciones_tomadas' => 'array',
            'datos_adicionales' => 'object'
        ];
    }

    /**
     * Get the empresa that the reporte belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Get the usuario that created the reporte.
     */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    /**
     * Get the empleado that reported the hallazgo.
     */
    public function empleadoReporta()
    {
        return $this->belongsTo(Empleado::class, 'empleado_reporta_id');
    }

    /**
     * Get the empleado involved in the hallazgo.
     */
    public function empleadoInvolucrado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_involucrado_id');
    }

    /**
     * Get the variables associated with this reporte.
     */
    public function variables()
    {
        return $this->hasMany(Variable::class, 'reporte_id');
    }

    /**
     * Scope for pending reports
     */
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope for reports by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severidad', $severity);
    }

    /**
     * Get report types
     */
    public static function getTypes(): array
    {
        return [
            'accidente' => 'Accidente de Trabajo',
            'incidente' => 'Incidente',
            'enfermedad' => 'Enfermedad Laboral',
            'acto_inseguro' => 'Acto Inseguro',
            'condicion_insegura' => 'Condición Insegura',
            'casi_accidente' => 'Casi Accidente',
            'otro' => 'Otro'
        ];
    }

    /**
     * Get severity levels
     */
    public static function getSeverityLevels(): array
    {
        return [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'critica' => 'Crítica'
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'cerrado' => 'Cerrado',
            'cancelado' => 'Cancelado'
        ];
    }

    /**
     * Close the report
     */
    public function close(string $observacion = ''): void
    {
        $this->update([
            'estado' => 'cerrado',
            'fecha_cierre' => now(),
            'observaciones' => $observacion
        ]);
    }
}

class Variable extends Model
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_hallazgos';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'variables';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reporte_id',
        'nombre',
        'tipo',
        'valor',
        'unidad',
        'descripcion',
        'categoria',
        'es_obligatoria',
        'valores_permitidos',
        'rango_minimo',
        'rango_maximo',
        'datos_adicionales'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'valor' => 'object',
            'es_obligatoria' => 'boolean',
            'valores_permitidos' => 'array',
            'rango_minimo' => 'decimal:2',
            'rango_maximo' => 'decimal:2',
            'datos_adicionales' => 'object'
        ];
    }

    /**
     * Get the reporte that the variable belongs to.
     */
    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'reporte_id');
    }

    /**
     * Get variable types
     */
    public static function getTypes(): array
    {
        return [
            'texto' => 'Texto',
            'numero' => 'Número',
            'decimal' => 'Decimal',
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'boolean' => 'Sí/No',
            'seleccion' => 'Selección',
            'multiple' => 'Selección Múltiple'
        ];
    }

    /**
     * Validate variable value
     */
    public function validateValue($value): bool
    {
        switch ($this->tipo) {
            case 'numero':
                return is_numeric($value) && 
                       ($this->rango_minimo === null || $value >= $this->rango_minimo) &&
                       ($this->rango_maximo === null || $value <= $this->rango_maximo);
            
            case 'seleccion':
                return in_array($value, $this->valores_permitidos ?? []);
            
            case 'boolean':
                return in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false']);
            
            case 'fecha':
                return strtotime($value) !== false;
            
            default:
                return true;
        }
    }
}
