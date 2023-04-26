<?php

namespace Ycore\Http\Controllers\Admin;

//use App\Http\Middleware\admin\Login;

use Ycore\Http\Middleware\admin\Login;

class LoginCheckController extends BaseController
{


    public function __construct()
    {

//        $this->middleware('login');


        $this->middleware(Login::class);

    }

}
