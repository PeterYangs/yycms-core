<?php

namespace Ycore\Listeners;

use GuzzleHttp\Client;
use Ycore\Events\WebsitePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class IndexNowPush
{

    protected int $now = 0;

    protected Client $client;

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


        $this->client = new Client(['timeout' => 5]);


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


        try {

            $rps = $this->push($pcUrl);

            \Ycore\Models\WebsitePush::create(['article_id' => $articleId, 'link' => $pcUrl, 'spider' => 'indexnow', 'platform' => 'pc', 'msg' => $rps->getBody()->getContents()]);

        } catch (\Exception $exception) {

            Log::error("Indexnow推送报错---" . $exception->getMessage());

        }


        try {

            $rps = $this->push($mobileUrl);

            \Ycore\Models\WebsitePush::create(['article_id' => $articleId, 'link' => $mobileUrl, 'spider' => 'indexnow', 'platform' => 'mobile', 'msg' => $rps->getBody()->getContents()]);

        } catch (\Exception $exception) {

            Log::error("Indexnow推送报错---" . $exception->getMessage());

        }


    }


    function push($url)
    {


        $u = parse_url($url);

        $key = "7ef0fcb958e24e2f9c54ecabcfdd9cd2";

        return $this->client->post('https://api.indexnow.org/indexnow?url=url-changed&key=' . $key, [
            'json' => [
                'host' => $u['host'] ?? "",
                'key' => $key,
                'keyLocation' => $u['scheme'] . "://" . $u['host'] . "/" . $key . ".txt",
                'urlList' => [
                    $url
                ]
            ]
        ]);

    }

}
