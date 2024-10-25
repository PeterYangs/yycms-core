<?php

namespace Ycore\Console;

use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Models\Article;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Tool\ArticleGenerator;

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


            $ad = new ArticleGenerator();

            $ad->fill(['push_status' => 1], [])->update(['id' => $article->id], true);


//
            echo $article->title . "----" . "发布成功---" . date("Y-m-d H:i:s") . PHP_EOL;


        }


        return 0;
    }
}
