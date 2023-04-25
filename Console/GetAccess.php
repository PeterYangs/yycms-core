<?php

namespace Ycore\Console;

use Ycore\Models\Article;
use Ycore\Models\UserAccess;
use Illuminate\Console\Command;

class GetAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetAccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取网站访问数据';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $today = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->count();

        $yesterday = UserAccess::whereDate("created_at", \Date::now()->subDays()->format("Y-m-d"))->count();

        $today_ip = UserAccess::selectRaw('count("ip") as num')->whereDate("created_at",
            \Date::now()->format("Y-m-d"))->groupBy('ip')->get()->count();


        $yesterday_ip = UserAccess::selectRaw('count("ip") as num')->whereDate("created_at",
            \Date::now()->subDays()->format("Y-m-d"))->groupBy('ip')->get()->count();


        $today_article = Article::whereDate("push_time", \Date::now()->format("Y-m-d"))->count();

        $yesterday_article = Article::whereDate("push_time", \Date::now()->subDays()->format("Y-m-d"))->count();


        \Cache::put('access', [
            'today_access' => $today,
            'yesterday_access' => $yesterday,
            'today_ip' => $today_ip,
            'yesterday_ip' => $yesterday_ip,
            'today_article' => $today_article,
            'yesterday_article' => $yesterday_article
        ], \Date::now()->addDay());


        return 0;
    }
}
