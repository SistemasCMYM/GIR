<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use MongoDB\Client;
use MongoDB\Database;
use Carbon\Carbon;

class PlanesController extends Controller
{
    private $mongoClient;
    private $database;
    
    public function __construct()
    {
        $this->mongoClient = new Client("mongodb://localhost:27017");
        $this->database = $this->mongoClient->selectDatabase('gir365');
    }

    /**
     * Muestra el índice de planes de acción
     */
    public function index(Request $request)
    {
        try {
            // Verificar autenticación
            if (!session('usuario_data')) {
                return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a planes');
            }

            $userData = session('usuario_data');
            $empresaData = session('empresa_data');

            // Obtener planes de la empresa
            $planes = $this->database->selectCollection('planes')
                ->find(['empresa_id' => $empresaData['_id']])
                ->toArray();

            // Estadísticas de planes
            $totalPlanes = count($planes);
            $planesActivos = count(array_filter($planes, function($plan) {
                return ($plan['estado'] ?? 'pendiente') === 'activo';
            }));
            $planesCompletados = count(array_filter($planes, function($plan) {
                return ($plan['estado'] ?? 'pendiente') === 'completado';
            }));
            $planesPendientes = $totalPlanes - $planesActivos - $planesCompletados;

            $estadisticas = [
                'total' => $totalPlanes,
                'activos' => $planesActivos,
                'completados' => $planesCompletados,
                'pendientes' => $planesPendientes
            ];

            return view('planes.index', compact('planes', 'estadisticas'));
            
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Error al cargar planes: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear un nuevo plan
     */
    public function create()
    {
        try {
            $userData = session('usuario_data');
            $empresaData = session('empresa_data');

            return view('planes.create');
            
        } catch (\Exception $e) {
            return redirect()->route('planes.index')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar un nuevo plan
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio'
            ]);

            $userData = session('usuario_data');
            $empresaData = session('empresa_data');

            // Crear nuevo plan
            $nuevoPlan = [
                'empresa_id' => $empresaData['_id'],
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => new \MongoDB\BSON\UTCDateTime(strtotime($request->fecha_inicio) * 1000),
                'fecha_fin' => new \MongoDB\BSON\UTCDateTime(strtotime($request->fecha_fin) * 1000),
                'estado' => 'pendiente',
                'fecha_creacion' => new \MongoDB\BSON\UTCDateTime(),
                'usuario_creador_id' => $userData['_id']
            ];

            $resultado = $this->database->selectCollection('planes')->insertOne($nuevoPlan);

            if ($resultado->getInsertedCount() > 0) {
                return redirect()->route('planes.index')
                    ->with('success', 'Plan de acción creado exitosamente');
            } else {
                throw new \Exception('No se pudo crear el plan');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar un plan específico
     */
    public function show($id)
    {
        try {
            $plan = $this->database->selectCollection('planes')
                ->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if (!$plan) {
                return redirect()->route('planes.index')
                    ->with('error', 'Plan no encontrado');
            }

            return view('planes.show', compact('plan'));
            
        } catch (\Exception $e) {
            return redirect()->route('planes.index')
                ->with('error', 'Error al mostrar plan: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        try {
            $plan = $this->database->selectCollection('planes')
                ->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if (!$plan) {
                return redirect()->route('planes.index')
                    ->with('error', 'Plan no encontrado');
            }

            return view('planes.edit', compact('plan'));
            
        } catch (\Exception $e) {
            return redirect()->route('planes.index')
                ->with('error', 'Error al cargar plan: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar plan
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'estado' => 'required|in:pendiente,activo,completado,cancelado'
            ]);

            $datosActualizacion = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => new \MongoDB\BSON\UTCDateTime(strtotime($request->fecha_inicio) * 1000),
                'fecha_fin' => new \MongoDB\BSON\UTCDateTime(strtotime($request->fecha_fin) * 1000),
                'estado' => $request->estado,
                'fecha_actualizacion' => new \MongoDB\BSON\UTCDateTime()
            ];

            $resultado = $this->database->selectCollection('planes')->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => $datosActualizacion]
            );

            if ($resultado->getModifiedCount() > 0) {
                return redirect()->route('planes.index')
                    ->with('success', 'Plan actualizado exitosamente');
            } else {
                return redirect()->back()
                    ->with('warning', 'No se realizaron cambios en el plan');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar plan
     */
    public function destroy($id)
    {
        try {
            $resultado = $this->database->selectCollection('planes')->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'estado' => 'cancelado',
                    'fecha_cancelacion' => new \MongoDB\BSON\UTCDateTime()
                ]]
            );

            if ($resultado->getModifiedCount() > 0) {
                return redirect()->route('planes.index')
                    ->with('success', 'Plan cancelado exitosamente');
            } else {
                return redirect()->back()
                    ->with('error', 'No se pudo cancelar el plan');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cancelar plan: ' . $e->getMessage());
        }
    }
}
