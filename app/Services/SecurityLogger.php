<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SecurityLogger
{
    /**
     * Log de intento de autenticación
     */
    public static function logAuthAttempt(Request $request, string $username, bool $success): void
    {
        $data = [
            'event' => 'auth_attempt',
            'username' => $username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success' => $success,
            'timestamp' => now()->toISOString(),
        ];

        if ($success) {
            Log::info('Successful authentication attempt', $data);
        } else {
            Log::warning('Failed authentication attempt', $data);
        }
    }

    /**
     * Log de actividad sospechosa
     */
    public static function logSuspiciousActivity(Request $request, string $reason, array $context = []): void
    {
        $data = array_merge([
            'event' => 'suspicious_activity',
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::warning('Suspicious activity detected', $data);
    }

    /**
     * Log de acceso a datos sensibles
     */
    public static function logDataAccess(Request $request, string $resource, string $action, $userId = null): void
    {
        $data = [
            'event' => 'data_access',
            'resource' => $resource,
            'action' => $action,
            'user_id' => $userId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        Log::info('Data access logged', $data);
    }

    /**
     * Log de cambios de configuración
     */
    public static function logConfigurationChange(Request $request, array $changes, $userId): void
    {
        $data = [
            'event' => 'configuration_change',
            'changes' => $changes,
            'user_id' => $userId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        Log::warning('Configuration changed', $data);
    }

    /**
     * Log de intentos de upload maliciosos
     */
    public static function logMaliciousUpload(Request $request, string $filename, string $reason): void
    {
        $data = [
            'event' => 'malicious_upload',
            'filename' => $filename,
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        Log::error('Malicious upload attempt', $data);
    }

    /**
     * Log de rate limiting
     */
    public static function logRateLimitExceeded(Request $request, string $type): void
    {
        $data = [
            'event' => 'rate_limit_exceeded',
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now()->toISOString(),
        ];

        Log::warning('Rate limit exceeded', $data);
    }

    /**
     * Log de login fallido
     */
    public function logFailedLogin(string $email, string $ip): void
    {
        $data = [
            'event' => 'failed_login',
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now()->toISOString(),
        ];

        Log::warning('Failed login attempt', $data);
    }

    /**
     * Log de login exitoso
     */
    public function logSuccessfulLogin(string $email, string $ip): void
    {
        $data = [
            'event' => 'successful_login',
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now()->toISOString(),
        ];

        Log::info('Successful login', $data);
    }

    /**
     * Log de logout
     */
    public function logLogout(string $email, string $ip): void
    {
        $data = [
            'event' => 'logout',
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now()->toISOString(),
        ];

        Log::info('User logout', $data);
    }

    /**
     * Obtener logs de seguridad recientes (stub)
     */
    public function getRecentLogs($empresaId, $limit = 20)
    {
        // TODO: Implementar lógica real de obtención de logs
        // Ejemplo: return SecurityLog::where('empresa_id', $empresaId)->orderBy('created_at', 'desc')->limit($limit)->get();
        return collect([]); // Devuelve colección vacía por defecto
    }
}
