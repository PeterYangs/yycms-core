<?php

namespace Ycore\Http\Controllers\Third;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ycore\Jobs\ArticleStatic;
use Ycore\Models\Article;
use Ycore\Models\ArticleDownload;
use Ycore\Models\DownloadSite;
use Ycore\Models\ExpandChange;
use Ycore\Models\Mode;
use Ycore\Models\Special;
use Ycore\Tool\ArticleGenerator;
use Ycore\Tool\Signature;

class ContentController extends BaseController
{


    /**
     * 添加软件
     * Create by Peter Yang
     * 2023-11-22 22:30:50
     */
    function content()
    {

        $post = request()->post();


        $validator = \Validator::make($post, [
            'main.title' => 'required',
            'main.category_id' => 'required|integer',
            'main.content' => 'required',
            'main.img' => 'required',
        ], [
            'required' => ':attribute 字段必填',
            'integer' => ':attribute 字段必须是数字'
        ]);

        if ($validator->fails()) {
            return Signature::fail(Signature::PARAMS_ERROR, $validator->errors()->first());
        }

        $article = null;


        try {
            DB::beginTransaction();
            $isUpdatedAt = false;

            $ag = new ArticleGenerator();

            $data = [
                'title' => $post['main']['title'],
                'category_id' => $post['main']['category_id'],
                'push_time' => now(),
                'content' => $post['main']['content'],
                'img' => $post['main']['img'],
                'seo_title' => $post['main']['seo_title'] ?? "",
                'seo_desc' => $post['main']['seo_desc'] ?? "",
                'seo_keyword' => $post['main']['seo_keyword'] ?? "",
                'push_status' => $post['main']['push_status'],
                'status' => 1,

            ];

            $special_id = $post['main']['special_id'] ?? 0;

            if ($special_id) {
                $data['special_id'] = $post['main']['special_id'];
            }

            if ($post['main']['is_update_at']) {
                if (isset($post['main']['push_time']) && $post['main']['push_time']) {
                    $data['push_time'] = $post['main']['push_time'];
                }

            }

            $rule = ($post['download']['rule'] ?? "{path}") ?: "{path}";

            $note = $post['download']['note'] ?? "";

            $downloadSite = DownloadSite::where('rule', $rule)->first();

            $downloadSiteId = 0;

            //设置下载服务器
            if (!$downloadSite) {
                if (empty($rule)) {
                    $downloadSite = DownloadSite::where('rule', '{path}')->first();
                    $downloadSiteId = $downloadSite->id;
                } else {
                    $d = DownloadSite::create([
                        'rule' => $rule ?: "",
                        'note' => $note ?: "",
                    ]);
                    $downloadSiteId = $d->id;
                }

            } else {
                $downloadSiteId = $downloadSite->id;
            }

            $file_path = trim($post['download']['file_path'] ?? "");

            if ($special_id !== 0 && empty($file_path) && $downloadSite->rule === "{path}") {
                $file_path = getOption("domain");
                if (isset($post['expand']['ios']) && empty($post['expand']['ios'])) {
                    $post['expand']['ios'] = getOption("domain");
                }
            }

            $article = $ag->fill($data, $post['expand'])->create(true, false, false);

            $get_article_download_article_id = ArticleDownload::where('library_id', $post['main']['library_article_id'])->first();
            //重复分发，直接返回结果
            if ($get_article_download_article_id) {
                DB::commit();
                return Signature::success([
                    'id' => $article->id,
                    'path' => parse_url(getDetailUrl($article))['path']
                ]);
            }

            ArticleDownload::create([
                'article_id' => $article->id,
                'library_id' => $post['main']['library_article_id'],
                'apk_id' => $post['main']['library_apk_id'] ?? 0,
                'download_site_id' => $downloadSiteId,
                'file_path' => $file_path,
                'save_type' => $post['download']['save_type'] ?? 1,
                'pan_password' => $post['download']['pan_password'] ?? ""
            ]);


            DB::commit();

            //重新静态化，防止下载地址不出现
            dispatch(new ArticleStatic($article->id));

            return Signature::success([
                'id' => $article->id,
                'path' => parse_url(getDetailUrl($article))['path']
            ]);

        } catch (\Exception $exception) {

            report($exception);
            DB::rollBack();
            return Signature::fail(Signature::CONTENT_ERROR, $exception->getMessage());

        }

    }


    /**
     * 特殊属性列表
     * @return array
     */
    function specialList()
    {

        return Signature::success(Special::all());
    }


    /**
     * 根据链接获取seo标题
     * @return array
     */
    function getSeoByUrl()
    {
        $post = request()->post();

        $validator = \Validator::make($post, [
            'urls' => 'required|array',
        ]);

        if ($validator->fails()) {
            return Signature::fail(Signature::PARAMS_ERROR, $validator->errors()->first());
        }

        $urls = $post['urls'];
        $result = [];
        foreach ($urls as $url) {

            try {
                $request = \Request::create($url);
                $route = \Route::getRoutes()->match($request);
                $id = $route->parameter('id');
                if (!is_numeric($id)) {
                    $result[] = ['url' => $url, 'seo_title' => ""];
                    continue;
                }
            } catch (\Throwable $exception) {
                $result[] = ['url' => $url, 'seo_title' => ""];
                continue;
            }

            $article = ArticleDetailModel()->where('id', $id)->first();
            if ($article) {
                $result[] = ['url' => $url, 'seo_title' => $article->seo_title];
            }

        }

        return Signature::success($result);
    }


    /**
     * 获取网站信息
     * @return array
     * @throws \JsonException
     */
    function getWebsiteInfo()
    {
        $data = [
            'domain' => getOption("domain"),
            'm_domain' => getOption('m_domain'),
            'site_name' => getOption('site_name'),
        ];

        return Signature::success($data);
    }


    /**
     * 同步友情链接
     * @return array
     * @throws \Exception
     */
    function syncFriendshipLinks()
    {

        $post = request()->post();

        $validator = \Validator::make($post, [
            'links' => 'required|array',
            'links.*.site_name' => 'string|required',
            'links.*.domain' => 'string|required',
            'links.*.m_domain' => 'string|required',
        ]);

        if ($validator->fails()) {
            return Signature::fail(Signature::PARAMS_ERROR, $validator->errors()->first());
        }

        foreach ($post['links'] as $link) {

            $this->addFriendshipLinks($link['site_name'], $link['domain'], "pc");
            $this->addFriendshipLinks($link['site_name'], $link['m_domain'], "mobile");

        }

        return Signature::success([]);
    }


    private function addFriendshipLinks($websiteName, $websiteLink, $device = 'pc')
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
     * 同步下载包
     * @return array
     */
    function syncApk()
    {
        $post = request()->post();
        $validator = \Validator::make($post, [
            'special_id' => 'required|integer',
            'android' => 'required|url',
            'ios' => 'required|url',

        ]);

        if ($validator->fails()) {
            return Signature::fail(Signature::PARAMS_ERROR, $validator->errors()->first());
        }

        ExpandChange::updateOrCreate(['special_id' => $post['special_id'], 'type' => 2], ['special_id' => $post['special_id'], 'type' => 2, 'download_url' => $post['android'], 'category_id' => 0, 'detail' => ""]);
        ExpandChange::updateOrCreate(['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.game')], ['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.game'), 'detail' => [['field' => 'ios', 'value' => $post['ios']]]]);
        ExpandChange::updateOrCreate(['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.app')], ['special_id' => $post['special_id'], 'type' => 1, 'category_id' => config('category.app'), 'detail' => [['field' => 'ios', 'value' => $post['ios']]]]);

        return Signature::success([]);
    }


    /**
     * 静态化一篇文章
     * @return void
     */
    function static()
    {
        $maxAttempts = 10; // 最多尝试 10 次抢文章，避免死循环
        $cacheKey = '_article_static_id'; // 上次处理的位置
        $currentId = \Cache::get($cacheKey, 0);

        for ($i = 0; $i < $maxAttempts; $i++) {
            // 查找下一篇未处理的文章
            $article = Article::where('id', '>', $currentId)->orderBy('id')->first();

            if (!$article) {
                // 回到开头重试
                $article = Article::orderBy('id')->first();
                if (!$article) {
                    \Log::warning("⚠️ 没有任何文章可处理");
                    return;
                }
            }

            $articleId = $article->id;
            $lockKey = "_article_static_lock:{$articleId}";

            // 只处理未被锁住的文章（锁10秒）
            if (\Cache::add($lockKey, 1, 10)) {
                // 抢到了，更新位置记录
                \Cache::put($cacheKey, $articleId, 3600);

                // 派发任务
                dispatch(new ArticleStatic($articleId));

                \Log::info("✅ 成功派发静态任务，文章ID: {$articleId}");
                return;
            }

            // 没抢到，继续尝试下一篇
            $currentId = $articleId;
        }

        \Log::info("⏭️ 多次尝试未获取可处理文章，跳过执行");
    }

}
