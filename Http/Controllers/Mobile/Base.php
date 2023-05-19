<?php

namespace Ycore\Http\Controllers\Mobile;

use Ycore\Http\Controllers\YyCms;

class Base extends YyCms
{

    public function __construct()
    {
        parent::__construct();

        \View::addLocation(base_path('theme/demo/pc/view'));
    }

}
