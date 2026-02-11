<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'tipo',
        'avatar_url',
        'estado',
        'verificado',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'verificado' => 'boolean',
    ];

    /**
     * Relación polimórfica: un usuario puede ser cliente
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    /**
     * Relación polimórfica: un usuario puede ser taxista
     */
    public function taxista(): HasOne
    {
        return $this->hasOne(Taxista::class);
    }

    /**
     * Viajes como cliente
     */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class, 'cliente_id');
    }

    /**
     * Viajes como taxista
     */
    public function viajesToxista(): HasMany
    {
        return $this->hasMany(Viaje::class, 'taxista_id');
    }

    /**
     * Transacciones del usuario
     */
    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }

    /**
     * Evaluaciones dadas por el usuario
     */
    public function evaluacionesDadas(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'evaluador_id');
    }

    /**
     * Evaluaciones recibidas por el usuario
     */
    public function evaluacionesRecibidas(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'evaluado_id');
    }

    /**
     * Notificaciones del usuario
     */
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    /**
     * Validar si es de tipo cliente
     */
    public function esCliente(): bool
    {
        return $this->tipo === 'cliente';
    }

    /**
     * Validar si es de tipo taxista
     */
    public function esTaxista(): bool
    {
        return $this->tipo === 'taxista';
    }

    /**
     * Validar si es de tipo admin
     */
    public function esAdmin(): bool
    {
        return $this->tipo === 'admin';
    }

    /**
     * Hash de contraseña
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
