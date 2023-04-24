<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/6/20
 * Time: 16:33
 */

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\ArticleExpand;
use Ycore\Models\Category;
use Ycore\Models\CategoryRoute;
use App\Tool\Json;
use Illuminate\Http\Request;

class CategoryController extends AuthCheckController
{
    /**
     * 获取拓展分类id
     * Create by Peter Yang
     * 2022-06-22 17:06:52
     * @param $category_id
     * @return int
     */
    static function getExpandTableCategoryId($category_id): int
    {


        $ae = ArticleExpand::where('category_id', $category_id)->first();

        if ($ae) {


            return $category_id;
        }


        $category = Category::where('id', $category_id)->first();

        if (!$category) {


            return 0;
        }


        return self::getExpandTableCategoryId($category->pid);


    }

    /**
     * 获取关联表名称
     * Create by Peter Yang
     * 2022-06-22 16:46:32
     * @param $category_id
     */
    static function getExpandTableName($category_id): string
    {


        $ae = ArticleExpand::where('category_id', $category_id)->first();

        if ($ae) {


            return "expand_table_" . $category_id;
        }


        $category = Category::where('id', $category_id)->first();

        if (!$category) {


            return "";
        }


        return self::getExpandTableName($category->pid);


    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Notes:分类列表
     * User: Zy
     * Date: 2022/6/20 16:37
     */
    public function categoryList(Request $request)
    {
        $list = $this->infiniteClassification();

        return Json::code(1, 'success', $list);
    }

    /**
     * @return array
     * Notes:无限极分类
     * User: Zy
     * Date: 2022/6/20 16:39
     */
    private function infiniteClassification()
    {
        static $all = [];
        $category_list = Category::with('category_route')->with('collect.son')->withoutGlobalScope('statusScope')->get()->toArray();
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

    //  无限极分类  根据点击分类 上架 下架  判断有没有子级  获取所有子级 递归调用此方法

    /**
     * @param Request $request
     * @return string
     * Notes:分类 新增 或 修改
     * User: Zy
     * Date: 2022/6/23 16:38
     */
    public function categoryUpdate(Request $request)
    {
        $post = $request->input();

        $id = $post['id'] ?? null;   //修改分类 id

        try {

            \DB::beginTransaction();

            $category = Category::withoutGlobalScope('statusScope')->updateOrCreate(['id' => $id], $post);

            foreach ($post['category_route'] as $value) {


                $value['category_id'] = $category->id;

                CategoryRoute::updateOrCreate(['id' => $value['id'] ?? null], $value);

            }

            \DB::commit();

        } catch (\Exception $exception) {

            \DB::rollBack();

            return Json::code(2, $exception->getMessage());

        }


        return Json::code(1, 'success');
    }

    /**
     * @return string
     * Notes:分类 发布状态
     * User: Zy
     * Date: 2022/6/24 17:03
     */
    public function categoryReleaseStatus()
    {
        $id = request()->post('id', 0);
        $status = request()->post('status', 0);

        if (!$id) {
            return Json::code(3, '分类不存在！');
        }
        //根据当前分类下  还有没有子级分类
        $list = $this->infiniteAllClassification_categoryStatus($id);

        //只取 当前分类下  所有子级分类的 主键
        $list_category_son_id = array_values(array_column($list, 'id'));
        //将 当前 点击分类 和 所有子级分类 合并为一个数组
        array_unshift($list_category_son_id, $id);

        if ($status == 0) {
            //前台为下架  即将变为上架  所有子级都变为 上架
            $update_status = 1;
        } else {
            //前台为上架 即将变为下架 所有子级都变为 下架
            $update_status = 0;
        }
        //使用 whereIn  value可以为 一维数组  批量更新分类状态
        $bool_category = Category::whereIn('id',
            $list_category_son_id)->withoutGlobalScope('statusScope')->update(['status' => $update_status]);
        if (!$bool_category) {
            return Json::code(2, '失败', ['ids' => $list_category_son_id, 'status' => $update_status]);
        }
        return Json::code(1, '修改成功');
    }

    private function infiniteAllClassification_categoryStatus($id)
    {
        static $all = [];
        $category_list = Category::where('pid', $id)->get(['id', 'pid'])->toArray();
        if ($category_list) {
            foreach ($category_list as $k => $v) {
                $all[] = $v;
//                $all[]=str_repeat('---',$level).$v['name'];
                $id = $v['id'];
                $this->infiniteAllClassification_categoryStatus($id);
            }
        }
        return array_values($all);

    }

    /**
     * @return string
     * Notes:删除分类
     * User: Zy
     * Date: 2022/6/24 17:08
     */
    public function categoryDelete()
    {
        $id = request()->post('id', 0);
        if (!$id) {
            return Json::code(3, '分类不存在！');
        }
        $isHavingChildCategory = Category::where('pid', $id)->first();
        if ($isHavingChildCategory) {
            return Json::code(2, '无法删除,该分类下有其他子分类,请先删除子分类!');
        }

        $bool = Category::destroy($id);
        if ($bool) {
            return Json::code(1, '删除成功');
        }
        return Json::code(2, '删除失败');
    }


    function getCategoryNameById()
    {

        $cid = \request()->input('cid');

        $item = Category::where('id', $cid)->firstOrFail();


        return Json::code(1, 'success', $item);
    }


}
