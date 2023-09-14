<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Ycore\Core\Core;

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


        if (!File::exists(public_path('yycms'))) {


            File::makeDirectory(public_path('yycms'), 755);

        }

        if (!File::isWritable(public_path('yycms'))) {


            throw new \Exception("根目录下 yycms目录无写入权限！");
        }

        $adminVersion = "";

        try {

            $adminVersion = str_replace("\n", "", \File::get(public_path('yycms/static/version')));

        } catch (\Exception $exception) {


            if (app()->runningInConsole()) {

                $this->error($exception->getMessage());

            }


        }


        if ($adminVersion != Core::GetAdminVersion()) {


            if (app()->runningInConsole()) {

                $this->info("正在更新admin。。。");
            }

            $downloadUrl = "https://gitee.com/mryy1996/yycms-admin-static/releases/download/" . Core::GetAdminVersion() . "/dist.zip";


            $rsp = Http::withOptions(['verify' => false])->timeout(60)->connectTimeout(10)->get($downloadUrl);


            if ($rsp->status() !== 200) {


                $this->error("下载失败：" . $rsp->body());


                return 0;

            }


            if (!File::exists(public_path('yycms'))) {


                mkdir(public_path('yycms'), 0755);
            }


            Storage::put("yycms.zip", $rsp->body());

            File::cleanDirectory(public_path('yycms'));

            $this->unzip_file(Storage::path('yycms.zip'), public_path('yycms'));


            Storage::delete('yycms.zip');

            if (app()->runningInConsole()) {

                $this->info("admin更新成功($adminVersion)");
            }


        } else {


            if (app()->runningInConsole()) {

                $this->info("admin已是最新版本($adminVersion)");
            }


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
