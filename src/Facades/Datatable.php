<?php

namespace Veriablesign\Datatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Veriablesign\Datatable\Contracts\Datatable
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
