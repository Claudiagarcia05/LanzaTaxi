<?php

namespace Tests\Unit\Models;

use App\Models\Vehiculo;
use PHPUnit\Framework\TestCase;

class VehiculoTest extends TestCase
{
    /**
     * Test validar matrícula
     */
    public function test_validar_matricula_valida()
    {
        $this->assertTrue(Vehiculo::matriculaValida('1234ABC'));
    }

    /**
     * Test validar matrícula inválida - formato incorrecto
     */
    public function test_validar_matricula_invalida_formato()
    {
        $this->assertFalse(Vehiculo::matriculaValida('ABCD1234'));
        $this->assertFalse(Vehiculo::matriculaValida('123ABC'));
        $this->assertFalse(Vehiculo::matriculaValida('12345abc'));
    }

    /**
     * Test crear vehículo
     */
    public function test_crear_vehiculo()
    {
        $vehiculo = new Vehiculo([
            'taxista_id' => 1,
            'marca' => 'Toyota',
            'modelo' => 'Prius',
            'matricula' => '1234ABC',
            'color' => 'Blanco',
            'seats' => 4,
        ]);

        $this->assertEquals('Toyota', $vehiculo->marca);
        $this->assertEquals('Prius', $vehiculo->modelo);
        $this->assertEquals('1234ABC', $vehiculo->matricula);
    }

    /**
     * Test documentación completa
     */
    public function test_documentacion_completa()
    {
        $vehiculo = new Vehiculo([
            'licencia' => 'LIC123456',
            'seguro_until' => now()->addMonths(6),
            'itv_until' => now()->addMonths(12),
        ]);

        $this->assertTrue($vehiculo->documentacionCompleta());
    }

    /**
     * Test documentación incompleta
     */
    public function test_documentacion_incompleta()
    {
        $vehiculo = new Vehiculo([
            'licencia' => null,
            'seguro_until' => now()->addMonths(6),
            'itv_until' => now()->addMonths(12),
        ]);

        $this->assertFalse($vehiculo->documentacionCompleta());
    }

    /**
     * Test seguro vencido
     */
    public function test_seguro_vencido()
    {
        $vehiculo = new Vehiculo([
            'seguro_until' => now()->subMonths(1),
        ]);

        $this->assertFalse($vehiculo->seguroVigente());
    }
}
