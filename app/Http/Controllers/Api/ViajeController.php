<?php

namespace App\Http\Controllers\Api;

use App\Models\Viaje;
use App\Services\ViajeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ViajeController
{
    private $viajeService;

    public function __construct(ViajeService $viajeService)
    {
        $this->viajeService = $viajeService;
    }

    /**
     * Crear nuevo viaje
     */
    public function crear(Request $request): JsonResponse
    {
        try {
            $validado = $request->validate([
                'cliente_id' => 'required|integer',
                'origen_lat' => 'required|numeric',
                'origen_lng' => 'required|numeric',
                'origen_direccion' => 'required|string',
                'destino_lat' => 'required|numeric',
                'destino_lng' => 'required|numeric',
                'destino_direccion' => 'required|string',
                'tarifa_id' => 'required|integer',
                'ocupantes' => 'nullable|integer|min:1|max:4',
                'metodo_pago' => 'nullable|string',
            ]);

            $viaje = $this->viajeService->crearViaje($validado);

            return response()->json([
                'success' => true,
                'mensaje' => 'Viaje creado',
                'viaje' => $viaje->load('cliente', 'tarifa'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener viaje por ID
     */
    public function obtener(int $id): JsonResponse
    {
        $viaje = Viaje::find($id);

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'error' => 'Viaje no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'viaje' => $viaje->load('cliente', 'taxista', 'tarifa', 'evaluaciones'),
        ]);
    }

    /**
     * Aceptar viaje
     */
    public function aceptar(Request $request, int $id): JsonResponse
    {
        try {
            $validado = $request->validate([
                'taxista_id' => 'required|integer',
            ]);

            $viaje = $this->viajeService->aceptarViaje($id, $validado['taxista_id']);

            return response()->json([
                'success' => true,
                'mensaje' => 'Viaje aceptado',
                'viaje' => $viaje,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Completar viaje
     */
    public function completar(int $id): JsonResponse
    {
        try {
            $viaje = $this->viajeService->completarViaje($id);

            return response()->json([
                'success' => true,
                'mensaje' => 'Viaje completado',
                'viaje' => $viaje,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancelar viaje
     */
    public function cancelar(Request $request, int $id): JsonResponse
    {
        try {
            $validado = $request->validate([
                'razon' => 'nullable|string',
            ]);

            $viaje = $this->viajeService->cancelarViaje($id, $validado['razon'] ?? null);

            return response()->json([
                'success' => true,
                'mensaje' => 'Viaje cancelado',
                'viaje' => $viaje,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Listar viajes del cliente
     */
    public function viajesCliente(int $clienteId): JsonResponse
    {
        $viajes = $this->viajeService->obtenerViajesCliente($clienteId);

        return response()->json([
            'success' => true,
            'viajes' => $viajes,
        ]);
    }

    /**
     * Listar viajes activos del taxista
     */
    public function viajesToxistaActivos(int $taxistaId): JsonResponse
    {
        $viajes = $this->viajeService->obtenerViajesActivosTaxista($taxistaId);

        return response()->json([
            'success' => true,
            'viajes' => $viajes,
        ]);
    }

    /**
     * Obtener taxistas disponibles cercanos
     */
    public function taxistasCercanos(Request $request): JsonResponse
    {
        $validado = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radio' => 'nullable|numeric|min:1|max:50',
        ]);

        $taxistas = $this->viajeService->obtenerTaxistasCercanos(
            $validado['lat'],
            $validado['lng'],
            $validado['radio'] ?? 5
        );

        return response()->json([
            'success' => true,
            'taxistas' => $taxistas,
        ]);
    }
}
