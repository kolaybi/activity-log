<?php

use Illuminate\Foundation\Bus\PendingClosureDispatch;
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

it('with() returns an instance of the concrete activity', function () {
    $activity = TestConcreteActivity::with('name', 'value');

    expect($activity)->toBeInstanceOf(TestConcreteActivity::class);
});

it('log() returns PendingClosureDispatch', function () {
    $result = TestConcreteActivity::with('name', 'value')->log();

    expect($result)->toBeInstanceOf(PendingClosureDispatch::class);
});

it('reads queue connection from config', function () {
    config(['kolaybi.activity-log.queue.connection' => 'redis']);

    $activity = new TestConcreteActivity('name', 'value');
    $reflection = new ReflectionMethod($activity, 'queueConnection');

    expect($reflection->invoke($activity))->toBe('redis');
});

it('defaults queue connection to sync', function () {
    config(['kolaybi.activity-log.queue' => []]);

    $activity = new TestConcreteActivity('name', 'value');
    $reflection = new ReflectionMethod($activity, 'queueConnection');

    expect($reflection->invoke($activity))->toBe('sync');
});

it('stores type as the concrete activity FQCN', function () {
    TestConcreteActivity::with('name', 'value')->log();

    $activity = Activity::first();

    expect($activity->type)->toBe(TestConcreteActivity::class);
});

it('stores group from the concrete activity GROUP constant', function () {
    TestConcreteActivity::with('name', 'value')->log();

    $activity = Activity::first();

    expect($activity->group)->toBe('testing');
});

it('stores parameters from the concrete activity', function () {
    TestConcreteActivity::with('foo', 'bar')->log();

    $activity = Activity::first();

    expect($activity->parameters)->toBe([
        'name'  => 'foo',
        'value' => 'bar',
    ]);
});

it('builds activity attributes with context provider values', function () {
    $customProvider = new class () implements ActivityContextProvider {
        public function creatorId(): string
        {
            return 'user-99';
        }

        public function tenantId(): string
        {
            return 'tenant-88';
        }
    };

    app()->instance(ActivityContextProvider::class, $customProvider);

    $activity = TestConcreteActivity::with('my-name', 'my-value');
    $reflection = new ReflectionMethod($activity, 'toActivityAttributes');
    $attributes = $reflection->invoke($activity);

    expect($attributes)->toBe([
        'creator_id' => 'user-99',
        'tenant_id'  => 'tenant-88',
        'type'       => TestConcreteActivity::class,
        'group'      => 'testing',
        'parameters' => [
            'name'  => 'my-name',
            'value' => 'my-value',
        ],
    ]);
});

it('builds activity attributes with null context when NullContextProvider is used', function () {
    $activity = TestConcreteActivity::with('a', 'b');
    $reflection = new ReflectionMethod($activity, 'toActivityAttributes');
    $attributes = $reflection->invoke($activity);

    expect($attributes['creator_id'])->toBeNull()
        ->and($attributes['tenant_id'])->toBeNull()
        ->and($attributes['type'])->toBe(TestConcreteActivity::class)
        ->and($attributes['group'])->toBe('testing');
});

it('createRecord persists via the given model class', function () {
    $reflection = new ReflectionMethod(TestConcreteActivity::class, 'createRecord');

    $reflection->invoke(null, Activity::class, [
        'creator_id' => 'user-42',
        'tenant_id'  => 'tenant-7',
        'type'       => TestConcreteActivity::class,
        'group'      => 'testing',
        'parameters' => ['name' => 'x', 'value' => 'y'],
    ]);

    $activity = Activity::first();

    expect($activity)->not->toBeNull()
        ->and($activity->creator_id)->toBe('user-42')
        ->and($activity->group)->toBe('testing');
});
