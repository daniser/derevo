<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Derevo\Relations\HasManyDescendants;

trait HasRelationshipsWithinTree
{
    /**
     * Define a one-to-many relationship within a tree.
     *
     * @param  string  $related
     * @param  string|null  $leftColumn
     * @param  string|null  $rightColumn
     * @return HasManyDescendants
     */
    public function hasManyDescendants($related = null, $leftColumn = null, $rightColumn = null)
    {
        $instance = $this->newRelatedInstance($related ?? static::class);

        $foreignKey = $instance->getTable().'.'.$this->getForeignKey();

        $localKey = $this->getKeyName();

        $leftColumn = $leftColumn ?: $this->getLeftColumnName();

        $rightColumn = $rightColumn ?: $this->getRightColumnName();

        $foreignLeftColumn = $instance->getTable().'.'.$leftColumn;

        $foreignRightColumn = $instance->getTable().'.'.$rightColumn;

        return $this->newHasManyDescendants(
            $instance->newQuery(), $this, $foreignKey, $localKey, $foreignLeftColumn, $foreignRightColumn, $leftColumn, $rightColumn
        );
    }

    /**
     * Instantiate a new HasManyDescendants relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @param  string  $leftColumn
     * @param  string  $rightColumn
     * @return HasManyDescendants
     */
    protected function newHasManyDescendants(Builder $query, Model $parent, $foreignKey, $localKey, $foreignLeftColumn, $foreignRightColumn, $leftColumn, $rightColumn)
    {
        return new HasManyDescendants($query, $parent, $foreignKey, $localKey, $foreignLeftColumn, $foreignRightColumn, $leftColumn, $rightColumn);
    }
}
