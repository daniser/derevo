<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use TTBooking\Derevo\TreeScope;

trait MultiTree
{
    public static function bootMultiTree()
    {
        static::addGlobalScope(new TreeScope);
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
