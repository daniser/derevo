<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ColumnScoped
{
    public static function bootColumnScoped()
    {
        static::addGlobalScope('column', function (Builder $builder) {
            $builder->where($builder->getModel()->getQualifiedScopedValues());
        });
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
