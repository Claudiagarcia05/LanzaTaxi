<?php

namespace App\Http\Controllers\Api;

use App\Services\EvaluacionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EvaluacionController
{
    private $evaluacionService;

    public function __construct(EvaluacionService $evaluacionService)
    {
        $this->evaluacionService = $evaluacionService;
    }

    /**
     * Crear evaluación
     */
    public function crear(Request $request): JsonResponse
    {
        try {
            $validado = $request->validate([
                'viaje_id' => 'required|integer',
                'evaluador_id' => 'required|integer',
                'evaluado_id' => 'required|integer',
                'calificacion' => 'required|integer|min:1|max:5',
                'comentario' => 'nullable|string',
                'aspecto' => 'required|in:puntualidad,limpieza,trato,seguridad',
            ]);

            $evaluacion = $this->evaluacionService->crearEvaluacion(
                $validado['viaje_id'],
                $validado['evaluador_id'],
                $validado['evaluado_id'],
                $validado['calificacion'],
                $validado['comentario'] ?? '',
                $validado['aspecto']
            );

            return response()->json([
                'success' => true,
                'mensaje' => 'Evaluación creada',
                'evaluacion' => $evaluacion,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener evaluaciones de usuario
     */
    public function obtenerEvaluaciones(Request $request, int $usuarioId): JsonResponse
    {
        $aspecto = $request->query('aspecto');

        $evaluaciones = $this->evaluacionService->obtenerEvaluaciones($usuarioId, $aspecto);

        return response()->json([
            'success' => true,
            'evaluaciones' => $evaluaciones,
        ]);
    }

    /**
     * Obtener calificación promedio
     */
    public function obtenerCalificacion(int $usuarioId): JsonResponse
    {
        $promedio = $this->evaluacionService->obtenerCalificacionPromedio($usuarioId);

        return response()->json([
            'success' => true,
            'calificacion_promedio' => $promedio,
        ]);
    }

    /**
     * Verificar si usuario puede evaluar
     */
    public function puedeEvaluar(int $usuarioId, int $viajeId): JsonResponse
    {
        $puede = $this->evaluacionService->puedeEvaluar($usuarioId, $viajeId);

        return response()->json([
            'success' => true,
            'puede_evaluar' => $puede,
        ]);
    }
}
