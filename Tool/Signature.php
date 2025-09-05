<?php

namespace Ycore\Tool;

class Signature
{

    //签名错误
    public const SIGNATURE_ERROR = 10000;

    //时间验证出错
    public const TIME_CHECK_ERROR = 10001;

    //请求参数出错
    public const PARAMS_ERROR = 10002;

    //内容添加错误
    public const CONTENT_ERROR = 10003;

    //站点内部错误
    public const WEBSITE_ERROR = 10004;


    public static function decrypt($time, $echostr, $app_id, $secret, $signature)
    {
        $params = [$time, $echostr, $app_id, $secret];
        sort($params);
        $params = implode('&', $params);
        $hmac = hash_hmac('sha256', $params, $secret);
        return hash_equals($hmac, $signature);
    }


    public static function encrypt($appid, $secret)
    {
        # 获取当前时间，服务端验证时间正负误差不得大于5分钟
        $time = date("Y-m-d H:i:s");
        # 生成随机校验串
        $echostr = \Str::random(16);
        # 组合一起，如果当前请求中还有其他GET参数需要一并组合校验
        $params = array_values(compact('time', 'echostr', 'secret', 'appid'));
        # 自然排序法排序
        sort($params);
        # 组装为字符串参数
        $p = join('&', $params);
        # 通过hash_hmac函数sha256加密请求参数体，秘钥作为秘钥
        $signature = hash_hmac('sha256', $p, $secret);
        # 组装与打印参数路径字符串
        return http_build_query(array_map(function ($value) {
            //return urlencode($value);
            return $value;
        }, compact('time', 'echostr', 'appid', 'signature')));
    }


    public static function success($data)
    {
        //记录日志
        return ['status' => 'success', 'code' => 200, 'data' => $data];
    }


    public static function fail($code, $message)
    {
        //记录日志
        return ['status' => 'error', 'code' => $code, 'message' => $message];
    }

}
