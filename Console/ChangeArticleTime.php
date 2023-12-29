<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Models\Article;

class ChangeArticleTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangeArticleTime  {start_time} {cid=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改文章发布时间';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cid = $this->argument('cid');

        $start_time = strtotime($this->argument('start_time'));

        $end_time = now()->unix();

        $query = Article::orderBy('id', 'desc');


        if ($cid !== '0') {

            $ids = getCategoryIds($cid);


            $query->whereIn('category_id', $ids->all());

        }

        $query->chunkById(1000, function ($items) use ($start_time, $end_time) {


            foreach ($items as $item) {

                $time = date("Y-m-d H:i:s", mt_rand($start_time, $end_time));


                $ok = \DB::table('article')->where('id', $item->id)->update([
                    'created_at' => $time,
                    'updated_at' => $time,
                    'push_time' => $time
                ]);


                $this->info($item->title . "---" . $time . "---" . $ok);

            }

        });


        return 0;
    }
}
