<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxista extends Model
{
    protected $table = 'taxistas';

    protected $fillable = [
        'usuario_id',
        'licencia_nro',
        'vehiculo_id',
        'estado',
        'ubicacion_lat',
        'ubicacion_lng',
        'calificacion',
        'municipio',
        'comisiones_pagadas',
    ];

    protected $casts = [
        'calificacion' => 'float',
        'ubicacion_lat' => 'float',
        'ubicacion_lng' => 'float',
        'comisiones_pagadas' => 'boolean',
    ];

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relación con Vehículo
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Viajes del taxista
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class, 'taxista_id', 'usuario_id');
    }

    /**
     * Verificar si está disponible
     */
    public function estaDisponible(): bool
    {
        return $this->estado === 'disponible' && $this->usuario->estado === 'activo';
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado(string $nuevoEstado): bool
    {
        if (!in_array($nuevoEstado, ['disponible', 'ocupado', 'fuera'])) {
            return false;
        }

        $this->estado = $nuevoEstado;
        return $this->save();
    }

    /**
     * Actualizar ubicación GPS
     */
    public function actualizarUbicacion(float $lat, float $lng): void
    {
        $this->update([
            'ubicacion_lat' => $lat,
            'ubicacion_lng' => $lng,
        ]);
    }

    /**
     * Obtener ingresos totales
     */
    public function ingresosTotales(): float
    {
        return $this->viajes()
            ->where('estado', 'completado')
            ->where('pagado', true)
            ->sum('precio') ?? 0;
    }

    /**
     * Obtener ingresos del día
     */
    public function ingresosDia(): float
    {
        return $this->viajes()
            ->where('estado', 'completado')
            ->where('pagado', true)
            ->whereDate('created_at', today())
            ->sum('precio') ?? 0;
    }

    /**
     * Obtener calificación promedio
     */
    public function calificacionPromedio(): float
    {
        return $this->viajes()
            ->where('estado', 'completado')
            ->whereNotNull('calificacion_cliente')
            ->avg('calificacion_cliente') ?? 0;
    }

    /**
     * Obtener total de viajes
     */
    public function totalViajes(): int
    {
        return $this->viajes()
            ->where('estado', 'completado')
            ->count();
=======

class Taxista extends Model
{
    protected $fillable = [
        'user_id',
        'licencia',
        'municipio',
        'matricula',
        'modelo_vehiculo',
        'estado',
        'latitud',
        'longitud',
        'valoracion_media',
        'num_valoraciones',
    ];

    protected $casts = [
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'valoracion_media' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viajes()
    {
        return $this->hasMany(Viaje::class);
>>>>>>> origin/master
    }
}
