<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\Collect;

class AutoAssociationObject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoAssociationObject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动设置一对多关系';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $collects = Collect::get();


        if ($collects->count() <= 0) {

            return 0;
        }

        $cid = $collects->pluck('son_id')->all();


        $category = getCategory($cid, 100);


        if ($category->count() <= 0) {


            return 0;

        }

        Article::whereIn('category_id', $category->pluck('id')->all())
            ->whereRaw("not EXISTS(select *  from article_association_object  WHERE `slave` = article.id)")->orderBy('id', 'desc')->chunkById(100, function ($items) {

                foreach ($items as $item) {

                    $ok = autoAssociationObject($item);

                    if ($ok) {
                        //更新静态文件
                        event(new ArticleUpdate($item->id));
                    }


                }

            });


        return 0;
    }
}
