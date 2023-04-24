<?php

namespace Ycore\Http\Controllers\Admin;

class AuthCheckController extends LoginCheckController
{


    public function __construct()
    {

        parent::__construct();

        $this->middleware('powerAuth');

    }


}
