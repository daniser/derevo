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
            $this->causedByConstraintViolation($e, [
                $this->getQualifiedLeftColumnName(),
                $this->getQualifiedRightColumnName(),
            ]) && $this->throwOverflowException($e);

            throw $e;
        }
    }

    /**
     * @param  QueryException|null  $previous
     * @throws TreeOverflowException
     */
    protected function throwOverflowException(QueryException $previous = null)
    {
        throw (new TreeOverflowException('Cannot insert node: tree overflown, rebuild needed', 0, $previous))
            ->setNode($this);
    }
}
