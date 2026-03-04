<?php

namespace KolayBi\ActivityLog\Contracts;

class NullContextProvider implements ActivityContextProvider
{
    public function creatorId(): int|string|null
    {
        return null;
    }

    public function tenantId(): int|string|null
    {
        return null;
    }
}
