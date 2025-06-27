<?php

namespace Ycore\Http\Controllers\Third;


use Illuminate\Support\Facades\DB;
use Ycore\Jobs\ArticleStatic;
use Ycore\Models\ArticleDownload;
use Ycore\Models\DownloadSite;
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

            if (($post['main']['special_id'] ?? 0)) {
                $data['special_id'] = $post['main']['special_id'];
            }

            if ($post['main']['is_update_at']) {
                if (isset($post['main']['push_time']) && $post['main']['push_time']) {
                    $data['push_time'] = $post['main']['push_time'];
                }

            }

            $article = $ag->fill($data, $post['expand'])->create(true, false, false);


            $rule = $post['download']['rule'] ?? "";

            $note = $post['download']['note'] ?? "";

            $downloadSite = DownloadSite::where('rule', $rule)->first();

            $downloadSiteId = 0;

            //设置下载服务器
            if (!$downloadSite) {
                $d = DownloadSite::create([
                    'rule' => $rule ?: "",
                    'note' => $note ?: "",
                ]);
                $downloadSiteId = $d->id;
            } else {
                $downloadSiteId = $downloadSite->id;
            }

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
                'file_path' => $post['download']['file_path'] ?? "",
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


}
