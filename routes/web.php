<?php


//后台图片访问
use App\Http\Controllers\Pc\HitsController;
use Ycore\Http\Controllers\YyCms;
use Ycore\Tool\YRoute;


YRoute::pcRoute(function () {

});

YRoute::mobileRoute(function () {

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


    return view('beian');
});
