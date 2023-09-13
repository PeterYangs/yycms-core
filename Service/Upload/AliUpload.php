<?php

namespace Ycore\Service\Upload;

use Illuminate\Support\Facades\Validator;
use Ycore\Core\Core;
use Ycore\Tool\Json;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use OSS\Core\OssException;
use OSS\OssClient;

class AliUpload implements Upload
{

    protected OssClient $ossClient;

    public function __construct()
    {
        $accessKeyId = env('ALI_KEY', "");
        $accessKeySecret = env("ALI_SECRET", "");
        $endpoint = env("ALI_ENDPOINT", "");


        // true为开启CNAME。CNAME是指将自定义域名绑定到存储空间上。
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, true);


        $this->ossClient = $ossClient;


    }


    function upload(array $files, string $upload_path = "", bool $is_watermark = false)
    {
        // TODO: Implement upload() method.


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


            try {

                //储存到临时文件
                Storage::put($fileName, file_get_contents($realPath));


                $watermark = getOption('watermark', null);


                //添加水印
                if ($is_watermark && getOption('open_watermark') === 1 && $watermark) {


                    $image = Image::make(Storage::path($fileName));

                    $waterWidth = $image->getWidth() / 3;

                    //图片太小就不添加水印了
                    if ($waterWidth >= 10) {

                        $water = Image::make(Storage::disk('upload')->get(getOption('watermark')));

                        //设置水印图片大小
                        $water->resize($waterWidth, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });

                        //设置在右下角
                        $image->insert($water, 'bottom-right', 10, 10);


                        $image->save();

                    }

                }


                $this->ossClient->putObject(env("ALI_BUCKET_NAME", ""), rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName,
                    Storage::get($fileName));

                $this->ossClient->putSymlink(env("ALI_BUCKET_NAME", ""), "api/" . rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName,
                    rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName);

                $file_array[] = $fileName;

            } finally {

                //删除临时文件
                Storage::delete($fileName);

            }

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


//            $watermark = getOption('watermark', null);
//
//
////            $open_watermark = getOption('open_watermark', 0);
//
//
//            //添加水印
//            if (getOption('open_watermark') === 1 && $watermark && Storage::disk('upload')->exists($watermark)) {
//
//                $image = Image::make(public_path(rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName));
//
//                $waterWidth = $image->getWidth() / 3;
//
//                //图片太小就不添加水印了
//                if ($waterWidth >= 10) {
//
//                    $water = Image::make(Storage::disk('upload')->path(getOption('watermark')));
//
//                    //设置水印图片大小
//                    $water->resize($waterWidth, null, function ($constraint) {
//                        $constraint->aspectRatio();
//                        $constraint->upsize();
//                    });
//
//                    //设置在右下角
//                    $image->insert($water, 'bottom-right', 10, 10);
//
//                    $image->save();
//
//                }
//
//            }

        }

        $path = resource_path('Ueditor');


        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($path . "/config.json")), true);
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);


                return $result;


            case "uploadimage":

                foreach ($files as $key => $file) {


                    # 扩展名
                    $ext = $file->getClientOriginalExtension();

                    $allowList = env('ALLOW_UPLOAD_TYPE', 'png,gif,jpg,jpeg');

                    $allowList = explode(',', $allowList);

                    if (!in_array(strtolower($ext), $allowList)) {
                        return Json::code(2, '不允许上传该类型文件,' . $ext);
                    }


                    # 临时绝对路径
                    $realPath = $file->getRealPath();

                    # 自定义文件名
                    $fileName = date('Ymd') . '/' . uniqid('', true) . '.' . $ext;


                    $this->ossClient->putObject(env("ALI_BUCKET_NAME", ""), rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName,
                        file_get_contents($realPath));


                    $this->ossClient->putSymlink(env("ALI_BUCKET_NAME", ""), "api/" . rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName,
                        rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName);


                    return [
                        'original' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'state' => "SUCCESS",
                        'type' => $ext,
                        'url' => "/uploads/" . $fileName

                    ];


                }


                break;


            //处理远程图片
            case "catchimage":

                $result = ['state' => "SUCCESS", 'list' => []];

                $client = new Client([
                    'headers' => ['User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36"],
                    'timeout' => 15,
                ]);

                //远程图片列表
                $source = request()->input('source');


                foreach ($source as $key => $value) {


                    try {

                        $body = $client->get($value)->getBody();

                        $size = $body->getSize();

                        $img = $body->getContents();

                        $fileName = date('Ymd') . '/' . uniqid('', true) . '.png';

                        $this->ossClient->putObject(env("ALI_BUCKET_NAME", ""), "uploads/" . $fileName,
                            $img);


                        $this->ossClient->putSymlink(env("ALI_BUCKET_NAME", ""), "api/uploads/" . $fileName,
                            "uploads/" . $fileName);


                        $result['list'][] = [
                            'original' => $fileName,
                            'size' => $size,
                            'source' => $value,
                            'state' => 'SUCCESS',
                            'title' => $fileName,
                            "url" => "uploads/" . $fileName
                        ];


                    } catch (\Exception $exception) {


                        $result['list'][] = [
                            'original' => "",
                            'size' => null,
                            'source' => $value,
                            'state' => '链接不可用',
                            'title' => "",
                            "url" => null,
                            'msg' => $exception->getMessage(),
                        ];

                    }


                }


                return $result;


        }


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

        $this->ossClient->putObject(env("ALI_BUCKET_NAME", ""), rtrim(config('yycms.upload_prefix'), '/') . "/" . $fileName,
            $rsp);

        $this->ossClient->putSymlink(env("ALI_BUCKET_NAME", ""), "api/" . ltrim(rtrim(config('yycms.upload_prefix'), '/'), '/') . "/" . $fileName,
            ltrim(rtrim(config('yycms.upload_prefix'), '/'), '/') . $fileName);


        return "/" . ltrim(rtrim(config('yycms.upload_prefix'), '/'), '/') . "/" . $fileName;


    }
}
