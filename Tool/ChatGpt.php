<?php

namespace Ycore\Tool;

class ChatGpt
{


    public static function do(string $keyword): string
    {

        $address = env('CHAT_GPT_ADDRESS', null);

        if (!$address) {

            throw new \Exception("chatgpt地址配置错误，请检查");
        }

        $rsp = \Http::get(str_replace("{keyword}", urlencode($keyword), $address));


        if ($rsp->status() !== 200) {

            throw new \Exception("请求chatgpt接口错误(" . $rsp->status() . ")," . $rsp->body());

        }

        return $rsp->body();


    }


}
