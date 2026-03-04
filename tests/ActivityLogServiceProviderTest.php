<?php

use KolayBi\ActivityLog\Contracts\ActivityContextProvider;
use KolayBi\ActivityLog\Contracts\NullContextProvider;

it('merges config', function () {
    $config = config('kolaybi.activity-log');

    expect($config)->toBeArray()
        ->and($config['table'])->toBe('activities')
        ->and($config['connection'])->toBeNull()
        ->and($config['context_provider'])->toBeNull();
});

it('binds NullContextProvider when no provider is configured', function () {
    $provider = app(ActivityContextProvider::class);

    expect($provider)->toBeInstanceOf(NullContextProvider::class);
});

it('binds custom context provider when configured', function () {
    $customProvider = new class () implements ActivityContextProvider {
        public function creatorId(): int|string|null
        {
            return 'custom-user';
        }

        public function tenantId(): int|string|null
        {
            return 'custom-tenant';
        }
    };

    config(['kolaybi.activity-log.context_provider' => $customProvider::class]);
    app()->forgetInstance(ActivityContextProvider::class);

    $provider = app(ActivityContextProvider::class);

    expect($provider->creatorId())->toBe('custom-user')
        ->and($provider->tenantId())->toBe('custom-tenant');
});
