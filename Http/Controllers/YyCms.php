<?php

namespace Ycore\Http\Controllers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Ycore\Models\Article;
use Ycore\Tool\Download;
use Ycore\Tool\Json;

class YyCms extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function __construct()
    {
    }


    /**
     * 文章点击数加一
     */
    function add()
    {

        $id = request()->route('id');

        \DB::table('article')->where('id', $id)->increment('hits');


        return Json::code(1, 'success');

    }


    function download()
    {

        $type = request()->route('type');

        $id = request()->route('id');

        $article = getArticleById($id);

        if (!$article) {

            abort(404);
        }

        $specialEx = $article->special_ex;


        if (!$article) {


            abort(404);

        }

        if ($type === "ios") {


            $url = $specialEx[config('static.ios_download_link')];

            //防止链接为空
            if (!$url && $article->special_id !== 0) {
                return redirect()->away(getOption('domain'));
            }

            if (!$url) {

                abort(404);

            }


            $u = parse_url($url);


            return redirect()->away(Download::dealUrl(($url)), 302,
                [
                    'referer' => ($u['scheme'] ?? "") . "://" . $u['host'] ?? "",
                    'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36"
                ],
            );

        }

        $url = $specialEx[config('static.android_download_link')];

        //防止链接为空
        if (!$url && $article->special_id !== 0) {
            return redirect()->away(getOption('domain'));
        }

        if (!$url) {
            abort(404);
        }

        $u = parse_url($url);

        return redirect()->away(Download::dealUrl(($url)), 302,
            [
                'referer' => ($u['scheme'] ?? "") . "://" . $u['host'] ?? "",
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36"
            ],
        );


    }


    /**
     * 二维码生成
     * Create by Peter Yang
     * 2022-08-20 14:34:53
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    function build($id)
    {


        $item = Article::findOrFail($id);

        $url = getDetailUrl($item);


        $url = str_replace("www.", "m.", $url);


        $result = Builder::create()->data($url)->encoding(new Encoding('UTF-8'))->errorCorrectionLevel(new ErrorCorrectionLevelHigh())->build();


        return response($result->getString(), 200, ['Content-type' => 'image/jpeg']);
    }

}
