<?php

namespace Ycore\Console;

use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use QL\QueryList;
use Symfony\Component\Mailer\Transport;
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
use Ycore\Tool\Sitemap;

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


//        Transport::fromDsn();

//        Mai

        \Ycore\Tool\Mail::send(['904801074@qq.com'],'测试标题',"测试内容",'随便爽爽爽',"123.txt");


        return;

        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = "smtp.qq.com";
        $mail->SMTPAuth = true;
        $mail->Username = "904801074@qq.com";
        $mail->Password = 'fangdlbaosembfif';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = '465';

        //Recipients
        $mail->setFrom('904801074@qq.com', '发送者');
        $mail->addAddress('1259343832@qq.com', '接受者');     //Add a recipient
//        $mail->addAddress('ellen@example.com');               //Name is optional
//        $mail->addReplyTo('info@example.com', 'Information');
//        $mail->addCC('cc@example.com');
//        $mail->addBCC('bcc@example.com');


        $mail->addStringAttachment(file_get_contents(base_path('123.xml')), "123.xml");

        $mail->isHTML(true);


        $mail->Subject = 'title';

        $mail->Body = '<h1>content</h1>';

        $mail->send();


//        Mail::set

//        $mailer=new Mailer();


//        $mailer= new Mailer();


//        $mail= new PHPMail
//
//
//        $mail


//        $transport = \Swift_SmtpTransport::newInstance('smtp.exmail.qq.com', 465, 'ssl');


//        PHPMai


//        Swift

//        ;


//        Mail::


        return;

//        dd(Sitemap::getSitemapList());


        $a = new ArticleGenerator();

        $a->fill([
            'category_id' => 2,
            'content' => "角色扮演内容啊",
            'title' => '随便一个标题' . time(),
            'img' => '123'

        ], [])->create();


        return;


//        ;

        //df79b569a14b40ba9991830db9f33673


//        $list = [
//            'https://www.522sy.com/game/',
//            'https://www.522sy.com/game01/',
//            'https://www.522sy.com/app/',
//            'https://www.522sy.com/app01/',
//            'https://www.522sy.com/game02/',
//            'https://www.522sy.com/game03/',
//            'https://www.522sy.com/game04/',
//            'https://www.522sy.com/game05/',
//            'https://www.522sy.com/game06/',
//            'https://www.522sy.com/game07/',
//            'https://www.522sy.com/game08/',
//            'https://www.522sy.com/game09/',
//            'https://www.522sy.com/app02/',
//            'https://www.522sy.com/app03/',
//            'https://www.522sy.com/app04/',
//            'https://www.522sy.com/app05/',
//            'https://www.522sy.com/app06/',
//            'https://www.522sy.com/app07/',
//            'https://www.522sy.com/zixun/',
//            'https://www.522sy.com/gl/',
//            'https://www.522sy.com/zx/',
//            'https://www.522sy.com/phb/',
//            'https://www.522sy.com/gc/',
//            'https://www.522sy.com/game10/',
//            'https://www.522sy.com/app08/',
//            'https://www.522sy.com/app09/',
//            'https://www.522sy.com/game11/',
//            'https://www.522sy.com/app10/',
//            'https://www.522sy.com/pc/',
//            'https://www.522sy.com/jc/',
//            'https://www.522sy.com/game02/11.html',
//            'https://www.522sy.com/game05/12.html',
//            'https://www.522sy.com/game01/13.html',
//            'https://www.522sy.com/game06/14.html',
//            'https://www.522sy.com/game03/16.html',
//            'https://www.522sy.com/game02/17.html',
//            'https://www.522sy.com/game05/19.html',
//            'https://www.522sy.com/game01/20.html',
//            'https://www.522sy.com/game05/21.html',
//            'https://www.522sy.com/game10/22.html',
//            'https://www.522sy.com/game05/23.html',
//            'https://www.522sy.com/game06/24.html',
//            'https://www.522sy.com/game01/25.html',
//            'https://www.522sy.com/phb/33.html',
//            'https://www.522sy.com/app07/34.html',
//            'https://www.522sy.com/app07/35.html',
//            'https://www.522sy.com/app01/36.html',
//            'https://www.522sy.com/app07/37.html',
//            'https://www.522sy.com/app07/38.html',
//            'https://www.522sy.com/game01/39.html',
//            'https://www.522sy.com/game01/40.html',
//            'https://www.522sy.com/game05/41.html',
//
//        ];

        $data = file_get_contents(base_path('33.txt'));


        $list = array_filter(explode("\n", $data));

//        dd($list);


        $client = new Client();


        foreach ($list as $value) {

//            $rsp = $client->request('post', 'https://ssl.bing.com/webmaster/api.svc/json/SubmitUrlbatch?apikey=df79b569a14b40ba9991830db9f33673', [
//                'json' => [
//                    'siteUrl' => 'https://www.522sy.com',
//                    'urlList' => [
//                        $value
//                    ]
//                ]
//            ]);

            $rsp = Http::post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrlbatch?apikey=df79b569a14b40ba9991830db9f33673", [
                'siteUrl' => 'https://www.522sy.com',
                'urlList' => [
                    $value
                ]
            ]);


//            $rsp->s

            $this->info($rsp->status() . "---" . $rsp->body());

        }


//        dd($rsp->getStatusCode(), $rsp->getBody()->getContents());


//        $client->post();


        //$bing_token


        return;


        foreach (getCategoryIds(config()) as $value) {


            dd($value->name);

        }


        return;

        $env = file_get_contents(base_path('.env'));

//        preg_match("/APP_NAME=([\w.]+)/", $env, $res);

//        ;

        dd(preg_replace("/APP_NAME=[^\n]+/", "APP_NAME=123", $env, 1));

        return;

        $html = <<<oef
<html><head>
<title>王者荣耀手机游戏介绍</title>
</head>
<body>

<h3>游戏玩法</h3>
<p>《王者荣耀》是一款多人在线对战游戏，玩家需要通过组建自己的五人团队与其他玩家进行实时对战。游戏采用即时战略游戏（MOBA）模式，玩家需要合理利用各个英雄角色的技能，配合团队成员，在地图上争夺资源、攻击敌方防御塔和击败敌方英雄以获得胜利。</p>

<h3>游戏亮点</h3>
<p>王者荣耀拥有丰富多样的英雄选择，每个英雄都有独特的技能和特点，玩家可以根据自己的游戏风格选择适合的英雄进行对战。游戏地图设计精美，包含丰富的地形和战略要点，玩家需要通过团队合作和个人操作来掌控战局。游戏画面精致细腻，流畅度高，给玩家带来极致的视觉享受。</p>

<h3>游戏优势</h3>
<p>首先，《王者荣耀》是一款跨平台游戏，可以在手机上进行畅快的游戏对战，不受时间和地点限制。其次，游戏提供了丰富的游戏模式，包括排位赛、挑战赛、好友对战等，满足玩家的不同需求。此外，游戏进行了多次优化和更新，确保游戏的平衡性和稳定性。</p>

<h3>小编评语</h3>
<p>《王者荣耀》作为一款经典的手机游戏，无论是游戏玩法还是画面表现都取得了很大的成就。游戏的团队合作和个人操作的要求使得玩家在游戏中体验到了紧张刺激的战斗，同时也培养了玩家的策略思维和团队合作能力。如果你是一位MOBA游戏爱好者，那《王者荣耀》绝对不容错过。</p>


</body></html>
oef;


        $doc = QueryList::html($html);


        dd($doc->find('body')->eq(0)->html());


        return;

        $list = getArticleByCategoryName(20, 10, 0, [], [], 'push_time', 'desc', [['obj', '=', '23855']]);


        dd($list->toArray());

        return 0;

        $json = file_get_contents(base_path('123.json'));

//        dd(json_decode($json,true));

        $json = json_decode($json, true);


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

            file_put_contents(public_path('error.html'), mb_convert_encoding($content, "UTF-8", $ch));


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
