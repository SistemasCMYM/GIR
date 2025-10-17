<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToEmpresa
{
    /**
     * Hook para aplicar el scope de empresa automáticamente
     */
    protected static function bootBelongsToEmpresa()
    {
        // Aplicar scope global para filtrar por empresa del usuario autenticado
        static::addGlobalScope('empresa', function (Builder $builder) {
            $empresaData = self::getEmpresaFromSession();
            $userData = self::getUserFromSession();
            
            // Solo aplicar filtro si no es super admin y hay empresa en sesión
            if ($empresaData && isset($empresaData->id)) {
                $isSuperAdmin = isset($userData->isSuperAdmin) && $userData->isSuperAdmin;
                if (!$isSuperAdmin) {
                    $builder->where('empresa_id', $empresaData->id);
                }
            }
        });

        // Asignar empresa automáticamente al crear
        static::creating(function ($model) {
            $empresaData = self::getEmpresaFromSession();
            if ($empresaData && isset($empresaData->id) && !$model->empresa_id) {
                $model->empresa_id = $empresaData->id;
            }
        });
    }

    /**
     * Obtener datos de empresa desde la sesión
     */
    private static function getEmpresaFromSession()
    {
        try {
            return \App\Http\Controllers\AuthController::empresa();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtener datos de usuario desde la sesión
     */
    private static function getUserFromSession()
    {
        try {
            return \App\Http\Controllers\AuthController::user();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Relación con la empresa
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    /**
     * Scope para filtrar por empresa específica (solo para super admin)
     */
    public function scopeForEmpresa(Builder $query, $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope para obtener datos de todas las empresas (solo super admin)
     */
    public function scopeAllEmpresas(Builder $query)
    {
        return $query->withoutGlobalScope('empresa');
    }

    /**
     * Verificar si el usuario actual puede acceder a este registro
     */
    public function canAccess()
    {
        try {
            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            if (!$userData || !$empresaData) {
                return false;
            }

            // Super admin puede acceder a todo
            if (isset($userData->isSuperAdmin) && $userData->isSuperAdmin) {
                return true;
            }

            // Usuario normal solo puede acceder a datos de su empresa
            return $this->empresa_id === $empresaData->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar si el modelo pertenece a la empresa actual del usuario
     */
    public function belongsToCurrentEmpresa()
    {
        try {
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            return $empresaData && $this->empresa_id === $empresaData->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Scope para verificar acceso basado en empresa (útil en consultas)
     */
    public function scopeAccessibleToCurrentUser(Builder $query)
    {
        try {
            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();
            
            // Si no hay datos de sesión, no mostrar nada
            if (!$userData || !$empresaData) {
                return $query->whereRaw('1 = 0'); // Consulta que no retorna resultados
            }

            // Super admin ve todo
            if (isset($userData->isSuperAdmin) && $userData->isSuperAdmin) {
                return $query->withoutGlobalScope('empresa');
            }

            // Usuario normal ve solo de su empresa
            return $query->where('empresa_id', $empresaData->id);
        } catch (\Exception $e) {
            return $query->whereRaw('1 = 0'); // En caso de error, no mostrar nada
        }
    }
}
