<?php

namespace Ycore\Tool;

use Ycore\Http\Middleware\home\ArticleSpecial;
use Ycore\Http\Middleware\home\StaticRender;
use Ycore\Http\Middleware\home\UserAccess;

class YRoute
{


    public static function pcRoute($callback)
    {


        try {

            $domain = parse_url(getOption('domain'))['host'] ?? "";

        } catch (\Exception $exception) {

            return;
        }


        \Route::domain($domain)->middleware([UserAccess::class, ArticleSpecial::class])->group(function () use ($callback) {


            try {

                include_once base_path("routes/channel/pc.php");

            } catch (\Exception $exception) {

            }


            $callback();


        });


    }


    public static function mobileRoute($callback)
    {


        try {

            $domain = parse_url(getOption('m_domain'))['host'] ?? "";

        } catch (\Exception $exception) {

            return;
        }


        \Route::domain($domain)->middleware([UserAccess::class, ArticleSpecial::class])->group(function () use ($callback) {


            try {

                include_once base_path("routes/channel/mobile.php");

            } catch (\Exception $exception) {

            }


            $callback();


        });

    }


}
