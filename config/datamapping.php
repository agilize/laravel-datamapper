<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Entity Directory
    |--------------------------------------------------------------------------
    |
    | This value is the default path to a directory where all Models must be
    | searched. Depending on development design you follow on your project
    | (DDD, MVC, etc), Models will be placed on different directories.
    */
    'entity_directory' => app_path('Packages'),

    /*
    |--------------------------------------------------------------------------
    | Primary Key Type
    |--------------------------------------------------------------------------
    |
    | This value is the type of primary key you project database uses.
    | This package cover only integer or uuid.
    */
    'primary_key_type' => 'uuid',

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | This value is the current version of you project API.
    */
    'api_version' => 'v1',


];