<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class BooleanFilter
{
    private object $true;

    private object $false;

    private string $trueLabel = 'True';

    private string $falseLabel = 'False';

    private string $defaultLabel = 'All';

    public array $options = [];

    private function dataSource(): array
    {
        return [
            '' => $this->defaultLabel,
            'true' => $this->trueLabel,
            'false' => $this->falseLabel
        ];
    }

    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function default(string $label): self
    {
        $this->defaultLabel = $label;
        
        return $this;
    }

    public function true(?string $label = null, ?callable $query = null): self
    {
        $this->trueLabel = $label;
        $this->true = $query;
        
        return $this;
    }

    public function false(?string $label = null, ?callable $query = null): self
    {
        $this->falseLabel = $label;
        $this->false = $query;
        
        return $this;
    }

    public function getFilter(string $column, mixed $value, Builder|QueryBuilder $query): Builder|QueryBuilder|null
    {
        return match ($value) {
            'true' => $this->true ? call_user_func($this->true, $query) : $query->where($column, 1),
            'false' => $this->false ? call_user_func($this->false, $query) : $query->where($column, 0),
            default => $query
        };
    }

    public function getDataSource(): ?array
    {
        return $this->dataSource();
    }

    public function getElement(): array
    {
        return [
            'type' => 'select',
            'multiple' => false
        ];
    }
}