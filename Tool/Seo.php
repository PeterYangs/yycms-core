<?php

namespace Ycore\Tool;

use Ycore\Models\Article;
use Ycore\Models\SeoTitleChange;
use Ycore\Models\Tag;

class Seo
{


    /**
     * 设置seo标题
     * Create by Peter Yang
     * 2022-06-23 16:47:21
     * @param int $articleId
     * @param bool $forceChang 是否强制更新
     */
    static function setSeoTitle(int $articleId, bool $forceChang = false)
    {


        $article = Article::whereNull('deleted_at')
            ->withoutGlobalScopes()
            ->where('id', $articleId)
            ->first();

        if (!$article) {

            return;
        }


        $seo_title = $article->seo_title;


        if (!($seo_title === "" || $seo_title === null || $forceChang === true)) {

            return;
        }


        $title = self::getSeoTitle($article);


        $article->seo_title = $title;


        $article->save();


    }


    /**
     * 获取文章seo标题
     * Create by Peter Yang
     * 2023-01-04 11:10:31
     * @param Article $article
     * @return string
     */
    static function getSeoTitle(Article $article)
    {


        $title = "";


        $hookTitle = Hook::applyFilter('seo_title_detail', $article, $article->category);


        if ($hookTitle !== null && $hookTitle !== "") {


            return $hookTitle;
        }


        //游戏和应用标题设置
        if (getObjPlus($article, 'category.pid') === config('category.game') || getObjPlus($article,
                'category.pid') === config('category.app')) {


            //无安卓，无ios
            if (getObjPlus($article, "ex." . config('static.android_download_link')) === "" && getObjPlus($article,
                    "ex." . config('static.ios_download_link')) === "") {


                $arr = [
                    '[title]' . getDesc($article) . '下载(暂未上线)',

                ];


                $title = str_replace('[title]', getObjPlus($article, 'title'), $arr[array_rand($arr)]);


            }

            //有安卓 或者有ios(有特殊属性的不显示暂无下载)
            if ($article->special_id !== 0 || getObjPlus($article,
                    "ex." . config('static.android_download_link')) !== "" || getObjPlus($article,
                    "ex." . config('static.ios_download_link')) !== "") {


                if (str_contains($article->title, "版")) {


                    $arr = [
                        '[title]' . getDesc($article) . '下载_[title]官方下载' . getVersion($article),
                        '[title]' . getDesc($article) . '下载_[title]免费下载' . getVersion($article),
                        '[title]' . getDesc($article) . '下载_[title]手机下载' . getVersion($article),
                    ];


                } else {

                    $oneDesc = getDesc($article);

                    $twoDesc = "";

                    //防止两次随机重复
                    while (true) {


                        $twoDesc = getDesc($article);

                        if ($oneDesc !== $twoDesc) {

                            break;
                        }

                    }


                    $arr = [
                        '[title]' . $oneDesc . '下载_[title]' . $twoDesc . '下载' . getVersion($article),
                        '[title]' . $oneDesc . '下载_[title]' . $twoDesc . '下载' . getVersion($article),
                        '[title]' . $oneDesc . '下载_[title]' . $twoDesc . '下载' . getVersion($article),
                    ];

                }


                $title = str_replace('[title]', getObjPlus($article, 'title'), $arr[array_rand($arr)]);
            }


        } elseif (getObjPlus($article, 'category.id') === config('category.collect')) {

            $arr = [
                '好玩的[title]手游合集推荐-最新的[title]手游大全下载',
                '[title]手游排行榜-[title]手游哪个好玩'

            ];


            $arr = array_map(function ($item) use ($article) {


                if (self::findKeyword(getObjPlus($article, 'title'), ['游戏', '手游'])) {


                    $item = str_replace(['游戏', '手游'], "", $item);


                }

                return $item;

            }, $arr);


            $title = str_replace('[title]', getObjPlus($article, 'title'), $arr[array_rand($arr)]);

        } elseif (getObjPlus($article, 'category.id') === config('category.app_collect')) {

            $arr = [
                '[title]软件推荐-好用的[title]app-[title]软件合集',
                '[title]app有哪些-好用的[title]软件-[title]软件下载'

            ];

            $arr = array_map(function ($item) use ($article) {

                if (self::findKeyword(getObjPlus($article, 'title'), ['应用', 'app', '软件'])) {


                    $item = str_replace(['软件', 'app', '应用'], "", $item);

                }

                return $item;

            }, $arr);


            $title = str_replace('[title]', getObjPlus($article, 'title'), $arr[array_rand($arr)]);

        } else {


            $title = $article->title;

        }


        return $title;


    }


    /**
     * 查找文章内容中的标签并替换
     * Create by Peter Yang
     * 2022-08-05 19:32:19
     * @param Article $article
     * @param string $content
     * @return string
     */
    static function setTagLinkForContent(Article $article, string $content)
    {


        //获取已经被使用过的标签（避免点击标签后无结果）
        if (\Cache::has('tag_list')) {


            $tagList = \Cache::get('tag_list', []);

        } else {


            $list = Tag::select([
                'id',
                'title'
            ])->whereRaw("exists (select * from `article_tag` where `tag`.`id` = `article_tag`.`tag_id` limit 1)")->get();

            \Cache::put('tag_list', $list);

            $tagList = $list;

        }


        $replaceList = [];


        foreach ($tagList as $value) {

            if (str_replace("." . env('TOP_DOMAIN'), '', request()->getHttpHost()) === config('static.mobile_prefix')) {


                $replaceList[$value->title] = route('mobile.tag', ['tag' => $value->id]);

            } else {


                $replaceList[$value->title] = route('pc.tag', ['tag' => $value->id]);

            }

        }


        return (new KeyReplace($content, $replaceList, true, [], false))->getResultText();


    }


    /**
     * 是否修改文章seo标题
     * Create by Peter Yang
     * 2022-10-08 13:58:19
     * @param int $articleId
     * @param array $articleData
     * @param string $expandTableName
     * @param array $expandData
     */
    static function isChangeArticleSeoTitle(
        int    $articleId,
        array  $articleData,
        string $expandTableName,
        array  $expandData
    )
    {

        $seoTitleChangeItem = SeoTitleChange::first();

        if (!$seoTitleChangeItem) {


            return false;
        }


        $articleItem = Article::with('category')->whereNull('deleted_at')
            ->withoutGlobalScopes()->find($articleId);


        foreach ($seoTitleChangeItem->article_fields as $value) {

            $articleItemData = $articleItem->$value;

            if (!$articleItemData) {

                $articleItemData = null;
            }

            $articleItemForm = $articleData[$value];

            if (!$articleItemForm) {

                $articleItemForm = null;
            }


            if ($articleItemData !== $articleItemForm) {


                return true;

            }


        }


        $expandItem = \DB::table($expandTableName)->where('article_id', $articleId)->first();

        if (!$expandItem) {


            return false;
        }


        foreach ($seoTitleChangeItem->category_item as $value) {


            if ($value['category_id'] === $articleItem->category_id || $value['category_id'] === $articleItem->category->pid) {


                foreach ($value["fields"] as $v) {

                    $expandItemData = $expandItem->$v;

                    if (!$expandItemData) {

                        $expandItemData = null;
                    }

                    $expandItemForm = $expandData[$v];

                    if (!$expandItemForm) {

                        $expandItemForm = null;
                    }


                    if ($expandItemData !== $expandItemForm) {


                        return true;

                    }


                }

            }


        }


        return false;
    }


    /**
     * 是否有对应的关键字
     * Create by Peter Yang
     * 2022-12-14 10:39:57
     * @param string $str
     * @param array $keywords
     * @return bool
     */
    static function findKeyword(string $str, array $keywords)
    {


        $isFind = false;

        foreach ($keywords as $value) {


            if (stripos($str, $value) !== false) {

                $isFind = true;

                break;
            }

        }


        return $isFind;

    }


}
