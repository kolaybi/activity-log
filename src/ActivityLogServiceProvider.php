<?php

namespace KolayBi\ActivityLog;

use Illuminate\Support\ServiceProvider;
use KolayBi\ActivityLog\Contracts\ActivityContextProvider;
use KolayBi\ActivityLog\Contracts\ActivityParameterResolver;
use KolayBi\ActivityLog\Contracts\NullContextProvider;
use KolayBi\ActivityLog\Contracts\NullParameterResolver;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/activity-log.php', 'kolaybi.activity-log');

        $this->app->singleton(ActivityContextProvider::class, function () {
            $class = config('kolaybi.activity-log.context_provider');

            return $class ? $this->app->make($class) : new NullContextProvider();
        });

        $this->app->singleton(ActivityParameterResolver::class, function () {
            $class = config('kolaybi.activity-log.parameter_resolver');

            return $class ? $this->app->make($class) : new NullParameterResolver();
        });
    }

    public function boot(): void
    {
        if (config('kolaybi.activity-log.migrations', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'activity-log-migrations');

        $this->publishes([
            __DIR__ . '/../config/activity-log.php' => config_path('kolaybi/activity-log.php'),
        ], 'activity-log-config');
    }
}
