<?php

namespace Ycore\Tool;

class Download
{

    /**
     * 处理下载链接
     * @param $url
     * @return string
     */
    static function dealUrl($url): string
    {
        $p = parse_url($url);
        if ($p["host"] !== config('yycms.wp_host')) {
            return $url;
        }
        $private_key = config('yycms.wp_secret');
        $uid = config('yycms.wp_uid');
        $expire_time = time() + 60;   // 该签发的资源30s以后过期
        $rand_value = rand(0, 100000); // 生成随机数
        $parse_result = parse_url($url); // 解析 URL
        $request_path = rawurldecode($parse_result["path"]); // /29/音乐/02.一千零一夜-李克勤.wma
        $sign = md5(sprintf("%s-%d-%d-%d-%s", $request_path, $expire_time, $rand_value, $uid, $private_key));
        $wait = sprintf("%d-%d-%d-%s", $expire_time, $rand_value, $uid, $sign);
        return $url . "?auth_key=" . $wait;
    }

}
