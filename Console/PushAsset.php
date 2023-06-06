<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Models\CollectTag;

class PushAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PushAsset {theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发布当前主题的静态文件';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $theme = $this->argument('theme');

        if (!File::exists('theme/' . $theme)) {


            $this->error("主题:" . $theme . ",不存在！");

            return 0;
        }


        $pcThemePath = base_path('theme/' . $theme . "/pc/asset");

        $mobileThemePath = base_path('theme/' . $theme . "/mobile/asset");


        File::deleteDirectories(base_path('public/pc'));
        File::deleteDirectories(base_path('public/mobile'));


        foreach (File::allFiles($pcThemePath) as $file) {


            if (!File::exists(base_path('public/pc') . "/" . $file->getRelativePath())) {

                mkdir(base_path('public/pc') . "/" . $file->getRelativePath(), 0755, true);
            }


            File::put(base_path('public/pc') . "/" . $file->getRelativePath() . "/" . $file->getFilename(), $file->getContents());

        }


        foreach (File::allFiles($mobileThemePath) as $file) {


            if (!File::exists(base_path('public/mobile') . "/" . $file->getRelativePath())) {

                mkdir(base_path('public/mobile') . "/" . $file->getRelativePath(), 0755, true);
            }


            File::put(base_path('public/mobile') . "/" . $file->getRelativePath() . "/" . $file->getFilename(), $file->getContents());

        }


        return 0;
    }
}
