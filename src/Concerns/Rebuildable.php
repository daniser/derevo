<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use TTBooking\Derevo\Contracts\TreeBuilder;

trait Rebuildable
{
    /**
     * @return $this
     */
    public function rebuild(): self
    {
        app(TreeBuilder::class)->build($this);

        return $this;
    }
}
