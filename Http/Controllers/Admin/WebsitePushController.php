<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\WebsitePush;
use Ycore\Tool\Json;

class WebsitePushController extends AuthCheckController
{


    function list()
    {

        $list = WebsitePush::orderBy('id');

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


}
