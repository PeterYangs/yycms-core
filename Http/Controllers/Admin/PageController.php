<?php

namespace Ycore\Http\Controllers\Admin;


use Ycore\Models\Page;
use Ycore\Tool\Json;

class PageController extends AuthCheckController
{

    function update(){

        $id=request()->input('id');

        $post=request()->input();


        Page::updateOrCreate(['id'=>$id],$post);


        return Json::code(1,'success');


    }


    function list(){


        $list=Page::orderBy('id','desc');


        return Json::code(1,'success',paginate($list,request()->input('p',1)));

    }


    function detail(){

        $id=request()->input('id');


        $data=Page::find($id);


        return Json::code(1,'success',$data);

    }

    function delete(){

        $id=request()->input('id');

        $id=(int)$id;

        Page::destroy($id);


        return Json::code(1,'success');


    }


}
