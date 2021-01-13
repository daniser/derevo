<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasSiblings extends HasMany
{
    /**
     * Force relation to include parent model in the result set.
     *
     * @var bool
     */
    protected $andSelf = false;

    /**
     * Create a new has siblings relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $ownerKey
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $ownerKey)
    {
        parent::__construct($query, $parent, $ownerKey, $ownerKey);
    }

    /**
     * Force relation to include parent model in the result set.
     *
     * @param  bool  $andSelf
     * @return $this
     */
    public function andSelf($andSelf = true)
    {
        $this->andSelf = $andSelf;

        return $this;
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->foreignKey, $this->getParentKey());
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like whereColumn.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Builder  $parentQuery
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Builder  $parentQuery
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationExistenceQueryForSelfRelation(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
    }
}
