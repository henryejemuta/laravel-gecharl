<?php

namespace HenryEjemuta\LaravelGecharl;

use HenryEjemuta\LaravelGecharl\Console\InstallLaravelGecharl;
use Illuminate\Support\ServiceProvider;

class GecharlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-gecharl');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-gecharl');
//         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
//         $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('gecharl.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-gecharl'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-gecharl'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-gecharl'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                InstallLaravelGecharl::class,
            ]);

        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'gecharl');

        // Register the main class to use with the facade
        $this->app->singleton('gecharl', function ($app) {
            $baseUrl = config('gecharl.base_url');
            $instanceName = 'gecharl';

            return new Gecharl(
                $baseUrl,
                $instanceName,
                config('gecharl')
            );
        });

    }
}
