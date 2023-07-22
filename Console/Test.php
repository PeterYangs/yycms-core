<?php

namespace Ycore\Console;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Database\Schema\Blueprint;
use Ycore\Core\Core;
use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\ArticleExpand;
use Ycore\Service\Upload\Upload;
use Ycore\Tool\ArticleGenerator;
use Illuminate\Console\Command;
use Ycore\Tool\ChatGpt;
use Ycore\Service\Ai\Ai;

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
    public function handle(Upload $upload)
    {

        $json = file_get_contents(base_path('123.json'));

//        dd(json_decode($json,true));

        $json=json_decode($json,true);


        try {

            $client = new Client();

            $rsp = $client->request('post', 'http://dtop.push:8088/dtop/contentUpdate?time=2023-07-14+14%3A27%3A38&echostr=swpzskljrlytcyfz&appid=9d7s1q7Nvy0ACVzA&signature=65bd9f3def704cf8657a301f309f74245e7f5b9a9e34126e3b3a4f9f593bd786', [
//            $rsp = $client->request('post', 'http://106.12.31.47:8088/dtop/contentUpdate?time=2023-07-22 14:11:31&echostr=sjvb3mhfq0k3f6h9gv57p4mxhz60o5vy&appid=sj121q7Nvy0ACVzA&signature=a2317e2b76230601af84689d4111dbd72de2bb2a0ced0aa157fa28bf9804adff', [
//            $rsp = $client->request('post', 'http://www.520apk.com/dtop/dtop/contentUpdate?time=2023-07-14+14%3A27%3A38&echostr=swpzskljrlytcyfz&appid=9d7s1q7Nvy0ACVzA&signature=65bd9f3def704cf8657a301f309f74245e7f5b9a9e34126e3b3a4f9f593bd786', [

                'json' => $json,
                'timeout' => 60

            ]);


            dd($rsp->getBody()->getContents());

        } catch (BadResponseException $exception) {


//            preg_match($exception->getMessage());

            $content = $exception->getResponse()->getBody()->getContents();

            $ch = mb_detect_encoding($content, ["ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5']);

//            ;


            print_r(mb_convert_encoding($content, "UTF-8", $ch));

            file_put_contents(public_path('error.html'),mb_convert_encoding($content, "UTF-8", $ch));


//            print_r($exception->getResponse()->getBody()->getContents());

        }


//        $address = $upload->uploadRemoteFile("https://img.925g.com/upload/cms/20230714/1356/4de750827714b14c49ebcd0fab509475.jpg");
//
//
//        dd($address);


//        dd(Core::GetVersion());


//        dd(resolve(Ai::class));


//        $game=ArticleDetailModel()->where('category_id', 2)->first();
//
////        ;
//
//        dd($ai->article($game));


//        dd($ai->do("请告诉我怎么发财！"));


//        dd(ChatGpt::gameTemplate("王者荣耀"));

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
//        $ag = new ArticleGenerator();
////
////
//        $ag->fill([
//            'title' => '原神官方版啊11',
//            'category_id' => 6,
//            'content' => '<p>这是一个内容休闲益智游戏</p>',
//            'img' => 'test_img/7.png'
//        ], ['size' => "123M"])->create(true, true, true);

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
