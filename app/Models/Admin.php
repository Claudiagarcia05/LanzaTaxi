<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $table = 'admins';

    protected $fillable = [
        'usuario_id',
        'permisos',
        'activo',
    ];

    protected $casts = [
        'permisos' => 'array',
        'activo' => 'boolean',
    ];

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Permisos disponibles
     */
    public static function permisosDisponibles(): array
    {
        return [
            'users_manage',
            'taxistas_manage',
            'viajes_view',
            'tarifas_edit',
            'comisiones_view',
            'reportes_view',
            'sistema_config',
        ];
    }

    /**
     * Verificar si tiene permiso
     */
    public function tienePermiso(string $permiso): bool
    {
        return in_array($permiso, $this->permisos ?? []);
    }

    /**
     * Dar permiso
     */
    public function darPermiso(string $permiso): bool
    {
        if (!in_array($permiso, self::permisosDisponibles())) {
            return false;
        }

        $permisos = $this->permisos ?? [];
        if (!in_array($permiso, $permisos)) {
            $permisos[] = $permiso;
            $this->permisos = $permisos;
            $this->save();
        }

        return true;
    }

    /**
     * Revocar permiso
     */
    public function revocarPermiso(string $permiso): bool
    {
        $permisos = $this->permisos ?? [];
        $permisos = array_filter($permisos, fn($p) => $p !== $permiso);
        $this->permisos = array_values($permisos);

        return $this->save();
    }
}
