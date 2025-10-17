<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureEmpresaAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!\App\Http\Controllers\AuthController::isAuthenticated()) {
                Log::warning('Intento de acceso sin autenticación a ruta protegida: ' . $request->path());
                return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a esta sección.');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // SuperAdmin tiene acceso completo sin restricciones de empresa
            if (esSuperAdmin($userData)) {
                return $next($request);
            }

            // Verificar que existan datos de empresa en la sesión
            if (!$empresaData || !isset($empresaData['id'])) {
                Log::warning('Intento de acceso sin empresa válida por usuario: ' . ($userData['usuario'] ?? 'desconocido'));
                return redirect()->route('login')->with('error', 'No se ha seleccionado una empresa válida. Por favor, vuelva a iniciar sesión.');
            }

            // Verificar integridad de datos de empresa
            if (empty($empresaData['nit']) || empty($empresaData['razon_social'])) {
                Log::error('Datos de empresa incompletos en sesión: ' . json_encode($empresaData));
                return redirect()->route('login')->with('error', 'Datos de empresa incompletos. Por favor, vuelva a iniciar sesión.');
            }

            // Para rutas con parámetros de ID (show, edit, update, destroy)
            $routeParameters = $request->route()->parameters();
            
            if (!empty($routeParameters)) {
                foreach ($routeParameters as $parameter => $value) {
                    // Si el parámetro parece ser un ID de modelo
                    if (in_array($parameter, ['id', 'diagnostico', 'hallazgo', 'hoja']) && !empty($value)) {
                        // Validar acceso según el tipo de recurso
                        if (!$this->validateResourceAccess($parameter, $value, $userData, $empresaData)) {
                            Log::warning("Intento de acceso no autorizado al recurso $parameter:$value por usuario: " . ($userData['usuario'] ?? 'desconocido') . " de empresa: " . $empresaData['nit']);
                            return redirect()->back()->with('error', 'No tiene permisos para acceder a este recurso.');
                        }
                    }
                }
            }

            // Agregar headers de seguridad adicionales
            $response = $next($request);
            
            if (method_exists($response, 'header')) {
                $response->header('X-Empresa-ID', $empresaData['id']);
                $response->header('X-Content-Security', 'empresa-filtered');
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Error en middleware EnsureEmpresaAccess: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Error de seguridad. Por favor, vuelva a iniciar sesión.');
        }
    }

    /**
     * Validar acceso a un recurso específico
     */
    private function validateResourceAccess($parameterType, $resourceId, $userData, $empresaData)
    {
        try {
            // SuperAdmin tiene acceso completo a todos los recursos
            if (esSuperAdmin($userData)) {
                return true;
            }

            // SEGURIDAD: TODOS los demás usuarios solo pueden acceder a recursos de su empresa actual

            switch ($parameterType) {
                case 'diagnostico':
                case 'id':
                    // Para diagnósticos psicosociales
                    $diagnostico = \App\Models\Diagnostico::withoutGlobalScope('empresa')->find($resourceId);
                    if ($diagnostico) {
                        return $diagnostico->empresa_id === $empresaData['id'];
                    }
                    break;

                case 'hallazgo':
                    // Para hallazgos
                    $hallazgo = \App\Models\Hallazgo::withoutGlobalScope('empresa')->find($resourceId);
                    if ($hallazgo) {
                        return $hallazgo->empresa_id === $empresaData['id'];
                    }
                    break;

                case 'hoja':
                    // Para hojas psicosociales
                    $hoja = \App\Models\Hoja::withoutGlobalScope('empresa')->find($resourceId);
                    if ($hoja) {
                        return $hoja->empresa_id === $empresaData['id'];
                    }
                    break;
            }

            // Si no se puede validar, denegar acceso por seguridad
            return false;

        } catch (\Exception $e) {
            Log::error("Error validando acceso a recurso $parameterType:$resourceId - " . $e->getMessage());
            return false;
        }
    }
}
