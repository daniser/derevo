<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * The nested set node being queried.
     *
     * @var Node
     */
    protected $model;

    /**
     * Add a where clause on the parent key to the query.
     *
     * @param  mixed  $parentKey
     * @return $this
     */
    public function whereParentKey($parentKey): self
    {
        if (is_array($parentKey) || $parentKey instanceof Arrayable) {
            $this->query->whereIn($this->model->getQualifiedParentColumnName(), $parentKey);

            return $this;
        }

        if ($parentKey !== null && $this->model->getKeyType() === 'string') {
            $parentKey = (string) $parentKey;
        }

        return $this->where($this->model->getQualifiedParentColumnName(), '=', $parentKey);
    }

    /**
     * Add a where clause on the parent key to the query.
     *
     * @param  mixed  $parentKey
     * @return $this
     */
    public function whereParentKeyNot($parentKey): self
    {
        if (is_array($parentKey) || $parentKey instanceof Arrayable) {
            $this->query->whereNotIn($this->model->getQualifiedParentColumnName(), $parentKey);

            return $this;
        }

        if ($parentKey !== null && $this->model->getKeyType() === 'string') {
            $parentKey = (string) $parentKey;
        }

        return $this->where($this->model->getQualifiedParentColumnName(), '!=', $parentKey);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array|string  $columns
     * @return Collection|static[]
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get($columns);
        /*$builder = $this->applyScopes();

        // If we actually found nodes we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($nodes = $builder->getNodes($columns)) > 0) {
            $nodes = $builder->eagerLoadRelations($nodes);
        }

        return $builder->getNode()->newCollection($nodes);*/
    }

    /**
     * Get the hydrated nodes without eager loading.
     *
     * @param  array|string  $columns
     * @return Node[]|static[]
     */
    public function getNodes($columns = ['*']): array
    {
        return $this->getModels($columns);
    }

    /**
     * Get the node instance being queried.
     *
     * @return Node|static
     */
    public function getNode(): Node
    {
        return $this->getModel();
    }

    /**
     * Set a node instance for the node being queried.
     *
     * @param  Node  $node
     * @return $this
     */
    public function setNode(Node $node): self
    {
        return $this->setModel($node);
    }
}
