<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use Illuminate\Support\Str;
use Throwable;

trait DetectsConstraintViolations
{
    /**
     * @param  Throwable  $e
     * @param  string|string[]  $columns
     * @return bool
     */
    protected function causedByConstraintViolation(Throwable $e, $columns = []): bool
    {
        return Str::contains($e->getMessage(), array_merge([
            'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry',
        ], $this->withoutDatabaseName($columns)));
    }

    /**
     * @param  string|string[]  $columns
     * @return string|string[]
     */
    protected function withoutDatabaseName($columns)
    {
        return array_map(
            static fn (string $column) => Str::substrCount($column, '.') > 1 ? Str::after($column, '.') : $column,
            (array) $columns
        );
    }
}
