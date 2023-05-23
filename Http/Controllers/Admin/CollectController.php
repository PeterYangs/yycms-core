<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Tool\Json;

class CollectController extends AuthCheckController
{


    function add()
    {


        $post = request()->input();

        $collect = Collect::create($post);


        $item = Collect::with('son')->where('id', $collect->id)->first();

        return Json::code(1, 'success', $item);

    }


    function remove()
    {

        $post = request()->input();


        Collect::where('category_id', $post['category_id'])->where('son_id', $post['son_id'])->delete();

        return Json::code(1, 'success');

    }


    function search()
    {
        $page = request()->input('page', 1);

        $category_id = request()->input('category_id');

        $title = trim(request()->input('title'));

        $category = Category::where('id', $category_id)->first();


        $mainCategoryIds = Collect::whereIn('son_id',
            [$category->id, $category->pid])->get()->pluck('category_id')->all();


        $query = Article::with('category')->whereNull('deleted_at')
            ->whereIn('category_id', $mainCategoryIds)
            ->withoutGlobalScopes()->orderBy('id', 'desc');


        if ($title) {

            $query->where('title', "like", "%" . $title . "%");
        }

        $list = $query->paginate(10, ["*"], 'page', $page);


        return Json::code(1, 'success', $list);


    }


    /**
     * 添加一对多关联
     */
    function add_association_object()
    {


        $article_id = request()->input('id');

        $maidId = request()->input('main_id', []);


        if (!is_array($maidId)) {


            return Json::code(2, '主id参数类型错误');
        }


        foreach ($maidId as $value) {


            try {

                ArticleAssociationObject::create([
                    'main' => $value,
                    'slave' => $article_id
                ]);

            } catch (\Exception $exception) {

            }


        }

        //更新updated_at时间
        Article::where('id', $article_id)->update(['updated_at' => date('Y-m-d H:i:s')]);


        return Json::code(1, 'success');


    }


    function detail()
    {


        $id = request()->input('id');

        $list = ArticleAssociationObject::where('slave', $id)->get();


        if ($list->count() <= 0) {

            return Json::code(1, 'success');
        }

        $re = Article::with('category.category_route')->whereIn('id',
            $list->pluck('main')->all())->whereNull('deleted_at')->withoutGlobalScopes()->get();


        return Json::code(1, 'success', $re);
    }


    /**
     * 移除关联关系
     */
    function removeObj()
    {

        $id = request()->input('id');

        $maidId = request()->input('main_id');

        ArticleAssociationObject::where('slave', $id)->where('main', $maidId)->delete();


        return Json::code(1, 'success');


    }


}
