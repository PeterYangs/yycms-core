<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\ExpandChange;
use Ycore\Tool\Json;

class ExpandChangeController extends AuthCheckController
{


    function update()
    {

        $id = request()->input('id');

        $post = request()->input();

        if ($post['category_id'] === "") {
            $post['category_id'] = 0;
        }

        ExpandChange::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');

    }

    function list()
    {


        $list = ExpandChange::with('category')->with('special')->orderBy('id', 'desc');


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');


        $data = ExpandChange::find($id);


        return Json::code(1, 'success', $data);

    }

}
