<?php

use KolayBi\ActivityLog\Contracts\NullContextProvider;

it('returns null for creatorId', function () {
    $provider = new NullContextProvider();

    expect($provider->creatorId())->toBeNull();
});

it('returns null for tenantId', function () {
    $provider = new NullContextProvider();

    expect($provider->tenantId())->toBeNull();
});
