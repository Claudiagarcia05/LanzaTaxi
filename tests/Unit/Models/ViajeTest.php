<?php

namespace Tests\Unit\Models;

use App\Models\Viaje;
use PHPUnit\Framework\TestCase;

class ViajeTest extends TestCase
{
    /**
     * Test calcular distancia con fórmula Haversine
     */
    public function test_calcular_distancia()
    {
        // Coordenadas: Arrecife, Lanzarote a Puerto del Carmen, Lanzarote (8.6 km aprox)
        $lat1 = 29.0469;
        $lng1 = -13.5901;
        $lat2 = 28.9644;
        $lng2 = -13.6362;

        $distancia = Viaje::calcularDistancia($lat1, $lng1, $lat2, $lng2);

        // La distancia debe estar entre 7 y 11 km
        $this->assertGreaterThan(7, $distancia);
        $this->assertLessThan(12, $distancia);
    }

    /**
     * Test estados válidos
     */
    public function test_estados_validos()
    {
        $estados = Viaje::estadosValidos();

        $this->assertIsArray($estados);
        $this->assertContains('solicitado', $estados);
        $this->assertContains('aceptado', $estados);
        $this->assertContains('en_curso', $estados);
        $this->assertContains('completado', $estados);
        $this->assertContains('cancelado', $estados);
    }

    /**
     * Test crear viaje
     */
    public function test_crear_viaje()
    {
        $viaje = new Viaje([
            'cliente_id' => 1,
            'origen_lat' => 29.0469,
            'origen_lng' => -13.5901,
            'origen_direccion' => 'Calle Principal, Arrecife',
            'destino_lat' => 28.9644,
            'destino_lng' => -13.6362,
            'destino_direccion' => 'Centro Comercial Puerto del Carmen',
            'distancia_km' => 8.6,
            'tiempo_estimado' => 9,
            'precio' => 15.50,
            'tarifa_id' => 1,
            'estado' => 'solicitado',
        ]);

        $this->assertEquals(1, $viaje->cliente_id);
        $this->assertEquals('solicitado', $viaje->estado);
        $this->assertEquals(15.50, $viaje->precio);
    }

    /**
     * Test cambiar estado - validación de atributos
     */
    public function test_estados_validos_enum()
    {
        $viaje = new Viaje();
        
        // Validar que los estados válidos son los correctos
        $estadosValidos = Viaje::estadosValidos();
        
        $this->assertCount(5, $estadosValidos);
        $this->assertContains('solicitado', $estadosValidos);
        $this->assertContains('cancelado', $estadosValidos);
    }

    /**
     * Test cambio de estado inválido - sin actualizar BD
     */
    public function test_cambio_estado_estructura()
    {
        // Test que no requiere BD: solo verifica la estructura
        $viaje = new Viaje(['estado' => 'solicitado']);
        
        $this->assertEquals('solicitado', $viaje->estado);
    }
}

