<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    }
}
