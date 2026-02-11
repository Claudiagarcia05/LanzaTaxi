<?php

namespace App\Http\Controllers\Api;

use App\Services\TransaccionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransaccionController
{
    private $transaccionService;

    public function __construct(TransaccionService $transaccionService)
    {
        $this->transaccionService = $transaccionService;
    }

    /**
     * Procesar pago de viaje
     */
    public function procesarPago(Request $request): JsonResponse
    {
        try {
            $validado = $request->validate([
                'viaje_id' => 'required|integer',
                'metodo' => 'required|in:efectivo,tarjeta,bizum,wallet',
                'monto' => 'required|numeric|min:0.1',
            ]);

            $transaccion = $this->transaccionService->procesarPago(
                $validado['viaje_id'],
                $validado['metodo'],
                $validado['monto']
            );

            return response()->json([
                'success' => true,
                'mensaje' => 'Pago procesado',
                'transaccion' => $transaccion,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener transacciones de usuario
     */
    public function obtenerTransacciones(Request $request, int $usuarioId): JsonResponse
    {
        $tipo = $request->query('tipo');

        $transacciones = $this->transaccionService->obtenerTransacciones($usuarioId, $tipo);

        return response()->json([
            'success' => true,
            'transacciones' => $transacciones,
        ]);
    }

    /**
     * Obtener balance de usuario
     */
    public function obtenerBalance(int $usuarioId): JsonResponse
    {
        $balance = $this->transaccionService->obtenerBalance($usuarioId);

        return response()->json([
            'success' => true,
            'balance' => $balance,
        ]);
    }

    /**
     * Obtener ganancias de período
     */
    public function obtenerGanancias(Request $request, int $usuarioId): JsonResponse
    {
        $periodo = $request->query('periodo', 'mes');

        $ganancias = $this->transaccionService->obtenerGananciasPeríodo($usuarioId, $periodo);

        return response()->json([
            'success' => true,
            'periodo' => $periodo,
            'ganancias' => $ganancias,
        ]);
    }
}
