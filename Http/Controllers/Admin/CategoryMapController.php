<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\CategoryMap;
use App\Tool\Json;
use App\Tool\Search;

class CategoryMapController extends AuthCheckController
{


    function update()
    {

        $post = request()->input();

        $id = $post['id'] ?? 0;


        $categoryMap = CategoryMap::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');
    }


    function list()
    {


        $list = CategoryMap::orderBy('id', 'desc');

        Search::searchList($list, request()->input('search', '[]'));

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));
    }


    function detail()
    {

        $id = request()->input('id');


        $data = CategoryMap::find($id);

        return Json::code(1, 'success', $data);
    }


    /**
     * 下拉框列表
     * Create by Peter Yang
     * 2022-09-03 17:36:42
     * @return string
     */
    function selectList()
    {


        return Json::code(1, 'success', CategoryMap::get());
    }

}
