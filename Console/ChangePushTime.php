<?php

namespace Ycore\Console;


use Ycore\Models\Article;
use Ycore\Models\ArticleTag;
use Ycore\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;


class ChangePushTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangePushTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改文章发布时间，从现在开始往以前的时间推，正常时间在早上9点到晚上8点';


    protected int $count = 0;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $now = Date::now();

        Article::chunkById(100, function ($items) use (&$now) {


            foreach ($items as $item) {


                $now = $now->subSeconds(mt_rand(30 * 60, 60 * 60));

                echo $now . PHP_EOL;

                $item->push_time = $now;

                $item->save();

            }


        });


        return 0;
    }
}
