<?php

namespace Ycore\Console;

use Ycore\Models\StoreArticle;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SpiderTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SpiderTable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '采集统计数据';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $one = StoreArticle::with('category')->whereDate('created_at',
            \Date::today()->format('Y-m-d'))->selectRaw("category_id,count(*) as num")->groupBy('category_id')->get();

        foreach ($one as $item) {

            $item->list = StoreArticle::select(['id','created_at','title'])->where('category_id', $item->category_id)->whereDate('created_at',
                \Date::today()->format('Y-m-d'))->limit(20)->get();
        }

        $two = StoreArticle::with('category')->whereDate('created_at',
            \Date::today()->subDays(1)->format('Y-m-d'))->selectRaw("category_id,count(*) as num")->groupBy('category_id')->get();


        foreach ($two as $item) {

            $item->list = StoreArticle::select(['id','created_at','title'])->where('category_id', $item->category_id)->whereDate('created_at',
                \Date::today()->subDays(1)->format('Y-m-d'))->limit(20)->get();
        }


        $three = StoreArticle::with('category')->whereDate('created_at',
            \Date::today()->subDays(2)->format('Y-m-d'))->selectRaw("category_id,count(*) as num")->groupBy('category_id')->get();


        foreach ($three as $item) {

            $item->list = StoreArticle::select(['id','created_at','title'])->where('category_id', $item->category_id)->whereDate('created_at',
                \Date::today()->subDays(2)->format('Y-m-d'))->limit(20)->get();
        }


        \Cache::put('spider_table', [$one, $two, $three], \Date::now()->addDays());

        return 0;
    }
}
