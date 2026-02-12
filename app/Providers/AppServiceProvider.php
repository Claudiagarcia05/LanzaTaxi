<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Services\ViajeService;
use App\Services\AuthService;
use App\Services\TransaccionService;
use App\Services\EvaluacionService;
=======
>>>>>>> origin/master
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
<<<<<<< HEAD
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
=======
        //
>>>>>>> origin/master
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
