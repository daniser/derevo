<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\Derevo\Node;

/**
 * @method static void build(Node $node)
 * @see \TTBooking\Derevo\TreeBuilder::class
 */
class Tree extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \TTBooking\Derevo\Contracts\TreeBuilder::class;
    }
}
