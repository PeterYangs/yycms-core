<?php

namespace Ycore\Tool;

use App\Dao\CategoryPushConfig;
use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\StoreArticle;

class Push
{


    /**
     * 采集文章发布到正式文章
     * Create by Peter Yang
     * 2023-02-09 15:33:30
     * @param StoreArticle $storeArticle
     * @param int $push_status 发布类型
     * @param int $status 文章状态
     * @param bool $is_gpt 是否用gpt生成
     * @throws \Throwable
     */
    static function spiderToArticle(StoreArticle $storeArticle, int $push_status = 1, int $status = 1, bool $is_gpt = false)
    {

        $article = null;

        try {

            if (!$storeArticle->title || !$storeArticle->img || !$storeArticle->content) {


                throw new \Exception('文章数据缺失！' . "采集id为：" . $storeArticle->id);
            }


            $ag = new ArticleGenerator();


            $ex = json_decode($storeArticle->expand_data, true, 512, JSON_THROW_ON_ERROR);


            $ag->fill([
                'category_id' => $storeArticle->category_id,
                'push_time' => \Date::now(),
                'content' => $storeArticle->content,
                'img' => $storeArticle->img,
                'title' => $storeArticle->title,
                'seo_desc' => $storeArticle->seo_desc,
                'seo_keyword' => $storeArticle->seo_keyword,
                'special_id' => $storeArticle->special_id,
                'push_status' => $push_status,
                'status' => $status
            ], $ex)->create(true, true, $is_gpt);


        } catch (\Exception $exception) {


            \Log::error("推送采集文章失败:" . $storeArticle->title . "--" . $exception->getMessage());

            if (app()->runningInConsole()) {

                echo "推送采集文章失败:" . $storeArticle->title . "--" . $exception->getMessage() . PHP_EOL;
            }


            throw new \Exception("推送采集文章失败:" . $storeArticle->title . "--" . $exception->getMessage());

//            return;

        } finally {

            //标记为已用
            $storeArticle->status = 2;

            $storeArticle->save();

        }


        if (app()->runningInConsole()) {


            echo $storeArticle->title . PHP_EOL;

        }


    }


}
