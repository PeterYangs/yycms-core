<?php


//后台图片访问
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


