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

        $process = Process::fromShellCommandline("git rev-parse --abbrev-ref HEAD");

        $process->run();

        $re = $process->getOutput();

        $branch = trim($re);

        $configFile = ".env." . $branch;

        if (file_exists(base_path("envConfig/" . $configFile))) {

            $data = file_get_contents(base_path("envConfig/" . $configFile));

            file_put_contents(base_path('.env'), $data);

            echo "配置切换成功" . PHP_EOL;

        } else {
            echo "配置文件不存在" . PHP_EOL;
        }

        return 0;
    }
}
