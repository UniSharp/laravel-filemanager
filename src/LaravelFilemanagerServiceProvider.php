<?php namespace Tsawler\Laravelfilemanager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;


class LaravelFilemanagerServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';

        //$this->loadTranslationsFrom(__DIR__.'/lang', 'vcms5');

        $this->loadViewsFrom(__DIR__.'/views', 'laravel-filemanager');

        $this->publishes([
            __DIR__ . '/config/laravel-filemanager.php' => config_path('laravel-filemanager.php', 'config'),
        ], 'lfm_config');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/laravel-filemanager'),
        ], 'lfm_public');
//
//        $this->publishes([
//            __DIR__.'/migrations' => base_path('/database/migrations'),
//        ], 'v_migrations');
//
//        $this->publishes([
//            __DIR__.'/views' => base_path('resources/views/vendor/vcms5'),
//        ], 'v_views');
//
//        $this->publishes([
//            __DIR__.'/seeds' => base_path('database/seeds'),
//        ], 'seeds');
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
