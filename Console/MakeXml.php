<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MakeXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MakeXml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成网站地图';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $process = Process::fromShellCommandline('./script/makeXml');

        $process->setWorkingDirectory(base_path());


        $process->run(function ($type, $buffer) {


            echo $buffer;

        });


        return 0;
    }
}
