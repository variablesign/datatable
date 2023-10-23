<?php

namespace VariableSign\DataTable;

use VariableSign\DataTable\Traits\HasAttributes;

class Table
{
    use HasAttributes;

    public function __get($name)
	{
		return $this->{$name};
	}
}