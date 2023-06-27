<?php


//后台图片访问
use App\Http\Controllers\Pc\HitsController;
use Ycore\Http\Controllers\YyCms;
use Ycore\Tool\YRoute;


YRoute::pcRoute(function () {


    //网站地图
    Route::get('/sitemap/{sitemap}.xml', function ($sitemap) {


        try {

            return response()->file(storage_path('sitemap/pc/' . $sitemap . ".xml"),
                ['content-type' => 'text/xml; charset=utf-8']);

        } catch (\Exception $exception) {


            abort(404);
        }


    })->where(['sitemap' => '[0-9a-z]+']);


    Route::get("/sitemap{index}.xml", function ($index) {


        try {

            return response()->file(storage_path('sitemap/pc/sitemap' . $index . ".xml"),
                ['content-type' => 'text/xml; charset=utf-8']);

        } catch (\Exception $exception) {


            abort(404);
        }


    })->where(['index' => '[0-9]+']);


    //搜狗验证文件
    Route::get('/sogousiteverification.txt', function () {

        return response()->file(public_path('sougou/pc/sogousiteverification.txt'));
    });


    //全站链接
    Route::get('/links/links.txt', function () {


        return response()->file(storage_path('link/pc.txt'), ['content-type' => 'text/plain']);
    });


});

YRoute::mobileRoute(function () {

    //网站地图
    Route::get('/sitemap/{sitemap}.xml', function ($sitemap) {


        try {


            return response()->file(storage_path('sitemap/mobile/' . $sitemap . ".xml"),
                ['content-type' => 'text/xml; charset=utf-8']);

        } catch (\Exception $exception) {


            abort(404);
        }


    })->where(['sitemap' => '[0-9a-z]+']);


    Route::get("/sitemap{index}.xml", function ($index) {


        try {

            return response()->file(storage_path('sitemap/mobile/sitemap' . $index . ".xml"),
                ['content-type' => 'text/xml; charset=utf-8']);

        } catch (\Exception $exception) {


            abort(404);
        }


    })->where(['index' => '[0-9]+']);


    //搜狗验证文件
    Route::get('/sogousiteverification.txt', function () {

        return response()->file(public_path('sougou/mobile/sogousiteverification.txt'));
    });

    //全站链接
    Route::get('/links/links.txt', function () {


        return response()->file(storage_path('link/mobile.txt'), ['content-type' => 'text/plain']);
    });


});


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

//二维码
Route::get('/qrcode/{id}', [YyCms::class, 'build'])->name('qrcode');


Route::get('/download/{type}/{id}',
    [YyCms::class, 'download'])->where(['type' => '(az|ios){1}'])->name('download');


//文章点击
Route::get('/hits/{id}', [YyCms::class, 'add']);


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

    return \Ycore\Tool\Json::code(200, 'success');

});

Route::get('_beian.js', function () {


    return response()->file(dirname(__DIR__) . "/asset/_beian.js", ['Content-Type' => 'application/javascript']);

});


Route::get('_js_hide.js', function () {

    return response()->file(dirname(__DIR__) . "/asset/_js_hide.js", ['Content-Type' => 'application/javascript']);
});

