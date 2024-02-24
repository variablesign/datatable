<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Filters\BooleanFilter;

class Filter
{
    public function boolean(): BooleanFilter
    {
        return new BooleanFilter;
    }
}