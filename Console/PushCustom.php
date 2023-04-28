<?php

namespace Ycore\Console;

use App\Dao\CategoryPushConfig;
use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\StoreArticle;
use Ycore\Tool\ArticleGenerator;
use Ycore\Tool\Expand;
use Ycore\Tool\Push;
use Ycore\Tool\Seo;
use Illuminate\Console\Command;

class PushCustom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PushCustom {cname} {num}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自定义发布采集文章到正式文章';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $categoryName = $this->argument('cname');

        $num = $this->argument('num');


        $category = getCategoryIds($categoryName);

        $query = StoreArticle::limit($num)->where('debug', 0)->where('status', 1)->orderBy('id',
            'desc');


        $list = $query->whereIn('category_id', $category)->get();


        $ag = new ArticleGenerator();
        foreach ($list as $item) {

            $article = null;

            try {


                if (!$item->title || !$item->img || !$item->content) {


                    if (app()->runningInConsole()) {

                        echo '文章内容缺失！' . "采集id为：" . $item->id . PHP_EOL;
                    }

                    continue;

                }


                $ex = json_decode($item->expand_data, true, 512, JSON_THROW_ON_ERROR);


                $ag->fill([
                    'category_id' => $item->category_id,
                    'push_time' => \Date::now(),
                    'content' => $item->content,
                    'img' => $item->img,
                    'title' => $item->title,
                    'seo_title' => '',
                    'seo_desc' => $item->seo_desc,
                    'seo_keyword' => $item->seo_keyword,
                    'admin_id_create' => 1,
                    'admin_id_update' => 1,
                    'special_id' => $item->special_id,
                ], $ex)->create(false);


                echo $item->title . " 发布成功！" . PHP_EOL;


            } catch (\Exception $exception) {


                if (app()->runningInConsole()) {

                    echo $item->title . ":" . $exception->getMessage() . PHP_EOL;
                }


                \Log::error("推送采集文章失败:" . $item->title . "--" . $exception->getMessage());


                continue;
            } finally {
                //标记为已用
                $item->status = 2;

                $item->save();
            }
        }


        return 0;
    }


}


