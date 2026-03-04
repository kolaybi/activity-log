<?php

namespace KolayBi\ActivityLog\Contracts;

interface ActivityContextProvider
{
    public function creatorId(): int|string|null;

    public function tenantId(): int|string|null;
}
