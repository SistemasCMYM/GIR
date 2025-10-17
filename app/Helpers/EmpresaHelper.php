<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;

class EmpresaHelper
{
    /**
     * Obtener el ID de la empresa actual
     */
    public static function getCurrentEmpresaId(): ?string
    {
        try {
            // Intentar desde sesión directa
            $empresaId = session('empresa_id');
            if ($empresaId) {
                return $empresaId;
            }

            // Fallback: desde empresa_data
            $empresaData = session('empresa_data');
            if ($empresaData && isset($empresaData['id'])) {
                return $empresaData['id'];
            }

            // Fallback: desde user_data
            $userData = session('user_data');
            if ($userData && isset($userData['empresa_id'])) {
                return $userData['empresa_id'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error obteniendo empresa_id actual: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener datos completos de la empresa actual
     */
    public static function getCurrentEmpresaData(): ?array
    {
        try {
            return session('empresa_data');
        } catch (\Exception $e) {
            Log::error('Error obteniendo empresa_data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si el usuario actual es SuperAdmin
     */
    public static function isSuperAdmin(): bool
    {
        try {
            $userData = session('user_data');
            return $userData['isSuperAdmin'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Aplicar filtro de empresa a una consulta
     */
    public static function applyEmpresaFilter($query, $empresaField = 'empresa_id')
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            Log::warning('No hay empresa_id disponible para filtrar consulta');
            return $query->whereRaw('1 = 0'); // Retornar consulta vacía
        }

        if (self::isSuperAdmin()) {
            Log::info('SuperAdmin detectado - Sin filtro de empresa aplicado');
            return $query; // SuperAdmin ve todo
        }

        return $query->where($empresaField, $empresaId);
    }

    /**
     * Verificar que un recurso pertenece a la empresa actual
     */
    public static function belongsToCurrentEmpresa($resourceEmpresaId): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        $currentEmpresaId = self::getCurrentEmpresaId();
        return $resourceEmpresaId == $currentEmpresaId;
    }

    /**
     * Obtener todas las empresas accesibles por el usuario actual
     */
    public static function getAccessibleEmpresas(): array
    {
        try {
            if (self::isSuperAdmin()) {
                // SuperAdmin tiene acceso a todas las empresas
                // Aquí podrías hacer una consulta para obtener todas las empresas
                return ['*']; // Indicador de acceso total
            }

            $userData = session('user_data');
            return $userData['empresas_acceso'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error obteniendo empresas accesibles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Validar contexto de empresa en la sesión
     */
    public static function validateEmpresaContext(): bool
    {
        try {
            $empresaId = self::getCurrentEmpresaId();
            $empresaData = self::getCurrentEmpresaData();
            
            if (!$empresaId || !$empresaData) {
                Log::warning('Contexto de empresa inválido', [
                    'empresa_id' => $empresaId,
                    'has_empresa_data' => !empty($empresaData)
                ]);
                return false;
            }

            // Verificar que el usuario tiene acceso a esta empresa
            if (!self::isSuperAdmin()) {
                $empresasAcceso = self::getAccessibleEmpresas();
                if (!in_array($empresaId, $empresasAcceso) && !in_array('*', $empresasAcceso)) {
                    Log::warning('Usuario sin acceso a empresa actual', [
                        'empresa_id' => $empresaId,
                        'empresas_acceso' => $empresasAcceso
                    ]);
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error validando contexto de empresa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpiar contexto de empresa (para logout)
     */
    public static function clearEmpresaContext(): void
    {
        try {
            session()->forget(['empresa_id', 'empresa_data']);
            Log::info('Contexto de empresa limpiado');
        } catch (\Exception $e) {
            Log::error('Error limpiando contexto de empresa: ' . $e->getMessage());
        }
    }

    /**
     * Establecer empresa activa (para cambio de empresa)
     */
    public static function setActiveEmpresa(array $empresaData): bool
    {
        try {
            $empresaId = $empresaData['id'] ?? null;
            
            if (!$empresaId) {
                Log::error('Intento de establecer empresa sin ID');
                return false;
            }

            // Verificar acceso si no es SuperAdmin
            if (!self::isSuperAdmin()) {
                $empresasAcceso = self::getAccessibleEmpresas();
                if (!in_array($empresaId, $empresasAcceso) && !in_array('*', $empresasAcceso)) {
                    Log::warning('Intento de acceso a empresa no autorizada', [
                        'empresa_id' => $empresaId,
                        'empresas_acceso' => $empresasAcceso
                    ]);
                    return false;
                }
            }

            // Establecer en sesión
            session(['empresa_id' => $empresaId, 'empresa_data' => $empresaData]);
            
            // Actualizar también en user_data
            $userData = session('user_data', []);
            $userData['empresa_id'] = $empresaId;
            session(['user_data' => $userData]);

            Log::info('Empresa activa establecida', [
                'empresa_id' => $empresaId,
                'empresa_name' => $empresaData['razon_social'] ?? 'Sin nombre'
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error estableciendo empresa activa: ' . $e->getMessage());
            return false;
        }
    }
}
