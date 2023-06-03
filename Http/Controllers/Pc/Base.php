<?php

namespace Ycore\Http\Controllers\Pc;

use Illuminate\Pagination\Paginator;
use Ycore\Http\Controllers\YyCms;

class Base extends YyCms
{

    public function __construct()
    {
        parent::__construct();

        \View::addLocation(base_path('theme/' . getOption('theme', 'demo') . '/pc/view'));

    }


    function getViewPath()
    {

        return base_path('theme/' . getOption('theme', 'demo') . '/pc/view');
    }


}
