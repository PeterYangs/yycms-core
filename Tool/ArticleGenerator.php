<?php

namespace Ycore\Tool;

//文章操作类
use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Models\CollectTag;

class ArticleGenerator
{


    protected array $articleData;

    protected array $expandData;


    public function __construct()
    {
    }


    /**
     * 数据填充
     * Create by Peter Yang
     * 2023-03-16 17:41:41
     * @param array $articleData 文章主表数据
     * @param array $expandData 拓展表数据
     */
    function fill(array $articleData, array $expandData): static
    {

        $this->articleData = $articleData;

        $this->expandData = $expandData;


        return $this;

    }


    /**
     * 更新文章
     * Create by Peter Yang
     * 2023-03-22 19:03:49
     * @param array $attributes 更新条件
     * @param bool $isPush 是否推送到站长
     * @throws \Throwable
     */
    function update(array $attributes, bool $isPush = false)
    {


        $now = Date::now();

        try {

            DB::beginTransaction();

            $articleData = $this->articleData;


            $article = Article::where($attributes)->whereNull('deleted_at')->withoutGlobalScopes()->first();

            if (!$article) {

                throw new \Exception('未找到更新文章');
            }


            //定时发布
            if (isset($articleData['push_status']) && $articleData['push_status'] === 2) {


                //发布时间小于当前时间置为已发布
                if (isset($articleData['push_time']) && $now->unix() > strtotime($articleData['push_time'])) {

                    $articleData['push_status'] = 1;
                }

            }


            $expand = $article->expand;

            foreach ($expand as $key => $value) {


                foreach ($this->expandData as $k => $v) {

                    if ($k === $value['name']) {


                        switch ($value['type']) {

                            case 4:
                            case 6:
                            case 7:

                                if (!is_array($v)) {

                                    $v = json_decode($v, true);
                                }

                                break;


                        }


                        $expand[$key]['value'] = $v;

                    }

                }


            }

            //获取关联表
            $table_name = CategoryController::getExpandTableName($article->category_id);

            $expandDataKeyValue = dealExpandToTable($expand);

            foreach ($this->articleData as $key => $value) {


                if ($article->isFillable($key)) {

                    $article->$key = $value;
                }


            }

//            dd($article->toArray());


            //判断是否更新seo标题
            $isChangeSeoTitle = Seo::isChangeArticleSeoTitle($article->id, $article->toArray(), $table_name, $expandDataKeyValue);

            $article->expand = $expand;


            $article->save();


            //处理一对多关联
            dealArticleAssociationObject($article->id, $expand);


            if ($table_name) {

                \DB::table($table_name)->updateOrInsert(['article_id' => $article->id], $expandDataKeyValue);
            }


            //设置seo标题
            Seo::setSeoTitle($article->id, $isChangeSeoTitle);


            DB::commit();


            //文章更新触发的事件
            event(new ArticleUpdate($article->id));


            if ($article->push_status === 1 && $article->status === 1 && $isPush) {

                event(new WebsitePush($article->id));
            }


        } catch (\Exception $exception) {

            DB::rollBack();

            throw new \Exception($exception->getMessage() . "   " . $exception->getFile() . ":" . $exception->getLine());


        }

    }


    /**
     * 创建文章
     * Create by Peter Yang
     * 2023-03-23 15:03:56
     * @param bool $isPush 是否推送到站长
     * @throws \Throwable
     */
    function create(bool $isPush = true, bool $autoAssociationObject = true)
    {


        $validator = \Validator::make($this->articleData, [
            'title' => 'required',
            'category_id' => 'required|integer',
            'content' => 'required',
            'img' => 'required',
        ], [
            'required' => ':attribute 字段必填',
            'integer' => ':attribute 字段必须是数字'
        ]);

        if ($validator->fails()) {


            throw new \Exception(join("\n", $validator->errors()->all()));

        }


        try {

            \DB::beginTransaction();


            $articleData = $this->articleData;

            $isFind = Article::where('title', $articleData['title'])->where('category_id', $articleData['category_id'])->first();


            if ($isFind) {


                throw new \Exception("《" . $articleData['title'] . "》" . "已存在！");
            }


            $now = Date::now();

            if (!($articleData['push_time'] ?? "")) {

                //未设置发布时间则为当前时间
                $articleData['push_time'] = date("Y-m-d H:i:s", $now->unix());
            }

            //发布状态，1为已发布，2为定时发布，3为自动发布
            $push_status = $articleData['push_status'] ?? 1;


            //定时发布
            if ($push_status === 2) {


                //发布时间小于当前时间置为已发布
                if ($now->unix() > strtotime($articleData['push_time'])) {

                    $articleData['push_status'] = 1;
                }

            }


            if (app()->has('adminInfo')) {


                $articleData['admin_id_create'] = resolve('adminInfo')['id'];
                $articleData['admin_id_update'] = resolve('adminInfo')['id'];

            } else {


                $articleData['admin_id_create'] = 1;
                $articleData['admin_id_update'] = 1;

            }

            //获取空的拓展数据
            $expandData = optional(getExpandByCategoryId($articleData['category_id']))->toArray() ?: [];


            foreach ($expandData as $key => $value) {


                foreach ($this->expandData as $k => $v) {


                    if ($k === $value['name']) {


                        switch ($value['type']) {

                            case 4:
                            case 6:
                            case 7:

                                if (!is_array($v)) {

                                    $v = json_decode($v, true);
                                }

                                break;


                        }

                        $expandData[$key]['value'] = $v;

                        break;
                    }

                }


            }


            $articleData['expand'] = $expandData;


            $expandDataKeyValue = dealExpandToTable($expandData);

            //添加默认数据
            $articleData['seo_title'] = ($articleData['seo_title'] ?? "");
            $articleData['seo_desc'] = ($articleData['seo_desc'] ?? "");
            $articleData['seo_keyword'] = ($articleData['seo_keyword'] ?? "");


            $article = Article::create($articleData);


            //处理一对多关联
            dealArticleAssociationObject($article->id, $expandData);

            //获取关联表
            $table_name = CategoryController::getExpandTableName($articleData['category_id']);

            if ($table_name) {

                \DB::table($table_name)->updateOrInsert(['article_id' => $article->id], $expandDataKeyValue);
            }


            //设置seo标题
            Seo::setSeoTitle($article->id, true);


            //自动设置关联关系
            if ($autoAssociationObject) {

                $category = Category::where('id', $this->articleData['category_id'])->first();


                $collect = Collect::whereIn('son_id', [$category->id, $category->parent->id])->first();


                if ($collect) {


                    $content = $this->articleData['content'];


                    $t = CollectTag::whereRaw("? like CONCAT('%',title,'%')", [$content])->limit(10)->get();


                    if ($t->count() > 0) {


                        $mainList = Article::where('category_id', $collect->category_id)->where(function ($query) use ($t) {


                            foreach ($t as $v) {

                                $query->orWhere('title', 'like', '%' . $v->title . '%');
                            }

                        })->limit(4)->get();


                        foreach ($mainList as $main) {

                            ArticleAssociationObject::create([
                                'main' => $main->id,
                                'slave' => $article->id
                            ]);

                        }


                    }


                }


            }


            \DB::commit();


            //文章更新触发的事件
            event(new ArticleUpdate($article->id));


            if ($article->push_status === 1 && $article->status === 1 && $isPush) {

                event(new WebsitePush($article->id));
            }


        } catch (\Exception $exception) {

            \DB::rollBack();


            throw new \Exception($exception->getMessage() . "   " . $exception->getFile() . ":" . $exception->getLine());

        }


    }


}
