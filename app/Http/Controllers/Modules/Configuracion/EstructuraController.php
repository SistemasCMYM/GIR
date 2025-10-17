<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use App\Services\ConfiguracionService;
use App\Models\Configuracion\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empresa;

class EstructuraController extends Controller
{
    protected $configuracionService;

    public function __construct(ConfiguracionService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Mostrar configuración de estructura organizacional
     */
    public function index()
    {
        try {
            $empresaData = session('empresa_data');
            $userData = session('user_data');
            
            if (!$empresaData || !isset($empresaData['id'])) {
                return redirect()->route('configuracion.index')
                               ->with('error', 'No se encontró información de empresa');
            }

            // Obtener empresa actual desde MongoDB
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return redirect()->route('configuracion.index')->with('error', 'Empresa no encontrada');
            }

            // Obtener configuraciones de estructura
            $configuraciones = $this->configuracionService->getConfigurationsByComponent(
                $empresaData['id'], 
                'estructura'
            );

            // Configuraciones por defecto si no existen
            if ($configuraciones->isEmpty()) {
                $this->createDefaultEstructuraConfig($empresaData['id']);
                $configuraciones = $this->configuracionService->getConfigurationsByComponent(
                    $empresaData['id'], 
                    'estructura'
                );
            }
            
            // Obtener áreas, departamentos y cargos
            $estructura = [
                'areas' => $empresa->areas ?? [],
                'departamentos' => $empresa->departamentos ?? [],
                'cargos' => $empresa->cargos ?? []
            ];
            
            return view('modules.configuracion.estructura.index', [
                'empresa' => $empresa,
                'estructura' => $estructura,
                'userData' => $userData,
                'configuraciones' => $configuraciones
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de estructura: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración de estructura');
        }
    }
    
    /**
     * Guardar área
     */
    public function guardarArea(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'responsable' => 'nullable|string|max:100',
                'area_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar áreas si no existen
            if (!isset($empresa->areas)) {
                $empresa->areas = [];
            }
            
            // Editar o crear nueva área
            if ($request->area_id) {
                // Editar área existente
                foreach ($empresa->areas as $key => $area) {
                    if ($area['id'] == $request->area_id) {
                        $empresa->areas[$key]['nombre'] = $request->nombre;
                        $empresa->areas[$key]['descripcion'] = $request->descripcion;
                        $empresa->areas[$key]['responsable'] = $request->responsable;
                        $empresa->areas[$key]['updated_at'] = now();
                    }
                }
            } else {
                // Crear nueva área
                $newArea = [
                    'id' => uniqid('area_'),
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'responsable' => $request->responsable,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $empresa->areas[] = $newArea;
            }
            
            $empresa->save();
            
            return back()->with('success', 'Área guardada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al guardar área: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el área');
        }
    }
    
    /**
     * Eliminar área
     */
    public function eliminarArea(Request $request)
    {
        try {
            $request->validate([
                'area_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->areas)) {
                return back()->with('error', 'Empresa o áreas no encontradas');
            }
            
            // Filtrar áreas para remover la seleccionada
            $empresa->areas = array_filter($empresa->areas, function($area) use ($request) {
                return $area['id'] != $request->area_id;
            });
            
            // Reindexar el array
            $empresa->areas = array_values($empresa->areas);
            
            $empresa->save();
            
            return back()->with('success', 'Área eliminada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar área: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el área');
        }
    }
    
    /**
     * Guardar departamento
     */
    public function guardarDepartamento(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'area_id' => 'required|string',
                'responsable' => 'nullable|string|max:100',
                'departamento_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar departamentos si no existen
            if (!isset($empresa->departamentos)) {
                $empresa->departamentos = [];
            }
            
            // Editar o crear nuevo departamento
            if ($request->departamento_id) {
                // Editar departamento existente
                foreach ($empresa->departamentos as $key => $departamento) {
                    if ($departamento['id'] == $request->departamento_id) {
                        $empresa->departamentos[$key]['nombre'] = $request->nombre;
                        $empresa->departamentos[$key]['descripcion'] = $request->descripcion;
                        $empresa->departamentos[$key]['area_id'] = $request->area_id;
                        $empresa->departamentos[$key]['responsable'] = $request->responsable;
                        $empresa->departamentos[$key]['updated_at'] = now();
                    }
                }
            } else {
                // Crear nuevo departamento
                $newDepartamento = [
                    'id' => uniqid('dept_'),
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'area_id' => $request->area_id,
                    'responsable' => $request->responsable,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $empresa->departamentos[] = $newDepartamento;
            }
            
            $empresa->save();
            
            return back()->with('success', 'Departamento guardado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al guardar departamento: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el departamento');
        }
    }
    
    /**
     * Eliminar departamento
     */
    public function eliminarDepartamento(Request $request)
    {
        try {
            $request->validate([
                'departamento_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->departamentos)) {
                return back()->with('error', 'Empresa o departamentos no encontrados');
            }
            
            // Filtrar departamentos para remover el seleccionado
            $empresa->departamentos = array_filter($empresa->departamentos, function($departamento) use ($request) {
                return $departamento['id'] != $request->departamento_id;
            });
            
            // Reindexar el array
            $empresa->departamentos = array_values($empresa->departamentos);
            
            $empresa->save();
            
            return back()->with('success', 'Departamento eliminado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar departamento: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el departamento');
        }
    }
    
    /**
     * Guardar cargo
     */
    public function guardarCargo(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
                'departamento_id' => 'required|string',
                'nivel' => 'nullable|integer',
                'cargo_id' => 'nullable|string' // Para edición
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Inicializar cargos si no existen
            if (!isset($empresa->cargos)) {
                $empresa->cargos = [];
            }
            
            // Editar o crear nuevo cargo
            if ($request->cargo_id) {
                // Editar cargo existente
                foreach ($empresa->cargos as $key => $cargo) {
                    if ($cargo['id'] == $request->cargo_id) {
                        $empresa->cargos[$key]['nombre'] = $request->nombre;
                        $empresa->cargos[$key]['descripcion'] = $request->descripcion;
                        $empresa->cargos[$key]['departamento_id'] = $request->departamento_id;
                        $empresa->cargos[$key]['nivel'] = $request->nivel;
                        $empresa->cargos[$key]['updated_at'] = now();
                    }
                }
            } else {
                // Crear nuevo cargo
                $newCargo = [
                    'id' => uniqid('cargo_'),
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'departamento_id' => $request->departamento_id,
                    'nivel' => $request->nivel,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $empresa->cargos[] = $newCargo;
            }
            
            $empresa->save();
            
            return back()->with('success', 'Cargo guardado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al guardar cargo: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el cargo');
        }
    }
    
    /**
     * Eliminar cargo
     */
    public function eliminarCargo(Request $request)
    {
        try {
            $request->validate([
                'cargo_id' => 'required|string'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa || !isset($empresa->cargos)) {
                return back()->with('error', 'Empresa o cargos no encontrados');
            }
            
            // Filtrar cargos para remover el seleccionado
            $empresa->cargos = array_filter($empresa->cargos, function($cargo) use ($request) {
                return $cargo['id'] != $request->cargo_id;
            });
            
            // Reindexar el array
            $empresa->cargos = array_values($empresa->cargos);
            
            $empresa->save();
            
            return back()->with('success', 'Cargo eliminado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar cargo: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el cargo');
        }
    }

    /**
     * Crear configuraciones por defecto para estructura
     */
    private function createDefaultEstructuraConfig($empresaId)
    {
        $defaultConfigs = [
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'jerarquia',
                'clave' => 'estructura_niveles_jerarquicos',
                'valor' => [3],
                'tipo_dato' => 'integer',
                'descripcion' => 'Número de niveles jerárquicos en la estructura organizacional',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura', 'hallazgos', 'psicosocial'],
                'orden' => 1
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'codificacion',
                'clave' => 'estructura_auto_asignacion_codigo',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Asignación automática de códigos para elementos de estructura',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura'],
                'orden' => 2
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'codificacion',
                'clave' => 'estructura_formato_codigo_area',
                'valor' => ['AR-{###}'],
                'tipo_dato' => 'string',
                'descripcion' => 'Formato para códigos de áreas',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura', 'hallazgos', 'psicosocial'],
                'orden' => 3
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'codificacion',
                'clave' => 'estructura_formato_codigo_centro',
                'valor' => ['CT-{###}'],
                'tipo_dato' => 'string',
                'descripcion' => 'Formato para códigos de centros',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura', 'hallazgos', 'psicosocial'],
                'orden' => 4
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'codificacion',
                'clave' => 'estructura_formato_codigo_departamento',
                'valor' => ['DP-{###}'],
                'tipo_dato' => 'string',
                'descripcion' => 'Formato para códigos de departamentos',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura', 'hallazgos', 'psicosocial'],
                'orden' => 5
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'validacion',
                'clave' => 'estructura_permitir_areas_sin_centro',
                'valor' => [false],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Permitir crear áreas sin asignar a un centro',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura'],
                'orden' => 6
            ],
            [
                'empresa_id' => $empresaId,
                'modulo' => 'estructura',
                'componente' => 'validacion',
                'clave' => 'estructura_validar_duplicados',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Validar duplicados en nombres y códigos de estructura',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura'],
                'orden' => 7
            ]
        ];

        foreach ($defaultConfigs as $config) {
            Configuracion::create($config);
        }
    }

    /**
     * Actualizar configuración de estructura
     */
    public function updateConfiguration(Request $request)
    {
        try {
            $request->validate([
                'clave' => 'required|string',
                'valor' => 'required'
            ]);

            $empresaData = session('empresa_data');
            
            $this->configuracionService->updateConfiguration(
                $empresaData['id'],
                $request->clave,
                $request->valor,
                'estructura'
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de estructura: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la configuración'], 500);
        }
    }
}
