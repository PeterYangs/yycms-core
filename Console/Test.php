<?php

namespace Ycore\Console;

use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use QL\QueryList;
use Symfony\Component\Mailer\Transport;
use Ycore\Core\Core;
use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\ArticleExpand;
use Ycore\Models\ExpandChange;
use Ycore\Models\Mode;
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


    function addFriendshipLinks($websiteName, $websiteLink, $device = 'pc')
    {

        $websiteLink = rtrim($websiteLink, '/') . "/";

        $modeTitle = "";

        if ($device === 'pc') {
            $modeTitle = "友情链接-pc";
        } else {
            $modeTitle = "友情链接-mobile";
        }

        $mode = Mode::where('title', $modeTitle)->first();

        if (!$mode) {
            throw new \Exception('未找到对应的模块(mode)');
        }

        $list = $mode->list;

        if (!is_array($list)) {
            throw new \Exception('模块数据结构不是数组');
        }

        $isFind = false;
        foreach ($list as $item) {

            $link = rtrim($item[1], '/') . "/";

            if ($link === $websiteLink) {
                $isFind = true;
                break;
            }

        }

        if (!$isFind) {
            $list[] = [$websiteName, $websiteLink];
            $mode->list = $list;
            $mode->save();
        }

    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \JsonException
     */
    public function handle()
    {

        $post = ['special_id' => 2, 'android' => 'https://www.925g.com', 'ios' => "https://www.52xz.com"];

//        ExpandChange::where('special_id',$post['special_id']);

        ExpandChange::updateOrCreate(['special_id' => $post['special_id'], 'type' => 2], ['special_id' => $post['special_id'], 'type' => 2, 'download_url' => $post['android'], 'category_id' => 0, 'detail' => ""]);
        ExpandChange::updateOrCreate(['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.game')], ['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.game'), 'detail' => [['field' => 'ios', 'value' => $post['ios']]]]);
        ExpandChange::updateOrCreate(['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.app')], ['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.app'), 'detail' => [['field' => 'ios', 'value' => $post['ios']]]]);


        return;

        $websiteName = "522gg手游网";
        $websiteLink = "https://www.baidu.com";
        $device = "pc";

        $this->addFriendshipLinks($websiteName, $websiteLink, $device);

        return;

        $url = "http://www.core.com/yx/60504.html";

        $request = \Request::create($url);

        $route = \Route::getRoutes()->match($request);

        dd($route->parameter('id'));


//        dd();


        $f = Date::createFromTimeString(date("Y-m-d") . " " . $this->getNightTimeFromString("522gg.com13"));

        dd($f->addMinutes(50)->format('H:i'));

        return;

        $url = 'https://apk.down8818.com/1818836746/apk/068084b4c24c2309895d47e6a47e917d.apk';

        $private_key = 'iamyourfather6';

        $uid = 1818836746;

        $expire_time = time() + 60;   // 该签发的资源30s以后过期

        $rand_value = rand(0, 100000); // 生成随机数

        $parse_result = parse_url($url); // 解析 URL

        $request_path = rawurldecode($parse_result["path"]); // /29/音乐/02.一千零一夜-李克勤.wma

        $sign = md5(sprintf("%s-%d-%d-%d-%s", $request_path, $expire_time, $rand_value, $uid, $private_key));

        $wait = sprintf("%d-%d-%d-%s", $expire_time, $rand_value, $uid, $sign);

        $result = $url . "?auth_key=" . $wait;

        dd($result);


//        dd(parse_url("https://www.baidu.com/aaa"));

//        dd(\File::fi(storage_path('app/public/062fbbc1-e067-4d4a-9d08-fc888f4ecef8-temp-article/文章1.txt')));


//        deleteArticle(23985);
//
//        dd("");


        $a = new ArticleGenerator();
//
//        $a->fill([])->create(false);


//        dd("");

        $a->fill([
            'category_id' => 2,
            'content' => "角色扮演内容啊",
            'title' => '随便一个标题' . time(),
            'img' => 'https://soft-library.oss-cn-hangzhou.aliyuncs.com/icon/2023/11/20/3162ec9cf15363c9d2380bb3ef348caf.png',
//            'push_time'=>'2023-12-01',
//            'updated_at'=>'2023-12-01'
            'issue_time' => now()
        ], [
            'screenshots' => [
                [
                    'img' => 'https://soft-library.oss-cn-hangzhou.aliyuncs.com/screenshot/2023/11/20/85fd7f4d-83e2-4e66-b7d8-82079a25aa820.png',
                    'name' => '截图1'
                ],
                [
                    'img' => 'https://soft-library.oss-cn-hangzhou.aliyuncs.com/screenshot/2023/11/20/8ad79eec-5401-465c-8a1f-df064d6f9c8e1.png',
                    'name' => '截图2'
                ],

            ]
        ])->update(['id' => 23991]);


        dd("");


//        Http::get();

//        dd();
//
//        ;

        $arr = array_filter(get_headers("http://www.925g.com", true), function ($item) {


            if (is_numeric($item)) {

                return true;
            }

            return false;

        }, ARRAY_FILTER_USE_KEY);

//        dd($arr);

        dd(end($arr));

//        $ch=curl_init("http://www.925g.com");

//        dd(curl_getinfo($ch));


        $content = file_get_contents(public_path('250312.html'));

//        dd($content);

        $encode = mb_detect_encoding($content, ['utf8', 'gb2312']);
        $content = mb_convert_encoding($content, "utf8", $encode);

//        file_put_contents(public_path('123.html'),$content);

        dd($content);

        return;

        $client = new Client();

        $rsp = $client->request('get', "https://d.apkpure.com/b/APK/com.supercell.brawlstars?versionCode=52177&nc=arm64-v8a%2Carmeabi-v7a&sv=24", [
            'proxy' => "http://127.0.0.1:33210",
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
                "Accept-Encoding" => 'gzip, deflate',
                "Accept" => '*/*',
                "Connection" => "keep-alive",
            ],
            'allow_redirects' => false,
//            'allow_redirects' => [
//                'max' => 5,
//                'strict' => false,
//                'referer' => false,
//                'protocols' => ['http', 'https'],
//                'track_redirects' => false
//            ]
        ]);


        echo $rsp->getStatusCode() . PHP_EOL;


        return;


        for ($i = 0; $i < 10; $i++) {


            print_r(get_headers("https://dcd.7230x.com/apk/d47ab2150bbd3884b5d2d16347525345/2104/04/1882427.apk", 1));

        }

//        dd();


        return;

        $client = new Client();


        $client->request('get', 'https://store-drcn.hispace.dbankcloud.cn/dl/appdl/application/apk/04/042cd58f1e4b48d3bc04d00b9db618fa/com.nyhy.khyuny.2306170939.apk?sign=e90k1001e710010720009000@39242AEB32C94CE8B8351B071FFF55F6&source=autoList_search&subsource=%E7%96%AF%E7%8B%82%E7%BE%8E%E9%A3%9F%E5%A4%A7%E5%B8%88&listId=15&position=1&extendStr=serviceType%3A0%3Bs%3A1694767686707&tabStatKey=1&hcrId=f073b797d4d942d38d3a4a413e307a36&maple=0&distOpEntity=HWSW&traceId=65041a462e539b5a', ['sink' => base_path('123.apk')]);


        return;

//        $a = new ArticleGenerator();
//
//        $a->fill([
//            'category_id' => 2,
//            'content' => "这是一个经营手游和角色扮演和我的世界",
//            'title' => '随便一个标题' . time(),
//            'img' => '123'
//
//        ], [])->create();


        $article = getArticleById(23941);

        autoAssociationObject($article);


        return;

        $image = Image::make((public_path('uploads/20230605/647d8b90bc53c2.44800558.temp')));

        $waterWidth = $image->getWidth() / 3;

        //图片太小就不添加水印了
        if ($waterWidth >= 10) {

//                    $water = Image::make(Storage::disk('upload')->path(getOption('watermark')));
            $water = Image::make(file_get_contents(rtrim(env('IMAGE_DOMAIN'), '/') . '/' . trim(env('UPLOAD_PREFIX'), '/') . '/' . ltrim(getOption('watermark'), '/')));

            //设置水印图片大小
            $water->resize($waterWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            //设置在右下角
            $image->insert($water, 'bottom-right', 10, 10);

            $image->save(null, null, 'png');


//            dd($data);


        }

        return;


//        Transport::fromDsn();

//        Mai


//        $image = Image::make(base_path("64d0584fce47d6.85531711.jpg"));
        $image = Image::make(file_get_contents(base_path("64d0584fce47d6.85531711.jpg")));


        $waterWidth = $image->getWidth() / 5;


//        dd($waterWidth);

        $water = Image::make(base_path('logo.png'));

        $water->resize($waterWidth, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->insert($water, 'bottom-right', 10, 10);

        $image->save(base_path('ll.png'));


        return;

        \Ycore\Tool\Mail::send(['904801074@qq.com'], '测试标题', "测试内容", '随便爽爽爽', "123.txt");


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


    function getNightTimeFromString($input)
    {
        // 将字符串转换为哈希值
        $hash = crc32($input);

        // 限制在 0~239（4小时 * 60分钟）
        $minutes = $hash % 240;

        // 计算小时和分钟
        $hour = intdiv($minutes, 60);
        $minute = $minutes % 60;

        // 返回格式化时间
        return sprintf("%02d:%02d", $hour, $minute);
    }

}
