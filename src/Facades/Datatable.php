<?php

namespace VariableSign\Datatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed render(?string $view = null, array $data = [])
 * 
 * @see \VariableSign\Datatable\Contracts\Datatable
 */
class Datatable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'datatable';
    }
}
