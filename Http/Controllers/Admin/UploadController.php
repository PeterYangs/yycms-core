<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Service\Upload\Upload;
use Ycore\Tool\Json;
use DebugBar\DebugBar;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends LoginCheckController
{


    /**
     * 上传普通图片
     * @return false|string
     */
    function uploadNormal(Upload $upload)
    {


        $all_file = request()->allFiles();

        $upload_path = request()->input('upload_path', "");

        $file_array = $upload->upload($all_file, $upload_path);


        return Json::code(1, 'success', $file_array);


    }


    /**
     * 百度编辑器上传
     *
     * Create by PeterYang
     * 2020/08/06 20:32:56
     * Email:904801074@qq.com
     */
    function ueditor(Upload $upload)
    {

        \Debugbar::disable();

        $all_file = request()->allFiles();



        return $upload->ueditor($all_file);


    }


}
