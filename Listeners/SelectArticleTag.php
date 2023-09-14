<?php

namespace Ycore\Listeners;

use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleTag;
use Ycore\Models\Tag;

//自动设置文章标签（根据文章标题和文章内容）
class SelectArticleTag
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \Ycore\Events\ArticleUpdate $event
     * @return void
     */
    public function handle(ArticleUpdate $event)
    {

        $article = Article::where('id', $event->articleId)->with('category')->first();


        if (!$article) {

            return;
        }


        selectArticleTag($article);


    }
}
