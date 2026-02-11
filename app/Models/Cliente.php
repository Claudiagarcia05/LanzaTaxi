<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'usuario_id',
        'direccion',
        'ciudad',
        'pais',
        'metodo_pago',
    ];

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Viajes del cliente
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class, 'cliente_id', 'usuario_id');
    }

    /**
     * Obtener nombre del cliente
     */
    public function getNombreAttribute(): string
    {
        return $this->usuario->nombre ?? '';
    }

    /**
     * Obtener email del cliente
     */
    public function getEmailAttribute(): string
    {
        return $this->usuario->email ?? '';
    }

    /**
     * Obtener calificación promedio
     */
    public function calificacionPromedio(): float
    {
        return $this->viajes()
            ->where('estado', 'completado')
            ->whereNotNull('calificacion_taxista')
            ->avg('calificacion_taxista') ?? 0;
    }

    /**
     * Obtener total de viajes completados
     */
    public function totalViajes(): int
    {
        return $this->viajes()
            ->where('estado', 'completado')
            ->count();
    }
}
