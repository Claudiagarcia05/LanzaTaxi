<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    protected $table = 'tarifas';

    protected $fillable = [
        'nombre',
        'tipo',
        'bajada_bandera',
        'precio_km',
        'precio_espera_hora',
        'minimo_viaje',
        'activa',
        'vigente_desde',
        'vigente_hasta',
        'municipios',
    ];

    protected $casts = [
        'bajada_bandera' => 'float',
        'precio_km' => 'float',
        'precio_espera_hora' => 'float',
        'minimo_viaje' => 'float',
        'activa' => 'boolean',
        'vigente_desde' => 'date',
        'vigente_hasta' => 'date',
        'municipios' => 'array',
    ];

    /**
     * Viajes con esta tarifa
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    /**
     * Tipos válidos de tarifa
     */
    public static function tiposValidos(): array
    {
        return ['urbano', 'interurbano', 'especial'];
    }

    /**
     * Verificar si tarifa está vigente
     */
    public function estaVigente(): bool
    {
        return $this->activa &&
               today()->greaterThanOrEqualTo($this->vigente_desde) &&
               today()->lessThanOrEqualTo($this->vigente_hasta);
    }

    /**
     * Calcular precio de un viaje
     */
    public function calcularPrecio(float $distancia, int $minutos = 0): float
    {
        $precio = $this->bajada_bandera + ($distancia * $this->precio_km);

        // Añadir tiempo de espera si aplica
        if ($minutos > 0) {
            $horas = $minutos / 60;
            $precio += $horas * $this->precio_espera_hora;
        }

        // Aplicar mínimo de viaje
        return max($precio, $this->minimo_viaje);
    }

    /**
     * Obtener tarifas activas
     */
    public static function activas()
    {
        return self::where('activa', true)->get();
    }
=======

class Tarifa extends Model
{
    protected $fillable = [
        'nombre',
        'bajada_bandera',
        'precio_km',
        'suplemento_aeropuerto',
        'suplemento_puerto',
        'suplemento_nocturno',
        'suplemento_festivo',
        'activa',
    ];

    protected $casts = [
        'bajada_bandera' => 'decimal:2',
        'precio_km' => 'decimal:2',
        'suplemento_aeropuerto' => 'decimal:2',
        'suplemento_puerto' => 'decimal:2',
        'suplemento_nocturno' => 'decimal:2',
        'suplemento_festivo' => 'decimal:2',
        'activa' => 'boolean',
    ];
>>>>>>> origin/master
}
