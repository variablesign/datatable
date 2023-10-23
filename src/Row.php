<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Traits\HasAttributes;

class Row
{
    use HasAttributes;

    public function __get($name)
	{
		return $this->{$name};
	}
}