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

class CleanCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CleanCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理缓存';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        //清理文章分类查询缓存
        \Cache::tags(['search_category'])->flush();


        return 0;
    }


}


