<?php

namespace Ycore\Http\Controllers\Mobile;

use Ycore\Http\Controllers\YyCms;

class Base extends YyCms
{

    public function __construct()
    {
        parent::__construct();

    }

    function getViewPath()
    {

        return base_path('theme/' . getOption('theme', 'demo') . '/mobile/view');
    }

}
