<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Generator;

class TreeBuilder implements Contracts\TreeBuilder
{
    protected BigDecimal $leftRatio;

    protected BigDecimal $bodyRatio;

    protected BigDecimal $interimRatio;

    protected BigDecimal $rightRatio;

    /**
     * TreeBuilder constructor.
     *
     * @param  BigNumber|int|float|string  $leftRatio
     * @param  BigNumber|int|float|string  $bodyRatio
     * @param  BigNumber|int|float|string  $interimRatio
     * @param  BigNumber|int|float|string  $rightRatio
     */
    public function __construct($leftRatio = 1, $bodyRatio = 1, $interimRatio = 1, $rightRatio = 1)
    {
        $this->setRatios($leftRatio, $bodyRatio, $interimRatio, $rightRatio);
    }

    public function setRatios($leftRatio, $bodyRatio, $interimRatio, $rightRatio): self
    {
        return $this
            ->setLeftRatio($leftRatio)
            ->setBodyRatio($bodyRatio)
            ->setInterimRatio($interimRatio)
            ->setRightRatio($rightRatio);
    }

    public function setLeftRatio($leftRatio): self
    {
        $this->leftRatio = BigDecimal::of($leftRatio);

        return $this;
    }

    public function setBodyRatio($bodyRatio): self
    {
        $this->bodyRatio = BigDecimal::of($bodyRatio);

        return $this;
    }

    public function setInterimRatio($interimRatio): self
    {
        $this->interimRatio = BigDecimal::of($interimRatio);

        return $this;
    }

    public function setRightRatio($rightRatio): self
    {
        $this->rightRatio = BigDecimal::of($rightRatio);

        return $this;
    }

    public function getRatios(): array
    {
        return [
            $this->getLeftRatio(),
            $this->getBodyRatio(),
            $this->getInterimRatio(),
            $this->getRightRatio(),
        ];
    }

    public function getLeftRatio(): BigDecimal
    {
        return $this->leftRatio;
    }

    public function getBodyRatio(): BigDecimal
    {
        return $this->bodyRatio;
    }

    public function getInterimRatio(): BigDecimal
    {
        return $this->interimRatio;
    }

    public function getRightRatio(): BigDecimal
    {
        return $this->rightRatio;
    }

    public function build(Node $node = null): void
    {
        // TODO: Implement build() method.
    }

    public function needsRebuild(Node $node = null): bool
    {
        return false;
    }

    public function isValid(Node $node = null): bool
    {
        return true;
    }

    protected function getRatioSum(int $count = 1): BigDecimal
    {
        return BigDecimal::sum(
            $this->getLeftRatio(),
            $this->getBodyRatio()->multipliedBy($count),
            $this->getInterimRatio()->multipliedBy($count - 1),
            $this->getRightRatio()
        );
    }

    /**
     * @param  BigInteger  $left
     * @param  BigInteger  $right
     * @param  int  $count
     * @return Generator<array<BigInteger>>
     */
    protected function allocateWithin(BigInteger $left, BigInteger $right, int $count = 1): Generator
    {
        if ($left->isGreaterThanOrEqualTo($right)) {
            throw new \RuntimeException('Left boundary must be lesser than right.');
        }

        $space = $right->minus($left)->toBigDecimal();
        $ratioSum = $this->getRatioSum($count);
        $leftRatio = $this->getLeftRatio()->dividedBy($ratioSum);
        $bodyRatio = $this->getBodyRatio()->dividedBy($ratioSum);
        $interimRatio = $this->getInterimRatio()->dividedBy($ratioSum);

        for ($current = 0; $current < $count; $current++) yield [
            BigDecimal::sum(
                $left,
                $leftRatio->multipliedBy($space),
                $bodyRatio->multipliedBy($current)->multipliedBy($space),
                $interimRatio->multipliedBy($current)->multipliedBy($space),
            ),
        ];
    }
}
