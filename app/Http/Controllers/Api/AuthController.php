<?php

namespace App\Http\Controllers\Api;

<<<<<<< HEAD
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
=======
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Taxista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Registrar nuevo usuario
     */
    public function register(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nombre' => 'required|string',
            'telefono' => 'nullable|string',
            'role' => 'required|in:cliente,taxista,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'details' => $validator->errors()
            ], 400);
        }

        // Crear usuario
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'role' => $request->role,
        ]);

        // Generar token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'nombre' => $user->nombre,
                'role' => $user->role,
            ]
        ], 201);
>>>>>>> origin/master
    }

    /**
     * Iniciar sesión
     */
<<<<<<< HEAD
    public function iniciarSesion(Request $request): JsonResponse
    {
        $validado = $request->validate([
=======
    public function login(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
>>>>>>> origin/master
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

<<<<<<< HEAD
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
=======
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Faltan email o contraseña'
            ], 400);
        }

        // Buscar usuario
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Email o contraseña incorrectos'
            ], 401);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Email o contraseña incorrectos'
            ], 401);
        }

        // Generar token JWT
        $token = JWTAuth::fromUser($user);

        $response = [
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'nombre' => $user->nombre,
                'role' => $user->role,
            ]
        ];

        // Si es taxista, incluir información adicional
        if ($user->role === 'taxista') {
            $taxista = Taxista::where('user_id', $user->id)->first();
            if ($taxista) {
                $response['taxista'] = [
                    'id' => $taxista->id,
                    'licencia' => $taxista->licencia,
                    'matricula' => $taxista->matricula,
                    'estado' => $taxista->estado,
                ];
            }
        }

        return response()->json($response);
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    public function getProfile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'nombre' => $user->nombre,
                    'telefono' => $user->telefono,
                    'role' => $user->role,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }
>>>>>>> origin/master
    }

    /**
     * Cambiar contraseña
     */
<<<<<<< HEAD
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
=======
    public function changePassword(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Validar datos
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validación fallida',
                    'details' => $validator->errors()
                ], 400);
            }

            // Verificar contraseña actual
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'error' => 'La contraseña actual es incorrecta'
                ], 400);
            }

            // Actualizar contraseña
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Contraseña actualizada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }
    }

    /**
     * Cerrar sesión (logout)
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'message' => 'Sesión cerrada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cerrar sesión'], 500);
        }
    }
}

>>>>>>> origin/master
