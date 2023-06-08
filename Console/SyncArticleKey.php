<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Models\CollectTag;

class SyncArticleKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncArticleKey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步文章表的key';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        Article::whereNull("key")->chunkById(1000, function ($items) {


            foreach ($items as $item) {


                $item->key = $item->id;

                $item->save();

                echo $item->title . PHP_EOL;

            }


        });


        return 0;
    }
}
