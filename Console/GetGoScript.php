<?php

namespace Ycore\Console;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Ycore\Tool\Cmd;

class GetGoScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetGoScript';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取go脚本';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $list = [
            ['name' => 'goScript', 'version' => 'v0.0.2', 'download_url' => 'https://gitee.com/mryy1996/go-script/releases/download/{tag}/goScript'],
        ];

        $client = new Client();


        foreach ($list as $value) {


            $version = str_replace("\n", "", Cmd::commandline(Cmd::getCommandlineByName($value['name']) . " --version"));


            if ($version !== $value['version']) {


                $this->info($value['name'] . "正在更新。。。");

                $url = $this->getDownUrlByOs($value['download_url'], $value['version']);

                try {

                    $client->get($url, ['sink' => base_path('storage/app/public/' . basename($url)), 'verify' => false, 'timeout' => 90]);


                    $this->info($value['name'] . "已更新到" . $value['version'] . "!");

                    //设置可执行权限
                    if (in_array(PHP_OS, ['Darwin', 'FreeBSD', 'Linux'])) {

                        Cmd::commandline("chmod +x " . base_path('storage/app/public/' . basename($url)));
                    }


                } catch (\Exception $exception) {


                    $this->error($exception->getMessage());

                }


            } else {

                $this->info($value['name'] . "已是最新版本！");
            }


        }


        return 0;
    }


    function getDownUrlByOs($downloadUrl, $tag): string
    {


        if (in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows'])) {


            return str_replace("{tag}", $tag, $downloadUrl) . ".exe";
        }

        if (in_array(PHP_OS, ['Darwin', 'FreeBSD', 'Linux'])) {


            return str_replace("{tag}", $tag, $downloadUrl);
        }


        return "";
    }

}
