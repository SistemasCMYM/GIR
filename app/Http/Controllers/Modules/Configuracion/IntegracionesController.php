<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empresa;

class IntegracionesController extends Controller
{
    /**
     * Mostrar configuración de integraciones
     */
    public function index()
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData) {
                return redirect()->route('dashboard')->with('error', 'No hay empresa seleccionada');
            }
            
            // Obtener empresa actual desde MongoDB
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('dashboard')->with('error', 'Empresa no encontrada');
            }
            
            // Configuración actual de integraciones
            $configuracion = $empresa->configuracion_integraciones ?? [
                'apis_habilitadas' => false,
                'claves_api' => [],
                'webhooks' => [],
                'conexiones_externas' => [],
                'sso_habilitado' => false,
                'sso_proveedores' => []
            ];
            
            return view('modules.configuracion.integraciones.index', compact('empresa', 'configuracion', 'userData'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de integraciones: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración de integraciones');
        }
    }
    
    /**
     * Actualizar configuración general de integraciones
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'apis_habilitadas' => 'boolean',
                'sso_habilitado' => 'boolean'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_integraciones)) {
                $empresa->configuracion_integraciones = [];
            }
            
            // Preservar configuraciones existentes
            $clavesExistentes = $empresa->configuracion_integraciones['claves_api'] ?? [];
            $webhooksExistentes = $empresa->configuracion_integraciones['webhooks'] ?? [];
            $conexionesExistentes = $empresa->configuracion_integraciones['conexiones_externas'] ?? [];
            $ssoProveedores = $empresa->configuracion_integraciones['sso_proveedores'] ?? [];
            
            // Actualizar configuración
            $empresa->configuracion_integraciones = [
                'apis_habilitadas' => $request->boolean('apis_habilitadas'),
                'claves_api' => $clavesExistentes,
                'webhooks' => $webhooksExistentes,
                'conexiones_externas' => $conexionesExistentes,
                'sso_habilitado' => $request->boolean('sso_habilitado'),
                'sso_proveedores' => $ssoProveedores
            ];
            
            $empresa->save();
            
            return back()->with('success', 'Configuración general de integraciones actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de integraciones: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de integraciones');
        }
    }
    
    /**
     * Gestionar claves API
     */
    public function gestionarClavesApi()
    {
        try {
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('dashboard')->with('error', 'Empresa no encontrada');
            }
            
            // Obtener claves API existentes
            $clavesApi = $empresa->configuracion_integraciones['claves_api'] ?? [];
            
            return view('modules.configuracion.integraciones.claves_api', compact('empresa', 'clavesApi'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de claves API: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la gestión de claves API');
        }
    }
    
    /**
     * Generar nueva clave API
     */
    public function generarClaveApi(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'permisos' => 'required|array',
                'permisos.*' => 'in:lectura,escritura,hallazgos,psicosocial,reportes'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_integraciones)) {
                $empresa->configuracion_integraciones = [
                    'apis_habilitadas' => true,
                    'claves_api' => []
                ];
            }
            
            // Inicializar claves API si no existen
            if (!isset($empresa->configuracion_integraciones['claves_api'])) {
                $empresa->configuracion_integraciones['claves_api'] = [];
            }
            
            // Generar nueva clave API
            $claveApi = bin2hex(random_bytes(16)); // 32 caracteres hex
            $prefijoEmpresa = substr(preg_replace('/[^a-zA-Z0-9]/', '', $empresa->nombre), 0, 3);
            $apiKey = strtoupper($prefijoEmpresa) . '_' . $claveApi;
            
            // Crear nueva clave
            $nuevaClave = [
                'id' => uniqid('api_'),
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'clave' => $apiKey,
                'permisos' => $request->permisos,
                'activa' => true,
                'fecha_creacion' => now(),
                'fecha_expiracion' => now()->addYear(),
                'ultimo_uso' => null,
                'creada_por' => session('user_data')['id'] ?? null
            ];
            
            $empresa->configuracion_integraciones['claves_api'][] = $nuevaClave;
            $empresa->configuracion_integraciones['apis_habilitadas'] = true;
            
            $empresa->save();
            
            return back()->with('success', 'Clave API generada correctamente')->with('api_key', $apiKey);
            
        } catch (\Exception $e) {
            Log::error('Error al generar clave API: ' . $e->getMessage());
            return back()->with('error', 'Error al generar la clave API');
        }
    }
    
    /**
     * Revocar clave API
     */
    public function revocarClaveApi(Request $request)
    {
        try {
            $request->validate([
                'clave_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_integraciones['claves_api'])) {
                return back()->with('error', 'Empresa o claves API no encontradas');
            }
            
            // Buscar y desactivar la clave
            foreach ($empresa->configuracion_integraciones['claves_api'] as $key => $clave) {
                if ($clave['id'] == $request->clave_id) {
                    $empresa->configuracion_integraciones['claves_api'][$key]['activa'] = false;
                    $empresa->configuracion_integraciones['claves_api'][$key]['fecha_revocacion'] = now();
                    $empresa->configuracion_integraciones['claves_api'][$key]['revocada_por'] = session('user_data')['id'] ?? null;
                }
            }
            
            $empresa->save();
            
            return back()->with('success', 'Clave API revocada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al revocar clave API: ' . $e->getMessage());
            return back()->with('error', 'Error al revocar la clave API');
        }
    }
    
    /**
     * Gestionar webhooks
     */
    public function gestionarWebhooks()
    {
        try {
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('dashboard')->with('error', 'Empresa no encontrada');
            }
            
            // Obtener webhooks existentes
            $webhooks = $empresa->configuracion_integraciones['webhooks'] ?? [];
            
            return view('modules.configuracion.integraciones.webhooks', compact('empresa', 'webhooks'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de webhooks: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la gestión de webhooks');
        }
    }
    
    /**
     * Guardar webhook
     */
    public function guardarWebhook(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'url' => 'required|url|max:255',
                'eventos' => 'required|array',
                'eventos.*' => 'in:hallazgo.creado,hallazgo.actualizado,hallazgo.cerrado,psicosocial.iniciado,psicosocial.completado,usuario.creado',
                'activo' => 'boolean',
                'webhook_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_integraciones)) {
                $empresa->configuracion_integraciones = [
                    'apis_habilitadas' => true,
                    'webhooks' => []
                ];
            }
            
            // Inicializar webhooks si no existen
            if (!isset($empresa->configuracion_integraciones['webhooks'])) {
                $empresa->configuracion_integraciones['webhooks'] = [];
            }
            
            // Generar secreto para verificación
            $secreto = $request->webhook_id ? null : bin2hex(random_bytes(16)); // 32 caracteres hex
            
            // Editar o crear nuevo webhook
            if ($request->webhook_id) {
                // Editar webhook existente
                foreach ($empresa->configuracion_integraciones['webhooks'] as $key => $webhook) {
                    if ($webhook['id'] == $request->webhook_id) {
                        $empresa->configuracion_integraciones['webhooks'][$key]['nombre'] = $request->nombre;
                        $empresa->configuracion_integraciones['webhooks'][$key]['url'] = $request->url;
                        $empresa->configuracion_integraciones['webhooks'][$key]['eventos'] = $request->eventos;
                        $empresa->configuracion_integraciones['webhooks'][$key]['activo'] = $request->boolean('activo');
                        $empresa->configuracion_integraciones['webhooks'][$key]['actualizado_en'] = now();
                        // No actualizar el secreto
                    }
                }
            } else {
                // Crear nuevo webhook
                $nuevoWebhook = [
                    'id' => uniqid('webhook_'),
                    'nombre' => $request->nombre,
                    'url' => $request->url,
                    'eventos' => $request->eventos,
                    'secreto' => $secreto,
                    'activo' => $request->boolean('activo'),
                    'creado_en' => now(),
                    'actualizado_en' => now(),
                    'creado_por' => session('user_data')['id'] ?? null,
                    'fallos' => 0,
                    'ultimo_envio' => null
                ];
                
                $empresa->configuracion_integraciones['webhooks'][] = $nuevoWebhook;
            }
            
            $empresa->save();
            
            $mensaje = $request->webhook_id 
                ? 'Webhook actualizado correctamente' 
                : 'Webhook creado correctamente. Guarde el secreto: ' . $secreto;
            
            return back()->with('success', $mensaje)->with('webhook_secreto', $secreto);
            
        } catch (\Exception $e) {
            Log::error('Error al guardar webhook: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el webhook');
        }
    }
    
    /**
     * Eliminar webhook
     */
    public function eliminarWebhook(Request $request)
    {
        try {
            $request->validate([
                'webhook_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_integraciones['webhooks'])) {
                return back()->with('error', 'Empresa o webhooks no encontrados');
            }
            
            // Filtrar webhooks para remover el seleccionado
            $empresa->configuracion_integraciones['webhooks'] = array_filter(
                $empresa->configuracion_integraciones['webhooks'], 
                function($webhook) use ($request) {
                    return $webhook['id'] != $request->webhook_id;
                }
            );
            
            // Reindexar el array
            $empresa->configuracion_integraciones['webhooks'] = array_values(
                $empresa->configuracion_integraciones['webhooks']
            );
            
            $empresa->save();
            
            return back()->with('success', 'Webhook eliminado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar webhook: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el webhook');
        }
    }
    
    /**
     * Configuración de SSO
     */
    public function configurarSSO()
    {
        try {
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('dashboard')->with('error', 'Empresa no encontrada');
            }
            
            // Obtener configuración SSO
            $configuracion = $empresa->configuracion_integraciones ?? [];
            $ssoHabilitado = $configuracion['sso_habilitado'] ?? false;
            $ssoProveedores = $configuracion['sso_proveedores'] ?? [];
            
            return view('modules.configuracion.integraciones.sso', compact('empresa', 'ssoHabilitado', 'ssoProveedores'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración SSO: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración SSO');
        }
    }
    
    /**
     * Guardar proveedor SSO
     */
    public function guardarProveedorSSO(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'tipo' => 'required|in:saml2,oauth2,ldap,oidc',
                'config' => 'required|array',
                'activo' => 'boolean',
                'proveedor_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar configuración si no existe
            if (!isset($empresa->configuracion_integraciones)) {
                $empresa->configuracion_integraciones = [
                    'sso_habilitado' => true,
                    'sso_proveedores' => []
                ];
            }
            
            // Inicializar proveedores SSO si no existen
            if (!isset($empresa->configuracion_integraciones['sso_proveedores'])) {
                $empresa->configuracion_integraciones['sso_proveedores'] = [];
            }
            
            // Editar o crear nuevo proveedor SSO
            if ($request->proveedor_id) {
                // Editar proveedor existente
                foreach ($empresa->configuracion_integraciones['sso_proveedores'] as $key => $proveedor) {
                    if ($proveedor['id'] == $request->proveedor_id) {
                        $empresa->configuracion_integraciones['sso_proveedores'][$key]['nombre'] = $request->nombre;
                        $empresa->configuracion_integraciones['sso_proveedores'][$key]['tipo'] = $request->tipo;
                        $empresa->configuracion_integraciones['sso_proveedores'][$key]['config'] = $request->config;
                        $empresa->configuracion_integraciones['sso_proveedores'][$key]['activo'] = $request->boolean('activo');
                        $empresa->configuracion_integraciones['sso_proveedores'][$key]['actualizado_en'] = now();
                    }
                }
            } else {
                // Crear nuevo proveedor
                $nuevoProveedor = [
                    'id' => uniqid('sso_'),
                    'nombre' => $request->nombre,
                    'tipo' => $request->tipo,
                    'config' => $request->config,
                    'activo' => $request->boolean('activo'),
                    'creado_en' => now(),
                    'actualizado_en' => now(),
                    'creado_por' => session('user_data')['id'] ?? null
                ];
                
                $empresa->configuracion_integraciones['sso_proveedores'][] = $nuevoProveedor;
            }
            
            // Asegurar que SSO esté habilitado
            $empresa->configuracion_integraciones['sso_habilitado'] = true;
            
            $empresa->save();
            
            return back()->with('success', 'Proveedor SSO guardado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al guardar proveedor SSO: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el proveedor SSO');
        }
    }
    
    /**
     * Eliminar proveedor SSO
     */
    public function eliminarProveedorSSO(Request $request)
    {
        try {
            $request->validate([
                'proveedor_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->configuracion_integraciones['sso_proveedores'])) {
                return back()->with('error', 'Empresa o proveedores SSO no encontrados');
            }
            
            // Filtrar proveedores para remover el seleccionado
            $empresa->configuracion_integraciones['sso_proveedores'] = array_filter(
                $empresa->configuracion_integraciones['sso_proveedores'], 
                function($proveedor) use ($request) {
                    return $proveedor['id'] != $request->proveedor_id;
                }
            );
            
            // Reindexar el array
            $empresa->configuracion_integraciones['sso_proveedores'] = array_values(
                $empresa->configuracion_integraciones['sso_proveedores']
            );
            
            // Si no quedan proveedores, deshabilitar SSO
            if (empty($empresa->configuracion_integraciones['sso_proveedores'])) {
                $empresa->configuracion_integraciones['sso_habilitado'] = false;
            }
            
            $empresa->save();
            
            return back()->with('success', 'Proveedor SSO eliminado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar proveedor SSO: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el proveedor SSO');
        }
    }
}
