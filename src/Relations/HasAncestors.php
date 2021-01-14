<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Relations;

class HasAncestors extends HasAncestorsOrDescendants
{
    /**
     * @return string[]
     */
    protected function getBoundComparisonOperators()
    {
        return $this->andSelf ? ['<=', '>='] : ['<', '>'];
    }
}
