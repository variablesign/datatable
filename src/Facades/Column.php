<?php

namespace VariableSign\DataTable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \VariableSign\DataTable\Column
 * @method static \VariableSign\DataTable\Column name(string $name)
 * @method static \VariableSign\DataTable\Column alias(string $alias)
 * @method static \VariableSign\DataTable\Column title(string $title)
 * @method static \VariableSign\DataTable\Column index()
 * @method static \VariableSign\DataTable\Column edit(callable $edit)
 * @method static \VariableSign\DataTable\Column searchable(bool|callable $searchable = true)
 * @method static \VariableSign\DataTable\Column sortable(bool|callable $sortable = true)
 * @method static \VariableSign\DataTable\Column attributes(null|array|callable $attributes = null)
 * @method static \VariableSign\DataTable\Column responsive(string $breakpoint)
 * @method static \VariableSign\DataTable\Column align(string $alignment)
 * @method static \VariableSign\DataTable\Column checkbox(null|array|callable $attributes = null)
 * @method static \VariableSign\DataTable\Column responsive(string $breakpoint)
 * @see \VariableSign\DataTable\Column
 */
class Column extends Facade
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
        return \VariableSign\DataTable\Column::class;
    }
}