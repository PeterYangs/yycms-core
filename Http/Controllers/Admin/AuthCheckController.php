<?php

namespace Ycore\Http\Controllers\Admin;

//use App\Http\Middleware\admin\Auth;

use Ycore\Http\Middleware\admin\Auth;

class AuthCheckController extends LoginCheckController
{


    public function __construct()
    {

        parent::__construct();


        $this->middleware(Auth::class);

    }


}
