<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ViajeController;
use App\Http\Controllers\Api\TransaccionController;
use App\Http\Controllers\Api\EvaluacionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí están todas las rutas API para LanzaTaxi
| Prefijo: /api/
|
*/

// Autenticación - Sin protección
Route::prefix('auth')->group(function () {
    Route::post('/registrar', [AuthController::class, 'registrar']);
    Route::post('/iniciar-sesion', [AuthController::class, 'iniciarSesion']);
});

// Rutas protegidas - Requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticación - Requieren sesión
    Route::prefix('auth')->group(function () {
        Route::get('/perfil', [AuthController::class, 'obtenerPerfil']);
        Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);
        Route::post('/logout', function (Request $request) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['success' => true, 'mensaje' => 'Sesión cerrada']);
        });
    });

    // Viajes
    Route::prefix('viajes')->group(function () {
        Route::post('/', [ViajeController::class, 'crear']);
        Route::get('/{id}', [ViajeController::class, 'obtener']);
        Route::put('/{id}/aceptar', [ViajeController::class, 'aceptar']);
        Route::put('/{id}/completar', [ViajeController::class, 'completar']);
        Route::put('/{id}/cancelar', [ViajeController::class, 'cancelar']);
        Route::get('/cliente/{clienteId}', [ViajeController::class, 'viajesCliente']);
        Route::get('/taxista/{taxistaId}/activos', [ViajeController::class, 'viajesToxistaActivos']);
        Route::get('/taxistas-cercanos', [ViajeController::class, 'taxistasCercanos']);
    });

    // Transacciones
    Route::prefix('transacciones')->group(function () {
        Route::post('/pagar', [TransaccionController::class, 'procesarPago']);
        Route::get('/usuario/{usuarioId}', [TransaccionController::class, 'obtenerTransacciones']);
        Route::get('/usuario/{usuarioId}/balance', [TransaccionController::class, 'obtenerBalance']);
        Route::get('/usuario/{usuarioId}/ganancias', [TransaccionController::class, 'obtenerGanancias']);
    });

    // Evaluaciones
    Route::prefix('evaluaciones')->group(function () {
        Route::post('/', [EvaluacionController::class, 'crear']);
        Route::get('/usuario/{usuarioId}', [EvaluacionController::class, 'obtenerEvaluaciones']);
        Route::get('/usuario/{usuarioId}/calificacion', [EvaluacionController::class, 'obtenerCalificacion']);
        Route::get('/{usuarioId}/puede-evaluar/{viajeId}', [EvaluacionController::class, 'puedeEvaluar']);
    });
});

// Health check - Sin protección
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
