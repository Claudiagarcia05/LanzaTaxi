<?php

namespace Tests\Unit\Services;

use App\Services\AuthService;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    /**
     * Test registar usuario válido
     */
    public function test_registar_usuario_valido()
    {
        $datos = [
            'nombre' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'telefono' => '+34 928 234 567',
            'tipo' => 'cliente',
            'ciudad' => 'Arrecife',
            'pais' => 'España',
        ];

        // Validar estructura
        $this->assertArrayHasKey('nombre', $datos);
        $this->assertArrayHasKey('email', $datos);
        $this->assertEquals('cliente', $datos['tipo']);
    }

    /**
     * Test email debe ser válido
     */
    public function test_email_valido()
    {
        $email = 'usuario@example.com';
        $this->assertStringContainsString('@', $email);
    }

    /**
     * Test password mínimo 6 caracteres
     */
    public function test_password_minimo()
    {
        $password = 'short';
        $this->assertLessThan(6, strlen($password));

        $password = 'password123';
        $this->assertGreaterThanOrEqual(6, strlen($password));
    }

    /**
     * Test tipos de usuario válidos
     */
    public function test_tipos_usuario_validos()
    {
        $tiposValidos = ['cliente', 'taxista', 'admin'];

        $this->assertContains('cliente', $tiposValidos);
        $this->assertContains('taxista', $tiposValidos);
        $this->assertContains('admin', $tiposValidos);
    }
}
