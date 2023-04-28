<?php

namespace Ycore\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Jenssegers\Agent\Agent;

class ArticleScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        // TODO: Implement apply() method.

        $builder->where('status', 1)->where('push_status',
            1);


    }
}
