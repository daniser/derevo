<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder scoped(string[] $scope = [])
 */
trait ColumnScoped
{
    public static function bootColumnScoped()
    {
        static::addGlobalScope('column', function (Builder $builder) {
            $builder->getModel()->isScoped() && $builder->where($builder->getModel()->getQualifiedScopedValues());
        });
    }

    public static function scopeScoped(Builder $query, array $scope = []): Builder
    {
        return $query->withoutGlobalScope('column')->where($scope);
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        return parent::newInstance($attributes + $this->getScopedValues(), $exists);
    }

    public function isScoped(): bool
    {
        return count($this->getScopedColumns()) > 0;
    }

    public function getScopedColumns(): array
    {
        return (array) $this->scoped;
    }

    public function getQualifiedScopedColumns(): array
    {
        $table = $this->getTable();

        return array_map(fn ($column) => $table.'.'.$column, $this->getScopedColumns());
    }

    public function getScopedValues(): array
    {
        return $this->only($this->getScopedColumns());
    }

    public function getQualifiedScopedValues(): array
    {
        return array_combine($this->getQualifiedScopedColumns(), $this->getScopedValues());
    }

    public function inSameScope(self $other): bool
    {
        return $this->getScopedValues() === $other->getScopedValues();
    }
}
