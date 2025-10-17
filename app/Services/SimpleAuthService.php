<?php

namespace App\Services;

use MongoDB\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Models\Cuenta;
use App\Models\Empresa;
use App\Services\MongoPermisoService;

/**
 * Servicio de autenticación simple para MongoDB
 */
class SimpleAuthService
{
    private $empresasClient;
    private $cuentasClient;
    private $permisoService;
    
    public function __construct(MongoPermisoService $permisoService = null)
    {
        try {
            // Conexión a base de datos de empresas
            $dbConfig = Config::get('database.connections.mongodb_empresas');
            $hostEmpresas = $dbConfig['host'] ?? '127.0.0.1';
            $portEmpresas = $dbConfig['port'] ?? 27017;
            $userEmpresas = $dbConfig['username'] ?? '';
            $passEmpresas = $dbConfig['password'] ?? '';
            
            $authStringEmpresas = '';
            if (!empty($userEmpresas) && !empty($passEmpresas)) {
                $authStringEmpresas = "$userEmpresas:$passEmpresas@";
            }
            
            $this->empresasClient = new Client(
                "mongodb://{$authStringEmpresas}{$hostEmpresas}:{$portEmpresas}",
                [],
                ['typeMap' => ['root' => 'array', 'document' => 'array']]
            );
            
            // Conexión a base de datos de cuentas
            $dbConfigCuentas = Config::get('database.connections.mongodb_cuentas');
            $hostCuentas = $dbConfigCuentas['host'] ?? '127.0.0.1';
            $portCuentas = $dbConfigCuentas['port'] ?? 27017;
            $userCuentas = $dbConfigCuentas['username'] ?? '';
            $passCuentas = $dbConfigCuentas['password'] ?? '';
            
            $authStringCuentas = '';
            if (!empty($userCuentas) && !empty($passCuentas)) {
                $authStringCuentas = "$userCuentas:$passCuentas@";
            }
            
            $this->cuentasClient = new Client(
                "mongodb://{$authStringCuentas}{$hostCuentas}:{$portCuentas}",
                [],
                ['typeMap' => ['root' => 'array', 'document' => 'array']]
            );
            
            // Servicio de permisos
            $this->permisoService = $permisoService ?? new MongoPermisoService();
        } catch (Exception $e) {
            Log::error('Error conectando a MongoDB: ' . $e->getMessage());
            throw new Exception('Error conectando a MongoDB: ' . $e->getMessage());
        }
    }
      /**
     * Buscar empresa por NIT - usando documentos reales existentes
     */
    public function findEmpresaByNit($nit)
    {
        try {
            $collection = $this->empresasClient->selectDatabase('empresas')->selectCollection('empresas');
            
            // Buscar con la estructura real del documento existente
            $empresa = $collection->findOne([
                'nit' => $nit,
                'estado' => true,
                '_esBorrado' => ['$ne' => true]
            ]);
            
            if ($empresa) {
                // Verificar si existe el campo 'id' personalizado (no _id)
                if (!isset($empresa['id'])) {
                    Log::warning('Empresa encontrada pero sin campo "id" definido: NIT=' . $nit);
                    
                    // Si no tiene campo id, usar _id como fallback y convertirlo a string
                    if (isset($empresa['_id'])) {
                        $empresa['id'] = is_object($empresa['_id']) ? $empresa['_id']->__toString() : (string)$empresa['_id'];
                        Log::info('Se asignó _id como id: ' . $empresa['id']);
                    } else {
                        Log::error('Empresa sin _id ni id: NIT=' . $nit);
                        return null;
                    }
                }
                
                Log::info('Empresa encontrada: NIT=' . $nit . ', ID=' . $empresa['id'] . ', Nombre=' . ($empresa['nombre'] ?? 'N/A'));
                return $empresa;
            } else {
                Log::warning('Empresa no encontrada para NIT: ' . $nit);
                
                // Obtener muestra de empresas disponibles para verificación
                $todasEmpresas = iterator_to_array($collection->find([], ['limit' => 5]));
                Log::info('Muestra de empresas disponibles: ' . json_encode(array_map(function($emp) {
                    $id = $emp['id'] ?? 'N/A';
                    if (!$id && isset($emp['_id'])) {
                        $id = is_object($emp['_id']) ? $emp['_id']->__toString() : (string)$emp['_id'];
                    }
                    
                    return [
                        'nit' => $emp['nit'] ?? 'N/A',
                        'id' => $id,
                        'nombre' => $emp['nombre'] ?? 'N/A',
                        'estado' => $emp['estado'] ?? 'N/A'
                    ];
                }, $todasEmpresas)));
            }
            
            return null;
        } catch (Exception $e) {
            Log::error('Error buscando empresa: ' . $e->getMessage());
            return null;
        }
    }
      /**
     * Buscar cuenta por email y empresa - usando documentos reales existentes
     */
    public function findCuentaByEmailAndEmpresa($email, $empresaId)
    {
        try {
            $collection = $this->cuentasClient->selectDatabase('cmym')->selectCollection('cuentas');
            
            // Buscar cuenta usando la estructura real del documento existente
            $cuenta = $collection->findOne([
                'email' => $email,
                'estado' => 'activa',
                '_esBorrado' => ['$ne' => true],
                'empresas' => $empresaId // El ID debe estar en el array de empresas
            ]);
            
            if ($cuenta) {
                // Convertir BSONArray a array PHP normal
                if (isset($cuenta['empresas']) && $cuenta['empresas'] instanceof \MongoDB\Model\BSONArray) {
                    $cuenta['empresas'] = iterator_to_array($cuenta['empresas']);
                }
                
                // Asegurarnos de usar el ID correcto - priorizar 'id' personalizado
                $cuentaId = $cuenta['id'] ?? null;
                if (!$cuentaId && isset($cuenta['_id'])) {
                    $cuentaId = is_object($cuenta['_id']) ? $cuenta['_id']->__toString() : (string)$cuenta['_id'];
                    // Guardar el ID para futura referencia
                    $cuenta['id'] = $cuentaId;
                }
                
                Log::info('Cuenta encontrada: Email=' . $email . ', EmpresaID=' . $empresaId);
                
                // Cargar permisos para la cuenta y la empresa
                $cuenta['permisos'] = $this->permisoService->getPermisosByCuentaAndEmpresa($cuentaId, $empresaId);
                
                // Verificar si la contraseña existe
                if (!isset($cuenta['contrasena']) && !isset($cuenta['password'])) {
                    Log::error('Cuenta sin campo de contraseña: ' . $email);
                    return null;
                }
                
                return $cuenta;
            } else {
                Log::warning('Cuenta no encontrada: Email=' . $email . ', EmpresaID=' . $empresaId);
                
                // Verificar cuenta para autenticación
                $cuentaDebug = $collection->findOne(['email' => $email]);
                if ($cuentaDebug) {
                    // Convertir BSONArray a array PHP normal
                    if (isset($cuentaDebug['empresas']) && $cuentaDebug['empresas'] instanceof \MongoDB\Model\BSONArray) {
                        $cuentaDebug['empresas'] = iterator_to_array($cuentaDebug['empresas']);
                    }
                    
                    Log::info('Cuenta existe pero empresas no coinciden:');
                    Log::info('Email: ' . $email);
                    Log::info('Empresas del usuario: ' . json_encode($cuentaDebug['empresas'] ?? []));
                    Log::info('Empresa buscada: ' . $empresaId);
                    Log::info('Estado de la cuenta: ' . ($cuentaDebug['estado'] ?? 'No definido'));
                    Log::info('Es borrado: ' . (($cuentaDebug['_esBorrado'] ?? false) ? 'Sí' : 'No'));
                } else {
                    Log::warning('Cuenta con email ' . $email . ' no existe en la base de datos');
                }
            }
            
            return null;
        } catch (Exception $e) {
            Log::error('Error buscando cuenta: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verificar contraseña
     */
    public function verifyPassword($password, $hashedPassword)
    {
        // Si la contraseña está en texto plano (para desarrollo/testing)
        if (Config::get('app.env') === 'local' && $password === $hashedPassword) {
            Log::warning('¡ADVERTENCIA! Usando comparación de texto plano para contraseñas en entorno local');
            return true;
        }
        
        return password_verify($password, $hashedPassword);
    }    /**
     * Verificar acceso a empresa - usando estructura real de documentos
     */
    public function hasAccessToEmpresa($cuenta, $empresaId)
    {
        // Verificar si es Super Administrador (verificar variantes del nombre del rol)
        $roles_super_admin = ['super_admin', 'SuperAdministrador', 'super_administrador', 'superadmin'];
        if (isset($cuenta['rol']) && in_array($cuenta['rol'], $roles_super_admin)) {
            Log::info('Usuario Super Admin: ' . ($cuenta['email'] ?? 'N/A') . ' - Acceso autorizado a todas las empresas');
            return true;
        }
        
        // Verificar si el ID de empresa está en el array de empresas del usuario
        if (isset($cuenta['empresas'])) {
            // Convertir BSONArray a array PHP normal si es necesario
            $empresas = $cuenta['empresas'];
            if ($empresas instanceof \MongoDB\Model\BSONArray) {
                $empresas = iterator_to_array($empresas);
            }
            
            if (is_array($empresas)) {
                // Asegurarnos que estamos comparando strings (importante para MongoDB)
                $empresaIdStr = (string)$empresaId;
                $empresasStr = array_map(function($id) { return (string)$id; }, $empresas);
                
                $hasAccess = in_array($empresaIdStr, $empresasStr);
                
                Log::info('Verificando acceso a empresa:');
                Log::info('EmpresaID buscada: ' . $empresaIdStr);
                Log::info('Empresas del usuario: ' . json_encode($empresasStr));
                Log::info('Acceso: ' . ($hasAccess ? 'SÍ' : 'NO'));
                
                if ($hasAccess) {
                    // Si tiene acceso, verificar permisos específicos (opcional, según lógica de negocio)
                    $cuentaId = $cuenta['id'] ?? (
                        isset($cuenta['_id']) 
                            ? (is_object($cuenta['_id']) ? $cuenta['_id']->__toString() : (string)$cuenta['_id']) 
                            : null
                    );
                    
                    if ($cuentaId) {
                        // Verificar permisos específicos con el servicio de permisos
                        $permisos = $this->permisoService->getPermisosByCuentaAndEmpresa($cuentaId, $empresaIdStr);
                        
                        // Si no hay permisos específicos para esta empresa, verificar si hay permisos globales
                        if (empty($permisos)) {
                            Log::warning('Usuario sin permisos específicos para empresa: ' . $empresaIdStr);
                            // Podríamos rechazar el acceso aquí o permitirlo con restricciones básicas
                            // return false;
                        }
                        
                        return true;
                    }
                }
                
                return $hasAccess;
            }
        }
        
        Log::warning('Usuario sin empresas asignadas o estructura incorrecta');
        return false;
    }
    
    /**
     * Obtener permisos de módulos para una cuenta y empresa
     */
    public function getPermisos($cuentaId, $empresaId)
    {
        return $this->permisoService->getPermisosByCuentaAndEmpresa($cuentaId, $empresaId);
    }
}
