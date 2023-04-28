<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\AutoPush;
use Ycore\Tool\Json;

class AutoPushController extends AuthCheckController
{

    function update()
    {

        $post = request()->input();


        $id = $post['id'] ?? 0;


        AutoPush::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');


    }


    function list()
    {

        $list = AutoPush::with('category')->orderBy('id', 'desc');

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');

        $item = AutoPush::where('id', $id)->firstOrFail();


        return Json::code(1, 'success', $item);

    }


    /**
     * 禁用
     * Create by Peter Yang
     * 2023-02-09 11:53:06
     * @return string
     */
    function disable()
    {


        $id = request()->input('id');


        $item = AutoPush::where('id', $id)->firstOrFail();


        $item->status = 2;

        $item->save();


        return Json::code(1, 'success');


    }


    /**
     * 启用
     * Create by Peter Yang
     * 2023-02-09 11:53:23
     * @return string
     */
    function open()
    {

        $id = request()->input('id');


        $item = AutoPush::where('id', $id)->firstOrFail();


        $item->status = 1;

        $item->save();


        return Json::code(1, 'success');

    }


    function destroy()
    {


        $id = request()->input('id');

        $id = (int)$id;

        AutoPush::destroy($id);


        return Json::code(1, 'success');


    }


}
