<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Empresas\Empresa;
use App\Services\ConfiguracionService;
use App\Models\Configuracion\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{    protected $configuracionService;

    public function __construct(ConfiguracionService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Mostrar configuración de empresa
     */
    public function index()
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData || !isset($empresaData['id'])) {
                // Si no hay datos de empresa, crear estructura básica
                $empresa = $this->createEmptyEmpresaStructure();
                $configuracionesEmpresa = collect([]);
                Log::info('EmpresaController: No hay empresa_data, usando estructura vacía');
            } else {
                // Obtener empresa actual desde MongoDB
                try {
                    $empresa = Empresa::where('id', $empresaData['id'])->first();
                    
                    if (!$empresa) {
                        Log::warning('Empresa no encontrada en la Base de Datos: ' . $empresaData['id']);
                        $empresa = $this->createEmpresaFromSession($empresaData);
                    }
                } catch (\Exception $e) {
                    Log::error('Error conectando a MongoDB para empresa: ' . $e->getMessage());
                    $empresa = $this->createEmpresaFromSession($empresaData);
                }

                // Obtener configuraciones específicas de la empresa (siempre devuelve collect, nunca excepción)
                $configuracionesEmpresa = $this->getEmpresaConfigurations($empresaData['id']);
            }

            // Estructurar datos según el patrón de referencia (businesscorp)
            $configData = [
                'empresa' => $empresa,
                'usuario' => $userData,
                'configuraciones' => $this->formatConfigurationsForView($configuracionesEmpresa),
                'tabs' => $this->getEmpresaTabs(),
                'formatos' => $this->getFormatosDisponibles(),
                'zonas_horarias' => $this->getZonasHorarias(),
                'monedas' => $this->getMonedasDisponibles(),
                'idiomas' => $this->getIdiomasDisponibles(),
                'configuraciones_hallazgos' => $this->getConfiguracionesHallazgos($empresaData['id'] ?? null),
                'configuraciones_psicosocial' => $this->getConfiguracionesPsicosocial($empresaData['id'] ?? null),
                'database_available' => true // Asumimos que está disponible si llegamos aquí
            ];
            
            Log::info('EmpresaController: Vista cargada exitosamente');
            return view('modules.configuracion.empresa.index', $configData);
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de empresa: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // En lugar de redirigir, intentar mostrar la vista con datos mínimos
            try {
                return view('modules.configuracion.empresa.index', [
                    'empresa' => $this->createEmptyEmpresaStructure(),
                    'usuario' => session('user_data'),
                    'configuraciones' => [
                        'informacion_basica' => [],
                        'configuracion_regional' => [],
                        'configuracion_fiscal' => [],
                        'branding' => [],
                        'integracion_modulos' => []
                    ],
                    'tabs' => $this->getEmpresaTabs(),
                    'formatos' => $this->getFormatosDisponibles(),
                    'zonas_horarias' => $this->getZonasHorarias(),
                    'monedas' => $this->getMonedasDisponibles(),
                    'idiomas' => $this->getIdiomasDisponibles(),
                    'configuraciones_hallazgos' => collect([]),
                    'configuraciones_psicosocial' => collect([]),
                    'database_available' => false,
                    'error_message' => 'No se pudo cargar la configuración desde la base de datos. Mostrando valores por defecto.'
                ]);
            } catch (\Exception $fallbackException) {
                Log::error('Error crítico en fallback de empresa: ' . $fallbackException->getMessage());
                return back()->with('error', 'Error crítico al cargar la configuración de empresa');
            }
        }
    }
    
    /**
     * Actualizar información de empresa
     */
    public function update(Request $request)
    {
        try {
            // Validación con mensajes personalizados
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'razon_social' => 'required|string|max:255',
                'nit' => 'required|string|max:20',
                'direccion' => 'nullable|string|max:500',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'sitio_web' => 'nullable|url|max:255',
                'colorPrimario' => 'nullable|string|max:7',
                'colorSecundario' => 'nullable|string|max:7',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'favicon' => 'nullable|image|mimes:ico,png|max:512'
            ], [
                'nombre.required' => 'El nombre de la empresa es obligatorio.',
                'nombre.string' => 'El nombre de la empresa debe ser texto.',
                'nombre.max' => 'El nombre de la empresa no puede tener más de 255 caracteres.',
                'razon_social.required' => 'La razón social es obligatoria.',
                'razon_social.string' => 'La razón social debe ser texto.',
                'razon_social.max' => 'La razón social no puede tener más de 255 caracteres.',
                'nit.required' => 'El NIT es obligatorio.',
                'nit.string' => 'El NIT debe ser texto.',
                'nit.max' => 'El NIT no puede tener más de 20 caracteres.',
                'email.email' => 'El email debe tener un formato válido.',
                'sitio_web.url' => 'El sitio web debe ser una URL válida.',
                'logo.image' => 'El logo debe ser una imagen.',
                'logo.mimes' => 'El logo debe ser un archivo JPEG, PNG, JPG, GIF o SVG.',
                'logo.max' => 'El logo no puede ser mayor a 2MB.',
                'favicon.image' => 'El favicon debe ser una imagen.',
                'favicon.mimes' => 'El favicon debe ser un archivo ICO o PNG.',
                'favicon.max' => 'El favicon no puede ser mayor a 512KB.'
            ]);

            if ($validator->fails()) {
                Log::error('EmpresaController@update - Error de validación:', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                return back()->withErrors($validator)->withInput();
            }
            
            Log::info('EmpresaController@update - Validación exitosa');
            
            $empresaData = session('empresa_data');
            Log::info('EmpresaController@update - Datos de empresa de sesión:', ['empresa_data' => $empresaData]);
            
            if (!$empresaData || !isset($empresaData['id'])) {
                Log::error('EmpresaController@update - No hay datos de empresa en sesión');
                return back()->with('error', 'No se encontró información de empresa en la sesión. Por favor, inicie sesión nuevamente.');
            }
            
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            Log::info('EmpresaController@update - Empresa encontrada:', ['empresa_found' => !!$empresa]);
            
            if (!$empresa) {
                Log::error('EmpresaController@update - Empresa no encontrada en BD', ['empresa_id' => $empresaData['id']]);
                return back()->with('error', 'Empresa no encontrada en la base de datos.');
            }
            
            // Actualizar datos básicos
            $empresa->nombre = $request->nombre;
            $empresa->razon_social = $request->razon_social;
            $empresa->nit = $request->nit;
            $empresa->direccion = $request->direccion;
            $empresa->telefono = $request->telefono;
            $empresa->email = $request->email;
            $empresa->sitio_web = $request->sitio_web;
            $empresa->colorPrimario = $request->colorPrimario ?: '#007bff';
            $empresa->colorSecundario = $request->colorSecundario ?: '#6c757d';
            
            // Manejar logo
            if ($request->hasFile('logo')) {
                // Eliminar logo anterior si existe
                if ($empresa->logo && Storage::exists('public/logos/' . $empresa->logo)) {
                    Storage::delete('public/logos/' . $empresa->logo);
                }
                
                $logoName = 'logo_' . $empresa->id . '_' . time() . '.' . $request->logo->extension();
                $request->logo->storeAs('public/logos', $logoName);
                $empresa->logo = $logoName;
            }
            
            // Manejar favicon
            if ($request->hasFile('favicon')) {
                // Eliminar favicon anterior si existe
                if ($empresa->favicon && Storage::exists('public/favicons/' . $empresa->favicon)) {
                    Storage::delete('public/favicons/' . $empresa->favicon);
                }
                
                $faviconName = 'favicon_' . $empresa->id . '_' . time() . '.' . $request->favicon->extension();
                $request->favicon->storeAs('public/favicons', $faviconName);
                $empresa->favicon = $faviconName;
            }
            
            $empresa->save();
            
            Log::info('EmpresaController@update - Empresa actualizada exitosamente', [
                'empresa_id' => $empresa->id,
                'campos_actualizados' => [
                    'nombre' => $empresa->nombre,
                    'razon_social' => $empresa->razon_social,
                    'nit' => $empresa->nit
                ]
            ]);
            
            // Actualizar sesión
            session(['empresa_data' => array_merge(session('empresa_data'), [
                'nombre' => $empresa->nombre,
                'razon_social' => $empresa->razon_social,
                'nit' => $empresa->nit,
                'colorPrimario' => $empresa->colorPrimario,
                'colorSecundario' => $empresa->colorSecundario,
                'logo' => $empresa->logo
            ])]);
            
            Log::info('EmpresaController@update - Sesión actualizada exitosamente');
            
            return back()->with('success', 'Información de empresa actualizada correctamente');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('EmpresaController@update - Error de validación:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('EmpresaController@update - Error general:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al actualizar la información de empresa: ' . $e->getMessage());
        }
    }
    
    /**
     * Configuración avanzada de empresa
     */
    public function configuracionAvanzada()
    {
        try {
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            return view('modules.configuracion.empresa.avanzada', compact('empresa'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración avanzada: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración avanzada');
        }
    }
    
    /**
     * Actualizar configuración avanzada
     */
    public function updateAvanzada(Request $request)
    {
        try {
            $request->validate([
                'configuracion_psicosocial' => 'nullable|array',
                'configuracion_hallazgos' => 'nullable|array',
                'limites_usuarios' => 'nullable|integer|min:1',
                'limite_evaluaciones' => 'nullable|integer|min:1',
                'retener_datos_dias' => 'nullable|integer|min:30|max:3650',
                'backup_automatico' => 'boolean',
                'notificaciones_email' => 'boolean',
                'reportes_automaticos' => 'boolean'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Actualizar configuraciones específicas
            $empresa->configuracion_psicosocial = $request->configuracion_psicosocial ?: [];
            $empresa->configuracion_hallazgos = $request->configuracion_hallazgos ?: [];
            $empresa->limites_usuarios = $request->limites_usuarios ?: 100;
            $empresa->limite_evaluaciones = $request->limite_evaluaciones ?: 1000;
            $empresa->retener_datos_dias = $request->retener_datos_dias ?: 365;
            $empresa->backup_automatico = $request->boolean('backup_automatico');
            $empresa->notificaciones_email = $request->boolean('notificaciones_email');
            $empresa->reportes_automaticos = $request->boolean('reportes_automaticos');
            
            $empresa->save();
            
            return back()->with('success', 'Configuración avanzada actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración avanzada: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración avanzada');
        }
    }

    /**
     * Actualizar configuración específica de empresa
     */
    public function updateConfiguration(Request $request)
    {
        try {
            $request->validate([
                'seccion' => 'required|string',
                'configuraciones' => 'required|array'
            ]);

            $empresaData = session('empresa_data');
            if (!$empresaData || !isset($empresaData['id'])) {
                return response()->json(['error' => 'No se encontró información de empresa'], 400);
            }

            $empresaId = $empresaData['id'];
            $seccion = $request->seccion;
            $configuraciones = $request->configuraciones;

            // Procesar según la sección
            switch ($seccion) {
                case 'informacion_basica':
                    $this->updateInformacionBasica($empresaId, $configuraciones);
                    break;
                case 'configuracion_regional':
                    $this->updateConfiguracionRegional($empresaId, $configuraciones);
                    break;
                case 'configuracion_fiscal':
                    $this->updateConfiguracionFiscal($empresaId, $configuraciones);
                    break;
                case 'branding':
                    $this->updateBranding($empresaId, $configuraciones);
                    break;
                case 'integracion_modulos':
                    $this->updateIntegracionModulos($empresaId, $configuraciones);
                    break;
                default:
                    return response()->json(['error' => 'Sección no válida'], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración de empresa: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la configuración'], 500);
        }
    }

    /**
     * Crear estructura vacía de empresa
     */
    private function createEmptyEmpresaStructure()
    {
        return (object)[
            'id' => null,
            'nombre' => 'Empresa no configurada',
            'razon_social' => '',
            'nit' => '',
            'direccion' => '',
            'telefono' => '',
            'email' => '',
            'sitio_web' => '',
            'colorPrimario' => '#007bff',
            'colorSecundario' => '#6c757d',
            'logo' => null,
            'favicon' => null,
            'zona_horaria' => 'America/Bogota',
            'formato_fecha' => 'DD/MM/YYYY',
            'formato_hora' => '24',
            'moneda_principal' => 'COP',
            'idioma' => 'es-419'
        ];
    }

    /**
     * Crear empresa desde datos de sesión
     */
    private function createEmpresaFromSession($empresaData)
    {
        return (object)[
            'id' => $empresaData['id'],
            'nombre' => $empresaData['nombre'] ?? 'Empresa',
            'razon_social' => $empresaData['razon_social'] ?? '',
            'nit' => $empresaData['nit'] ?? '',
            'direccion' => '',
            'telefono' => '',
            'email' => '',
            'sitio_web' => '',
            'colorPrimario' => $empresaData['colorPrimario'] ?? '#007bff',
            'colorSecundario' => $empresaData['colorSecundario'] ?? '#6c757d',
            'logo' => $empresaData['logo'] ?? null,
            'favicon' => null,
            'zona_horaria' => 'America/Bogota',
            'formato_fecha' => 'DD/MM/YYYY',
            'formato_hora' => '24',
            'moneda_principal' => 'COP',
            'idioma' => 'es-419'
        ];
    }

    /**
     * Obtener configuraciones específicas de la empresa
     */
    private function getEmpresaConfigurations($empresaId)
    {
        try {
            return $this->configuracionService->getEmpresaConfigurations($empresaId);
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones de empresa: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Formatear configuraciones para la vista
     */
    private function formatConfigurationsForView($configuraciones)
    {
        $formattedConfigs = [
            'informacion_basica' => [],
            'configuracion_regional' => [],
            'configuracion_fiscal' => [],
            'branding' => [],
            'integracion_modulos' => []
        ];

        foreach ($configuraciones as $config) {
            $categoria = $this->determineConfigCategory($config->clave);
            $formattedConfigs[$categoria][$config->clave] = [
                'valor' => $config->valor,
                'tipo' => $config->tipo_dato,
                'descripcion' => $config->descripcion
            ];
        }

        return $formattedConfigs;
    }

    /**
     * Determinar categoría de configuración
     */
    private function determineConfigCategory($clave)
    {
        if (str_contains($clave, 'formato_') || str_contains($clave, 'zona_horaria') || str_contains($clave, 'idioma')) {
            return 'configuracion_regional';
        }
        if (str_contains($clave, 'fiscal_') || str_contains($clave, 'nit_') || str_contains($clave, 'moneda_')) {
            return 'configuracion_fiscal';
        }
        if (str_contains($clave, 'color') || str_contains($clave, 'logo') || str_contains($clave, 'tema')) {
            return 'branding';
        }
        if (str_contains($clave, 'hallazgos_') || str_contains($clave, 'psicosocial_')) {
            return 'integracion_modulos';
        }
        
        return 'informacion_basica';
    }

    /**
     * Obtener pestañas de configuración de empresa
     */
    private function getEmpresaTabs()
    {
        return [
            [
                'id' => 'informacion_basica',
                'name' => 'Información Básica',
                'icon' => 'fas fa-building',
                'description' => 'Datos generales de la empresa'
            ],
            [
                'id' => 'configuracion_regional',
                'name' => 'Configuración Regional',
                'icon' => 'fas fa-globe',
                'description' => 'Zona horaria, idioma, formatos'
            ],
            [
                'id' => 'configuracion_fiscal',
                'name' => 'Configuración Fiscal',
                'icon' => 'fas fa-calculator',
                'description' => 'Información tributaria y fiscal'
            ],
            [
                'id' => 'branding',
                'name' => 'Identidad Corporativa',
                'icon' => 'fas fa-palette',
                'description' => 'Logo, colores, diseño'
            ],
            [
                'id' => 'integracion_modulos',
                'name' => 'Integración de Módulos',
                'icon' => 'fas fa-puzzle-piece',
                'description' => 'Configuraciones para Hallazgos y Psicosocial'
            ]
        ];
    }

    /**
     * Obtener formatos disponibles
     */
    private function getFormatosDisponibles()
    {
        return [
            'fecha' => [
                'DD/MM/YYYY' => '31/12/2023',
                'MM/DD/YYYY' => '12/31/2023',
                'YYYY-MM-DD' => '2023-12-31',
                'DD-MM-YYYY' => '31-12-2023'
            ],
            'hora' => [
                '24' => '23:59',
                '12' => '11:59 PM'
            ]
        ];
    }

    /**
     * Obtener zonas horarias disponibles
     */
    private function getZonasHorarias()
    {
        return [
            'America/Bogota' => 'Bogotá (GMT-5)',
            'America/Mexico_City' => 'Ciudad de México (GMT-6)',
            'America/Lima' => 'Lima (GMT-5)',
            'America/Caracas' => 'Caracas (GMT-4)',
            'America/Buenos_Aires' => 'Buenos Aires (GMT-3)',
            'UTC' => 'UTC (GMT+0)'
        ];
    }

    /**
     * Obtener monedas disponibles
     */
    private function getMonedasDisponibles()
    {
        return [
            'COP' => 'Peso Colombiano (COP)',
            'USD' => 'Dólar Estadounidense (USD)',
            'EUR' => 'Euro (EUR)',
            'MXN' => 'Peso Mexicano (MXN)',
            'PEN' => 'Sol Peruano (PEN)',
            'ARS' => 'Peso Argentino (ARS)'
        ];
    }

    /**
     * Obtener idiomas disponibles
     */
    private function getIdiomasDisponibles()
    {
        return [
            'es-419' => 'Español (Latinoamérica)',
            'es-ES' => 'Español (España)', 
            'en-US' => 'English (United States)',
            'pt-BR' => 'Português (Brasil)',
            'fr-FR' => 'Français (France)'
        ];
    }

    /**
     * Actualizar información básica
     */
    private function updateInformacionBasica($empresaId, $configuraciones)
    {
        $empresa = Empresa::where('id', $empresaId)->first();
        if (!$empresa) {
            throw new \Exception('Empresa no encontrada');
        }

        // Actualizar campos directos de la empresa
        if (isset($configuraciones['nombre'])) {
            $empresa->nombre = $configuraciones['nombre'];
        }
        if (isset($configuraciones['razon_social'])) {
            $empresa->razon_social = $configuraciones['razon_social'];
        }
        if (isset($configuraciones['nit'])) {
            $empresa->nit = $configuraciones['nit'];
        }
        if (isset($configuraciones['direccion'])) {
            $empresa->direccion = $configuraciones['direccion'];
        }
        if (isset($configuraciones['telefono'])) {
            $empresa->telefono = $configuraciones['telefono'];
        }
        if (isset($configuraciones['email'])) {
            $empresa->email = $configuraciones['email'];
        }
        if (isset($configuraciones['sitio_web'])) {
            $empresa->sitio_web = $configuraciones['sitio_web'];
        }

        $empresa->save();

        // Actualizar sesión
        $this->updateSessionData($empresa);
    }

    /**
     * Actualizar configuración regional
     */
    private function updateConfiguracionRegional($empresaId, $configuraciones)
    {
        foreach ($configuraciones as $clave => $valor) {
            $this->configuracionService->updateConfiguration(
                $empresaId,
                $clave,
                $valor,
                'empresa'
            );
        }
    }

    /**
     * Actualizar configuración fiscal
     */
    private function updateConfiguracionFiscal($empresaId, $configuraciones)
    {
        foreach ($configuraciones as $clave => $valor) {
            $this->configuracionService->updateConfiguration(
                $empresaId,
                $clave,
                $valor,
                'empresa'
            );
        }
    }

    /**
     * Actualizar branding
     */
    private function updateBranding($empresaId, $configuraciones)
    {
        $empresa = Empresa::where('id', $empresaId)->first();
        if (!$empresa) {
            throw new \Exception('Empresa no encontrada');
        }

        if (isset($configuraciones['colorPrimario'])) {
            $empresa->colorPrimario = $configuraciones['colorPrimario'];
        }
        if (isset($configuraciones['colorSecundario'])) {
            $empresa->colorSecundario = $configuraciones['colorSecundario'];
        }

        $empresa->save();

        // Actualizar configuraciones adicionales
        foreach ($configuraciones as $clave => $valor) {
            if (!in_array($clave, ['colorPrimario', 'colorSecundario'])) {
                $this->configuracionService->updateConfiguration(
                    $empresaId,
                    $clave,
                    $valor,
                    'empresa'
                );
            }
        }

        // Actualizar sesión
        $this->updateSessionData($empresa);
    }

    /**
     * Actualizar integración de módulos
     */
    private function updateIntegracionModulos($empresaId, $configuraciones)
    {
        foreach ($configuraciones as $clave => $valor) {
            $modulo = str_contains($clave, 'hallazgos') ? 'hallazgos' : 'psicosocial';
            $this->configuracionService->updateConfiguration(
                $empresaId,
                $clave,
                $valor,
                $modulo
            );
        }
    }

    /**
     * Actualizar datos de sesión
     */
    private function updateSessionData($empresa)
    {
        session(['empresa_data' => array_merge(session('empresa_data', []), [
            'nombre' => $empresa->nombre,
            'razon_social' => $empresa->razon_social,
            'nit' => $empresa->nit,
            'colorPrimario' => $empresa->colorPrimario,
            'colorSecundario' => $empresa->colorSecundario,
            'logo' => $empresa->logo
        ])]);
    }

    /**
     * Obtener configuraciones de Hallazgos para la empresa
     */
    private function getConfiguracionesHallazgos($empresaId)
    {
        if (!$empresaId) return collect([]);
        try {
            return $this->configuracionService->getHallazgosConfigurations($empresaId);
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones de hallazgos: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Obtener configuraciones de Psicosocial para la empresa
     */
    private function getConfiguracionesPsicosocial($empresaId)
    {
        if (!$empresaId) return collect([]);
        try {
            return $this->configuracionService->getPsicosocialConfigurations($empresaId);
        } catch (\Exception $e) {
            Log::error('Error obteniendo configuraciones de psicosocial: ' . $e->getMessage());
            return collect([]);
        }
    }
}
