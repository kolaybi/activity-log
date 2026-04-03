<?php

namespace KolayBi\ActivityLog\Contracts;

use KolayBi\ActivityLog\Models\Activity;

interface ActivityParameterResolver
{
    /**
     * Resolve additional predefined parameters for the activity entry.
     *
     * @return array<string, mixed>
     */
    public function __invoke(Activity $activity): array;
}
