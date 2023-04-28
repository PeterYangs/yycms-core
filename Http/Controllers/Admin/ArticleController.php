<?php

namespace Ycore\Http\Controllers\Admin;

use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
use App\Jobs\EmailJob;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\ArticleExpand;
use Ycore\Models\Category;
use Ycore\Models\Special;
use Ycore\Tool\ArticleGenerator;
use Ycore\Tool\Expand;
use Ycore\Tool\Json;
use Ycore\Tool\Seo;
use Illuminate\Contracts\Foundation\ExceptionRenderer;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Date;
use function PHPUnit\Framework\throwException;

class ArticleController extends AuthCheckController
{


    function update()
    {

        $post = request()->input();

        $id = $post['id'] ?? 0;


        $now = Date::now();

        try {

            $ag = new ArticleGenerator();

            if ($id) {


                $ag->fill($post, dealExpandToTable($post['expand']))->update(['id' => $id]);

            } else {

//                dd(dealExpandToTable($post['expand']));

                $ag->fill($post, dealExpandToTable($post['expand']))->create();

            }


            return Json::code(1, 'success');


        } catch (\Exception $exception) {

            \DB::rollBack();


            $view = app(ExceptionRenderer::class)->render($exception);


            //发送邮件
            if (env('APP_ENV') === "production") {


                dispatch(new EmailJob(["904801074@qq.com"], env('APP_NAME') . "-报错日志-" . date("Y-m-d-H-i-s"),
                    $exception->getTraceAsString(), 'error.html', $view));

            }


            return Json::code(2, $exception->getMessage(), $exception->getTrace());

        }


    }


    function detail()
    {


        $id = request()->input('id');


        $item = Article::with('category')
            ->whereNull('deleted_at')
            ->withoutGlobalScopes()
            ->with('article_tag')
            ->findOrFail($id);


        $item->append('has_collect');


        $expand = $item->expand;


        //将拓展表中字段写入expand(防止新增字段后，编辑页面不显示新增字段的表单)
        $articleExpand = getExpandByCategoryId($item->category_id);


        foreach ($articleExpand ?: [] as $key => $value) {


            foreach ($expand as $k => $v) {


                if ($value->id == $v['id']) {


                    $articleExpand[$key]->value = $v['value'];

                }

            }


        }


        $item->expand = $articleExpand;


        return Json::code(1, 'success', $item);

    }


    function delete()
    {

        $id = (int)request()->input('id');


        Article::destroy($id);


        return Json::code(1, 'success');


    }


    /**
     * 主页静态化
     * Create by Peter Yang
     * 2022-08-09 15:20:44
     * @return string
     */
    function homeStatic()
    {


        \Artisan::call('HomeStatic');

        return Json::code(1, 'success');

    }


    /**
     * 回收站文章恢复
     * Create by Peter Yang
     * 2022-08-09 15:12:24
     */
    function recover()
    {

        $id = request()->input('id');

        Article::withTrashed()->where('id', $id)->restore();


        event(new ArticleUpdate($id));

        return Json::code(1, 'success');
    }


    /**
     * 文章下架
     * Create by Peter Yang
     * 2023-01-16 17:18:43
     */
    function down()
    {
        $id = request()->input('id');


        $item = Article::where('id', $id)->firstOrFail();


        $item->status = 2;

        $item->save();

        return Json::code(1, 'success');


    }


    /**
     * 文章上架
     * Create by Peter Yang
     * 2023-01-16 17:18:43
     */
    function up()
    {
        $id = request()->input('id');


//        return Json::code(1, 'success');

        $item = Article::where('id', $id)->whereNull('deleted_at')
            ->withoutGlobalScopes()->firstOrFail();


        $item->status = 1;

        $item->save();

        return Json::code(1, 'success');


    }


    /**
     * 回收站列表
     * Create by Peter Yang
     * 2022-08-09 15:12:02
     * @return string
     */
    function article_recover()
    {

        $list = Article::onlyTrashed()->with('category')->with('admin_id_create')->with('admin_id_update')->orderBy('deleted_at',
            'desc');

        $custom = \Ycore\Tool\Search::searchList($list, request()->input('search', '[]'));


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


    function list()
    {


        $list = Article::with('category.category_route')
            ->with('special')
            ->with('admin_id_create')
            ->with('admin_id_update')
            ->whereNull('deleted_at')
            ->withoutGlobalScopes()
            ->orderBy('id', 'desc');


        $custom = \Ycore\Tool\Search::searchList($list, request()->input('search', '[]'));


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


            if ($value['dec'] === "file") {


                if ($value['value'] === "1") {
                    //关联存在的查询结果
                    $list->has("expand_file");

                } else {
                    if ($value['value'] === "2") {
                        //关联不存在的查询结果
                        $list->doesntHave("expand_file");

                    }
                }

            }


            if ($value['dec'] === "collect_num") {


                if ($value['value'] === '0') {

                    $list->whereRaw("not EXISTS(select *  from article_association_object  WHERE `slave` = article.id)");


                } else {


                    $list->whereRaw("EXISTS(select count(1) as num from article_association_object  WHERE `slave` = article.id  GROUP BY article_association_object.`slave` HAVING  count(1) = " . $value['value'] . "  )");

                }


            }


        }


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function listForAlert()
    {


        $post = request()->input();

        $category_id = $post['search']['category_id'];

        $name = $post['search']['name'];


        $list = Article::with('category')->where('status', 1)->where('push_status',
            1)->select([
            'id',
            'created_at',
            'updated_at',
            'category_id',
            'push_time',
            'deleted_at',
            'img',
            'title',
            'seo_title',
            'admin_id_create',
            'admin_id_update'
        ])->orderBy('id', 'desc');


        $category_id_list = [];

        if ($category_id) {

            $category_id_list = [];

            $category_id_list[] = $category_id;

            $category_list = CommonController::getCategoryByPid($category_id);


            if ($category_list) {

                foreach ($category_list as $key => $value) {

                    $category_id_list[] = $value->id;
                }

            }

            $list->whereIn('category_id', $category_id_list);


        }

        if ($name) {

            $list->where('title', 'like', '%' . $name . '%');

        }


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    /**
     * 特殊属性列表
     * Create by Peter Yang
     * 2022-07-21 17:38:24
     * @return string
     */
    function specialList()
    {


        return Json::code(1, 'success', Special::all());
    }


    /**
     * 查找新闻对应的游戏列表
     * Create by Peter Yang
     * 2022-08-03 11:03:04
     * @return string
     */
    function findGame()
    {


        $newsName = "资讯";

        $pid = Category::where('name', $newsName)->first()->id;

        $table_name = CategoryController::getExpandTableName($pid);


//        \DB::connection()->enableQueryLog();

        $list = Article::with('category')->whereHas('category', function ($query) use ($pid) {

            $query->where('pid', $pid);

        })->whereRaw("EXISTS(select * from " . $table_name . " where article.id = " . $table_name . ".article_id and ( " . config('static.news_game_field') . "= 0 or " . config('static.news_game_field') . " is null ) )")->orderBy('select_order',
            'asc')->orderBy('id',
            'desc');


        $custom = \Ycore\Tool\Search::searchList($list, request()->input('search', '[]'));

//        dd($custom);

        foreach ($custom as $key => $value) {


            if ($value['dec'] === 'match') {


                if ($value['value'] === "1") {

                    $gameIds = Category::whereIn('pid', [config('category.game'), config('category.app')])->get()->pluck('id')->all();

//                    $list->whereRaw("EXISTS( select * from article as game  where  category_id in (".implode(",",$gameIds).") and  article.title like CONCAT('%',game.title,'%') )");


//                    $list->whereRaw("EXISTS( select * from article_tag where tag_id in (select tag_id from article_tag left join article as game2 on game2.id = article_tag.article_id  where article_id = article.id  and  category_id in (".implode(",",$gameIds).")  )   )");
//                    $list->whereRaw("EXISTS( select * from article_tag where tag_id in (select tag_id from article_tag   where article_id = article.id    )   and EXISTS( select * from article as game2 where  article.id = article_tag.article_id and game2.category_id in (".implode(",",$gameIds).")  )   )");
                    $list->whereRaw("(  EXISTS( select * from article as game  where  category_id in (" . implode(",",
                            $gameIds) . ") and  article.title like CONCAT('%',game.title,'%') ) or   EXISTS( select * from article as game2 where  game2.category_id in (" . implode(",",
                            $gameIds) . ")  and EXISTS(select * from article_tag where game2.id = article_tag.article_id and tag_id in (select tag_id from article_tag   where article_id = article.id) )  ) )");

                }

//                dd($value['value']);


            }


        }


        //->whereRaw("EXISTS( select * from article as game  where  category_id in (".implode(",",$gameIds).") and  article.title like CONCAT('%',game.title,'%') )")

//        $logs = \DB::getQueryLog();


        return Json::code(1, "success", paginate($list, request()->input('p', 1)));


    }


    /**
     * 匹配上的游戏列表
     * Create by Peter Yang
     * 2022-08-03 14:03:54
     * @return string
     */
    function matchGame()
    {


        $matchName = [config('category.game'), config('category.app')];

        $pid = Category::whereIn('id', $matchName)->get();

        $newsId = request()->input('id');

        $item = getArticleById($newsId);

        $article_tag = $item->article_tag->pluck('tag_id')->all();

        $tag_condition = "";
        if (count($article_tag) > 0) {

            $tag_condition = "or EXISTS (select * from article_tag where article.id = article_tag.article_id and tag_id in (" . implode(",",
                    $article_tag) . ") )";
        }

        if (!$item) {


            return Json::code(1, 'success', []);
        }

        $list = Article::with('category')->whereHas('category', function ($query) use ($pid) {

            $query->whereIn('pid', $pid->pluck('id')->all());

        })->whereRaw("( ? like CONCAT('%',title,'%')  $tag_condition   )", [$item->title])->limit(8)->get();


        return Json::code(1, 'success', ['article' => $item, 'list' => $list]);

    }

    /**
     * 设置新闻关联游戏
     * Create by Peter Yang
     * 2022-08-03 14:21:01
     */
    function setNewsMatchGame()
    {

        $newsId = request()->input('news_id');

        $gameId = request()->input('game_id');

        $newsName = "资讯";

        try {

            \DB::beginTransaction();

            $pid = Category::where('name', $newsName)->first()->id;

            $table_name = CategoryController::getExpandTableName($pid);


            \DB::table($table_name)->where('article_id', $newsId)->update([
                config('static.news_game_field') => $gameId,
            ]);


            Expand::SyncExpand(getArticleById($newsId));


            \DB::commit();


        } catch (\Exception $exception) {

            \DB::rollBack();

            return Json::code(2, $exception->getMessage());


        }

        event(new ArticleUpdate($newsId));

        return Json::code(1, 'success');


    }


    /**
     * 延后匹配包的排序
     * Create by Peter Yang
     * 2022-08-03 14:56:14
     */
    function delayOrder()
    {


        $id = request()->input('id');

        Article::where('id', $id)->increment('select_order');


        return Json::code(1, 'success');


    }


    function removeArticleAssociationObject()
    {

        $main = request()->input('main');

        $slave = request()->input('slave');

        ArticleAssociationObject::where('main', $main)->where('slave', $slave)->delete();


        return Json::code(1, 'success');

    }


}
