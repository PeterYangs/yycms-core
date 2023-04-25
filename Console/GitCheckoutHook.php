<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class GitCheckoutHook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GitCheckoutHook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '分支切换钩子';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

//        dd(storage_path('link'));

        $process = Process::fromShellCommandline("git status");


        $process->run();


        $re = $process->getOutput();


        $re = str_replace("\n", "|", $re);

        $re = explode("|", $re)[0];

        $branch = str_replace("On branch", "", $re);

        $branch = trim($branch);

        $configFile = ".env." . $branch;


        if (file_exists("envConfig/" . $configFile)) {


            $data = file_get_contents("envConfig/" . $configFile);

            file_put_contents('.env', $data);

            echo "配置切换成功" . PHP_EOL;


        } else {

            echo "配置文件不存在" . PHP_EOL;

        }


        return 0;
    }
}
