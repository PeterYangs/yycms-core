<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StaticTool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StaticTool';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '网站静态化';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

//        dd(storage_path('link'));


        $process = Process::fromShellCommandline('./script/staticTool start  -d');

        $process->setWorkingDirectory(base_path());


        $process->run(function ($type, $buffer) {


            echo $buffer;

        });


        return 0;
    }
}
