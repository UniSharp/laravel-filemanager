<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageManagerInterface;
use UniSharp\LaravelFilemanager\Services\ImageService;

/**
 * Class LaravelFilemanagerServiceProvider.
 */
class LaravelFilemanagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'laravel-filemanager');

        $this->loadViewsFrom(__DIR__ . '/views', 'laravel-filemanager');

        $this->publishes([
            __DIR__ . '/config/lfm.php' => base_path('config/lfm.php'),
        ], 'lfm_config');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/laravel-filemanager'),
        ], 'lfm_public');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/laravel-filemanager'),
        ], 'lfm_view');

        $this->publishes([
            __DIR__ . '/Handlers/LfmConfigHandler.php' => base_path('app/Handlers/LfmConfigHandler.php'),
        ], 'lfm_handler');

        if (config('lfm.use_package_routes')) {
            Route::group(['prefix' => config('lfm.url_prefix') ?: 'filemanager', 'middleware' => config('lfm.middlewares') ?: ['web', 'auth']], function () {
                \UniSharp\LaravelFilemanager\Lfm::routes();
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/lfm.php', 'lfm');

        $this->app->singleton('laravel-filemanager', function () {
            return true;
        });

        $this->app->singleton(ImageManagerInterface::class, function ($app) {
            $driver = config('lfm.intervention_driver');

            $driverInstance = match ($driver) {
                'gd' => new GdDriver(),
                'imagick' => new ImagickDriver(),
                default => null,
            };

            if (is_null($driverInstance)) {
                \Log::error("Unsupported image driver [$driver]. GdDriver will be used.");
                $driverInstance = new GdDriver();
            }

            return new ImageManager($driverInstance);
        });

        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService($app->make(ImageManagerInterface::class));
        });
    }
}
