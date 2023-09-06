<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\ErrorAccess;
use Ycore\Models\Rules;
use Ycore\Tool\Json;

class SeoController extends AuthCheckController
{


    function errorAccessList()
    {

        $list = ErrorAccess::orderBy('id','desc');

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


}
