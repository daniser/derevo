<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Database\Eloquent\Factories\Factory;

abstract class NodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            $this->newModel()->getParentColumnName() =>
                $this->newModel()->newQuery()->inRandomOrder()->firstOrNew()->getKey(),
        ];
    }
}
