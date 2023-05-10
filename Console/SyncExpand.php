<?php

namespace Ycore\Console;

use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Illuminate\Console\Command;

class SyncExpand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncExpand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步拓展表数据到expand字段';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \JsonException
     */
    public function handle()
    {


        Article::orderBy('id', 'desc')->chunk(100, function ($array) {


            foreach ($array as $value) {


                $expand = getExpandByCategoryId($value->category_id);


                $table = CategoryController::getExpandTableName($value->category_id);

                $expand_data = \DB::table($table)->where('article_id', $value->id)->first();


                if (!$expand_data) {

                    //写入默认数据到拓展表
                    \DB::table($table)->insert(['article_id' => $value->id]);

                    $expand_data = \DB::table($table)->where('article_id', $value->id)->first();


                }


                foreach ($expand as $k => $v) {


                    $field = $v->name;


                    $fv = $expand_data->$field;


                    if (is_array($v->value)) {

                        if ($fv) {

                            $expand[$k]->value = json_decode($fv, true, 512, JSON_THROW_ON_ERROR);

                        } else {

                            $expand[$k]->value = [];
                        }


                    } else {

                        if ($fv) {

                            $expand[$k]->value = $fv;

                        } else {

                            $expand[$k]->value = "";
                        }


                    }


                }


                \DB::table('article')->where('id', $value->id)->update([
                    'expand' => json_encode($expand,
                        JSON_THROW_ON_ERROR)
                ]);


                echo $value->title . PHP_EOL;


            }

        });


        return 0;
    }
}
