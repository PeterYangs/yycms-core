<?php

namespace Ycore\Console;

use App\Dao\CategoryPushConfig;
use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
use App\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\StoreArticle;
use App\Tool\Expand;
use App\Tool\Push;
use App\Tool\Seo;
use Illuminate\Console\Command;

class PushRandomStoreToArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PushRandomStoreToArticle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推送采集文章到文章表';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        //暂时写法，后期改后台设置
        $categoryList = [
            new CategoryPushConfig('手游攻略', 1),
//            new CategoryPushConfig('游戏资讯', 1),
            new CategoryPushConfig('游戏', 1),
            new CategoryPushConfig('应用', 1),
            new CategoryPushConfig('软件教程', 1),
            new CategoryPushConfig('游戏资讯', 1),

        ];


        foreach ($categoryList as $item) {


            Push::push($item);


        }


        return 0;
    }


}


