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
            ->whereRaw("not EXISTS(select *  from article_association_object  WHERE `slave` = article.id)")->orderBy('id', 'desc')->chunk(100, function ($items) {


                foreach ($items as $item) {


                    $category = Category::where('id', $item->category_id)->first();


                    $collect = Collect::whereIn('son_id', [$category->id, $category->parent->id])->first();


                    if ($collect) {


                        $content = $item->content;


                        $t = CollectTag::whereRaw("? like CONCAT('%',title,'%')", [$content])->limit(10)->get();


                        if ($t->count() > 0) {


                            $mainList = Article::where('category_id', $collect->category_id)->where(function ($query) use ($t) {


                                foreach ($t as $v) {

                                    $query->orWhere('title', 'like', '%' . $v->title . '%');
                                }

                            })->limit(4)->get();


                            foreach ($mainList as $main) {

                                ArticleAssociationObject::create([
                                    'main' => $main->id,
                                    'slave' => $item->id
                                ]);


                                if (app()->runningInConsole()) {


                                    echo $item->title . "=>" . $mainList->pluck('title')->join(",") . PHP_EOL;

                                }

                                //文章更新触发的事件
                                event(new ArticleUpdate($item->id));

                            }


                        }


                    }


                }

            });


        return 0;
    }
}
