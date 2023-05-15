<?php

namespace Ycore\Console;

use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Illuminate\Console\Command;


class CreateArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:article {num=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成文章测试数据';


    /**
     * @var int
     */
    protected int $index = 0;

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {

        $num = (int)$this->argument('num');


        if (!is_int($num)) {


            echo "只能是数字";

            return 0;
        }


        for ($i = 0; $i < $num; $i++) {

            $this->deal();
        }


        return 0;
    }


    /**
     * @throws \JsonException
     * @throws \Exception
     */
    function deal()
    {

        $list = Category::get();


        foreach ($list as $key => $value) {


            $detail_channel = \Cache::get('category:detail:pc_' . $value->id);


            if (!$detail_channel) {

                continue;
            }

            $this->index++;

            $title = date("YmH").$value->name . $key . $this->index;

            $post = [

                'category_id' => $value->id,
                'push_time' => \Date::now(),
                'content' => str_repeat('<h3>测试内容</h3>', 30),
                'img' => "test_img/" . random_int(1, 13) . ".png",
                'title' => $title,
                'seo_title' => $value->name . $key,
                'seo_desc' => $value->name . $key,
                'seo_keyword' => $value->name . $key,
                'admin_id_create' => 1,
                'admin_id_update' => 1,


            ];

            $expand_data = getExpandByCategoryId($value->id)->toArray();


            foreach ($expand_data as $k2 => $v2) {


                switch ($v2['type']) {

                    case 1:
                    case 2:
                    case 3:
                    case 4:

                        $expand_data[$k2]['value'] = "";

                        break;
                    case 5:

                        $expand_data[$k2]['value'] = \Date::now();

                        break;

                    case 6:

                        $expand_data[$k2]['value'] = [
                            [
                                'img' => "test_img/" . random_int(1, 13) . ".png",
                                'name' => ""
                            ]
                        ];
                        break;
                    case 7:

                        $list = Article::select(['id', 'title', 'img'])->inRandomOrder()->limit(10)->get();

                        $expand_data[$k2]['value'] = $list;


                        break;

                    case 8:

                        $item = Article::select(['id'])->inRandomOrder()->limit(10)->first();


                        $expand_data[$k2]['value'] = $item->id;

                        break;

                }


            }


            $post['expand'] = $expand_data;


            $table_expand_data = dealExpandToTable($post['expand']);


            $article = Article::create($post);


            dealArticleAssociationObject($article->id, $expand_data);


            $table_name = CategoryController::getExpandTableName($value->id);


            if ($table_name) {

                \DB::table($table_name)->updateOrInsert(['article_id' => $article->id], $table_expand_data);
            }


            echo $title . PHP_EOL;


        }

    }


}
