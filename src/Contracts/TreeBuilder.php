<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Contracts;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use TTBooking\Derevo\Node;

interface TreeBuilder
{
    /**
     * @param  BigNumber|int|float|string  $leftRatio
     * @param  BigNumber|int|float|string  $bodyRatio
     * @param  BigNumber|int|float|string  $interimRatio
     * @param  BigNumber|int|float|string  $rightRatio
     * @return $this
     */
    public function setRatios($leftRatio, $bodyRatio, $interimRatio, $rightRatio): self;

    /**
     * @param  BigNumber|int|float|string  $leftRatio
     * @return $this
     */
    public function setLeftRatio($leftRatio): self;

    /**
     * @param  BigNumber|int|float|string  $bodyRatio
     * @return $this
     */
    public function setBodyRatio($bodyRatio): self;

    /**
     * @param  BigNumber|int|float|string  $interimRatio
     * @return $this
     */
    public function setInterimRatio($interimRatio): self;

    /**
     * @param  BigNumber|int|float|string  $rightRatio
     * @return $this
     */
    public function setRightRatio($rightRatio): self;

    /**
     * @return BigDecimal[]
     */
    public function getRatios(): array;

    /**
     * @return BigDecimal
     */
    public function getLeftRatio(): BigDecimal;

    /**
     * @return BigDecimal
     */
    public function getBodyRatio(): BigDecimal;

    /**
     * @return BigDecimal
     */
    public function getInterimRatio(): BigDecimal;

    /**
     * @return BigDecimal
     */
    public function getRightRatio(): BigDecimal;

    public function build(Node $node = null): void;

    public function needsRebuild(Node $node = null): bool;

    public function isValid(Node $node = null): bool;
}
