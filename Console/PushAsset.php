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

class PushAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PushAsset {theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发布当前主题的静态文件';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $theme = $this->argument('theme');


//        dd($theme);

//        \Storage::disk()


        return 0;
    }
}
