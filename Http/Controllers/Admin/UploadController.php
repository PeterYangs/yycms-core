<?php

namespace Ycore\Http\Controllers\Admin;

use Ramsey\Uuid\Uuid;
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

        //是否添加水印
        $watermark = request()->input('watermark') === "1";

        $upload_path = request()->input('upload_path', "");

        $file_array = $upload->upload($all_file, $upload_path, $watermark);


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


    /**
     * 文件上传(大文件上传)
     * Create by Peter Yang
     * 2023-07-16 11:45:06
     */
    function uploadFile()
    {

        $name = request()->input('name');

        //获取文件拓展名
        $extension = pathinfo($name)['extension'];

        $allowList = env('ALLOW_UPLOAD_TYPE', 'png,gif,jpg,jpeg,apk,zip');

        $allowList = explode(',', $allowList);

        if (!in_array(strtolower($extension), $allowList)) {


            return Json::code(2, '不允许上传该类型文件');
        }

        //切割文件对象
        $file = request()->file('file');

        //上传总数
        $total = request()->input('total');

        //当前块数
        $current = request()->input('current');

        //临时文件夹名称
        $dir_name = request()->input('dir_name');


        if ($current == 1) {

            //生成临时文件夹名称
            $uuid = Uuid::uuid1()->toString();

            $dir_name = $uuid;

            $path = storage_path('uploadTemp/' . $dir_name);

            if (!is_dir($path)) {

                mkdir($path, 0755, true);
            }

        }

        # 临时绝对路径
        $realPath = $file->getRealPath();

        //移动文件到大文件临时目录
        move_uploaded_file($realPath, storage_path('uploadTemp/' . $dir_name) . '/temp' . $current);

        //上传完成，开始合并
        if ($current == $total) {

            //临时文件路径
            $dir_path = storage_path('uploadTemp/' . $dir_name);

            //上传文件夹名
            $upload_dir = date('Ymd');

            //生成文件名称
            $fileName = uniqid() . '.' . $extension;

            for ($i = 0; $i < $total; $i++) {

                //获取临时文件数据
                $t = file_get_contents($dir_path . '/temp' . ($i + 1));


                if ($i == 0) {
                    //不存在就创建文件夹
                    if (!is_dir(public_path('uploads/' . $upload_dir))) {


                        mkdir(public_path('uploads/' . $upload_dir), 0777, true);
                    }

                }

                //追加写入
                file_put_contents(public_path('uploads/' . $upload_dir) . '/' . $fileName, $t, FILE_APPEND);

                //删除临时文件
                unlink($dir_path . '/temp' . ($i + 1));


            }

            //删除临时文件夹
            rmdir($dir_path);


            return Json::code(1, '上传成功', $upload_dir . '/' . $fileName);


        }


        return Json::code(1, 'success', ['dir_name' => $dir_name]);

    }


}
