<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Filters\EnumFilter;
use VariableSign\DataTable\Filters\TextFilter;
use VariableSign\DataTable\Filters\SelectFilter;
use VariableSign\DataTable\Filters\BooleanFilter;

class Filter
{
    public function boolean(): BooleanFilter
    {
        return new BooleanFilter;
    }

    public function enum(): EnumFilter
    {
        return new EnumFilter;
    }

    public function select(): SelectFilter
    {
        return new SelectFilter;
    }

    // public function text(): TextFilter
    // {
    //     return new TextFilter;
    // }
}