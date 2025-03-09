<?php

namespace Rgalstyan\Larapi\Providers;

use Illuminate\Support\ServiceProvider;
use Rgalstyan\Larapi\Services\LaraPiDbService;
use Rgalstyan\Larapi\Services\LaraPiPaymentService;

final class LaraPiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');

        $this->publishes([
            __DIR__ . '/../../config/larapi.php' => config_path('larapi.php'),
        ], 'larapi-config');

        $this->publishes([
            __DIR__ . '/../../migrations/' => database_path('migrations')
        ], 'larapi-migrations');
    }

    public function register(): void
    {
        $this->app->bind('lara_pi_payment', function ($app) {
            return $app->make(LaraPiPaymentService::class);
        });

        $this->app->bind('lara_pi_db', function ($app) {
            return $app->make(LaraPiDbService::class);
        });
    }
}
