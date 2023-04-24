<?php

namespace Ycore\Http\Controllers\Admin;


use App\Tool\Json;

class HomeController extends AuthCheckController
{

    function spiderTable()
    {


        return Json::code(1, 'success', \Cache::get('spider_table', []));
    }

    /**
     * 访问量
     */
    function access()
    {


        return Json::code(1, 'success', \Cache::get('access', []));
    }


    function search()
    {


        return Json::code(1, 'success', \Cache::get('search_access', []));

    }

}
