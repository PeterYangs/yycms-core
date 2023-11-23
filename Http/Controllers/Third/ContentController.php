<?php

namespace Ycore\Http\Controllers\Third;


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

        ], $post['expand'])->create();


        return Signature::success([
            'id' => $article->id,
            'path' => getDetailUrl($article)
        ]);

    }

}
