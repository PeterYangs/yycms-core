<?php

namespace Ycore\Http\Controllers\Admin;

class LoginCheckController extends BaseController
{


    public function __construct()
    {

        $this->middleware('login');

    }

}
