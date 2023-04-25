<?php

namespace Ycore\Console;

use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
use Ycore\Models\Article;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TimingArticlePush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TimingArticlePush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定时文章发布';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $articles = Article::whereNull('deleted_at')
            ->where('push_status', 2)
            ->where('push_time', "<", \Date::now())
            ->withoutGlobalScopes()->get();


        foreach ($articles as $article) {


            $article->push_status = 1;


            $article->save();

            //文章更新触发的事件
            event(new ArticleUpdate($article->id));

            //站长推送
            event(new WebsitePush($article->id));

            echo $article->title . "----" . "发布成功---" . date("Y-m-d H:i:s") . PHP_EOL;


        }


        return 0;
    }
}
