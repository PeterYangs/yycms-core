<?php

namespace Ycore\Console;


use Ycore\Models\Article;
use Ycore\Models\ArticleTag;
use Ycore\Models\Tag;
use Illuminate\Console\Command;


class CleanStaticPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CleanStaticPage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理文章静态页面';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        \File::cleanDirectory(storage_path('static/pc'));
        \File::cleanDirectory(storage_path('static/mobile'));


        return 0;
    }
}
