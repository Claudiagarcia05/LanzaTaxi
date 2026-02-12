<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $fillable = [
        'viaje_id',
        'user_id',
        'tipo',
        'descripcion',
        'estado',
    ];

    // Relaciones
    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
