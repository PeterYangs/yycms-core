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

        $rep=Http::withOptions(['verify' => false])->get("https://gitee.com/api/v5/repos/mryy1996/yycms-library/releases/latest?access_token=9fc2df259a8d30857378100b35c63e17");


        dd(json_decode($rep->body(),true));

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
