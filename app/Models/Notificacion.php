<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'viaje_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relación con Viaje
     */
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Viaje::class);
    }

    /**
     * Tipos válidos de notificación
     */
    public static function tiposValidos(): array
    {
        return [
            'viaje_aceptado',
            'viaje_cancelado',
            'llegada_inminente',
            'nuevo_viaje',
            'pago_procesado',
            'evaluacion_pendiente',
        ];
    }

    /**
     * Marcar como leída
     */
    public function marcarLeida(): void
    {
        $this->update(['leida' => true]);
    }

    /**
     * Obtener notificaciones no leídas
     */
    public static function noLeidas(int $usuarioId)
    {
        return self::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
