<?php

namespace Ycore\Tool;

class Category
{


    /**
     * @return array
     * Notes:树形结构
     * User: Zy
     * Date: 2022/6/20 16:39
     */
    public static function infiniteClassification($fields = "*")
    {
        static $all = [];
        $category_list = \Ycore\Models\Category::where('status',1)->withoutGlobalScope('statusScope');
        if ($fields !== "*") {
            $category_list->select($fields);
        }
        $category_list = $category_list->get()->toArray();
        $items = [];
        foreach ($category_list as $value) {
            $items[$value['id']] = $value;
        }
        if ($items) {
            foreach ($items as $k => $v) {
                if (isset($items[$v['pid']])) {    // 这里就是 取 pid    如果 为顶级分类  就是0  所以 走 else ; 有下级分类 就是 son
                    $items[$v['pid']]['son'][] = &$items[$k];
                } else {
                    $all[] =& $items[$k];  //  存 顶级分类
                }
            }
        }
        return array_values($all);
    }


    /**
     * 无限极查询
     * @param $pid
     * @return array
     */
    public static function getCategoryByPid($pid)
    {


        return (new self())->c($pid, []);
    }


    private function c($pid, array $disableID)
    {

        static $all = [];

        $list = \Ycore\Models\Category::where('pid', $pid)->get();

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

}
