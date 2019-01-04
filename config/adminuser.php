<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel-admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for laravel-admin builtin model & tables.
    |
    */
    // admin_users table and model
    'database' => [
    'users_table' => 'admin_users',
    'users_model' => App\Models\AdminUser::class,

    // addresses table and model
    'addresses_table' => 'admin_addresses',
    'addresses_model' => App\Models\Address::class
    ],
];
