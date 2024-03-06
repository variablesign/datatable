<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Filters\DateFilter;
use VariableSign\DataTable\Filters\EnumFilter;
use VariableSign\DataTable\Filters\TextFilter;
use VariableSign\DataTable\Filters\NumberFilter;
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

    public function date(): DateFilter
    {
        return new DateFilter;
    }

    public function text(): TextFilter
    {
        return new TextFilter;
    }

    public function number(): NumberFilter
    {
        return new NumberFilter;
    }
}