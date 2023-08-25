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

        try {

            $b = \Http::get(getDetailUrlForCli($article) . '?admin_key=' . env('ADMIN_KEY'))->body();


            \Storage::disk('static')->put('pc/' . str_replace("{id}", $article->id,
                    \Cache::get('category:detail:pc_' . $article->category->id)),
                $b);


            $b = \Http::withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'])->get(getDetailUrlForCli($article,
                    'mobile') . '?admin_key=' . env('ADMIN_KEY'))->body();


            \Storage::disk('static')->put('mobile/' . str_replace("{id}", $article->id,
                    \Cache::get('category:detail:mobile_' . $article->category->id)), $b);


        } catch (\Exception $exception) {


            \Log::error("文章静态化失败，文章id为" . $article->id . "(" . $exception->getMessage() . ")");

        }


    }
}
