<?php

namespace Ycore\Service\Ai;

use Ycore\Models\Article;
use Ycore\Models\StoreArticle;

class ChatGpt implements Ai
{

    function do(string $cmd): string
    {
        // TODO: Implement do() method.

        $address = env('CHAT_GPT_ADDRESS', null);

        if (!$address) {

            throw new \Exception("chatgpt地址配置错误，请检查");
        }

        $rsp = \Http::timeout(90)->retry(3,100)->get(str_replace("{keyword}", urlencode($cmd), $address));


        if ($rsp->status() !== 200) {

            throw new \Exception("请求chatgpt接口错误(" . $rsp->status() . ")," . $rsp->body());

        }

        return $rsp->body();

    }

    function article(Article $article): string
    {
        // TODO: Implement article() method.


        $aiCommand = $article->category->ai_command;

        //获取父级的命令
        if (!$aiCommand) {

            $aiCommand = optional($article->category->parent)->ai_command;
        }


        if (!$aiCommand) {

            throw new \Exception("ai生成命令未配置");
        }

        $aiCommand = $aiCommand[array_rand($aiCommand)];


        $aiCommand = str_replace("{category}", $article->category->name, str_replace("{title}", $article->title, $aiCommand));


        return $this->do($aiCommand);

    }

    function spider(StoreArticle $storeArticle): string
    {
        // TODO: Implement spider() method.

        return "";
    }
}
