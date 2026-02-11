<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Registrar nuevo usuario
     */
    public function registrar(Request $request): JsonResponse
    {
        try {
            $validado = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'telefono' => 'nullable|string',
                'tipo' => 'required|in:cliente,taxista,admin',
                'ciudad' => 'nullable|string',
                'pais' => 'nullable|string',
                'direccion' => 'nullable|string',
                'licencia_nro' => 'nullable|string',
                'municipio' => 'nullable|string',
                'metodo_pago' => 'nullable|string',
            ]);

            $usuario = $this->authService->registrar($validado);

            return response()->json([
                'success' => true,
                'mensaje' => 'Registro exitoso',
                'usuario' => $usuario,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Iniciar sesión
     */
    public function iniciarSesion(Request $request): JsonResponse
    {
        $validado = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $resultado = $this->authService->iniciarSesion($validado['email'], $validado['password']);

        if (!$resultado) {
            return response()->json([
                'success' => false,
                'error' => 'Email o contraseña inválidos',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Sesión iniciada',
            'usuario' => $resultado,
        ]);
    }

    /**
     * Obtener perfil del usuario
     */
    public function obtenerPerfil(Request $request): JsonResponse
    {
        $usuario = $request->user();

        return response()->json([
            'success' => true,
            'usuario' => $usuario,
            'tipo' => $usuario->tipo,
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request): JsonResponse
    {
        try {
            $validado = $request->validate([
                'password_actual' => 'required|string',
                'password_nueva' => 'required|string|min:6',
            ]);

            $exito = $this->authService->cambiarPassword(
                $request->user()->id,
                $validado['password_actual'],
                $validado['password_nueva']
            );

            if (!$exito) {
                return response()->json([
                    'success' => false,
                    'error' => 'Contraseña actual inválida',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'mensaje' => 'Contraseña cambiada exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
