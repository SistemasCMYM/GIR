<?php

namespace App\Models\Auth;

use App\Models\BaseMongoModel;
use Illuminate\Support\Facades\Hash;

/**
 * Modelo Cuenta con generación automática de IDs personalizados
 * Compatible con el schema de Node.js especificado
 */
class Cuenta extends BaseMongoModel
{
    /**
     * Prefijo personalizado para IDs de cuenta
     * 
     * @var string
     */
    protected $idPrefix = '';

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_cmym';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'cuentas';

    /**
     * Roles permitidos según schema Node.js
     */
    public static $roles = [
        'SuperAdmin', 
        'administrador', 
        'profesional', 
        'tecnico', 
        'supervisor', 
        'usuario'
    ];

    /**
     * Estados permitidos según schema Node.js
     */
    public static $estados = [
        'activa', 
        'suspendida', 
        'inactiva'
    ];

    /**
     * Tipos permitidos según schema Node.js
     */
    public static $tipos = [
        'interna', 
        'cliente', 
        'profesional', 
        'crm-cliente', 
        'usuario'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'empleado_id',
        'nick',
        'email', 
        'contrasena',
        'dni',
        'rol',
        'estado',
        'tipo',
        'empresas',
        'canales',
        'centro_key'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'contrasena',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'empresas' => 'array',
        'canales' => 'array',
        'activo' => 'boolean'
    ];

    /**
     * Valores por defecto según schema Node.js
     */
    protected $attributes = [
        'empleado_id' => null,
        'nick' => null,
        'email' => null,
        'contrasena' => '',
        'dni' => null,
        'rol' => 'usuario',
        'estado' => 'inactiva',
        'tipo' => 'cliente',
        'empresas' => [],
        'canales' => [],
        'centro_key' => null
    ];

    /**
     * Generar hash de contraseña compatible con Node.js bcrypt
     * 
     * @param string $password
     * @return string
     */
    public function generarHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }

    /**
     * Comparar contraseña con hash bcrypt
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function compararHash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Virtual para $link compatible con Node.js
     */
    public function getLinkAttribute()
    {
        return "/v2.0/cuentas/{$this->id}";
    }

    /**
     * Relación virtual con permisos
     */
    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'cuenta_id', 'id');
    }

    /**
     * Relación virtual con perfil
     */
    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'cuenta_id', 'id');
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresas', $empresaId);
    }

    /**
     * Scope para filtrar por rol
     */
    public function scopePorRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Verificar si la cuenta tiene acceso a una empresa
     */
    public function tieneAccesoEmpresa($empresaId)
    {
        if ($this->rol === 'SuperAdmin') {
            return true;
        }
        
        return in_array($empresaId, $this->empresas ?? []);
    }

    /**
     * Verificar si la cuenta está activa
     */
    public function estaActiva()
    {
        return $this->estado === 'activa';
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cuenta) {
            // Generar ID único si no se proporciona
            if (empty($cuenta->id)) {
                $cuenta->id = generateBase64UrlId(16);
            }

            // Encriptar contraseña si se proporciona en texto plano
            if (!empty($cuenta->contrasena) && !password_get_info($cuenta->contrasena)['algo']) {
                $cuenta->contrasena = $cuenta->generarHash($cuenta->contrasena);
            }

            // Validar y configurar valores por defecto
            if (!in_array($cuenta->rol, self::$roles)) {
                $cuenta->rol = 'usuario';
            }

            if (!in_array($cuenta->estado, self::$estados)) {
                $cuenta->estado = 'inactiva';
            }

            if (!in_array($cuenta->tipo, self::$tipos)) {
                $cuenta->tipo = 'cliente';
            }

            // Asegurar que empresas y canales sean arrays
            $cuenta->empresas = is_array($cuenta->empresas) ? $cuenta->empresas : [];
            $cuenta->canales = is_array($cuenta->canales) ? $cuenta->canales : [];
        });

        static::updating(function ($cuenta) {
            // Encriptar contraseña si se cambió y no está ya encriptada
            if ($cuenta->isDirty('contrasena') && !empty($cuenta->contrasena)) {
                if (!password_get_info($cuenta->contrasena)['algo']) {
                    $cuenta->contrasena = $cuenta->generarHash($cuenta->contrasena);
                }
            }
        });
    }

    /**
     * Convertir a array con estructura compatible Node.js
     */
    public function toNodejsArray()
    {
        return [
            'id' => $this->id,
            'empleado_id' => $this->empleado_id,
            'nick' => $this->nick,
            'email' => $this->email,
            'contrasena' => $this->contrasena,
            'dni' => $this->dni,
            'rol' => $this->rol,
            'estado' => $this->estado,
            'tipo' => $this->tipo,
            'empresas' => $this->empresas ?? [],
            'canales' => $this->canales ?? [],
            'centro_key' => $this->centro_key,
            '$link' => $this->link
        ];
    }
}
