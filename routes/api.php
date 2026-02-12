<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ViajeController;
use App\Http\Controllers\Api\TaxistaController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\TarifaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas de autenticación (públicas)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rutas protegidas
    Route::middleware('auth:api')->group(function () {
        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Rutas de viajes (protegidas)
Route::middleware('auth:api')->prefix('viajes')->group(function () {
    Route::get('/mis-viajes', [ViajeController::class, 'misViajes']);
    Route::get('/pendientes', [ViajeController::class, 'pendientes']);
    Route::post('/', [ViajeController::class, 'crear']);
    Route::get('/{id}', [ViajeController::class, 'obtener']);
    Route::put('/{id}/aceptar', [ViajeController::class, 'aceptar']);
    Route::put('/{id}/iniciar', [ViajeController::class, 'iniciar']);
    Route::put('/{id}/finalizar', [ViajeController::class, 'finalizar']);
    Route::put('/{id}/cancelar', [ViajeController::class, 'cancelar']);
});

// Rutas de taxistas
Route::middleware('auth:api')->prefix('taxistas')->group(function () {
    Route::get('/mis-viajes', [TaxistaController::class, 'misViajes']);
    Route::get('/', [TaxistaController::class, 'listar']);
    Route::get('/{id}', [TaxistaController::class, 'obtener']);
    Route::put('/{id}/estado', [TaxistaController::class, 'cambiarEstado']);
    Route::put('/{id}/ubicacion', [TaxistaController::class, 'actualizarUbicacion']);
    Route::get('/cercanos', [TaxistaController::class, 'cercanos']);
});

// Rutas de administración
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/usuarios', [AdminController::class, 'usuarios']);
    Route::delete('/usuarios/{id}', [AdminController::class, 'eliminarUsuario']);
    Route::get('/viajes', [AdminController::class, 'viajes']);
    Route::post('/viajes/{id}/cancelar', [AdminController::class, 'cancelarViaje']);
    Route::get('/taxistas', [AdminController::class, 'taxistas']);
    Route::post('/taxistas', [AdminController::class, 'crearTaxista']);
    Route::get('/taxistas/ubicaciones', [AdminController::class, 'ubicacionesTaxistas']);
});

// Tarifas
Route::middleware('auth:api')->get('/tarifas', [TarifaController::class, 'index']);
