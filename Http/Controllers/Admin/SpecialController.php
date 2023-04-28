<?php

namespace Ycore\Http\Controllers\Admin;


use Ycore\Models\Special;
use Ycore\Tool\Json;

class SpecialController extends AuthCheckController
{


    function list()
    {

        $list = Special::orderBy('id');

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function update()
    {

        $post = request()->input();

        $id = $post['id'] ?? 0;


        Special::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');


    }

}
