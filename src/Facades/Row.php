<?php

namespace VariableSign\DataTable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \VariableSign\DataTable\Row
 * @method static \VariableSign\DataTable\Row attributes(null|array|callable $attributes = null)
 * @see \VariableSign\DataTable\Row
 */
class Row extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \VariableSign\DataTable\Row::class;
    }
}