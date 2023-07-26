<?php

namespace Ycore\Console;


use Faker\Core\Uuid;
use Illuminate\Console\Command;
use QL\Dom\Elements;
use QL\QueryList;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Ycore\Models\ExpandData;
use Ycore\Service\Upload\Upload;
use Ycore\Tool\ArticleGenerator;


class BatchImportArticleWithZip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchImportArticleWithZip {zipPath} {cid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量从zip文件上传文章';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle(Upload $upload)
    {

        $cid = $this->argument('cid');

        $zipPath = $this->argument('zipPath');

        $zipPath = public_path($zipPath);


        $tempDir = \Ramsey\Uuid\Uuid::uuid4()->toString() . "-temp-article";

        $category = Category::where('id', $cid)->first();

        if (!$category) {

            throw new \Exception('cid ' . $cid . "不存在");
        }

        $detail = \Cache::get('category:detail:pc_' . $category->id);


        if (!$detail) {


            throw new \Exception('cid ' . $cid . "详情页不存在");
        }


        try {

            $ac = new ArticleGenerator();


            $ok = $this->unzip_file($zipPath, storage_path('app/public/' . $tempDir));

            if (!$ok) {

                throw new \Exception('压缩文件解压失败');
            }

            $fileList = \File::allFiles(storage_path('app/public/' . $tempDir));


            foreach ($fileList as $fileInfo) {


                if ($fileInfo->getExtension() === "txt") {


                    $html = QueryList::html($fileInfo->getContents());

                    $html->find('img')->map(function (Elements $elements) use ($upload) {


                        $url = $upload->uploadRemoteFile($elements->attr('src'));


                        $elements->attr('src', $url);

                    });


                    try {
                        $ac->fill([
                            'category_id' => $cid,
                            'push_time' => \Date::now(),
                            'content' => $html->getHtml(),
                            'img' => "test_img/" . random_int(1, 13) . ".png",
                            'title' => $fileInfo->getBasename('.txt'),

                        ], []);

                        $article = $ac->create();

                        $this->info($article->title . "（发布成功）");


                    } catch (\Exception $exception) {

                        $this->error($fileInfo->getBasename('.txt') . "（发布失败：" . $exception->getMessage() . "）");

                        continue;

                    }


                }

            }


        } catch (\Exception $exception) {

        } finally {


            \File::deleteDirectories(storage_path('app/public/' . $tempDir));


            rmdir(storage_path('app/public/' . $tempDir));

        }


        return 0;
    }

    function unzip_file(string $zipName, string $dest)
    {
        //检测要解压压缩包是否存在
        if (!is_file($zipName)) {
            return false;
        }
        //检测目标路径是否存在
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        $zip = new \ZipArchive();
        if ($zip->open($zipName)) {
            $zip->extractTo($dest);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}
