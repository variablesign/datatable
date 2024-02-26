<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TextFilter
{
    private ?array $operators = null;

    public function operators(array $operators): self
    {
        $this->operators = $operators;
        
        return $this;
    }

    public function getFilter(string $column, ?string $key, Builder|QueryBuilder $query): Builder|QueryBuilder|null
    {
        return $query->where($column, 0);
    }

    public function getDataSource(): ?array
    {
        return null;
    }

    public function getElement(): array
    {
        return [
            'type' => 'text'
        ];
    }
}