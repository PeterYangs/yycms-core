<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Category;
use Ycore\Tool\Json;

class CommonController extends AuthCheckController
{


    static function getCategoryByPid($pid)
    {


        return (new self())->c($pid, []);
    }

    /**
     * Create by Peter
     * 2019/11/12 16:27:39
     * Email:904801074@qq.com
     * @param $pid
     * @param array $disableID 不查询的id
     * @return array
     */
    private function c($pid, array $disableID)
    {

        static $all = [];

        $list = Category::where('pid', $pid)->get();

        if ($list) {

            foreach ($list as $key => $value) {

                $id = $value->id;

                $detail = \Cache::get('category:detail:pc_' . $id);

                if (in_array($id, $disableID, true)) {
                    continue;
                }

                $value->detail = $detail;

                $all[] = $value;

                $this->c($id, $disableID);


            }

        }

        return array_values($all);

    }

    /**
     * @Auth(type='skip_auth')
     * 分类下拉组件使用
     */
    function forCategory()
    {

        $pid = request()->input('id', 0);

        return Json::code(1, 'success', $this->c($pid, []));

    }

}
