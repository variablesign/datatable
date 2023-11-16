<?php

namespace VariableSign\DataTable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \VariableSign\DataTable\Table
 * @method static \VariableSign\DataTable\Table attributes(null|array|callable $attributes = null)
 * @see \VariableSign\DataTable\Table
 */
class Table extends Facade
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
        return \VariableSign\DataTable\Table::class;
    }
}