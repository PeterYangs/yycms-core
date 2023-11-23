<?php

namespace Ycore\Http\Controllers\Third;

use Ycore\Tool\Category;
use Ycore\Tool\Json;
use Ycore\Tool\Signature;

class CategoryController extends BaseController
{


    function category()
    {


        return Signature::success(Category::getCategoryByPid(0));

    }


}
