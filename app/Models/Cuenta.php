<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as MongoModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Traits\GeneratesUniqueId;

class Cuenta extends MongoModel
{
    use GeneratesUniqueId;
    
    protected $connection = 'mongodb_cmym';
    protected $collection = 'cuentas';
    
    // Roles disponibles del sistema (exactos del schema de Node.js)
    public static $roles = [
        'SuperAdmin',
        'administrador',
        'profesional',
        'tecnico',
        'supervisor', 
        'usuario'
    ];
    
    // Estados de cuenta (exactos del schema de Node.js)
    public static $estados = [
        'activa',
        'suspendida', 
        'inactiva'
    ];
    
    // Tipos de cuenta (exactos del schema de Node.js)
    public static $tipos = [
        'interna',
        'cliente',
        'profesional',
        'crm-cliente',
        'usuario'
    ];
    
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
    
    protected $casts = [
        // Avoid casting 'empresas' and 'canales' to array here because the MongoDB driver
        // already returns native arrays and Eloquent's Json cast may call json_decode
        // on an array (causing the TypeError). Use accessors/mutators instead.
        'estado' => 'string',
        'tipo' => 'string',
        'rol' => 'string'
    ];

    // Accessor + mutator for empresas (defensive: accepts array, JSON string or null)
    public function getEmpresasAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '') return [];
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }
        return (array) $value;
    }

    public function setEmpresasAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['empresas'] = is_array($decoded) ? $decoded : [$value];
            return;
        }
        $this->attributes['empresas'] = is_null($value) ? [] : (array) $value;
    }

    // Accessor + mutator for canales (defensive)
    public function getCanalesAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '') return [];
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }
        return (array) $value;
    }

    public function setCanalesAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['canales'] = is_array($decoded) ? $decoded : [$value];
            return;
        }
        $this->attributes['canales'] = is_null($value) ? [] : (array) $value;
    }
    
    protected $hidden = [
        'contrasena'
    ];
    
    /**
     * Generar hash para contraseña con bcrypt usando factor 11 (igual que Node.js)
     */
    public function generarHash($password)
    {
        return bcrypt($password, ['rounds' => 11]);
    }
    
    /**
     * Comparar hash de contraseña - Compatible con algoritmos legacy y Bcrypt
     */
    public function compararHash($password, $hash)
    {
        // Si el hash es vacío, retornar false
        if (empty($hash)) {
            Log::warning('compararHash: Hash vacío recibido');
            return false;
        }
        
        Log::info('compararHash: Comparando password con hash', [
            'hash_length' => strlen($hash),
            'hash_preview' => substr($hash, 0, 10) . '...',
            'hash_format' => substr($hash, 0, 3)
        ]);
        
        // Detectar si es formato Bcrypt ($2a$, $2y$, $2x$)
        if (substr($hash, 0, 3) === '$2a' || substr($hash, 0, 3) === '$2y' || substr($hash, 0, 3) === '$2x') {
            Log::info('compararHash: Formato Bcrypt detectado');
            // Usar password_verify directamente para evitar problemas con configuración de Laravel
            $result = password_verify($password, $hash);
            Log::info('compararHash: Resultado password_verify', ['result' => $result]);
            return $result;
        }
        
        // Compatibilidad con algoritmos legacy del sistema Node.js
        // Verificar MD5 (32 caracteres hexadecimales)
        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            Log::info('compararHash: Formato MD5 detectado');
            return md5($password) === $hash;
        }
        
        // Verificar SHA1 (40 caracteres hexadecimales)
        if (strlen($hash) === 40 && ctype_xdigit($hash)) {
            Log::info('compararHash: Formato SHA1 detectado');
            return sha1($password) === $hash;
        }
        
        // Verificar SHA256 (64 caracteres hexadecimales)
        if (strlen($hash) === 64 && ctype_xdigit($hash)) {
            Log::info('compararHash: Formato SHA256 detectado');
            return hash('sha256', $password) === $hash;
        }
        
        // Intentar verificar como bcrypt por si tiene formato diferente
        try {
            Log::info('compararHash: Intentando como Bcrypt no estándar');
            return Hash::check($password, $hash);
        } catch (\Exception $e) {
            Log::warning('compararHash: Error en verificación Bcrypt: ' . $e->getMessage());
            // Si todo falla, comparar texto plano (solo para desarrollo - INSEGURO)
            Log::info('compararHash: Fallback a texto plano');
            return $password === $hash;
        }
    }
    
    /**
     * Verificar si la contraseña necesita migración a Bcrypt
     */
    public function necesitaMigracionBcrypt($hash)
    {
        // Si no es Bcrypt, necesita migración
        return !(substr($hash, 0, 3) === '$2a' || substr($hash, 0, 3) === '$2y' || substr($hash, 0, 3) === '$2x');
    }
    
    /**
     * Migrar contraseña a Bcrypt después de autenticación exitosa
     */
    public function migrarPasswordBcrypt($cuentaId, $password)
    {
        try {
            $nuevoHash = $this->generarHash($password);
            
            // Actualizar en MongoDB usando el campo correcto
            \DB::connection('mongodb_cmym')
               ->collection('cuentas')
               ->where('id', $cuentaId)
               ->update([
                   'contrasena' => $nuevoHash,
                   'password' => $nuevoHash, // Para compatibilidad
                   'migrated_to_bcrypt' => true,
                   'migration_date' => now()->toISOString()
               ]);
               
            \Log::info("Contraseña migrada a Bcrypt para cuenta: {$cuentaId}");
            return true;
        } catch (\Exception $e) {
            \Log::error("Error migrando contraseña a Bcrypt para cuenta {$cuentaId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Relación virtual con permisos
     */
    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'cuenta_id', 'id');
    }
    
    /**
     * Relación virtual con perfil (uno a uno)
     */
    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'cuenta_id', 'id');
    }
    
    /**
     * Obtener el link virtual (igual que Node.js)
     */
    public function getLinkAttribute()
    {
        return "/v2.0/cuentas/{$this->id}";
    }
    
    /**
     * Verificar si la cuenta tiene un rol específico
     */
    public function tieneRol($rol)
    {
        return $this->rol === $rol;
    }
    
    /**
     * Verificar si la cuenta es SuperAdmin
     */
    public function esSuperAdmin()
    {
        return $this->rol === 'SuperAdmin' && $this->tipo === 'interna';
    }
    
    /**
     * Verificar si la cuenta tiene acceso a una empresa específica
     */
    public function tieneAccesoEmpresa($empresaId)
    {
        return $this->esSuperAdmin() || in_array($empresaId, $this->empresas ?? []);
    }
    
    /**
     * Scope para filtrar por empresa
     */
    public function scopeParaEmpresa($query, $empresaId)
    {
        return $query->where('empresas', $empresaId);
    }
    
    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
    
    /**
     * Scope para filtrar por rol
     */
    public function scopePorRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }
    
    /**
     * Scope para cuentas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = generateBase64UrlId();
            }
            
            // Estado por defecto según el schema de Node.js
            if (!isset($model->estado)) {
                $model->estado = 'inactiva';
            }
            
            // Tipo por defecto según el schema de Node.js
            if (!isset($model->tipo)) {
                $model->tipo = 'cliente';
            }
            
            // Rol por defecto según el schema de Node.js
            if (!isset($model->rol)) {
                $model->rol = 'usuario';
            }
        });
    }
}