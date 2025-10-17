<?php

namespace App\Models\Plan;

use MongoDB\Laravel\Eloquent\Model;
use App\Models\Empresas\Empleado;

class Plan extends Model
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_planes';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'planes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empresa_id',
        'usuario_creador_id',
        'nombre',
        'descripcion',
        'tipo',
        'categoria',
        'objetivo',
        'alcance',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite',
        'estado',
        'prioridad',
        'responsable_id',
        'presupuesto',
        'recursos_necesarios',
        'criterios_exito',
        'riesgos_identificados',
        'observaciones',
        'progreso_porcentaje',
        'datos_adicionales'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'fecha_limite' => 'date',
            'presupuesto' => 'decimal:2',
            'progreso_porcentaje' => 'decimal:2',
            'recursos_necesarios' => 'array',
            'criterios_exito' => 'array',
            'riesgos_identificados' => 'array',
            'datos_adicionales' => 'object'
        ];
    }

    /**
     * Get the empresa that the plan belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Get the usuario that created the plan.
     */
    public function usuarioCreador()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_creador_id');
    }

    /**
     * Get the responsable of the plan.
     */
    public function responsable()
    {
        return $this->belongsTo(Empleado::class, 'responsable_id');
    }

    /**
     * Get the tareas for the plan.
     */
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'plan_id');
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope for plans by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('prioridad', $priority);
    }

    /**
     * Get plan types
     */
    public static function getTypes(): array
    {
        return [
            'mejora' => 'Plan de Mejora',
            'contingencia' => 'Plan de Contingencia',
            'capacitacion' => 'Plan de Capacitación',
            'auditoria' => 'Plan de Auditoría',
            'inspeccion' => 'Plan de Inspección',
            'emergencia' => 'Plan de Emergencia',
            'mantenimiento' => 'Plan de Mantenimiento',
            'otro' => 'Otro'
        ];
    }

    /**
     * Get priority levels
     */
    public static function getPriorityLevels(): array
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
            'borrador' => 'Borrador',
            'activo' => 'Activo',
            'en_ejecucion' => 'En Ejecución',
            'pausado' => 'Pausado',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado'
        ];
    }

    /**
     * Calculate progress percentage
     */
    public function calculateProgress(): float
    {
        $totalTareas = $this->tareas->count();
        
        if ($totalTareas === 0) {
            return 0.0;
        }

        $tareasCompletadas = $this->tareas->where('estado', 'completada')->count();
        
        return round(($tareasCompletadas / $totalTareas) * 100, 2);
    }

    /**
     * Update progress percentage
     */
    public function updateProgress(): void
    {
        $this->update([
            'progreso_porcentaje' => $this->calculateProgress()
        ]);
    }

    /**
     * Check if plan is overdue
     */
    public function isOverdue(): bool
    {
        return $this->fecha_limite && 
               $this->fecha_limite < now() && 
               !in_array($this->estado, ['completado', 'cancelado']);
    }
}

class Tarea extends Model
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_planes';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'tareas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plan_id',
        'empresa_id',
        'nombre',
        'descripcion',
        'tipo',
        'responsable_id',
        'asignado_a_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite',
        'fecha_completada',
        'estado',
        'prioridad',
        'progreso_porcentaje',
        'tiempo_estimado_horas',
        'tiempo_real_horas',
        'recursos_necesarios',
        'dependencias',
        'observaciones',
        'archivos_adjuntos',
        'datos_adicionales'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'fecha_limite' => 'date',
            'fecha_completada' => 'datetime',
            'progreso_porcentaje' => 'decimal:2',
            'tiempo_estimado_horas' => 'decimal:2',
            'tiempo_real_horas' => 'decimal:2',
            'recursos_necesarios' => 'array',
            'dependencias' => 'array',
            'archivos_adjuntos' => 'array',
            'datos_adicionales' => 'object'
        ];
    }

    /**
     * Get the plan that the tarea belongs to.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get the empresa that the tarea belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Get the responsable of the tarea.
     */
    public function responsable()
    {
        return $this->belongsTo(Empleado::class, 'responsable_id');
    }

    /**
     * Get the assigned person for the tarea.
     */
    public function asignadoA()
    {
        return $this->belongsTo(Empleado::class, 'asignado_a_id');
    }

    /**
     * Scope for active tareas
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'activa');
    }

    /**
     * Scope for completed tareas
     */
    public function scopeCompleted($query)
    {
        return $query->where('estado', 'completada');
    }

    /**
     * Get task status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'activa' => 'Activa',
            'en_progreso' => 'En Progreso',
            'pausada' => 'Pausada',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            'vencida' => 'Vencida'
        ];
    }

    /**
     * Mark task as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'estado' => 'completada',
            'fecha_completada' => now(),
            'progreso_porcentaje' => 100.0
        ]);

        // Update parent plan progress
        $this->plan->updateProgress();
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->fecha_limite && 
               $this->fecha_limite < now() && 
               !in_array($this->estado, ['completada', 'cancelada']);
    }

    /**
     * Calculate duration in days
     */
    public function getDurationDaysAttribute(): int
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 0;
        }

        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }
}
