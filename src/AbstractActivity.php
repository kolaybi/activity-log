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
        $context = app(ActivityContextProvider::class);
        $creatorId = $context->creatorId();
        $tenantId = $context->tenantId();

        $closure = function () use ($creatorId, $tenantId) {
            $this->createRecord($creatorId, $tenantId);
        };

        return dispatch($closure)
            ->afterCommit()
            ->onConnection($this->queueConnection());
    }

    /**
     * Create the activity record in the database.
     */
    protected function createRecord(int|string|null $creatorId, int|string|null $tenantId): void
    {
        $modelClass = config('kolaybi.activity-log.model');

        $modelClass::create([
            'creator_id' => $creatorId,
            'tenant_id'  => $tenantId,
            'type'       => static::class,
            'group'      => static::GROUP->value,
            'parameters' => $this->parameters(),
        ]);
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
     * Attributes to be used in the translation replacements.
     */
    abstract protected function parameters(): array;
}
