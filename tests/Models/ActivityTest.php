<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use KolayBi\ActivityLog\Models\Activity;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    config(['kolaybi.activity-log.connection' => 'testing']);
});

it('uses table name from config', function () {
    expect((new Activity())->getTable())->toBe('activities');

    config(['kolaybi.activity-log.table' => 'custom_activities']);

    expect((new Activity())->getTable())->toBe('custom_activities');
});

it('uses connection from config', function () {
    expect((new Activity())->getConnectionName())->toBe('testing');

    config(['kolaybi.activity-log.connection' => 'custom']);

    expect((new Activity())->getConnectionName())->toBe('custom');
});

it('creates an activity record', function () {
    $activity = Activity::create([
        'creator_id' => 'user-123',
        'tenant_id'  => 'tenant-456',
        'group'      => 'company',
        'type'       => 'App\\Activities\\CompanyCreated',
        'parameters' => ['company_name' => 'Acme'],
    ]);

    expect($activity->exists)->toBeTrue()
        ->and($activity->id)->toBeString()
        ->and(strlen($activity->id))->toBe(26)
        ->and($activity->creator_id)->toBe('user-123')
        ->and($activity->tenant_id)->toBe('tenant-456')
        ->and($activity->group)->toBe('company')
        ->and($activity->type)->toBe('App\\Activities\\CompanyCreated')
        ->and($activity->parameters)->toBe(['company_name' => 'Acme']);
});

it('allows nullable creator_id and tenant_id', function () {
    $activity = Activity::create([
        'group'      => 'system',
        'type'       => 'App\\Activities\\SystemEvent',
        'parameters' => [],
    ]);

    expect($activity->exists)->toBeTrue()
        ->and($activity->creator_id)->toBeNull()
        ->and($activity->tenant_id)->toBeNull();
});

it('resolves entry from translation', function () {
    $activity = Activity::create([
        'group'      => 'company',
        'type'       => 'company_updated',
        'parameters' => ['company_name' => 'Acme Corp'],
    ]);

    app('translator')->addLines([
        'activities.company_updated' => ':company_name was updated',
    ], 'en');

    expect($activity->entry)->toBe('Acme Corp was updated');
});

it('resolves entry with enum parameter', function () {
    $activity = Activity::create([
        'group'      => 'company',
        'type'       => 'status_changed',
        'parameters' => [
            'status' => [
                'enum'     => \KolayBi\ActivityLog\Tests\Fixtures\TestStatus::class,
                'value'    => 'active',
                'function' => 'label',
            ],
        ],
    ]);

    app('translator')->addLines([
        'activities.status_changed' => 'Status changed to :status',
    ], 'en');

    expect($activity->entry)->toBe('Status changed to Active');
});
