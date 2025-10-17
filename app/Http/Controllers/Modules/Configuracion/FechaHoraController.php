<?php

namespace App\Http\Controllers\Modules\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empresa;

class FechaHoraController extends Controller
{
    /**
     * Mostrar configuración de fecha y hora
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
            
            // Obtener zonas horarias disponibles
            $zonasHorarias = timezone_identifiers_list();
            
            // Configuración actual
            $configuracion = [
                'zona_horaria' => $empresa->zona_horaria ?? 'America/Bogota',
                'formato_fecha' => $empresa->formato_fecha ?? 'Y-m-d',
                'formato_hora' => $empresa->formato_hora ?? 'H:i:s',
                'primer_dia_semana' => $empresa->primer_dia_semana ?? 1, // 1 = Lunes
                'idioma' => $empresa->idioma ?? 'es',
                'calendario' => $empresa->calendario ?? 'gregoriano'
            ];
            
            return view('modules.configuracion.fechahora.index', compact('empresa', 'configuracion', 'zonasHorarias', 'userData'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración de fecha y hora: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la configuración de fecha y hora');
        }
    }
    
    /**
     * Actualizar configuración de fecha y hora
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'zona_horaria' => 'required|string',
                'formato_fecha' => 'required|string|max:20',
                'formato_hora' => 'required|string|max:20',
                'primer_dia_semana' => 'required|integer|min:0|max:6',
                'idioma' => 'required|string|max:5',
                'calendario' => 'required|string|max:20'
            ]);
            
            $empresaData = session('empresa_data');
            $empresa = Empresa::where('id', $empresaData['id'])->first();
            
            if (!$empresa) {
                return back()->with('error', 'Empresa no encontrada');
            }
            
            // Actualizar configuración de fecha y hora
            $empresa->zona_horaria = $request->zona_horaria;
            $empresa->formato_fecha = $request->formato_fecha;
            $empresa->formato_hora = $request->formato_hora;
            $empresa->primer_dia_semana = $request->primer_dia_semana;
            $empresa->idioma = $request->idioma;
            $empresa->calendario = $request->calendario;
            
            $empresa->save();
            
            return back()->with('success', 'Configuración de fecha y hora actualizada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar configuración de fecha y hora: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la configuración de fecha y hora');
        }
    }
    
    /**
     * Previsualizar formato de fecha y hora
     */
    public function previsualizar(Request $request)
    {
        try {
            $request->validate([
                'formato_fecha' => 'required|string',
                'formato_hora' => 'required|string',
                'zona_horaria' => 'required|string'
            ]);
            
            // Configurar zona horaria
            date_default_timezone_set($request->zona_horaria);
            
            // Obtener fecha actual
            $ahora = new \DateTime();
            
            // Formatear según los formatos proporcionados
            $fechaFormateada = $ahora->format($request->formato_fecha);
            $horaFormateada = $ahora->format($request->formato_hora);
            $fechaHoraFormateada = $ahora->format($request->formato_fecha . ' ' . $request->formato_hora);
            
            return response()->json([
                'fecha' => $fechaFormateada,
                'hora' => $horaFormateada,
                'fechaHora' => $fechaHoraFormateada
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en el formato: ' . $e->getMessage()
            ], 400);
        }
    }
}
