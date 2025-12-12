<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Export Format
    |--------------------------------------------------------------------------
    |
    | This option controls the default export format when none is specified.
    | Supported formats: 'xlsx', 'csv', 'pdf'
    |
    */
    'default_format' => 'xlsx',

    /*
    |--------------------------------------------------------------------------
    | CSV Delimiter
    |--------------------------------------------------------------------------
    |
    | Define the default delimiter for CSV exports.
    |
    */
    'csv_delimiter' => ',',

    /*
    |--------------------------------------------------------------------------
    | File Name Prefix
    |--------------------------------------------------------------------------
    |
    | Default prefix for exported file names.
    |
    */
    'file_name_prefix' => 'export',

    /*
    |--------------------------------------------------------------------------
    | Date Format
    |--------------------------------------------------------------------------
    |
    | Format for date columns in exports.
    |
    */
    'date_format' => 'Y-m-d',

    /*
    |--------------------------------------------------------------------------
    | Time Format
    |--------------------------------------------------------------------------
    |
    | Format for datetime columns in exports.
    |
    */
    'time_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Auto-discover Relations
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic relationship discovery.
    |
    */
    'auto_discover_relations' => true,

    /*
    |--------------------------------------------------------------------------
    | Max Relations Depth
    |--------------------------------------------------------------------------
    |
    | Maximum depth for relationship discovery (prevent infinite loops).
    |
    */
    'max_relations_depth' => 1,

    /*
    |--------------------------------------------------------------------------
    | Format States
    |--------------------------------------------------------------------------
    |
    | Automatically format boolean and enum states.
    |
    */
    'format_states' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Hidden Columns
    |--------------------------------------------------------------------------
    |
    | Include hidden columns in export options.
    |
    */
    'show_hidden_columns' => false,

    /*
    |--------------------------------------------------------------------------
    | Page Orientation (PDF)
    |--------------------------------------------------------------------------
    |
    | Default page orientation for PDF exports.
    | Options: 'portrait', 'landscape'
    |
    */
    'page_orientation' => 'portrait',

    /*
    |--------------------------------------------------------------------------
    | Excluded Columns
    |--------------------------------------------------------------------------
    |
    | Columns to exclude from auto-discovery by default.
    |
    */
    'excluded_columns' => [
        'password',
        'remember_token',
        'email_verified_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable Preview
    |--------------------------------------------------------------------------
    |
    | Show preview before downloading.
    |
    */
    'enable_preview' => true,

    /*
    |--------------------------------------------------------------------------
    | Records Per Preview Page
    |--------------------------------------------------------------------------
    |
    | Number of records to show per page in preview.
    |
    */
    'preview_per_page' => 10,
];
