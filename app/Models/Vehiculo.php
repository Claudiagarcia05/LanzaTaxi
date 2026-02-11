<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'taxista_id',
        'marca',
        'modelo',
        'matricula',
        'color',
        'seats',
        'licencia',
        'seguro_until',
        'itv_until',
    ];

    protected $casts = [
        'seguro_until' => 'date',
        'itv_until' => 'date',
    ];

    /**
     * Relación con Taxista
     */
    public function taxista(): BelongsTo
    {
        return $this->belongsTo(Taxista::class);
    }

    /**
     * Viajes realizados con este vehículo
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    /**
     * Verificar si la matrícula es válida
     */
    public static function matriculaValida(string $matricula): bool
    {
        return preg_match('/^\d{4}[A-Z]{3}$/', $matricula) === 1;
    }

    /**
     * Verificar si seguro está vigente
     */
    public function seguroVigente(): bool
    {
        return $this->seguro_until->isFuture();
    }

    /**
     * Verificar si ITV está vigente
     */
    public function itvVigente(): bool
    {
        return $this->itv_until->isFuture();
    }

    /**
     * Verificar si documentación está completa
     */
    public function documentacionCompleta(): bool
    {
        return $this->seguroVigente() && $this->itvVigente();
    }
}
