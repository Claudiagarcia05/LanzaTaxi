<?php

namespace App\Providers;

use App\Services\ViajeService;
use App\Services\AuthService;
use App\Services\TransaccionService;
use App\Services\EvaluacionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar servicios en el contenedor de inyección de dependencias
        $this->app->singleton(ViajeService::class, function ($app) {
            return new ViajeService();
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });

        $this->app->singleton(TransaccionService::class, function ($app) {
            return new TransaccionService();
        });

        $this->app->singleton(EvaluacionService::class, function ($app) {
            return new EvaluacionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
