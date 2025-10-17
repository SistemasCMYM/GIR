<?php

namespace App\Models\Auth;

use App\Models\BaseMongoModel;

/**
 * Modelo Perfil compatible con PerfilSchema Node.js
 * Base de datos: cmym, Colección: perfiles
 */
class Perfil extends BaseMongoModel
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_cmym';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'perfiles';

    /**
     * Géneros permitidos según PerfilSchema Node.js
     */
    public static $generos = [
        'masculino', 
        'femenino', 
        'otro'
    ];

    /**
     * Módulos permitidos según PerfilSchema Node.js
     */
    public static $modulos = [
        'dashboard', 
        'administracion', 
        'hallazgos', 
        'psicosocial'
    ];

    /**
     * Permisos permitidos según PerfilSchema Node.js
     */
    public static $permisos = [
        'all', 
        'write', 
        'read', 
        'delete'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'cuenta_id',
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

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        // Los campos mantienen su tipo string según el schema Node.js
    ];

    /**
     * Valores por defecto según PerfilSchema Node.js
     */
    protected $attributes = [
        'cuenta_id' => null,
        'nombre' => null,
        'apellido' => null,
        'descripcion' => null,
        'genero' => null,
        'modulos' => 'dashboard',
        'permisos' => null,
        'ocupacion' => null,
        'firma' => '',
        'pieFirma' => '',
        'licencia' => ''
    ];

    /**
     * Relación con cuenta
     */
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }

    /**
     * Virtual para $link compatible con PerfilSchema Node.js
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
        return trim($this->nombre . ' ' . $this->apellido);
    }

    /**
     * Scope para filtrar por género
     */
    public function scopePorGenero($query, $genero)
    {
        return $query->where('genero', $genero);
    }

    /**
     * Scope para filtrar por módulo
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulos', $modulo);
    }

    /**
     * Scope para filtrar por permiso
     */
    public function scopePorPermiso($query, $permiso)
    {
        return $query->where('permisos', $permiso);
    }

    /**
     * Scope para filtrar por ocupación
     */
    public function scopePorOcupacion($query, $ocupacion)
    {
        return $query->where('ocupacion', $ocupacion);
    }

    /**
     * Verificar si tiene permiso específico
     */
    public function tienePermiso($permiso)
    {
        return $this->permisos === 'all' || $this->permisos === $permiso;
    }

    /**
     * Verificar si tiene acceso a módulo específico
     */
    public function tieneAccesoModulo($modulo)
    {
        return $this->modulos === $modulo;
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($perfil) {
            // Generar ID único si no se proporciona
            if (empty($perfil->id)) {
                $perfil->id = generateBase64UrlId(16);
            }

            // Validar y configurar valores por defecto
            if (!in_array($perfil->genero, array_merge(self::$generos, [null]))) {
                $perfil->genero = null;
            }

            if (!in_array($perfil->modulos, self::$modulos)) {
                $perfil->modulos = 'dashboard';
            }

            if (!in_array($perfil->permisos, array_merge(self::$permisos, [null]))) {
                $perfil->permisos = null;
            }
        });

        static::updating(function ($perfil) {
            // Validar valores en actualización
            if ($perfil->isDirty('genero') && !in_array($perfil->genero, array_merge(self::$generos, [null]))) {
                $perfil->genero = null;
            }

            if ($perfil->isDirty('modulos') && !in_array($perfil->modulos, self::$modulos)) {
                $perfil->modulos = 'dashboard';
            }

            if ($perfil->isDirty('permisos') && !in_array($perfil->permisos, array_merge(self::$permisos, [null]))) {
                $perfil->permisos = null;
            }
        });
    }

    /**
     * Convertir a array compatible con PerfilSchema Node.js
     */
    public function toNodejsArray()
    {
        return [
            'id' => $this->id,
            'cuenta_id' => $this->cuenta_id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'descripcion' => $this->descripcion,
            'genero' => $this->genero,
            'modulos' => $this->modulos,
            'permisos' => $this->permisos,
            'ocupacion' => $this->ocupacion,
            'firma' => $this->firma,
            'pieFirma' => $this->pieFirma,
            'licencia' => $this->licencia,
            'nombre_completo' => $this->nombre_completo,
            '$link' => $this->link
        ];
    }
}
