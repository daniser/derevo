<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use TTBooking\Derevo\Concerns\MultiTree;

class TreeScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if ($model instanceof Node || in_array(MultiTree::class, class_uses_recursive($model))) {
            $builder->where($model->getQualifiedScopedValues());
        }
    }
}
