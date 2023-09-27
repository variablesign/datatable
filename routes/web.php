<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('datatable.route.prefix'))
    ->middleware(config('datatable.route.middleware'))
    ->group(function () {
        Route::get(config('datatable.route.uri'), function (string $table) {
            $parts = explode('.', $table);

            if (count($parts) > 1) {
                $name = array_pop($parts);
                $parts = array_map(function (string $item) {
                    return str($item)->studly();
                }, $parts);
                $path = implode('\\', $parts);
                $class = '\\App\\' . config('datatable.directory') . '\\' . $path . '\\' . str($name)->studly();
            } else {
                $class = '\\App\\' . config('datatable.directory') . '\\' . str($table)->studly();
            }

            $class = new $class();

            return response()->json($class->api());
        })
        ->name(config('datatable.route.name'));
    });