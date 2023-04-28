<?php

namespace Ycore\Http\Controllers\Admin;

use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
use Ycore\Models\Article;
use Ycore\Models\SearchArticle;
use Ycore\Models\StoreArticle;
use Ycore\Tool\Expand;
use Ycore\Tool\Json;
use Ycore\Tool\Search;
use Ycore\Tool\Seo;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use OSS\OssClient;
use QL\QueryList;
use Symfony\Component\Process\Process;

class SearchArticleController extends AuthCheckController
{


    function list()
    {
        $list = SearchArticle::with('category')->orderBy('id', 'desc');

        Search::searchList($list, request()->input('search', '[]'));


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));
    }


    function detail()
    {

        $id = request()->input('id');

        $item = SearchArticle::with('category')->where('id', $id)->firstOrFail();


        return Json::code(1, 'success', $item);

    }


    function run()
    {

        $keyword = urlencode(request()->input('keyword'));

        $type = request()->input('type');


        $category_id = request()->input('category_id');


        $out = "";

        $cmd = "";

        switch ($type) {

            case "今日头条":

                $cmd = './script/toutiao -k ' . $keyword . " -c " . $category_id;

                $process = Process::fromShellCommandline($cmd);

                $process->setWorkingDirectory(base_path());


                $process->run();

                $out = $process->getOutput();


                break;


        }


        return Json::code(1, $cmd, $out);

    }


    function update()
    {
        $post = request()->post();

        $id = $post['id'] ?? null;

        SearchArticle::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');
    }


    function updateAndPush()
    {


        $post = request()->post();


//        dd($post);

        $id = $post['id'] ?? null;

        $searchArticle = SearchArticle::updateOrCreate(['id' => $id], $post);


        try {

            $this->push($searchArticle, $post['isMsk']);

        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());
        }


        return Json::code(1, 'success');

    }


    protected function push(SearchArticle $searchArticle, string $isMsk)
    {


        $img = "";


        $article = null;

        try {


            $html = QueryList::html($searchArticle->content);


            $imgIndex = 0;

            $html->find('img')->map(function (\QL\Dom\Elements $elements) use (&$imgIndex, &$img, $isMsk) {


                $url = $elements->attr('src');


                $fileName = "";

                //本地域名
                if (preg_match("/^\/api\/uploads/", $url)) {


                    $fileName = str_replace("/api/uploads/", "", $url);

                } else {


                    $process = Process::fromShellCommandline('./script/image-deal "' . $url . '" ' . $isMsk);

                    $process->setWorkingDirectory(base_path());


                    try {

                        $process->mustRun();

                        $fileName = $process->getOutput();


                    } catch (\Exception $exception) {

                        \Log::error($exception->getMessage());


                        throw new \Exception($exception->getMessage());

                    }


                }


                if ($imgIndex === 0) {


                    $img = $fileName;

                }


                $elements->attr('src', "/api/uploads/" . $fileName);


                $imgIndex++;

            });


            if (!$img) {

                throw new \Exception("未找到图片");

            }


            \DB::beginTransaction();

            $article = Article::create([
                'category_id' => $searchArticle->category_id,
                'push_time' => \Date::now(),
                'content' => $html->getHtml(),
                'img' => $img,
                'title' => $searchArticle->title,
                'seo_title' => '',
                'seo_desc' => $searchArticle->seo_desc,
                'seo_keyword' => $searchArticle->seo_keyword,
                'admin_id_create' => 1,
                'admin_id_update' => 1

            ]);


            $table = CategoryController::getExpandTableName($article->category_id);

            $ex['article_id'] = $article->id;

            \DB::table($table)->insert($ex);

            Expand::SyncExpand($article);


            Seo::setSeoTitle($article->id);

            $searchArticle->delete();

            \DB::commit();

        } catch (\Exception $exception) {

            \DB::rollBack();

//            echo $item->title . ":" . $exception->getMessage() . PHP_EOL;

            \Log::error("推送采集文章失败:" . $searchArticle->title . "--" . $exception->getMessage());


            throw new \Exception($exception->getMessage());


//            continue;

//            return Json::code(2, "推送采集文章失败:" . $article->title . "--" . $exception->getMessage());

        } finally {


//            $searchArticle->delete();

            //标记为已用
//            $item->status = 2;
//
//            $item->save();
        }


        if ($article !== null && env('APP_DEBUG') === false) {

            //推送到站长
            event(new WebsitePush($article->id));

            //静态化
            event(new ArticleUpdate($article->id));


        }

    }

    function destroy()
    {

        $id = request()->input('id', 0);


        SearchArticle::where('id', $id)->delete();


        return Json::code(1, 'success');
    }


}
