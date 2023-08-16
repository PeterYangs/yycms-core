<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Json;
use Ycore\Tool\Sitemap;

class SitemapController extends AuthCheckController
{


    function create()
    {


        \Artisan::call('MakeXml');


        return Json::code(1, 'success');

    }


    function list()
    {


        return Json::code(1, 'success', Sitemap::getSitemapList());

    }


}
