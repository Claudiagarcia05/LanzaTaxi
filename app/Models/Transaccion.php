<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaccion extends Model
{
    protected $table = 'transacciones';

    protected $fillable = [
        'viaje_id',
        'usuario_id',
        'tipo',
        'monto',
        'metodo',
        'estado',
        'referencia',
    ];

    protected $casts = [
        'monto' => 'float',
    ];

    /**
     * Relación con Viaje
     */
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Viaje::class);
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Tipos válidos de transacción
     */
    public static function tiposValidos(): array
    {
        return ['pago', 'comisión', 'reembolso'];
    }

    /**
     * Métodos de pago válidos
     */
    public static function metodosValidos(): array
    {
        return ['efectivo', 'tarjeta', 'bizum', 'wallet'];
    }

    /**
     * Estados válidos
     */
    public static function estadosValidos(): array
    {
        return ['pendiente', 'completado', 'fallido'];
    }

    /**
     * Obtener transacciones completadas
     */
    public static function completadas()
    {
        return self::where('estado', 'completado')->get();
    }

    /**
     * Obtener ganancias totales de un usuario
     */
    public static function gananciasTotales(int $usuarioId): float
    {
        return self::where('usuario_id', $usuarioId)
            ->where('tipo', 'pago')
            ->where('estado', 'completado')
            ->sum('monto') ?? 0;
    }
}
