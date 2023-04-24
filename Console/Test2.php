<?php

namespace Ycore\Console;


use App\Events\WebsitePush;
use App\Http\Controllers\Admin\CategoryController;
use App\Models\Article;
use App\Tool\ArticleGenerator;
use Illuminate\Console\Command;

class Test2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test2';

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



        dd("核心调用");

//        event(new WebsitePush(9233));

//        setOption('site_name', '60下载');
//        echo getOption('site_name', 'xx') . PHP_EOL;


//        Article::update();
//
//
//         dd(app()->has('icps'));

//
//        $ag = new ArticleGenerator();
//
//
////        $ag->fill([
////            'title' => '这是一个标题2',
////            'category_id' => config('category.game'),
////            'content' => '<p>这是一个内容</p>',
////            'img' => 'test_img/7.png'
////        ], ['size' => "123M"])->create();
//
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
