<?php

namespace Ycore\Tool;


use QL\QueryList;
use Throwable;
use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Ycore\Models\Category;
use Ycore\Models\ExpandData;
use Ycore\Service\Ai\Ai;
use Ycore\Service\Upload\Upload;

/**
 * 文章操作类
 */
class ArticleGenerator
{


    protected array $articleData;

    protected array $expandData;


    protected Upload $upload;


    /**
     * @var Ai
     */
    protected Ai $ai;

    public function __construct()
    {


        $this->ai = resolve(Ai::class);

        $this->upload = resolve(Upload::class);

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
     * @throws Throwable
     */
    function update(array $attributes, bool $isPush = false): Article
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

            //获取空的拓展数据
            $expandData = optional(getExpandByCategoryId($article->category_id))->toArray() ?: [];

            //原值覆盖
            foreach ($this->expandData as $key => $item) {


                foreach ($expandData as $expandKey => $expand) {

                    if ($key === $expand['name']) {

                        $expandData[$expandKey]['value'] = $item;
                    }

                }

            }


            foreach ($expandData as $key => $value) {


                foreach ($this->expandData as $k => $v) {


                    if ($k === $value['name']) {


                        switch ($value['type']) {

                            case 4:
                            case 6:
                                //图片
                            case 7:

                                if (!is_array($v)) {

                                    $v = json_decode($v, true, 512, JSON_THROW_ON_ERROR);
                                }


                                foreach ($v as $imgKey => $imgItem) {

                                    $img_url = $imgItem['img'] ?? "";


                                    //外链图片
                                    if (preg_match("/^(http|https):\/\//", $img_url)) {

                                        if (!$img_url) {

                                            continue;
                                        }
                                        $v[$imgKey]['img'] = ltrim($this->upload->uploadRemoteFile($img_url), '/uploads/');

                                    }
                                }

                                break;


                        }

                        $expandData[$key]['value'] = $v;

                        break;
                    }

                }


            }

//            dd($expandData);


            //获取关联表
            $table_name = CategoryController::getExpandTableName($article->category_id);


            $expandDataKeyValue = dealExpandToTable($expandData);

            foreach ($this->articleData as $key => $value) {


                if ($article->isFillable($key)) {

                    $article->$key = $value;
                }


            }


            //判断是否更新seo标题
            $isChangeSeoTitle = Seo::isChangeArticleSeoTitle($article->id, $article->toArray(), $table_name, $expandDataKeyValue);

            $article->expand = $expandData;


            $article->save();


            //设置拓展表数据
            foreach ($expandData as $item) {


                ExpandData::updateOrCreate(['article_id' => $article->id, 'article_expand_detail_id' => $item['id']], [
                    'article_id' => $article->id,
                    'article_expand_detail_id' => $item['id'],
                    'article_expand_id' => $item['article_expand_id'] ?? 0,
                    'name' => $item['name'] ?? "",
                    'desc' => $item['desc'] ?? "",
                    'type' => $item['type'] ?? 1,
                    'select_list' => is_array($item['select_list']) ? json_encode($item['select_list']) : $item['select_list'],
                    'model_name' => $item['model_name'],
                    'label' => is_array($item['label']) ? json_encode($item['label']) : $item['label'],
                    'condition' => is_array($item['condition']) ? json_encode($item['condition']) : $item['condition'],
                    'default_condition' => is_array($item['default_condition']) ? json_encode($item['default_condition']) : $item['default_condition'],
                    'show_field' => is_array($item['show_field']) ? json_encode($item['show_field']) : $item['show_field'],
                    'value' => is_array($item['value']) ? json_encode($item['value']) : $item['value'] ?? ""

                ]);


            }


            //处理一对多关联
            dealArticleAssociationObject($article->id, $expandData);


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

            return $article;

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
     * @throws Throwable
     */
    function create(bool $isPush = true, bool $is_gpt = false, $titleUniqueCheck = true): Article
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


            throw new \Exception(implode("\n", $validator->errors()->all()));

        }

        $img = $this->articleData['img'];


        //外链图片
        if (preg_match("/^(http|https):\/\//", $img)) {


            $img_url = ltrim($this->upload->uploadRemoteFile($img), '/uploads/');

            $this->articleData['img'] = $img_url;

        }


        try {

            \DB::beginTransaction();


            $articleData = $this->articleData;


            if ($titleUniqueCheck) {

                $isFind = Article::withoutGlobalScopes()->where('title', $articleData['title'])->where('category_id', $articleData['category_id'])->first();


                if ($isFind) {


                    throw new \Exception("《" . $articleData['title'] . "》" . "已存在！");
                }

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
                                //图片
                            case 7:

                                if (!is_array($v)) {

                                    $v = json_decode($v, true, 512, JSON_THROW_ON_ERROR);
                                }

                                foreach ($v as $imgKey => $imgItem) {

                                    $img_url = $imgItem['img'] ?? "";

                                    //外链图片
                                    if (preg_match("/^(http|https):\/\//", $img_url) !== false) {

                                        if (!$img_url) {

                                            continue;
                                        }
                                        $v[$imgKey]['img'] = ltrim($this->upload->uploadRemoteFile($img_url), '/uploads/');

                                    }
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


            $category = Category::where('id', $articleData['category_id'])->first();

            if (!$category) {


                throw new \Exception("分类id不存在:" . $category->id);
            }


            $article = Article::create($articleData);


            //设置拓展表数据
            foreach ($article->expand as $item) {


                ExpandData::updateOrCreate(['article_id' => $article->id, 'article_expand_detail_id' => $item['id']], [
                    'article_id' => $article->id,
                    'article_expand_detail_id' => $item['id'],
                    'article_expand_id' => $item['article_expand_id'] ?? 0,
                    'name' => $item['name'] ?? "",
                    'desc' => $item['desc'] ?? "",
                    'type' => $item['type'] ?? 1,
                    'select_list' => is_array($item['select_list']) ? json_encode($item['select_list']) : $item['select_list'],
                    'model_name' => $item['model_name'],
                    'label' => is_array($item['label']) ? json_encode($item['label']) : $item['label'],
                    'condition' => is_array($item['condition']) ? json_encode($item['condition']) : $item['condition'],
                    'default_condition' => is_array($item['default_condition']) ? json_encode($item['default_condition']) : $item['default_condition'],
                    'show_field' => is_array($item['show_field']) ? json_encode($item['show_field']) : $item['show_field'],
                    'value' => is_array($item['value']) ? json_encode($item['value']) : $item['value'] ?? ""

                ]);


            }


            //处理一对多关联
            dealArticleAssociationObject($article->id, $expandData);

            //获取关联表
            $table_name = CategoryController::getExpandTableName($articleData['category_id']);

            if ($table_name) {

                \DB::table($table_name)->updateOrInsert(['article_id' => $article->id], $expandDataKeyValue);
            }


            //如果设置了seo_title数据就不自动设置seo_title
            if (!$articleData['seo_title']) {

                //设置seo标题
                Seo::setSeoTitle($article->id, true);
            }

            //chatgpt替换内容
            if ($is_gpt) {


                $article->content = $this->ai->article($article);

                if (str_contains($article->content, "<html")) {


                    $doc = QueryList::html($article->content);

                    $article->content = trim($doc->find('body')->eq(0)->html());

                    if ($article->content === "") {

                        throw new \Exception('AI生成内容为空！');
                    }

                }


                $article->save();


            }


            \DB::commit();


            //文章更新触发的事件
            event(new ArticleUpdate($article->id));


            if ($article->push_status === 1 && $article->status === 1 && $isPush) {

                event(new WebsitePush($article->id));

            } else {

                \Log::channel('push')->error("【" . $article->title . "】" . $article->id . ",不推送，当前【push_status:" . $article->push_status . "】【status:" . $article->status . "】【isPush:" . $isPush . "】");
            }


            return $article;

        } catch (\Exception $exception) {

            \DB::rollBack();


            throw new \Exception($exception->getMessage() . "   " . $exception->getFile() . ":" . $exception->getLine());

        }


    }


    function articleStatic(int $articleId): bool
    {

        $article = Article::where('id', $articleId)->with('category')->first();


        if (!$article) {


            return false;
        }

        staticByArticle($article);


        return true;
    }


}
