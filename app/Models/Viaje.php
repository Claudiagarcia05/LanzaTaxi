<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Viaje extends Model
{
    protected $table = 'viajes';

    protected $fillable = [
        'cliente_id',
        'taxista_id',
        'origen_lat',
        'origen_lng',
        'origen_direccion',
        'destino_lat',
        'destino_lng',
        'destino_direccion',
        'distancia_km',
        'tiempo_estimado',
        'precio',
        'tarifa_id',
        'estado',
        'ocupantes',
        'comentario_cliente',
        'calificacion_cliente',
        'calificacion_taxista',
        'metodo_pago',
        'pagado',
    ];

    protected $casts = [
        'origen_lat' => 'float',
        'origen_lng' => 'float',
        'destino_lat' => 'float',
        'destino_lng' => 'float',
        'distancia_km' => 'float',
        'precio' => 'float',
        'calificacion_cliente' => 'integer',
        'calificacion_taxista' => 'integer',
        'pagado' => 'boolean',
    ];

    /**
     * Relación con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    /**
     * Relación con Taxista
     */
    public function taxista(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'taxista_id');
    }

    /**
     * Relación con Tarifa
     */
    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class);
    }

    /**
     * Evaluaciones
     */
    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    /**
     * Transacciones
     */
    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }

    /**
     * Notificaciones
     */
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    /**
     * Estados válidos
     */
    public static function estadosValidos(): array
    {
        return ['solicitado', 'aceptado', 'en_curso', 'completado', 'cancelado'];
    }

    /**
     * Cambiar estado del viaje
     */
    public function cambiarEstado(string $nuevoEstado): bool
    {
        if (!in_array($nuevoEstado, self::estadosValidos())) {
            return false;
        }

        $this->estado = $nuevoEstado;
        return $this->save();
    }

    /**
     * Aceptar viaje (taxista)
     */
    public function aceptar(int $taxistaId): bool
    {
        if ($this->estado !== 'solicitado') {
            return false;
        }

        $this->update([
            'taxista_id' => $taxistaId,
            'estado' => 'aceptado',
        ]);

        return true;
    }

    /**
     * Completar viaje
     */
    public function completar(): bool
    {
        if (!in_array($this->estado, ['aceptado', 'en_curso'])) {
            return false;
        }

        $this->cambiarEstado('completado');
        return true;
    }

    /**
     * Cancelar viaje
     */
    public function cancelar(): bool
    {
        if (in_array($this->estado, ['completado', 'cancelado'])) {
            return false;
        }

        return $this->cambiarEstado('cancelado');
    }

    /**
     * Calcular distancia entre dos puntos (Haversine)
     */
    public static function calcularDistancia(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Obtener viajes activos de un taxista
     */
    public static function activosPorTaxista(int $taxistaId)
    {
        return self::where('taxista_id', $taxistaId)
            ->whereIn('estado', ['aceptado', 'en_curso'])
            ->get();
    }
}
