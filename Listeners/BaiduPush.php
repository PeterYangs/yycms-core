<?php

namespace Ycore\Listeners;

use Ycore\Events\WebsitePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BaiduPush
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

        //判断是否超出推送配额标记
        if ($isOver = \Cache::get("isOver")) {


            if ($isOver === date("Y-m-d", $this->now)) {


                \Log::channel('push')->error("超出当天配额了(文章id-" . $event->articleId . ")");

                return;

            } else {

                //不一致清除超出配额标记
                \Cache::forget("isOver");
            }

        }


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


        $baidu_token = getOption('pc_token', "");


        if (!$baidu_token) {

            \Log::channel('push')->error("token不能为空");

            return;
        }


        $domain = getOption('domain', "");

        if ($domain) {

            $this->push($pcUrl, $domain, $baidu_token);

        }


        $m_domain = getOption('m_domain', "");


        if ($m_domain) {

            $this->push($mobileUrl, $m_domain, $baidu_token);
        }


    }


    function push(string $url, $domain, $token)
    {
        $urls = array(
            $url,
        );

        $api = 'http://data.zz.baidu.com/urls?site=' . $domain . '&token=' . $token;
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {

            $msg = curl_error($ch);

            \Log::channel('push')->error($msg);

        } else {


            if (str_contains($result, "over quota")) {


                //设置超出当天配额标记
                \Cache::put('isOver', date("Y-m-d", $this->now), 60 * 60 * 24);

            }

            \Log::channel('push')->info($result . "-----" . $url);
        }


        curl_close($ch);

    }

}
