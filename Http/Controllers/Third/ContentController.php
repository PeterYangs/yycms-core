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
            'title' => 'required',
            'category_id' => 'required|integer',
            'content' => 'required',
            'img' => 'required',
        ], [
            'required' => ':attribute 字段必填',
            'integer' => ':attribute 字段必须是数字'
        ]);

        if ($validator->fails()) {
            return response(Signature::fail(Signature::PARAMS_ERROR, $validator->errors()->first()));
        }


//        $ag = new ArticleGenerator();
//
//        $ag->fill([], []);
//
//        $ag->create();

    }

}
