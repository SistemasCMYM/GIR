<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/**
 * Trait para manejar permisos basados en rol
 */
trait RoleBasedAccess
{
    /**
     * Verificar si el usuario tiene un rol específico
     */
    protected function hasRole($role)
    {
        if (!Session::has('user_data')) {
            return false;
        }
        
        $userData = Session::get('user_data');
        $userRole = $userData['rol'] ?? 'usuario';
        
        if (is_array($role)) {
            return in_array($userRole, $role);
        }
        
        return $userRole === $role;
    }
    
    /**
     * Verificar si el usuario es Super Administrador
     */
    protected function isSuperAdmin()
    {
        if (!Session::has('user_data')) {
            return false;
        }
        
        $userData = Session::get('user_data');
        
        // Verificar primero la bandera isSuperAdmin
        if (isset($userData['isSuperAdmin']) && $userData['isSuperAdmin'] === true) {
            return true;
        }
        
        // Verificar el rol
        $superAdminRoles = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin'];
        return isset($userData['rol']) && in_array($userData['rol'], $superAdminRoles);
    }
    
    /**
     * Verificar si el usuario es Administrador de Empresa
     */
    protected function isEmpresaAdmin()
    {
        if (!Session::has('user_data')) {
            return false;
        }
        
        $userData = Session::get('user_data');
        $adminRoles = ['administrador_empresa', 'admin_empresa', 'admin'];
        
        return isset($userData['rol']) && in_array($userData['rol'], $adminRoles);
    }
      /**
     * Verificar si el usuario tiene acceso a un módulo específico según su rol
     */
    protected function hasModuleAccess($module)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // El dashboard siempre es accesible para todos los usuarios autenticados
        if ($module === 'dashboard') {
            return true;
        }
        
        // Mapeo de módulos a roles que tienen acceso
        $moduleRoleMap = [
            'dashboard' => ['administrador_empresa', 'admin_empresa', 'psicologo', 'tecnico', 'supervisor', 'usuario'],
            'psicosocial' => ['administrador_empresa', 'admin_empresa', 'psicologo', 'supervisor'],
            'hallazgos' => ['administrador_empresa', 'admin_empresa', 'tecnico', 'supervisor'],
            'reportes' => ['administrador_empresa', 'admin_empresa', 'supervisor'],
            'empleados' => ['administrador_empresa', 'admin_empresa'],
            'configuracion' => ['administrador_empresa', 'admin_empresa']
        ];
        
        if (!isset($moduleRoleMap[$module])) {
            Log::warning('Verificación de acceso a módulo desconocido: ' . $module);
            return false;
        }
        
        $allowedRoles = $moduleRoleMap[$module];
        return $this->hasRole($allowedRoles);
    }
    
    /**
     * Verificar si el usuario tiene acceso de escritura a un módulo específico según su rol
     */
    protected function hasWriteAccess($module)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Mapeo de módulos a roles que tienen acceso de escritura
        $moduleWriteRoleMap = [
            'dashboard' => ['administrador_empresa', 'admin_empresa'],
            'psicosocial' => ['administrador_empresa', 'admin_empresa', 'psicologo'],
            'hallazgos' => ['administrador_empresa', 'admin_empresa', 'tecnico'],
            'reportes' => ['administrador_empresa', 'admin_empresa'],
            'empleados' => ['administrador_empresa', 'admin_empresa'],
            'configuracion' => ['administrador_empresa', 'admin_empresa']
        ];
        
        if (!isset($moduleWriteRoleMap[$module])) {
            Log::warning('Verificación de acceso de escritura a módulo desconocido: ' . $module);
            return false;
        }
        
        $allowedRoles = $moduleWriteRoleMap[$module];
        return $this->hasRole($allowedRoles);
    }
}
