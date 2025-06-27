<?php

namespace Ycore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArticleStatic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $article_id;

    /**
     * @param $article_id
     */
    public function __construct($article_id)
    {
        $this->article_id = $article_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $re = ArticleDetailModel()->where('id', $this->article_id)->first();

        if (!$re) {
            return;
        }

        staticByArticle($re);

    }
}
