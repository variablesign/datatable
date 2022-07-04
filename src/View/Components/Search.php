<?php

namespace VariableSign\Datatable\View\Components;

use Illuminate\View\Component;

class Search extends Component
{
    public function __construct($message)
    {
        //
    }

    public function render()
    {
        return view('datatable::components.search');
    }
}