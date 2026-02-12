<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $fillable = [
        'cliente_id',
        'taxista_id',
        'origen_lat',
        'origen_lng',
        'origen_direccion',
        'destino_lat',
        'destino_lng',
        'destino_direccion',
        'distancia',
        'precio_estimado',
        'precio_final',
        'estado',
        'tipo_tarifa',
        'suplementos',
        'fecha_solicitud',
        'fecha_aceptacion',
        'fecha_inicio',
        'fecha_fin',
        'valoracion',
        'comentario',
    ];

    protected $casts = [
        'origen_lat' => 'decimal:7',
        'origen_lng' => 'decimal:7',
        'destino_lat' => 'decimal:7',
        'destino_lng' => 'decimal:7',
        'distancia' => 'decimal:2',
        'precio_estimado' => 'decimal:2',
        'precio_final' => 'decimal:2',
        'fecha_solicitud' => 'datetime',
        'fecha_aceptacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function taxista()
    {
        return $this->belongsTo(Taxista::class);
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
