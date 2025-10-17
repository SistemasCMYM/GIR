<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Cuenta;
use App\Models\Perfil;
use App\Models\Sesion;
use App\Services\SecurityLogger;
use App\Services\SessionManagementService;

class AuthController extends Controller
{
    protected $securityLogger;
    protected $sessionService;

    public function __construct(SecurityLogger $securityLogger, SessionManagementService $sessionService)
    {
        $this->securityLogger = $securityLogger;
        $this->sessionService = $sessionService;
    }

    /**
     * Show NIT form (first step of authentication)
     */
    public function showNitForm()
    {
        // Verificar si ya tiene sesión completa y válida
        $sessionStatus = $this->sessionService->validateFullSession();
        
        if ($sessionStatus['valid']) {
            // Obtener URL preservada o redirigir al dashboard
            $intendedUrl = $this->sessionService->getAndClearIntendedUrl();
            return $intendedUrl ? redirect($intendedUrl) : redirect()->route('dashboard');
        }

        return view('admin.auth.nit');
    }

    /**
     * Show landing page with modern design
     */
    public function showLanding()
    {
        // Verificar si ya tiene sesión completa y válida
        $sessionStatus = $this->sessionService->validateFullSession();
        
        if ($sessionStatus['valid']) {
            // Obtener URL preservada o redirigir al dashboard
            $intendedUrl = $this->sessionService->getAndClearIntendedUrl();
            return $intendedUrl ? redirect($intendedUrl) : redirect()->route('dashboard');
        }

        return view('admin.auth.landing');
    }

    /**
     * Verify NIT and set empresa in session (first step)
     */
    public function verifyNit(Request $request)
    {
        $request->validate([
            'nit' => 'required|string|min:4|max:15'
        ]);

        $nit = preg_replace('/[^0-9]/', '', $request->nit);

        try {
            // 1. Buscar empresa en la base de datos 'empresas' por el campo 'nit'
            $empresa = \DB::connection('mongodb_empresas')
                          ->collection('empresas')
                          ->where(function($query) use ($nit) {
                              $query->where('nit', $nit)
                                    ->orWhere('id', $nit);
                          })
                          ->where('estado', '!=', 0)
                          ->first();

            if (!$empresa) {
                Log::warning('Intento de acceso con NIT no válido: ' . $nit);
                return back()->withErrors([
                    'nit' => 'El NIT ingresado no está registrado en el sistema o la empresa está inactiva.'
                ])->withInput();
            }

            // Verificar que la empresa esté activa
            if (isset($empresa['estado']) && ($empresa['estado'] == 0 || $empresa['estado'] === false)) {
                Log::warning('Intento de acceso con empresa inactiva: ' . $nit);
                return back()->withErrors([
                    'nit' => 'La empresa está inactiva. Contacte al administrador.'
                ])->withInput();
            }

            // Guardar empresa en sesión
            $empresaData = [
                'id' => $empresa['id'] ?? $empresa['_id'],
                '_id' => $empresa['_id'],
                'nit' => $empresa['nit'],
                'razon_social' => $empresa['razon_social'],
                'nombre_comercial' => $empresa['nombre_comercial'] ?? $empresa['razon_social'],
                'estado' => $empresa['estado'],
                'ciudad_id' => $empresa['ciudad_id'] ?? null,
                'departamento_id' => $empresa['departamento_id'] ?? null,
                'sector_id' => $empresa['sector_id'] ?? null,
                'centro_id' => $empresa['centro_id'] ?? null
            ];

            Session::put('empresa_data', $empresaData);
            Session::put('nit_verified', true);

            Log::info('NIT verificado exitosamente: ' . $nit . ' - Empresa: ' . $empresa['razon_social'] . ' - ID: ' . ($empresa['id'] ?? $empresa['_id']));

            return redirect()->route('login.credentials');

        } catch (\Exception $e) {
            Log::error('Error en verificación de NIT: ' . $e->getMessage());
            return back()->withErrors([
                'nit' => 'Error interno del servidor. Intente nuevamente.'
            ])->withInput();
        }
    }

    /**
     * Show credentials form (second step of authentication)
     */
    public function showCredentialsForm()
    {
        // Verificar si ya tiene sesión completa
        $sessionStatus = $this->sessionService->validateFullSession();
        
        if ($sessionStatus['valid']) {
            // Obtener URL preservada o redirigir al dashboard
            $intendedUrl = $this->sessionService->getAndClearIntendedUrl();
            return $intendedUrl ? redirect($intendedUrl) : redirect()->route('dashboard');
        }

        // Verificar que el NIT fue verificado previamente
        if (!Session::get('nit_verified') || !Session::has('empresa_data')) {
            return redirect()->route('auth.nit.form')->withErrors([
                'general' => 'Debe verificar el NIT primero.'
            ]);
        }

        $empresa = Session::get('empresa_data');
        return view('admin.auth.credentials', compact('empresa'));
    }

    /**
     * Verify credentials and complete authentication (second step)
     */
    public function verifyCredentials(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if (!Session::get('nit_verified') || !Session::has('empresa_data')) {
            return redirect()->route('login.nit')->withErrors([
                'general' => 'Sesión expirada. Debe verificar el NIT nuevamente.'
            ]);
        }

        try {
            $empresaData = Session::get('empresa_data');
            $empresaId = $empresaData['id'];

            // 2. Buscar cuenta en la base de datos 'cmym' colección 'cuentas'
            $cuenta = \DB::connection('mongodb_cmym')
                         ->collection('cuentas')
                         ->where('email', $request->email)
                         ->where('estado', '!=', 'inactiva')
                         ->first();

            if (!$cuenta) {
                Log::warning('Intento de login con email no válido: ' . $request->email);
                return back()->withErrors([
                    'email' => 'El correo electrónico no está registrado en el sistema.'
                ])->withInput();
            }

            // Verificar que la cuenta tenga acceso a la empresa actual
            $empresasAcceso = $cuenta['empresas'] ?? [];
            
            if (is_string($empresasAcceso) || $empresasAcceso instanceof \Traversable) {
                $empresasAcceso = safe_json_decode($empresasAcceso, true) ?: [];
            }
            if (!is_array($empresasAcceso)) {
                $empresasAcceso = [];
            }
            
            $tieneAcceso = in_array($empresaId, $empresasAcceso) || 
                          in_array($empresaData['_id'], $empresasAcceso) ||
                          in_array((string)$empresaData['_id'], $empresasAcceso);
                          
            if (!$tieneAcceso) {
                Log::warning('Cuenta sin acceso a empresa: ' . $request->email . ' - Empresa ID: ' . $empresaId);
                return back()->withErrors([
                    'email' => 'Esta cuenta no tiene acceso a la empresa seleccionada.'
                ])->withInput();
            }

            // Verificar contraseña con soporte para algoritmos legacy
            $cuentaModel = new Cuenta();
            $passwordField = isset($cuenta['password']) ? 'password' : 'contrasena';
            $hashedPassword = $cuenta[$passwordField] ?? '';
            
            Log::info('AuthController: Iniciando verificación de contraseña', [
                'email' => $request->email,
                'password_field' => $passwordField,
                'hash_length' => strlen($hashedPassword),
                'hash_preview' => substr($hashedPassword, 0, 10) . '...'
            ]);
            
            try {
                // Verificación directa de contraseña sin usar método personalizado
                Log::info('AuthController: Verificación directa con password_verify', [
                    'email' => $request->email,
                    'hash_preview' => substr($hashedPassword, 0, 10) . '...'
                ]);
                
                $passwordMatch = password_verify($request->password, $hashedPassword);
                
                Log::info('AuthController: Resultado verificación directa', [
                    'email' => $request->email,
                    'match' => $passwordMatch
                ]);
                
                if (!$passwordMatch) {
                    Log::warning('Intento de login con contraseña incorrecta: ' . $request->email);
                    $this->securityLogger->logFailedLogin($request->email, $request->ip());
                    return back()->withErrors([
                        'password' => 'La contraseña es incorrecta.'
                    ])->withInput();
                }
            } catch (\Exception $e) {
                Log::error('AuthController: Error verificando contraseña', [
                    'email' => $request->email,
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors([
                    'email' => 'Error interno del servidor. Intente nuevamente.'
                ])->withInput();
            }
            
            // Migrar a Bcrypt si la contraseña no está en formato Bcrypt
            if ($cuentaModel->necesitaMigracionBcrypt($hashedPassword)) {
                Log::info('Migrando contraseña legacy a Bcrypt para: ' . $request->email);
                $cuentaModel->migrarPasswordBcrypt($cuenta['id'], $request->password);
            }

            // Verificar que la cuenta está activa
            if (isset($cuenta['estado']) && $cuenta['estado'] !== 'activa') {
                Log::warning('Cuenta inactiva: ' . $request->email);
                return back()->withErrors([
                    'general' => 'La cuenta está inactiva. Contacte al administrador.'
                ])->withInput();
            }

            // 3. Obtener perfil de la cuenta desde la base de datos cmym
            $perfil = \DB::connection('mongodb_cmym')
                         ->collection('perfiles')
                         ->where('cuenta_id', $cuenta['id'])
                         ->first();

            if (!$perfil) {
                Log::warning('Cuenta sin perfil asignado: ' . $request->email);
                return back()->withErrors([
                    'general' => 'La cuenta no tiene un perfil asignado. Contacte al administrador.'
                ])->withInput();
            }

            // 4. Obtener permisos específicos para esta cuenta
            $permisos = \DB::connection('mongodb_cmym')
                           ->collection('permisos')
                           ->where('cuenta_id', $cuenta['id'])
                           ->get()
                           ->toArray();

            // Extraer módulos y acciones de los permisos
            $modulosPermisos = [];
            $accionesPermitidas = [];
            foreach ($permisos as $permiso) {
                if (isset($permiso['modulo'])) {
                    $modulosPermisos[] = $permiso['modulo'];
                }
                if (isset($permiso['acciones']) && is_array($permiso['acciones'])) {
                    $accionesPermitidas = array_merge($accionesPermitidas, $permiso['acciones']);
                }
            }

            // 5. Crear sesión MongoDB
            $tokenSesion = generateBase64UrlId(32); // Token único para la sesión
            $sesionMongo = Sesion::createForAccount(
                $cuenta['id'],
                $empresaId,
                $tokenSesion,
                [
                    'ip' => $request->ip(),
                    'dispositivo' => $request->userAgent(),
                    'navegador' => $request->userAgent()
                ]
            );

            // Verificar si es SuperAdmin
            $isSuperAdmin = $this->checkSuperAdmin($cuenta, $perfil);

            // Establecer sesión Laravel completa con integración MongoDB
            Session::put('authenticated', true);
            Session::put('cuenta_id', $cuenta['id']);
            Session::put('session_id', $sesionMongo->id); // NUEVO: ID de sesión MongoDB
            Session::put('mongo_session_token', $sesionMongo->token);
            Session::put('empresa_id', $empresaId); // NUEVO: ID directo para consultas
            Session::put('user_data', [
                'id' => $cuenta['id'],
                'cuenta_id' => $cuenta['id'], // NUEVO: Para middleware
                'email' => $cuenta['email'],
                'nick' => $cuenta['nick'] ?? $perfil['nombre'] ?? 'Usuario',
                'nombre' => $perfil['nombre'] ?? $cuenta['nick'] ?? '',
                'apellido' => $perfil['apellido'] ?? '',
                'rol' => $cuenta['rol'] ?? 'usuario',
                'tipo' => $cuenta['tipo'] ?? 'cliente',
                'perfil_id' => $perfil['id'] ?? null,
                'permisos' => array_unique(array_merge(
                    $perfil['permisos'] ?? [],
                    $accionesPermitidas
                )),
                'modulos' => array_unique(array_merge(
                    $perfil['modulos'] ?? [],
                    $modulosPermisos
                )),
                'isSuperAdmin' => $isSuperAdmin,
                'empresas_acceso' => $empresasAcceso ?: [$empresaId],
                'empresa_id' => $empresaId, // NUEVO: Para fácil acceso
                'last_login' => now()
            ]);

            // Log successful authentication
            Log::info('Login exitoso: ' . $request->email . ' - Empresa: ' . $empresaData['razon_social'] . ' - Rol: ' . $cuenta['rol'] . ' - SuperAdmin: ' . ($isSuperAdmin ? 'SI' : 'NO') . ' - Sesión MongoDB: ' . $sesionMongo->id);
            $this->securityLogger->logSuccessfulLogin($request->email, $request->ip());

            // Regenerar ID de sesión por seguridad
            $request->session()->regenerate();

            // Verificar que la sesión se estableció correctamente
            Log::info('Sesión establecida correctamente:', [
                'authenticated' => Session::get('authenticated'),
                'user_id' => Session::get('user_data.id'),
                'user_email' => Session::get('user_data.email'),
                'empresa_id' => Session::get('empresa_data.id'),
                'empresa_nit' => Session::get('empresa_data.nit'),
                'empresa_nombre' => Session::get('empresa_data.razon_social')
            ]);

            // Verificar si hay URL preservada (por timeout) y redirigir
            $intendedUrl = $this->sessionService->getAndClearIntendedUrl();
            if ($intendedUrl) {
                Log::info('Redirigiendo a URL preservada', ['url' => $intendedUrl]);
                return redirect($intendedUrl);
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Error en autenticación de credenciales: ' . $e->getMessage());
            return back()->withErrors([
                'general' => 'Error interno del servidor. Intente nuevamente.'
            ])->withInput();
        }
    }

    /**
     * Logout user with MongoDB session cleanup
     */
    public function logout(Request $request)
    {
        $userEmail = Session::get('user_data.email', 'unknown');
        
        Log::info('Logout iniciado por usuario: ' . $userEmail);
        $this->securityLogger->logLogout($userEmail, $request->ip());

        // Usar el servicio centralizado para cerrar sesión
        $this->sessionService->closeSession();

        return redirect()->route('welcome')->with('success', 'Sesión cerrada exitosamente.');
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        return Session::get('authenticated', false) && 
               Session::has('user_data');
    }
    
    /**
     * Check if user has complete session (user + empresa)
     */
    public static function hasCompleteSession(): bool
    {
        return self::isAuthenticated() && 
               Session::has('empresa_data');
    }

    /**
     * Get current user data
     */
    public static function user(): ?array
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        $userData = Session::get('user_data');
        return $userData ? $userData : null;
    }

    /**
     * Get current empresa data
     */
    public static function empresa(): ?array
    {
        if (!Session::has('empresa_data')) {
            return null;
        }

        $empresaData = Session::get('empresa_data');
        return $empresaData ? $empresaData : null;
    }

    /**
     * Check if current user is SuperAdmin
     */
    private function checkSuperAdmin($cuenta, $perfil): bool
    {
        $superAdminConfig = config('app.super_admins', []);
        $superAdminEmails = $superAdminConfig['emails'] ?? [];
        $superAdminRoles = $superAdminConfig['roles'] ?? [];

        // Verificar por email específico de SuperAdmin
        if (in_array($cuenta['email'], $superAdminEmails)) {
            return true;
        }

        // Verificar por rol/perfil - SuperAdmin tiene acceso a TODAS las empresas
        // Verificar en cuenta.rol
        if (isset($cuenta['rol']) && in_array($cuenta['rol'], $superAdminRoles)) {
            return true;
        }

        // Verificar en perfil.ocupacion  
        if ($perfil && in_array($perfil['ocupacion'] ?? '', $superAdminRoles)) {
            return true;
        }

        return false;
    }

    /**
     * Get current user's role
     */
    public static function getUserRole(): string
    {
        $user = self::user();
        return $user->rol ?? 'usuario';
    }

    /**
     * Check if current user is SuperAdmin (static method)
     */
    public static function isSuperAdmin(): bool
    {
        $user = self::user();
        return $user ? ($user->isSuperAdmin ?? false) : false;
    }

    /**
     * Check if user has specific permission
     */
    public static function hasPermission(string $permission): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        // SuperAdmin tiene todos los permisos
        if ($user->isSuperAdmin ?? false) {
            return true;
        }

        $permisos = $user->permisos ?? [];
        return in_array($permission, $permisos);
    }

    /**
     * Check if user has access to module
     */
    public static function hasModuleAccess(string $module): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        // SuperAdmin tiene acceso a todos los módulos
        if ($user->isSuperAdmin ?? false) {
            return true;
        }

        $modulos = $user->modulos ?? [];
        return in_array($module, $modulos);
    }

    /**
     * Verificar si un recurso pertenece a la empresa actual del usuario
     */
    public static function resourceBelongsToCurrentEmpresa($resourceEmpresaId): bool
    {
        try {
            $empresaData = self::empresa();
            $userData = self::user();
            
            if (!$empresaData || !$userData) {
                return false;
            }

            // Super admin puede acceder a recursos de cualquier empresa
            if (isset($userData->isSuperAdmin) && $userData->isSuperAdmin) {
                return true;
            }

            // Usuario normal solo puede acceder a recursos de su empresa
            return $resourceEmpresaId === $empresaData->id;
        } catch (\Exception $e) {
            Log::error('Error verificando pertenencia de recurso a empresa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener el ID de la empresa actual del usuario autenticado
     */
    public static function getCurrentEmpresaId(): ?string
    {
        try {
            $empresaData = self::empresa();
            return $empresaData ? $empresaData['id'] : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verificar que el usuario tenga datos de empresa válidos
     */
    public static function hasValidEmpresa(): bool
    {
        try {
            $empresaData = self::empresa();
            return $empresaData && 
                   isset($empresaData->id) && 
                   !empty($empresaData->id) &&
                   isset($empresaData->nit) && 
                   !empty($empresaData->nit) &&
                   isset($empresaData->razon_social) && 
                   !empty($empresaData->razon_social);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Aplicar filtro de empresa a una consulta si no es super admin
     */
    public static function applyEmpresaFilter($query, $empresaIdField = 'empresa_id')
    {
        try {
            $userData = self::user();
            $empresaData = self::empresa();
            
            if (!$userData || !$empresaData) {
                // Si no hay datos de sesión, no retornar nada
                return $query->whereRaw('1 = 0');
            }

            // SEGURIDAD: TODOS los usuarios ven solo datos de su empresa actual
            // NO hay bypasses para SuperAdmin en el aislamiento de datos
            return $query->where($empresaIdField, $empresaData['id']);
        } catch (\Exception $e) {
            Log::error('Error aplicando filtro de empresa: ' . $e->getMessage());
            return $query->whereRaw('1 = 0');
        }
    }
}
