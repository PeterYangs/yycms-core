<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Spider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Spider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬取文章到采集表';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $process = Process::fromShellCommandline('./script/spider');

        $process->setWorkingDirectory(base_path());


        $process->setTimeout(60 * 5);


        $process->run(function ($type, $buffer) {


            echo $buffer;

        });


        return 0;
    }
}
