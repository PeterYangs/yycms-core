<?php

namespace Ycore\Listeners;

use Illuminate\Support\Facades\Http;
use Ycore\Events\WebsitePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BingPush
{

    protected int $now = 0;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        //获取运行时间
        $this->now = time();
    }

    /**
     * Handle the event.
     *
     * @param \Ycore\Events\WebsitePush $event
     * @return void
     */
    public function handle(WebsitePush $event)
    {
        //


        //调试模式不推送
        if (!(env('APP_DEBUG') === false && env('APP_ENV') === "production")) {


            return;
        }


        $articleId = $event->articleId;

        $article = getArticleById($articleId);

        if (!$article) {

            \Log::channel('push')->error("获取文章信息失败");

            return;
        }

        $pcUrl = getDetailUrlForCli($article);
        $mobileUrl = getDetailUrlForCli($article, 'mobile');


        $bing_token = getOption('bing_token', "");


        if (!$bing_token) {

//            \Log::channel('push')->error("token不能为空");

            return;
        }


        $domain = getOption('domain', "");

        if ($domain) {


            //判断是否超出推送配额标记
            if ($isOver = \Cache::get("_bing_pc_is_over")) {


                if ($isOver === date("Y-m-d", $this->now)) {


                    \Log::channel('push')->error("必应超出当天配额了(文章链接-" . $pcUrl . ")");

                    return;

                } else {

                    //不一致清除超出配额标记
                    \Cache::forget("_bing_pc_is_over");
                }

            }


            try {

                $result = $this->push($pcUrl, $domain, $bing_token);


                if ($result->status() !== 200) {

                    $res = json_decode($result->body(), true, 512, JSON_THROW_ON_ERROR);

                    if ($res['ErrorCode'] === 2) {

                        \Cache::put('_bing_pc_is_over', date("Y-m-d", $this->now), 60 * 60 * 24);
                    }

                }


                \Ycore\Models\WebsitePush::create(['article_id' => $articleId, 'link' => $pcUrl, 'spider' => 'bing', 'platform' => 'pc', 'msg' => $result->body()]);

            } catch (\Exception $exception) {


            }


        }


        $m_domain = getOption('m_domain', "");


        if ($m_domain) {


            //判断是否超出推送配额标记
            if ($isOver = \Cache::get("_bing_mobile_is_over")) {


                if ($isOver === date("Y-m-d", $this->now)) {


                    \Log::channel('push')->error("必应超出当天配额了(文章链接-" . $mobileUrl . ")");

                    return;

                } else {

                    //不一致清除超出配额标记
                    \Cache::forget("_bing_mobile_is_over");
                }

            }


            try {

                $result = $this->push($mobileUrl, $m_domain, $bing_token);


                if ($result->status() !== 200) {

                    $res = json_decode($result->body(), true, 512, JSON_THROW_ON_ERROR);

                    if ($res['ErrorCode'] === 2) {

                        \Cache::put('_bing_mobile_is_over', date("Y-m-d", $this->now), 60 * 60 * 24);
                    }

                }


                \Ycore\Models\WebsitePush::create(['article_id' => $articleId, 'link' => $mobileUrl, 'spider' => 'bing', 'platform' => 'mobile', 'msg' => $result->body()]);

            } catch (\Exception $exception) {


            }


        }


    }


    function push(string $url, $domain, $token)
    {

        $rsp = Http::post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrlbatch?apikey=" . $token, [
            'siteUrl' => rtrim($domain, '/'),
            'urlList' => [
                $url
            ]
        ]);

        return $rsp;


    }

}
