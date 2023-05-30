<?php

namespace Ycore\Service\Ai;

use Ycore\Models\Article;
use Ycore\Models\StoreArticle;

interface Ai
{

    /**
     * 直接生成
     * @param string $keyword
     * @return string
     */
    function do(string $keyword): string;


    /**
     * 文章生成
     * @param Article $article
     * @return string
     */
    function article(Article $article): string;


    /**
     * 采集文章生成
     * @param StoreArticle $storeArticle
     * @return mixed
     */
    function spider(StoreArticle $storeArticle):string;

}
