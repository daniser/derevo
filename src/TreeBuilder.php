<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;

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
     * @return array<array<BigInteger>>
     */
    protected function allocateWithin(BigInteger $left, BigInteger $right, int $count = 1): array
    {
        if ($left->isGreaterThanOrEqualTo($right)) {
            throw new \RuntimeException('Left boundary must be lesser than right.');
        }

        $space = $right->minus($left)->toBigDecimal();
        $ratioSum = $this->getRatioSum($count);
        $leftPadding = $this->getLeftRatio()->dividedBy($ratioSum)->multipliedBy($space);
        $bodyPadding = $this->getBodyRatio()->dividedBy($ratioSum)->multipliedBy($space);
        $interimPadding = $this->getInterimRatio()->dividedBy($ratioSum)->multipliedBy($space);
        $currentOffset = $left->plus($leftPadding);

        $chunks = [];

        for ($current = 0; $current < $count; $current++) {
            $chunks[] = [
                $currentOffset->toScale(0, RoundingMode::HALF_EVEN)->toBigInteger(),
                $currentOffset->plus($bodyPadding)->toScale(0, RoundingMode::HALF_EVEN)->toBigInteger(),
            ];
            $currentOffset = BigDecimal::sum($currentOffset, $bodyPadding, $interimPadding);
        }

        return $chunks;
    }
}
