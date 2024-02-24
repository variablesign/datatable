<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Traits\HasAttributes;

class Column
{
    use HasAttributes;
    
    protected ?string $name = null;

    protected ?string $alias = null;

    protected ?string $title = null;

    protected bool $index = false;

    protected bool $checkbox = false;

    protected ?object $edit = null;

    protected ?string $responsive = null;

    protected ?string $alignment = null;

    protected bool|array|object $searchable = false;

    protected bool|array|object $sortable = false;

    protected bool|object $filterable = false;

    // protected null|array|object $colgroup = null;

    protected null|array|object $checkboxAttributes = null;

    public function name(string $name): self
    {
        $this->name = $name;
        $this->alias = $name;

        return $this;
    }

    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function index(): self
    {
        $this->index = true;

        return $this;
    }

    public function edit(callable $edit): self
    {
        $this->edit = $edit;

        return $this;
    }

    public function searchable(bool|array|callable $searchable = true): self
    {
        $searchable = $searchable === true ? [$this->name] : $searchable;
        $this->searchable = $searchable;

        return $this;
    }

    public function sortable(bool|array|callable $sortable = true): self
    {
        $sortable = $sortable === true ? [$this->name] : $sortable;
        $this->sortable = $sortable;

        return $this;
    }

    public function filterable(callable $filterable): self
    {
        $this->filterable = $filterable;

        return $this;
    }

    // public function colgroup(null|array|callable $attributes = null): self
    // {
    //     $this->colgroup = $attributes;

    //     return $this;
    // }

    public function responsive(string $breakpoint): self
    {
        $this->responsive = $breakpoint;

        return $this;
    }

    public function align(string $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function checkbox(null|array|callable $attributes = null): self
    {
        $this->checkbox = true;
        $this->checkboxAttributes = $attributes;

        return $this;
    }

    public function __get($name)
	{
		return $this->{$name};
	}
}