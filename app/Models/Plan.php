<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use Carbon\Carbon;
use MongoDB\BSON\ObjectId;

class Plan extends MongoModel
{
    protected $connection = 'mongodb_planes';
    protected $collection = 'planes';

    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'responsable_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'objetivo',
        'origen',
        'origen_id',
        'estado',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite',
        'porcentaje_avance',
        'presupuesto_estimado',
        'presupuesto_ejecutado',
        'recursos_necesarios',
        'tareas',
        'hitos',
        'riesgos',
        'indicadores',
        'documentos',
        'seguimientos',
        'observaciones',
        'aprobado',
        'aprobado_por',
        'fecha_aprobacion',
        'metadatos'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'presupuesto_estimado' => 'decimal:2',
        'presupuesto_ejecutado' => 'decimal:2',
        'porcentaje_avance' => 'integer',
        'recursos_necesarios' => 'array',
        'tareas' => 'array',
        'hitos' => 'array',
        'riesgos' => 'array',
        'indicadores' => 'array',
        'documentos' => 'array',
        'seguimientos' => 'array',
        'metadatos' => 'array',
        'aprobado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the company that owns the plan
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }    /**
     * Get the user who created the plan
     */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    /**
     * Get the responsible person for the plan
     */
    public function responsable()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'responsable_id');
    }

    /**
     * Get the approver
     */
    public function aprobador()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'aprobado_por');
    }

    /**
     * Generate automatic code
     */
    public static function generateCode($type = 'PLAN')
    {
        $year = Carbon::now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return $type . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate progress based on completed tasks
     */
    public function calculateProgress()
    {
        if (!$this->tareas || count($this->tareas) === 0) {
            return 0;
        }

        $totalTasks = count($this->tareas);
        $completedTasks = 0;

        foreach ($this->tareas as $tarea) {
            if (isset($tarea['estado']) && $tarea['estado'] === 'completada') {
                $completedTasks++;
            }
        }

        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Check if plan is overdue
     */
    public function isOverdue()
    {
        return $this->fecha_limite && 
               Carbon::parse($this->fecha_limite)->isPast() && 
               $this->estado !== 'completado';
    }

    /**
     * Get days until deadline
     */
    public function getDaysUntilDeadline()
    {
        if (!$this->fecha_limite) {
            return null;
        }

        return Carbon::now()->diffInDays(Carbon::parse($this->fecha_limite), false);
    }

    /**
     * Add a new task
     */
    public function addTask($taskData)
    {
        $tasks = $this->tareas ?? [];
        $taskData['id'] = uniqid();
        $taskData['created_at'] = Carbon::now();
        $tasks[] = $taskData;

        $this->tareas = $tasks;
        $this->save();

        // Recalculate progress
        $this->porcentaje_avance = $this->calculateProgress();
        $this->save();
    }

    /**
     * Update task status
     */
    public function updateTaskStatus($taskId, $status)
    {
        $tasks = $this->tareas ?? [];
        
        foreach ($tasks as &$task) {
            if ($task['id'] === $taskId) {
                $task['estado'] = $status;
                $task['updated_at'] = Carbon::now();
                break;
            }
        }

        $this->tareas = $tasks;
        $this->save();

        // Recalculate progress
        $this->porcentaje_avance = $this->calculateProgress();
        $this->save();
    }

    /**
     * Add follow-up
     */
    public function addFollowUp($description, $user_id, $type = 'seguimiento')
    {
        $followUps = $this->seguimientos ?? [];
        $followUps[] = [
            'fecha' => Carbon::now(),
            'usuario_id' => $user_id,
            'tipo' => $type,
            'descripcion' => $description,
            'created_at' => Carbon::now()
        ];

        $this->seguimientos = $followUps;
        $this->save();
    }

    /**
     * Mark as approved
     */
    public function markAsApproved($approverId)
    {
        $this->update([
            'aprobado' => true,
            'aprobado_por' => $approverId,
            'fecha_aprobacion' => Carbon::now(),
            'estado' => 'aprobado'
        ]);
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('estado', ['completado', 'cancelado']);
    }

    /**
     * Scope for approved plans
     */
    public function scopeApproved($query)
    {
        return $query->where('aprobado', true);
    }

    /**
     * Scope for overdue plans
     */
    public function scopeOverdue($query)
    {
        return $query->where('fecha_limite', '<', Carbon::now())
                    ->whereNotIn('estado', ['completado', 'cancelado']);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('prioridad', $priority);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('tipo', $type);
    }

    /**
     * Scope by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('empresa_id', $companyId);
    }
}
