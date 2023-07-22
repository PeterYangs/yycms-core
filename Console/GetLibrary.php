<?php

namespace Ycore\Console;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Ycore\Core\Core;
use Ycore\Events\ArticleUpdate;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Models\CollectTag;

class GetLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetLibrary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取核心库';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $rep = Http::withOptions(['verify' => false])->get("http://121.199.20.221:8198/releases");

        if ($rep->status() !== 200) {

            throw new \Exception("获取更新失败(" . $rep->body() . ")");

        }

        $data = json_decode($rep->body(), true);

        $tag = $data['tag_name'];


        if ($tag !== Core::GetVersion()) {


            $url = $data['assets'][0]['browser_download_url'];

            $rsp = Http::withOptions(['verify' => false])->timeout(60)->connectTimeout(10)->get($url);

            if ($rsp->status() !== 200) {

                throw new \Exception("下载更新失败(" . $rep->body() . ")");

            }

            Storage::put("library.zip", $rsp->body());


            if (!File::isWritable(base_path('library'))) {

                throw new \Exception('library目录无写入权限');
            }


            File::cleanDirectory(base_path('library'));

            $this->unzip_file(Storage::path('library.zip'), base_path('library'));

            Storage::delete('library.zip');

            if (app()->runningInConsole()) {

                $this->info("library更新成功($tag)");
            }


            if (File::isWritable(base_path('config'))) {

                $this->info("开始写入配置！");
                //发布资源
                $this->call("vendor:publish", ['--provider' => "Ycore\YyCmsServiceProvider", "--force" => true]);

            } else {

                if (app()->runningInConsole()) {

                    $this->error('config目录无写入权限，配置写入失败！');

                }
            }


        } else {

            if (app()->runningInConsole()) {

                $this->info("library已是最新版本($tag)");
            }

            if (File::isWritable(base_path('config'))) {

                $this->info("开始写入配置！");
                //发布资源
                $this->call("vendor:publish", ['--provider' => "Ycore\YyCmsServiceProvider", "--force" => true]);

            } else {


                if (app()->runningInConsole()) {

                    $this->error('config目录无写入权限，配置写入失败！');
                }

            }


        }




        //执行数据库迁移
        \Artisan::call("migrate");


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
