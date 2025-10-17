<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\HasCustomId;
use App\Traits\HasEmpresaScope;

class Encuesta extends BaseMongoModel
{
    use HasCustomId, HasEmpresaScope;

    protected $connection = 'mongodb_empresas';
    protected $table = 'encuestas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'contenido',
        'preguntas',
        'configuracion',
        'tipo',
        'estado',
        'publicada',
        'plantilla',
        'empresa_id',
        'fecha_creacion',
        'fecha_modificacion',
        'fecha_publicacion',
        'usuario_creador',
        'usuario_modificador',
        'items_total',
        'tiempo_estimado',
        'categoria'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
        'fecha_publicacion' => 'datetime',
        'estado' => 'boolean',
        'publicada' => 'boolean',
        'plantilla' => 'boolean',
        'items_total' => 'integer',
        'tiempo_estimado' => 'integer',
        'preguntas' => 'array',
        'configuracion' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->fecha_creacion = now();
            $model->fecha_modificacion = now();
            
            if (!isset($model->estado)) {
                $model->estado = true;
            }
            
            if (!isset($model->publicada)) {
                $model->publicada = false;
            }
            
            if (!isset($model->plantilla)) {
                $model->plantilla = false;
            }
        });

        static::updating(function ($model) {
            $model->fecha_modificacion = now();
        });
    }

    /**
     * Scope para encuestas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para encuestas publicadas
     */
    public function scopePublicadas($query)
    {
        return $query->where('publicada', true);
    }

    /**
     * Scope para plantillas
     */
    public function scopePlantillas($query)
    {
        return $query->where('plantilla', true);
    }

    /**
     * Scope por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Obtener el estado como texto
     */
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activa' : 'Inactiva';
    }

    /**
     * Obtener el estado de publicación como texto
     */
    public function getPublicacionTextoAttribute()
    {
        return $this->publicada ? 'Publicada' : 'Borrador';
    }

    /**
     * Obtener el tipo de encuesta formateado
     */
    public function getTipoFormateadoAttribute()
    {
        $tipos = [
            'satisfaccion' => 'Satisfacción',
            'clima_laboral' => 'Clima Laboral',
            'evaluacion_desempeño' => 'Evaluación de Desempeño',
            'feedback_360' => 'Feedback 360°',
            'cultura_organizacional' => 'Cultura Organizacional',
            'personalizada' => 'Personalizada',
            'general' => 'General'
        ];

        return $tipos[$this->tipo] ?? ucfirst($this->tipo);
    }

    /**
     * Obtener la categoría formateada
     */
    public function getCategoriaFormateadaAttribute()
    {
        $categorias = [
            'rrhh' => 'Recursos Humanos',
            'psicosocial' => 'Psicosocial',
            'seguridad' => 'Seguridad y Salud',
            'calidad' => 'Calidad',
            'satisfaccion' => 'Satisfacción',
            'general' => 'General'
        ];

        return $categorias[$this->categoria] ?? ucfirst($this->categoria);
    }

    /**
     * Publicar encuesta
     */
    public function publicar()
    {
        $this->update([
            'publicada' => true,
            'fecha_publicacion' => now()
        ]);
        
        return $this;
    }

    /**
     * Despublicar encuesta
     */
    public function despublicar()
    {
        $this->update(['publicada' => false]);
        return $this;
    }

    /**
     * Marcar como plantilla
     */
    public function marcarComoPlantilla()
    {
        $this->update(['plantilla' => true]);
        return $this;
    }

    /**
     * Clonar encuesta
     */
    public function clonar($nuevo_titulo = null)
    {
        $nueva_encuesta = $this->replicate();
        $nueva_encuesta->titulo = $nuevo_titulo ?? $this->titulo . ' (Copia)';
        $nueva_encuesta->publicada = false;
        $nueva_encuesta->plantilla = false;
        $nueva_encuesta->fecha_publicacion = null;
        $nueva_encuesta->save();

        return $nueva_encuesta;
    }

    /**
     * Contar total de preguntas
     */
    public function contarPreguntas()
    {
        return is_array($this->preguntas) ? count($this->preguntas) : 0;
    }

    /**
     * Actualizar items_total basado en preguntas
     */
    public function actualizarItemsTotal()
    {
        $this->update(['items_total' => $this->contarPreguntas()]);
        return $this;
    }
}
