<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Support;

class IntegerAllocator extends Allocator
{
    protected const INITIAL_RIGHT = PHP_INT_MAX;

    public function allocate(array $ratios): array
    {
        $amount = $this->getRightBoundary() - $this->getLeftBoundary();
        $total = array_sum($ratios);

        $spaces = [];
        $leftBoundary = $this->getLeftBoundary();
        foreach ($ratios as $ratio) {
            $share = $amount * $ratio / $total;
            if ($share > 0) {
                $spaces[] = new static($leftBoundary, $leftBoundary += $share);
            }
        }

        return $spaces;
    }

    public function allocateTo($number, $bodyRatio = 1, $spacingRatio = 0): array
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
