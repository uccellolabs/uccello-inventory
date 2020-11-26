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

        $this->initBladeDirectives();

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

    protected function initBladeDirectives()
    {
        Blade::directive('InventoryLines', function ($type) {
            $content = null;
            if ($type === 'edit') {
                $content = "<?php echo view('inventory::lines.edit')->render(); ?>";
            } elseif ($type === 'detail') {
                $content = "<?php echo view('inventory::lines.detail')->render(); ?>";
            }
            return $content;
        });

        Blade::directive('InventoryTotals', function ($type) {
            $content = null;
            if ($type === 'edit') {
                $content = "<?php echo view('inventory::total.edit')->render(); ?>";
            } elseif ($type === 'detail') {
                $content = "<?php echo view('inventory::total.detail')->render(); ?>";
            }
            return $content;
        });
    }
}
