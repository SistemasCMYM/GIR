<?php

namespace App\Models\Auth;

use App\Models\BaseMongoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Modelo de Notificación siguiendo exactamente el NotificacionSchema de Node.js
 * 
 * Estructura compatible con:
 * - Base de datos: cmym
 * - Colección: notificaciones
 * - Schema Node.js: NotificacionSchema con virtuals y Common
 */
class Notificacion extends BaseMongoModel
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
    protected $collection = 'notificaciones';

    /**
     * Campos que se pueden asignar masivamente (siguiendo NotificacionSchema)
     * 
     * @var array
     */
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

    /**
     * Campos que deben ser convertidos a tipos específicos
     * 
     * @var array
     */
    protected $casts = [
        'vista' => 'boolean',
        'canales' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Configuración de timestamps
     * 
     * @var bool
     */
    public $timestamps = true;

    /**
     * Módulos válidos para notificaciones (enum del schema)
     * 
     * @var array
     */
    public static $modulos = [
        'hallazgos',
        'psicosocial', 
        'plan-trabajo'
    ];

    /**
     * Boot del modelo con validaciones y configuraciones
     */
    protected static function boot()
    {
        parent::boot();

        // Validaciones al crear
        static::creating(function ($notificacion) {
            // Generar ID único si no existe
            if (empty($notificacion->id)) {
                $notificacion->id = generateBase64UrlId(16);
            }

            // Validar módulo si está presente
            if (!empty($notificacion->modulo) && !in_array($notificacion->modulo, self::$modulos)) {
                throw new \InvalidArgumentException('Módulo no válido. Debe ser uno de: ' . implode(', ', self::$modulos));
            }

            // Configurar valores por defecto
            if (!isset($notificacion->vista)) {
                $notificacion->vista = false;
            }

            if (empty($notificacion->modulo)) {
                $notificacion->modulo = 'plan-trabajo';
            }

            if (!isset($notificacion->canales)) {
                $notificacion->canales = [];
            }

            Log::info('Creando notificación', [
                'id' => $notificacion->id,
                'empresa_id' => $notificacion->empresa_id,
                'modulo' => $notificacion->modulo,
                'titulo' => $notificacion->titulo
            ]);
        });

        // Validaciones al actualizar
        static::updating(function ($notificacion) {
            // Validar módulo si está siendo actualizado
            if ($notificacion->isDirty('modulo') && !empty($notificacion->modulo)) {
                if (!in_array($notificacion->modulo, self::$modulos)) {
                    throw new \InvalidArgumentException('Módulo no válido. Debe ser uno de: ' . implode(', ', self::$modulos));
                }
            }

            Log::info('Actualizando notificación', [
                'id' => $notificacion->id,
                'empresa_id' => $notificacion->empresa_id,
                'vista' => $notificacion->vista
            ]);
        });
    }

    /**
     * Relación con Empresa (mediante empresa_id)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id', '_id');
    }

    /**
     * Virtual Link (siguiendo el schema de Node.js)
     * 
     * @return string
     */
    public function getLinkAttribute()
    {
        if (!empty($this->attributes['link'])) {
            return $this->attributes['link'];
        }
        
        return "/v2.0/notificaciones/{$this->id}";
    }

    /**
     * Accessor para el atributo virtual $link
     * 
     * @return string
     */
    public function getDollarLinkAttribute()
    {
        return $this->getLinkAttribute();
    }

    /**
     * Marcar notificación como vista
     * 
     * @return bool
     */
    public function marcarComoVista()
    {
        try {
            $resultado = $this->update(['vista' => true]);

            Log::info('Notificación marcada como vista', [
                'id' => $this->id,
                'empresa_id' => $this->empresa_id
            ]);

            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al marcar notificación como vista', [
                'id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Marcar notificación como no vista
     * 
     * @return bool
     */
    public function marcarComoNoVista()
    {
        try {
            $resultado = $this->update(['vista' => false]);

            Log::info('Notificación marcada como no vista', [
                'id' => $this->id,
                'empresa_id' => $this->empresa_id
            ]);

            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al marcar notificación como no vista', [
                'id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si la notificación ha sido vista
     * 
     * @return bool
     */
    public function estaVista()
    {
        return (bool) $this->vista;
    }

    /**
     * Agregar canal de notificación
     * 
     * @param string $canal
     * @return bool
     */
    public function agregarCanal($canal)
    {
        if (!in_array($canal, $this->canales)) {
            $canales = $this->canales;
            $canales[] = $canal;
            return $this->update(['canales' => $canales]);
        }
        return true;
    }

    /**
     * Remover canal de notificación
     * 
     * @param string $canal
     * @return bool
     */
    public function removerCanal($canal)
    {
        $canales = array_filter($this->canales, function($c) use ($canal) {
            return $c !== $canal;
        });
        return $this->update(['canales' => array_values($canales)]);
    }

    /**
     * Scope para notificaciones vistas
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVistas($query)
    {
        return $query->where('vista', true);
    }

    /**
     * Scope para notificaciones no vistas
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoVistas($query)
    {
        return $query->where('vista', false);
    }

    /**
     * Scope para notificaciones por empresa
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $empresa_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
    }

    /**
     * Scope para notificaciones por módulo
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $modulo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para notificaciones con canales específicos
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $canal
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConCanal($query, $canal)
    {
        return $query->where('canales', $canal);
    }

    /**
     * Obtener información de la notificación formateada
     * 
     * @return array
     */
    public function getInfoNotificacion()
    {
        return [
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'modulo' => $this->modulo,
            'vista' => $this->vista,
            'link' => $this->getLinkAttribute(),
            'canales' => $this->canales,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Obtener módulos válidos
     * 
     * @return array
     */
    public static function getModulosValidos()
    {
        return self::$modulos;
    }

    /**
     * Validar módulo
     * 
     * @param string $modulo
     * @return bool
     */
    public static function esModuloValido($modulo)
    {
        return in_array($modulo, self::$modulos);
    }

    /**
     * Crear notificación de modificación no autorizada
     * 
     * @param array $datos
     * @return self
     */
    public static function crearModificacionNoAutorizada($datos)
    {
        return self::create([
            'empresa_id' => $datos['empresa_id'],
            'titulo' => $datos['titulo'] ?? 'Modificación no autorizada detectada',
            'descripcion' => $datos['descripcion'] ?? 'Se detectó una modificación no autorizada en el sistema',
            'modulo' => $datos['modulo'] ?? 'plan-trabajo',
            'canales' => $datos['canales'] ?? ['sistema', 'email'],
            'vista' => false
        ]);
    }
}
