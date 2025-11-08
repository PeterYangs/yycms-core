<?php

namespace Ycore\Console;

use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\StoreArticle;
use Ycore\Tool\Expand;
use Ycore\Tool\Push;
use Ycore\Tool\Seo;
use Illuminate\Console\Command;

class SetOption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetOption {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置属性';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $key = $this->argument('key');

        $value = $this->argument('value');


        setOption($key, $value, true);


        return 0;
    }


}


