<?php

use KolayBi\ActivityLog\Models\Activity;

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Model
    |--------------------------------------------------------------------------
    |
    | The Eloquent model used to store activity records. Override this to
    | use a custom model that extends the default Activity model.
    |
    */

    'model' => Activity::class,

    /*
    |--------------------------------------------------------------------------
    | Context Provider
    |--------------------------------------------------------------------------
    |
    | The class that resolves creator and tenant context for activities.
    | Must implement KolayBi\ActivityLog\Contracts\ActivityContextProvider.
    | When null, NullContextProvider is used (returns null for both).
    |
    */

    'context_provider' => null,

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection used for the activities table.
    | When null, the default connection is used.
    |
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    */

    'table' => 'activities',

    /*
    |--------------------------------------------------------------------------
    | Column Names
    |--------------------------------------------------------------------------
    |
    | Customize column names used for creator and tenant identification.
    | Useful when your schema uses different naming conventions
    | (e.g. 'company_id' instead of 'tenant_id').
    |
    */

    'columns' => [
        'creator' => 'creator_id',
        'tenant'  => 'tenant_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Run Migrations
    |--------------------------------------------------------------------------
    |
    | When true, the package will automatically load its migrations.
    | Set to false if you have published or written your own migrations.
    |
    */

    'migrations' => true,

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Queue connection and queue name for dispatching activity records.
    | Use 'sync' to write activities synchronously (default).
    |
    */

    'queue' => [
        'connection' => 'sync',
    ],
];
