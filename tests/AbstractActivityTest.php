<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use KolayBi\ActivityLog\Contracts\ActivityContextProvider;
use KolayBi\ActivityLog\Models\Activity;
use KolayBi\ActivityLog\Tests\Fixtures\TestConcreteActivity;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    config(['kolaybi.activity-log.connection' => 'testing']);
});

it('creates activity record via ::with()->log()', function () {
    $customProvider = new class () implements ActivityContextProvider {
        public function creatorId(): string
        {
            return 'user-abc';
        }

        public function tenantId(): string
        {
            return 'tenant-xyz';
        }
    };

    app()->instance(ActivityContextProvider::class, $customProvider);

    TestConcreteActivity::with('test-name', 'test-value')->log();

    $activity = Activity::first();

    expect($activity)->not->toBeNull()
        ->and($activity->creator_id)->toBe('user-abc')
        ->and($activity->tenant_id)->toBe('tenant-xyz')
        ->and($activity->group)->toBe('testing')
        ->and($activity->type)->toBe(TestConcreteActivity::class)
        ->and($activity->parameters)->toBe([
            'name'  => 'test-name',
            'value' => 'test-value',
        ]);
});

it('uses sync queue connection by default', function () {
    $customProvider = new class () implements ActivityContextProvider {
        public function creatorId(): string
        {
            return 'user-1';
        }

        public function tenantId(): int|string|null
        {
            return null;
        }
    };

    app()->instance(ActivityContextProvider::class, $customProvider);

    TestConcreteActivity::with('name', 'value')->log();

    expect(Activity::count())->toBe(1);
});

it('stores null creator_id and tenant_id when NullContextProvider is used', function () {
    TestConcreteActivity::with('name', 'value')->log();

    $activity = Activity::first();

    expect($activity->creator_id)->toBeNull()
        ->and($activity->tenant_id)->toBeNull();
});
