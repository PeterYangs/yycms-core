<?php

namespace Ycore\Observers;

use Ycore\Models\Article;

class ArticleObserver
{


    /**
     * 在提交所有事务后处理事件。
     *
     * @var bool
     */
    public $afterCommit = true;


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

//        \Artisan::call('SetExpandData', ['id' => $article->id]);


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
//        \Artisan::call('SetExpandData', ['id' => $article->id]);
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
