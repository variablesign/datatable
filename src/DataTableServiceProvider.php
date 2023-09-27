<?php

namespace VariableSign\DataTable;

use Illuminate\Support\ServiceProvider;
use VariableSign\DataTable\Console\DataTableMakeCommand;

class DataTableServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'datatable');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/datatable.php' => config_path('datatable.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/datatable'),
            ], 'views');

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/datatable'),
            ], 'assets');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/datatable'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                DataTableMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // $this->app->bind('datatable.config', function () {
        //     return config('datatable');
        // });

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/datatable.php', 'datatable');
    }
}
