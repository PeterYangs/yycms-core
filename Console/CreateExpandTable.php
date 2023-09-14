<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Ycore\Models\ArticleExpand;


class CreateExpandTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateExpandTable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成拓展表';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $expand_list = ArticleExpand::with('list')->get();


        foreach ($expand_list as $value) {


            $table_name = 'expand_table_' . $value->category_id;


            if (\Schema::hasTable($table_name)) {

                throw new \RuntimeException($table_name . ':表已存在！');
            }


            \Schema::create($table_name, function (Blueprint $table) use ($value) {

                $table->bigIncrements('id');
                $table->timestamps();
                $table->integer('article_id')->unsigned()->unique()->comment('文章表id');

                $list = $value->list;


                foreach ($list as $v) {


                    $this->setTable($v, $table);


                }


            });


        }


        return 0;
    }


    private function setTable($value, Blueprint $table)
    {


        switch ($value['type']) {

            case 5:

                $table->timestamp($value['name'])->nullable()->comment($value['desc']);

                break;

            case 7:
            case 6:


                $table->text($value['name'])->nullable()->comment($value['desc']);

                break;

            case 8:

                $table->integer($value['name'])->unsigned()->default(0)->nullable()->comment($value['desc']);

                break;

            default:

                $table->string($value['name'], 500)->nullable()->comment($value['desc']);

        }

    }
}
