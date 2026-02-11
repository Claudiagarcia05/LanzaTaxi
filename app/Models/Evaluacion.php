<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    protected $table = 'evaluaciones';

    protected $fillable = [
        'viaje_id',
        'evaluador_id',
        'evaluado_id',
        'calificacion',
        'comentario',
        'aspecto',
    ];

    protected $casts = [
        'calificacion' => 'integer',
    ];

    /**
     * Relación con Viaje
     */
    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Viaje::class);
    }

    /**
     * Relación con evaluador (Usuario)
     */
    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'evaluador_id');
    }

    /**
     * Relación con evaluado (Usuario)
     */
    public function evaluado(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'evaluado_id');
    }

    /**
     * Aspectos válidos
     */
    public static function aspectosValidos(): array
    {
        return ['puntualidad', 'limpieza', 'trato', 'seguridad'];
    }

    /**
     * Validar calificación
     */
    public static function calificacionValida(int $calificacion): bool
    {
        return $calificacion >= 1 && $calificacion <= 5;
    }
}
