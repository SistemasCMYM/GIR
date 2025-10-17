<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consentimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ConsentimientosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $consentimientos = Consentimiento::orderBy('fecha_creacion', 'desc')
                ->paginate(10);

            $stats = [
                'total' => Consentimiento::count(),
                'activos' => Consentimiento::activos()->count(),
                'plantillas' => Consentimiento::plantillas()->count(),
                'versiones' => Consentimiento::distinct('titulo')->count()
            ];

            return view('admin.gestion-instrumentos.consentimientos.index', compact('consentimientos', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error al cargar consentimientos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los consentimientos');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gestion-instrumentos.consentimientos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'contenido' => 'required|string',
            'tipo' => 'required|string|in:datos_personales,evaluacion_psicosocial,tratamiento_datos,general,personalizado',
            'plantilla' => 'boolean'
        ]);

        try {
            $consentimiento = new Consentimiento();
            $consentimiento->fill($request->all());
            $consentimiento->usuario_creador = Auth::id();
            $consentimiento->items_total = 1; // Un consentimiento tiene un ítem principal
            
            $consentimiento->save();

            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('success', 'Consentimiento creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el consentimiento');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $consentimiento = Consentimiento::findOrFail($id);
            return view('admin.gestion-instrumentos.consentimientos.show', compact('consentimiento'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar consentimiento: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', 'Consentimiento no encontrado');
        }
    }

    /**
     * Test method - TEMPORAL
     */
    public function test()
    {
        $consentimiento = new \stdClass();
        $consentimiento->id = 1;
        $consentimiento->nombre = 'Consentimiento de Prueba';
        $consentimiento->tipo = 'datos_personales';
        $consentimiento->descripcion = 'Descripción de prueba';
        $consentimiento->contenido = 'Contenido de prueba para verificar que la vista funciona correctamente.';
        $consentimiento->version = '1.0';
        $consentimiento->estado = 1;
        $consentimiento->created_at = now();
        $consentimiento->updated_at = now();
        
        return view('admin.gestion-instrumentos.consentimientos.test', compact('consentimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $consentimiento = Consentimiento::findOrFail($id);
            return view('admin.gestion-instrumentos.consentimientos.edit', compact('consentimiento'));
        } catch (\Exception $e) {
            Log::error('Error al editar consentimiento: ' . $e->getMessage());
            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('error', 'Consentimiento no encontrado');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'contenido' => 'required|string',
            'tipo' => 'required|string|in:datos_personales,evaluacion_psicosocial,tratamiento_datos,general,personalizado'
        ]);

        try {
            $consentimiento = Consentimiento::findOrFail($id);
            $consentimiento->fill($request->all());
            $consentimiento->usuario_modificador = Auth::id();
            $consentimiento->version = $consentimiento->generarSiguienteVersion();
            
            $consentimiento->save();

            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('success', 'Consentimiento actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el consentimiento');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $consentimiento = Consentimiento::findOrFail($id);
            $consentimiento->delete();

            return redirect()->route('gestion-instrumentos.consentimientos.index')
                ->with('success', 'Consentimiento eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el consentimiento');
        }
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleEstado(string $id)
    {
        try {
            $consentimiento = Consentimiento::findOrFail($id);
            $consentimiento->estado = !$consentimiento->estado;
            $consentimiento->save();

            $mensaje = $consentimiento->estado ? 'Consentimiento activado' : 'Consentimiento desactivado';
            
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'nuevo_estado' => $consentimiento->estado
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del consentimiento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado'
            ], 500);
        }
    }

    /**
     * Clone the specified resource.
     */
    public function clonar(string $id)
    {
        try {
            $consentimiento = Consentimiento::findOrFail($id);
            $nuevo_consentimiento = $consentimiento->clonar();

            return redirect()->route('gestion-instrumentos.consentimientos.edit', $nuevo_consentimiento->_id)
                ->with('success', 'Consentimiento clonado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al clonar consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al clonar el consentimiento');
        }
    }

    /**
     * Generate reports for the specified resource.
     */
    public function informes(string $id)
    {
        try {
            $consentimiento = Consentimiento::findOrFail($id);
            
            // Aquí implementarías la lógica de generación de informes
            // Por ahora, redirigimos a una vista de informes
            
            return view('admin.gestion-instrumentos.consentimientos.informes', compact('consentimiento'));
        } catch (\Exception $e) {
            Log::error('Error al generar informes de consentimiento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al generar los informes');
        }
    }

    /**
     * Get plantillas for AJAX requests
     */
    public function getPlantillas()
    {
        try {
            $plantillas = Consentimiento::plantillas()
                ->activos()
                ->select('_id', 'titulo', 'tipo', 'contenido')
                ->get();

            return response()->json([
                'success' => true,
                'plantillas' => $plantillas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener plantillas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las plantillas'
            ], 500);
        }
    }
}
