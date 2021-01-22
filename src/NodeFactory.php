<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

abstract class NodeFactory extends Factory
{
    /** @var array<Node|null> $nodes */
    protected array $nodes = [null];

    public function configure()
    {
        return $this->afterMaking(function (Node $node) {
            shuffle($this->nodes);
            $node->setRelation('parent', $parent = reset($this->nodes));
            is_null($parent) || $parent->children[] = $node;
            $this->nodes[] = $node;
        });
    }

    protected function createChildren(Model $model)
    {
        parent::createChildren($model);

        if ($model instanceof Node) {
            $model->children->each(fn (Node $child) => $child->{$child->getParentColumnName()} = $model->getKey());
        }
    }
}
