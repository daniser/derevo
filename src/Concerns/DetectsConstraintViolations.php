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
        return Str::contains($message = $e->getMessage(), [
            'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry',
        ]) && (empty($columns) || Str::contains($message, $this->withoutDatabaseName($columns)));
    }

    /**
     * @param  string|string[]  $columns
     * @return string|string[]
     */
    protected function withoutDatabaseName($columns)
    {
        return is_array($columns)
            ? array_map([$this, __FUNCTION__], $columns)
            : (Str::substrCount($columns, '.') > 1 ? Str::after($columns, '.') : $columns);
    }
}
