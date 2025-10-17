<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EvaluacionPsicosocial;
use App\Helpers\EmpresaHelper;
use Illuminate\Support\Facades\Log;

class EvaluacionPsicosocialPolicy
{
    /**
     * Verificar si el usuario puede ver todas las evaluaciones
     */
    public function viewAny(User $user): bool
    {
        try {
            // SuperAdmin puede ver todo
            if (EmpresaHelper::isSuperAdmin()) {
                Log::info('SuperAdmin acceso autorizado para viewAny evaluaciones');
                return true;
            }

            // Usuario regular debe tener empresa válida
            $valid = EmpresaHelper::validateEmpresaContext();
            
            Log::info('Validación acceso viewAny evaluaciones psicosociales', [
                'user_id' => $user->id ?? 'N/A',
                'empresa_id' => EmpresaHelper::getCurrentEmpresaId(),
                'context_valid' => $valid
            ]);

            return $valid;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::viewAny: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede ver una evaluación específica
     */
    public function view(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        try {
            // SuperAdmin puede ver todo
            if (EmpresaHelper::isSuperAdmin()) {
                return true;
            }

            // Verificar que la evaluación pertenece a la empresa actual
            $belongs = EmpresaHelper::belongsToCurrentEmpresa($evaluacion->empresa_id);
            
            Log::info('Validación acceso view evaluación psicosocial', [
                'user_id' => $user->id ?? 'N/A',
                'evaluacion_id' => $evaluacion->id,
                'evaluacion_empresa_id' => $evaluacion->empresa_id,
                'current_empresa_id' => EmpresaHelper::getCurrentEmpresaId(),
                'belongs' => $belongs
            ]);

            return $belongs;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::view: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede crear evaluaciones
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
            
            Log::info('Validación acceso create evaluación psicosocial', [
                'user_id' => $user->id ?? 'N/A',
                'empresa_id' => EmpresaHelper::getCurrentEmpresaId(),
                'context_valid' => $valid
            ]);

            return $valid;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::create: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede actualizar una evaluación
     */
    public function update(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        try {
            // Verificar acceso básico
            if (!$this->view($user, $evaluacion)) {
                return false;
            }

            // Restricciones adicionales: no se puede modificar evaluaciones validadas
            if ($evaluacion->validada && !EmpresaHelper::isSuperAdmin()) {
                Log::warning('Intento de modificar evaluación validada', [
                    'user_id' => $user->id ?? 'N/A',
                    'evaluacion_id' => $evaluacion->id,
                    'is_superadmin' => EmpresaHelper::isSuperAdmin()
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::update: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede eliminar una evaluación
     */
    public function delete(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        try {
            // SuperAdmin puede eliminar cualquier evaluación
            if (EmpresaHelper::isSuperAdmin()) {
                return true;
            }

            // Verificar acceso básico
            if (!$this->view($user, $evaluacion)) {
                return false;
            }

            // No se pueden eliminar evaluaciones finalizadas o validadas
            if (in_array($evaluacion->estado, ['finalizada', 'validada'])) {
                Log::warning('Intento de eliminar evaluación finalizada/validada', [
                    'user_id' => $user->id ?? 'N/A',
                    'evaluacion_id' => $evaluacion->id,
                    'estado' => $evaluacion->estado
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::delete: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede validar una evaluación
     */
    public function validate(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        try {
            // Verificar acceso básico
            if (!$this->view($user, $evaluacion)) {
                return false;
            }

            // Solo usuarios con permisos especiales pueden validar
            // Aquí puedes agregar lógica específica según roles/permisos
            $userData = session('user_data');
            $canValidate = $userData['permisos']['validar_evaluaciones'] ?? false;

            Log::info('Validación acceso validate evaluación', [
                'user_id' => $user->id ?? 'N/A',
                'evaluacion_id' => $evaluacion->id,
                'can_validate' => $canValidate,
                'is_superadmin' => EmpresaHelper::isSuperAdmin()
            ]);

            return EmpresaHelper::isSuperAdmin() || $canValidate;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::validate: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede generar reportes de una evaluación
     */
    public function generateReport(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        try {
            // Verificar acceso básico
            if (!$this->view($user, $evaluacion)) {
                return false;
            }

            // Solo evaluaciones finalizadas pueden generar reportes
            if ($evaluacion->estado !== 'finalizada' && $evaluacion->estado !== 'validada') {
                Log::warning('Intento de generar reporte de evaluación no finalizada', [
                    'user_id' => $user->id ?? 'N/A',
                    'evaluacion_id' => $evaluacion->id,
                    'estado' => $evaluacion->estado
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error en EvaluacionPsicosocialPolicy::generateReport: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario puede restaurar una evaluación
     */
    public function restore(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        // Solo SuperAdmin puede restaurar
        return EmpresaHelper::isSuperAdmin();
    }

    /**
     * Verificar si el usuario puede eliminar permanentemente una evaluación
     */
    public function forceDelete(User $user, EvaluacionPsicosocial $evaluacion): bool
    {
        // Solo SuperAdmin puede eliminar permanentemente
        return EmpresaHelper::isSuperAdmin();
    }
}
