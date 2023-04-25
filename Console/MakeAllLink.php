<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MakeAllLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MakeAllLink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成全站链接';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $process = Process::fromShellCommandline('./script/makeAllLink start --path ' . storage_path('link'));

        $process->setWorkingDirectory(base_path());


        $process->run(function ($type, $buffer) {


            echo $buffer;

        });


        return 0;
    }
}
