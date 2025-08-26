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

class HttpToHttps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HttpToHttps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改网站domain协议从http改为https';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $domain = getOption('domain');
        $m_domain = getOption('m_domain');

        setOption('domain', str_replace("http://", "https://", $domain), true);
        setOption('m_domain', str_replace("http://", "https://", $m_domain), true);

        return 0;
    }


}


