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


            //判断是否超出推送配额标记
            if ($isOver = \Cache::get("_pc_is_over")) {


                if ($isOver === date("Y-m-d", $this->now)) {


                    \Log::channel('push')->error("百度推送超出当天配额了(文章链接-" . $pcUrl . ")");

                    return;

                } else {

                    //不一致清除超出配额标记
                    \Cache::forget("_pc_is_over");
                }

            }


            try {

                $result = $this->push($pcUrl, $domain, $baidu_token);

                if (str_contains($result, "over quota") || str_contains($result, "site error")) {


                    //设置超出当天配额标记
                    \Cache::put('_pc_is_over', date("Y-m-d", $this->now), 60 * 60 * 24);

                }


                \Ycore\Models\WebsitePush::create(['article_id' => $articleId, 'link' => $pcUrl, 'spider' => 'baidu', 'platform' => 'pc', 'msg' => $result]);

            } catch (\Exception $exception) {


                Log::error("baidu推送报错---" . $exception->getMessage());

            }


        }


        $m_domain = getOption('m_domain', "");


        if ($m_domain) {


            //判断是否超出推送配额标记
            if ($isOver = \Cache::get("_mobile_is_over")) {


                if ($isOver === date("Y-m-d", $this->now)) {


                    \Log::channel('push')->error("百度推送超出当天配额了(文章链接-" . $mobileUrl . ")");

                    return;

                } else {

                    //不一致清除超出配额标记
                    \Cache::forget("_mobile_is_over");
                }

            }


            try {

                $result = $this->push($mobileUrl, $m_domain, $baidu_token);

                if (str_contains($result, "over quota") || str_contains($result, "site error")) {


                    //设置超出当天配额标记
                    \Cache::put('_mobile_is_over', date("Y-m-d", $this->now), 60 * 60 * 24);

                }

                \Ycore\Models\WebsitePush::create(['article_id' => $articleId, 'link' => $mobileUrl, 'spider' => 'baidu', 'platform' => 'mobile', 'msg' => $result]);

            } catch (\Exception $exception) {

                Log::error("baidu推送报错---" . $exception->getMessage());

            }


        }


    }


    function push(string $url, $domain, $token): string
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

            curl_close($ch);

            \Log::channel('push')->error($msg);


            throw new \Exception($msg);


        } else {


            \Log::channel('push')->info($result . "-----" . $url);

            curl_close($ch);

            return $result;

        }


    }

}
