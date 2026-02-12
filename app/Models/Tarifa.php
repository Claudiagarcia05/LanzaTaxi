<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
