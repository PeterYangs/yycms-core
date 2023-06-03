<?php

namespace Ycore\Tool;

use Illuminate\Pagination\Paginator;
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


            \Route::get("/", make(\Ycore\Http\Controllers\Pc\Index::class, 'index'))->middleware(StaticRender::class)->name('pc.index');

            try {

                include_once base_path("routes/channel/pc.php");

            } catch (\Exception $exception) {

            }


            if (file_exists(base_path('theme/' . getOption('theme', 'demo') . '/pc/route/route.php'))) {

                include_once base_path('theme/' . getOption('theme', 'demo') . '/pc/route/route.php');

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


            \Route::get("/", make(\Ycore\Http\Controllers\Mobile\Index::class, 'index'))->middleware(StaticRender::class)->name('mobile.index');

            try {

                include_once base_path("routes/channel/mobile.php");

            } catch (\Exception $exception) {

            }

            if (file_exists(base_path('theme/' . getOption('theme', 'demo') . '/mobile/route/route.php'))) {

                include_once base_path('theme/' . getOption('theme', 'demo') . '/mobile/route/route.php');

            }


            $callback();


        });

    }


}
