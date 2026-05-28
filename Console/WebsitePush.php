<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Ycore\Listeners\BaiduPush;
use Ycore\Listeners\BingPush;
use Ycore\Listeners\IndexNowPush;

class WebsitePush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WebsitePush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '站点推送到站长';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ((int)getOption('enable_website_push_schedule', 0) !== 1) {
            return 0;
        }

        $spiderType = ['baidu', 'bing', 'indexnow'];


        foreach ($spiderType as $type) {


            $res = \DB::select("SELECT a.id
FROM article a
WHERE a.status = 1
  AND a.push_status = 1
  AND a.deleted_at IS NULL
  AND NOT EXISTS (
      SELECT 1
      FROM website_push wp
      WHERE wp.article_id = a.id
        AND wp.spider = ?
  )
ORDER BY a.id ASC
LIMIT 1;", [$type]);


            if ($res) {

                switch ($type) {

                    case 'baidu':

                        app(BaiduPush::class)->handle(new \Ycore\Events\WebsitePush($res[0]->id));

                        break;

                    case 'bing':

                        app(BingPush::class)->handle(new \Ycore\Events\WebsitePush($res[0]->id));

                        break;

                    case 'indexnow':

                        app(IndexNowPush::class)->handle(new \Ycore\Events\WebsitePush($res[0]->id));

                        break;

                }

            }


        }


        return 0;
    }
}
