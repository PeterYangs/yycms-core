<?php

namespace Ycore\Console;

use App\Events\ArticleUpdate;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\StoreApk;
use Ycore\Tool\Expand;
use Ycore\Tool\Seo;
use Illuminate\Console\Command;

class MatchApk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MatchApk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匹配下载包';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $apkCategoryName = [config('category.game')];


        $cids = Category::whereIn('pid', $apkCategoryName)->get()->pluck('id')->all();


        $count = 0;

        StoreApk::chunk(100, function (\Illuminate\Database\Eloquent\Collection $items) use ($cids, &$count) {


            foreach ($items as $item) {


                $article = Article::whereIn('category_id', $cids)->where('title', 'like',
                    "%" . $item->title . "%")->orderBy('id',
                    'desc')->first();


                if ($article) {


                    echo $article->title . PHP_EOL;


                    $table = CategoryController::getExpandTableName($article->category_id);


                    $ex['article_id'] = $article->id;

                    $ex['android'] = $item->url;

                    \DB::table($table)->updateOrInsert(['article_id' => $article->id], $ex);

                    Expand::SyncExpand($article);


                    Seo::setSeoTitle($article->id, true);

                    //静态化
                    event(new ArticleUpdate($article->id));


                    $count++;

                }

            }


        });


        echo "总计" . $count . "个" . PHP_EOL;

        return 0;
    }
}
