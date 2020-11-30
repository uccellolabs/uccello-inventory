<?php

namespace Uccello\Inventory\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * App Service Provider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        // Views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'inventory');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'inventory');

        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/uccello/inventory'),
        ], 'inventory-assets');

        // Config
        $this->publishes([
            __DIR__ . '/../../config/inventory.php' => config_path('inventory.php'),
        ], 'inventory-config');
    }

    public function register()
    {
        // Config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/inventory.php',
            'inventory'
        );
    }
}
