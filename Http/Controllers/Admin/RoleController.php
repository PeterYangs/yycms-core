<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Role;
use Ycore\Models\Rules;
use App\Tool\Json;

class RoleController extends AuthCheckController
{


    function allRules()
    {

        $list = Rules::all();


        $list?->toArray();

        $list = groupByKey($list, 'group_name');

        return Json::code(1, 'success', $list);


    }


    /**
     * Create by Peter Yang
     * 2022-06-20 14:40:55
     * @return string
     */
    function update()
    {


        $post = request()->post();

        $id = $post['id'] ?? null;

        Role::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');

    }


    function list()
    {


        $list = Role::orderBy('id', 'desc');


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');

        $item = Role::where('id', $id)->firstOrFail();


        return Json::code(1, 'success', $item);

    }


}
