<?php

use VariableSign\DataTable\DataTable;

if (!function_exists('datatable')) {
    function datatable(string $table): DataTable
    {
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

        return new $class();
    }
}