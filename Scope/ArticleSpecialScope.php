<?php

namespace Ycore\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Jenssegers\Agent\Agent;

/**
 * 次作用域应用在前台中间件中
 */
class ArticleSpecialScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        // TODO: Implement apply() method.

        $a =getHostPrefix();

        if ($a ==="m") {

            $builder->whereRaw("(special_id = 0 or (special_id != 0 and exists (select 1 from `special` where `article`.`special_id` = `special`.`id` and `real_mobile_hide` = 2)) )");

        } else {


            $builder->whereRaw("(special_id = 0 or (special_id != 0 and exists (select 1 from `special` where `article`.`special_id` = `special`.`id` and `real_pc_hide` = 2)) )");

        }


    }
}
