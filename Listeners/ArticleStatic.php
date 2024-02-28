<?php

namespace Ycore\Listeners;

use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;

class ArticleStatic
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
        //

        $article = Article::where('id', $event->articleId)->with('category')->first();


        if (!$article) {

//            \Log::info("已知悉");

            return;
        }


        staticByArticle($article);


    }
}
