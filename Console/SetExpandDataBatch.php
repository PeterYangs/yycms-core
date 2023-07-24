<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Ycore\Models\Article;
use Ycore\Models\ExpandData;


class SetExpandDataBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetExpandDataBatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量设置拓展表数据';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        Article::chunk(1000, function ($items) {


            foreach ($items as $item) {


                \Artisan::call('SetExpandData', ['id' => $item->id]);

                $this->info($item->title);

            }

        });


        return 0;
    }
}
