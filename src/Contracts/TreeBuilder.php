<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Contracts;

use TTBooking\Derevo\Node;

interface TreeBuilder
{
    public function build(Node $node = null): void;
}
