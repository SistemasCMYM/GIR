<?php

namespace App\Models\MongoDB;

use MongoDB\Laravel\Eloquent\Model;

class Permiso extends Model
{
    protected $connection = 'mongodb_cmym';
    protected $collection = 'permisos';
    
    protected $fillable = [
        'id',
        'cuenta_id',
        'modulo',
        'tipo',
        'acciones',
        'link'
    ];
    
    // Avoid Json cast because MongoDB driver may already return arrays.
    // Provide safe accessors below instead.
    protected $casts = [
        // left intentionally empty
    ];
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'id');
    }

    /**
     * Safe accessor for acciones: tolerate arrays or JSON strings.
     */
    public function getAccionesAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Safe accessor for modulo: tolerate arrays or JSON strings.
     */
    public function getModuloAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
