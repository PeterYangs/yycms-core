<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Models\Article;
use Ycore\Models\ExpandData;
use Ycore\Tool\Cmd;

class SetExpandData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetExpandData {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置拓展表数据';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $id = $this->argument('id');


        $article = Article::where('id', $id)->first();

        if (!$article) {

            throw new \Exception('未找到该文章(' . $id . ')');
        }


        foreach ($article->expand as $item) {


            ExpandData::updateOrCreate(['article_id' => $article->id, 'article_expand_detail_id' => $item['id']], [
                'article_id' => $article->id,
                'article_expand_detail_id' => $item['id'],
                'article_expand_id' => $item['article_expand_id'] ?? 0,
                'name' => $item['name'] ?? "",
                'desc' => $item['desc'] ?? "",
                'type' => $item['type'] ?? 1,
                'select_list' => is_array($item['select_list']) ? json_encode($item['select_list']) : $item['select_list'],
                'model_name' => $item['model_name'],
                'label' => is_array($item['label']) ? json_encode($item['label']) : $item['label'],
                'condition' => is_array($item['condition']) ? json_encode($item['condition']) : $item['condition'],
                'default_condition' => is_array($item['default_condition']) ? json_encode($item['default_condition']) : $item['default_condition'],
                'show_field' => is_array($item['show_field']) ? json_encode($item['show_field']) : $item['show_field'],
                'value' => is_array($item['value']) ? json_encode($item['value']) : $item['value']??""

            ]);


        }


        return 0;
    }
}
