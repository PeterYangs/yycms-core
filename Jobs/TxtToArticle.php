<?php

namespace Ycore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use QL\Dom\Elements;
use QL\QueryList;
use Ycore\Service\Ai\Ai;
use Ycore\Service\Upload\Upload;
use Ycore\Tool\ArticleGenerator;

class TxtToArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 60;


    public string $extension;

    public string $content;

    public int $category_id;

    public string $title;

    public int $push_status;

    public int $status;

    public string $img;

    /**
     * @param string $extension
     * @param string $content
     * @param int $category_id
     * @param string $title
     * @param int $push_status
     * @param int $status
     * @param string $img
     */
    public function __construct(string $extension, string $content, int $category_id, string $title, int $push_status, int $status, string $img)
    {
        $this->extension = $extension;
        $this->content = $content;
        $this->category_id = $category_id;
        $this->title = $title;
        $this->push_status = $push_status;
        $this->status = $status;
        $this->img = $img;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ArticleGenerator $generator, Upload $upload)
    {

        if ($this->extension === "txt") {


            $html = QueryList::html($this->content);


            $html->find('img')->map(function (Elements $elements) use ($upload) {


                $url = $upload->uploadRemoteFile($elements->attr('src'));


                $elements->attr('src', $url);

            });


            $generator->fill([
                'category_id' => $this->category_id,
                'push_time' => \Date::now(),
                'content' => $html->getHtml(),
                'img' => $this->img,
                'title' => $this->title,
                'push_status' => $this->push_status,
                'status' => $this->status

            ], []);

            try {

                $article = $generator->create();

            } catch (\Exception $exception) {

                if (str_contains($exception->getMessage(), "已存在！")) {

                    echo $this->title . "已存在！" . PHP_EOL;

                } else {

                    report($exception);
                }

            }


        }
    }
}
