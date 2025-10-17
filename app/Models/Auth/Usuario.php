<?php

namespace App\Models\Auth;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Usuario extends MongoModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_cuentas';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'cuentas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'empresa_id',
        'perfil_id',
        'estado',
        'ultimo_acceso',
        'intentos_fallidos',
        'bloqueado_hasta',
        'datos_adicionales',
        'modulos_permitidos',
        'permisos_especiales'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ultimo_acceso' => 'datetime',
            'bloqueado_hasta' => 'datetime',
            'datos_adicionales' => 'object',
            'modulos_permitidos' => 'array',
            'permisos_especiales' => 'array',
            'estado' => 'boolean'
        ];
    }

    /**
     * Get the perfil (profile) that the user belongs to.
     */
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    /**
     * Get the empresa that the user belongs to.
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Get the sesiones for the user.
     */
    public function sesiones()
    {
        return $this->hasMany(Sesion::class, 'usuario_id');
    }

    /**
     * Get the notificaciones for the user.
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    /**
     * Check if user has access to a specific module
     */
    public function hasModuleAccess(string $module): bool
    {
        return in_array($module, $this->modulos_permitidos ?? []);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->perfil && $this->perfil->hasPermission($permission)) {
            return true;
        }

        return in_array($permission, $this->permisos_especiales ?? []);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->estado && 
               (!$this->bloqueado_hasta || $this->bloqueado_hasta < now());
    }

    /**
     * Record login attempt
     */
    public function recordLoginAttempt(bool $successful = true): void
    {
        if ($successful) {
            $this->update([
                'ultimo_acceso' => now(),
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null
            ]);
        } else {
            $intentos = $this->intentos_fallidos + 1;
            $bloqueado = null;
            
            if ($intentos >= 5) {
                $bloqueado = now()->addMinutes(30);
            }

            $this->update([
                'intentos_fallidos' => $intentos,
                'bloqueado_hasta' => $bloqueado
            ]);
        }
    }
}
