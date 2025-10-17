<?php

namespace App\Models;

use App\Models\BaseMongoModel;
use App\Traits\GeneratesUniqueId;
use App\Traits\HasEmpresaScope;
use Carbon\Carbon;

/**
 * Modelo Usuario con generación automática de IDs personalizados
 * y soporte para multi-tenancy por empresa
 * 
 * @method static \MongoDB\Laravel\Eloquent\Builder where(string|array|\Closure $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static \MongoDB\Laravel\Eloquent\Builder orWhere(string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'or')
 * @method static \MongoDB\Laravel\Eloquent\Builder with(string|array $relations)
 * @method static static|null first()
 * @method static static|null find(mixed $id)
 * @method static static create(array $attributes = [])
 * @method static int count()
 * @method static \Illuminate\Database\Eloquent\Collection get()
 */
class Usuario extends BaseMongoModel
{
    use GeneratesUniqueId, HasEmpresaScope;
    /**
     * Prefijo personalizado para IDs de usuario
     * 
     * @var string
     */
    protected $idPrefix = 'USR';

    /**
     * Conexión de base de datos MongoDB
     * 
     * @var string
     */
    protected $connection = 'mongodb_empresas'; // Base de datos empresas
    
    /**
     * Nombre de la colección
     * 
     * @var string
     */
    protected $collection = 'usuarios';

    protected $fillable = [
        'email',
        'password',
        'nombre',
        'apellido',
        'telefono',
        'cargo',
        'estado',
        'empresa_id',
        'perfil_id',
        'fecha_creacion',
        'fecha_actualizacion',
        'ultimo_acceso',
        'intentos_fallidos',
        'bloqueado_hasta'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'ultimo_acceso' => 'datetime',
        'bloqueado_hasta' => 'datetime',
        'estado' => 'boolean',
        'intentos_fallidos' => 'integer'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class, 'usuario_id');
    }

    /**
     * Hash password before saving
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->estado === true;
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked()
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta->isFuture();
    }

    /**
     * Record login attempt
     */
    public function recordLoginAttempt($successful = true)
    {
        if ($successful) {
            $this->update([
                'intentos_fallidos' => 0,
                'ultimo_acceso' => now(),
                'bloqueado_hasta' => null
            ]);
        } else {
            $intentos = $this->intentos_fallidos + 1;
            $updates = ['intentos_fallidos' => $intentos];
            
            // Block user after 5 failed attempts for 30 minutes
            if ($intentos >= 5) {
                $updates['bloqueado_hasta'] = now()->addMinutes(30);
            }
            
            $this->update($updates);
        }
    }
}
