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
    | Pagination Style
    |--------------------------------------------------------------------------
    |
    | The default pagination styles for your tables.
    | You can choose from 'default', 'minimal', 'simple', 'advanced'.
    |
    */

    'pagination_style' => 'simple',

    /*
    |--------------------------------------------------------------------------
    | Text Pagination Controls
    |--------------------------------------------------------------------------
    |
    | By default, icons are used as the pagination controls.
    | You can use text based pagination control by enabling this feature.
    | HTML code is also supported for the 'previous' & 'next' values.
    |
    */

    'text_pagination_controls' => [
        'enable' => false,
        'previous' => 'Prev',
        'next' => 'Next'
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Mapping
    |--------------------------------------------------------------------------
    |
    | You can customize the request names to your preferred names.
    | Example: 'search' => 'q', 'sort_column' => 'sort', 'per_page' => 'limit'
    | https://example.com?search=jane&sort_column=name&per_page=25
    | ==>
    | https://example.com?q=jane&sort=name&limit=25
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