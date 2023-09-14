<?php

namespace Ycore\Console;


use Ycore\Models\Article;
use Ycore\Models\ArticleTag;
use Ycore\Models\Tag;
use Illuminate\Console\Command;


class FindTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FindTag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文章标记标签';


    protected int $count = 0;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        Article::with('category')->orderBy('id','desc')->chunk(1000, function ($items) {


            foreach ($items as $item) {


                selectArticleTag($item);

                $this->count++;

                $this->info($item->title);

            }


        });

        \Cache::forget('tag_list');


        echo "总计" . $this->count . PHP_EOL;

        return 0;
    }
}
