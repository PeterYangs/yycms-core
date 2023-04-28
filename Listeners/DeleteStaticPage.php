<?php

namespace Ycore\Listeners;

use App\Events\ArticleDestroy;
use Ycore\Models\Article;

/**
 * 删除静态文件
 */
class DeleteStaticPage
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
     * @param \App\Events\ArticleDestroy $event
     * @return void
     */
    public function handle(ArticleDestroy $event)
    {
        //

        $item = Article::where('id', $event->articleId)->with('category')->withoutGlobalScopes()->first();


        if (!$item) {

            return;

        }


        //删除静态文件
        \Storage::disk('static')->delete('pc/'.\Cache::get('category:list:pc_' . $item->category->id)."/".$item->id.".html");
        \Storage::disk('static')->delete('mobile/'.\Cache::get('category:list:mobile_' . $item->category->id)."/".$item->id.".html");


    }
}
