<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Json;
use Illuminate\Support\Facades\Route;

class RouteController extends AuthCheckController
{

    /**
     * 获取路由提示
     * Create by Peter Yang
     * 2022-06-20 11:01:40
     * @return string
     */
    function getRouteTip(){

        $keyword=request()->input('keyword',null);

        if(!$keyword) return Json::code(1,'no match',[]);

        $uriList=[];



        foreach (Route::getRoutes() as $v){


            $uri=$v->uri;


            if(explode('/',$uri)[0] !== 'admin') {
                continue;
            }

            if(stripos($uri, $keyword) === false) {
                continue;
            }

            $uriList[]=['value'=>'/'.$uri];

        }

        return Json::code(1,Route::getRoutes(),$uriList);
    }

}
