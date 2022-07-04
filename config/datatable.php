<?php

return [

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
    | Maximum Per Page
    |--------------------------------------------------------------------------
    |
    | The default maximum number of records to display per page.
    |
    */

    'max_per_page' => 50,

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
    | Request Mapping
    |--------------------------------------------------------------------------
    |
    | You can customize the request to your preferred names.
    | Example: changing 'search' => 'q' and 'sort_column' => 'sort':
    | https://example.com?search=&sort_column=  <==>  https://example.com?q=&sort=
    |
    */

    'request_map' => [
        'search' => 'search',
        'sort_column' => 'sort_column',
        'sort_direction' => 'sort_direction',
        'per_page' => 'per_page',
        'get_total_records' => 'get_total_records'
    ],
    
];