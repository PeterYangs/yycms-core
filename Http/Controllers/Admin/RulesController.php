<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Rules;
use Ycore\Tool\Json;

class RulesController extends AuthCheckController
{

    function update()
    {

        $post = request()->post();

        $id = $post['id'] ?? null;

        Rules::updateOrCreate(['id' => $id], $post);

        return Json::code(1, 'success');

    }


    function list()
    {

        $list = Rules::orderBy('id');

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');


        $item = Rules::where('id', $id)->firstOrFail();


        return Json::code(1, 'success', $item);

    }


    function destroy()
    {

        $id = (int)request()->input('id');


        Rules::destroy($id);


        return Json::code(1, 'success');
    }


}
