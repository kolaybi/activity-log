<?php

namespace KolayBi\ActivityLog\Contracts;

use KolayBi\ActivityLog\Models\Activity;

class NullParameterResolver implements ActivityParameterResolver
{
    public function __invoke(Activity $activity): array
    {
        return [];
    }
}
