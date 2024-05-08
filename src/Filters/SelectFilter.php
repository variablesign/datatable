<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class SelectFilter
{
    private array $dataSource;

    private string $value = 'id';

    private string $label = 'name';

    private string $defaultLabel = 'All';

    public array $options = [];

    public function dataSource(array|Collection $dataSource): self
    {
        $this->dataSource = $dataSource instanceof Collection ? $dataSource->toArray() : $dataSource;

        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function value(string $value): self
    {
        $this->value = $value;
        
        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;
        
        return $this;
    }

    public function default(string $label): self
    {
        $this->defaultLabel = $label;
        
        return $this;
    }

    public function getFilter(string $column, mixed $value, Builder|QueryBuilder $query): Builder|QueryBuilder
    {
        return match ($value) {
            '' => $query,
            default => $query->where($column, $value)
        };
    }

    public function getDataSource(): ?array
    {
        $data = [
            '' => $this->defaultLabel
        ];

        foreach ($this->dataSource as $options) {
            $data[$options[$this->value]] = $options[$this->label];
        }

        return $data;
    }

    public function getElement(): array
    {
        return [
            'type' => 'select',
            'multiple' => false
        ];
    }
}