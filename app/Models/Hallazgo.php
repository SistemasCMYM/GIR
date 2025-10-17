<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\GeneratesUniqueId;
use App\Traits\HasEmpresaScope;
use Carbon\Carbon;
use MongoDB\BSON\ObjectId;

class Hallazgo extends MongoModel
{
    use GeneratesUniqueId, HasEmpresaScope;
    
    /**
     * Prefijo personalizado para IDs de hallazgo
     */
    protected $idPrefix = 'HAL';
    
    protected $connection = 'mongodb_empresas'; // Cambiar a conexión empresas para multi-tenancy
    protected $collection = 'hallazgos';    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'codigo',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'prioridad',
        'area',
        'departamento',
        'proceso',
        'fecha_identificacion',
        'fecha_limite',
        'fecha_cierre',
        'responsable',
        'responsable_id',
        'ubicacion',
        'origen',
        'categoria',
        'subcategoria',
        'impacto',
        'probabilidad',
        'nivel_riesgo',
        'acciones_inmediatas',
        'acciones_correctivas',
        'acciones_preventivas',
        'recursos_necesarios',
        'evidencias',
        'seguimientos',
        'observaciones',
        'tags',
        'metadatos'
    ];

    protected $casts = [
        'fecha_identificacion' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_cierre' => 'datetime',
        'acciones_inmediatas' => 'array',
        'acciones_correctivas' => 'array',
        'acciones_preventivas' => 'array',
        'recursos_necesarios' => 'array',
        'evidencias' => 'array',
        'seguimientos' => 'array',
        'tags' => 'array',
        'metadatos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the company that owns the hallazgo
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Get the user who created the hallazgo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Get the responsible person for the hallazgo
     */
    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'responsable_id');
    }

    /**
     * Generate automatic code
     */
    public static function generateCode()
    {
        $year = Carbon::now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'HAL-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate risk level based on impact and probability
     */
    public function calculateRiskLevel()
    {
        $impact = $this->impacto ?? 1;
        $probability = $this->probabilidad ?? 1;
        $riskScore = $impact * $probability;

        if ($riskScore <= 4) {
            return 'bajo';
        } elseif ($riskScore <= 9) {
            return 'medio';
        } elseif ($riskScore <= 16) {
            return 'alto';
        } else {
            return 'critico';
        }
    }

    /**
     * Check if hallazgo is overdue
     */
    public function isOverdue()
    {
        return $this->fecha_limite && 
               Carbon::parse($this->fecha_limite)->isPast() && 
               $this->estado !== 'cerrado';
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
     * Add a new follow-up
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
     * Scope for active hallazgos
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('estado', ['cerrado', 'cancelado']);
    }

    /**
     * Scope for overdue hallazgos
     */
    public function scopeOverdue($query)
    {
        return $query->where('fecha_limite', '<', Carbon::now())
                    ->whereNotIn('estado', ['cerrado', 'cancelado']);
    }

    /**
     * Scope by risk level
     */
    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('nivel_riesgo', $level);
    }

    /**
     * Scope by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('empresa_id', $companyId);
    }
}
