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
