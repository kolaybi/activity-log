<?php

namespace KolayBi\ActivityLog;

use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use KolayBi\ActivityLog\Contracts\ActivityContextProvider;

abstract class AbstractActivity
{
    /**
     * Log this activity. Dispatches record creation as a queued closure.
     *
     * Context (creator, tenant) is resolved eagerly before dispatch,
     * because Auth/Request won't be available inside queued closures.
     */
    public function log(): PendingClosureDispatch|PendingDispatch
    {
        $attributes = $this->toActivityAttributes();
        $modelClass = config('kolaybi.activity-log.model');

        $closure = static function () use ($modelClass, $attributes) {
            self::createRecord($modelClass, $attributes);
        };

        return dispatch($closure)
            ->afterCommit()
            ->onConnection($this->queueConnection());
    }

    /**
     * Build the attribute array for the activity record.
     *
     * Context (creator, tenant) is resolved eagerly here,
     * because Auth/Request won't be available inside queued closures.
     */
    protected function toActivityAttributes(): array
    {
        $context = app(ActivityContextProvider::class);

        return [
            'creator_id' => $context->creatorId(),
            'tenant_id'  => $context->tenantId(),
            'type'       => static::class,
            'group'      => static::GROUP->value,
            'parameters' => $this->parameters(),
        ];
    }

    /**
     * The queue connection to use for dispatching.
     */
    protected function queueConnection(): string
    {
        return config('kolaybi.activity-log.queue.connection', 'sync');
    }

    /**
     * Fluent static factory.
     */
    public static function with(mixed ...$args): static
    {
        return new static(...$args);
    }

    /**
     * Persist the activity record.
     *
     * @param class-string $modelClass
     */
    protected static function createRecord(string $modelClass, array $attributes): void
    {
        $modelClass::create($attributes);
    }

    /**
     * Attributes to be used in the translation replacements.
     */
    abstract protected function parameters(): array;
}
