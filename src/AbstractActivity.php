<?php

namespace KolayBi\ActivityLog;

use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;
use KolayBi\ActivityLog\Contracts\ActivityContextProvider;

abstract class AbstractActivity
{
    private bool $withoutTenant = false;

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
     * Bypass tenant logging — sets tenant_id to null for this activity.
     */
    public function withoutTenant(): static
    {
        $this->withoutTenant = true;

        return $this;
    }

    /**
     * Whether to log the tenant for this activity.
     *
     * Override in concrete classes to permanently disable tenant logging.
     * The default implementation respects the withoutTenant() fluent call.
     */
    protected function shouldLogTenant(): bool
    {
        return ! $this->withoutTenant;
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

        $creatorColumn = config('kolaybi.activity-log.columns.creator', 'creator_id');
        $tenantColumn = config('kolaybi.activity-log.columns.tenant', 'tenant_id');

        return [
            $creatorColumn => $context->creatorId(),
            $tenantColumn  => $this->shouldLogTenant() ? $context->tenantId() : null,
            'type'         => static::class,
            'group'        => static::GROUP->value,
            'parameters'   => $this->parameters(),
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
