<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Tool\Cmd;

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


        $out = Cmd::commandline(Cmd::getCommandlineByName('makeAllLink') . " start --path " . storage_path('link'), 60);


        if (app()->runningInConsole()) {


            $this->info($out);
        }


        return 0;
    }
}
