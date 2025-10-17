<?php

namespace App\Services;

use MongoDB\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class MongoPermisoService
{
    private $permisosClient;
    
    public function __construct()
    {
        try {
            // Conexión a base de datos de permisos
            $dbConfig = Config::get('database.connections.mongodb_cuentas');
            $host = $dbConfig['host'] ?? '127.0.0.1';
            $port = $dbConfig['port'] ?? 27017;
            $user = $dbConfig['username'] ?? '';
            $pass = $dbConfig['password'] ?? '';
            
            $authString = '';
            if (!empty($user) && !empty($pass)) {
                $authString = "$user:$pass@";
            }
            
            $this->permisosClient = new Client(
                "mongodb://{$authString}{$host}:{$port}",
                [],
                ['typeMap' => ['root' => 'array', 'document' => 'array']]
            );
        } catch (Exception $e) {
            Log::error('Error conectando a MongoDB para permisos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener permisos por cuenta_id y empresa_id
     */
    public function getPermisosByCuentaAndEmpresa($cuentaId, $empresaId)
    {
        try {
            $collection = $this->permisosClient->selectDatabase('cmym')->selectCollection('permisos');
            
            // Buscar permisos específicos para la cuenta y empresa
            $permisos = $collection->find([
                'cuenta_id' => $cuentaId,
                'empresa_id' => $empresaId
            ])->toArray();
            
            if (empty($permisos)) {
                Log::info("No se encontraron permisos específicos para: CuentaID=$cuentaId, EmpresaID=$empresaId");
                
                // Buscar permisos globales para la cuenta (sin empresa específica)
                $permisosGlobales = $collection->find([
                    'cuenta_id' => $cuentaId,
                    'empresa_id' => ['$exists' => false]
                ])->toArray();
                
                if (!empty($permisosGlobales)) {
                    Log::info("Se encontraron permisos globales para: CuentaID=$cuentaId");
                    return $permisosGlobales;
                }
            } else {
                Log::info("Permisos encontrados para: CuentaID=$cuentaId, EmpresaID=$empresaId");
                return $permisos;
            }
            
            return [];
        } catch (Exception $e) {
            Log::error('Error obteniendo permisos: ' . $e->getMessage());
            return [];
        }
    }    /**
     * Verificar si la cuenta tiene permiso para un módulo específico
     */
    public function tienePermiso($cuentaId, $empresaId, $modulo, $accion = null)
    {
        try {
            // Excepción especial: Todos los usuarios autenticados tienen acceso al dashboard
            if ($modulo === 'dashboard') {
                Log::info("Acceso al dashboard permitido para usuario: $cuentaId");
                return true;
            }
            
            $permisos = $this->getPermisosByCuentaAndEmpresa($cuentaId, $empresaId);
            
            // Si no hay permisos, intentar buscar permisos basados en el rol del usuario
            if (empty($permisos)) {
                // Intentar obtener el rol del usuario de la colección de cuentas
                $rolUsuario = $this->getRolUsuario($cuentaId);
                
                // Verificar si es Super Administrador (acceso global)
                $roles_super_admin = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin', 'SuperAdmin'];
                if (in_array($rolUsuario, $roles_super_admin)) {
                    Log::info("Super Admin: acceso autorizado a módulo $modulo");
                    return true;
                }
                
                // Buscar permisos basados en el rol para esta empresa
                $permisosRol = $this->getPermisosByRolAndEmpresa($rolUsuario, $empresaId);
                if (!empty($permisosRol)) {
                    Log::info("Usando permisos basados en rol ($rolUsuario) para cuenta: $cuentaId");
                    $permisos = $permisosRol;
                }
            }
            
            foreach ($permisos as $permiso) {
                // Verificar permiso por módulo
                if (isset($permiso['modulo']) && $permiso['modulo'] === $modulo) {
                    // Si no se especifica acción, solo verificamos acceso al módulo
                    if ($accion === null) {
                        return true;
                    }
                    
                    // Verificar acción específica
                    $acciones = $permiso['acciones'] ?? [];
                    if (is_array($acciones) && in_array($accion, $acciones)) {
                        return true;
                    }
                }
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Error verificando permiso: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener el rol de un usuario por su ID de cuenta
     */
    private function getRolUsuario($cuentaId)
    {
        try {
            $collection = $this->permisosClient->selectDatabase('cmym')->selectCollection('cuentas');
            
            $cuenta = $collection->findOne(['id' => $cuentaId]);
            if (!$cuenta && isset($cuenta['_id'])) {
                // Intentar buscar por _id si no se encuentra por id
                $cuenta = $collection->findOne(['_id' => $cuentaId]);
            }
            
            return $cuenta['rol'] ?? 'usuario';
        } catch (Exception $e) {
            Log::error('Error obteniendo rol de usuario: ' . $e->getMessage());
            return 'usuario';
        }
    }
    
    /**
     * Obtener permisos basados en rol y empresa
     */
    private function getPermisosByRolAndEmpresa($rol, $empresaId)
    {
        try {
            $collection = $this->permisosClient->selectDatabase('cmym')->selectCollection('permisos_roles');
            
            // Buscar permisos para este rol y empresa
            $permisos = $collection->find([
                'rol' => $rol,
                'empresa_id' => $empresaId
            ])->toArray();
            
            if (empty($permisos)) {
                // Buscar permisos globales para este rol
                $permisosGlobales = $collection->find([
                    'rol' => $rol,
                    'empresa_id' => ['$exists' => false]
                ])->toArray();
                
                if (!empty($permisosGlobales)) {
                    return $permisosGlobales;
                }
            }
            
            return $permisos;
        } catch (Exception $e) {
            Log::error('Error obteniendo permisos por rol: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todos los módulos con permisos para una cuenta y empresa
     */
    public function getModulosPermitidos($cuentaId, $empresaId)
    {
        try {
            $permisos = $this->getPermisosByCuentaAndEmpresa($cuentaId, $empresaId);
            $modulos = [];
            
            foreach ($permisos as $permiso) {
                if (isset($permiso['modulo'])) {
                    $modulos[] = $permiso['modulo'];
                }
            }
            
            return array_unique($modulos);
        } catch (Exception $e) {
            Log::error('Error obteniendo módulos permitidos: ' . $e->getMessage());
            return [];
        }
    }
}