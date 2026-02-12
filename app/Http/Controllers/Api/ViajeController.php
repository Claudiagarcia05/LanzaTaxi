<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Viaje;
use App\Models\Taxista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ViajeController extends Controller
{
    /**
     * Crear un nuevo viaje
     */
    public function crear(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'origen_lat' => 'required|numeric',
                'origen_lng' => 'required|numeric',
                'origen_direccion' => 'required|string',
                'destino_lat' => 'required|numeric',
                'destino_lng' => 'required|numeric',
                'destino_direccion' => 'required|string',
                'tipo_tarifa' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Datos inválidos',
                    'detalles' => $validator->errors()
                ], 400);
            }

            $user = Auth::user();
            
            // Calcular distancia aproximada (Haversine)
            $distancia = $this->calcularDistancia(
                $request->origen_lat,
                $request->origen_lng,
                $request->destino_lat,
                $request->destino_lng
            );

            // Calcular precio estimado (tarifa base + km)
            $precio_estimado = 3.15 + ($distancia * 0.55); // Tarifa diurna básica

            $viaje = Viaje::create([
                'cliente_id' => $user->id,
                'origen_lat' => $request->origen_lat,
                'origen_lng' => $request->origen_lng,
                'origen_direccion' => $request->origen_direccion,
                'destino_lat' => $request->destino_lat,
                'destino_lng' => $request->destino_lng,
                'destino_direccion' => $request->destino_direccion,
                'distancia' => $distancia,
                'precio_estimado' => $precio_estimado,
                'estado' => 'pendiente',
                'tipo_tarifa' => $request->tipo_tarifa ?? 'diurna',
                'fecha_solicitud' => now(),
            ]);

            return response()->json([
                'message' => 'Viaje creado exitosamente',
                'viaje' => $viaje->load(['cliente', 'taxista'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear viaje',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un viaje específico
     */
    public function obtener($id)
    {
        try {
            $viaje = Viaje::with(['cliente', 'taxista'])->findOrFail($id);
            
            return response()->json($viaje);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Viaje no encontrado'
            ], 404);
        }
    }

    /**
     * Aceptar un viaje (taxista)
     */
    public function aceptar(Request $request, $id)
    {
        try {
            $viaje = Viaje::findOrFail($id);
            
            if ($viaje->estado !== 'pendiente') {
                return response()->json([
                    'error' => 'El viaje ya no está disponible'
                ], 400);
            }

            $taxistaId = $request->input('taxista_id');
            
            $viaje->update([
                'taxista_id' => $taxistaId,
                'estado' => 'aceptado',
                'fecha_aceptacion' => now(),
            ]);

            return response()->json([
                'message' => 'Viaje aceptado exitosamente',
                'viaje' => $viaje->load(['cliente', 'taxista'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al aceptar viaje',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar un viaje
     */
    public function iniciar($id)
    {
        try {
            $viaje = Viaje::findOrFail($id);
            
            if ($viaje->estado !== 'aceptado') {
                return response()->json([
                    'error' => 'El viaje no puede iniciarse en su estado actual'
                ], 400);
            }

            $viaje->update([
                'estado' => 'en_curso',
                'fecha_inicio' => now(),
            ]);

            return response()->json([
                'message' => 'Viaje iniciado',
                'viaje' => $viaje->load(['cliente', 'taxista'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al iniciar viaje',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalizar un viaje
     */
    public function finalizar(Request $request, $id)
    {
        try {
            $viaje = Viaje::findOrFail($id);
            
            if ($viaje->estado !== 'en_curso') {
                return response()->json([
                    'error' => 'El viaje no puede finalizarse en su estado actual'
                ], 400);
            }

            $precio_final = $request->input('precio_final', $viaje->precio_estimado);

            $viaje->update([
                'estado' => 'completado',
                'fecha_fin' => now(),
                'precio_final' => $precio_final,
            ]);

            return response()->json([
                'message' => 'Viaje finalizado',
                'viaje' => $viaje->load(['cliente', 'taxista'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al finalizar viaje',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar un viaje
     */
    public function cancelar(Request $request, $id)
    {
        try {
            $viaje = Viaje::findOrFail($id);
            
            if (in_array($viaje->estado, ['completado', 'cancelado'])) {
                return response()->json([
                    'error' => 'El viaje no puede cancelarse'
                ], 400);
            }

            $viaje->update([
                'estado' => 'cancelado',
                'comentario' => $request->input('motivo', 'Cancelado por el usuario'),
            ]);

            return response()->json([
                'message' => 'Viaje cancelado',
                'viaje' => $viaje->load(['cliente', 'taxista'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cancelar viaje',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener viajes del usuario autenticado
     */
    public function misViajes()
    {
        try {
            $user = Auth::user();
            
            $viajes = Viaje::where('cliente_id', $user->id)
                ->with(['taxista'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'viajes' => $viajes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar viajes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener viajes pendientes (disponibles para taxistas)
     */
    public function pendientes()
    {
        try {
            $viajes = Viaje::where('estado', 'pendiente')
                ->with(['cliente'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'viajes' => $viajes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar viajes pendientes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular distancia entre dos puntos (fórmula Haversine)
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $radioTierra = 6371; // Radio de la Tierra en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distancia = $radioTierra * $c;

        return round($distancia, 2);
    }
}
