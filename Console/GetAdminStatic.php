<?php

namespace Ycore\Console;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Ycore\Core\Core;
use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Models\CollectTag;

class GetAdminStatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetAdminStatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取后台静态文件';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $adminVersion = "";

        try {

            $adminVersion = str_replace("\n", "", \File::get(public_path('yycms/static/version')));

        } catch (\Exception $exception) {


            if (app()->runningInConsole()) {

                $this->error($exception->getMessage());

            }


        }


        if ($adminVersion != Core::ADMIN_VERSION) {

//            $downloadUrl = "https://github.com/PeterYangs/yy-cms-admin-static/releases/download/" . Core::ADMIN_VERSION . "/dist.zip";
            $downloadUrl = "https://gitee.com/mryy1996/yycms-admin-static/releases/download/" . Core::ADMIN_VERSION . "/dist.zip";


            $rsp = Http::withOptions(['verify' => false])->timeout(60)->connectTimeout(10)->get($downloadUrl);


            if ($rsp->status() !== 200) {


                $this->error("下载失败：" . $rsp->body());


                return 0;

            }

            File::put(public_path('yycms.zip'), $rsp->body());

            File::deleteDirectories(public_path('yycms'));

            File::delete(public_path('yycms.zip'));

            $this->unzip_file(public_path('yycms.zip'), public_path('yycms'));


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
