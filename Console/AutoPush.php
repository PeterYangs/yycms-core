<?php

namespace Ycore\Console;

use App\Dao\CategoryPushConfig;
use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\StoreArticle;
use Ycore\Tool\Expand;
use Ycore\Tool\Push;
use Ycore\Tool\Seo;
use Illuminate\Console\Command;

class AutoPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoPush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动发布采集文章到正式文章';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $now = time();

        $list = \Ycore\Models\AutoPush::where('status', 1)->where('type', 'spider')->get();


        foreach ($list as $key => $value) {


            $time_range = $value->time_range;


            if ($time_range) {


                $startTime = strtotime(date("Y-m-d ", $now) . $time_range[0]);

                $endTime = strtotime(date("Y-m-d ", $now) . $time_range[1]);


                if (!($startTime < $now && $endTime > $now)) {


                    continue;
                }


            }


            $isOk = false;

            switch ($value->cycle) {


                case "min":

                    $minNumber = (int)($now / 60);

                    $min = $value->min;


                    if ($minNumber % $min === 0) {

                        $isOk = true;

                    }


                    break;


                case "hour":


                    $hourNumber = (int)($now / 60 / 60);

                    $hour = $value->hour;


                    if ($hourNumber % $hour === 0 && (int)date("i", $now) === $value->min) {


                        $isOk = true;
                    }


                    break;

                case "day":

                    $dayNumber = (int)($now / 60 / 60 / 24);

                    $day = $value->day;

                    if ($dayNumber % $day === 0 && (int)date("H") === $value->hour && (int)date("i",
                            $now) === $value->min) {


                        $isOk = true;


                    }


                    break;


            }


            if (!$isOk) {


                echo "发布设置(id" . $value->id . ")不在发布时间！" . PHP_EOL;

                continue;
            }


//            if ($isOk) {


            $category_id = $value->category_id;

            //获取该分类的子分类
            $cIds = Category::where('pid', $category_id)->get()->pluck('id')->push($category_id)->all();


            $query = StoreArticle::whereIn('category_id', $cIds)->where('status', 1)->limit($value->number);


            switch ($value->rule) {

                case 1:

                    $query->orderBy('id', 'desc');

                    break;


                case 2:

                    $query->orderBy('id', 'asc');

                    break;

                case 3:

                    $query->inRandomOrder();

                    break;


            }

            $res = $query->get();


            foreach ($res as $a) {


                try {

                    Push::spiderToArticle($a, $value->push_status);

                } catch (\Exception $exception) {


                    echo "发布错误" . $exception->getMessage() . PHP_EOL;

                }


            }


//            }


        }


        return 0;
    }


}


