<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;

trait HasEmpresaScope
{
    /**
     * Boot the trait
     */
    protected static function bootHasEmpresaScope()
    {
        // Aplicar scope automáticamente en todas las consultas
        static::addGlobalScope('empresa', function (Builder $builder) {
            $empresaId = self::getCurrentEmpresaId();
            
            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            if ($empresaId) {
                $builder->where('empresa_id', $empresaId);
                Log::debug('HasEmpresaScope: Aplicando filtro empresa_id', [
                    'model' => static::class,
                    'empresa_id' => $empresaId
                ]);
            }
        });

        // Aplicar empresa_id automáticamente al crear registros
        static::creating(function ($model) {
            if (!isset($model->empresa_id) || empty($model->empresa_id)) {
                $empresaId = self::getCurrentEmpresaId();
                if ($empresaId) {
                    $model->empresa_id = $empresaId;
                }
            }
        });
    }

    /**
     * Scope para consultar solo registros de la empresa actual
     */
    public function scopeForCurrentEmpresa(Builder $query)
    {
        $empresaId = self::getCurrentEmpresaId();
        
        // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
        if ($empresaId) {
            return $query->where('empresa_id', $empresaId);
        }
        
        return $query;
    }

    /**
     * Scope para consultar registros de una empresa específica
     */
    public function scopeForEmpresa(Builder $query, $empresaId)
    {
        // SEGURIDAD: Verificar que el usuario tiene acceso a esta empresa
        $currentEmpresaId = self::getCurrentEmpresaId();
        if ($empresaId != $currentEmpresaId) {
            Log::warning('Intento de acceso a empresa no autorizada', [
                'user_empresa_id' => $currentEmpresaId,
                'requested_empresa_id' => $empresaId,
                'user_email' => session('user_data')['email'] ?? 'unknown'
            ]);
            // Retornar consulta vacía
            return $query->whereRaw('1 = 0');
        }
        
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Verificar que el registro pertenece a la empresa actual
     */
    public function belongsToCurrentEmpresa(): bool
    {
        $currentEmpresaId = self::getCurrentEmpresaId();
        return $this->empresa_id == $currentEmpresaId;
    }

    /**
     * Obtener el ID de la empresa actual
     */
    protected static function getCurrentEmpresaId(): ?string
    {
        try {
            $empresaData = session('empresa_data');
            return $empresaData['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('Error obteniendo empresa_id de sesión: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si el usuario actual es SuperAdmin
     */
    protected static function isSuperAdmin(): bool
    {
        try {
            $userData = session('user_data');
            return $userData['isSuperAdmin'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener datos de la empresa actual
     */
    protected static function getCurrentEmpresaData(): ?array
    {
        try {
            return session('empresa_data');
        } catch (\Exception $e) {
            Log::error('Error obteniendo empresa_data de sesión: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validar acceso a recurso por empresa
     */
    public function checkEmpresaAccess(): bool
    {
        if (!$this->belongsToCurrentEmpresa()) {
            Log::warning('Acceso denegado a recurso de otra empresa', [
                'resource_empresa_id' => $this->empresa_id,
                'user_empresa_id' => self::getCurrentEmpresaId(),
                'user_email' => session('user_data')['email'] ?? 'unknown',
                'model' => get_class($this),
                'resource_id' => $this->id ?? 'unknown'
            ]);
            return false;
        }

        return true;
    }
}
