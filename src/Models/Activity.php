<?php

namespace KolayBi\ActivityLog\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     *
     * Supports enum resolution via {'enum': ..., 'value': ..., 'function': ...} pattern in parameters.
     */
    public function entry(): Attribute
    {
        return new Attribute(
            get: function (mixed $value, array $attributes) {
                $params = json_decode($attributes['parameters'] ?? '{}', true) ?: [];

                foreach ($params as $key => $param) {
                    if (is_array($param) && isset($param['enum'], $param['value'], $param['function'])) {
                        $params[$key] = $param['enum']::tryFrom($param['value'])?->{$param['function']}();
                    }
                }

                return __('activities.' . $attributes['type'], $params);
            },
        );
    }

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
        ];
    }
}
