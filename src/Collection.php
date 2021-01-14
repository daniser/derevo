<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function toHierarchy(): BaseCollection
    {
        $dictionary = $this->getDictionary();

        uasort($dictionary, fn (Node $a, Node $b) => $a->getLeft() <=> $b->getLeft());

        /** @var Node $node */
        foreach ($dictionary as $node) {
            $node->setRelation('children', new BaseCollection);
        }

        $nestedKeys = [];

        foreach ($dictionary as $node) {
            $parentKey = $node->getParentKey();

            if (! is_null($parentKey) && array_key_exists($parentKey, $dictionary)) {
                $node->setRelation('parent', $dictionary[$parentKey]);
                $dictionary[$parentKey]->children[] = $node;
                $nestedKeys[] = $node->getKey();
            }
        }

        foreach ($nestedKeys as $key) {
            unset($dictionary[$key]);
        }

        return new BaseCollection($dictionary);
    }
}
