<?php

namespace VariableSign\DataTable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DateFilter
{
    private bool $range = false;

    private string $from= 'From';

    private string $to = 'To';

    public array $options = [];

    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function from(string $from): self
    {
        $this->from = $from;
        
        return $this;
    }

    public function to(string $to): self
    {
        $this->to = $to;
        
        return $this;
    }

    public function range(): self
    {
        $this->range = true;
        
        return $this;
    }

    public function getFilter(string $column, mixed $value, Builder|QueryBuilder $query): Builder|QueryBuilder
    {
        dd($value);
        // return match ($value) {
        //     '' => $query,
        //     default => $query->where($column, $value)
        // };
        return $query;
    }

    public function getDataSource(): ?array
    {
        return [
            'from' => $this->from,
            'to' => $this->to
        ];
    }

    public function getElement(): array
    {
        return [
            'type' => 'date',
            'range' => $this->range
        ];
    }
}