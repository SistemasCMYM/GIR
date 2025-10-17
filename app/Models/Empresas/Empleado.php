<?php

namespace App\Models\Empresas;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\HasEmpresaScope;

class Empleado extends MongoModel
{
    use HasEmpresaScope;
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_empresas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'empleados';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'empresa_id',
        'numero_documento',
        'tipo_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'fecha_nacimiento',
        'genero',
        'estado_civil',
        'direccion',
        'ciudad_id',
        'ciudad',
        'departamento_id',
        'cargo',
        'area_id',
        'centro_id',
        'proceso_id',
        'grupo_id',
        'fecha_ingreso',
        'fecha_retiro',
        'tipo_contrato',
        'contrato',
        'psicosocial_tipo',
        'tipo_cargo',
        'salario',
        'estado',
        'activo',
        'datos_adicionales'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_ingreso' => 'date',
            'fecha_retiro' => 'date',
            'salario' => 'decimal:2',
            'estado' => 'boolean',
            'datos_adicionales' => 'object'
        ];
    }

    /**
     * Get the empresa that the empleado belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }    /**
     * Get the ciudad that the empleado belongs to.
     */
    /* public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    } */

    /**
     * Get the departamento that the empleado belongs to.
     */
    /* public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    } */

    /**
     * Get the area that the empleado belongs to.
     */
    /* public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    } */

    /**
     * Get the centro that the empleado belongs to.
     */
    /* public function centro()
    {
        return $this->belongsTo(Centro::class, 'centro_id');
    } */

    /**
     * Get the grupo that the empleado belongs to.
     */
    /* public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    } */

    /**
     * Scope for active empleados
     */
    public function scopeActive($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->primer_nombre && $this->primer_apellido) {
            return trim(
                ($this->primer_nombre ?? '') . ' ' . 
                ($this->segundo_nombre ?? '') . ' ' . 
                ($this->primer_apellido ?? '') . ' ' . 
                ($this->segundo_apellido ?? '')
            );
        }
        return $this->nombre . ' ' . $this->apellidos;
    }

    /**
     * Check if empleado is active
     */
    public function isActive(): bool
    {
        return ($this->estado === true || $this->activo === true) && is_null($this->fecha_retiro);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Concatenar nombres completos antes de guardar
        static::saving(function ($empleado) {
            if ($empleado->primer_nombre && $empleado->primer_apellido) {
                $empleado->nombre = trim(
                    ($empleado->primer_nombre ?? '') . ' ' . 
                    ($empleado->segundo_nombre ?? '')
                );
                $empleado->apellidos = trim(
                    ($empleado->primer_apellido ?? '') . ' ' . 
                    ($empleado->segundo_apellido ?? '')
                );
            }
        });
    }
}
