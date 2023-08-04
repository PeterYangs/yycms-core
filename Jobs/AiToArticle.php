<?php

namespace Ycore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ycore\Service\Ai\Ai;
use Ycore\Tool\ArticleGenerator;

class AiToArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected string $keyword;

    protected array $cmds;

    protected array $imgs;

    protected $category_id;

    protected $push_status;

    protected $special_id;

    public $timeout = 90;


    /**
     * @param string $keyword
     * @param array $cmds
     * @param array $imgs
     * @param $category_id
     * @param $push_status
     * @param $special_id
     */
    public function __construct(string $keyword, array $cmds, array $imgs, $category_id, $push_status, $special_id)
    {
        $this->keyword = $keyword;
        $this->cmds = $cmds;
        $this->imgs = $imgs;
        $this->category_id = $category_id;
        $this->push_status = $push_status;
        $this->special_id = $special_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Ai $ai, ArticleGenerator $generator)
    {

        $cmd = $this->cmds[array_rand($this->cmds)];

        $img = $this->imgs[array_rand($this->imgs)];

        $content = $ai->do(str_replace('{keyword}', $this->keyword, $cmd));


        $generator->fill([
            'content' => $content,
            'img' => $img,
            'title' => $this->keyword,
            'push_status' => $this->push_status,
            'special_id' => $this->special_id,
            'category_id' => $this->category_id,
        ], []);

        $generator->create(false);

        if (app()->runningInConsole()) {

            echo $content . PHP_EOL;
        }


    }
}
