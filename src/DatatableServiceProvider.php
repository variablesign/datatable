<?php

namespace VariableSign\Datatable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use VariableSign\Datatable\Console\DatatableMakeCommand;

class DatatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'datatable');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'datatable');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/datatable.php' => config_path('datatable.php'),
            ], 'config');

            $this->commands([
                DatatableMakeCommand::class,
            ]);

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/datatable'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/datatable'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/datatable'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/datatable.php', 'datatable');

        // Define blade directive
        Blade::if('hasrecords', function ($value) {
            return $value->isNotEmpty() || ($value->isEmpty() && request('search'));
        });

        // Register the main class to use with the facade
        $this->app->singleton('datatable', function () {
            return new Datatable;
        });
    }
}
