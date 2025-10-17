<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\GeneratesUniqueId;

class Notificacion extends MongoModel
{
    use GeneratesUniqueId;

    protected $connection = 'mongodb_cmym';
    protected $collection = 'notificaciones';
    
    // Módulos disponibles (exactos del schema de Node.js)
    public static $modulos = [
        'hallazgos',
        'psicosocial', 
        'plan-trabajo'
    ];

    protected $fillable = [
        'id',
        'empresa_id',
        'vista',
        'link',
        'titulo',
        'descripcion',
        'modulo',
        'canales'
    ];

    protected $casts = [
        'vista' => 'boolean',
        'canales' => 'array',
        'modulo' => 'string'
    ];

    /**
     * Obtener el link virtual (igual que Node.js)
     */
    public function getLinkAttribute($value)
    {
        return $value ?? "/v2.0/notificaciones/{$this->id}";
    }

    /**
     * Marcar notificación como vista
     */
    public function marcarComoVista()
    {
        $this->update(['vista' => true]);
    }

    /**
     * Verificar si la notificación ha sido vista
     */
    public function fueVista()
    {
        return $this->vista === true;
    }

    /**
     * Scope para notificaciones no vistas
     */
    public function scopeNoVistas($query)
    {
        return $query->where('vista', false);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeParaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para filtrar por módulo
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Crear notificación del sistema
     */
    public static function crearNotificacionSistema($empresaId, $titulo, $descripcion, $modulo = 'plan-trabajo', $link = null)
    {
        return self::create([
            'id' => generateBase64UrlId(),
            'empresa_id' => $empresaId,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'modulo' => $modulo,
            'link' => $link,
            'vista' => false,
            'canales' => []
        ]);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = generateBase64UrlId();
            }
            
            // Vista por defecto según el schema de Node.js
            if (!isset($model->vista)) {
                $model->vista = false;
            }
            
            // Módulo por defecto según el schema de Node.js
            if (!isset($model->modulo)) {
                $model->modulo = 'plan-trabajo';
            }
            
            // Canales por defecto (array vacío)
            if (!isset($model->canales)) {
                $model->canales = [];
            }
        });
    }
}
