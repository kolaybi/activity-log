<?php

namespace KolayBi\ActivityLog\Tests\Fixtures;

use KolayBi\ActivityLog\AbstractActivity;

class TestTenantFreeActivity extends AbstractActivity
{
    protected const TestActivityGroup GROUP = TestActivityGroup::TESTING;

    public function __construct(
        private readonly string $name,
        private readonly string $value,
    ) {}

    protected function shouldLogTenant(): bool
    {
        return false;
    }

    protected function parameters(): array
    {
        return [
            'name'  => $this->name,
            'value' => $this->value,
        ];
    }
}
