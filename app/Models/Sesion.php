<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use App\Traits\GeneratesUniqueId;
use Carbon\Carbon;

class Sesion extends MongoModel
{
    use GeneratesUniqueId;

    protected $connection = 'mongodb_cmym';
    protected $collection = 'sesiones';

    protected $fillable = [
        'id',
        'cuenta_id',
        'empresa_id',
        'token',
        'dispositivo',
        'ip',
        'navegador',
        'fecha_inicio',
        'fecha_ultimo_acceso',
        'activa',
        'datos_sesion'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_ultimo_acceso' => 'datetime',
        'activa' => 'boolean',
        'datos_sesion' => 'array'
    ];

    /**
     * Relación con la cuenta asociada
     */
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }

    /**
     * Obtener el link virtual (igual que Node.js)
     */
    public function getLinkAttribute()
    {
        return "/v2.0/sesiones/{$this->id}";
    }

    /**
     * Crear nueva sesión
     */
    public static function crearSesion($cuentaId, $empresaId, $token, $datosRequest = [])
    {
        return self::create([
            'id' => generateBase64UrlId(),
            'cuenta_id' => $cuentaId,
            'empresa_id' => $empresaId,
            'token' => $token,
            'dispositivo' => $datosRequest['dispositivo'] ?? request()->header('User-Agent'),
            'ip' => $datosRequest['ip'] ?? request()->ip(),
            'navegador' => $datosRequest['navegador'] ?? request()->header('User-Agent'),
            'fecha_inicio' => now(),
            'fecha_ultimo_acceso' => now(),
            'activa' => true,
            'datos_sesion' => $datosRequest['datos_sesion'] ?? []
        ]);
    }

    /**
     * Crear sesión para cuenta (alias del método anterior para compatibilidad)
     */
    public static function createForAccount($cuentaId, $empresaId, $token, $datosRequest = [])
    {
        return self::crearSesion($cuentaId, $empresaId, $token, $datosRequest);
    }

    /**
     * Actualizar último acceso de la sesión
     */
    public function actualizarUltimoAcceso()
    {
        $this->update([
            'fecha_ultimo_acceso' => now()
        ]);
    }

    /**
     * Cerrar sesión (marcar como inactiva)
     */
    public function cerrarSesion()
    {
        $this->update(['activa' => false]);
    }

    /**
     * Verificar si la sesión está activa
     */
    public function estaActiva()
    {
        return $this->activa === true;
    }

    /**
     * Verificar si la sesión ha expirado (más de 24 horas sin actividad)
     */
    public function haExpirado($horasExpiracion = 24)
    {
        if (!$this->fecha_ultimo_acceso) {
            return true;
        }

        return $this->fecha_ultimo_acceso->addHours($horasExpiracion)->isPast();
    }

    /**
     * Scope para sesiones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para sesiones de una cuenta específica
     */
    public function scopeParaCuenta($query, $cuentaId)
    {
        return $query->where('cuenta_id', $cuentaId);
    }

    /**
     * Scope para sesiones de una empresa específica
     */
    public function scopeParaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para sesiones por token
     */
    public function scopePorToken($query, $token)
    {
        return $query->where('token', $token);
    }

    /**
     * Cerrar todas las sesiones de una cuenta
     */
    public static function cerrarSesionesCuenta($cuentaId)
    {
        return self::where('cuenta_id', $cuentaId)
                  ->where('activa', true)
                  ->update(['activa' => false]);
    }

    /**
     * Limpiar sesiones expiradas
     */
    public static function limpiarSesionesExpiradas($horasExpiracion = 24)
    {
        $fechaLimite = now()->subHours($horasExpiracion);
        
        return self::where('fecha_ultimo_acceso', '<', $fechaLimite)
                  ->where('activa', true)
                  ->update(['activa' => false]);
    }

    /**
     * Buscar sesión activa por token
     */
    public static function buscarPorToken($token)
    {
        return self::where('token', $token)
                  ->where('activa', true)
                  ->first();
    }

    /**
     * Obtener información de la sesión para logs
     */
    public function getInfoSesionAttribute()
    {
        return [
            'id' => $this->id,
            'cuenta_id' => $this->cuenta_id,
            'empresa_id' => $this->empresa_id,
            'ip' => $this->ip,
            'dispositivo' => $this->dispositivo,
            'fecha_inicio' => $this->fecha_inicio?->format('Y-m-d H:i:s'),
            'activa' => $this->activa
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = generateBase64UrlId();
            }
            
            // Valores por defecto según el schema de Node.js
            if (!isset($model->activa)) {
                $model->activa = true;
            }
            
            if (!$model->fecha_inicio) {
                $model->fecha_inicio = now();
            }
            
            if (!$model->fecha_ultimo_acceso) {
                $model->fecha_ultimo_acceso = now();
            }
            
            if (!isset($model->datos_sesion)) {
                $model->datos_sesion = [];
            }
        });

        static::updating(function ($model) {
            // Auto-actualizar fecha_ultimo_acceso si no se especifica otra fecha
            if (!$model->isDirty('fecha_ultimo_acceso') && $model->isDirty(['datos_sesion', 'activa'])) {
                $model->fecha_ultimo_acceso = now();
            }
        });
    }
}
