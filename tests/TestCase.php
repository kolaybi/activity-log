<?php

namespace KolayBi\ActivityLog\Tests;

use KolayBi\ActivityLog\ActivityLogServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ActivityLogServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('kolaybi.activity-log.queue.connection', 'sync');
    }
}
