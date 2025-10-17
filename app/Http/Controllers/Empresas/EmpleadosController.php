<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;

class EmpleadosController extends Controller
{
    private $mongoClient;
    private $database;
    
    public function __construct()
    {
        $this->mongoClient = new Client("mongodb://localhost:27017");
        $this->database = $this->mongoClient->selectDatabase('gir365');
    }

    /**
     * Muestra el índice de empleados
     */
    public function index(Request $request)
    {
        try {
            // Verificar autenticación
            if (!session('usuario_data')) {
                return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a empleados');
            }

            $userData = session('usuario_data');
            $empresaData = session('empresa_data');

            // Obtener empleados de la empresa
            $empleados = $this->database->selectCollection('empleados')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            // Obtener áreas para filtros
            $areas = $this->database->selectCollection('areas')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            // Obtener centros para filtros
            $centros = $this->database->selectCollection('centros')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            // Estadísticas de empleados
            $totalEmpleados = count($empleados);
            $empleadosActivos = count(array_filter($empleados, function($emp) {
                return ($emp['estado'] ?? 'activo') === 'activo';
            }));
            $empleadosInactivos = $totalEmpleados - $empleadosActivos;

            $estadisticas = [
                'total' => $totalEmpleados,
                'activos' => $empleadosActivos,
                'inactivos' => $empleadosInactivos,
                'porcentaje_activos' => $totalEmpleados > 0 ? round(($empleadosActivos / $totalEmpleados) * 100, 2) : 0
            ];

            return view('empleados.index', compact('empleados', 'areas', 'centros', 'estadisticas'));
            
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Error al cargar empleados: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear un nuevo empleado
     */
    public function create()
    {
        try {
            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Obtener áreas disponibles
            $areas = $this->database->selectCollection('areas')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            // Obtener centros disponibles
            $centros = $this->database->selectCollection('centros')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            return view('empleados.create', compact('areas', 'centros'));
            
        } catch (\Exception $e) {
            return redirect()->route('empleados.index')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar un nuevo empleado
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'documento' => 'required|string|unique:empleados,documento',
                'email' => 'required|email|unique:empleados,email',
                'area_id' => 'required',
                'centro_id' => 'required',
                'cargo' => 'required|string|max:255'
            ]);

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Crear nuevo empleado
            $nuevoEmpleado = [
                'empresa_id' => $empresaData->_id,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'documento' => $request->documento,
                'email' => $request->email,
                'area_id' => new \MongoDB\BSON\ObjectId($request->area_id),
                'centro_id' => new \MongoDB\BSON\ObjectId($request->centro_id),
                'cargo' => $request->cargo,
                'telefono' => $request->telefono,
                'estado' => 'activo',
                'fecha_creacion' => new \MongoDB\BSON\UTCDateTime(),
                'usuario_creador_id' => $userData['_id']
            ];

            $resultado = $this->database->selectCollection('empleados')->insertOne($nuevoEmpleado);

            if ($resultado->getInsertedCount() > 0) {
                return redirect()->route('empleados.index')
                    ->with('success', 'Empleado creado exitosamente');
            } else {
                throw new \Exception('No se pudo crear el empleado');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar un empleado específico
     */
    public function show($id)
    {
        try {
            $empleado = $this->database->selectCollection('empleados')
                ->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if (!$empleado) {
                return redirect()->route('empleados.index')
                    ->with('error', 'Empleado no encontrado');
            }

            // Obtener datos relacionados
            $area = $this->database->selectCollection('areas')
                ->findOne(['_id' => $empleado['area_id']]);

            $centro = $this->database->selectCollection('centros')
                ->findOne(['_id' => $empleado['centro_id']]);

            return view('empleados.show', compact('empleado', 'area', 'centro'));
            
        } catch (\Exception $e) {
            return redirect()->route('empleados.index')
                ->with('error', 'Error al mostrar empleado: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        try {
            $empleado = $this->database->selectCollection('empleados')
                ->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if (!$empleado) {
                return redirect()->route('empleados.index')
                    ->with('error', 'Empleado no encontrado');
            }

            $userData = \App\Http\Controllers\AuthController::user();
            $empresaData = \App\Http\Controllers\AuthController::empresa();

            // Obtener áreas disponibles
            $areas = $this->database->selectCollection('areas')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            // Obtener centros disponibles
            $centros = $this->database->selectCollection('centros')
                ->find(['empresa_id' => $empresaData->_id])
                ->toArray();

            return view('empleados.edit', compact('empleado', 'areas', 'centros'));
            
        } catch (\Exception $e) {
            return redirect()->route('empleados.index')
                ->with('error', 'Error al cargar empleado: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar empleado
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'documento' => 'required|string',
                'email' => 'required|email',
                'area_id' => 'required',
                'centro_id' => 'required',
                'cargo' => 'required|string|max:255'
            ]);

            $datosActualizacion = [
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'documento' => $request->documento,
                'email' => $request->email,
                'area_id' => new \MongoDB\BSON\ObjectId($request->area_id),
                'centro_id' => new \MongoDB\BSON\ObjectId($request->centro_id),
                'cargo' => $request->cargo,
                'telefono' => $request->telefono,
                'fecha_actualizacion' => new \MongoDB\BSON\UTCDateTime()
            ];

            $resultado = $this->database->selectCollection('empleados')->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => $datosActualizacion]
            );

            if ($resultado->getModifiedCount() > 0) {
                return redirect()->route('empleados.index')
                    ->with('success', 'Empleado actualizado exitosamente');
            } else {
                return redirect()->back()
                    ->with('warning', 'No se realizaron cambios en el empleado');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar empleado
     */
    public function destroy($id)
    {
        try {
            // En lugar de eliminar, cambiar estado a inactivo
            $resultado = $this->database->selectCollection('empleados')->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'estado' => 'inactivo',
                    'fecha_inactivacion' => new \MongoDB\BSON\UTCDateTime()
                ]]
            );

            if ($resultado->getModifiedCount() > 0) {
                return redirect()->route('empleados.index')
                    ->with('success', 'Empleado desactivado exitosamente');
            } else {
                return redirect()->back()
                    ->with('error', 'No se pudo desactivar el empleado');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al desactivar empleado: ' . $e->getMessage());
        }
    }
}
