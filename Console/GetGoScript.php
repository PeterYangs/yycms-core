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
            ['name' => 'makeAllLink', 'version' => 'v0.0.1', 'download_url' => 'https://gitee.com/mryy1996/make-all-link/releases/download/{tag}/makeAllLink'],
            ['name' => 'makeXml', 'version' => 'v0.0.1', 'download_url' => 'https://gitee.com/mryy1996/make-xml/releases/download/{tag}/makeXml'],
        ];

        $client = new Client();


        foreach ($list as $value) {


            $version = Cmd::commandline(Cmd::getCommandlineByName($value['name']) . " --version");


            if ($version !== $value['version']) {


                $this->info($value['name'] . "正在更新。。。");

                $url = $this->getDownUrlByOs($value['download_url'], $value['version']);

                try {

                    $client->get($url, ['sink' => base_path('script/' . basename($url)), 'verify' => false, 'timeout' => 90]);


                    $this->info($value['name'] . "已更新到" . $value['version'] . "!");

                    //设置可执行权限
                    if (in_array(PHP_OS, ['Darwin', 'FreeBSD', 'Linux'])) {

                        Cmd::commandline("chmod +x " . base_path('script/' . basename($url)));
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
