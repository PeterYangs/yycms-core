<?php

namespace Ycore\Listeners;

use App\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleTag;
use Ycore\Models\Tag;

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
     * @param \App\Events\ArticleUpdate $event
     * @return void
     */
    public function handle(ArticleUpdate $event)
    {
        //
        $article = Article::where('id', $event->articleId)->with('category')->first();


        if (!$article) {

            return;
        }

        $tags = Tag::whereRaw(" ? like CONCAT('%',title,'%')", [$article->title])->get();


        foreach ($tags as $tag) {


            try {


                ArticleTag::create([
                    'article_id' => $article->id,
                    'tag_id' => $tag->id,
                ]);


            } catch (\Exception $exception) {
            }


        }

    }
}
