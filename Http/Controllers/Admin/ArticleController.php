<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Jobs\AiToArticle;
use Ycore\Jobs\EmailJob;
use Ycore\Jobs\TxtToArticle;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\ArticleDownload;
use Ycore\Models\ArticleExpand;
use Ycore\Models\Category;
use Ycore\Models\Special;
use Ycore\Scope\ArticleScope;
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


        try {

            $ag = new ArticleGenerator();

            $article = null;

            if ($id) {


                $article = $ag->fill($post, dealExpandToTable($post['expand']))->update(['id' => $id]);

            } else {


                $article = $ag->fill($post, dealExpandToTable($post['expand']))->create();

            }

            if ($post['article_download']['download_site_id']) {
                //下载模块写入
                ArticleDownload::updateOrCreate(['article_id' => $article->id], [
                    'article_id' => $article->id,
                    'download_site_id' => $post['article_download']['download_site_id'],
                    'file_path' => $post['article_download']['file_path'],
                    'save_type' => $post['article_download']['save_type'],
                    'pan_password' => $post['article_download']['pan_password']
                ]);
            }

            return Json::code(1, 'success');


        } catch (\Exception $exception) {


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
            ->with('article_download.download_site')
            ->findOrFail($id);

        $item->append('has_collect');

//        $item->append('download_url');

        $expand = $item->expand;

        //将拓展表中字段写入expand(防止新增字段后，编辑页面不显示新增字段的表单)
        $articleExpand = getExpandByCategoryId($item->category_id);

        foreach (($articleExpand ?: []) as $key => $value) {

            foreach ($expand as $k => $v) {

                if ($value->name === $v['name']) {

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


        $article = Article::withoutGlobalScopes()->where('id', $id)->first();

        if (!$article) {

            return Json::code(2, $id . "不存在!");
        }

        $article->delete();

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

        $list = Article::onlyTrashed()->withoutGlobalScope(ArticleScope::class)->with('category')->with('admin_id_create')->with('admin_id_update')->orderBy('deleted_at',
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

        //草稿箱
        if (request()->get('is_draft') === "1"){

            $list->where('push_status',3);
        }


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
            'issue_time',
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

//        $table_name = CategoryController::getExpandTableName($pid);

//        dd("EXISTS(select * from  `expand_data` where article.id = `expand_data`.`article_id` and  `name` = '".config('static.news_game_field')."' (  value = 0 or  value is null  or value = '' ) )");

        $list = Article::with('category')->whereHas('category', function ($query) use ($pid) {

            $query->where('pid', $pid);

            //筛选没关联上的文章
        })->whereRaw("EXISTS(select * from  `expand_data` where article.id = `expand_data`.`article_id` and  `name` = '" . config('static.news_game_field') . "' and (  value = 0 or  value is null  or value = '' ) )")->orderBy('select_order',
            'asc')->orderBy('id',
            'desc');


        $custom = \Ycore\Tool\Search::searchList($list, request()->input('search', '[]'));


        foreach ($custom as $key => $value) {


            if ($value['dec'] === 'match') {


                if ($value['value'] === "1") {

                    $gameIds = Category::whereIn('pid', [config('category.game'), config('category.app')])->get()->pluck('id')->all();

//                    $list->whereRaw("EXISTS( select * from article as game  where  category_id in (".implode(",",$gameIds).") and  article.title like CONCAT('%',game.title,'%') )");


//                    $list->whereRaw("EXISTS( select * from article_tag where tag_id in (select tag_id from article_tag left join article as game2 on game2.id = article_tag.article_id  where article_id = article.id  and  category_id in (".implode(",",$gameIds).")  )   )");
//                    $list->whereRaw("EXISTS( select * from article_tag where tag_id in (select tag_id from article_tag   where article_id = article.id    )   and EXISTS( select * from article as game2 where  article.id = article_tag.article_id and game2.category_id in (".implode(",",$gameIds).")  )   )");


//                    $list->whereRaw("(  EXISTS( select * from article as game  where  category_id in (" . implode(",",
//                            $gameIds) . ")  and find_in_set(`game`.title,`article`.`title`)   ))");


                    $list->whereRaw("(  EXISTS( select * from article as game  where  category_id in (" . implode(",",
                            $gameIds) . ") and  article.title like CONCAT('%',game.title,'%') ) or   EXISTS( select * from article as game2 where  game2.category_id in (" . implode(",",
                            $gameIds) . ")  and EXISTS(select * from article_tag where game2.id = article_tag.article_id and tag_id in (select tag_id from article_tag   where article_id = article.id) )  ) ) ");

                }


            }


        }


        //->whereRaw("EXISTS( select * from article as game  where  category_id in (".implode(",",$gameIds).") and  article.title like CONCAT('%',game.title,'%') )")


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

        })->whereRaw("( ? like CONCAT('%',title,'%')  $tag_condition   )", [$item->title])->orderByRaw("LENGTH(title) asc ")->limit(8)->get();


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


        try {

            $ag = new ArticleGenerator();

            $ag->fill([], [config('static.news_game_field') => $gameId]);

            $ag->update(['id' => $newsId]);


        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());


        }


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


    /**
     * 清理所有静态页面
     * @return string
     */
    function CleanStaticPage()
    {


        \Artisan::call("CleanStaticPage");


        return Json::code(1, 'success');

    }


    /**
     * @return string
     */
    function batchImportByTxt()
    {

        $post = request()->post();


        if (!\Storage::disk('upload')->fileExists($post['file_path'])) {

            return Json::code(2, '文件：' . $post['file_path'] . "不存在！");
        }

        $handle = fopen(\Storage::disk('upload')->path($post['file_path']), "r");

        if (!$handle) {
            return Json::code(2, "文件打开失败!");
        }

        $txt_list = [];

        try {

            while (false !== ($char = fgets($handle, 1024))) {


                dispatch(new AiToArticle(trim(str_replace(["\r", "\n"], '', $char)), [$post['cmd']], [$post['img']], $post['category_id'], $post['push_status'], $post['special_id']));
            }

        } catch (\Exception $exception) {

            return Json::code(2, $exception->getMessage());

        } finally {
            fclose($handle);
        }


        return Json::code(1, $txt_list, $post);

    }


    /**
     * @return string
     */
    function batchImportByZip()
    {

        $post = request()->post();

        $category_id = $post['category_id'];

        $tempDir = \Ramsey\Uuid\Uuid::uuid4()->toString() . "-temp-article";

        $path = $post['file_path'];

        $category = Category::where('id', $category_id)->first();

        $push_status = $post['push_status'];

        $img = $post['img'];

        if (!$category) {

            return Json::code(2, "cid" . " " . $category_id . ",不存在！");
        }

        $ok = $this->unzip_file(public_path('uploads/' . $path), storage_path('app/public/' . $tempDir));


        if (!$ok) {

            Json::code(2, "压缩文件解压失败");
        }

        $fileList = \File::allFiles(storage_path('app/public/' . $tempDir));


        foreach ($fileList as $fileInfo) {


            dispatch(new TxtToArticle($fileInfo->getExtension(), $fileInfo->getContents(), $category_id, $fileInfo->getBasename('.txt'), $push_status, 1, $img));

        }


        return Json::code(1, 'success');


    }


    function unzip_file(string $zipName, string $dest)
    {
        //检测要解压压缩包是否存在
        if (!is_file($zipName)) {
            return false;
        }
        //检测目标路径是否存在
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        $zip = new \ZipArchive();
        if ($zip->open($zipName)) {
            $zip->extractTo($dest);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }


}
