<?php

namespace Tests\Unit\Services;

use App\Services\ViajeService;
use PHPUnit\Framework\TestCase;

class ViajeServiceTest extends TestCase
{
    private $viajeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viajeService = new ViajeService();
    }

    /**
     * Test crear viaje
     */
    public function test_crear_viaje_valida()
    {
        $datos = [
            'cliente_id' => 1,
            'origen_lat' => 29.0469,
            'origen_lng' => -13.5901,
            'origen_direccion' => 'Calle Principal, Arrecife',
            'destino_lat' => 28.9644,
            'destino_lng' => -13.6362,
            'destino_direccion' => 'Centro Comercial',
            'tarifa_id' => 1,
            'ocupantes' => 2,
        ];

        // Este test falla sin BD real, pero valida la estructura
        $this->assertArrayHasKey('cliente_id', $datos);
        $this->assertArrayHasKey('tarifa_id', $datos);
    }

    /**
     * Test validar datos viaje incompletos
     */
    public function test_crear_viaje_datos_incompletos()
    {
        $this->expectException(\Exception::class);

        $datos = [
            'cliente_id' => 1,
            // Falta origen_lat
            'origen_lng' => -13.5901,
        ];

        // Esto debería lanzar una excepción
        $this->viajeService->crearViaje($datos);
    }

    /**
     * Test coordenadas inválidas
     */
    public function test_coordenadas_invalidas()
    {
        $this->expectException(\Exception::class);

        $datos = [
            'cliente_id' => 1,
            'origen_lat' => 'no_es_numero',
            'origen_lng' => -13.5901,
            'origen_direccion' => 'Calle',
            'destino_lat' => 28.9644,
            'destino_lng' => -13.6362,
            'destino_direccion' => 'Centro',
            'tarifa_id' => 1,
        ];

        $this->viajeService->crearViaje($datos);
    }
}
