<?php

namespace Ycore\Service\Upload;

use Illuminate\Support\Facades\Validator;
use Ycore\Core\Core;
use Ycore\Tool\Json;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * 本地文件上传
 */
class LocalUpload implements Upload
{

    public function upload(array $files, string $upload_path = "")
    {
        // TODO: Implement Upload() method.

        $file_array = [];

        foreach ($files as $key => $file) {

            # 原文件名
            $originalName = $file->getClientOriginalName();

            # 扩展名
            $ext = $file->getClientOriginalExtension();

            $size = $file->getSize();

            $allowList = env('ALLOW_UPLOAD_TYPE', 'png,gif,jpg,jpeg');

            $allowList = explode(',', $allowList);

            if (!in_array(strtolower($ext), $allowList)) {

                throw new \Exception('不允许上传该类型文件,' . $ext);

            }

            # Mimetype
            $type = $file->getClientMimeType();

            # 临时绝对路径
            $realPath = $file->getRealPath();

            # 自定义文件名
            $fileName = (($upload_path === "") ? "" : $upload_path . "/") . date('Ymd') . '/' . uniqid('',
                    true) . '.' . $ext;

            # 选择磁盘
            Storage::disk('upload')->put($fileName, file_get_contents($realPath));


            //图片压缩
            if (request()->input('resize') > 0 && in_array($ext,
                    ['png', 'jpg', 'jpeg']) && $size > request()->input('maxResizeByte')) {


                Image::make(public_path(rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName))->resize(request()->input('resize'), null,
                    function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save(public_path(rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName));

            }

            $file_array[] = $fileName;

        }

        return $file_array;

    }

    function ueditor(array $files)
    {
        // TODO: Implement ueditor() method.


        foreach ($files as $key => $file) {


            # 扩展名
            $ext = $file->getClientOriginalExtension();

            $allowList = env('ALLOW_UPLOAD_TYPE', 'png,gif,jpg,jpeg');

            $allowList = explode(',', $allowList);

            if (!in_array(strtolower($ext), $allowList)) {


                throw new \Exception('不允许上传该类型文件,' . $ext);

            }

        }


        $path = resource_path('Ueditor');


        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($path . "/config.json")), true);
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':

                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = include($path . "/action_upload.php");
                break;

            /* 列出图片 */
            case 'listimage':
                $result = include($path . "/action_list.php");
                break;
            /* 列出文件 */
            case 'listfile':
                $result = include($path . "/action_list.php");
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = include($path . "/action_crawler.php");
                break;

            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                return htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            }

            return json_encode(array(
                'state' => 'callback参数不合法'
            ), JSON_THROW_ON_ERROR);
        }

        return $result;


    }

    function uploadRemoteFile($url)
    {
        // TODO: Implement uploadRemoteFile() method.
        $va = Validator::make(['url' => $url], [
            'url' => 'required|url'
        ]);

        if ($va->fails()) {

            throw new \Exception($va->errors()->first());
        }

        $ext = pathinfo($url, PATHINFO_EXTENSION);


        $allowList = env('ALLOW_UPLOAD_TYPE', 'png,gif,jpg,jpeg');

        $allowList = explode(',', $allowList);

        if (!in_array(strtolower($ext), $allowList)) {

            throw new \Exception('不允许上传该类型文件:' . $ext);

        }

        $fileName = date('Ymd') . '/' . uniqid('',
                true) . '.' . $ext;

        $rsp = \Http::withOptions(['verify' => false])->timeout(15)->connectTimeout(10)->get($url);

        Storage::disk('upload')->put($fileName, $rsp);


        return "/" . ltrim(rtrim(config('yycms.upload_prefix'), '/'), '/') . "/" . $fileName;

    }
}
