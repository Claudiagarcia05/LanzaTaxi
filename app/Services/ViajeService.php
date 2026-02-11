<?php

namespace App\Services;

use App\Models\Viaje;
use App\Models\Tarifa;
use App\Models\Notificacion;
use DateTime;

class ViajeService
{
    /**
     * Crear nuevo viaje
     */
    public function crearViaje(array $datos): Viaje
    {
        // Validar datos
        $this->validarDatos($datos);

        // Calcular distancia
        $distancia = Viaje::calcularDistancia(
            $datos['origen_lat'],
            $datos['origen_lng'],
            $datos['destino_lat'],
            $datos['destino_lng']
        );

        // Estimar tiempo (aproximadamente 1 km por minuto)
        $tiempoEstimado = ceil($distancia);

        // Obtener tarifa y calcular precio
        $tarifa = Tarifa::find($datos['tarifa_id']);
        if (!$tarifa) {
            throw new \Exception('Tarifa no válida');
        }

        $precio = $tarifa->calcularPrecio($distancia, $tiempoEstimado);

        // Crear viaje
        return Viaje::create([
            'cliente_id' => $datos['cliente_id'],
            'origen_lat' => $datos['origen_lat'],
            'origen_lng' => $datos['origen_lng'],
            'origen_direccion' => $datos['origen_direccion'],
            'destino_lat' => $datos['destino_lat'],
            'destino_lng' => $datos['destino_lng'],
            'destino_direccion' => $datos['destino_direccion'],
            'distancia_km' => $distancia,
            'tiempo_estimado' => $tiempoEstimado,
            'precio' => $precio,
            'tarifa_id' => $datos['tarifa_id'],
            'estado' => 'solicitado',
            'ocupantes' => $datos['ocupantes'] ?? 1,
            'metodo_pago' => $datos['metodo_pago'] ?? 'tarjeta',
        ]);
    }

    /**
     * Aceptar viaje
     */
    public function aceptarViaje(int $viajeId, int $taxistaId): Viaje
    {
        $viaje = Viaje::findOrFail($viajeId);

        if (!$viaje->aceptar($taxistaId)) {
            throw new \Exception('No se puede aceptar este viaje');
        }

        // Crear notificación para el cliente
        $this->crearNotificacion(
            $viaje->cliente_id,
            $viajeId,
            'viaje_aceptado',
            'Tu taxi está en camino',
            'Tu viaje ha sido aceptado. Llega en ' . $viaje->tiempo_estimado . ' minutos'
        );

        return $viaje;
    }

    /**
     * Completar viaje
     */
    public function completarViaje(int $viajeId): Viaje
    {
        $viaje = Viaje::findOrFail($viajeId);

        if (!$viaje->completar()) {
            throw new \Exception('No se puede completar este viaje');
        }

        return $viaje;
    }

    /**
     * Cancelar viaje
     */
    public function cancelarViaje(int $viajeId, string $razon = null): Viaje
    {
        $viaje = Viaje::findOrFail($viajeId);

        if (!$viaje->cancelar()) {
            throw new \Exception('No se puede cancelar este viaje');
        }

        // Crear notificación
        $this->crearNotificacion(
            $viaje->cliente_id,
            $viajeId,
            'viaje_cancelado',
            'Viaje cancelado',
            $razon ?? 'Tu viaje ha sido cancelado'
        );

        return $viaje;
    }

    /**
     * Obtener taxistas disponibles cercanos
     */
    public function obtenerTaxistasCercanos(float $lat, float $lng, float $radio = 5): array
    {
        $taxistas = [];

        // Aquí iría la lógica con base de datos
        // Por ahora retornamos un array vacío
        return $taxistas;
    }

    /**
     * Validar datos del viaje
     */
    private function validarDatos(array $datos): void
    {
        $required = ['cliente_id', 'origen_lat', 'origen_lng', 'origin_direccion', 
                     'destino_lat', 'destino_lng', 'destino_direccion', 'tarifa_id'];

        foreach ($required as $field) {
            if (!isset($datos[$field])) {
                throw new \Exception("Campo requerido: $field");
            }
        }

        if (!is_numeric($datos['origen_lat']) || !is_numeric($datos['origen_lng'])) {
            throw new \Exception('Coordenadas de origen inválidas');
        }

        if (!is_numeric($datos['destino_lat']) || !is_numeric($datos['destino_lng'])) {
            throw new \Exception('Coordenadas de destino inválidas');
        }
    }

    /**
     * Crear notificación
     */
    private function crearNotificacion(int $usuarioId, int $viajeId, string $tipo, 
                                       string $titulo, string $mensaje): void
    {
        Notificacion::create([
            'usuario_id' => $usuarioId,
            'viaje_id' => $viajeId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'leida' => false,
        ]);
    }

    /**
     * Obtener viajes del cliente
     */
    public function obtenerViajesCliente(int $clienteId, string $estado = null)
    {
        $query = Viaje::where('cliente_id', $clienteId);

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtener viajes activos del taxista
     */
    public function obtenerViajesActivosTaxista(int $taxistaId)
    {
        return Viaje::where('taxista_id', $taxistaId)
            ->whereIn('estado', ['aceptado', 'en_curso'])
            ->get();
    }
}
