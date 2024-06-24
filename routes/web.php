<?php


//后台图片访问
use App\Http\Controllers\Pc\HitsController;
use Ycore\Http\Controllers\YyCms;
use Ycore\Http\Middleware\home\ArticleSpecial;
use Ycore\Http\Middleware\home\HomeTag;
use Ycore\Http\Middleware\home\StaticRender;
use Ycore\Http\Middleware\home\UserAccess;
use Ycore\Models\ArticleDownload;
use Ycore\Tool\YRoute;

//dd(getOption('site_name'));
$enable_channel_domain = getOption('enable_channel_domain');

if ($enable_channel_domain == 1) {

    \Route::middleware([HomeTag::class, UserAccess::class, ArticleSpecial::class])->group(function () {


        \Route::get("/", make(\Ycore\Http\Controllers\Pc\Index::class, 'index'))->middleware(StaticRender::class)->name('pc.index');

        try {

            include_once base_path("routes/channel/pc.php");

        } catch (\Exception $exception) {

        }


        if (file_exists(base_path('theme/' . getOption('theme', 'demo') . '/pc/route/route.php'))) {

            include_once base_path('theme/' . getOption('theme', 'demo') . '/pc/route/route.php');

        }


        //网站地图
        Route::get("/sitemap/{name}.xml", function ($name) {


            try {

                return response()->file(storage_path('sitemap/pc/' . $name . ".xml"),
                    ['content-type' => 'text/xml; charset=utf-8']);

            } catch (\Exception $exception) {


                abort(404);
            }


        })->where(['name' => '[0-9A-Za-z\-]+']);


        //搜狗验证文件
        Route::get('/sogousiteverification.txt', function () {

            return response()->file(public_path('sougou/pc/sogousiteverification.txt'));
        });


        //全站链接
        Route::get('/links/links.txt', function () {


            return response()->file(storage_path('link/pc.txt'), ['content-type' => 'text/plain']);
        });


        Route::get("_common.js", function () {


            $statistics_pc = getOption("statistics_pc", "");

            return response($statistics_pc, 200, ['content-type' => 'text/javascript']);

        });


        Route::get("_detail-{id}.js", function ($id) {

            $all_js = "";

            $article = ArticleDetailModel()->where('id', $id)->first();

            if (!$article) {

                abort(404);
            }

            if (optional($article->special)->pc_js) {

                $all_js .= $article->special->pc_js;

            }

            return response($all_js, 200, ['content-type' => 'text/javascript']);

        });


    });

} else {
    YRoute::pcRoute(function () {


        //网站地图
        Route::get("/sitemap/{name}.xml", function ($name) {


            try {

                return response()->file(storage_path('sitemap/pc/' . $name . ".xml"),
                    ['content-type' => 'text/xml; charset=utf-8']);

            } catch (\Exception $exception) {


                abort(404);
            }


        })->where(['name' => '[0-9A-Za-z\-]+']);


        //搜狗验证文件
        Route::get('/sogousiteverification.txt', function () {

            return response()->file(public_path('sougou/pc/sogousiteverification.txt'));
        });


        //全站链接
        Route::get('/links/links.txt', function () {


            return response()->file(storage_path('link/pc.txt'), ['content-type' => 'text/plain']);
        });


        Route::get("_common.js", function () {


            $statistics_pc = getOption("statistics_pc", "");

            return response($statistics_pc, 200, ['content-type' => 'text/javascript']);

        });


        Route::get("_detail-{id}.js", function ($id) {

            $all_js = "";

            $article = ArticleDetailModel()->where('id', $id)->first();

            if (!$article) {

                abort(404);
            }

            if (optional($article->special)->pc_js) {

                $all_js .= $article->special->pc_js;

            }

            return response($all_js, 200, ['content-type' => 'text/javascript']);

        });


    });

    YRoute::mobileRoute(function () {

        //网站地图
        Route::get("/sitemap/{name}.xml", function ($name) {


            try {

                return response()->file(storage_path('sitemap/mobile/' . $name . ".xml"),
                    ['content-type' => 'text/xml; charset=utf-8']);

            } catch (\Exception $exception) {


                abort(404);
            }


        })->where(['name' => '[0-9A-Za-z\-]+']);


        //搜狗验证文件
        Route::get('/sogousiteverification.txt', function () {

            return response()->file(public_path('sougou/mobile/sogousiteverification.txt'));
        });

        //全站链接
        Route::get('/links/links.txt', function () {


            return response()->file(storage_path('link/mobile.txt'), ['content-type' => 'text/plain']);
        });


        Route::get("_common.js", function () {


            $statistics_mobile = getOption("statistics_mobile", "");

            return response($statistics_mobile, 200, ['content-type' => 'text/javascript']);

        });


        Route::get("_detail-{id}.js", function ($id) {

            $all_js = "";

            $article = ArticleDetailModel()->where('id', $id)->first();

            if (!$article) {

                abort(404);
            }

            if (optional($article->special)->mobile_js) {

                $all_js .= $article->special->mobile_js;

            }

            return response($all_js, 200, ['content-type' => 'text/javascript']);

        });


    });
}


Route::get('/backend/{path}.{ex}', function ($path, $ex) {


    switch (env('UPLOAD_TYPE')) {


        case "ali_oss":


            return response()->redirectGuest(env('IMAGE_DOMAIN') . "/uploads/" . $path . "." . $ex);


        default:

            if (Storage::disk('upload')->exists($path . "." . $ex)) {

                return response()->file(Storage::disk('upload')->path($path . "." . $ex));
            }

            abort(404);

    }


})->where(['path' => "[/A-Za-z0-9._]+", 'ex' => "(jpg|jpeg|png|gif|webp){1}"]);


//编辑器图片访问
Route::get("/api/uploads/{path}.{ex}", function ($path, $ex) {


    switch (env('UPLOAD_TYPE')) {


        case "ali_oss":


            return response()->redirectGuest(env('IMAGE_DOMAIN') . "/uploads/" . $path . "." . $ex);


        default:

            if (Storage::disk('upload')->exists($path . "." . $ex)) {

                return response()->file(Storage::disk('upload')->path($path . "." . $ex));
            }

            abort(404);

    }


})->where(['path' => "[/A-Za-z0-9._]+", 'ex' => "(jpg|jpeg|png|gif|webp){1}"]);


Route::get("/uploads/{path}.{ex}", function ($path, $ex) {


    switch (env('UPLOAD_TYPE')) {


        case "ali_oss":


            return response()->redirectGuest(env('IMAGE_DOMAIN') . "/uploads/" . $path . "." . $ex);


        default:

            if (Storage::disk('upload')->exists($path . "." . $ex)) {

                return response()->file(Storage::disk('upload')->path($path . "." . $ex));
            }

            abort(404);

    }


})->where(['path' => "[/A-Za-z0-9._]+", 'ex' => "(jpg|jpeg|png|gif|webp){1}"]);


//二维码
Route::get('/qrcode/{id}', [YyCms::class, 'build'])->name('qrcode');


Route::get('/download/{type}/{id}',
    [YyCms::class, 'download'])->where(['type' => '(az|ios){1}'])->name('download');


//文章点击
Route::get('/hits/{id}', [YyCms::class, 'add'])->where(['id' => "[0-9]+"]);


Route::get('/now', function () {


    return now()->format("Y-m-d H:i:s");
});


Route::get("search-article-baidu-check", function () {

    $id = (int)request()->input('id');


    if (!$id) {


        abort(404);


    }


    $item = \Ycore\Models\SearchArticle::where('id', $id)->firstOrFail();


    $html = \QL\QueryList::html($item->content);

    $text = $html->find("")->text();


    $key = mb_substr($text, 0, 37);


    $url = "https://www.baidu.com/s?wd=" . urlencode($key);


    return response()->redirectGuest($url);

});


Route::get('beian', function () {


    return view('_beian');
});


Route::get('_hit', function () {

    $id = request()->query('id', 0);

    \DB::table('article')->where('id', $id)->increment('hits');


});

Route::get('_beian.js', function () {


    return response()->file(dirname(__DIR__) . "/asset/_beian.js", ['Content-Type' => 'application/javascript']);

});


Route::get('_js_hide.js', function () {

    return response()->file(dirname(__DIR__) . "/asset/_js_hide.js", ['Content-Type' => 'application/javascript']);
});


Route::middleware(['throttle:download'])->get('__download/{article_download_id}', function ($article_download_id) {
    $articleDownload = ArticleDownload::with('download_site')->findOrFail($article_download_id);

    if (!$articleDownload->download_site) {

        abort(404);
    }

    $url = str_replace("{path}", $articleDownload->file_path, $articleDownload->download_site->rule);

    return redirect()->away($url, 302);
});


Route::get("7ef0fcb958e24e2f9c54ecabcfdd9cd2.txt", function () {

    return "7ef0fcb958e24e2f9c54ecabcfdd9cd2";
});


Route::get("_www_death.txt", function () {


    return response()->file(storage_path('app/public/www-death.txt'));
});

Route::get("_m_death.txt", function () {


    return response()->file(storage_path('app/public/m-death.txt'));
});

