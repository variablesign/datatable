<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('datatable.route.prefix'))
    ->middleware(config('datatable.route.middleware'))
    ->group(function () {
        Route::get(config('datatable.route.uri'), function (string $table) {
            $datatable = datatable($table);

            return response()->json($datatable->api());
        })
        ->name(config('datatable.route.name'));
    });