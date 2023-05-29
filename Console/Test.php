<?php

namespace Ycore\Console;

use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Tool\ArticleGenerator;
use Illuminate\Console\Command;
use Ycore\Tool\ChatGpt;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '调试';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \JsonException
     */
    public function handle()
    {

//        $is_gpt=1;
//
//
//        dd(!($is_gpt === 0));

//        dd(ChatGpt::do(ChatGpt::gameTemplate("王者荣耀")));

//        dd();


//        event(new WebsitePush(9233));

//        setOption('site_name', '60下载');
//        echo getOption('site_name', 'xx') . PHP_EOL;


//        Article::update();
//
//
//         dd(app()->has('icps'));


//        dd(123);
//
        $ag = new ArticleGenerator();
//
//
        $ag->fill([
            'title' => '原神',
            'category_id' => 6,
            'content' => '<p>这是一个内容休闲益智游戏</p>',
            'img' => 'test_img/7.png'
        ], ['size' => "123M"])->create(true, true, true);

//
//        $ag->fill([
//            'title' => '这是一个标题4',
//            'category_id' => config('category.game'),
//            'content' => '<p>这是一个内容修改</p>',
//            'img' => 'test_img/7.png'
//        ], ['size' => "125M"])->update(['id' => 24481]);


        return 0;
    }
}
