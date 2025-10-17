<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\GeneratesUniqueId;
use App\Traits\HasEmpresaScope;
use App\Models\Empresas\Empleado;
use Carbon\Carbon;
use MongoDB\BSON\ObjectId;

class EvaluacionPsicosocial extends MongoModel
{
    use GeneratesUniqueId, HasEmpresaScope;
    
    /**
     * Prefijo personalizado para IDs de evaluación psicosocial
     */
    protected $idPrefix = 'PSI';
    
    protected $connection = 'mongodb_empresas'; // Cambiar a conexión empresas para multi-tenancy
    protected $collection = 'evaluaciones_psicosociales';

    protected $fillable = [
        'empresa_id',
        'empleado_id',
        'evaluador_id',
        'codigo',
        'tipo_evaluacion',
        'instrumento_utilizado',
        'fecha_evaluacion',
        'fecha_inicio',
        'fecha_finalizacion',
        'estado',
        'completitud',
        'datos_personales',
        'datos_laborales',
        'dimensiones',
        'resultados',
        'interpretacion',
        'recomendaciones',
        'plan_intervencion',
        'nivel_riesgo_general',
        'niveles_dimension',
        'observaciones',
        'validada',
        'validada_por',
        'fecha_validacion',
        'metadatos'
    ];

    protected $casts = [
        'fecha_evaluacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
        'fecha_validacion' => 'datetime',
        'datos_personales' => 'array',
        'datos_laborales' => 'array',
        'dimensiones' => 'array',
        'resultados' => 'array',
        'interpretacion' => 'array',
        'recomendaciones' => 'array',
        'plan_intervencion' => 'array',
        'niveles_dimension' => 'array',
        'metadatos' => 'array',
        'validada' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the company that owns the evaluation
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Get the employee being evaluated
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Get the evaluator
     */
    public function evaluador()
    {
        return $this->belongsTo(Usuario::class, 'evaluador_id');
    }

    /**
     * Get the validator
     */
    public function validador()
    {
        return $this->belongsTo(Usuario::class, 'validada_por');
    }

    /**
     * Generate automatic code
     */
    public static function generateCode()
    {
        $year = Carbon::now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'PSI-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate overall risk level based on dimensions
     */
    public function calculateOverallRiskLevel()
    {
        if (!$this->niveles_dimension) {
            return 'sin_evaluar';
        }

        $riskLevels = ['muy_bajo' => 0, 'bajo' => 1, 'medio' => 2, 'alto' => 3, 'muy_alto' => 4];
        $totalScore = 0;
        $dimensionCount = 0;

        foreach ($this->niveles_dimension as $dimension => $level) {
            if (isset($riskLevels[$level])) {
                $totalScore += $riskLevels[$level];
                $dimensionCount++;
            }
        }

        if ($dimensionCount === 0) {
            return 'sin_evaluar';
        }

        $averageScore = $totalScore / $dimensionCount;

        if ($averageScore < 0.8) {
            return 'muy_bajo';
        } elseif ($averageScore < 1.6) {
            return 'bajo';
        } elseif ($averageScore < 2.4) {
            return 'medio';
        } elseif ($averageScore < 3.2) {
            return 'alto';
        } else {
            return 'muy_alto';
        }
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage()
    {
        if (!$this->dimensiones) {
            return 0;
        }

        $totalQuestions = 0;
        $answeredQuestions = 0;

        foreach ($this->dimensiones as $dimension => $data) {
            if (isset($data['preguntas'])) {
                $totalQuestions += count($data['preguntas']);
                foreach ($data['preguntas'] as $pregunta) {
                    if (isset($pregunta['respuesta']) && $pregunta['respuesta'] !== null) {
                        $answeredQuestions++;
                    }
                }
            }
        }

        return $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 2) : 0;
    }

    /**
     * Check if evaluation requires intervention
     */
    public function requiresIntervention()
    {
        $riskLevel = $this->nivel_riesgo_general ?? $this->calculateOverallRiskLevel();
        return in_array($riskLevel, ['alto', 'muy_alto']);
    }

    /**
     * Mark as validated
     */
    public function markAsValidated($validatorId)
    {
        $this->update([
            'validada' => true,
            'validada_por' => $validatorId,
            'fecha_validacion' => Carbon::now(),
            'estado' => 'validada'
        ]);
    }

    /**
     * Scope for completed evaluations
     */
    public function scopeCompleted($query)
    {
        return $query->where('estado', 'finalizada');
    }

    /**
     * Scope for pending evaluations
     */
    public function scopePending($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    /**
     * Scope for high risk evaluations
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('nivel_riesgo_general', ['alto', 'muy_alto']);
    }

    /**
     * Scope by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('empresa_id', $companyId);
    }

    /**
     * Scope by evaluation type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('tipo_evaluacion', $type);
    }
}
