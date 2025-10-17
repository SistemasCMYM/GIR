<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait para validación de permisos de Super Administrador
 * Proporciona métodos consistentes para verificar acceso de Super Admin
 */
trait SuperAdminAccess
{
    /**
     * Verificar si el usuario actual es Super Administrador
     */
    protected function isSuperAdmin(): bool
    {
        if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
            return false;
        }

        $userData = \App\Http\Controllers\AuthController::user();
        
        if (!$userData) {
            return false;
        }

        return $this->validateSuperAdminRole($userData);
    }

    /**
     * Verificar si un usuario específico es Super Administrador
     */
    protected function isUserSuperAdmin($userData): bool
    {
        if (!$userData) {
            return false;
        }

        return $this->validateSuperAdminRole($userData);
    }

    /**
     * Validar rol de Super Administrador con múltiples criterios
     */
    private function validateSuperAdminRole($userData): bool
    {
        // Tipos válidos de Super Admin
        $validTypes = ['super_admin', 'superadmin', 'root'];
        
        // 1. Verificar por campo 'tipo'
        if (isset($userData->tipo) && in_array(strtolower($userData->tipo), $validTypes)) {
            return true;
        }
        
        // 2. Verificar por campo 'rol'
        if (isset($userData->rol) && in_array(strtolower($userData->rol), $validTypes)) {
            return true;
        }

        // 3. Verificación por email predefinido (configurado en .env)
        $superAdminConfig = config('app.super_admins', []);
        $superAdminEmails = $superAdminConfig['emails'] ?? [];
        
        if (isset($userData->email) && in_array(strtolower($userData->email), array_map('strtolower', $superAdminEmails))) {
            return true;
        }

        // 4. Verificar por campo booleano
        if (isset($userData->is_super_admin) && $userData->is_super_admin === true) {
            return true;
        }

        // 5. Admin sin empresa asignada (caso especial para multi-tenancy)
        if (empty($userData->empresa_id) && isset($userData->rol) && strtolower($userData->rol) === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Verificar acceso y redirigir si no es Super Admin
     */
    protected function requireSuperAdmin()
    {
        if (!$this->isSuperAdmin()) {
            $userData = \App\Http\Controllers\AuthController::user();
            
            Log::warning('Intento de acceso no autorizado a función de Super Admin', [
                'user_email' => $userData->email ?? 'N/A',
                'user_tipo' => $userData->tipo ?? 'N/A',
                'user_rol' => $userData->rol ?? 'N/A',
                'method' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown',
                'class' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown',
                'ip' => request()->ip()
            ]);
            
            return redirect()->route('dashboard')->withErrors([
                'general' => 'No tiene permisos para acceder a esta funcionalidad. Solo Super Administradores.'
            ]);
        }
        
        return null;
    }

    /**
     * Obtener permisos completos para Super Admin
     */
    protected function getSuperAdminPermissions(): array
    {
        return [
            'crear',
            'leer', 
            'actualizar',
            'eliminar',
            'aprobar',
            'reportes',
            'exportar',
            'configurar',
            'administrar',
            'auditoria'
        ];
    }

    /**
     * Verificar si el usuario tiene acceso irrestricto (Super Admin)
     */
    protected function hasUnrestrictedAccess(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Verificar acceso a módulo específico (Super Admin siempre tiene acceso)
     */
    protected function hasModuleAccess(string $moduleName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Para usuarios normales, verificar en canales
        $userData = \App\Http\Controllers\AuthController::user();
        
        if (!$userData || !isset($userData->canales)) {
            return false;
        }

        return isset($userData->canales[$moduleName]) && 
               ($userData->canales[$moduleName]['acceso'] ?? false);
    }

    /**
     * Obtener permisos específicos para un módulo
     */
    protected function getModulePermissions(string $moduleName): array
    {
        if ($this->isSuperAdmin()) {
            return $this->getSuperAdminPermissions();
        }

        $userData = \App\Http\Controllers\AuthController::user();
        
        if (!$userData || !isset($userData->canales[$moduleName])) {
            return [];
        }

        return $userData->canales[$moduleName]['permisos'] ?? [];
    }

    /**
     * Log de acceso de Super Admin para auditoría
     */
    protected function logSuperAdminAccess(string $action, array $context = []): void
    {
        if ($this->isSuperAdmin()) {
            $userData = \App\Http\Controllers\AuthController::user();
            
            Log::info('Acción de Super Admin ejecutada', array_merge([
                'action' => $action,
                'user_email' => $userData->email ?? 'N/A',
                'user_id' => $userData->_id ?? $userData->id ?? 'N/A',
                'ip' => request()->ip(),
                'timestamp' => now()->toISOString()
            ], $context));
        }
    }
}
