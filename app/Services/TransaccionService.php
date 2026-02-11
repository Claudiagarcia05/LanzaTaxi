<?php

namespace App\Services;

use App\Models\Evaluacion;
use App\Models\Transaccion;
use App\Models\Viaje;

class TransaccionService
{
    /**
     * Procesar pago de viaje
     */
    public function procesarPago(int $viajeId, string $metodo, float $monto): Transaccion
    {
        $viaje = Viaje::findOrFail($viajeId);

        if ($viaje->pagado) {
            throw new \Exception('Viaje ya pagado');
        }

        $transaccion = Transaccion::create([
            'viaje_id' => $viajeId,
            'usuario_id' => $viaje->cliente_id,
            'tipo' => 'pago',
            'monto' => $monto,
            'metodo' => $metodo,
            'estado' => 'completado',
        ]);

        // Marcar viaje como pagado
        $viaje->update(['pagado' => true]);

        // Crear transacción de comisión para el taxista
        $this->procesarComision($viajeId, $monto);

        return $transaccion;
    }

    /**
     * Procesar comisión para el taxista
     */
    private function procesarComision(int $viajeId, float $montoViaje): void
    {
        $viaje = Viaje::find($viajeId);

        if (!$viaje->taxista_id) {
            return;
        }

        // Comisión del 15% para la plataforma
        $comision = $montoViaje * 0.15;
        $ingresoTaxista = $montoViaje - $comision;

        Transaccion::create([
            'viaje_id' => $viajeId,
            'usuario_id' => $viaje->taxista_id,
            'tipo' => 'pago',
            'monto' => $ingresoTaxista,
            'metodo' => 'wallet',
            'estado' => 'completado',
        ]);
    }

    /**
     * Obtener transacciones de un usuario
     */
    public function obtenerTransacciones(int $usuarioId, string $tipo = null)
    {
        $query = Transaccion::where('usuario_id', $usuarioId);

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtener balance del usuario
     */
    public function obtenerBalance(int $usuarioId): float
    {
        return Transaccion::where('usuario_id', $usuarioId)
            ->where('estado', 'completado')
            ->sum('monto') ?? 0;
    }

    /**
     * Obtener ganancias de un período
     */
    public function obtenerGananciasPeríodo(int $usuarioId, string $periodo = 'mes'): float
    {
        $query = Transaccion::where('usuario_id', $usuarioId)
            ->where('estado', 'completado')
            ->where('tipo', 'pago');

        return match($periodo) {
            'hoy' => $query->whereDate('created_at', today())->sum('monto'),
            'semana' => $query->whereBetween('created_at', [today()->subDays(7), today()])->sum('monto'),
            'mes' => $query->whereMonth('created_at', now()->month)->sum('monto'),
            'año' => $query->whereYear('created_at', now()->year)->sum('monto'),
            default => 0,
        } ?? 0;
    }
}
