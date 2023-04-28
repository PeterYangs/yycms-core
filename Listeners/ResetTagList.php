<?php

namespace Ycore\Listeners;

use App\Events\ArticleUpdate;
use Ycore\Models\Article;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ResetTagList
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
     * @param  \App\Events\ArticleUpdate  $event
     * @return void
     */
    public function handle(ArticleUpdate $event)
    {
        //

        \Cache::forget('tag_list');

    }
}
