<?php

namespace Ycore\Http\Controllers\Admin;

use App\Tool\Json;

class MenuController extends AuthCheckController
{

    /**
     * @Auth(type='no_check')
     * 菜单列表
     * Create by Peter Yang
     * 2022-06-20 14:25:10
     */
    function getMenu()
    {


        return Json::code(1, 'success', config('menu'));

    }

}
