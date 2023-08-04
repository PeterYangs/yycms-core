<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Models\Article;
use Ycore\Models\SearchArticle;
use Ycore\Models\StoreArticle;
use Ycore\Tool\ArticleGenerator;
use Ycore\Tool\Cmd;
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


                $cmd = Cmd::getCommandlineByName("goScript") . " toutiao --k " . $keyword . " --c " . $category_id;


                try {

                    $out = Cmd::commandline($cmd, 60 * 3);

                } catch (\Exception $exception) {


                    return Json::code(2, $exception->getMessage(), $cmd);
                }


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


        $id = $post['id'] ?? null;

        $searchArticle = SearchArticle::updateOrCreate(['id' => $id], $post);


        try {

            $this->push($searchArticle, $post['isMsk']);

        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());
        }


        return Json::code(1, 'success');

    }


    /**
     * 发布到正式文章
     * @param SearchArticle $searchArticle
     * @param string $isMsk
     * @return void
     * @throws \Throwable
     */
    protected function push(SearchArticle $searchArticle, string $isMsk)
    {


        $img = "";


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


                    $fileName = Cmd::commandline(Cmd::getCommandlineByName("goScript") . " image-deal \"" . $url . '" ' . $isMsk);


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

            $ag = new ArticleGenerator();

            $ag->fill([
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
            ], []);

            $ag->create();


        } catch (\Exception $exception) {


            \Log::error("推送采集文章失败:" . $searchArticle->title . "=》" . $exception->getMessage());


            throw new \Exception($exception->getMessage());


        }


    }

    function destroy()
    {

        $id = request()->input('id', 0);


        SearchArticle::where('id', $id)->delete();


        return Json::code(1, 'success');
    }


}
