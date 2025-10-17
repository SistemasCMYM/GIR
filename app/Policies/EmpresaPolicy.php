<?php

namespace App\Policies;

use App\Models\User;
use App\Helpers\EmpresaHelper;
use Illuminate\Support\Facades\Log;

class EmpresaPolicy
{
    /**
     * Verificar si el usuario puede ver recursos de la empresa
     */
    public function viewAny(User $user): bool
    {
        try {
            // SuperAdmin puede ver todo
            if (EmpresaHelper::isSuperAdmin()) {
                Log::info('Acceso SuperAdmin autorizado para viewAny');
                return true;
            }

            // Usuario regular debe tener empresa válida
            $empresaId = EmpresaHelper::getCurrentEmpresaId();
            $valid = EmpresaHelper::validateEmpresaContext();
            
            Log::info('Validación de acceso viewAny', [
                'user_id' => $user->id ?? 'N/A',
                'empresa_id' => $empresaId,
                'context_valid' => $valid
            ]);

            return $valid;
        } catch (\Exception $e) {
            Log::error('Error en EmpresaPolicy::viewAny: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede ver un recurso específico
     */
    public function view(User $user, $model): bool
    {
        try {
            // SuperAdmin puede ver todo
            if (EmpresaHelper::isSuperAdmin()) {
                return true;
            }

            // Verificar que el recurso pertenece a la empresa actual
            $resourceEmpresaId = null;
            
            if (is_object($model) && property_exists($model, 'empresa_id')) {
                $resourceEmpresaId = $model->empresa_id;
            } elseif (is_array($model) && isset($model['empresa_id'])) {
                $resourceEmpresaId = $model['empresa_id'];
            }

            if (!$resourceEmpresaId) {
                Log::warning('Recurso sin empresa_id encontrado', [
                    'model_type' => is_object($model) ? get_class($model) : gettype($model),
                    'model_id' => is_object($model) && property_exists($model, 'id') ? $model->id : 'N/A'
                ]);
                return false;
            }

            $belongs = EmpresaHelper::belongsToCurrentEmpresa($resourceEmpresaId);
            
            Log::info('Validación de acceso view', [
                'user_id' => $user->id ?? 'N/A',
                'resource_empresa_id' => $resourceEmpresaId,
                'current_empresa_id' => EmpresaHelper::getCurrentEmpresaId(),
                'belongs' => $belongs
            ]);

            return $belongs;
        } catch (\Exception $e) {
            Log::error('Error en EmpresaPolicy::view: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede crear recursos
     */
    public function create(User $user): bool
    {
        try {
            // SuperAdmin puede crear en cualquier empresa
            if (EmpresaHelper::isSuperAdmin()) {
                return true;
            }

            // Usuario regular debe tener contexto de empresa válido
            $valid = EmpresaHelper::validateEmpresaContext();
            
            Log::info('Validación de acceso create', [
                'user_id' => $user->id ?? 'N/A',
                'empresa_id' => EmpresaHelper::getCurrentEmpresaId(),
                'context_valid' => $valid
            ]);

            return $valid;
        } catch (\Exception $e) {
            Log::error('Error en EmpresaPolicy::create: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede actualizar un recurso
     */
    public function update(User $user, $model): bool
    {
        return $this->view($user, $model);
    }

    /**
     * Verificar si el usuario puede eliminar un recurso
     */
    public function delete(User $user, $model): bool
    {
        return $this->view($user, $model);
    }

    /**
     * Verificar si el usuario puede restaurar un recurso
     */
    public function restore(User $user, $model): bool
    {
        return $this->view($user, $model);
    }

    /**
     * Verificar si el usuario puede eliminar permanentemente un recurso
     */
    public function forceDelete(User $user, $model): bool
    {
        // Solo SuperAdmin puede eliminar permanentemente
        return EmpresaHelper::isSuperAdmin();
    }
}
