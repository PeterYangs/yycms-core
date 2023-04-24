<?php


namespace Ycore\Http\Controllers\Admin;


use App\Service\Search\SearchInterface;
use App\Tool\Json;

class SearchController extends AuthCheckController
{


    /**
     * model 模型,如Admin
     *
     * label 显示列表 ,如[{label:'用户名',key:'username'},{label:'昵称',key:'nickname'}]
     *
     * p  分页数量
     *
     * @Auth(type='skip_auth')
     * Create by Peter
     * 2019/09/27 09:28:58
     * Email:904801074@qq.com
     * @param SearchInterface $search
     * @return false|string
     */
    function search(SearchInterface $search)
    {


        $model = request()->input('search.model');

        $condition = request()->input('search.condition');

        $label = request()->input('search.label');

        $defaultCondition = request()->input('search.defaultCondition');

        $namespace = request()->input('search.namespace');

        $with = request()->input('search.with');


        $p = request()->input('p', 1);

        \DB::connection()->enableQueryLog();

        try {
            $list = $search->AlertSearch($model, $condition, $label, $p, $defaultCondition, $namespace, $with);
        } catch (\Exception $exception) {

            return Json::code(2, $exception->getMessage());
        }
        $logs = \DB::getQueryLog();

        return Json::code(1, $logs, $list);

    }

}
