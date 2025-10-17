<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class RespuestaEncuesta extends Model
{
    use HasFactory;

    protected $connection = 'mongodb_empresas';
    protected $collection = 'respuestas_encuestas';

    protected $fillable = [
        'encuesta_id',
        'usuario_id',
        'respuestas',
        'fecha_inicio',
        'fecha_completada',
        'tiempo_respuesta',
        'ip_address',
        'user_agent',
        'completada',
        'parcial',
        'metadata'
    ];

    protected $casts = [
        'respuestas' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_completada' => 'datetime',
        'completada' => 'boolean',
        'parcial' => 'boolean',
        'metadata' => 'array',
        'tiempo_respuesta' => 'integer'
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_completada',
        'created_at',
        'updated_at'
    ];

    /**
     * Relación con la encuesta
     */
    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'encuesta_id', '_id');
    }

    /**
     * Relación con el usuario (si la encuesta no es anónima)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para respuestas completas
     */
    public function scopeCompletas($query)
    {
        return $query->where('completada', true);
    }

    /**
     * Scope para respuestas parciales
     */
    public function scopeParciales($query)
    {
        return $query->where('parcial', true);
    }

    /**
     * Scope para respuestas de una encuesta específica
     */
    public function scopeDeEncuesta($query, $encuestaId)
    {
        return $query->where('encuesta_id', $encuestaId);
    }

    /**
     * Scope para respuestas de un usuario específico
     */
    public function scopeDeUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para respuestas en un rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener el porcentaje de completitud de la respuesta
     */
    public function getPorcentajeCompletitudAttribute()
    {
        if (!$this->encuesta || !$this->encuesta->preguntas) {
            return 0;
        }

        $totalPreguntas = count($this->encuesta->preguntas);
        $preguntasRespondidas = 0;

        foreach ($this->respuestas as $respuesta) {
            if (!empty($respuesta)) {
                $preguntasRespondidas++;
            }
        }

        return $totalPreguntas > 0 ? round(($preguntasRespondidas / $totalPreguntas) * 100, 2) : 0;
    }

    /**
     * Obtener el tiempo de respuesta formateado
     */
    public function getTiempoRespuestaFormateadoAttribute()
    {
        if (!$this->tiempo_respuesta) {
            return 'No disponible';
        }

        $horas = floor($this->tiempo_respuesta / 60);
        $minutos = $this->tiempo_respuesta % 60;

        if ($horas > 0) {
            return "{$horas}h {$minutos}m";
        }

        return "{$minutos}m";
    }

    /**
     * Verificar si la respuesta está completa
     */
    public function estaCompleta()
    {
        return $this->completada === true;
    }

    /**
     * Verificar si la respuesta es parcial
     */
    public function esParcial()
    {
        return $this->parcial === true;
    }

    /**
     * Obtener respuesta de una pregunta específica
     */
    public function respuestaDePregunta($indicePregunta)
    {
        return $this->respuestas[$indicePregunta] ?? null;
    }

    /**
     * Calcular estadísticas básicas de las respuestas
     */
    public static function estadisticasDeEncuesta($encuestaId)
    {
        $respuestas = self::deEncuesta($encuestaId)->get();
        
        return [
            'total' => $respuestas->count(),
            'completas' => $respuestas->where('completada', true)->count(),
            'parciales' => $respuestas->where('parcial', true)->count(),
            'tasa_completitud' => $respuestas->count() > 0 ? 
                round(($respuestas->where('completada', true)->count() / $respuestas->count()) * 100, 2) : 0,
            'tiempo_promedio' => $respuestas->where('completada', true)->avg('tiempo_respuesta') ?? 0,
            'fecha_primera' => $respuestas->min('created_at'),
            'fecha_ultima' => $respuestas->max('created_at')
        ];
    }

    /**
     * Exportar respuestas a array para análisis
     */
    public static function exportarParaAnalisis($encuestaId)
    {
        return self::deEncuesta($encuestaId)
            ->completas()
            ->select(['respuestas', 'tiempo_respuesta', 'created_at', 'usuario_id'])
            ->get()
            ->toArray();
    }

    /**
     * Obtener distribución de respuestas por pregunta
     */
    public static function distribucionRespuestas($encuestaId, $indicePregunta)
    {
        $respuestas = self::deEncuesta($encuestaId)
            ->completas()
            ->pluck('respuestas')
            ->map(function ($respuesta) use ($indicePregunta) {
                return $respuesta[$indicePregunta] ?? null;
            })
            ->filter()
            ->countBy();

        return $respuestas->toArray();
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($respuesta) {
            // Establecer valores por defecto
            if (is_null($respuesta->completada)) {
                $respuesta->completada = false;
            }
            if (is_null($respuesta->parcial)) {
                $respuesta->parcial = !$respuesta->completada;
            }
        });

        static::updating(function ($respuesta) {
            // Actualizar estado parcial/completo
            if ($respuesta->completada && $respuesta->parcial) {
                $respuesta->parcial = false;
            }
        });
    }
}
