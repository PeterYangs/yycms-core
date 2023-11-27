<?php

namespace Ycore\Http\Controllers\Third;


use Ycore\Models\ArticleDownload;
use Ycore\Models\DownloadSite;
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

            $ag = new ArticleGenerator();

            $article = $ag->fill([
                'title' => $post['main']['title'],
                'category_id' => $post['main']['category_id'],
                'push_time' => now(),
                'content' => $post['main']['content'],
                'img' => $post['main']['img'],
                'seo_title' => $post['main']['seo_title'] ?? "",
                'seo_desc' => $post['main']['seo_desc'] ?? "",
                'seo_keyword' => $post['main']['seo_keyword'] ?? "",
                'push_status' => 1,
                'status' => 1,

            ], $post['expand'])->create(true, false, false);

        } catch (\Exception $exception) {

            report($exception);

            return Signature::fail(Signature::CONTENT_ERROR, $exception->getMessage());

        }

        $rule = $post['download']['rule'];

        $note = $post['download']['note'] ?? "";

        $downloadSite = DownloadSite::where('rule', $rule)->first();

        $downloadSiteId = 0;

        //设置下载服务器
        if (!$downloadSite) {

            $d = DownloadSite::create([
                'rule' => $rule,
                'note' => $note,
            ]);
            $downloadSiteId = $d->id;

        } else {

            $downloadSiteId = $downloadSite->id;
        }


        ArticleDownload::create([
            'article_id' => $article->id,
            'library_id' => $post['main']['library_article_id'],
            'apk_id' => $post['main']['library_apk_id'],
            'download_site_id' => $downloadSiteId,
            'file_path' => $post['download']['file_path'],
            'save_type' => $post['download']['save_type'],
            'pan_password' => $post['download']['pan_password']
        ]);


        //重新静态化，防止下载地址不出现
        $ag->articleStatic($article->id);


        return Signature::success([
            'id' => $article->id,
            'path' => parse_url(getDetailUrl($article))['path']
        ]);

    }

}
