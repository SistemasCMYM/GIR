<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Auth\Sesion;
use App\Models\Cuenta;
use App\Models\Perfil;
use App\Models\Auth\Permiso;
use Carbon\Carbon;

/**
 * Servicio centralizado de gestión de sesión
 * 
 * Maneja toda la lógica de validación y gestión de sesiones
 * integrando los esquemas centralizados de MongoDB
 */
class SessionManagementService
{
    /**
     * Configuración de timeouts
     */
    private const INACTIVITY_TIMEOUT = 120; // 2 horas en minutos
    private const SESSION_LIFETIME = 480;   // 8 horas en minutos

    /**
     * Validar sesión completa con todos los componentes
     * 
     * @return array Estado de validación con detalles
     */
    public function validateFullSession(): array
    {
        try {
            Log::info('SessionManagementService: Iniciando validación completa de sesión');

            // 1. Verificar sesión Laravel básica
            $basicSession = $this->validateBasicSession();
            if (!$basicSession['valid']) {
                return $basicSession;
            }

            // 2. Validar NIT y empresa
            $nitValidation = $this->validateNitAndCompany();
            if (!$nitValidation['valid']) {
                return $nitValidation;
            }

            // 3. Validar credenciales de cuenta
            $accountValidation = $this->validateAccountCredentials();
            if (!$accountValidation['valid']) {
                return $accountValidation;
            }

            // 4. Validar perfil por cuenta_id
            $profileValidation = $this->validateAccountProfile($accountValidation['data']['id']);
            if (!$profileValidation['valid']) {
                return $profileValidation;
            }

            // 5. Validar permisos por cuenta_id
            $permissionsValidation = $this->validateAccountPermissions($accountValidation['data']['id']);
            if (!$permissionsValidation['valid']) {
                return $permissionsValidation;
            }

            // 6. Validar sesión MongoDB
            $mongoSessionValidation = $this->validateMongoSession($accountValidation['data']['id']);
            if (!$mongoSessionValidation['valid']) {
                return $mongoSessionValidation;
            }

            // 7. Verificar límites de tiempo
            $timeoutValidation = $this->validateSessionTimeouts($mongoSessionValidation['data']);
            if (!$timeoutValidation['valid']) {
                return $timeoutValidation;
            }

            // 8. Actualizar actividad
            $this->updateSessionActivity($mongoSessionValidation['data']);

            // 9. Asegurar integridad de datos
            $this->ensureSessionIntegrity(
                $accountValidation['data'],
                $profileValidation['data'],
                $permissionsValidation['data']
            );

            Log::info('SessionManagementService: Validación completa exitosa');

            return [
                'valid' => true,
                'message' => 'Sesión válida',
                'data' => [
                    'account' => $accountValidation['data'],
                    'profile' => $profileValidation['data'],
                    'permissions' => $permissionsValidation['data'],
                    'mongo_session' => $mongoSessionValidation['data'],
                    'company' => $nitValidation['data']
                ]
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error en validación completa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'valid' => false,
                'error' => 'internal_error',
                'message' => 'Error interno durante validación de sesión',
                'redirect' => 'auth.nit.form'
            ];
        }
    }

    /**
     * Validar sesión Laravel básica
     */
    private function validateBasicSession(): array
    {
        $authenticated = Session::get('authenticated', false);
        $cuentaId = Session::get('cuenta_id');
        $userData = Session::get('user_data');

        if (!$authenticated || !$cuentaId || !$userData) {
            Log::warning('SessionManagementService: Sesión Laravel básica inválida');
            
            return [
                'valid' => false,
                'error' => 'basic_session_invalid',
                'message' => 'Sesión básica no válida',
                'redirect' => 'auth.nit.form'
            ];
        }

        return [
            'valid' => true,
            'data' => [
                'cuenta_id' => $cuentaId,
                'user_data' => $userData
            ]
        ];
    }

    /**
     * Validar NIT y datos de empresa
     */
    private function validateNitAndCompany(): array
    {
        $empresaData = Session::get('empresa_data');
        $nitVerified = Session::get('nit_verified', false);

        if (!$nitVerified || !$empresaData || !is_array($empresaData)) {
            Log::warning('SessionManagementService: NIT no verificado o empresa faltante');
            
            return [
                'valid' => false,
                'error' => 'nit_not_verified',
                'message' => 'NIT no verificado o datos de empresa faltantes',
                'redirect' => 'auth.nit.form'
            ];
        }

        // Verificar campos esenciales
        $requiredFields = ['id', 'nit', 'razon_social'];
        foreach ($requiredFields as $field) {
            if (empty($empresaData[$field])) {
                Log::error('SessionManagementService: Campo empresa faltante', ['field' => $field]);
                
                return [
                    'valid' => false,
                    'error' => 'company_data_incomplete',
                    'message' => 'Datos de empresa incompletos',
                    'redirect' => 'auth.nit.form'
                ];
            }
        }

        // Verificar que la empresa sigue existiendo
        try {
            $empresa = \DB::connection('mongodb_empresas')
                          ->collection('empresas')
                          ->where('nit', $empresaData['nit'])
                          ->where('estado', '!=', 0)
                          ->first();

            if (!$empresa) {
                Log::error('SessionManagementService: Empresa no encontrada o inactiva', [
                    'nit' => $empresaData['nit']
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'company_not_found',
                    'message' => 'La empresa ya no está disponible o está inactiva',
                    'redirect' => 'auth.nit.form'
                ];
            }

            return [
                'valid' => true,
                'data' => $empresaData
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error validando empresa', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'error' => 'company_validation_error',
                'message' => 'Error validando datos de empresa',
                'redirect' => 'auth.nit.form'
            ];
        }
    }

    /**
     * Validar credenciales de cuenta
     */
    private function validateAccountCredentials(): array
    {
        $cuentaId = Session::get('cuenta_id');
        $userData = Session::get('user_data', []);

        try {
            $cuenta = \DB::connection('mongodb_cmym')
                         ->collection('cuentas')
                         ->where('id', $cuentaId)
                         ->first();

            if (!$cuenta) {
                Log::error('SessionManagementService: Cuenta no encontrada', ['cuenta_id' => $cuentaId]);
                
                return [
                    'valid' => false,
                    'error' => 'account_not_found',
                    'message' => 'La cuenta ya no existe',
                    'redirect' => 'login.credentials'
                ];
            }

            // Verificar estado de cuenta
            if (isset($cuenta['estado']) && $cuenta['estado'] !== 'activa') {
                Log::error('SessionManagementService: Cuenta inactiva', [
                    'cuenta_id' => $cuentaId,
                    'estado' => $cuenta['estado']
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'account_inactive',
                    'message' => 'La cuenta está inactiva',
                    'redirect' => 'login.credentials'
                ];
            }

            // Verificar acceso a empresa actual
            $empresaData = Session::get('empresa_data');
            $empresasAcceso = $cuenta['empresas'] ?? [];
            
            if (is_string($empresasAcceso)) {
                $empresasAcceso = json_decode($empresasAcceso, true) ?: [];
            }
            
            if (!is_array($empresasAcceso)) {
                $empresasAcceso = [];
            }
            
            $tieneAcceso = in_array($empresaData['id'], $empresasAcceso) || 
                          in_array($empresaData['_id'] ?? '', $empresasAcceso);
                          
            if (!$tieneAcceso && !$this->isSuperAdmin($cuenta)) {
                Log::error('SessionManagementService: Sin acceso a empresa', [
                    'cuenta_id' => $cuentaId,
                    'empresa_id' => $empresaData['id']
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'no_company_access',
                    'message' => 'Sin acceso a la empresa actual',
                    'redirect' => 'auth.nit.form'
                ];
            }

            return [
                'valid' => true,
                'data' => $cuenta
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error validando credenciales', [
                'cuenta_id' => $cuentaId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'error' => 'credentials_validation_error',
                'message' => 'Error validando credenciales',
                'redirect' => 'login.credentials'
            ];
        }
    }

    /**
     * Validar perfil de cuenta
     */
    private function validateAccountProfile(string $cuentaId): array
    {
        try {
            $perfil = \DB::connection('mongodb_cmym')
                         ->collection('perfiles')
                         ->where('cuenta_id', $cuentaId)
                         ->first();

            if (!$perfil) {
                Log::error('SessionManagementService: Perfil no encontrado', ['cuenta_id' => $cuentaId]);
                
                return [
                    'valid' => false,
                    'error' => 'profile_not_found',
                    'message' => 'No se encontró perfil asignado a la cuenta',
                    'redirect' => 'auth.nit.form'
                ];
            }

            // Verificar campos esenciales
            if (empty($perfil['nombre'])) {
                Log::error('SessionManagementService: Perfil incompleto', ['cuenta_id' => $cuentaId]);
                
                return [
                    'valid' => false,
                    'error' => 'profile_incomplete',
                    'message' => 'El perfil está incompleto',
                    'redirect' => 'auth.nit.form'
                ];
            }

            return [
                'valid' => true,
                'data' => $perfil
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error validando perfil', [
                'cuenta_id' => $cuentaId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'error' => 'profile_validation_error',
                'message' => 'Error validando perfil',
                'redirect' => 'auth.nit.form'
            ];
        }
    }

    /**
     * Validar permisos de cuenta
     */
    private function validateAccountPermissions(string $cuentaId): array
    {
        try {
            $permisos = \DB::connection('mongodb_cmym')
                           ->collection('permisos')
                           ->where('cuenta_id', $cuentaId)
                           ->get()
                           ->toArray();

            if (empty($permisos)) {
                Log::warning('SessionManagementService: Sin permisos asignados', ['cuenta_id' => $cuentaId]);
                
                return [
                    'valid' => false,
                    'error' => 'no_permissions',
                    'message' => 'No hay permisos asignados a la cuenta',
                    'redirect' => 'auth.nit.form'
                ];
            }

            // Verificar estructura de permisos
            $permisosValidos = [];
            foreach ($permisos as $permiso) {
                if (isset($permiso['modulo']) && isset($permiso['acciones'])) {
                    $permisosValidos[] = $permiso;
                }
            }

            if (empty($permisosValidos)) {
                Log::error('SessionManagementService: Permisos con estructura inválida', ['cuenta_id' => $cuentaId]);
                
                return [
                    'valid' => false,
                    'error' => 'invalid_permissions_structure',
                    'message' => 'Los permisos tienen estructura inválida',
                    'redirect' => 'auth.nit.form'
                ];
            }

            return [
                'valid' => true,
                'data' => $permisosValidos
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error validando permisos', [
                'cuenta_id' => $cuentaId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'error' => 'permissions_validation_error',
                'message' => 'Error validando permisos',
                'redirect' => 'auth.nit.form'
            ];
        }
    }

    /**
     * Validar sesión MongoDB
     */
    private function validateMongoSession(string $cuentaId): array
    {
        try {
            $sessionToken = Session::get('mongo_session_token');
            
            $sesion = \DB::connection('mongodb_cmym')
                         ->collection('sesiones')
                         ->where('usuario_id', $cuentaId)
                         ->where('activa', true)
                         ->whereNull('fin_sesion')
                         ->orderBy('ultima_actividad', 'desc')
                         ->first();

            if (!$sesion) {
                Log::error('SessionManagementService: Sesión MongoDB no encontrada', ['cuenta_id' => $cuentaId]);
                
                return [
                    'valid' => false,
                    'error' => 'mongo_session_not_found',
                    'message' => 'Sesión MongoDB no encontrada',
                    'redirect' => 'auth.nit.form'
                ];
            }

            // Verificar token si está disponible
            if ($sessionToken && $sesion['token_sesion'] !== $sessionToken) {
                Log::error('SessionManagementService: Token de sesión no coincide', [
                    'cuenta_id' => $cuentaId,
                    'sesion_id' => $sesion['id'] ?? 'unknown'
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'session_token_mismatch',
                    'message' => 'Token de sesión no válido',
                    'redirect' => 'auth.nit.form'
                ];
            }

            return [
                'valid' => true,
                'data' => $sesion
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error validando sesión MongoDB', [
                'cuenta_id' => $cuentaId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'error' => 'mongo_session_validation_error',
                'message' => 'Error validando sesión MongoDB',
                'redirect' => 'auth.nit.form'
            ];
        }
    }

    /**
     * Validar timeouts de sesión
     */
    private function validateSessionTimeouts(array $sesion): array
    {
        try {
            $ultimaActividad = $sesion['ultima_actividad'] ?? null;
            $inicioSesion = $sesion['inicio_sesion'] ?? null;

            if (!$ultimaActividad) {
                Log::error('SessionManagementService: Sesión sin última actividad', [
                    'sesion_id' => $sesion['id'] ?? 'unknown'
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'no_activity_timestamp',
                    'message' => 'Sesión sin marca de tiempo de actividad',
                    'timeout_type' => 'system_error',
                    'redirect' => 'auth.nit.form'
                ];
            }

            // Convertir a Carbon
            if (is_string($ultimaActividad)) {
                $ultimaActividad = Carbon::parse($ultimaActividad);
            } elseif (is_object($ultimaActividad) && isset($ultimaActividad->date)) {
                $ultimaActividad = Carbon::parse($ultimaActividad->date);
            }

            if (is_string($inicioSesion)) {
                $inicioSesion = Carbon::parse($inicioSesion);
            } elseif (is_object($inicioSesion) && isset($inicioSesion->date)) {
                $inicioSesion = Carbon::parse($inicioSesion->date);
            }

            $minutosInactivo = now()->diffInMinutes($ultimaActividad);
            $minutosTotal = $inicioSesion ? now()->diffInMinutes($inicioSesion) : 0;

            Log::info('SessionManagementService: Verificando timeouts', [
                'sesion_id' => $sesion['id'] ?? 'unknown',
                'minutos_inactivo' => $minutosInactivo,
                'minutos_total' => $minutosTotal,
                'limite_inactividad' => self::INACTIVITY_TIMEOUT,
                'limite_total' => self::SESSION_LIFETIME
            ]);

            // Verificar timeout de inactividad
            if ($minutosInactivo > self::INACTIVITY_TIMEOUT) {
                Log::warning('SessionManagementService: Timeout por inactividad', [
                    'minutos_inactivo' => $minutosInactivo,
                    'limite' => self::INACTIVITY_TIMEOUT
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'inactivity_timeout',
                    'message' => 'Sesión expirada por inactividad',
                    'timeout_type' => 'inactivity',
                    'preserve_location' => true,
                    'redirect' => 'auth.nit.form'
                ];
            }

            // Verificar lifetime total de sesión
            if ($minutosTotal > self::SESSION_LIFETIME) {
                Log::warning('SessionManagementService: Timeout por duración total', [
                    'minutos_total' => $minutosTotal,
                    'limite' => self::SESSION_LIFETIME
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'session_lifetime_timeout',
                    'message' => 'Sesión expirada por duración máxima',
                    'timeout_type' => 'lifetime',
                    'preserve_location' => false,
                    'redirect' => 'auth.nit.form'
                ];
            }

            return [
                'valid' => true,
                'data' => [
                    'minutos_inactivo' => $minutosInactivo,
                    'minutos_total' => $minutosTotal
                ]
            ];

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error validando timeouts', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'valid' => false,
                'error' => 'timeout_validation_error',
                'message' => 'Error validando timeouts de sesión',
                'redirect' => 'auth.nit.form'
            ];
        }
    }

    /**
     * Actualizar actividad de sesión
     */
    private function updateSessionActivity(array $sesion): void
    {
        try {
            \DB::connection('mongodb_cmym')
               ->collection('sesiones')
               ->where('id', $sesion['id'])
               ->update([
                   'ultima_actividad' => now(),
                   'updated_at' => now()
               ]);

            Log::debug('SessionManagementService: Actividad de sesión actualizada', [
                'sesion_id' => $sesion['id']
            ]);

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error actualizando actividad', [
                'sesion_id' => $sesion['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Asegurar integridad de datos de sesión
     */
    private function ensureSessionIntegrity(array $cuenta, array $perfil, array $permisos): void
    {
        try {
            $userData = Session::get('user_data', []);
            $updated = false;

            // Actualizar datos básicos si han cambiado
            if (($userData['email'] ?? '') !== ($cuenta['email'] ?? '')) {
                $userData['email'] = $cuenta['email'] ?? '';
                $updated = true;
            }

            if (($userData['nombre'] ?? '') !== ($perfil['nombre'] ?? '')) {
                $userData['nombre'] = $perfil['nombre'] ?? '';
                $updated = true;
            }

            if (($userData['apellido'] ?? '') !== ($perfil['apellido'] ?? '')) {
                $userData['apellido'] = $perfil['apellido'] ?? '';
                $updated = true;
            }

            // Actualizar permisos y módulos
            $modulosActualizados = [];
            $accionesActualizadas = [];
            
            foreach ($permisos as $permiso) {
                if (isset($permiso['modulo'])) {
                    $modulosActualizados[] = $permiso['modulo'];
                }
                if (isset($permiso['acciones']) && is_array($permiso['acciones'])) {
                    $accionesActualizadas = array_merge($accionesActualizadas, $permiso['acciones']);
                }
            }

            $userData['modulos'] = array_unique($modulosActualizados);
            $userData['permisos'] = array_unique($accionesActualizadas);
            $updated = true;

            if ($updated) {
                Session::put('user_data', $userData);
                Log::debug('SessionManagementService: Datos de sesión actualizados por integridad');
            }

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error asegurando integridad', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cerrar sesión completamente
     */
    public function closeSession(): bool
    {
        try {
            $cuentaId = Session::get('cuenta_id');
            
            if ($cuentaId) {
                // Cerrar sesiones MongoDB
                \DB::connection('mongodb_cmym')
                   ->collection('sesiones')
                   ->where('usuario_id', $cuentaId)
                   ->where('activa', true)
                   ->update([
                       'activa' => false,
                       'fin_sesion' => now(),
                       'updated_at' => now()
                   ]);

                Log::info('SessionManagementService: Sesiones MongoDB cerradas', ['cuenta_id' => $cuentaId]);
            }

            // Limpiar sesión Laravel
            Session::forget([
                'authenticated', 'cuenta_id', 'user_data', 'empresa_data',
                'mongo_session_token', 'session_id', 'nit_verified'
            ]);
            
            Session::regenerateToken();
            
            Log::info('SessionManagementService: Sesión Laravel limpiada');
            
            return true;

        } catch (\Exception $e) {
            Log::error('SessionManagementService: Error cerrando sesión', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Preservar ubicación actual para redirección posterior
     */
    public function preserveIntendedUrl(string $url): void
    {
        Session::put('intended_url', $url);
        Log::info('SessionManagementService: URL preservada', ['url' => $url]);
    }

    /**
     * Obtener y limpiar URL preservada
     */
    public function getAndClearIntendedUrl(): ?string
    {
        $url = Session::pull('intended_url');
        if ($url) {
            Log::info('SessionManagementService: URL recuperada', ['url' => $url]);
        }
        return $url;
    }

    /**
     * Verificar si es Super Admin
     */
    private function isSuperAdmin(array $cuenta): bool
    {
        $superAdminConfig = config('app.super_admins', []);
        $superAdminEmails = $superAdminConfig['emails'] ?? [];
        $superAdminRoles = $superAdminConfig['roles'] ?? [];

        return in_array($cuenta['email'] ?? '', $superAdminEmails) ||
               in_array($cuenta['rol'] ?? '', $superAdminRoles);
    }

    /**
     * Obtener información de estado de sesión
     */
    public function getSessionStatus(): array
    {
        return [
            'authenticated' => Session::get('authenticated', false),
            'cuenta_id' => Session::get('cuenta_id'),
            'empresa_id' => Session::get('empresa_data.id'),
            'nit_verified' => Session::get('nit_verified', false),
            'has_mongo_token' => !empty(Session::get('mongo_session_token')),
            'last_activity' => Session::get('last_activity'),
            'session_lifetime' => self::SESSION_LIFETIME,
            'inactivity_timeout' => self::INACTIVITY_TIMEOUT
        ];
    }
}
