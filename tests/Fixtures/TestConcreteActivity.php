<?php

namespace KolayBi\ActivityLog\Tests\Fixtures;

use KolayBi\ActivityLog\AbstractActivity;

class TestConcreteActivity extends AbstractActivity
{
    protected const TestActivityGroup GROUP = TestActivityGroup::TESTING;

    public function __construct(
        private readonly string $name,
        private readonly string $value,
    ) {}

    protected function parameters(): array
    {
        return [
            'name'  => $this->name,
            'value' => $this->value,
        ];
    }
}
