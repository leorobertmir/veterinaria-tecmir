<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. CARGA DE MIGRACIONES DDD (La línea nueva)
        // Esto le dice a Laravel: "Busca tablas también en la carpeta de Inventario"
        $this->loadMigrationsFrom(base_path('src/Inventario/Infrastructure/Migrations'));

        // 2. CONFIGURACIÓN SANCTUM (Lo que ya tenías)
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // 3. FIX RAILWAY HTTPS (Lo que ya tenías - VITAL)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}