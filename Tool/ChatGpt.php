<?php

namespace Ycore\Tool;

class ChatGpt
{


    /**
     * 直接生成
     * @param string $keyword
     * @return string
     * @throws \Exception
     */
    public static function do(string $keyword): string
    {

        $address = env('CHAT_GPT_ADDRESS', null);

        if (!$address) {

            throw new \Exception("chatgpt地址配置错误，请检查");
        }

        $rsp = \Http::timeout(90)->get(str_replace("{keyword}", urlencode($keyword), $address));


        if ($rsp->status() !== 200) {

            throw new \Exception("请求chatgpt接口错误(" . $rsp->status() . ")," . $rsp->body());

        }

        return $rsp->body();


    }


    /**
     * 游戏模板
     * @param string $keyword
     * @return string
     */
    public static function gameTemplate(string $keyword): string
    {

        $mode1 = [
            '游戏介绍',
            '游戏玩法'

        ];


        $mode2 = [
            '游戏特色',
            '游戏亮点'
        ];

        $mode3 = [
            '游戏优势',
            '游戏测评'
        ];

        $mode4 = [
            '小编评语'
        ];


        $list = [
            "请用中文帮我写一篇《{keyword}》的手机游戏介绍，其中要包含" . $mode1[array_rand($mode1)] . "、" . $mode2[array_rand($mode2)] . "、" . $mode3[array_rand($mode3)] . "和" . $mode4[array_rand($mode4)] . "四个方面来写,需要写详细一点,要使用HTML,只能用h3和p标签"
        ];


        return str_replace("{keyword}", $keyword, $list[array_rand($list)]);


    }


    /**
     * 应用模板
     * @param string $keyword
     * @return string
     */
    public static function appTemplate(string $keyword): string
    {

        $mode1 = [
            '应用介绍',
            '应用玩法'

        ];


        $mode2 = [
            '应用特色',
            '应用亮点'
        ];

        $mode3 = [
            '应用优势',
            '应用测评'
        ];

        $mode4 = [
            '小编评语'
        ];


        $list = [
            "请用中文帮我写一篇《{keyword}》的手机应用介绍，其中要包含" . $mode1[array_rand($mode1)] . "、" . $mode2[array_rand($mode2)] . "、" . $mode3[array_rand($mode3)] . "和" . $mode4[array_rand($mode4)] . "四个方面来写,需要写详细一点,要使用HTML,只能用h3和p标签"
        ];


        return str_replace("{keyword}", $keyword, $list[array_rand($list)]);


    }

}
