<?php

namespace Ycore\Http\Controllers\Third;

use Ycore\Tool\Category;
use Ycore\Tool\Json;

class CategoryController extends BaseController
{


    function category()
    {


        return Json::code(1, 'success', Category::infiniteClassification(['id', 'pid', 'lv', 'name']));

    }


}
