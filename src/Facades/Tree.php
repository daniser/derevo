<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Facades;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Illuminate\Support\Facades\Facade;
use TTBooking\Derevo\Node;

/**
 * @method static $this setRatios(BigNumber|int|float|string $leftRatio, BigNumber|int|float|string $bodyRatio, BigNumber|int|float|string $interimRatio, BigNumber|int|float|string $rightRatio)
 * @method static $this setLeftRatio(BigNumber|int|float|string $leftRatio)
 * @method static $this setBodyRatio(BigNumber|int|float|string $bodyRatio)
 * @method static $this setInterimRatio(BigNumber|int|float|string $interimRatio)
 * @method static $this setRightRatio(BigNumber|int|float|string $rightRatio)
 * @method static BigDecimal[] getRatios()
 * @method static BigDecimal getLeftRatio()
 * @method static BigDecimal getBodyRatio()
 * @method static BigDecimal getInterimRatio()
 * @method static BigDecimal getRightRatio()
 * @method static void build(Node $node = null)
 * @method static bool needsRebuild(Node $node = null)
 * @method static bool isValid(Node $node = null)
 * @see \TTBooking\Derevo\TreeBuilder::class
 */
class Tree extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \TTBooking\Derevo\Contracts\TreeBuilder::class;
    }
}
