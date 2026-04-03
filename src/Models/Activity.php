<?php

namespace KolayBi\ActivityLog\Models;

use BackedEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KolayBi\ActivityLog\Contracts\ActivityParameterResolver;

/**
 * @property string $type
 * @property array  $parameters
 */
class Activity extends Model
{
    use HasUlids;
    use SoftDeletes;

    protected $guarded = [];

    public function getConnectionName(): ?string
    {
        return config('kolaybi.activity-log.connection');
    }

    public function getTable(): string
    {
        return config('kolaybi.activity-log.table', 'activities');
    }

    /**
     * Resolve the human-readable entry from type + parameters using i18n.
     */
    public function entry(): Attribute
    {
        return new Attribute(
            get: fn() => $this->resolveEntry(),
        );
    }

    protected function resolveEntry(): string
    {
        $params = $this->resolveParameters();
        $predefined = app(ActivityParameterResolver::class)($this);

        return __('activities.' . $this->type, array_merge($predefined, $params));
    }

    protected function resolveParameters(): array
    {
        $params = $this->parameters ?? [];

        foreach ($params as $key => $param) {
            if (is_array($param) && isset($param['enum'], $param['value'], $param['function'])) {
                $enumClass = $param['enum'];

                if (is_string($enumClass) && enum_exists($enumClass) && is_a($enumClass, BackedEnum::class, true)) {
                    $params[$key] = $enumClass::tryFrom($param['value'])?->{$param['function']}();
                }
            }
        }

        return $params;
    }

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
        ];
    }
}
