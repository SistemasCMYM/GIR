<?php

namespace App\Models\Auth;

use App\Models\BaseMongoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Modelo de Sesión siguiendo exactamente el SesionSchema de Node.js
 * 
 * Estructura compatible con:
 * - Base de datos: cmym
 * - Colección: sesiones
 * - Schema Node.js: SesionSchema con timestamps personalizados
 */
class Sesion extends BaseMongoModel
{
    /**
     * Conexión a la base de datos MongoDB cmym
     * 
     * @var string
     */
    protected $connection = 'mongodb_cmym';

    /**
     * Colección de MongoDB
     * 
     * @var string
     */
    protected $collection = 'sesiones';

    /**
     * Campos que se pueden asignar masivamente (siguiendo SesionSchema)
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'usuario_id',
        'token_sesion', 
        'inicio_sesion',
        'ultima_actividad',
        'fin_sesion',
        'ip_origen',
        'user_agent',
        'activa'
    ];

    /**
     * Campos que deben ser convertidos a tipos específicos
     * 
     * @var array
     */
    protected $casts = [
        'inicio_sesion' => 'datetime',
        'ultima_actividad' => 'datetime', 
        'fin_sesion' => 'datetime',
        'activa' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Configurar timestamps personalizados como en Node.js
     * 
     * @var array
     */
    protected $dates = [
        'inicio_sesion',
        'ultima_actividad',
        'fin_sesion',
        'created_at',
        'updated_at'
    ];

    /**
     * Configuración de timestamps personalizada
     * 
     * @var bool
     */
    public $timestamps = true;

    /**
     * Nombres personalizados de timestamps (como en Node.js)
     */
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Boot del modelo con validaciones y configuraciones
     */
    protected static function boot()
    {
        parent::boot();

        // Validaciones al crear
        static::creating(function ($sesion) {
            // Generar ID único si no existe
            if (empty($sesion->id)) {
                $sesion->id = generateBase64UrlId(16);
            }

            // Validar campos requeridos
            if (empty($sesion->usuario_id)) {
                throw new \InvalidArgumentException('usuario_id es requerido');
            }

            if (empty($sesion->token_sesion)) {
                throw new \InvalidArgumentException('token_sesion es requerido');
            }

            // Configurar fechas por defecto
            if (empty($sesion->inicio_sesion)) {
                $sesion->inicio_sesion = now();
            }

            if (empty($sesion->ultima_actividad)) {
                $sesion->ultima_actividad = now();
            }

            // Configurar estado por defecto
            if (!isset($sesion->activa)) {
                $sesion->activa = true;
            }

            Log::info('Creando sesión', [
                'id' => $sesion->id,
                'usuario_id' => $sesion->usuario_id,
                'token_sesion' => substr($sesion->token_sesion, 0, 10) . '...'
            ]);
        });

        // Validaciones al actualizar
        static::updating(function ($sesion) {
            // Actualizar última actividad automáticamente
            if ($sesion->activa && !$sesion->isDirty('ultima_actividad')) {
                $sesion->ultima_actividad = now();
            }

            Log::info('Actualizando sesión', [
                'id' => $sesion->id,
                'usuario_id' => $sesion->usuario_id,
                'activa' => $sesion->activa
            ]);
        });
    }

    /**
     * Relación con Usuario (referencia como en Node.js)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', '_id');
    }

    /**
     * Cerrar sesión (establecer fin_sesion y desactivar)
     * 
     * @return bool
     */
    public function cerrarSesion()
    {
        try {
            $resultado = $this->update([
                'fin_sesion' => now(),
                'activa' => false,
                'ultima_actividad' => now()
            ]);

            Log::info('Sesión cerrada', [
                'id' => $this->id,
                'usuario_id' => $this->usuario_id,
                'fin_sesion' => $this->fin_sesion
            ]);

            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al cerrar sesión', [
                'id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si la sesión está expirada
     * 
     * @param int $horas_inactividad Horas de inactividad antes de expirar (default: 2)
     * @return bool
     */
    public function estaExpirada($horas_inactividad = 2)
    {
        // Si ya está marcada como inactiva
        if (!$this->activa) {
            return true;
        }

        // Si tiene fin_sesion definido
        if ($this->fin_sesion) {
            return true;
        }

        // Verificar inactividad
        return $this->ultima_actividad < now()->subHours($horas_inactividad);
    }

    /**
     * Actualizar última actividad
     * 
     * @return bool
     */
    public function actualizarActividad()
    {
        if (!$this->activa) {
            return false;
        }

        return $this->update(['ultima_actividad' => now()]);
    }

    /**
     * Verificar si el token es válido
     * 
     * @param string $token
     * @return bool
     */
    public function verificarToken($token)
    {
        return $this->token_sesion === $token && $this->activa && !$this->estaExpirada();
    }

    /**
     * Scope para sesiones activas
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true)->whereNull('fin_sesion');
    }

    /**
     * Scope para sesiones por usuario
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $usuario_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorUsuario($query, $usuario_id)
    {
        return $query->where('usuario_id', $usuario_id);
    }

    /**
     * Scope para sesiones expiradas
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $horas_inactividad
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpiradas($query, $horas_inactividad = 2)
    {
        return $query->where(function ($q) use ($horas_inactividad) {
            $q->where('activa', false)
              ->orWhereNotNull('fin_sesion')
              ->orWhere('ultima_actividad', '<', now()->subHours($horas_inactividad));
        });
    }

    /**
     * Obtener información de la sesión formateada
     * 
     * @return array
     */
    public function getInfoSesion()
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'inicio_sesion' => $this->inicio_sesion?->format('Y-m-d H:i:s'),
            'ultima_actividad' => $this->ultima_actividad?->format('Y-m-d H:i:s'),
            'fin_sesion' => $this->fin_sesion?->format('Y-m-d H:i:s'),
            'duracion_minutos' => $this->fin_sesion 
                ? $this->inicio_sesion->diffInMinutes($this->fin_sesion)
                : $this->inicio_sesion->diffInMinutes(now()),
            'activa' => $this->activa,
            'expirada' => $this->estaExpirada(),
            'ip_origen' => $this->ip_origen,
            'user_agent' => $this->user_agent
        ];
    }

    /**
     * Crear una nueva sesión para una cuenta específica (método estático para compatibilidad)
     * 
     * @param string $cuentaId ID de la cuenta
     * @param string $empresaId ID de la empresa 
     * @param string $token Token de sesión
     * @param array $datosRequest Datos adicionales de la request
     * @return self
     */
    public static function createForAccount($cuentaId, $empresaId, $token, $datosRequest = [])
    {
        try {
            $sesion = new self();
            $sesion->id = generateBase64UrlId(16);
            $sesion->usuario_id = $cuentaId;
            $sesion->token_sesion = $token;
            $sesion->inicio_sesion = now();
            $sesion->ultima_actividad = now();
            $sesion->ip_origen = $datosRequest['ip'] ?? request()->ip();
            $sesion->user_agent = $datosRequest['dispositivo'] ?? $datosRequest['navegador'] ?? request()->userAgent();
            $sesion->activa = true;
            
            $sesion->save();

            Log::info('Sesión creada exitosamente', [
                'id' => $sesion->id,
                'usuario_id' => $cuentaId,
                'empresa_id' => $empresaId,
                'token_preview' => substr($token, 0, 10) . '...'
            ]);

            return $sesion;
        } catch (\Exception $e) {
            Log::error('Error creando sesión para cuenta', [
                'cuenta_id' => $cuentaId,
                'empresa_id' => $empresaId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cerrar todas las sesiones activas de una cuenta (método estático para compatibilidad)
     * 
     * @param string $cuentaId ID de la cuenta
     * @return int Número de sesiones cerradas
     */
    public static function cerrarSesionesCuenta($cuentaId)
    {
        try {
            $sesionesActivas = self::where('usuario_id', $cuentaId)
                                  ->where('activa', true)
                                  ->whereNull('fin_sesion')
                                  ->get();

            $count = 0;
            foreach ($sesionesActivas as $sesion) {
                if ($sesion->cerrarSesion()) {
                    $count++;
                }
            }

            Log::info('Sesiones cerradas para cuenta', [
                'cuenta_id' => $cuentaId,
                'sesiones_cerradas' => $count
            ]);

            return $count;
        } catch (\Exception $e) {
            Log::error('Error cerrando sesiones de cuenta', [
                'cuenta_id' => $cuentaId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Buscar sesión activa por cuenta
     * 
     * @param string $cuentaId
     * @return self|null
     */
    public static function findActiveByAccount($cuentaId)
    {
        return self::where('usuario_id', $cuentaId)
                   ->activas()
                   ->orderBy('ultima_actividad', 'desc')
                   ->first();
    }

    /**
     * Buscar sesión activa por token
     * 
     * @param string $token
     * @return self|null
     */
    public static function findActiveByToken($token)
    {
        return self::where('token_sesion', $token)
                   ->activas()
                   ->first();
    }

    /**
     * Cerrar todas las sesiones de una cuenta (alias para compatibilidad)
     * 
     * @param string $cuentaId
     * @return int
     */
    public static function closeAllForAccount($cuentaId)
    {
        return self::cerrarSesionesCuenta($cuentaId);
    }

    /**
     * Verificar si la sesión está activa (método de instancia)
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->activa && !$this->estaExpirada();
    }

    /**
     * Actualizar actividad de la sesión (método de instancia)
     * 
     * @return bool
     */
    public function updateActivity()
    {
        return $this->actualizarActividad();
    }

    /**
     * Obtener la cuenta asociada (relación virtual)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuenta()
    {
        return $this->belongsTo(\App\Models\Cuenta::class, 'usuario_id', 'id');
    }

    /**
     * Obtener virtual $link para compatibilidad con Node.js
     * 
     * @return string
     */
    public function getLinkAttribute()
    {
        return "/admin/sesiones/{$this->id}";
    }

    /**
     * Scope para sesiones de hoy
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('inicio_sesion', today());
    }

    /**
     * Scope para sesiones por rango de fechas
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorRango($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('inicio_sesion', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para sesiones por IP
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $ip
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorIp($query, $ip)
    {
        return $query->where('ip_origen', $ip);
    }
}
