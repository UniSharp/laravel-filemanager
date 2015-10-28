<?php namespace Unisharp\Laravelfilemanager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;


/**
 * Class LaravelFilemanagerServiceProvider
 * @package Unisharp\Laravelfilemanager
 */
class LaravelFilemanagerServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Config::get('lfm.use_package_routes'))
            include __DIR__ . '/routes.php';

        $this->loadTranslationsFrom(__DIR__.'/lang', 'laravel-filemanager');

        $this->loadViewsFrom(__DIR__.'/views', 'laravel-filemanager');

        $this->publishes([
            __DIR__ . '/config/lfm.php' => config_path('lfm.php', 'config'),
        ], 'lfm_config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['laravel-filemanager'] = $this->app->share(function ()
        {
            return true;
        });
    }

}
