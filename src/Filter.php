<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Filters\TextFilter;
use VariableSign\DataTable\Filters\BooleanFilter;

class Filter
{
    public function boolean(): BooleanFilter
    {
        return new BooleanFilter;
    }

    // public function text(): TextFilter
    // {
    //     return new TextFilter;
    // }
}