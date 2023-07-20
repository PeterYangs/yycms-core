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
use Ycore\Tool\Cmd;

class PushAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PushAsset {theme?}';

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

        if (!$theme) {

            $theme = getOption("theme", "demo");
        }


        if (!File::isDirectory(base_path("theme/" . $theme))) {


            throw new \Exception("主题:" . $theme . ",不存在！");


        }


        if (!File::isWritable(public_path())) {

            throw new \Exception("public文件夹无写入权限，请给写入权限或者以管理员身份运行命令(php artisan PushAsset)");
        }


        $pcThemePath = base_path('theme/' . $theme . "/pc/asset");

        $mobileThemePath = base_path('theme/' . $theme . "/mobile/asset");


        //Windows平台删除软连接和创建软链接
        if (in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows'])) {
            try {
                Cmd::commandline('rmdir ' . base_path('public\pc'));
            } catch (\Exception $exception) {
            }

            Cmd::commandline('mklink /J ' . base_path('public\pc') . " " . str_replace("/", "\\", $pcThemePath));

            try {
                Cmd::commandline('rmdir ' . base_path('public\mobile'));
            } catch (\Exception $exception) {
            }

            Cmd::commandline('mklink /J ' . base_path('public\mobile') . " " . str_replace("/", "\\", $mobileThemePath));


        }

        //Linux平台删除软连接和创建软链接(未验证)
        if (in_array(PHP_OS, ['Darwin', 'FreeBSD', 'Linux'])) {

            try {
                Cmd::commandline('unlink ' . base_path('public/pc'));
            } catch (\Exception $exception) {
            }

            Cmd::commandline("ln -s " . $pcThemePath . " " . base_path('public/pc'));

            try {
                Cmd::commandline('unlink ' . base_path('public/mobile'));
            } catch (\Exception $exception) {
            }

            Cmd::commandline("ln -s " . $mobileThemePath . " " . base_path('public/mobile'));


        }


        return 0;
    }
}
