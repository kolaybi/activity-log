<?php

use KolayBi\ActivityLog\Contracts\ActivityContextProvider;
use KolayBi\ActivityLog\Contracts\ActivityParameterResolver;
use KolayBi\ActivityLog\Contracts\NullContextProvider;
use KolayBi\ActivityLog\Contracts\NullParameterResolver;
use KolayBi\ActivityLog\Models\Activity;

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

it('binds NullParameterResolver when no resolver is configured', function () {
    $resolver = app(ActivityParameterResolver::class);

    expect($resolver)->toBeInstanceOf(NullParameterResolver::class);
});

it('binds custom parameter resolver when configured', function () {
    $customResolver = new class () implements ActivityParameterResolver {
        public function __invoke(Activity $activity): array
        {
            return ['custom' => 'value'];
        }
    };

    config(['kolaybi.activity-log.parameter_resolver' => $customResolver::class]);
    app()->forgetInstance(ActivityParameterResolver::class);

    $resolver = app(ActivityParameterResolver::class);

    expect($resolver(new Activity()))->toBe(['custom' => 'value']);
});
