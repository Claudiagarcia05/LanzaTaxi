<?php

namespace Tests\Unit\Models;

use App\Models\Taxista;
use PHPUnit\Framework\TestCase;

class TaxistaTest extends TestCase
{
    /**
     * Test crear taxista
     */
    public function test_crear_taxista()
    {
        $taxista = new Taxista([
            'usuario_id' => 1,
            'licencia_nro' => 'ABC123456',
            'estado' => 'disponible',
            'municipio' => 'Arrecife',
            'calificacion' => 4.8,
        ]);

        $this->assertEquals(1, $taxista->usuario_id);
        $this->assertEquals('ABC123456', $taxista->licencia_nro);
        $this->assertEquals('disponible', $taxista->estado);
        $this->assertEquals(4.8, $taxista->calificacion);
    }

    /**
     * Test Estado disponible
     */
    public function test_esta_disponible()
    {
        $taxista = new Taxista(['estado' => 'disponible']);

        $this->assertTrue($taxista->estaDisponible());
    }

    /**
     * Test Estado no disponible
     */
    public function test_esta_no_disponible()
    {
        $taxista = new Taxista(['estado' => 'ocupado']);

        $this->assertFalse($taxista->estaDisponible());
    }

    /**
     * Test cambiar estado
     */
    public function test_cambiar_estado()
    {
        $taxista = new Taxista(['estado' => 'fuera']);

        $taxista->cambiarEstado('disponible');

        $this->assertEquals('disponible', $taxista->estado);
    }

    /**
     * Test actualizar ubicación
     */
    public function test_actualizar_ubicacion()
    {
        $taxista = new Taxista();

        $taxista->actualizarUbicacion(29.0469, -13.5901);

        $this->assertEquals(29.0469, $taxista->ubicacion_lat);
        $this->assertEquals(-13.5901, $taxista->ubicacion_lng);
    }
}
