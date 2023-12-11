<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Template
    |--------------------------------------------------------------------------
    |
    */

    'template' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Per Page
    |--------------------------------------------------------------------------
    |
    | The default number of records to display per page in a table.
    |
    */

    'per_page' => 10,

    /*
    |--------------------------------------------------------------------------
    | Pagination Links
    |--------------------------------------------------------------------------
    |
    */

    'on_each_side' => 3,

    /*
    |--------------------------------------------------------------------------
    | Per Page Options
    |--------------------------------------------------------------------------
    |
    | Default for number of records to display per page options.
    | The maximum value in the array should have the same value as 'max_per_page'.
    |
    */

    'per_page_options' => [
        10 => 'Show 10 Entries', 
        25 => 'Show 25 Entries', 
        50 => 'Show 50 Entries'
    ],

    /*
    |--------------------------------------------------------------------------
    | Column Alignment
    |--------------------------------------------------------------------------
    |
    | Set the center and right alignment classes for aligning columns columns.
    | The "left" alignment is set as the default.
    |
    */

    'alignment' => [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right'
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Breakpoints
    |--------------------------------------------------------------------------
    |
    | Define responsive breakpoints used for showing/hiding table columns.
    | You can use the !important CSS flag when creating your CSS classes
    | to override similar classes applied to your table column.
    |
    */

    'breakpoints' => [
        'sm' => 'hidden sm:table-cell',
        'md' => 'hidden md:table-cell',
        'lg' => 'hidden lg:table-cell',
        'xl' => 'hidden xl:table-cell'
    ],

    /*
    |--------------------------------------------------------------------------
    | Fetch Request Route
    |--------------------------------------------------------------------------
    |
    */

    'route' => [
        'prefix' => 'datatable',
        'uri' => '/fetch/table/{table}',
        'name' => 'datatable.fetch.table',
        'middleware' => ['web']
    ],

    /*
    |--------------------------------------------------------------------------
    | Directory
    |--------------------------------------------------------------------------
    |
    */

    'directory' => 'DataTables',

    /*
    |--------------------------------------------------------------------------
    | Query String Sync
    |--------------------------------------------------------------------------
    |
    | Sync the query string from the datatable URL with the URL of the page.
    |
    */

    'sync_query_string' => true,

    /*
    |--------------------------------------------------------------------------
    | Auto Update
    |--------------------------------------------------------------------------
    |
    | Auto refresh table after a set interval in seconds.
    |
    */

    'auto_update' => false,

    'auto_update_interval' => 60,

    /*
    |--------------------------------------------------------------------------
    | Request Mapping
    |--------------------------------------------------------------------------
    |
    | You can customize the request names to your preferred names.
    | Example: 'search' => 'q', 'order_column' => 'sort', 'per_page' => 'limit'
    | https://example.com?search=jane&order_column=name&per_page=25
    | ==>
    | https://example.com?q=jane&order=name&limit=25
    | 
    | NOTE: Remember to update the attributes section bellow if changes are made here
    |
    */

    'request_map' => [
        'page' => 'page',
        'search' => 'q',
        'order_column' => 'order_column',
        'order_direction' => 'order_direction',
        'per_page' => 'per_page'
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Attributes
    |--------------------------------------------------------------------------
    |
    | Data attributes for identifying various parts of the datatable through JavaScript
    |
    */

    'attributes' => [
        'data-uk-datatable' => 'true',
        'data-datatable-id' => ':id',
        'data-datatable-push-state' => ':sync_query_string',
        'data-datatable-auto-update' => ':auto_update',
        'data-datatable-auto-update-interval' => ':auto_update_interval',
        'data-datatable-url' => ':url',
        'data-datatable-table' => 'data-datatable-section=table',
        'data-datatable-search' => 'data-datatable-section=search',
        'data-datatable-info' => 'data-datatable-section=info',
        'data-datatable-length' => 'data-datatable-section=length',
        'data-datatable-pagination' => 'data-datatable-section=pagination',
        'data-datatable-region' => 'data-datatable-section=region',
        'data-datatable-loader' => 'data-datatable-section=loader',
        'data-datatable-page-length' => 'data-datatable-per-page',
        'data-datatable-index' => 'data-datatable-page-index',
        'data-datatable-order' => 'data-datatable-order-column',
        'data-datatable-direction' => 'data-datatable-order-direction',
        'data-datatable-search-input' => 'data-datatable-search-input',
        'data-datatable-checkbox' => 'data-datatable-checkbox',
        'data-datatable-request--page' => ':page',
        'data-datatable-request--search' => ':search',
        'data-datatable-request--order-column' => ':order_column',
        'data-datatable-request--order-direction' => ':order_direction',
        'data-datatable-request--per-page' => ':per_page'
    ],

];