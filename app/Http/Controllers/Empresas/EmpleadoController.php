<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use App\Models\Empresas\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource with filters and pagination
     */
    public function index(Request $request)
    {
        try {
            $empresaId = session('empresa_id');
            $isSuperAdmin = session('user_data.isSuperAdmin', false);

            // Construir query
            $query = DB::connection('mongodb_empresas')->collection('empleados');

            // Filtro por empresa (si no es SuperAdmin)
            if (!$isSuperAdmin && $empresaId) {
                $query = $query->where('empresa_id', $empresaId);
            }

            // Búsqueda general
            if ($request->filled('search')) {
                $search = $request->search;
                
                $query = $query->whereRaw([
                    '$or' => [
                        ['primerNombre' => ['$regex' => $search, '$options' => 'i']],
                        ['segundoNombre' => ['$regex' => $search, '$options' => 'i']],
                        ['primerApellido' => ['$regex' => $search, '$options' => 'i']],
                        ['segundoApellido' => ['$regex' => $search, '$options' => 'i']],
                        ['dni' => ['$regex' => $search, '$options' => 'i']],
                        ['email' => ['$regex' => $search, '$options' => 'i']],
                        ['cargo' => ['$regex' => $search, '$options' => 'i']],
                        ['ciudad' => ['$regex' => $search, '$options' => 'i']]
                    ]
                ]);
            }

            // Filtro por número de documento (DNI)
            if ($request->filled('numero_documento')) {
                $query = $query->where('dni', 'like', "%{$request->numero_documento}%");
            }

            // Filtro por email
            if ($request->filled('email')) {
                $query = $query->where('email', 'like', "%{$request->email}%");
            }

            // Filtro por cargo
            if ($request->filled('cargo')) {
                $query = $query->where('cargo', 'like', "%{$request->cargo}%");
            }

            // Filtros específicos
            if ($request->filled('genero')) {
                $query = $query->where('genero', $request->genero);
            }

            if ($request->filled('tipo_cargo')) {
                $query = $query->where('tipo_cargo', $request->tipo_cargo);
            }

            if ($request->filled('psicosocial_tipo')) {
                $query = $query->where('psicosocial_tipo', $request->psicosocial_tipo);
            }

            if ($request->filled('area_id')) {
                $query = $query->where('area_key', $request->area_id);
            }

            if ($request->filled('centro_id')) {
                $query = $query->where('centro_key', $request->centro_id);
            }

            if ($request->filled('proceso_id')) {
                $query = $query->where('proceso_key', $request->proceso_id);
            }

            if ($request->filled('ciudad')) {
                $query = $query->where('ciudad', 'like', "%{$request->ciudad}%");
            }

            if ($request->filled('estado')) {
                $query = $query->where('activo', $request->estado === 'activo');
            }

            // Paginación
            $perPage = $request->input('per_page', 50);
            if (!in_array($perPage, [50, 100, 500, 1000])) {
                $perPage = 50;
            }

            // Obtener resultados paginados
            $empleadosData = $query->paginate($perPage);
            
            // Convertir los resultados a array para facilitar el acceso en la vista
            $empleados = collect($empleadosData->items())->map(function($item) {
                return is_array($item) ? $item : $item->toArray();
            });

            // Obtener áreas, centros y procesos para filtros y relaciones
            $areas = DB::connection('mongodb_empresas')->collection('areas')
                ->where('empresa_id', $empresaId)
                ->get()
                ->map(function($item) {
                    return is_array($item) ? $item : $item->toArray();
                });
                
            $centros = DB::connection('mongodb_empresas')->collection('centros')
                ->where('empresa_id', $empresaId)
                ->get()
                ->map(function($item) {
                    return is_array($item) ? $item : $item->toArray();
                });
                
            $procesos = DB::connection('mongodb_empresas')->collection('procesos')
                ->where('empresa_id', $empresaId)
                ->get()
                ->map(function($item) {
                    return is_array($item) ? $item : $item->toArray();
                });

            // Estadísticas
            $totalQuery = DB::connection('mongodb_empresas')->collection('empleados');
            if (!$isSuperAdmin && $empresaId) {
                $totalQuery = $totalQuery->where('empresa_id', $empresaId);
            }

            $total = $totalQuery->count();
            $activos = $totalQuery->where('activo', true)->count();

            $estadisticas = [
                'total' => $total,
                'activos' => $activos,
                'inactivos' => $total - $activos,
                'porcentaje_activos' => $total > 0 ? round(($activos / $total) * 100, 2) : 0
            ];

            return view('admin.gestion-administrativa.empresa.empleados.index', compact(
                'empleados',
                'empleadosData',
                'areas',
                'centros',
                'procesos',
                'estadisticas'
            ));

        } catch (\Exception $e) {
            Log::error('Error en EmpleadoController@index: ' . $e->getMessage());
            
            return view('admin.gestion-administrativa.empresa.empleados.index', [
                'empleados' => collect([]),
                'empleadosData' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50),
                'areas' => collect([]),
                'centros' => collect([]),
                'procesos' => collect([]),
                'estadisticas' => [
                    'total' => 0,
                    'activos' => 0,
                    'inactivos' => 0,
                    'porcentaje_activos' => 0
                ]
            ]);
        }
    }

    /**
     * Store a newly created resource in storage (individual)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'genero' => 'required|in:masculino,femenino,otro',
            'numero_documento' => 'required|string|max:20',
            'tipo_documento' => 'nullable|string|max:50',
            'cargo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'area_id' => 'required|string',
            'proceso_id' => 'nullable|string',
            'centro_id' => 'required|string',
            'ciudad' => 'required|string|max:255',
            'psicosocial_tipo' => 'required|in:A,B',
            'tipo_cargo' => 'required|in:gerencial,profesional,tecnico,auxiliar,operativo',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $empresaId = session('empresa_id');
            
            if (!$empresaId) {
                return back()->withErrors(['error' => 'No se pudo identificar la empresa'])->withInput();
            }

            // Verificar si el documento ya existe
            $exists = DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('numero_documento', $request->numero_documento)
                ->where('empresa_id', $empresaId)
                ->exists();

            if ($exists) {
                return back()->withErrors(['numero_documento' => 'El número de documento ya existe'])->withInput();
            }

            // Crear empleado
            $psicosocialTipo = !empty($request->psicosocial_tipo) ? strtoupper(trim($request->psicosocial_tipo)) : null;
            $psicosocial = !empty($psicosocialTipo); // true si tiene tipo, false si está vacío
            
            $empleadoData = [
                'empresa_id' => $empresaId,
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'nombre' => trim($request->primer_nombre . ' ' . $request->segundo_nombre),
                'apellidos' => trim($request->primer_apellido . ' ' . $request->segundo_apellido),
                'genero' => $request->genero,
                'numero_documento' => $request->numero_documento,
                'tipo_documento' => $request->tipo_documento ?? 'CC',
                'cargo' => $request->cargo,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'area_id' => $request->area_id,
                'proceso_id' => $request->proceso_id,
                'centro_id' => $request->centro_id,
                'ciudad' => $request->ciudad,
                'psicosocial' => $psicosocial,
                'psicosocial_tipo' => $psicosocialTipo,
                'tipo_cargo' => $request->tipo_cargo,
                'activo' => true,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->insert($empleadoData);

            return redirect()->route('empresa.empleados.index')
                ->with('success', 'Empleado creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear empleado: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear el empleado: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Store multiple employees from CSV file
     */
    public function storeMasivo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo_csv' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $empresaId = session('empresa_id');
            
            if (!$empresaId) {
                Log::error('Carga masiva: No hay empresa_id en sesión');
                return back()->withErrors(['error' => 'No se pudo identificar la empresa']);
            }

            $file = $request->file('archivo_csv');
            $path = $file->getRealPath();
            
            Log::info('Iniciando carga masiva', [
                'empresa_id' => $empresaId,
                'archivo' => $file->getClientOriginalName(),
                'tamaño' => $file->getSize()
            ]);
            
            // Leer CSV con manejo de BOM UTF-8
            $content = file_get_contents($path);
            
            // Eliminar BOM si existe
            $content = str_replace("\xEF\xBB\xBF", '', $content);
            
            // Convertir diferentes tipos de saltos de línea
            $content = str_replace(["\r\n", "\r"], "\n", $content);
            
            // Separar por líneas
            $lines = explode("\n", $content);
            
            // Filtrar líneas vacías
            $lines = array_filter($lines, function($line) {
                return trim($line) !== '';
            });
            
            if (count($lines) < 2) {
                return back()->withErrors(['error' => 'El archivo está vacío o no tiene datos']);
            }
            
            // Obtener encabezados
            $headers = str_getcsv(array_shift($lines));
            
            // Limpiar encabezados (quitar espacios y BOM)
            $headers = array_map('trim', $headers);
            
            Log::info('Encabezados CSV detectados', ['headers' => $headers]);
            
            // Validar que los encabezados sean correctos
            $requiredHeaders = ['primer_nombre', 'primer_apellido', 'numero_documento', 'email'];
            $missingHeaders = array_diff($requiredHeaders, $headers);
            
            if (!empty($missingHeaders)) {
                return back()->withErrors([
                    'error' => 'El archivo CSV no tiene los encabezados correctos. Faltan: ' . implode(', ', $missingHeaders)
                ]);
            }
            
            // Cargar catálogos para conversión de labels a keys
            $areas = DB::connection('mongodb_empresas')
                ->collection('areas')
                ->where('empresa_id', $empresaId)
                ->get()
                ->keyBy('area_label');
                
            $procesos = DB::connection('mongodb_empresas')
                ->collection('procesos')
                ->where('empresa_id', $empresaId)
                ->get()
                ->keyBy('proceso_label');
                
            $centros = DB::connection('mongodb_empresas')
                ->collection('centros')
                ->where('empresa_id', $empresaId)
                ->get()
                ->keyBy('centro_label');
                
            $sedes = DB::connection('mongodb_empresas')
                ->collection('sedes')
                ->where('empresa_id', $empresaId)
                ->get()
                ->keyBy('sede_label');
                
            $contratos = DB::connection('mongodb_empresas')
                ->collection('contratos')
                ->where('empresa_id', $empresaId)
                ->get()
                ->keyBy('contrato_label');
            
            Log::info('Catálogos cargados', [
                'areas' => $areas->count(),
                'procesos' => $procesos->count(),
                'centros' => $centros->count(),
                'sedes' => $sedes->count(),
                'contratos' => $contratos->count()
            ]);
            
            // Validar número de registros (máximo 2000)
            if (count($lines) > 2000) {
                return back()->withErrors(['error' => 'El archivo excede el límite de 2000 registros. Total: ' . count($lines)]);
            }

            $empleadosCreados = 0;
            $errores = [];
            
            // Procesar cada línea del CSV
            foreach ($lines as $index => $line) {
                $row = str_getcsv($line);
                
                if (count($row) < count($headers)) {
                    $errores[] = "Fila " . ($index + 2) . ": Datos incompletos (tiene " . count($row) . " columnas, se esperan " . count($headers) . ")";
                    continue;
                }

                $data = array_combine($headers, $row);
                
                // Limpiar espacios en blanco de todos los valores
                $data = array_map('trim', $data);
                
                Log::debug('Procesando fila ' . ($index + 2), ['data' => $data]);
                
                // Validar campos requeridos
                if (empty($data['primer_nombre']) || empty($data['primer_apellido']) || 
                    empty($data['numero_documento']) || empty($data['email'])) {
                    $errores[] = "Fila " . ($index + 2) . ": Faltan campos requeridos";
                    continue;
                }

                // Verificar si el documento ya existe
                $exists = DB::connection('mongodb_empresas')
                    ->collection('empleados')
                    ->where('numero_documento', $data['numero_documento'])
                    ->where('empresa_id', $empresaId)
                    ->exists();

                if ($exists) {
                    $errores[] = "Fila " . ($index + 2) . ": Documento {$data['numero_documento']} ya existe";
                    continue;
                }

                try {
                    // Convertir labels a keys usando los catálogos cargados
                    $areaKey = null;
                    $areaLabel = null;
                    if (!empty($data['area_trabajo'])) {
                        $area = $areas->get($data['area_trabajo']);
                        if ($area) {
                            $areaKey = $area['area_key'];
                            $areaLabel = $area['area_label'];
                        }
                    }
                    
                    $procesoKey = null;
                    $procesoLabel = null;
                    if (!empty($data['proceso'])) {
                        $proceso = $procesos->get($data['proceso']);
                        if ($proceso) {
                            $procesoKey = $proceso['proceso_key'];
                            $procesoLabel = $proceso['proceso_label'];
                        }
                    }
                    
                    $centroKey = null;
                    $centroLabel = null;
                    if (!empty($data['sede'])) {
                        $centro = $centros->get($data['sede']);
                        if ($centro) {
                            $centroKey = $centro['centro_key'];
                            $centroLabel = $centro['centro_label'];
                        }
                    }
                    
                    $sedeKey = null;
                    $sedeLabel = null;
                    if (!empty($data['sede'])) {
                        $sede = $sedes->get($data['sede']);
                        if ($sede) {
                            $sedeKey = $sede['sede_key'];
                            $sedeLabel = $sede['sede_label'];
                        }
                    }
                    
                    $contratoKey = null;
                    $contratoLabel = null;
                    if (!empty($data['contrato'])) {
                        $contrato = $contratos->get($data['contrato']);
                        if ($contrato) {
                            $contratoKey = $contrato['contrato_key'];
                            $contratoLabel = $contrato['contrato_label'];
                        }
                    }
                    
                    // Determinar psicosocial_tipo y psicosocial
                    $psicosocialTipo = !empty($data['psicosocial_tipo']) ? strtoupper(trim($data['psicosocial_tipo'])) : null;
                    $psicosocial = !empty($psicosocialTipo); // true si tiene tipo, false si está vacío
                    
                    $empleadoData = [
                        'empresa_id' => $empresaId,
                        'primer_nombre' => $data['primer_nombre'],
                        'segundo_nombre' => $data['segundo_nombre'] ?? null,
                        'primer_apellido' => $data['primer_apellido'],
                        'segundo_apellido' => $data['segundo_apellido'] ?? null,
                        'nombre' => trim(($data['primer_nombre'] ?? '') . ' ' . ($data['segundo_nombre'] ?? '')),
                        'apellidos' => trim(($data['primer_apellido'] ?? '') . ' ' . ($data['segundo_apellido'] ?? '')),
                        'genero' => $data['genero'] ?? 'otro',
                        'numero_documento' => $data['numero_documento'],
                        'tipo_documento' => $data['tipo_documento'] ?? 'CC',
                        'cargo' => $data['cargo'] ?? 'Sin cargo',
                        'email' => $data['email'],
                        'telefono' => $data['telefono'] ?? null,
                        'area_key' => $areaKey,
                        'area_label' => $areaLabel,
                        'proceso_key' => $procesoKey,
                        'proceso_label' => $procesoLabel,
                        'centro_key' => $centroKey,
                        'centro_label' => $centroLabel,
                        'sede_key' => $sedeKey,
                        'sede_label' => $sedeLabel,
                        'ciudad' => $data['ciudad'] ?? null,
                        'psicosocial' => $psicosocial,
                        'psicosocial_tipo' => $psicosocialTipo,
                        'tipo_cargo' => $data['tipo_cargo'] ?? 'operativo',
                        'contrato_key' => $contratoKey,
                        'contrato_label' => $contratoLabel,
                        'activo' => true,
                        'estado' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    DB::connection('mongodb_empresas')
                        ->collection('empleados')
                        ->insert($empleadoData);

                    $empleadosCreados++;

                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($index + 2) . ": Error al guardar - " . $e->getMessage();
                }
            }

            $mensaje = "Se crearon {$empleadosCreados} empleados exitosamente.";
            
            if (count($errores) > 0) {
                $mensaje .= " Se encontraron " . count($errores) . " errores.";
                Log::warning('Errores en carga masiva: ' . json_encode($errores));
            }

            return redirect()->route('empresa.empleados.index')
                ->with('success', $mensaje)
                ->with('errores_carga', $errores);

        } catch (\Exception $e) {
            Log::error('Error en carga masiva: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }
    }

    /**
     * Download CSV template
     */
    public function descargarPlantilla()
    {
        try {
            $headers = [
                'primer_nombre',
                'segundo_nombre',
                'primer_apellido',
                'segundo_apellido',
                'genero',
                'numero_documento',
                'tipo_documento',
                'cargo',
                'email',
                'telefono',
                'area_trabajo',
                'proceso',
                'sede',
                'ciudad',
                'psicosocial_tipo',
                'tipo_cargo',
                'contrato'
            ];

            $filename = 'plantilla_empleados_' . date('Y-m-d') . '.csv';

            $handle = fopen('php://output', 'w');
            ob_start();

            // Agregar BOM para UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Escribir encabezados
            fputcsv($handle, $headers);

            fclose($handle);
            $csv = ob_get_clean();

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Error descargando plantilla: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al descargar la plantilla']);
        }
    }

    /**
     * Get data for create form (areas, centros, procesos)
     */
    public function create()
    {
        try {
            $empresaId = session('empresa_id');

            $areas = DB::connection('mongodb_empresas')
                ->collection('areas')
                ->where('empresa_id', $empresaId)
                ->get();

            $centros = DB::connection('mongodb_empresas')
                ->collection('centros')
                ->where('empresa_id', $empresaId)
                ->get();

            $procesos = DB::connection('mongodb_empresas')
                ->collection('procesos')
                ->where('empresa_id', $empresaId)
                ->get();

            return view('admin.gestion-administrativa.empresa.empleados.create', compact(
                'areas',
                'centros',
                'procesos'
            ));

        } catch (\Exception $e) {
            Log::error('Error en EmpleadoController@create: ' . $e->getMessage());
            return redirect()->route('empresa.empleados.index')
                ->withErrors(['error' => 'Error al cargar el formulario']);
        }
    }

    /**
     * Display the specified resource (para AJAX en modal)
     */
    public function show($id)
    {
        try {
            $empleado = DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('_id', new \MongoDB\BSON\ObjectId($id))
                ->first();

            if (!$empleado) {
                return response()->json(['error' => 'Empleado no encontrado'], 404);
            }

            // Convertir a array si es necesario
            $empleadoArray = is_array($empleado) ? $empleado : $empleado->toArray();

            // Agregar el _id como string para compatibilidad con JavaScript
            $empleadoArray['_id'] = (string) $empleadoArray['_id'];

            // Asegurar que el campo 'dni' también contenga 'numero_documento' para compatibilidad
            if (isset($empleadoArray['numero_documento']) && !isset($empleadoArray['dni'])) {
                $empleadoArray['dni'] = $empleadoArray['numero_documento'];
            }

            // Convertir nombres de campos camelCase
            $empleadoArray['primerNombre'] = $empleadoArray['primer_nombre'] ?? '';
            $empleadoArray['segundoNombre'] = $empleadoArray['segundo_nombre'] ?? '';
            $empleadoArray['primerApellido'] = $empleadoArray['primer_apellido'] ?? '';
            $empleadoArray['segundoApellido'] = $empleadoArray['segundo_apellido'] ?? '';

            return response()->json($empleadoArray);

        } catch (\Exception $e) {
            Log::error('Error en EmpleadoController@show: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar el empleado'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource (renderiza edit.blade.php)
     */
    public function edit(Request $request, $id)
    {
        try {
            $empresaId = session('empresa_id');
            
            Log::info('Edit llamado', [
                'id' => $id,
                'empresa_id' => $empresaId,
                'is_ajax' => $request->ajax()
            ]);

            $empleado = DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('_id', new \MongoDB\BSON\ObjectId($id))
                ->first();

            if (!$empleado) {
                Log::warning('Empleado no encontrado', ['id' => $id]);
                
                if ($request->ajax()) {
                    return response()->json(['error' => 'Empleado no encontrado'], 404);
                }

                return redirect()->route('empresa.empleados.index')
                    ->withErrors(['error' => 'Empleado no encontrado']);
            }

            $empleadoArray = is_array($empleado) ? $empleado : $empleado->toArray();
            $empleadoArray['_id'] = (string) $empleadoArray['_id'];
            
            Log::info('Empleado encontrado', [
                '_id' => $empleadoArray['_id'],
                'nombre' => ($empleadoArray['primer_nombre'] ?? '') . ' ' . ($empleadoArray['primer_apellido'] ?? ''),
                'campos' => array_keys($empleadoArray)
            ]);

            $areas = DB::connection('mongodb_empresas')
                ->collection('areas')
                ->where('empresa_id', $empresaId)
                ->get();

            $centros = DB::connection('mongodb_empresas')
                ->collection('centros')
                ->where('empresa_id', $empresaId)
                ->get();

            $procesos = DB::connection('mongodb_empresas')
                ->collection('procesos')
                ->where('empresa_id', $empresaId)
                ->get();
                
            Log::info('Catálogos cargados', [
                'areas' => $areas->count(),
                'centros' => $centros->count(),
                'procesos' => $procesos->count()
            ]);

            if ($request->ajax()) {
                Log::info('Renderizando vista para AJAX (modal)');
                
                $html = view('admin.gestion-administrativa.empresa.empleados.edit', [
                    'empleado' => $empleadoArray,
                    'areas' => $areas,
                    'centros' => $centros,
                    'procesos' => $procesos,
                    'isModal' => true, // Indicador de que se está cargando en modal
                ])->render();
                
                Log::info('HTML renderizado', ['length' => strlen($html)]);

                return response()->json(['html' => $html]);
            }

            return view('admin.gestion-administrativa.empresa.empleados.edit', [
                'empleado' => $empleadoArray,
                'areas' => $areas,
                'centros' => $centros,
                'procesos' => $procesos,
                'isModal' => false, // Indicador de que es página completa
            ]);

        } catch (\Exception $e) {
            Log::error('Error en EmpleadoController@edit: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Error al cargar el formulario de edición: ' . $e->getMessage()], 500);
            }

            return redirect()->route('empresa.empleados.index')
                ->withErrors(['error' => 'Error al cargar el formulario de edición']);
        }
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'genero' => 'required|in:masculino,femenino,otro',
            'numero_documento' => 'required|string|max:20',
            'tipo_documento' => 'nullable|string|max:50',
            'cargo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'area_id' => 'required|string',
            'proceso_id' => 'nullable|string',
            'centro_id' => 'required|string',
            'ciudad' => 'required|string|max:255',
            'psicosocial_tipo' => 'required|in:A,B',
            'tipo_cargo' => 'required|in:gerencial,profesional,tecnico,auxiliar,operativo',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $empresaId = session('empresa_id');

            // Verificar si el empleado existe
            $empleadoExiste = DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('_id', new \MongoDB\BSON\ObjectId($id))
                ->exists();

            if (!$empleadoExiste) {
                return back()->withErrors(['error' => 'Empleado no encontrado'])->withInput();
            }

            // Verificar si el documento ya existe en otro empleado
            $exists = DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('numero_documento', $request->numero_documento)
                ->where('empresa_id', $empresaId)
                ->where('_id', '!=', new \MongoDB\BSON\ObjectId($id))
                ->exists();

            if ($exists) {
                return back()->withErrors(['numero_documento' => 'El número de documento ya existe en otro empleado'])->withInput();
            }

            // Actualizar empleado
            $psicosocialTipo = !empty($request->psicosocial_tipo) ? strtoupper(trim($request->psicosocial_tipo)) : null;
            $psicosocial = !empty($psicosocialTipo); // true si tiene tipo, false si está vacío
            
            $updateData = [
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'nombre' => trim($request->primer_nombre . ' ' . $request->segundo_nombre),
                'apellidos' => trim($request->primer_apellido . ' ' . $request->segundo_apellido),
                'genero' => $request->genero,
                'numero_documento' => $request->numero_documento,
                'tipo_documento' => $request->tipo_documento ?? 'CC',
                'cargo' => $request->cargo,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'area_id' => $request->area_id,
                'proceso_id' => $request->proceso_id,
                'centro_id' => $request->centro_id,
                'ciudad' => $request->ciudad,
                'psicosocial' => $psicosocial,
                'psicosocial_tipo' => $psicosocialTipo,
                'tipo_cargo' => $request->tipo_cargo,
                'updated_at' => now()
            ];

            DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('_id', new \MongoDB\BSON\ObjectId($id))
                ->update($updateData);

            return redirect()->route('empresa.empleados.index')
                ->with('success', 'Empleado actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar empleado: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar el empleado: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::connection('mongodb_empresas')
                ->collection('empleados')
                ->where('_id', new \MongoDB\BSON\ObjectId($id))
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Empleado eliminado exitosamente']);
            }

            return response()->json(['success' => false, 'message' => 'Empleado no encontrado'], 404);

        } catch (\Exception $e) {
            Log::error('Error al eliminar empleado: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el empleado'], 500);
        }
    }
}

