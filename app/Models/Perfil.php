<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\GeneratesUniqueId;
use App\Models\Rol;

class Perfil extends MongoModel
{
    use GeneratesUniqueId;
    
    protected $connection = 'mongodb_cmym';
    protected $collection = 'perfiles';
    
    // Géneros disponibles (exactos del schema de Node.js)
    public static $generos = [
        'masculino',
        'femenino',
        'otro'
    ];
    
    // Módulos disponibles (exactos del schema de Node.js)
    public static $modulos = [
        'dashboard',
        'administracion',
        'hallazgos',
        'psicosocial'
    ];
    
    // Permisos disponibles (exactos del schema de Node.js)
    public static $permisos = [
        'all',
        'write',
        'read',
        'delete'
    ];
    
    protected $fillable = [
        'id',
        'cuenta_id',
        'rol_id',
        'nombre',
        'apellido',
        'descripcion',
        'genero',
        'modulos',
        'permisos',
        'ocupacion',
        'firma',
        'pieFirma',
        'licencia'
    ];
    
    protected $casts = [
    'genero' => 'string',
    // Modulos y permisos pueden guardarse como array o string dependiendo del origen
    ];
    
    /**
     * Relación virtual con cuenta (uno a uno)
     */
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }
    
    /**
     * Relación virtual con rol (muchos a uno)
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }
    
    /**
     * Obtener el link virtual (igual que Node.js)
     */
    public function getLinkAttribute()
    {
        return "/v2.0/cuenta/perfil/{$this->id}";
    }
    
    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellido ?? ''));
    }
    
    /**
     * Verificar si el perfil tiene un módulo específico
     */
    public function tieneModulo($modulo)
    {
        return $this->modulos === $modulo;
    }
    
    /**
     * Verificar si el perfil tiene un permiso específico
     */
    public function tienePermiso($permiso)
    {
        return $this->permisos === $permiso || $this->permisos === 'all';
    }
    
    /**
     * Scope para filtrar por cuenta
     */
    public function scopeParaCuenta($query, $cuentaId)
    {
        return $query->where('cuenta_id', $cuentaId);
    }
    
    /**
     * Scope para filtrar por módulo
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulos', $modulo);
    }
    
    /**
     * Scope para filtrar por género
     */
    public function scopePorGenero($query, $genero)
    {
        return $query->where('genero', $genero);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = generateBase64UrlId();
            }
            
            // Módulo por defecto según el schema de Node.js
            if (!isset($model->modulos)) {
                $model->modulos = 'dashboard';
            }
            
            // Valores por defecto (strings vacíos)
            if (!isset($model->firma)) {
                $model->firma = '';
            }
            
            if (!isset($model->pieFirma)) {
                $model->pieFirma = '';
            }
            
            if (!isset($model->licencia)) {
                $model->licencia = '';
            }
        });
    }

    /**
     * Normalizar modulos como array
     */
    public function getModulosAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }
        return [$value];
    }

    /**
     * Normalizar permisos como array
     */
    public function getPermisosAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }
        return [$value];
    }
}
