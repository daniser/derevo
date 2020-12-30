<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Support;

abstract class Allocator
{
    protected const INITIAL_LEFT = 0;

    protected const INITIAL_RIGHT = null;

    /** @var int|float|string */
    private $leftBoundary;

    /** @var int|float|string */
    private $rightBoundary;

    private function __construct($leftBoundary, $rightBoundary)
    {
        $this->leftBoundary = $leftBoundary;
        $this->rightBoundary = $rightBoundary;
    }

    public static function within($leftBoundary = null, $rightBoundary = null): self
    {
        return new static($leftBoundary ?? static::INITIAL_LEFT, $rightBoundary ?? static::INITIAL_RIGHT);
    }

    final public function getLeftBoundary()
    {
        return $this->leftBoundary;
    }

    final public function getRightBoundary()
    {
        return $this->rightBoundary;
    }

    final public function getBoundaries(): array
    {
        return [$this->leftBoundary, $this->rightBoundary];
    }

    /**
     * @param  array  $ratios
     * @return static[]
     */
    abstract public function allocate(array $ratios): array;

    /**
     * @param  int  $number
     * @param  int|float|string  $bodyRatio
     * @param  int|float|string  $spacingRatio
     * @return static[]
     */
    abstract public function allocateTo(int $number, $bodyRatio = 1, $spacingRatio = 0): array;
}
