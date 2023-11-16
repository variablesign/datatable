<?php

namespace VariableSign\DataTable\Traits;

trait HasAttributes
{
    protected null|array|object $attributes = null;
    
    public function attributes(null|array|callable $attributes = null): self
    {
        $this->attributes = $attributes;

        return $this;
    }
}