<?php

namespace App\Http\Controllers\Modules\Configuracion\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait para manejar errores de configuración de forma consistente
 * en todos los controladores del módulo de configuración
 */
trait HandlesConfigurationErrors
{
    /**
     * Obtener datos de empresa desde sesión con valores por defecto seguros
     */
    protected function getEmpresaDataSafe()
    {
        $empresaData = session('empresa_data');
        
        if (!$empresaData || !isset($empresaData['id'])) {
            Log::warning(class_basename($this) . ': No hay empresa_data en sesión');
            return [
                'id' => null,
                'nombre' => 'Sin empresa',
                'razon_social' => 'N/A',
                'nit' => 'N/A',
                'has_data' => false
            ];
        }
        
        return array_merge($empresaData, ['has_data' => true]);
    }
    
    /**
     * Obtener objeto de empresa desde MongoDB con manejo de errores
     */
    protected function getEmpresaModelSafe($empresaId = null)
    {
        if (!$empresaId) {
            $empresaData = $this->getEmpresaDataSafe();
            $empresaId = $empresaData['id'] ?? null;
        }
        
        if (!$empresaId) {
            return $this->createEmptyEmpresaObject();
        }
        
        try {
            $empresa = \App\Models\Empresa::where('id', $empresaId)->first();
            
            if (!$empresa) {
                Log::warning(class_basename($this) . ': Empresa no encontrada en BD: ' . $empresaId);
                $empresaData = session('empresa_data');
                return $this->createEmpresaObjectFromSession($empresaData);
            }
            
            return $empresa;
            
        } catch (\Exception $e) {
            Log::error(class_basename($this) . ': Error conectando a MongoDB: ' . $e->getMessage());
            $empresaData = session('empresa_data');
            return $this->createEmpresaObjectFromSession($empresaData);
        }
    }
    
    /**
     * Crear objeto empresa vacío
     */
    protected function createEmptyEmpresaObject()
    {
        return (object)[
            'id' => null,
            'nombre' => 'Sin empresa',
            'razon_social' => 'N/A',
            'nit' => 'N/A',
            'direccion' => '',
            'telefono' => '',
            'email' => '',
            'sitio_web' => '',
            'colorPrimario' => '#007bff',
            'colorSecundario' => '#6c757d',
            'logo' => null
        ];
    }
    
    /**
     * Crear objeto empresa desde datos de sesión
     */
    protected function createEmpresaObjectFromSession($empresaData)
    {
        if (!$empresaData) {
            return $this->createEmptyEmpresaObject();
        }
        
        return (object)[
            'id' => $empresaData['id'] ?? null,
            'nombre' => $empresaData['nombre'] ?? 'Empresa',
            'razon_social' => $empresaData['razon_social'] ?? 'N/A',
            'nit' => $empresaData['nit'] ?? 'N/A',
            'direccion' => $empresaData['direccion'] ?? '',
            'telefono' => $empresaData['telefono'] ?? '',
            'email' => $empresaData['email'] ?? '',
            'sitio_web' => $empresaData['sitio_web'] ?? '',
            'colorPrimario' => $empresaData['colorPrimario'] ?? '#007bff',
            'colorSecundario' => $empresaData['colorSecundario'] ?? '#6c757d',
            'logo' => $empresaData['logo'] ?? null
        ];
    }
    
    /**
     * Registrar error y preparar vista de fallback
     */
    protected function logErrorAndPrepareView($exception, $viewName, $defaultData = [])
    {
        Log::error(class_basename($this) . ' - Error: ' . $exception->getMessage());
        Log::error('Stack trace: ' . $exception->getTraceAsString());
        
        $fallbackData = array_merge([
            'empresa' => $this->getEmpresaModelSafe(),
            'usuario' => session('user_data'),
            'userData' => session('user_data'),
            'database_available' => false,
            'error_message' => 'No se pudo cargar la configuración desde la base de datos. Mostrando valores por defecto.'
        ], $defaultData);
        
        try {
            return view($viewName, $fallbackData);
        } catch (\Exception $viewException) {
            Log::error(class_basename($this) . ' - Error crítico en fallback: ' . $viewException->getMessage());
            return back()->with('error', 'Error crítico al cargar la configuración');
        }
    }
    
    /**
     * Verificar si hay conexión a base de datos disponible
     */
    protected function isDatabaseAvailable()
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::warning(class_basename($this) . ': Base de datos no disponible - ' . $e->getMessage());
            return false;
        }
    }
}
