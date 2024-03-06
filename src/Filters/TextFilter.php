<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TextFilter
{
    private bool $operators = false;

    public array $options = [];

    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function operators(): self
    {
        $this->operators = true;
        
        return $this;
    }

    public function getFilter(string $column, mixed $value, Builder|QueryBuilder $query): Builder|QueryBuilder
    {
        $operator = '';

        if (is_array($value)) {
            $operator = data_get($value, 'operator', $operator);
            $value = data_get($value, 'value');
        }

        return match ($operator) {
            '' => $query->where($column, $value),
            'contains' => $query->where($column, 'like', '%' . $value . '%'),
            'starts_with' => $query->where($column, 'like', $value . '%'),
            'ends_with' => $query->where($column, 'like', '%' . $value),
            'not_equal_to' => $query->where($column, '<>', $value),
            'empty' => $query->where(function ($query) use ($column) {
                $query->orWhere($column, null)->orWhere($column, '');
            }),
            default => $query
        };
    }

    public function getDataSource(): ?array
    {
        return [
            '' => 'Equal to',
            'contains' => 'Contains',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with',
            'not_equal_to' => 'Not equal to',
            'empty' => 'Empty'
        ];
    }

    public function getElement(): array
    {
        return [
            'type' => 'text',
            'operators' => $this->operators
        ];
    }
}