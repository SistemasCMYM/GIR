<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\GeneratesUniqueId;

class Permiso extends MongoModel
{
    use GeneratesUniqueId;
    
    protected $connection = 'mongodb_cmym';
    protected $collection = 'permisos';
    
    // Tipos de permiso disponibles (exactos del schema de Node.js)
    public static $tipos = [
        'interna',
        'cliente',
        'profesional',
        'crm-cliente',
        'usuario'
    ];
    
    protected $fillable = [
        'id',
        'cuenta_id',
        'modulo',
        'tipo',
        'acciones',
        'link'
    ];
    
    protected $casts = [
    'tipo' => 'string'
    ];

    /**
     * Normalizar el atributo acciones: si ya es array devolverlo, si es string intentar decodificar
     */
    public function getAccionesAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
    
    /**
     * Relación virtual con cuenta
     */
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }
    
    /**
     * Obtener el link virtual (igual que Node.js)
     */
    public function getLinkAttribute($value)
    {
        return $value ?? "/v2.0/permisos/{$this->id}";
    }
    
    /**
     * Verificar si el permiso incluye una acción específica
     */
    public function tieneAccion($accion)
    {
        return in_array($accion, $this->acciones ?? []);
    }
    
    /**
     * Agregar acción al permiso
     */
    public function agregarAccion($accion)
    {
        $acciones = $this->acciones ?? [];
        if (!in_array($accion, $acciones)) {
            $acciones[] = $accion;
            $this->update(['acciones' => $acciones]);
        }
    }
    
    /**
     * Remover acción del permiso
     */
    public function removerAccion($accion)
    {
        $acciones = $this->acciones ?? [];
        $acciones = array_values(array_filter($acciones, function($a) use ($accion) {
            return $a !== $accion;
        }));
        $this->update(['acciones' => $acciones]);
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
    public function scopeParaModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }
    
    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    /**
     * Scope para permisos que incluyen una acción específica
     */
    public function scopeConAccion($query, $accion)
    {
        return $query->where('acciones', $accion);
    }
    
    /**
     * Crear permisos básicos para una cuenta según su rol
     */
    public static function crearPermisosPorRol($cuentaId, $rol, $tipo)
    {
        $configuracionRoles = [
            'SuperAdmin' => [
                'modulos' => ['dashboard', 'administracion', 'hallazgos', 'psicosocial'],
                'acciones' => ['crear', 'leer', 'actualizar', 'eliminar', 'exportar']
            ],
            'administrador' => [
                'modulos' => ['dashboard', 'administracion', 'hallazgos', 'psicosocial'],
                'acciones' => ['crear', 'leer', 'actualizar', 'eliminar', 'exportar']
            ],
            'profesional' => [
                'modulos' => ['dashboard', 'psicosocial'],
                'acciones' => ['crear', 'leer', 'actualizar', 'exportar']
            ],
            'tecnico' => [
                'modulos' => ['dashboard', 'hallazgos'],
                'acciones' => ['crear', 'leer', 'actualizar']
            ],
            'supervisor' => [
                'modulos' => ['dashboard', 'hallazgos', 'psicosocial'],
                'acciones' => ['leer', 'exportar']
            ],
            'usuario' => [
                'modulos' => ['dashboard'],
                'acciones' => ['leer']
            ]
        ];
        
        $config = $configuracionRoles[$rol] ?? $configuracionRoles['usuario'];
        
        foreach ($config['modulos'] as $modulo) {
            self::create([
                'id' => generateBase64UrlId(),
                'cuenta_id' => $cuentaId,
                'modulo' => $modulo,
                'tipo' => $tipo,
                'acciones' => $config['acciones'],
                'link' => "/v2.0/permisos/" . generateBase64UrlId()
            ]);
        }
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = generateBase64UrlId();
            }
            
            // Acciones por defecto (array vacío)
            if (!isset($model->acciones)) {
                $model->acciones = [];
            }
        });
    }
}
