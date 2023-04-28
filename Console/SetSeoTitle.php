<?php

namespace Ycore\Console;

use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Tool\Seo;
use Illuminate\Console\Command;

class SetSeoTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetSeoTitle {cid=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重新生成seo标题';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $cid = (int)$this->argument('cid');

//        dd($cid);


        $query = Article::orderBy('id', 'desc');


        if ($cid !== 0) {


            $list = Category::where('pid', $cid)->get()->pluck('id')->all();


            $ids = array_merge([$cid], $list);


            $query->whereIn('category_id', $ids);

        }


        $query->chunkById(100, function ($items) {


            foreach ($items as $item) {


                Seo::setSeoTitle($item->id, true);


                echo $item->title . PHP_EOL;

            }


        });


        return 0;
    }
}
