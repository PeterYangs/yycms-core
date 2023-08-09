<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Json;

class InstallController extends BaseController
{

    function configSave()
    {

        $post = request()->post();


        return Json::code(1, 'success', $post);

    }


}
