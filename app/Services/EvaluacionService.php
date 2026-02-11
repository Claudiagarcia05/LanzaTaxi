<?php

namespace App\Services;

use App\Models\Evaluacion;
use App\Models\Viaje;

class EvaluacionService
{
    /**
     * Crear evaluación
     */
    public function crearEvaluacion(int $viajeId, int $evaluadorId, int $evaluadoId, 
                                   int $calificacion, string $comentario, string $aspecto): Evaluacion
    {
        $viaje = Viaje::findOrFail($viajeId);

        // Validar que el viaje esté completado
        if ($viaje->estado !== 'completado') {
            throw new \Exception('Solo se pueden evaluar viajes completados');
        }

        // Validar al menos uno de los participantes en el viaje
        if ($evaluadorId !== $viaje->cliente_id && $evaluadorId !== $viaje->taxista_id) {
            throw new \Exception('Solo participantes del viaje pueden evaluar');
        }

        // Validar calificación
        if (!Evaluacion::calificacionValida($calificacion)) {
            throw new \Exception('Calificación debe estar entre 1 y 5');
        }

        // Validar aspecto
        if (!in_array($aspecto, Evaluacion::aspectosValidos())) {
            throw new \Exception('Aspecto no válido');
        }

        $evaluacion = Evaluacion::create([
            'viaje_id' => $viajeId,
            'evaluador_id' => $evaluadorId,
            'evaluado_id' => $evaluadoId,
            'calificacion' => $calificacion,
            'comentario' => $comentario,
            'aspecto' => $aspecto,
        ]);

        // Actualizar calificación promedio del evaluado
        $this->actualizarCalificacionPromedio($evaluadoId);

        return $evaluacion;
    }

    /**
     * Actualizar calificación promedio
     */
    private function actualizarCalificacionPromedio(int $usuarioId): void
    {
        $promedio = Evaluacion::where('evaluado_id', $usuarioId)
            ->avg('calificacion') ?? 5;

        // Actualizar según tipo de usuario
        $usuario = \App\Models\Usuario::find($usuarioId);

        if ($usuario->esTaxista() && $usuario->taxista) {
            $usuario->taxista->update(['calificacion' => round($promedio, 1)]);
        }
    }

    /**
     * Obtener evaluaciones de un usuario
     */
    public function obtenerEvaluaciones(int $usuarioId, string $aspecto = null)
    {
        $query = Evaluacion::where('evaluado_id', $usuarioId);

        if ($aspecto) {
            $query->where('aspecto', $aspecto);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtener calificación promedio
     */
    public function obtenerCalificacionPromedio(int $usuarioId): float
    {
        return Evaluacion::where('evaluado_id', $usuarioId)
            ->avg('calificacion') ?? 5;
    }

    /**
     * Verificar si usuario puede evaluar un viaje
     */
    public function puedeEvaluar(int $usuarioId, int $viajeId): bool
    {
        $viaje = Viaje::find($viajeId);

        if (!$viaje || $viaje->estado !== 'completado') {
            return false;
        }

        return $usuarioId === $viaje->cliente_id || $usuarioId === $viaje->taxista_id;
    }
}
