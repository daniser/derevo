<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use TTBooking\Derevo\Exceptions\TreeOverflowException;

trait MonitorsTreeOverflow
{
    use DetectsConstraintViolations;

    protected function performUpdate(Builder $query)
    {
        return $this->monitoredOperation(__FUNCTION__, $query);
    }

    protected function performInsert(Builder $query)
    {
        return $this->monitoredOperation(__FUNCTION__, $query);
    }

    protected function monitoredOperation(string $method, ...$arguments)
    {
        try {
            return parent::{$method}(...$arguments);
        } catch (QueryException $e) {
            $columns = [$this->getQualifiedLeftColumnName(), $this->getQualifiedRightColumnName()];
            if ($this->causedByConstraintViolation($e, $columns)) {
                throw new TreeOverflowException('Cannot insert node: tree overflown, rebuild needed', 0, $e);
            }

            throw $e;
        }
    }
}
