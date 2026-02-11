<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Taxista;
use App\Models\Admin;

class AuthService
{
    /**
     * Registrar nuevo usuario
     */
    public function registrar(array $datos): Usuario
    {
        // Validar email único
        if (Usuario::where('email', $datos['email'])->exists()) {
            throw new \Exception('Email ya registrado');
        }

        $usuario = Usuario::create([
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'telefono' => $datos['telefono'] ?? null,
            'tipo' => $datos['tipo'],
            'estado' => 'activo',
            'verificado' => false,
        ]);

        // Crear perfil específico según tipo
        switch ($datos['tipo']) {
            case 'cliente':
                Cliente::create([
                    'usuario_id' => $usuario->id,
                    'direccion' => $datos['direccion'] ?? null,
                    'ciudad' => $datos['ciudad'] ?? null,
                    'pais' => $datos['pais'] ?? null,
                    'metodo_pago' => $datos['metodo_pago'] ?? 'tarjeta',
                ]);
                break;

            case 'taxista':
                Taxista::create([
                    'usuario_id' => $usuario->id,
                    'licencia_nro' => $datos['licencia_nro'],
                    'municipio' => $datos['municipio'] ?? 'Arrecife',
                    'estado' => 'fuera',
                    'calificacion' => 5.0,
                ]);
                break;

            case 'admin':
                Admin::create([
                    'usuario_id' => $usuario->id,
                    'permisos' => $datos['permisos'] ?? [],
                    'activo' => true,
                ]);
                break;
        }

        return $usuario;
    }

    /**
     * Iniciar sesión
     */
    public function iniciarSesion(string $email, string $password): ?array
    {
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario || !password_verify($password, $usuario->password)) {
            return null;
        }

        // Aquí iría generar JWT token
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'tipo' => $usuario->tipo,
            // 'token' => $this->generarToken($usuario),
        ];
    }

    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail(string $email): ?Usuario
    {
        return Usuario::where('email', $email)->first();
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNueva): bool
    {
        $usuario = Usuario::find($usuarioId);

        if (!$usuario || !password_verify($passwordActual, $usuario->password)) {
            return false;
        }

        $usuario->update(['password' => $passwordNueva]);
        return true;
    }

    /**
     * Desactivar usuario
     */
    public function desactivarUsuario(int $usuarioId): bool
    {
        $usuario = Usuario::find($usuarioId);

        if (!$usuario) {
            return false;
        }

        return $usuario->update(['estado' => 'inactivo']);
    }
}
