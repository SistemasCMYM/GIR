<?php

namespace App\Models\Auth;

use App\Models\BaseMongoModel;
use App\Traits\SafeJson;

/**
 * Modelo Rol compatible con schema Node.js
 * Base de datos: cmym, Colección: roles
 * Enlazado a cuentas por cuenta_id y empresas por empresa_id
 */
class Rol extends BaseMongoModel
{
    /**
     * The connection name for the model.
     */
    use SafeJson;
    protected $connection = 'mongodb_cmym';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'roles';
    /**
     * Roles permitidos según especificación del sistema
     */
    public static $roles = [
        'SuperAdmin',      // Super Administrador
        'administrador',   // Administrador empresa cliente
        'profesional',     // Psicólogo/a o Profesional Psicosocial
        'tecnico',         // Técnico/a de Hallazgos
        'supervisor',      // Supervisor/a de Casos
        'usuario'          // Usuario Final
    ];

    /**
     * Tipos de roles permitidos según especificación
     */
    public static $tipos = [
        'interna',         // Para SuperAdmin
        'cliente',         // Para administrador de empresa cliente
        'profesional',     // Para profesionales psicosociales
        'usuario'          // Para técnicos, supervisores y usuarios finales
    ];

    /**
     * Módulos disponibles en el sistema
     */
    public static $modulos = [
        'dashboard',       // Panel principal
        'administracion',  // Gestión de usuarios y configuraciones
        'hallazgos',       // Módulo de hallazgos/incidentes
        'psicosocial',     // Módulo psicosocial
        'configuracion',   // Configuraciones del sistema
        'informes'         // Generación de reportes
    ];

    /**
     * Permisos disponibles en el sistema
     */
    public static $permisos = [
        'all',            // Acceso completo
        'create',         // Crear
        'read',           // Leer
        'update',         // Actualizar
        'delete',         // Eliminar
        'admin',          // Administración
        'write'           // Escribir
    ];
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'tipo',
        'empresa_id',
        'cuenta_id',
        'modulos',
        'permisos',
        'activo'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'modulos' => 'array',
        'permisos' => 'array',
        'activo' => 'boolean'
    ];

    // Safe accessors for modulos and permisos (normalize arrays/JSON strings)
    public function getModulosAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    public function getPermisosAttribute($value)
    {
        return $this->safeJsonDecode($value, []);
    }

    /**
     * Valores por defecto
     */
    protected $attributes = [
        'modulos' => [],
        'permisos' => [],
        'activo' => true
    ];
    /**
     * Configuración predefinida de roles del sistema
     */
    public static $rolesConfig = [
        'SuperAdmin' => [
            'nombre' => 'Super Administrador',
            'tipo' => 'interna',
            'descripcion' => 'Acceso completo a todos los módulos y configuraciones del sistema. Gestión de usuarios y roles, configuración de la plataforma, supervisión de todos los hallazgos y casos psicosociales, generación de reportes y análisis globales.',
            'modulos' => ['dashboard', 'administracion', 'hallazgos', 'psicosocial', 'configuracion', 'informes'],
            'permisos' => ['all', 'create', 'read', 'update', 'delete', 'admin']
        ],
        'administrador' => [
            'nombre' => 'Administrador Empresa Cliente',
            'tipo' => 'cliente',
            'descripcion' => 'Acceso completo a todos los módulos asociados de la empresa cliente. Gestión de usuarios y roles, supervisión de todos los hallazgos y casos psicosociales, generación de reportes y análisis globales.',
            'modulos' => ['dashboard', 'administracion', 'hallazgos', 'psicosocial', 'informes'],
            'permisos' => ['all', 'create', 'read', 'update', 'delete']
        ],
        'profesional' => [
            'nombre' => 'Psicólogo/a o Profesional Psicosocial',
            'tipo' => 'profesional',
            'descripcion' => 'Acceso al módulo Psicosocial. Registro y seguimiento de casos psicosociales, aplicación de evaluaciones y encuestas, análisis de resultados y elaboración de informes, intervención directa con usuarios.',
            'modulos' => ['dashboard', 'psicosocial'],
            'permisos' => ['read', 'write', 'create', 'update']
        ],
        'tecnico' => [
            'nombre' => 'Técnico/a de Hallazgos',
            'tipo' => 'usuario',
            'descripcion' => 'Acceso al módulo Hallazgos. Registro de incidentes o hallazgos reportados, clasificación y priorización de hallazgos, coordinación con otros departamentos para resolución, elaboración de informes sobre hallazgos y acciones tomadas.',
            'modulos' => ['dashboard', 'hallazgos'],
            'permisos' => ['read', 'write', 'create', 'update']
        ],
        'supervisor' => [
            'nombre' => 'Supervisor/a de Casos',
            'tipo' => 'usuario',
            'descripcion' => 'Acceso de lectura y seguimiento en ambos módulos. Supervisión del progreso de casos psicosociales y hallazgos, coordinación entre equipos, aseguramiento de la calidad en la intervención y resolución de casos.',
            'modulos' => ['dashboard', 'hallazgos', 'psicosocial'],
            'permisos' => ['read']
        ],
        'usuario' => [
            'nombre' => 'Usuario Final',
            'tipo' => 'usuario',
            'descripcion' => 'Acceso limitado según necesidad. Reporte de hallazgos o situaciones psicosociales, participación en encuestas o evaluaciones.',
            'modulos' => ['dashboard'],
            'permisos' => ['read']
        ]
    ];
    
    /**
     * Relación con la cuenta asociada
     */
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }
    
    /**
     * Relación con cuentas que tienen este rol
     */
    public function cuentas()
    {
        return $this->hasMany(Cuenta::class, 'rol', 'nombre');
    }
    
    /**
     * Relación con permisos asociados a este rol
     */
    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'rol_id', 'id');
    }
    
    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function tienePermiso($permiso)
    {
        $permisos = $this->permisos ?? [];
        
        // Si tiene permiso 'all', puede hacer todo
        if (in_array('all', $permisos)) {
            return true;
        }
        
        // Verificar permiso específico
        return in_array($permiso, $permisos);
    }
    
    /**
     * Verificar si el rol puede acceder a un módulo
     */
    public function puedeAccederModulo($modulo)
    {
        $modulos = $this->modulos ?? [];
        
        // Si tiene acceso a administracion, puede acceder a todo
        if (in_array('administracion', $modulos)) {
            return true;
        }
        
        // Verificar acceso específico al módulo
        return in_array($modulo, $modulos);
    }
    
    /**
     * Virtual para $link compatible con Node.js
     */
    public function getLinkAttribute()
    {
        return "/v2.0/roles/{$this->id}";
    }
    
    /**
     * Verificar si es un rol de super administrador
     */
    public function esSuperAdmin()
    {
        return $this->nombre === 'SuperAdmin' && $this->tipo === 'interna';
    }
    
    /**
     * Verificar si es administrador (SuperAdmin o administrador de empresa)
     */
    public function esAdministrador()
    {
        return in_array($this->nombre, ['SuperAdmin', 'administrador']);
    }
    
    /**
     * Obtener la configuración predefinida del rol
     */
    public function getConfiguracionRol()
    {
        return self::$rolesConfig[$this->nombre] ?? null;
    }

    /**
     * Verificar si pertenece a una empresa específica
     */
    public function perteneceAEmpresa($empresaId)
    {
        // SuperAdmin no pertenece a empresa específica
        if ($this->esSuperAdmin()) {
            return true;
        }
        
        return $this->empresa_id === $empresaId;
    }

    /**
     * Scope para filtrar por nombre de rol
     */
    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', $nombre);
    }
    /**
     * Crear roles predefinidos del sistema para una empresa
     */
    public static function crearRolesSistema($empresaId = null, $cuentaId = null)
    {
        $rolesCreados = [];
        
        foreach (self::$rolesConfig as $key => $config) {
            // Solo crear SuperAdmin si no hay empresa específica (rol global)
            if ($key === 'SuperAdmin' && $empresaId) {
                continue;
            }
            
            $rol = self::firstOrCreate(
                [
                    'nombre' => $key,
                    'empresa_id' => $empresaId,
                    'cuenta_id' => $cuentaId
                ],
                [
                    'descripcion' => $config['descripcion'],
                    'tipo' => $config['tipo'],
                    'modulos' => $config['modulos'],
                    'permisos' => $config['permisos'],
                    'activo' => true
                ]
            );
            
            $rolesCreados[] = $rol;
        }
        
        return $rolesCreados;
    }
    
    /**
     * Scope para filtrar por empresa
     */
    public function scopeParaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
    
    /**
     * Scope para roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    /**
     * Scope para roles de un tipo específico
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rol) {
            // Generar ID único si no se proporciona
            if (empty($rol->id)) {
                $rol->id = generateBase64UrlId(16);
            }

            // Validar y configurar valores por defecto
            if (!in_array($rol->nombre, self::$roles)) {
                throw new \InvalidArgumentException("Rol '{$rol->nombre}' no válido. Roles permitidos: " . implode(', ', self::$roles));
            }

            if (!in_array($rol->tipo, self::$tipos)) {
                throw new \InvalidArgumentException("Tipo '{$rol->tipo}' no válido. Tipos permitidos: " . implode(', ', self::$tipos));
            }

            // Asegurar que esté activo por defecto
            if (!isset($rol->activo)) {
                $rol->activo = true;
            }

            // Aplicar configuración predefinida si no se especifica
            if (empty($rol->modulos) || empty($rol->permisos)) {
                $config = self::$rolesConfig[$rol->nombre] ?? null;
                if ($config) {
                    if (empty($rol->modulos)) {
                        $rol->modulos = $config['modulos'];
                    }
                    if (empty($rol->permisos)) {
                        $rol->permisos = $config['permisos'];
                    }
                    if (empty($rol->descripcion)) {
                        $rol->descripcion = $config['descripcion'];
                    }
                }
            }
        });
    }

    /**
     * Convertir a array compatible con Node.js
     */
    public function toNodejsArray()
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'empresa_id' => $this->empresa_id,
            'cuenta_id' => $this->cuenta_id,
            'modulos' => $this->modulos ?? [],
            'permisos' => $this->permisos ?? [],
            'activo' => $this->activo,
            '$link' => $this->link
        ];
    }
}
