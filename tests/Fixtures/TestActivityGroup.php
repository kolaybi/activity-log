<?php

namespace KolayBi\ActivityLog\Tests\Fixtures;

use KolayBi\ActivityLog\Contracts\ActivityGroup;

enum TestActivityGroup: string implements ActivityGroup
{
    case NONE = 'none';
    case TESTING = 'testing';
}
