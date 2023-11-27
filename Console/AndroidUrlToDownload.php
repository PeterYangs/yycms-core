<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleDownload;
use Ycore\Models\Collect;
use Ycore\Models\DownloadSite;
use Ycore\Tool\ArticleGenerator;

class AndroidUrlToDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AndroidUrlToDownload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拓展数据中的安卓下载链接同步到下载表';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $rule = "{path}";

        $downloadSite = DownloadSite::where('rule', $rule)->first();

        if (!$downloadSite) {

            $downloadSite = DownloadSite::create([
                'rule' => $rule,
                'note' => "外链"
            ]);

        }

        $ids = getCategoryIds([1, 3]);

        Article::whereIn('category_id', $ids)->chunkById(1000, function ($items) use ($downloadSite) {


            foreach ($items as $item) {


                if (isset($item->ex['android'])) {

                    $version = $item->ex['version'];


                    try {

                        ArticleDownload::create([
                            'article_id' => $item->id,
                            'download_site_id' => $downloadSite->id,
                            'file_path' => $item->ex['android'],
                            'save_type' => 1
                        ]);

                        if ($version) {

                            $ag = new ArticleGenerator();

                            $ag->fill([], ['version' => "", 'version_name' => $version])->update(['id' => $item->id]);
                        }


                    } catch (\Exception $exception) {

                        $this->error($exception->getMessage());

                        continue;
                    }

                }

                $this->info($item->title);

            }

        });


        return 0;
    }
}
