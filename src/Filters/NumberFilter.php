<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class NumberFilter
{
    private string $min = 'Min';

    private string $max = 'Max';

    public array $options = [];

    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function min(string $placeholder): self
    {
        $this->min = $placeholder;
        
        return $this;
    }

    public function max(string $placeholder): self
    {
        $this->max = $placeholder;
        
        return $this;
    }

    public function getFilter(string $column, mixed $value, Builder|QueryBuilder $query): Builder|QueryBuilder
    {
        if (array_key_exists('min', $value) && array_key_exists('max', $value)) {
            return $query->where($column, '>=', $value['min'])
                ->where($column, '<=', $value['max']);
        }
        
        if (array_key_exists('min', $value)) {
            return $query->where($column, '>=', $value['min']);
        }

        if (array_key_exists('max', $value)) {
            return $query->where($column, '<=', $value['max']);
        }

        return $query;
    }

    public function getDataSource(): ?array
    {
        return [
            'min' => $this->min,
            'max' => $this->max
        ];
    }

    public function getElement(): array
    {
        return [
            'type' => 'number',
            'range' => true
        ];
    }
}