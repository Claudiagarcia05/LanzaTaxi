<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taxista;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaxistaController extends Controller
{
    /**
     * Listar taxistas
     */
    public function listar()
    {
        $taxistas = Taxista::with('user')->get();

        return response()->json([
            'taxistas' => $taxistas,
        ]);
    }

    /**
     * Obtener taxista por id
     */
    public function obtener($id)
    {
        try {
            $taxista = Taxista::with('user')->findOrFail($id);

            return response()->json($taxista);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Taxista no encontrado',
            ], 404);
        }
    }

    /**
     * Cambiar estado del taxista
     */
    public function cambiarEstado(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:libre,ocupado,en_servicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Estado inválido',
                'detalles' => $validator->errors(),
            ], 400);
        }

        try {
            $taxista = Taxista::findOrFail($id);
            $taxista->update(['estado' => $request->estado]);

            return response()->json([
                'message' => 'Estado actualizado',
                'taxista' => $taxista,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Taxista no encontrado',
            ], 404);
        }
    }

    /**
     * Actualizar ubicacion del taxista
     */
    public function actualizarUbicacion(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Ubicacion invalida',
                'detalles' => $validator->errors(),
            ], 400);
        }

        try {
            $taxista = Taxista::findOrFail($id);
            $taxista->update([
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ]);

            return response()->json([
                'message' => 'Ubicacion actualizada',
                'taxista' => $taxista,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Taxista no encontrado',
            ], 404);
        }
    }

    /**
     * Obtener taxistas cercanos
     */
    public function cercanos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'radio' => 'nullable|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Parametros invalidos',
                'detalles' => $validator->errors(),
            ], 400);
        }

        $radio = $request->input('radio', 5);
        $lat = $request->latitud;
        $lng = $request->longitud;
        $delta = $radio / 111; // aproximacion en grados

        $taxistas = Taxista::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->whereBetween('latitud', [$lat - $delta, $lat + $delta])
            ->whereBetween('longitud', [$lng - $delta, $lng + $delta])
            ->get();

        return response()->json([
            'taxistas' => $taxistas,
        ]);
    }

    /**
     * Obtener viajes del taxista autenticado
     */
    public function misViajes()
    {
        try {
            $user = Auth::user();

            if (!$user || $user->role !== 'taxista') {
                return response()->json([
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $taxista = $user->taxista;

            if (!$taxista) {
                return response()->json([
                    'error' => 'Taxista no encontrado',
                ], 404);
            }

            $viajes = Viaje::where('taxista_id', $taxista->id)
                ->with(['cliente'])
                ->orderBy('fecha_solicitud', 'desc')
                ->get()
                ->map(function ($viaje) {
                    $viaje->cliente_nombre = $viaje->cliente ? $viaje->cliente->nombre : null;
                    return $viaje;
                });

            return response()->json([
                'viajes' => $viajes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar viajes',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
