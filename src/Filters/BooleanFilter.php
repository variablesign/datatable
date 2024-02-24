<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class BooleanFilter
{
    private array $default = [];

    private array $true = [];

    private array $false = [];

    public function default(string $label): self
    {
        $this->default = [$label, null];
        
        return $this;
    }

    public function true(?string $label = null, ?callable $query = null): self
    {
        $this->true = [$label, $query];
        
        return $this;
    }

    public function false(?string $label = null, ?callable $query = null): self
    {
        $this->false = [$label, $query];
        
        return $this;
    }

    public function getFilter(string $column, ?string $key, Builder|QueryBuilder $query): array
    {
        $filter = match ($key) {
            'true' => function (string $column, Builder|QueryBuilder $query) {
                return [
                    'label' => data_get($this->true, 0) ? $this->true[0] : 'True',
                    'query' => is_callable($this->true[1] ?? null) 
                        ? call_user_func($this->true[1], $query) 
                        : $query->where($column, 1)
                ];
            },
            'false' => function (string $column, Builder|QueryBuilder $query) {
                return [
                    'label' => data_get($this->false, 0) ? $this->false[0] : 'False',
                    'query' => is_callable($this->false[1] ?? null) 
                        ? call_user_func($this->false[1], $query) 
                        : $query->where($column, 0)
                ];
            },
            default => function () {
                return [
                    'label' => data_get($this->default, 0) ? $this->default[0] : 'All',
                    'query' => null
                ];
            }
        };

        return call_user_func($filter, $column, $query);
    }
}