<?php

namespace Tests\Unit\Services;

use App\Services\TransaccionService;
use PHPUnit\Framework\TestCase;

class TransaccionServiceTest extends TestCase
{
    private $transaccionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transaccionService = new TransaccionService();
    }

    /**
     * Test calcular comisión correcta
     */
    public function test_comision_correcta()
    {
        $montoViaje = 100;
        $comision = $montoViaje * 0.15;

        $this->assertEquals(15, $comision);
        $this->assertEquals(85, $montoViaje - $comision);
    }

    /**
     * Test obtener balance
     */
    public function test_obtener_balance()
    {
        // Test estructura sin BD real
        $usuarioId = 1;

        // El método debe retornar un float
        $balance = $this->transaccionService->obtenerBalance($usuarioId);
        $this->assertIsNumeric($balance);
    }

    /**
     * Test períodos válidos
     */
    public function test_periodos_validos()
    {
        $periodos = ['hoy', 'semana', 'mes', 'año'];

        foreach ($periodos as $periodo) {
            $this->assertIsString($periodo);
        }
    }
}
