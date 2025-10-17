<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Cuenta extends Model
{
    protected $connection = 'mongodb_cmym';
    protected $collection = 'cuentas';
    
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
        // MongoDB driver already returns arrays for these fields. Avoid using Eloquent 'array' cast
        // which calls json_decode() even when the value is already an array (causa TypeError).
        // We'll provide robust accessors/mutators instead.
    ];

    /**
     * Accessor para empresas: garantiza siempre un array
     */
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

    /**
     * Mutator para empresas: normaliza al guardar
     */
    public function setEmpresasAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['empresas'] = is_array($decoded) ? $decoded : [$value];
            return;
        }
        $this->attributes['empresas'] = is_null($value) ? [] : (array) $value;
    }

    /**
     * Accessor para canales: garantiza siempre un array
     */
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

    /**
     * Mutator para canales: normaliza al guardar
     */
    public function setCanalesAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['canales'] = is_array($decoded) ? $decoded : [$value];
            return;
        }
        $this->attributes['canales'] = is_null($value) ? [] : (array) $value;
    }
    
    public static function generateBase64UrlId($length = 16)
    {
        $bytes = random_bytes($length);
        $base64 = base64_encode($bytes);
        $safe = strtr($base64, '+/', '-_');
        return rtrim($safe, '=');
    }
    
    public function generarHash($pass)
    {
        return Hash::make($pass, ['rounds' => 11]);
    }
    
    public function compararHash($pass, $hash)
    {
        return Hash::check($pass, $hash);
    }
    
    public function perfiles()
    {
        return $this->hasMany(Perfil::class, 'cuenta_id', 'id');
    }
    
    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'cuenta_id', 'id');
    }
    
    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'cuenta_id', 'id');
    }
}
