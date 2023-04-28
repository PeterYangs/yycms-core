<?php

namespace Ycore\Tool;

use App\Dao\CategoryPushConfig;
use App\Events\ArticleUpdate;
use App\Events\WebsitePush;
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
     * @throws \Throwable
     */
    static function spiderToArticle(StoreArticle $storeArticle, int $push_status = 1)
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
                'push_status' => $push_status
            ], $ex)->create();


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


    /**
     * 根据配置项将采集文章发布到正式文章
     * Create by Peter Yang
     * 2023-02-09 15:45:06
     * @param CategoryPushConfig $categoryPushConfig
     * @throws \Throwable
     */
    static function push(CategoryPushConfig $categoryPushConfig)
    {

        $query = StoreArticle::limit($categoryPushConfig->num)->where('debug', 0)->where('status', 1)->orderBy('id',
            'desc');


        $id = optional(Category::where('name', $categoryPushConfig->categoryName)->first())->id;

        $son = Category::where('pid', $id)->get()->pluck('id')->all();

        $arr = array_merge([$id], $son);

        $list = $query->whereIn('category_id', $arr)->get();


        foreach ($list as $item) {

            $article = null;

            try {

                if (!$item->title || !$item->img || !$item->content) {


                    throw new \Exception('文章内容缺失！' . "采集id为：" . $item->id);
                }


                \DB::beginTransaction();

                $article = Article::create([
                    'category_id' => $item->category_id,
                    'push_time' => \Date::now(),
                    'content' => $item->content,
                    'img' => $item->img,
                    'title' => $item->title,
                    'seo_title' => '',
                    'seo_desc' => $item->seo_desc,
                    'seo_keyword' => $item->seo_keyword,
                    'admin_id_create' => 1,
                    'admin_id_update' => 1,
                    'special_id' => $item->special_id,

                ]);

                if ($item->expand_data) {


                    $ex = json_decode($item->expand_data, true, 512, JSON_THROW_ON_ERROR);

                    foreach ($ex as $key => $e) {


                        if (is_array($e)) {

                            $ex[$key] = json_encode($e, JSON_THROW_ON_ERROR);
                        }

                    }

                    $table = CategoryController::getExpandTableName($item->category_id);


                    $ex['article_id'] = $article->id;

                    \DB::table($table)->insert($ex);

                }

                Expand::SyncExpand($article);


                Seo::setSeoTitle($article->id);


                \DB::commit();

            } catch (\Exception $exception) {

                \DB::rollBack();

                echo $item->title . ":" . $exception->getMessage() . PHP_EOL;

                \Log::error("推送采集文章失败:" . $item->title . "--" . $exception->getMessage());


                continue;

            } finally {

                //标记为已用
                $item->status = 2;

                $item->save();
            }


            if ($article !== null && env('APP_ENV') === "production") {

                //推送到站长
                event(new WebsitePush($article->id));

                //静态化
                event(new ArticleUpdate($article->id));

                echo $item->title . PHP_EOL;

            } else {

                echo "debug:" . $item->title . PHP_EOL;
            }


        }
    }


}
