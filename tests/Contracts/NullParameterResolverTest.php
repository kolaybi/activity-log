<?php

use KolayBi\ActivityLog\Contracts\NullParameterResolver;
use KolayBi\ActivityLog\Models\Activity;

it('returns empty array', function () {
    $resolver = new NullParameterResolver();

    expect($resolver(new Activity()))->toBe([]);
});
