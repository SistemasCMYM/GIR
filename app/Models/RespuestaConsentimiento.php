<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Empresas\Empleado;

class RespuestaConsentimiento extends BaseMongoModel
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'respuestas_consentimientos';

    protected $fillable = [
        'consentimiento_id',
        'usuario_id',
        'empleado_id',
        'acepta',
        'firma_digital',
        'firma_imagen',
        'fecha_diligenciamiento',
        'ip_address',
        'user_agent',
        'observaciones',
        'metadata'
    ];

    protected $casts = [
        'acepta' => 'boolean',
        'fecha_diligenciamiento' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el consentimiento
     */
    public function consentimiento()
    {
        return $this->belongsTo(Consentimiento::class, 'consentimiento_id');
    }

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con el empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Scope para respuestas aceptadas
     */
    public function scopeAceptadas($query)
    {
        return $query->where('acepta', true);
    }

    /**
     * Scope para respuestas rechazadas
     */
    public function scopeRechazadas($query)
    {
        return $query->where('acepta', false);
    }

    /**
     * Scope para respuestas firmadas
     */
    public function scopeFirmadas($query)
    {
        return $query->whereNotNull('firma_digital');
    }

    /**
     * Accessor para el estado de aceptación
     */
    public function getEstadoAceptacionAttribute()
    {
        return $this->acepta ? 'Aceptado' : 'Rechazado';
    }

    /**
     * Verificar si tiene firma
     */
    public function tieneFirma()
    {
        return !empty($this->firma_digital) || !empty($this->firma_imagen);
    }

    /**
     * Obtener la URL de la firma si es una imagen
     */
    public function getUrlFirma()
    {
        if ($this->firma_imagen) {
            return asset('storage/firmas/' . $this->firma_imagen);
        }
        return null;
    }
}
