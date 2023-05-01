<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Models\Article;
use Ycore\Models\StoreArticle;
use Ycore\Tool\Expand;
use Ycore\Tool\Json;
use Ycore\Tool\Push;
use Ycore\Tool\Search;
use Ycore\Tool\Seo;
use Illuminate\Support\Facades\File;

class StoreArticleController extends AuthCheckController
{


    function list()
    {


        $list = StoreArticle::with('category')->with('special')->orderBy('id', 'desc')->where('status', 1);

        $custom = Search::searchList($list, request()->input('search', '[]'));


        foreach ($custom as $key => $value) {


            if ($value['dec'] === 'category_id') {


                $category_id_list = [];

                $category_id_list[] = $value['value'];

                $category_list = CommonController::getCategoryByPid($value['value']);


                if ($category_list) {

                    foreach ($category_list as $k => $v) {

                        $category_id_list[] = $v->id;
                    }

                }

                $list->whereIn('category_id', $category_id_list);

            }

        }

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    /**
     * @throws \JsonException
     */
    function detail()
    {

        $id = request()->input('id');

        $item = StoreArticle::with('category')->where('id', $id)->firstOrFail();


        $expand_data = $item->expand_data;


        if ($expand_data) {


            $real_data = json_decode($expand_data, true, 512, JSON_THROW_ON_ERROR);
            $expand_data = getExpandByCategoryId($item->category_id);


            foreach ($real_data as $key => $value) {


                foreach ($expand_data as $k => $v) {


                    if ($v['name'] === $key) {


                        $expand_data[$k]['value'] = $value;

                    }


                }

            }


        } else {

            $expand_data = getExpandByCategoryId($item->category_id);
        }

        $item->expand_data = $expand_data;

        return Json::code(1, 'success', $item);

    }


    /**
     * @throws \JsonException
     */
    function update()
    {
        $post = request()->post();

        $id = $post['id'] ?? null;

//        $post['expand_data'] = json_encode($post['expand_data'], JSON_THROW_ON_ERROR);

        $temp = [];

        foreach ($post['expand_data'] as $key => $value) {


            $temp[$value['name'] ?? ""] = $value['value'] ?? "";

        }

        $post['expand_data'] = $temp;


        StoreArticle::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');
    }


    function destroy()
    {


        $id = request()->input('id', 0);


        StoreArticle::where('id', $id)->update(['status' => 2]);


        return Json::code(1, 'success');


    }


    /**
     * 发布
     * Create by Peter Yang
     * 2022-08-06 17:46:36
     * @return string
     * @throws \Throwable
     */
    function push()
    {


        $id = request()->input('id', 0);


        $item = StoreArticle::where('id', $id)->first();

        if (!$item) {

            return Json::code(2, '文章未找到');
        }

        try {

            Push::spiderToArticle($item);

        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());
        }


        return Json::code(1, 'success');

    }


    /**
     * 清理所有调试文章(包含图片)
     */
    function removeDebugArticle()
    {


        $bool = File::cleanDirectory(public_path('uploads/debug'));


        StoreArticle::where('debug', 1)->delete();


        return Json::code(1, 'success', $bool);


    }


}
