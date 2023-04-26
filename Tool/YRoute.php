<?php

namespace Ycore\Tool;

use Ycore\Http\Middleware\home\ArticleSpecial;
use Ycore\Http\Middleware\home\StaticRender;
use Ycore\Http\Middleware\home\UserAccess;

class YRoute
{


    public static function pcRoute($callback)
    {

//        \Route::

        \Route::domain(parse_url(getOption('domain'))['host'] ?? "")->middleware([UserAccess::class, ArticleSpecial::class])->group(function () use ($callback) {


            try {

                include_once base_path("routes/channel/pc.php");

            } catch (\Exception $exception) {

            }


            $callback();


        });


    }


}
