<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('datatable.route.prefix'))
    ->middleware(config('datatable.route.middleware'))
    ->group(function () {
        Route::get(config('datatable.route.uri'), function (string $table) {
            $formattedName = 'datatable.' . str($table)->replace('.', '_')->toString();
            $datatable = datatable($table, session($formattedName . '.data', []));

            return response()->json($datatable->api());
        })
        ->name(config('datatable.route.name'));
    });