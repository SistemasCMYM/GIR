<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SessionManagementService;

/**
 * Middleware de validación completa de sesión integrado con esquemas centralizados
 * 
 * Funcionalidades:
 * 1. Validación de NIT y credenciales de cuenta
 * 2. Validación de perfil por cuenta_id
 * 3. Validación de permisos por cuenta_id
 * 4. Redirección a Dashboard si sesión activa
 * 5. Redirección a Landing si sesión cerrada
 * 6. Re-autenticación si excede límite de inactividad
 * 7. Preservación de ubicación tras timeout
 */
class SessionValidationMiddleware
{
    /**
     * Servicio de gestión de sesión
     */
    private SessionManagementService $sessionService;

    /**
     * Rutas que no requieren autenticación completa
     */
    private const EXCLUDED_ROUTES = [
        'welcome',
        'auth.landing',
        'auth.nit.form', 
        'auth.nit.verify',
        'login.credentials',
        'login.verify',
        'logout'
    ];

    /**
     * Constructor
     */
    public function __construct(SessionManagementService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $currentRoute = $request->route()?->getName();
            
            Log::info('SessionValidationMiddleware: Validando sesión', [
                'route' => $currentRoute,
                'path' => $request->path(),
                'user_ip' => $request->ip()
            ]);

            // Omitir validación para rutas excluidas
            if (in_array($currentRoute, self::EXCLUDED_ROUTES)) {
                Log::info('SessionValidationMiddleware: Ruta excluida, pasando sin validación');
                return $next($request);
            }

            // Validar sesión completa usando el servicio centralizado
            $validationResult = $this->sessionService->validateFullSession();

            if (!$validationResult['valid']) {
                Log::warning('SessionValidationMiddleware: Validación de sesión falló', [
                    'error' => $validationResult['error'],
                    'message' => $validationResult['message']
                ]);

                return $this->handleValidationFailure($request, $validationResult);
            }

            Log::info('SessionValidationMiddleware: Validación exitosa, continuando');
            return $next($request);

        } catch (\Exception $e) {
            Log::error('SessionValidationMiddleware: Error durante validación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->redirectToLogin($request, 'Error interno. Por favor, inicie sesión nuevamente.');
        }
    }

    /**
     * Manejar falla en validación
     */
    private function handleValidationFailure(Request $request, array $validationResult)
    {
        $error = $validationResult['error'] ?? 'unknown_error';
        $message = $validationResult['message'] ?? 'Error de validación';
        $redirect = $validationResult['redirect'] ?? 'auth.nit.form';

        // Preservar ubicación para timeouts de inactividad
        if (isset($validationResult['preserve_location']) && $validationResult['preserve_location']) {
            $this->sessionService->preserveIntendedUrl($request->fullUrl());
        }

        // Cerrar sesión si es necesario
        if (in_array($error, ['account_inactive', 'no_company_access', 'mongo_session_not_found', 'inactivity_timeout', 'session_lifetime_timeout'])) {
            $this->sessionService->closeSession();
        }

        // Manejar respuesta según tipo de request
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $error,
                'message' => $message,
                'redirect' => route($redirect),
                'timeout_type' => $validationResult['timeout_type'] ?? null
            ], 401);
        }

        $redirectResponse = redirect()->route($redirect)->with('error', $message);

        // Agregar información adicional para timeouts
        if (isset($validationResult['timeout_type'])) {
            $redirectResponse->with('timeout_type', $validationResult['timeout_type']);
            
            if ($validationResult['timeout_type'] === 'inactivity') {
                $redirectResponse->with('warning', 'Su sesión expiró por inactividad.');
            }
        }

        return $redirectResponse;
    }

    /**
     * Redireccionar a login general
     */
    private function redirectToLogin(Request $request, string $message = null)
    {
        $this->sessionService->closeSession();

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'authentication_required',
                'message' => $message ?: 'Debe iniciar sesión.',
                'redirect' => route('auth.nit.form')
            ], 401);
        }

        return redirect()->route('auth.nit.form')
                        ->with('error', $message ?: 'Debe iniciar sesión para acceder.');
    }
}
