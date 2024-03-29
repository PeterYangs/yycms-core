<?php

namespace Ycore\Service\Upload;

interface Upload
{

    /**
     * 上传文件
     * Create by Peter Yang
     * 2022-09-29 14:20:46
     * @param array $files
     * @param string $upload_path
     * @param bool $is_watermark 是否添加水印
     * @return mixed
     */
    function upload(array $files, string $upload_path = "", bool $is_watermark = false);


    /**
     * 百度编辑器上传
     * Create by Peter Yang
     * 2022-09-29 15:56:04
     * @param array $files
     * @return mixed
     */
    function ueditor(array $files);


    /**
     * 上传远程文件
     * @param $url
     * @return mixed
     */
    function uploadRemoteFile($url);

}
