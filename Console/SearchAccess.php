<?php

namespace Ycore\Console;

use Ycore\Models\UserAccess;
use Illuminate\Console\Command;

class SearchAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SearchAccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '搜索引擎来路获取';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        //百度
        $today_baidu = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Baiduspider%")->count();

        $today_baidu_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Baiduspider%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_baidu = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%Baiduspider%")->count();

        $yesterday_baidu_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%Baiduspider%")->limit(20)->orderBy('id', 'desc')->get();


        //谷歌
        $today_google = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Googlebot%")->count();

        $today_google_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Googlebot%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_google = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%Googlebot%")->count();

        $yesterday_google_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%Googlebot%")->limit(20)->orderBy('id', 'desc')->get();


        //360
        $today_360 = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%360Spider%")->count();

        $today_360_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%360Spider%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_360 = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%360Spider%")->count();

        $yesterday_360_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%360Spider%")->limit(20)->orderBy('id', 'desc')->get();


        //今日头条
        $today_byte = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Bytespider%")->count();

        $today_byte_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Bytespider%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_byte = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%Bytespider%")->count();

        $yesterday_byte_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%Bytespider%")->limit(20)->orderBy('id', 'desc')->get();




        //必应
        $today_bi = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%bingbot%")->count();

        $today_bi_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%bingbot%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_bi = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%bingbot%")->count();

        $yesterday_bi_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%bingbot%")->limit(20)->orderBy('id', 'desc')->get();


        //搜狗
        $today_sogou = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Sogou%")->count();

        $today_sogou_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%Sogou%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_sogou = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%Sogou%")->count();

        $yesterday_sogou_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%Sogou%")->limit(20)->orderBy('id', 'desc')->get();




        //神马
        $today_sm = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%YisouSpider%")->count();

        $today_sm_list = UserAccess::whereDate("created_at", \Date::now()->format("Y-m-d"))->where("agent", 'like',
            "%YisouSpider%")->limit(20)->orderBy('id', 'desc')->get();


        $yesterday_sm = UserAccess::whereDate("created_at", \Date::now()->subDay()->format("Y-m-d"))->where("agent",
            'like',
            "%YisouSpider%")->count();

        $yesterday_sm_list = UserAccess::whereDate("created_at",
            \Date::now()->subDay()->format("Y-m-d"))->where("agent", 'like',
            "%YisouSpider%")->limit(20)->orderBy('id', 'desc')->get();




        \Cache::put('search_access', [

            [
                ['name' => '百度', 'num' => $today_baidu, 'list' => $today_baidu_list],
                ['name' => '谷歌', 'num' => $today_google, 'list' => $today_google_list],
                ['name' => '360', 'num' => $today_360, 'list' => $today_360_list],
                ['name' => '头条', 'num' => $today_byte, 'list' => $today_byte_list],
                ['name' => '必应', 'num' => $today_bi, 'list' => $today_bi_list],
                ['name' => '搜狗', 'num' => $today_sogou, 'list' => $today_sogou_list],
                ['name' => '神马', 'num' => $today_sm, 'list' => $today_sm_list],
            ],
            [
                ['name' => '百度', 'num' => $yesterday_baidu, 'list' => $yesterday_baidu_list],
                ['name' => '谷歌', 'num' => $yesterday_google, 'list' => $yesterday_google_list],
                ['name' => '360', 'num' => $yesterday_360, 'list' => $yesterday_360_list],
                ['name' => '头条', 'num' => $yesterday_byte, 'list' => $yesterday_byte_list],
                ['name' => '必应', 'num' => $yesterday_bi, 'list' => $yesterday_bi_list],
                ['name' => '搜狗', 'num' => $yesterday_sogou, 'list' => $yesterday_sogou_list],
                ['name' => '神马', 'num' => $yesterday_sm, 'list' => $yesterday_sm_list],
            ]

        ], now()->addDay());


        return 0;
    }
}
