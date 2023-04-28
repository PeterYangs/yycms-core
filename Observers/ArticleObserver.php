<?php

namespace Ycore\Observers;

use Ycore\Models\Article;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     *
     * @param \Ycore\Models\Article $article
     * @return void
     */
    public function created(Article $article)
    {
        //


        \Cache::put('article_max_id', $article->max('id'));
        \Cache::put('article_min_id', $article->min('id'));


    }

    /**
     * Handle the Article "updated" event.
     *
     * @param \Ycore\Models\Article $article
     * @return void
     */
    public function updated(Article $article)
    {
        //
    }

    /**
     * Handle the Article "deleted" event.
     *
     * @param \Ycore\Models\Article $article
     * @return void
     */
    public function deleted(Article $article)
    {
        //
    }

    /**
     * Handle the Article "restored" event.
     *
     * @param \Ycore\Models\Article $article
     * @return void
     */
    public function restored(Article $article)
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     *
     * @param \Ycore\Models\Article $article
     * @return void
     */
    public function forceDeleted(Article $article)
    {
        //
    }
}
