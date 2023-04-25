<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BatchPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchPush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量推送链接';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

//        dd(storage_path('link'));





        $process = Process::fromShellCommandline('./script/baiduPush start -d --path '.storage_path('link'));

        $process->setWorkingDirectory(base_path());


        $process->run(function ($type, $buffer) {


            echo $buffer;

        });




        return 0;
    }
}
