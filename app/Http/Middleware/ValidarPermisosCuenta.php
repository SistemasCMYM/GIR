<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Cuenta;
use App\Models\Sesion;
use Illuminate\Support\Facades\Log;

// Instrumentación: registro de entrada para ayudar a depurar logout inesperado

class ValidarPermisosCuenta
{
    /**
     * Manejar una solicitud entrante.
     * Valida permisos según los schemas de Node.js especificados
     */
    public function handle(Request $request, Closure $next, $modulo = null, $accion = 'read')
    {
        try {
            // Obtener datos de sesión (soportar distintas claves usadas en el proyecto)
            $userData = session('user_data') ?? session('usuario_data') ?? session('usuario') ?? null;

            // Empresa puede almacenarse como 'empresa', 'empresa_data' o sólo 'empresa_id'
            $empresa = session('empresa') ?? session('empresa_data') ?? null;
            $empresaId = session('empresa_id') ?? null;
            if (!$empresaId && $empresa) {
                if (is_array($empresa) && isset($empresa['id'])) {
                    $empresaId = $empresa['id'];
                } elseif (is_object($empresa) && isset($empresa->id)) {
                    $empresaId = $empresa->id;
                }
            }
            
            // Normalizar posibles nombres de campo para cuenta_id
            $cuentaIdFromSession = null;
            if (is_array($userData)) {
                $cuentaIdFromSession = $userData['cuenta_id'] ?? $userData['cuentaId'] ?? $userData['id'] ?? null;
            } elseif (is_object($userData)) {
                $cuentaIdFromSession = $userData->cuenta_id ?? $userData->cuentaId ?? $userData->id ?? null;
            }

            if (!$userData || !$cuentaIdFromSession) {
                Log::warning('ValidarPermisosCuenta: Datos de sesión no válidos', [
                    'user_data_exists' => $userData ? 'SI' : 'NO',
                    'cuenta_id_present' => $cuentaIdFromSession ? 'SI' : 'NO',
                    'route' => $request->route()?->getName(),
                    'accion' => 'verificar_auth_controller'
                ]);
                
                // Verificar si realmente no está autenticado según AuthController
                if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                    return redirect()->route('auth.nit.form')->withErrors(['error' => 'Sesión inválida']);
                }
                
                // Si AuthController dice que está autenticado pero faltan datos de sesión, redirigir al dashboard
                return redirect()->route('dashboard')->withErrors(['error' => 'Datos de sesión incompletos']);
            }
            
            // Verificar que la cuenta existe y está activa
            $cuenta = null;
            if ($cuentaIdFromSession) {
                $cuenta = Cuenta::find($cuentaIdFromSession);
            }
            if (!$cuenta || $cuenta->estado !== 'activa') {
                Log::warning('ValidarPermisosCuenta: Cuenta inactiva o no encontrada', [
                    'cuenta_id' => $cuentaIdFromSession ?? 'N/A',
                    'cuenta_exists' => $cuenta ? 'SI' : 'NO',
                    'cuenta_estado' => $cuenta->estado ?? 'N/A',
                    'route' => $request->route()?->getName(),
                    'accion' => 'redirigir_dashboard_sin_limpiar_sesion'
                ]);
                // No limpiar la sesión para evitar logout inesperado; solo redirigir al dashboard
                return redirect()->route('dashboard')->withErrors(['error' => 'Su cuenta no está activa. Contacte al administrador.']);
            }
            
            // SuperAdmin tiene acceso completo
            if (esSuperAdmin($cuenta)) {
                return $next($request);
            }
            
            // Verificar acceso a empresa si se especifica
            if ($empresaId && !tieneAccesoEmpresa($cuenta, $empresaId)) {
                return redirect()->route('dashboard')->withErrors(['error' => 'Sin acceso a esta empresa']);
            }
            
            // Si no se especifica módulo, permitir acceso (solo validación de sesión)
            if (!$modulo) {
                return $next($request);
            }
            
            // Verificar permisos específicos del módulo
            $tienePermiso = $this->verificarPermisoModulo($cuenta, $modulo, $accion);
            
            if (!$tienePermiso) {
                return redirect()->route('dashboard')->withErrors([
                    'error' => "No tienes permisos para acceder al módulo: {$modulo}"
                ]);
            }
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Error en middleware ValidarPermisosCuenta: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Error validando permisos']);
        }
    }
    
    /**
     * Verificar si la cuenta tiene permiso para un módulo específico
     */
    private function verificarPermisoModulo(Cuenta $cuenta, string $modulo, string $accion): bool
    {
        try {
            // Obtener configuración del rol desde los schemas del sistema
            $configuracionRol = getRoleConfiguration($cuenta->rol);

            // Si no hay configuración estática para el rol, intentar inferir permisos desde
            // el perfil asociado o desde la sesión (casos donde los nombres de roles difieren)
            if (!$configuracionRol) {
                // Revisar modulos/permisos en perfil
                $perfil = $cuenta->perfil ?? null;
                $perfilModulos = [];
                $perfilPermisos = [];
                if ($perfil) {
                    if (is_array($perfil->modulos)) {
                        $perfilModulos = $perfil->modulos;
                    } elseif (is_string($perfil->modulos)) {
                        $perfilModulos = array_filter(array_map('trim', explode(',', $perfil->modulos)));
                    }
                    if (is_array($perfil->permisos)) {
                        $perfilPermisos = $perfil->permisos;
                    } elseif (is_string($perfil->permisos)) {
                        $perfilPermisos = array_filter(array_map('trim', explode(',', $perfil->permisos)));
                    }
                }

                // Revisar en sesión (user_data)
                $sessionUser = session('user_data') ?? [];
                $sessionModulos = $sessionUser['modulos'] ?? $sessionUser['modules'] ?? [];
                $sessionPermisos = $sessionUser['permisos'] ?? [];

                // Normalizar a arrays
                if (is_string($sessionModulos)) {
                    $sessionModulos = array_filter(array_map('trim', explode(',', $sessionModulos)));
                }
                if (is_string($sessionPermisos)) {
                    $sessionPermisos = array_filter(array_map('trim', explode(',', $sessionPermisos)));
                }

                // Alias de módulos comunes
                $moduloAliases = [
                    'usuarios' => ['usuarios', 'administracion', 'admin', 'autenticacion', 'perfiles'],
                    'empresas' => ['empresas', 'empresa'],
                ];

                $aliases = $moduloAliases[$modulo] ?? [$modulo];

                // Si alguno de los arrays de modulos/permisos contiene el módulo o un alias, permitir
                foreach (array_merge($perfilModulos, $sessionModulos) as $m) {
                    if (in_array($m, $aliases)) {
                        return true;
                    }
                }
                foreach (array_merge($perfilPermisos, $sessionPermisos) as $p) {
                    if (strpos($p, $modulo) !== false || in_array($p, $aliases)) {
                        return true;
                    }
                }

                // Si no podemos inferir, no cerrar de forma definitiva; devolver false para continuar con la ruta segura
                return false;
            }
            
            // Verificar si el rol tiene acceso al módulo
            $modulosPermitidos = $configuracionRol['modulos'] ?? [];
            if (!in_array($modulo, $modulosPermitidos)) {
                return false;
            }
            
            // Verificar si el rol tiene la acción específica
            $accionesPermitidas = $configuracionRol['acciones'] ?? [];
            
            // Mapeo de acciones
            $mapeoAcciones = [
                'read' => ['leer', 'read'],
                'create' => ['crear', 'create'],
                'update' => ['actualizar', 'update'],
                'delete' => ['eliminar', 'delete'],
                'export' => ['exportar', 'export'],
                'admin' => ['admin', 'configurar']
            ];
            
            $accionesRequeridas = $mapeoAcciones[$accion] ?? [$accion];
            
            foreach ($accionesRequeridas as $accionRequerida) {
                if (in_array($accionRequerida, $accionesPermitidas)) {
                    return true;
                }
            }
            
            // Si tiene permiso 'all', puede hacer todo
            if (in_array('all', $configuracionRol['permisos'] ?? [])) {
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error verificando permisos de módulo: ' . $e->getMessage());
            return false;
        }
    }
}
