<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Support;

/**
 * Class BCMathSpace or BCMathShare
 */
class BCMathAllocator
{
    const INITIAL_LEFT = 0;

    const INITIAL_RIGHT = PHP_INT_MAX;

    protected $leftBoundary;

    protected $rightBoundary;

    public function __construct(
        $leftBoundary = BCMathAllocator::INITIAL_LEFT,
        $rightBoundary = BCMathAllocator::INITIAL_RIGHT
        //BCMathAllocator $parent = null
    ) {
        $this->leftBoundary = $leftBoundary;
        $this->rightBoundary = $rightBoundary;
    }

    public function getLeftBoundary()
    {
        return $this->leftBoundary;
    }

    public function getRightBoundary()
    {
        return $this->rightBoundary;
    }

    /**
     * @param  int[]  $ratios
     * @return static[]
     */
    public function allocate(array $ratios): array
    {
        $amount = $this->rightBoundary - $this->leftBoundary;
        $total = array_sum($ratios);

        $spaces = [];
        $leftBoundary = $this->leftBoundary;
        foreach ($ratios as $ratio) {
            $share = $amount * $ratio / $total;
            if ($share > 0) {
                $spaces[] = new static($leftBoundary, $leftBoundary += $share);
            }
        }

        return $spaces;
    }

    /**
     * @param  int  $number
     * @param  int  $bodyRatio
     * @param  int  $spacingRatio
     * @return static[]
     */
    public function allocateTo(int $number, int $bodyRatio = 1, int $spacingRatio = 0): array
    {
        $ratioSet = [$spacingRatio, $bodyRatio];
        $useBodyRatio = true;

        $ratios = [];
        for ($i = 0; $i < $number * 2 + 1; $i++) {
            $ratios[] = $ratioSet[$useBodyRatio = ! $useBodyRatio];
        }

        return $this->allocate($ratios);
    }
}
