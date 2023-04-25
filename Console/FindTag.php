<?php

namespace Ycore\Console;


use Ycore\Models\Article;
use Ycore\Models\ArticleTag;
use Ycore\Models\Tag;
use Illuminate\Console\Command;


class FindTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FindTag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文章标记标签';


    protected int $count = 0;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        Tag::chunk(100, function ($items) {


            foreach ($items as $item) {


                $articles = Article::where('title', "like", "%" . $item->title . "%")->get();

//                dd($articles);

                if ($articles->count() > 0) {


                    foreach ($articles as $article) {


                        try {

                            ArticleTag::create([
                                'article_id' => $article->id,
                                'tag_id' => $item->id,
                            ]);

                            $this->count++;

                            echo $article->title . PHP_EOL;

                        } catch (\Exception $exception) {


//                            echo $article->title . "----" . $exception->getMessage();
                        }


                    }


                }

            }


        });


        echo "总计" . $this->count . PHP_EOL;

        return 0;
    }
}
